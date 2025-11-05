<?php
class SM_WlSession {

  public $uid, $sid, $sl, $tl, $dict, $url, $word, $wfs, $inc, $rmLi, $mode, $navsize, $tlRanks, $dictRanks, $tlArr;

  private $slPrev, $tlPrev, $dictPrev, $wfRot, $ind;

  public function __construct($sid=null) {
//For diagnostics
//$this->ind=-1;
      // If $sid is not passed as a non-null parameter then create a new session and store its record in the database,
      // giving it the next available id and the default values.
      // In any case, then retrieve session information from the database to populate the class variables

      // Overwrite the class variables with any values from the GET parameters in the HTTP request.
      // Store "user id", $uid in the permanent cookie wlUser
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');

      // Set and log uid (user id - stored in a permanent cookie called wlUser)
      if (isset($_COOKIE['wlUser'])) {
          $uid = $_COOKIE['wlUser'];
      } else {  // No uid so create a new one
          $crIP = $_SERVER['REMOTE_ADDR'];
          $crTime = time();
          $crHost = gethostbyaddr($crIP);
          $crReferer = ( isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
          $stmtSELmax = $DbMultidict->prepare("SELECT MAX(uid) FROM wlUser");
          $stmtINSuid = $DbMultidict->prepare("INSERT INTO wlUser (uid,IP,crIP,utime,crTime,crHost,crReferer)"
                                      . " VALUES (:uid,:crIP,:crIP,:crTime,:crTime,:crHost,:crReferer)");
        $DbMultidict->beginTransaction();
          $stmtSELmax->execute();
          $uid = $stmtSELmax->fetchColumn() + 1;
          $stmtINSuid->execute([':uid'=>$uid, ':crIP'=>$crIP, ':crTime'=>$crTime, ':crHost'=>$crHost, 'crReferer'=>$crReferer]);
        $DbMultidict->commit();
          $stmtSELmax = $stmtINSuid = null;
      }
      $utime = time();
      $IP = $_SERVER['REMOTE_ADDR'];
      $stmt = $DbMultidict->prepare("UPDATE wlUser SET calls=calls+1, IP=:IP, utime=:utime WHERE uid=:uid");
      $stmt->bindParam(':IP',$IP);
      $stmt->bindParam(':utime',$utime,PDO::PARAM_INT);
      $stmt->bindParam(':uid',$uid,PDO::PARAM_INT);
      $stmt->execute();
      $stmt = null;

      setcookie ('wlUser', $uid, time()+31556926, '/' );  //Store cookie for up to a year

      if (is_null($sid)) {  // No sid so create a new one
          $stmt = $DbMultidict->prepare("SELECT MAX(sid) AS sidMax FROM wlSession");
          $stmt->execute();
          $stmt->bindColumn(1,$sidMax);
          $stmt->fetch();
          $stmt = null;
          $sid = $sidMax+1;
          $utime = time();
          $stmt = $DbMultidict->prepare("INSERT INTO wlSession (sid,utime) VALUES (:sid,:utime)");
          $stmt->bindParam(':sid',$sid,PDO::PARAM_INT);
          $stmt->bindParam(':utime',$utime,PDO::PARAM_INT);
          $stmt->execute();
          $stmt = null;
      }
      $stmt = $DbMultidict->prepare("SELECT sl,tl,dict,url,word,wfs,inc,rmLi,mode,navsize,utime FROM wlSession WHERE sid=:sid");
      $stmt->bindParam(':sid',$sid,PDO::PARAM_INT);
      $stmt->execute();
      $r = $stmt->fetch(PDO::FETCH_ASSOC);
      extract($r);
      $stmt = null;
      if (empty($mode)) { $mode = 'ss'; }
      if (empty($wfs)) { $wfs = $word; }
      $this->slPrev   = $sl;
      $this->tlPrev   = $tl;
      $this->dictPrev = $dict;
      $this->wordPrev = $word;

      // Read GET parameters() {
      $this->wfRot = '';  // wfRot is for remembering whether recalculation or rotation of wordforms will be required before saving to wlSession
      if (!empty($_GET['sl']  ) and $sl<>$_GET['sl']) { $sl = $_GET['sl']; $tl ='';  $dict = '';  }
      if (!empty($_GET['tl']  ) and $tl<>$_GET['tl']) { $tl = $_GET['tl'];           $dict = '';  }
      if (!empty($_GET['dict'])) { $dict = $_GET['dict']; }
      if (!empty($_GET['url'] )) { $url  = $_GET['url'];  }
      if (isset($_GET['word'])) {
          $getword = trim($_GET['word']);
          if ($word==$getword) {
              $this->wfRot = 1; //Rotate by 1
              if (isset($_GET['rot'])) {
                  $rot = $_GET['rot'];
                  if (is_numeric($rot) && $rot<20) { $this->wfRot = $rot; }
              }
          } else {
              $this->wfRot = 'recalc';
              $word = $getword;
          }
      }
      if (isset($_GET['go'])) {
          if     ($_GET['go']=='>') { $inc++; $this->wfRot=0; }
          elseif ($_GET['go']=='<') { $inc--; $this->wfRot=0; }
          else                      { $inc = 0; }
      }
      if (!empty($_GET['encoding'])) { $word    = iconv($_GET['encoding'],'UTF-8',$word); }
      if (!empty($_GET['mode']))     { $mode    = $_GET['mode'];    }
      if (!empty($_GET['navsize']))  { $navsize = $_GET['navsize'];
                                       if (!is_numeric($navsize)) { throw new SM_MDexception('Non numeric navsize parameter'); }
                                       $navsize = (int)$navsize;
                                       if ($navsize<-1 || $navsize>1000) { throw new SM_MDexception('navsize parameter not in the range -1 to 1000'); }
                                     }
      // N.B. Don't read  rmLi, upload - These are handled separately by /wordlink/index.php alone
      if (preg_match('/^referer$|^referrer$|^self$/i',$url)==1 && !empty($_SERVER['HTTP_REFERER'])) {
          $url = $_SERVER['HTTP_REFERER'];
      } elseif (!empty($url) && $url<>'{compose}') {
          $url = trim($url);
          $url = strtr ( $url, array('{and}'=>'&') );  //Translate back any protected ampersands
          if (substr($url,0,2)=='//')       { $url = "http:$url"; }  //If the protocol is empty, use http
          if (!(strpos($url,'://')>0) and !(substr($url,0,4)=='http')) { $url = "http://$url"; }  //if  there is still no sign of a protocol, use http
          if (strpos($url,'google.com')>0) { throw new SM_MDexception('Sorry, Wordlink does not currently work with Google Drive or other Google sites'); }
      }

      // If sl or tl or dict are missing, fill them in from the user's most recent choices (provided uid can be found in a cookie)
      if (isset($_COOKIE['wlUser'])) {
          if (empty($sl)) {
              $stmt = $DbMultidict->prepare("SELECT sl,tl,dict FROM wlUserSlTl WHERE uid=:uid ORDER BY utime DESC LIMIT 1");
              $stmt->bindParam(':uid',$uid,PDO::PARAM_INT);
              $stmt->execute();
              $stmt->bindColumn(1,$sl);
              $stmt->bindColumn(2,$tl);
              $stmt->bindColumn(3,$dict);
              $stmt->fetch();
              $stmt = null;
          } elseif (empty($tl)) {
              $stmt = $DbMultidict->prepare("SELECT tl,dict FROM wlUserSlTl WHERE uid=:uid AND sl=:sl ORDER BY utime DESC LIMIT 1");
              $stmt->bindParam(':uid',$uid,PDO::PARAM_INT);
              $stmt->bindParam(':sl',$sl);
              $stmt->execute();
              $stmt->bindColumn(1,$tl);
              $stmt->bindColumn(2,$dict);
              $stmt->fetch();
              $stmt = null;
          } elseif (empty($dict)) {
              $stmt = $DbMultidict->prepare("SELECT dict FROM wlUserSlTl WHERE uid=:uid AND sl=:sl AND tl=:tl");
              $stmt->bindParam(':uid',$uid,PDO::PARAM_INT);
              $stmt->bindParam(':sl',$sl);
              $stmt->bindParam(':tl',$tl);
              $stmt->execute();
              $stmt->bindColumn(1,$dict);
              $stmt->fetch();
              $stmt = null;
          }
      }

      //Set up the preg pattern determining words in the source language
      if      ($sl=='br')  { $wordpreg = '([cC][\'’”][hH]|\p{L}\p{M}*)+'; } //Allow c’h (and c”h) as a “letter” in Breton
       elseif ($sl=='ca')  { $wordpreg =     '([lL]·[lL]|\p{L}\p{M}*)+'; }  //Allow l·l as a “letter” in Catalan 
       elseif ($sl=='sga') { $wordpreg =             '(·|\p{L}\p{M}*)+'; }  //Allow  ·  as a “letter” in Old Irish
       elseif ($sl=='oc')  { $wordpreg =   '([sSnN]·[hH]|\p{L}\p{M}*)+'; }  //Allow s·h and n·h as “letters” in Occitan 
       else                { $wordpreg =               '(\p{L}\p{M}*)+'; }

      $this->uid    = $uid;
      $this->sid    = $sid;
      $this->sl     = $sl;
      $this->tl     = $tl;
      $this->dict   = $dict;
      $this->url    = $url;
      $this->word   = $word;
      $this->wfs    = $wfs;
      $this->inc    = $inc;
      $this->rmLi   = $rmLi;
      $this->mode   = $mode;
      $this->navsize= $navsize;
      $this->wordpreg=$wordpreg;

  }


  public function storeVars() {
      $sid    = $this->sid;
      $sl     = $this->sl;
      $tl     = $this->tl;
      $dict   = $this->dict;
      $url    = $this->url;
      $word   = $this->word;
      $wfs    = $this->wfs;
      $inc    = $this->inc;
      $rmLi   = $this->rmLi;
      $mode   = $this->mode;
      $navsize= $this->navsize;
      $utime  = time();

      if ($this->wfRot<>'') {
          if ( $sl<>$this->slPrev || $tl<>$this->tlPrev || $dict<>$this->dictPrev ) { $this->wfRot = 'recalc'; }
          if ( $this->wfRot=='recalc') {
               $wfrule = self::wfruleGet();
               $wordArr = array_unique(array(lcfirst($word),$word));
               $wfsArr = self::wfCalcArr($wordArr,$wfrule);
               $wfs = implode('|',$wfsArr);
               if (empty($wfs)) { $wfs = $word; }
          } else {
               $wordformArr = explode('|',$this->wfs);
               for ($i=1;$i<=$this->wfRot;$i++) { array_push($wordformArr, array_shift($wordformArr)); } // Rotate wfs
               $wfs = implode('|',$wordformArr);
          }
      }
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $query = <<<EOSQL
          UPDATE wlSession
            SET sl=:sl,tl=:tl,dict=:dict,url=:url,word=:word,wfs=:wfs,inc=:inc,rmLi=:rmLi, mode=:mode, navsize=:navsize, utime=:utime
            WHERE sid=:sid
          EOSQL;
      $stmt = $DbMultidict->prepare($query);
      $stmt->bindParam(':sl',$sl);
      $stmt->bindParam(':tl',$tl);
      $stmt->bindParam(':dict',$dict);
      $stmt->bindParam(':url',$url);
      $stmt->bindParam(':word',$word);
      $stmt->bindParam(':wfs',$wfs);
      $stmt->bindParam(':inc',$inc,PDO::PARAM_INT);
      $stmt->bindParam(':rmLi',$rmLi,PDO::PARAM_INT);
      $stmt->bindParam(':mode',$mode);
      $stmt->bindParam(':navsize',$navsize);
      $stmt->bindParam(':utime',$utime,PDO::PARAM_INT);
      $stmt->bindParam(':sid',$sid,PDO::PARAM_INT);
      $stmt->execute();
      $stmt = null;
  }


  private function wfruleGet() {
    // Returns the string of rules appriopriate for deriving a string of wordforms to search for in succession in the current dictionary
      $sl   = $this->sl;
      $tl   = $this->tl;
      $dict = $this->dict;
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare("SELECT wfrule FROM dictParam WHERE dict=:dict AND ((sl=:sl AND tl=:tl) OR sl='¤')");
      $stmt->bindParam(':sl',$sl);
      $stmt->bindParam(':tl',$tl);
      $stmt->bindParam(':dict',$dict);
      $stmt->execute();
      $stmt->bindColumn(1,$wfrule);
      $stmt->fetch();
      $stmt = null;
      $wfrule = strtr($wfrule,array(' '=>'')); // remove any spaces
      if (empty($wfrule)) { //If there is no specific rule for the dictionary, then...
                           $wfrule  = 'lemtable~pri|prialg|self|lemtable|hun|lemalg';
          if ($sl=='en')  { $wfrule  = 'lemtable~pri|prialg|self|lemtable|hun|(lemtable~us2gb|lemtable~gb2us)>(self|hun)|lemalg'; }
          if ($sl=='ar')  { $wfrule  = str_replace('hun','hun|hun:ar_Aya',    $wfrule); }  //Actually Ayaspell doesn’t seem to give much if any gain
          if ($sl=='arz') { $wfrule  = str_replace('hun','hun:ar|hun:ar_Aya', $wfrule); }  //Actually Ayaspell doesn’t seem to give much if any gain
          if ($sl=='ru')  { $wfrule  = str_replace('hun','hun|hun:ru_GooCode',$wfrule); }
          if ($sl=='hy')  { $wfrule  = str_replace('hun','hun|hun:hy_e|hun:hy_w|hun:hy_e1940', $wfrule); }
          if ($sl=='ga')  { $wfrule .= '|lemtable~scannell'; }
          if ($sl=='ga' and substr($dict,0,7)=='Duinnin') { $wfrule .= '|lemtable~scannellRev'; }
          if ($sl=='sga') { $wfrule = '(self|splitMiddot)>'.$wfrule; }
      }
      return $wfrule;
  }


  private function prialg ($wordform,$sl) {
    // Attempts to lemmatizes a word algorithmicly, as appropriate to the language, returning an array of suggested lemmas.
    // Returns an empty array if it has no good suggestions.
    // This function applies the priority rules which we want to try first. Other rules are left for another function, lemalg.
      $word = $wordform;
      if ((($sl=='gd') or ($sl=='ga') or ($sl=='sga')) and (substr($word,1,1)=='h')) { $word = substr($word,0,1).substr($word,2); }
      if ($sl=='ga') {
          $first    = substr($word,0,1); //first letter
          $second   = substr($word,1,1); //second letter
          $firstTwo = substr($word,0,2); //first two letters
          if ($firstTwo=='mb') { $word = 'b'.substr($word,2); }
          if ($firstTwo=='gc') { $word = 'c'.substr($word,2); }
          if ($firstTwo=='nd') { $word = 'd'.substr($word,2); }
          if ($firstTwo=='bf') { $word = 'f'.substr($word,2); }
          if ($firstTwo=='ng') { $word = 'g'.substr($word,2); }
          if ($firstTwo=='dt') { $word = 't'.substr($word,2); }
          if ($firstTwo=='ts') { $word = 's'.substr($word,2); }
          if ($firstTwo=='mB') { $word = 'B'.substr($word,2); }
          if ($firstTwo=='gC') { $word = 'C'.substr($word,2); }
          if ($firstTwo=='nD') { $word = 'D'.substr($word,2); }
          if ($firstTwo=='bF') { $word = 'F'.substr($word,2); }
          if ($firstTwo=='nG') { $word = 'G'.substr($word,2); }
          if ($firstTwo=='dT') { $word = 'T'.substr($word,2); }
          if ($firstTwo=='tS') { $word = 'S'.substr($word,2); }
          if ( ($first=='n') and (strpos('AEIOUÁÉÍÓÚ',$second)  !== false )) { $word = substr($word,1); }
          if ( ($first=='h') and (stripos('AEIOUÁÉÍÓÚ',$second) !== false )) { $word = substr($word,1); }
      }
      if ($word==$wordform)  return array();
      return array($word);
  }


  private function lemalg ($wordform,$sl) {
    // Attempts to lemmatizes a word algorithmicly, as appropriate to the language, returning an array of suggested lemmas.
    // Returns an empty array if it has no good suggestions.
    // (The top priority rules are tried first by another function, prialg.)
      $word = $wordform;
      $len = strlen($word);
      if ($sl=='en' && $len>4) {
          if (substr($word,-1)=='s'
           && substr($word,-2)<>'ss') { $word = substr($word,0,-1); }
          if ($len>6) { //Try some en-US ~ en-GB spelling conversion
              switch (substr($word,-3)) {
                  case 'yze':
                      $word = substr($word,0,-3).'yse'; break;
                  case 'ise':
                      $last4 = substr($word,-4); if ($last4=='cise' || $last4=='prise' || $last4=='vise') break;
                      if ($word=='advertise'||$word=='chastise'||$word=='despise'||$word=='disguise'||$word=='franchise'||$word=='merchandise'||$word=='surmise') break;
                      $word = substr($word,0,-3).'ize'; break;
                  case 'ize':
                      if ($word=='capsize') break;
                      $word = substr($word,0,-3).'ise'; break;
              }
              switch (substr($word,-7)) {
                  case 'ization': $word = substr($word,0,-7).'isation';
                  case 'isation': $word = substr($word,0,-7).'ization';
              }
          }
      }
      if (($sl=='sco' || $sl='scz' || $sl=='fy') && $len>4) {
          if (substr($word,-1)=='s'
           && substr($word,-2)<>'ss') { $word = substr($word,0,-1); }
          if (substr($word,-2)=='en') { $word = substr($word,0,-2); }
      }
      if ($sl=='br' || $sl=='cy' || $sl=='kw') {
        //Demutation procedure for P-celtic languages.  Could no doubt be refined, esp. for Cornish,
        //to reflect the fact that if a specific rules applies, the general one probably should not.
          $demutArr = $resArr = array();
          $word = strtr($word,array("'"=>"’"));  // standardize to curly quote
          $demutArr[] = $word;
          if ($sl=='br') {
              $mutRules = array('soft'     => array('p'=>'b','t'=>'d','k'=>'g','b'=>'v','d'=>'z','g'=>'c’h','gw'=>'w','m'=>'v'),
                                'aspirated'=> array('p'=>'f','t'=>'z','k'=>'c’h'),
                                'strong'   => array('b'=>'p','d'=>'t','g'=>'k','gw'=>'gw'),
                                'mixed'    => array('b'=>'v','d'=>'t','g'=>'c’h','gw'=>'w','m'=>'v') );
          } elseif ($sl=='cy') {
              $mutRules = array('soft'     => array('c'=>'g','p'=>'b','t'=>'d','b'=>'f','d'=>'dd','m'=>'f','ll'=>'l','rh'=>'r'), // 'g'=>'' omitted (too difficult)
                                'nasal'    => array('c'=>'ngh','p'=>'mh','t'=>'nh','b'=>'m','d'=>'n','g'=>'ng'),
                                'aspirated'=> array('c'=>'ch','p'=>'ph','t'=>'th') );
          } elseif ($sl=='kw') {
              $mutRules = array('soft'     => array('p'=>'b','t'=>'d','k'=>'g','b'=>'v','d'=>'dh','m'=>'v','ch'=>'j',
                                                    'go'=>'wo','gu'=>'wu','gri'=>'wri','gry'=>'wry','gre'=>'wre','gra'=>'wra','gw'=>'w'),
                                'aspirate' => array('p'=>'f','t'=>'th','k'=>'h'),
                                'hard'     => array('b'=>'p','d'=>'t','g'=>'k','gw'=>'kw'),
                                'mixed'    => array('b'=>'f','d'=>'t','m'=>'f',
                                                    'gi'=>'hi','gy'=>'hy','ge'=>'he','ga'=>'ha','gl','hl','gro'=>'hro','gru'=>'hru',
                                                    'go'=>'hwo','gu'=>'hwu','gri'=>'wi','gry'=>'wy','gre'=>'we','gra'=>'wa','gw'=>'hw'),
                                'tradglyph'=> array('k'=>'c','hw'=>'wh','kw'=>'qw','g'=>'wh','gw'=>'wh','c'=>'g') );
          }
          foreach ($mutRules as $mutRule) {
              foreach ($mutRule as $init=>$mut) {
                  $mutlen = strlen($mut);
                  if ( strlen($word)>$mutlen && substr($word,0,$mutlen)==$mut ) { 
                      $demutArr[] = $init.substr($word,$mutlen);
                  }
              }
          }
          if ($sl=='br') { //For Breton, need to allow for both ascii c'h and non-ascii c’h
              foreach ($demutArr as $demut) {
                  $demutAscii = strtr($demut,array("’"=>"'"));
                  if ($demutAscii<>$demut) { $resArr[] = $demutAscii; }
                  $resArr[] = $demut;
              }
              return $resArr;
          }
          return $demutArr;
      }
      if ($sl=='ja') {
          $wordArr = array();
          $process = proc_open ( 'mecab', [ 0=>['pipe','r'], 1=>['pipe','w'], 2=>['file','/dev/null','a'] ], $pipes );
          fwrite($pipes[0],$word);  //stdin
          fclose($pipes[0]);
          $response = stream_get_contents($pipes[1]);  //stdout
          fclose($pipes[1]);
          proc_close($process);
          $lines = explode("\n", $response);
          $line = $lines[0];
          if (!empty($line) && $line<>'EOS') {
              $bits = explode("\t", $line);
              $jaWord = $bits[0];
              $moreBits = explode(',', $bits[1]);
              $wordArr[] = $moreBits[5];
          }
          return $wordArr;
      }
      if ($word==$wordform)  return array();
      return array($word);
  }


  private function arroot ($wordform) {
    // Obtains the root of an Arabic word using the ISRIStemmer in nltk
      $document_root = $_SERVER['DOCUMENT_ROOT'];
      $root = shell_exec("python3 ".escapeshellarg($document_root/multidict/arroot.py.' '.$wordform));
      return array($root);
  }

  private function wfCalcArr($word,$wfrule) {
//Old diagnostic code commented out
//$this->ind++;
//$pad=str_pad('',4*$this->ind);
//if (is_array($word)) { $wordDisp = '['.implode(',',$word).']'; } else { $wordDisp = $word; }
//error_log($pad."Enter wfCalcArr: \$wfrule=$wfrule  \$word=$wordDisp     ");
    // Calculates a array of suggested wordforms (possible lemmas, etc) for trying to find the words of $wordArr in the current dictionary.
    // To do this, it uses the rule string $wfrule, which should be appropriate to the current dictionary.
    // wfCalcArr calls itself recursively, both to deal with complex rule strings and to deal with the case where $word is actually an array of words.
      $sl   = $this->sl;
      if (substr($wfrule,0,1)=='(') { //Deal with rules containing bracketed expressions
//error_log('BRACKETS: $wfrule='.$wfrule);
          $nbrackets = 1;
          for ($i=1;$i<strlen($wfrule);$i++) {
              if      (substr($wfrule,$i,1)=='(') { $nbrackets++; }
               elseif (substr($wfrule,$i,1)==')') { $nbrackets--; }
              if ($nbrackets==0) { break; }
          }
          if ($nbrackets>0) { throw new SM_MDexception('Unclosed brackets in wfrule'); }
          $cli = substr($wfrule,1,$i-1);
          $deas = trim(substr($wfrule,$i+1));
          if (empty($deas)) {
              $wfArr = self::wfCalcArr($word,$cli);
          } else {
              $operator = substr($deas,0,1);
              $deas     = substr($deas,1);
              if     ($operator=='|') { $wfArr = array_unique(array_merge (self::wfCalcArr($word,$cli), self::wfCalcArr($word,$deas) )); }
              elseif ($operator=='>') {
                  $wfArr = array();
                  foreach (self::wfCalcArr($word,$cli) as $wordCli) {
                      $wfArr = array_merge( $wfArr, self::wfCalcArr($wordCli,$deas) );
                  }
                  $wfArr = array_unique($wfArr);
              }
              else { throw new SM_MDexception('Invalid operator in $wfRule'); }
          }
      } elseif ( preg_match( '/(.*?)\>(.*)/', $wfrule, $matches ) ) {  //Deal with the operator ‘>’, which denotes concatenation of rules 
          $wfrule1 = $matches[1];
          $wfrule2 = $matches[2];
          $wfArr1 = self::wfCalcArr($word,$wfrule1);
          $wfArr  = self::wfCalcArr($wfArr1,$wfrule2);
      } elseif ( preg_match( '/(.*?)\|(.*)/', $wfrule, $matches ) ) {  //Deal with the operator ‘|’, which denotes unions of rules 
          $wfrule1 = $matches[1];
          $wfrule2 = $matches[2];
          $wfArr1 = self::wfCalcArr($word,$wfrule1);
          $wfArr2 = self::wfCalcArr($word,$wfrule2);
          $wfArr = array_merge($wfArr1,$wfArr2);
      } elseif (is_array($word)) {
          $wfArr = array();
          foreach ($word as $w)  {
              $wfArr = array_merge($wfArr,self::wfCalcArr($w,$wfrule));
          }
      } elseif ($wfrule=='self') {
          $wfArr = array($word);
      } else {
          $slEffective = $sl;
          $batch = '';
          $bits = explode('~',$wfrule);  if (sizeof($bits)==2) { $wfrule = $bits[0]; $batch       = $bits[1]; }
          $bits = explode(':',$wfrule);  if (sizeof($bits)==2) { $wfrule = $bits[0]; $slEffective = $bits[1]; }
          if ($wfrule=='hun') {
              $wfArr = array();
              if (file_exists("/usr/share/hunspell/$slEffective.aff")) {
                  $env = ['LANG' => 'en_US.utf-8'];
                  $process = proc_open ( "hunspell -m -d $slEffective", [ 0=>['pipe','r'], 1=>['pipe','w'], 2=>['file','/dev/null','a'] ], $pipes, NULL , $env );
                  fwrite($pipes[0],$word);  //stdin
                  fclose($pipes[0]);
                  $response = stream_get_contents($pipes[1]);  //stdout
                  fclose($pipes[1]);
                  proc_close($process);

                  $lines = explode("\n", $response);
                  $wordpreg = $this->wordpreg;
                  foreach ($lines as $line) {
                      preg_match ('%\sst:('.$wordpreg.')%u' , $line, $matches );
                      if (!empty($matches[1])) { $wfArr[] = $matches[1]; }
                  }
              }
          } elseif ($wfrule=='lemtable') {
            $DbMultidict = SM_DbMultidictPDO::singleton('rw');
              $stmt = $DbMultidict->prepare('SELECT DISTINCT lemma FROM lemmas WHERE lang=:lang AND batch=:batch AND wordform=:wordform');
              $stmt->bindParam(':lang',$slEffective);
              $stmt->bindParam(':batch',$batch);
              $stmt->bindParam(':wordform',$word);
              $stmt->execute();
              $wfArr = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
          } elseif ($wfrule=='prialg') {
              $wfArr = self::prialg($word,$slEffective);
          } elseif ($wfrule=='lemalg') {
              $wfArr = self::lemalg($word,$slEffective);
          } elseif ($wfrule=='arroot') {
              $wfArr = self::arroot($word);
          } elseif ($wfrule=='splitMiddot') {
              $wfArr = explode('·',$word);
          } else { //This shouldn’t happen!
              $wfArr = array();
          }
      }
//Diagnostics
//error_log($pad."Leave wfCalcArr: \$wfrule=$wfrule  \$wfArr=[".implode(',',$wfArr).']     ');
//$this->ind--;
      return array_unique($wfArr);
  }


  public static function updateWlUserSlTl($uid,$sl,$tl,$dict) {
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $utime = time();
      $query = "INSERT INTO wlUserSlTl (uid,sl,tl,calls,dict,utime) VALUES (?,?,?,1,?,?)"
             ." ON DUPLICATE KEY UPDATE calls=calls+1, dict=?, utime=?";
      $stmt = $DbMultidict->prepare($query);
      $stmt->execute(array($uid, $sl, $tl, $dict, $utime, $dict, $utime));
      $stmt = null;
  }


  public function bestDict() {
       // For use with Multidict.
       // If tl is not set or has no dictionary, sets it to the target language with the best quality dictionary.
       // If dict is not set, sets it to the best available dictionary.
      if (empty($this->sl)) { $tl = $dict = $tlRanks = $dictRanks = ''; return 0; }
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $sl = $this->sl;

     // Populate $tlDictQualArray with quality values, first for single dictionaries...
      $stmt = $DbMultidict->prepare("SELECT tl,dict,quality FROM dictParamV WHERE sl=:sl");
      $stmt->bindParam(':sl',$sl);
      $stmt->execute();
      $stmt->bindColumn(1,$tl);
      $stmt->bindColumn(2,$dict);
      $stmt->bindColumn(3,$quality);
      while ($stmt->fetch()) {
          $tlDictQualArray[$tl][$dict] = $quality;
          if (!isset($tlQualArray[$tl])) { $tlQualArray[$tl] = $quality; }
             else                        { $tlQualArray[$tl] = max($quality,$tlQualArray[$tl]); }
      }
      $stmt = null;
     // ...and then for multidictionaries
      $query = "SELECT dictParamV.dict, dictParamV.quality, dictParamV.tl AS monolingCheck, dictLang2.lang, lang.quality"
             . " FROM dictParamV, dictLang,dictLang AS dictLang2, lang"
             . " WHERE dictParamV.dict=dictLang.dict AND dictParamV.dict=dictLang2.dict AND lang.id=dictLang2.lang AND dictLang.lang=:sl";
      $stmt = $DbMultidict->prepare($query);
      $stmt->bindParam(':sl',$sl);
      $stmt->execute();
      $stmt->bindColumn(1,$dict);
      $stmt->bindColumn(2,$quality);
      $stmt->bindColumn(3,$monolingCheck);
      $stmt->bindColumn(4,$tl);
      $stmt->bindColumn(5,$langQual);
      while ($stmt->fetch()) {
          if (($monolingCheck=='x') and ($tl==$sl)) { continue; }
          $quality += $langQual;
          $tlDictQualArray[$tl][$dict] = $quality;
          if (!isset($tlQualArray[$tl])) { $tlQualArray[$tl] = $quality; }
             else                        { $tlQualArray[$tl] = max($quality,$tlQualArray[$tl]); }
      }
      $stmt = null;

      if (empty($tlQualArray)) { throw new SM_MDexception("Error in bestDict: No dictionaries found for the current source language ($sl)"); }

     // Reset wlSession variables tl and dict to suitable values for the current sl if need be; also tlRanks and dictRanks
      arsort($tlQualArray);
      reset($tlQualArray);
      $tl   = $this->tl;
      $dict = $this->dict;
        if (!isset($tlQualArray[$tl])) { $tl = key($tlQualArray); }
        $dictQualArray = $tlDictQualArray[$tl];
        arsort($dictQualArray);
        reset($dictQualArray);
        if (!isset($dictQualArray[$dict])) { $dict = key($dictQualArray);  }
      $this->tl   = $tl;
      $this->dict = $dict;
      $this->tlRanks   = implode('|',array_keys($tlQualArray));
      $this->dictRanks = implode('|',array_keys($dictQualArray));
  }


  public function dictSelectHtml() {
       // Returns html for dictionary selection in forms
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $html = '';
      $dictArray = explode('|',$this->dictRanks);
      $stmt = $DbMultidict->prepare("SELECT name FROM dictParamV WHERE dict=:dict");
      $stmt->bindParam(':dict',$dic);
      $stmt->bindColumn(1,$name);
      foreach ($dictArray as $dic) {
          $stmt->execute();
          $stmt->fetch();
          $selected = ( $dic==$this->dict ? ' selected="selected"' : '');
          $html .= "   <option value=\"$dic\"$selected>$name</option>\n";
      }
      $stmt = null;
      return $html;
  }


  public function dictIconsHtml() {
      // Returns html for row of dictionary favicons
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $html = '';
      $dictArray = explode('|',$this->dictRanks);
      $stmt = $DbMultidict->prepare("SELECT name,icon,class FROM dict WHERE dict=:dict");
      $stmt->bindParam(':dict',$dic);
      $stmt->bindColumn(1,$name);
      $stmt->bindColumn(2,$icon);
      $stmt->bindColumn(3,$class);
      foreach ($dictArray as $dic) {
          $stmt->execute();
          $stmt->fetch();
          if (!empty($icon)) {
              $iconClassArr = [];
              if (!empty($class)) { $iconClassArr[] = $class; }
              if ($dic==$this->dict) { $iconClassArr[] = 'sel'; }
              $iconClass = implode(' ',$iconClassArr);
              $classHtml = ( empty($iconClass) ? '' : "class='$iconClass'" );
              $iconHtml = "<img src='icon.php?dict=$dic' $classHtml title=\"$name\" onclick=\"changeDict('$dic')\" alt=''>";
              $html .= "$iconHtml\n";
          }
      }
      $stmt = null;
      return $html;
  }


  public function dictClass() {
      // Returns the class ('p', 'm', etc) of the currect dictionary
      $dict = $this->dict;
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare('SELECT class FROM dict WHERE dict=:dict');
      $stmt->execute([':dict'=>$dict]);
      $class = $stmt->fetchColumn();
      return $class;
  }


  public function dictIconHtml() {
     // Returns html for a favicon for the current dictionary, if it has one
     // also an ESC button
     // also any user message for the dictionary
      $T = new SM_T('multidict');
      $T_Click_to_escape_Multidict = $T->h('Click_to_escape_Multidict');
      $dict = $this->dict;
      $sl   = $this->sl;
      $tl   = $this->tl;
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $html = '';
      $query = "SELECT icon, dict.url, dictParam.message FROM dict,dictParam"
              ." WHERE dict.dict=:dict AND dictParam.dict=dict.dict"
              ." AND (  (dictParam.sl=:sl AND dictParam.tl=:tl)"
              ."      OR dictParam.sl='¤')";
      $stmt = $DbMultidict->prepare($query);
      $stmt->bindParam(':dict',$dict);
      $stmt->bindParam(':sl',$sl);
      $stmt->bindParam(':tl',$tl);
      $stmt->bindColumn(1,$icon);
      $stmt->bindColumn(2,$url);
      $stmt->bindColumn(3,$message);
      $stmt->execute();
      $stmt->fetch();
      $html = '';
//The following dictionary icon used to be included in the next statement, but I have removed it (despite the name of the function) -- CPD 2019-08-22
//<img src="icon.php?dict=$dict" alt="" style="border:none;margin-top:-5px;vertical-align:bottom">
      if (!empty($icon)) { $html = <<<EOHTML
          <a href="$url" target="_top" title="$T_Click_to_escape_Multidict" id="esc">
          <span>Esc</span>
          </a>
          EOHTML;
      }
      $html .= (empty($message) ? '' : "<span style=\"color:red\" title=\"$message\">⚠</span>" . $message);
      return $html;
  }


  public function nbSlHtml() {
      // Returns html for swopping sl to a cognate language
      $T = new SM_T('multidict');
      $T_Switch_to = $T->h('Switch_to');
      $sl = $this->sl;
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $html = '';
      $query = "SELECT alt,endonym,icon FROM langAltSl,langV"
              ." WHERE langAltSl.id=:id AND langV.id=langAltSl.alt ORDER BY ord";
      $stmt = $DbMultidict->prepare($query);
      $stmt->bindParam(':id',$sl);
      $stmt->execute();
      $stmt->bindColumn(1,$alt);
      $stmt->bindColumn(2,$endonym);
      $stmt->bindColumn(3,$icon);
      while ($stmt->fetch()) {
          if (empty($icon)) {
              $html .= "<a onclick=\"slChange('$alt')\" title=\"$T_Switch_to $endonym\" class=\"box\">$alt</a>";
          } else {
              $html .= "<a onclick=\"slChange('$alt')\" title=\"$T_Switch_to $endonym\"><img src=\"/multidict/icon.php?lang=$alt\" alt=\"\"/></a>";
          }
      }
      if (!empty($html)) { $html = "<br/>\n<div class=\"nbLang\">$html</div>\n"; }
      return $html;
  }



  public function nbTlHtml() {
      // Returns html for swopping tl to a cognate language (or to sl to get monolingual dictionaries)
      $tl = $this->tl;
      $T = new SM_T('multidict');
      $T_Switch_to = $T->h('Switch_to');
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $html = '';
      $stmt = $DbMultidict->prepare('SELECT alt FROM langAltTl WHERE id=:tl ORDER BY ord');
      $stmt->execute([':tl'=>$tl]);
      $altTlArr = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
      $sl = $this->sl;
      if ($sl<>$tl && !in_array($sl,$altTlArr)) { $altTlArr[] = $sl; }
      $tlArr = $this->tlArr;
     if (empty($tlArr)) { return ''; }
      $stmt2 = $DbMultidict->prepare('SELECT endonym,icon FROM langV WHERE id=:id');
      $stmt2->bindColumn(1,$endonym);
      $stmt2->bindColumn(2,$icon);
      foreach ($altTlArr as $alt) {
          $stmt2->execute([':id'=>$alt]);
          if (isset($tlArr[$alt]) && $stmt2->fetch()) {
              if (empty($icon)) {
                  $html .= "<a onclick=\"tlChange('$alt')\" title='$T_Switch_to $endonym' class=box>$alt</a>";
              } else {
                  $html .= "<a onclick=\"tlChange('$alt')\" title='$T_Switch_to $endonym'><img src='/multidict/icon.php?lang=$alt' alt=''></a>";
              }
          }
      }
      if (!empty($html)) { $html = "<br>\n<div class=nbLang>$html</div>\n"; }
      return $html;
  }


  public static function slArr() {
      // Returns an array of source languages which have dictionaries in the database together with their native names and some other info
      $slArr = array();
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $query = <<<EOSQL
          SELECT dictLang.lang AS sl, endonym, script, wiki, pools, dictParamV.dict
            FROM dictParamV,dictLang LEFT JOIN lang ON dictLang.lang=lang.id
            WHERE dictParamV.sl='¤' AND dictParamV.dict=dictLang.dict
          UNION
          SELECT sl, endonym, script, wiki, pools, dict
            FROM dictParamV LEFT JOIN lang ON dictParamV.sl=lang.id WHERE sl<>'¤'
          ORDER BY endonym
          EOSQL;
      $stmt = $DbMultidict->prepare($query);
      $stmt->execute();
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($results as $res) {
          extract($res);
          $slArr[$sl]['endonym']  = "$endonym ($sl)";
          $slArr[$sl]['script']   = $script;
          $slArr[$sl]['wiki']  = $wiki;
          $slArr[$sl]['pools'] = $pools;
          if ($dict<>'Google') {
              $slArr[$sl]['onlyGoog'] = '';
          } elseif (!isset($slArr[$sl]['onlyGoog'])) {
              $slArr[$sl]['onlyGoog'] = 'onlyGoog';
          }
      }
      return $slArr;
  }


  public function tlArr() {
      // Returns an array of target languages which have dictionaries in the database for the current sl, together with the native language names
      $sl = $this->sl;
      $tlArr = array();
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $query = <<<EOSQL
          SELECT DISTINCT tl, endonym, script FROM dictParamV LEFT JOIN lang ON dictParamV.tl=lang.id WHERE sl=:sl
           UNION
          SELECT dl2.lang AS tl, endonym, script
           FROM dictParamV, dictLang AS dl1, dictLang AS dl2 LEFT JOIN lang ON dl2.lang=lang.id
           WHERE dictParamV.dict=dl1.dict AND dl1.dict=dl2.dict AND dl1.lang=:sl AND (dictParamV.tl='¤'
             OR (dictParamV.tl='x' AND dl1.lang<>dl2.lang))
           ORDER BY endonym
          EOSQL;
      $stmt = $DbMultidict->prepare($query);
      $stmt->execute([':sl'=>$sl]);
      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($results as $res) {
          extract($res);
          $tlArr[$tl] = ['endonym'=>"$endonym ($tl)",
                         'script' =>$script];
      }
      $this->tlArr = $tlArr;
      return $tlArr;
  }


  public static function updateCalls($sl,$tl,$dict) {
      // Updates the count of word lookup Calls to the combination ($sl, $tl, $dict)
      // and also the exponential decay average count over the last three months or whatever
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $query = "INSERT INTO wlSlTlDictCalls (sl,tl,dict,calls)"
              ." VALUES (?,?,?,1)"
              ." ON DUPLICATE KEY UPDATE calls=calls+1";
      $stmt = $DbMultidict->prepare($query);
      $stmt->execute(array($sl,$tl,$dict));
      $stmt = null;
  }


  public function csid() {
      //If the url looks like that of a Clilstore unit, return the unit number. Otherwise return 0, or return -1 if no url at all.
      $url = $this->url;
      if (empty($url)) return -1;
      if (preg_match('|//(.*)multidict\.(.*)/clilstore/page\.php\?id=(\d+)|',$url,$matches)) return $matches[3];
      if (preg_match('|//(.*)clilstore\.(.*)/clilstore/page\.php\?id=(\d+)|',$url,$matches)) return $matches[3];
      return 0;
  }

  public function csClickCounter() {
      //If it looks like Multidict is being called from a Clilstore unit, add 1 to the click count for that unit
      //And if a user is logged in, update the vocabulary tables for this user
      $csid = self::csid();
      if ($csid<1) return;  //Not a Clilstore unit
      if ( !isset($_GET['sid']) || !isset($_GET['word']) || isset($_GET['tl']) ) return; //Not a click from Wordlink
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare('UPDATE clilstore SET clicks=clicks+1 WHERE id=:id');
      $stmt->execute([':id'=>$csid]);
      $word  = $this->word;
      $sl    = $this->sl;
      $utime = time();

      //Record the word clicked for that unit
      $queryWC = "INSERT INTO csWclick (unit,word,clicks,newclicks,utime)"
                ." VALUES (:unit,:word,1,1,:utime)"
                ." ON DUPLICATE KEY UPDATE clicks=clicks+1,newclicks=newclicks+1,utime=:utime";
      $stmtWC = $DbMultidict->prepare($queryWC);
      $stmtWC->execute([':unit'=>$csid,':word'=>$word,':utime'=>$utime]);

      //Update the vocabulary for the Clilstore user, if the user has record set to on
      $myCLIL = SM_myCLIL::singleton();
      $csuser = $myCLIL->id;
      if ($csuser) {
          $stmt = $DbMultidict->prepare('SELECT record FROM users WHERE user=:user');
          $stmt->execute([':user'=>$csuser]);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          extract($row);
          if ($record) {
              $DbMultidict->beginTransaction();
              $queryV = "INSERT INTO csVoc (user,sl,word,meaning,head,calls) VALUES (:user,:sl,:word,'','',1)"
                       ." ON DUPLICATE KEY UPDATE calls=calls+1";
              $stmtV = $DbMultidict->prepare($queryV);
              $stmtV->execute([':user'=>$csuser,':sl'=>$sl,':word'=>$word]);
              $vocid = $DbMultidict->lastInsertId();
              $queryVU = "INSERT INTO csVocUnit (vocid,unit,calls) VALUES (:vocid,:unit,1)"
                        ." ON DUPLICATE KEY UPDATE calls=calls+1";
              $stmtVU = $DbMultidict->prepare($queryVU);
              $stmtVU->execute([':vocid'=>$vocid,':unit'=>$csid]);
              $DbMultidict->commit();
          }
      }
  }


  public static function getDictIcon($dict,&$icon,&$mimetype) {
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare("SELECT icon AS ic,mimetype AS mt FROM dict WHERE dict=:dict");
      $stmt->execute([':dict'=>$dict]);
      $res = $stmt->fetch(PDO::FETCH_ASSOC);
      extract($res);
      $icon = $ic;
      $mimetype = $mt;
  }


  public static function getLangIcon($lang,&$icon,&$mimetype) {
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare("SELECT icon AS ic,mimetype AS mt FROM langV WHERE id=:lang");
      $stmt->execute([':lang'=>$lang]);
      $res = $stmt->fetch(PDO::FETCH_ASSOC);
      extract($res);
      $icon = $ic;
      $mimetype = $mt;
  }


  public static function langValid($id) {
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $stmt = $DbMultidict->prepare("SELECT 1 FROM lang WHERE id=?");
      $stmt->execute(array($id));
      if ($stmt->fetch()) { return 1; } else { return 0; }
  }


  public static function clilOwnerArr() {
      // Returns an array of page ownere in Clilstore
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $sql = "SELECT DISTINCT owner FROM clilstore ORDER BY owner";
      $stmt = $DbMultidict->prepare($sql);
      $stmt->execute();
      $stmt->bindColumn(1,$owner);
      $ownerArr = array();
      while ($stmt->fetch()) {
         $ownerArr[] = $owner;
      }
      return $ownerArr;
  }

  public static function checkSafeBrowsing($url) {
// This is not working, following new version of GoogleSafeBrowsing, and is no longer used.
// It ought to be put working again soon.  --CPD 2020-09-28
      // Checks a url for suspected malware or phishing using the Google Safe Browsing API
      // Returns null string '' if no problem found or if the lookup times out.
      // Returns 'phishing' | 'malware' | 'phishing,malware' if Google Safe Browsing reports this.
      // Returns an error message starting 'GSB lookup failure:' if the actual lookup fails, other than by timeout.
      //
      if (empty($url)) { return ''; }
      if (preg_match('|multidict.net/|',$url)) { return ''; }  // multidict.net itself is always assumed to be safe
      if (preg_match('|smo.uhi.ac.uk/|',$url)) { return ''; }  // smo.uhi.ac.uk is also always assumed to be safe
//      $safebrowsing['a //(key should be stored safely off-site along with database connectors)
      $safebrowsing['api_url'] = 'https://sb-ssl.google.com/safebrowsing/api/lookup?pver=3.1&';
      $sbUrl = $safebrowsing['api_url'] . 'client=multidict-net&' . 'key='.$safebrowsing['api_key'] . '&appver=1.0&';
      $sbUrl .= 'url='.urlencode($url);
      $ch = curl_init();
      $timeout = 5;
      curl_setopt($ch,CURLOPT_URL,$sbUrl);
      curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
      curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
      curl_setopt($ch,CURLOPT_HEADER,1);
      $results = curl_exec($ch);
      $resultlines = explode("\n",$results);
      $httpResHead = $resultlines[0];
      if (empty($httpResHead)) {
          $res = '';
      } else {
          $httpResCode = (int)explode(' ',$httpResHead)[1];
          $httpResMess = explode(' ',$httpResHead,2)[1];
          if ($httpResCode>=400)      { $res = "GSB lookup failure: $httpResMess"; }
           elseif ($httpResCode==204) { $res = ''; }
           else                       { $res = end($resultlines); }
      }
      curl_close($ch);
      return $res;
  }

}
?>
