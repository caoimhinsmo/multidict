<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  function dateHtml($time,$showTime) {
      $dateTimeObj = new DateTime("@$time");
      $dateTime = $dateTimeObj->format('Y-m-d');
      if (!$showTime) return $dateTime;
      $dateTime = "<span title='" . $dateTimeObj->format(' H:i:s') . "'>$dateTime</span>";
      return $dateTime;
  }

  try {
    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $csSess   = SM_csSess::singleton();
    $mode    = $csSess->getCsSession()->mode;

    $myCLIL = SM_myCLIL::singleton();
    if (isset($myCLIL->id)) {
        $myid = $myCLIL->id;
        $stmt = $DbMultidict->prepare('SELECT adlev FROM users where user=:user');
        $stmt->execute([':user'=>$myid]);
        $myadlev = $stmt->fetchColumn();
    } else { $myadlev = -1; }

    if      ($myadlev<0)              { $inctest = 0; }
     elseif (isset($_GET['inctest'])) { $inctest = 1; }
     else                             { $inctest = 0; }
    if ($inctest) {
        $testCond = 'test<2';
        $inctestChecked = 'checked';
        $h1 = 'Clilstore users who have created units (including test units)';
    } else {
        $testCond = 'test<1';
        $inctestChecked = '';
        $h1 = 'Clilstore users who have published units';
    }
    $inctestCheckbox = ( $myadlev >= 0
                       ? "<input id=inctest type=checkbox name=inctest $inctestChecked onchange=inctestChange()><label for=inctest>Include test units</label>"
                       : ''
                       );
    $sort = $_GET['sort'] ?? '';
    $sorts = ( $sort ? explode('|',$sort) : [] );
    $sorts[] = 'fullname';
    $sorts[] = 'user';
    if     ($sorts[0]==    $sorts[1]) { $sorts[1] = '-' . $sorts[1]; array_shift($sorts); }   //This is a repeat click
    elseif ($sorts[0]=='-'.$sorts[1]) { $sorts[1] =       $sorts[0]; array_shift($sorts); }   //so reverse the sort
//    elseif (in_array($sorts[0],['nunits','nclicks','nviews','nclickav','adlev'])) { $sorts[0] = '-' . $sorts[0]; }  //These columns are reverse sorted by default
    $sortsSQL = $sorts;
    $columns = ['fullname','user','nunits','nclicks','nviews','clickav','joined','firstCreate','lastChange','adlev','email'];
    foreach ($sorts as $i=>$sortbit) {
        if (substr($sortbit,0,1)=='-')  {
            $sortbit = substr($sortbit,1);
            $swopsort = 1;
        } else {
            $swopsort = 0;
        }
        $descsort = in_array($sortbit,['nunits','nclicks','nviews','clickav','adlev']);
        if ($swopsort) { $descsort = 1-$descsort; }
        $desc = ( $descsort ? ' DESC' : '');
        $sortsSQL[$i] = "$sortbit$desc";
        if (!in_array($sortbit,$columns)) { throw new Exception("Invalid item $sortbit in sort parameter $sort"); }
    }
    $sortSQL = implode(',',$sortsSQL);
    $sort    = implode('|',array_slice($sorts,0,2));

    $showTime = ( $myadlev>1 ? 1 : 0 );

    $stmt = $DbMultidict->prepare("SELECT user, fullname, email, joined, adlev,"
                                . " COUNT(1) AS nunits, SUM(clicks) AS nclicks, SUM(views) AS nviews, SUM(clicks)/SUM(views) AS clickav, MIN(created) AS firstCreate, MAX(changed) AS lastChange"
                                . " FROM users, clilstore"
                                . " WHERE users.user=clilstore.owner AND $testCond"
                                . " GROUP BY user ORDER BY $sortSQL");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $nauthors = count($rows);
    $tableHtml = '';
    $totalNunits = $totalNclicks = $totalNviews = 0;
    foreach ($rows as $row) {
        extract($row);
        $userHtml     = htmlspecialchars($user);
        $fullnameHtml = htmlspecialchars($fullname);
        $adlevHtml    = ( $adlev ? $adlev : '' );
        $emailHtml    = htmlspecialchars($email);
        $joinedHtml      = dateHtml($joined,$showTime);
        $firstCreateHtml = dateHtml($firstCreate,$showTime);
        $lastChangeHtml  = dateHtml($lastChange,$showTime);
        $fnlen = mb_strlen($fullname);
        if ($fnlen>28) {
            $fnSizePercent = round(100*28/$fnlen);
            $fullnameHtml = "<span style=font-size:$fnSizePercent%>$fullnameHtml</span>";
        }
        $tableAdlev2 = '';
        if ($myadlev >= 2) {
            $tableAdlev2 = <<<EOD_tableAdlev2
  <td>$adlevHtml</td>
  <td>$emailHtml</td>
EOD_tableAdlev2;
        }           
        $tableHtml .= <<<EODtr
<tr>
  <td>$fullnameHtml</td>
  <td><a href=userinfo.php?user=$userHtml>$userHtml</a></td>
  <td>$nunits</td>
  <td>$nclicks</td>
  <td>$nviews</td>
  <td>$clickav</td>
  <td>$joinedHtml</td>
  <td>$firstCreateHtml</td>
  <td>$lastChangeHtml</td>
$tableAdlev2</tr>
EODtr;
        $totalNunits  += $nunits;
        $totalNclicks += $nclicks;
        $totalNviews  += $nviews;
    }
    $totalNclicks  = round($totalNclicks,3);
    $totalNviews   = round($totalNviews,3);
    $totalClicksav = round($totalNclicks/$totalNviews,4);
    $tableTopAdlev2 = $tableBotAdlev2 = '';
    $sortInctest = ( $inctest ? "$sort&amp;inctest" : $sort );
    if ($myadlev >= 2) {
        $tableTopAdlev2 = <<<EOD_tableTopAdlev2
  <td><a href="users.php?sort=adlev|$sortInctest">adlev</a></td>
  <td><a href="users.php?sort=email|$sortInctest">email</a></td>
EOD_tableTopAdlev2;
        $tableBotAdlev2 = <<<EOD_tableBotAdlev2
  <td></td>
  <td></td>
EOD_tableBotAdlev2;
    }
    $tableHtml = <<<EOD_tableHtml
<table id=main>
<tr id=maintop title='Click to sort'>
  <td><a href="users.php?sort=fullname|$sortInctest">Full name</a></td>
  <td><a href="users.php?sort=user|$sortInctest">User</a></td>
  <td><a href="users.php?sort=nunits|$sortInctest">Units</a></td>
  <td><a href="users.php?sort=nclicks|$sortInctest">Clicks</a></td>
  <td><a href="users.php?sort=nviews|$sortInctest">Views</a></td>
  <td><a href="users.php?sort=clickav|$sortInctest">Clicks/V</a></td>
  <td><a href="users.php?sort=joined|$sortInctest">Joined</a></td>
  <td><a href="users.php?sort=firstCreate|$sortInctest">First create</a></td>
  <td><a href="users.php?sort=lastChange|$sortInctest">Last change</a></td>
$tableTopAdlev2</tr>
$tableHtml
<tr id=mainbottom>
  <td>Total: $nauthors authors</td>
  <td></td>
  <td>$totalNunits</td>
  <td>$totalNclicks</td>
  <td>$totalNviews</td>
  <td>$totalClicksav</td>
  <td></td>
  <td></td>
  <td></td>
$tableBotAdlev2</tr>
</table>
EOD_tableHtml;

    echo <<<EOD
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>$h1</title>
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="StyleSheet" href="style.css?version=2014-04-15">
    <style>
        div.body-indent { clear:both; margin:0 0.25%; padding:0 0.25% 6px 0.25%; border-top:1px solid white; }
        table#main    { border-collapse:collapse; border:1px solid; margin:0 0.3em 1em 0.3em; }
        table#main tr:nth-child(odd) { background-color:#ddf; }
        table#main tr:hover { background-color:yellow; }
        table#main td { padding:1px 6px; }
        table#main td:nth-child(1)  { max-width:20em; }
        table#main td:nth-child(3)  { text-align:right; }
        table#main td:nth-child(4)  { text-align:right; }
        table#main td:nth-child(5)  { text-align:right; font-size:80%; }
        table#main td:nth-child(6)  { font-size:75%; }
        table#main td:nth-child(7)  { font-size:75%; }
        table#main td:nth-child(8)  { font-size:75%; }
        table#main td:nth-child(9)  { font-size:75%; }
        table#main td:nth-child(10) { text-align:center; font-size:75%; }
        table#main td:nth-child(11) { font-size:75%; }
        table#main td:nth-child { text-align:right; font-weight:bold; }
        table#main tr#maintop    { background-color:grey; color:white; }
        table#main tr#mainbottom { background-color:grey; color:white; }
        table#main tr#maintop a { color:white; }
        table#main tr#maintop a:hover { background-color:blue; color:yellow; }
        span.time { font-size:60%; color:grey;  }
    </style>
    <script>
        function inctestChange() {
            const params = new URLSearchParams(location.search)
            if (params.has('inctest')) { params.delete('inctest'); }
              else                     { params.append('inctest','on'); }
            var paramstr = params.toString();
            if (paramstr!='') { paramstr = '?'+paramstr; }
            loc = window.location;
            location = loc.protocol + '//' + loc.hostname + loc.pathname + paramstr;
        }
    </script>
</head>
<body>
<ul class="linkbuts">
<li><a href="./" title="Clilstore index page">Clilstore</a>
</ul>
<div class="body-indent">

<h1 style="font-size:130%">$h1</h1>
$inctestCheckbox

<p class=info style="margin:1em 0 0 1em">Click column name to sort. Click again to sort in the opposite direction. The previous sort column is used for secondary sorting.</p>
$tableHtml

</div>
<ul class="linkbuts">
<li><a href="./" title="Clilstore index page">Clilstore</a>
</ul>
</body>
</html>
EOD;

  } catch (Exception $e) { echo $e; }

?>
