<?php
class SM_csSess {

  private $csSession;
  public $csFields, $csFilter, $newSession, $servername, $server, $clilstoreUrl;

  public function getCsSession() {
     // $csSession itself kept private for security, but this function returns a read-only copy
      return $this->csSession;
  }

  public function fetchCsFields() {
     // Fetches everything from the table csFields into the array $csFields
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare('SELECT * FROM csFields');
      $stmt->execute();
      $this->csFields = array();
      while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $this->csFields[$r['fd']] = $r;
      }
  }


  public function fetchCsSession() {
     // Fetches the session record from table csSession into the object $csSession
     // Returns 1 if found ok; returns 0 otherwise
      $csid = $this->csSession->csid;
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare('SELECT * FROM csSession WHERE csid=:csid');
      $stmt->bindParam(':csid',$csid,PDO::PARAM_INT);
      $stmt->execute();
      if ($this->csSession = $stmt->fetch(PDO::FETCH_OBJ)) { return 1; } else { return 0; }
  }

  public function storeCsSession() {
     // Stores the record $csSession in the csSession table in the database
      $csid = $this->csSession->csid;
      $mode    = $this->csSession->mode;
      $incTest = $this->csSession->incTest;
      $user    = $this->csSession->user;
      if ($mode<>0 && $mode<>1 && $mode<>2 && $mode<>3) { throw new SM_Exception("storeCsSession called with invalid mode: $mode"); } //paranoid security check
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare('UPDATE csSession SET mode=:mode,incTest=:incTest,user=:user WHERE csid=:csid');
      $stmt->execute(array(':mode'=>$mode,':incTest'=>$incTest,':user'=>$user,':csid'=>$csid));
      $stmt = null;
  }


  public function fetchCsFilter() {
     // Fetches the filter records from table csFilter into the array $csFilter
      $csid = $this->csSession->csid;
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare('SELECT csFilter.* FROM csFilter,csFields WHERE csid=:csid AND csFilter.fd=csFields.fd ORDER BY fid'); //Try to order fields sensibly
      $stmt->bindParam(':csid',$csid,PDO::PARAM_INT);
      $stmt->execute();
      while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $this->csFilter[$r['fd']] = $r;
      }
      $stmt = null;
  }

  public function storeCsFilter() {
     // Stores the filter information from array $csFilter in the csFilter table in the database
      $csid = $this->csSession->csid;
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare('UPDATE csFilter SET m0=:m0,m1=:m1,m2=:m2,m3=:m3,sortpri=:sortpri,sortord=:sortord,val1=:val1,val2=:val2 WHERE csid=:csid AND fd=:fd');
      foreach ($this->csFilter as $filfd) {
          extract($filfd);
          $stmt->execute(array(':m0'=>$m0,':m1'=>$m1,':m2'=>$m2,':m3'=>$m3,':sortpri'=>$sortpri,':sortord'=>$sortord,':val1'=>$val1,':val2'=>$val2,':csid'=>$csid,':fd'=>$fd));
      }
      $stmt = null;
  }


  public function __construct() {
     // If the cookie csSessionId is found, then retrieve the Clilstore session id $csid from it, and check for a Clilstore session record in the database.
     // Otherwise, create a new session record, setting $csid to the next available number, and give the session the default values for everything.
     // In any case, (re)set the cookie and retrieve session information from the database to populate the class variables.

      $this->csSession = new stdClass();

      $this->newSession = 1;  // Assume a new session will be needed until proved otherwise
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      if (isset($_COOKIE['csSessionId'])) {
          $this->csSession->csid = $csid = $_COOKIE['csSessionId'];
          if ($this->fetchCsSession()) { $this->newSession = 0; }
      }
      
      if ($this->newSession==1) {
         // No valid csid exists so create a new one
          $stmt1 = $DbMultidict->prepare('SELECT MAX(csid) AS csidMax FROM csSession');
          $stmt1->execute();
          $stmt1->bindColumn(1,$csidMax);
          $stmt1->fetch();
          $stmt1 = null;
          $this->csSession->csid = $csid = ( isset($csidMax) ? $csidMax+1 : 1 );
          $crTime = time();
          $stmt2 = $DbMultidict->prepare('INSERT INTO csSession (csid,crTime) VALUES (:csid,:crTime)');
          $stmt2->bindParam(':csid',$csid,PDO::PARAM_INT);
          $stmt2->bindParam(':crTime',$crTime,PDO::PARAM_INT);
          $stmt2->execute();
          $stmt2 = null;
          $query = 'INSERT INTO csFilter(csid,fd,m0,m1,m2,m3,sortpri,sortord)'
                             . " SELECT $csid,fd,m0,m1,m2,m3,sortpri,sortord FROM csFields";
          $stmt3 = $DbMultidict->prepare($query);
          $stmt3->execute();
          $stmt3 = null;
      }

     // Log some information for statistics
      $IPaddr = $_SERVER['REMOTE_ADDR'];
      $stmt4 = $DbMultidict->prepare('UPDATE csSession SET nCalls=nCalls+1,IPaddr=:ipaddr WHERE csid=:csid');
      $stmt4->bindParam(':ipaddr',$IPaddr,PDO::PARAM_STR);
      $stmt4->bindParam(':csid',$csid,PDO::PARAM_INT);
      $stmt4->execute();

//Temporary lines, to delete any old-style cookies (containing a domain parameter)
setcookie('csSession','',1,'/clilstore/',$_SERVER['SERVER_NAME']);
setcookie('csSession','',1,'/clilstore/','multidict.net');

      setcookie('csSessionId',
                $csid,
                time()+31556926,  // expires in 1 year (So sessions with timestamps older than a year can be deleted from the database)
                '/clilstore/'
               );

      $this->csFilter = array();
     // Fetch information from database
      $this->fetchCsSession();
      $this->fetchCsFields();
      $this->fetchCsFilter();

     // Read and act on information from GET and POST parameters

      $mode = $this->csSession->mode ?: 0;
      if (isset($_GET['mode'])) {
          $mode = $this->csSession->mode = $_GET['mode'];
          if ($mode==0) {
             // Only standard simple CEFR levels allowed for mode 0.  Clear anything else.
              $val1 = $this->csFilter['level']['val1'];
              $val2 = $this->csFilter['level']['val2'];
              if ($val1=='' || $val1%10 <> 0 || $val2 <> $val1+9) {
                  $this->csFilter['level']['val1'] = '';
                  $this->csFilter['level']['val2'] = '';
              }
          }
          if ($mode==0 || $mode==1) { $this->csSession->incTest = 0; } //student modes - no test units
      }
      if ($mode<>0 && $mode<>1 && $mode<>2 && $mode<>3) { throw new SM_Exception("Invalid mode: $mode"); }  // Paranoid security check on $mode since it is used to construct field names
      $modecol = "m$mode";

      if (!empty($_GET['deleteCol'])) { $this->csFilter[$_GET['deleteCol']][$modecol] = 0; }

      if (!empty($_REQUEST['sl'])) { // An expedient pending a more comprehensive move of request parameter processing to this constructor
          $newSl = $_REQUEST['sl'];
          if ($this->csFilter['sl']['val1']<>$newSl) {
              $this->csFilter['sl']['val1'] = $newSl;
              $this->csFilter['level']['val1'] = '';  // Clear the level filter
              $this->csFilter['level']['val2'] = '';
          }
      }

      if (!empty($_REQUEST['levelBut'])) {
          $levelBut = $_REQUEST['levelBut'];
          if     ($levelBut=='Any') { $this->csFilter['level']['val1'] =   ''; $this->csFilter['level']['val2'] =   ''; }
          elseif ($levelBut=='A1')  { $this->csFilter['level']['val1'] =  '0'; $this->csFilter['level']['val2'] =  '9'; }
          elseif ($levelBut=='A2')  { $this->csFilter['level']['val1'] = '10'; $this->csFilter['level']['val2'] = '19'; }
          elseif ($levelBut=='B1')  { $this->csFilter['level']['val1'] = '20'; $this->csFilter['level']['val2'] = '29'; }
          elseif ($levelBut=='B2')  { $this->csFilter['level']['val1'] = '30'; $this->csFilter['level']['val2'] = '39'; }
          elseif ($levelBut=='C1')  { $this->csFilter['level']['val1'] = '40'; $this->csFilter['level']['val2'] = '49'; }
          elseif ($levelBut=='C2')  { $this->csFilter['level']['val1'] = '50'; $this->csFilter['level']['val2'] = '59'; }
      }

      $this->storeCsSession();
      $this->storeCsFilter();
  }

  private function __clone() {}

  private static $_instance;
  public static function singleton() {
      if (self::$_instance === null) { self::$_instance = new self(); }
      return self::$_instance;
  }


  public function setUser($user) {
      $this->csSession->user = $user;
      $this->storeCsSession();
  }


  public function setMode($mode) {
      $this->csSession->mode = $mode;
      if ($mode==0 || $mode==1) { $this->csSession->incTest = 0; } //student modes - no test units
      $this->storeCsSession();
  }


  public function setIncTest($incTest) {
      if ($incTest<>0 && $incTest<>1) { throw new SM_Exception("setIncTest called with invalid value: $incTest"); }
      $this->csSession->incTest = $incTest;
      $this->storeCsSession();
  }


  public function sortCol($field) {
     // Called when a field is clicked to change the sort order
      $csid = $this->csSession->csid;
      if ($this->csFilter[$field]['sortpri']==1) {
          // Field already has top priority (sortpri=1) so change the sort direction
          $this->csFilter[$field]['sortord'] *= -1;
      } else {
          // Nothing changed, so need to change the sort priority
          foreach ($this->csFilter as $fd=>$filfd) {
              if ($this->csFilter[$fd]['sortpri']>0) { $this->csFilter[$fd]['sortpri']++; }
          }
          $this->csFilter[$field]['sortpri'] = 1;
      }
      $this->storeCsFilter();
  }


  public function orderClause() {
      $csid = $this->csSession->csid;
      $mode = $this->csSession->mode;
      $modecol = "m$mode";
      $sortfds = array();
      foreach($this->csFilter as $fd=>$filfd) {
          $sortpri = $filfd['sortpri'];
          $sortord = $filfd['sortord'];
          $ord = ($sortord==-1 ? ' DESC' : '');
          if ($sortpri<>0 && $filfd[$modecol]<>0) { $sortfds[$sortpri] = "$fd$ord"; }
      }
      ksort($sortfds);
      $sortfds[] = 'id DESC';
      $clause = implode(',',$sortfds);
      return $clause;
  }


  public function symbolRowHtml() {
      $T = new SM_T('clilstore/symbolRowHtml');
      $T_Sort_the_column     = $T->h('Sort_the_column');
      $T_Hide_the_column     = $T->h('Hide_the_column');
      $T_Click_to_sort_hide  = $T->h('Click_to_sort_hide');
      $T_Restore             = $T->h('Restore');
      $T_Restore_title       = $T->h('Restore_title');

      $csid = $this->csSession->csid;
      $mode = $this->csSession->mode;
      $modecol = "m$mode";
      $symbol = $symbolHtml = $pri = array();
      foreach ($this->csFilter as $r) {
          $fd  = $r['fd'];
          $pr  = $r['sortpri'];
          if ($r[$modecol]==0) {
              $symbol[$fd] = '.';
          } else {
              if ($r['sortord']==1) { $symbol[$fd] = '▵'; }
                else                { $symbol[$fd] = '▿'; }
              if ($pr<>0) { $pri[$fd] = $pr; }
          }
      }
      $ranks = array_flip($pri);
      ksort($ranks);
      $prifd  = array_shift($ranks);  //The sort field
      $prifd2  = array_shift($ranks); //The secondary sort field
      if (!empty($prifd))  { if ($symbol[$prifd] =='▵') {$symbol[$prifd]  = '▲';} else {$symbol[$prifd]  = '▼';}  }
      if (!empty($prifd2)) { if ($symbol[$prifd2]=='▵') {$symbol[$prifd2] = '▴';} else {$symbol[$prifd2] = '▾';}  }

      $hrefSelf = $_SERVER['PHP_SELF'];
      if (substr($hrefSelf,-9)=='index.php') { $hrefSelf = substr($hrefSelf,0,strlen($hrefSelf)-9); }
      foreach ($symbol as $fd=>$sym) {
          if ($sym=='.') {
              $symbolHtml[$fd] = "<td class=\"$fd\">.</td>\r";
          } elseif ($fd<>'title') {
              $pad = ( $fd=='id' ? '' : '&nbsp;' );
              $deleteHtml = "<a href='$hrefSelf?deleteCol=$fd' style='color:red;font-size:80%' title='$T_Hide_the_column'>×</a>";
              $symbolHtml[$fd] = "<td class='$fd'><a href='$hrefSelf?sortCol=$fd' title='$T_Sort_the_column'>$sym$pad</a> $deleteHtml</td>\r";
          } else {
              $symbolinfo = '<span style="font-size:65%">⇐ '
                           . sprintf($T_Click_to_sort_hide, '<span style="color:#55a8eb;font-size:140%">▵</span>', '<span style="color:red;font-size:140%">×</span>')
                           . " [<a href='$hrefSelf?restoreCols=restore' title='$T_Restore_title' style='font-size:110%'>$T_Restore</a>]</span>";
              $symbolHtml['title'] = "<td class=title colspan=2 style='vertical-align:bottom'><div style='float:right'>$symbolinfo</div><a href='$hrefSelf?sortCol=title' title='$T_Sort_the_column'>$sym</a></td>";
          }
      }
      $symbolHtml_id = $symbolHtml['id'];
      $symbolRow = $symbolHtml['id']
                 . $symbolHtml['views']
                 . $symbolHtml['clicks']
                 . $symbolHtml['created']
                 . $symbolHtml['changed']
                 . $symbolHtml['licence']
                 . $symbolHtml['owner']
                 . $symbolHtml['sl']
                 . $symbolHtml['level']
                 . $symbolHtml['words']
                 . $symbolHtml['medtype']
                 . $symbolHtml['medlen']
                 . $symbolHtml['buttons']
                 . $symbolHtml['files']
                 . '<td class="delete"></td>'
                 . '<td class="edit"></td>'
                 . '<td class="nowl"></td>'
                 . $symbolHtml['title'];
      return $symbolRow;
  }


  public function display($fd) {
  // Returns either 'table-cell' or 'none', depending on whether or not the field should be displayed in the current mode
  // This ‘display’ mechanism should probably be replaced by something better
      $csid = $this->csSession->csid;
      $mode = $this->csSession->mode;
      if ($this->csFilter[$fd]["m$mode"]==1) {
          return 'table-cell';
      } else {
          return 'none';
      }
  }


/*
  public function deleteCol($fd) {
     // Deletes a column from display in the current mode by by setting its display value to 0
      $csid = $this->csSession->csid;
      $mode = $this->csSession->mode;
      $modecol = "m$mode";
      $this->csFilter[$fd][$modecol] = 0;
      $this->storeCsFilter();
  }
*/


  public function addCol($fd) {
  // Add a column to display in the current mode by by setting its display value to 1
      $csid = $this->csSession->csid;
      $mode = $this->csSession->mode;
      $modecol = "m$mode";
      $this->csFilter[$fd][$modecol] = 1;
      $this->storeCsFilter();
  }


  public function restoreCols() {
     // Restores columns to their default display and sort order for the mode
      $csid = $this->csSession->csid;
      $mode = $this->csSession->mode;
      $modecol = "m$mode";
      foreach ($this->csFilter as $fd=>$filfd) {
          $this->csFilter[$fd][$modecol]  = $this->csFields[$fd][$modecol];
          $this->csFilter[$fd]['sortpri'] = $this->csFields[$fd]['sortpri'];
          $this->csFilter[$fd]['sortord'] = $this->csFields[$fd]['sortord'];
      }
      $this->storeCsFilter();
  }


  public function addColHtml() {
     // Creates a select box for choosing a column to be added to the table

      $T = new SM_T('clilstore/addColHtml');
      $T_Add_a_column      = $T->h('Add_a_column');
      $T_Add_a_column_title = $T->h('Add_a_column_title');

      $csid = $this->csSession->csid;
      $mode = $this->csSession->mode;
      $opts = array();
      foreach ($this->csFilter as $r) {
          $fd   = $r['fd'];
          if ($r["m$mode"]<>1) { $opts[] = "<option value='$fd'>{$T->h("csCol_$fd")}</option>"; }
      }
      if (empty($opts)) { return ''; }
      $options = implode("\r",$opts);
      $selectHtml = <<<END_addColHtml
<select name="addCol" style="background-color:white" onchange="addColChange(this.value)" title="$T_Add_a_column_title">
<option value="">$T_Add_a_column</option>
$options
</select>
END_addColHtml;
      return $selectHtml;
  }


  public function setFilter($f) {
     // Stores in table csFilter the current filter parameters for the session
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $csid = $this->csSession->csid;
      $stmt1 = $DbMultidict->prepare('SELECT fd,minmax FROM csFields');
      $stmt1->execute();
      $res = $stmt1->fetchAll(PDO::FETCH_OBJ);
      $stmt1 = null;
      $stmt2 = $DbMultidict->prepare('UPDATE csFilter SET val1=:val1,val2=:val2 WHERE csid=:csid AND fd=:fd');
      foreach ($res as $r) {
          $fd     = $r->fd;
          $minmax = $r->minmax;
          $val1 = $val2 = '';
          if ($minmax==0) {
              if (isset($f[$fd.'Fil'])) { $val1 = $f[$fd.'Fil']; }
          } else {
              if (isset($f[$fd.'Min'])) { $val1 = $f[$fd.'Min']; }
              if (isset($f[$fd.'Max'])) { $val2 = $f[$fd.'Max']; }
          }
          $stmt2->execute(array(':val1'=>$val1,':val2'=>$val2,':csid'=>$csid,':fd'=>$fd));
      }
      $stmt2 = null;
  }


  public function getFilter(&$f) {
     // Retrieves from table csFilter the filter parameters for the session
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $csid = $this->csSession->csid;
      $stmt = $DbMultidict->prepare('SELECT csFields.fd,csFields.minmax,csFilter.val1,csFilter.val2 FROM csFields,csFilter WHERE csid=:csid AND csFields.fd=csFilter.fd');
      $stmt->bindParam(':csid',$csid, PDO::PARAM_INT);
      $stmt->execute();
      $res = $stmt->fetchAll(PDO::FETCH_OBJ);
      $stmt = null;
      foreach ($res as $r) {
          $fd     = $r->fd;
          $minmax = $r->minmax;
          $val1   = $r->val1;
          $val2   = $r->val2;
          if ($minmax==0) {
              $f[$fd.'Fil'] = $val1;
          } else {
              $f[$fd.'Min'] = $val1;
              $f[$fd.'Max'] = $val2;
          }
      }
  }


  public static function levelVis2Num ($lev,$minOrMax) {
     // Converts a CEFR level (A1..C2) to a numeric value appropriate for a minimum or maximum.
     // Leaves numeric values unchanged.  Converts illegal values to ''.
      $lev = strtoupper($lev);
      if ($minOrMax=='min') {
          $trArr = array('A1'=>'0', 'A2'=>'10', 'B1'=>'20', 'B2'=>'30', 'C1'=>'40', 'C2'=>'50',
                         'A' =>'0',             'B' =>'20',             'C' =>'40'             );
      } elseif ($minOrMax=='max') {
          $trArr = array('A1'=>'9', 'A2'=>'19', 'B1'=>'29', 'B2'=>'39', 'C1'=>'49', 'C2'=>'59',
                                    'A' =>'19',             'B' =>'39',             'C' =>'59' );
      } else { throw new SM_Exception("Error in levelVis2Num: invalid \$minOrMax value $minOrMax"); }
      if (isset($trArr[$lev])) {
          return $trArr[$lev];
      } elseif (!preg_match('|^[0-9]{1,2}$|',$lev)) {
          return '';
      } else {
          $lev = (int)$lev;
          if ($lev>59) { return ''; } else { return $lev; }
      }
  }


  public static function levelNum2Vis ($lev,$minOrMax) {
     // Converts a numeric level to CEFR format (A1..C2) if the numeric value is precisely the minimum or maximum for that CEFR level.
     // Otherwise returns the level unchanged.
      if ($minOrMax=='min') {
          if     ($lev== 0) { return 'A1'; }
          elseif ($lev==10) { return 'A2'; }
          elseif ($lev==20) { return 'B1'; }
          elseif ($lev==30) { return 'B2'; }
          elseif ($lev==40) { return 'C1'; }
          elseif ($lev==50) { return 'C2'; }
          else              { return $lev; }
      } elseif ($minOrMax=='max') {
          if     ($lev== 9) { return 'A1'; }
          elseif ($lev==19) { return 'A2'; }
          elseif ($lev==29) { return 'B1'; }
          elseif ($lev==39) { return 'B2'; }
          elseif ($lev==49) { return 'C1'; }
          elseif ($lev==59) { return 'C2'; }
          else              { return $lev; }
      } else { throw new SM_Exception("Error in levelVis2Num: invalid \$minOrMax value $minOrMax"); }
  }


  public function levelButHtml () {
     // Returns the HTML for displaying CEFR level choice buttons

      $T = new SM_T('clilstore/levelButHtml');
      $T_Level    = $T->_('csCol_level');
      $T_Basic    = $T->_('Basic');
      $T_Advanced = $T->_('Advanced');
      $T_Any      = $T->_('Any');

      $labels = array(-1=>"$T_Any",0=>'A1',1=>'A2',2=>'B1',3=>'B2',4=>'C1',5=>'C2');
      $counts = array(-1=>0, 0=>0, 1=>0, 2=>0, 3=>0, 4=>0, 5=>0 );
      $hrefSelf = $_SERVER['PHP_SELF'];
      $sl    = $this->csFilter['sl']['val1'];
      if (isset($_REQUEST['sl'])) { $sl = $_REQUEST['sl']; } //This should not be necessary once the program is changed to feed all request parameters properly into the class constructor
      if ($sl==='') { $sl='%'; }
      $level = $this->csFilter['level']['val1'];
      if ($level==='') { $cefrSel = -1; } else { $cefrSel = $level/10; }
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare('SELECT COUNT(1) AS cnt, ((level+10) DIV 10)-1 AS cefr FROM clilstore WHERE sl LIKE :sl AND test=0 GROUP BY cefr');
      $stmt->execute(array(':sl'=>$sl));
      $res = $stmt->fetchAll(PDO::FETCH_OBJ);
      $stmt = null;
      $totalCount = 0;
      foreach ($res as $r) {
          $counts[$r->cefr] = $r->cnt;
          $totalCount += $r->cnt;
      }
      $counts[-1] = $totalCount;
      for ($cefr=-1; $cefr<=5; $cefr++ ) {
          $label = $labels[$cefr];
          $href = "$hrefSelf?levelBut=$label";
          $count = $counts[$cefr];
          $class = 'levelbutton live';
          if ($count==0) {
              $class = 'levelbutton grey';
              $href = '';
          }
          if ($cefr==$cefrSel) {
              $class .= ' selected';
              $href = '';
          }
          $button[$cefr] = "<a href=\"$href\" class=\"$class\" title=\"$count units\">$label</a>";
      }
      $buttonAny = $button[-1];
      $buttonA1  = $button[0];
      $buttonA2  = $button[1];
      $buttonB1  = $button[2];
      $buttonB2  = $button[3];
      $buttonC1  = $button[4];
      $buttonC2  = $button[5];
      $html = <<<ENDHTML
<p style="padding:2px;margin:0 0 1.5em 0"><span style="background-color:#def;padding:5px 5px">$T_Level
$buttonAny
<span style="color:green;font-size:80%;padding-left:3em">$T_Basic</span>
$buttonA1
$buttonA2
$buttonB1
$buttonB2
$buttonC1
$buttonC2
<span style="color:green;font-size:80%">$T_Advanced</span></span></p>
ENDHTML;
      return $html;
  }


  public static function secs2minsecs ($secs) {
     // Converts an integer number of seconds such as 75 to a minutes and seconds display such as 1:15
     // (also displays hours such as 2:23:43 if required)
        if (empty($secs))  { return '?:??'; }
        $secs = (int)round($secs); //Just in case it had a fractional part
        if ($secs<60) { return $secs; }
        $s = $secs % 60;
        $mins = ($secs-$s)/60;
        if ($mins<60) { return sprintf('%2d:%02d',$mins,$s); }
        $m = $mins % 60;
        $hours = ($mins-$m)/60;
        return sprintf('%2d:%02d:%02d',$hours,$m,$s);
  }

  public static function minsecs2secs ($minsecs) {
     //Converts minsecs from string such as '2:30' or '2m30' to seconds
      if (empty($minsecs)) { return 0; }
      $minsecs = str_replace(' ','',$minsecs);
      if (preg_match('|^(\d+)s?$|i',$minsecs,$matches)) {
          return $matches[1];
      } elseif (preg_match('|^(\d+):(\d+)s?$|i',$minsecs,$matches)) {
          return 60*$matches[1] + $matches[2];
      } elseif (preg_match('|^(\d+)m(\d+)s?$|i',$minsecs,$matches)) {
          return 60*$matches[1] + $matches[2];
      } else {
          return -1;  //error
      }
  }


  public static function cefrHtml ($level) {
     //Converts a numeric Clilstore internal language level to a display of the CEFR level
      if      ($level<0)   { $cefr = '';   }
       elseif ($level<5)   { $cefr = 'A1<span class="fann">-</span>'; }
       elseif ($level==5)  { $cefr = 'A1<span class="fann">&nbsp;</span>'; }
       elseif ($level<10)  { $cefr = 'A1<span class="fann">+</span>'; }
       elseif ($level<15)  { $cefr = 'A2<span class="fann">-</span>'; }
       elseif ($level==15) { $cefr = 'A2<span class="fann">&nbsp;</span>'; }
       elseif ($level<20)  { $cefr = 'A2<span class="fann">+</span>'; }
       elseif ($level<25)  { $cefr = 'B1<span class="fann">-</span>'; }
       elseif ($level==25) { $cefr = 'B1<span class="fann">&nbsp;</span>'; }
       elseif ($level<30)  { $cefr = 'B1<span class="fann">+</span>'; }
       elseif ($level<35)  { $cefr = 'B2<span class="fann">-</span>'; }
       elseif ($level==35) { $cefr = 'B2<span class="fann">&nbsp;</span>'; }
       elseif ($level<40)  { $cefr = 'B2<span class="fann">+</span>'; }
       elseif ($level<45)  { $cefr = 'C1<span class="fann">-</span>'; }
       elseif ($level==45) { $cefr = 'C1<span class="fann">&nbsp;</span>'; }
       elseif ($level<50)  { $cefr = 'C1<span class="fann">+</span>'; }
       elseif ($level<55)  { $cefr = 'C2<span class="fann">-</span>'; }
       elseif ($level==55) { $cefr = 'C2<span class="fann">&nbsp;</span>'; }
       elseif ($level<60)  { $cefr = 'C2<span class="fann">+</span>'; }
      $cefrHtml  = ( empty($cefr) ? '' : "<span title=\"$level\">$cefr</span>" );
      return $cefrHtml;
  }

  public static function logWrite($user, $type, $info='', $user2=NULL) {
     // Writes to the log of significant events (evenus such as logins, username changes)
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare('INSERT INTO log SET user=:user, utime=:utime, ip=:ip, type=:type, info=:info, user2=:user2');
      $stmt->bindParam(':user',$user);
      $stmt->bindParam(':utime',$utime,PDO::PARAM_INT);
      $stmt->bindParam(':ip',$ip);
      $stmt->bindParam(':type',$type);
      $stmt->bindParam(':info',$info);
      $stmt->bindParam(':user2',$user2);
      $utime = time();
      $ip = $_SERVER['REMOTE_ADDR'];
      $stmt->execute();
      $stmt = null;
  }

  public static function csTitle($id) {
     // Returns the title of the Clilstore unit with id $id
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare('SELECT title FROM clilstore WHERE id=:id');
      $stmt->execute(['id'=>$id]);
      $title = $stmt->fetchColumn();
      if ($title) { return $title; } else { return ''; }
  }

}
?>
