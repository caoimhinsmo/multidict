<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  try {
      $myCLIL = SM_myCLIL::singleton();
      $user = ( isset($myCLIL->id) ? $myCLIL->id : '' );
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }

  function timeHtml($utime) {
  //Converts a Unix time to a date display with time hover
     $timeObj = new DateTime("@$utime");
     $timeDateTime = date_format($timeObj, 'Y-m-d H:i:s');
     $timeDate     = date_format($timeObj, 'Y-m-d');
     $timeDate = "<span title='$timeDateTime UT'>$timeDate</span>";
     return $timeDate;
  }

  try {
    if (!isset($_REQUEST['id'])) { throw new SM_MDexception('No id parameter'); }
    $id = $_REQUEST['id'];
    if (!is_numeric($id)) { throw new SM_MDexception('id parameter is not numeric'); }

    $sort = $_REQUEST['sort'] ?? 'clicks';
    $clicksHead = "<a href='unitwordclicks.php?id=$id&amp;sort=clicks' title='Sort'>Clicks</a>";
    $wordHead   = "<a href='unitwordclicks.php?id=$id&amp;sort=word' title='Sort'>Word</a>";
    $timeHead   = "<a href='unitwordclicks.php?id=$id&amp;sort=time' title='Sort'>Last click</a>";
    if ($sort=='clicks') {
        $ordering = 'clicks DESC,word';
        $clicksHead = 'Clicks';
    } elseif ($sort=='word') {
        $ordering = 'word';
        $wordHead = 'Word';
    } elseif ($sort=='time') {
        $ordering = 'utime';
        $timeHead = 'Last time';
    } else { throw new SM_MDexception("Invalid parameter $ordering"); }

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $stmt = $DbMultidict->prepare('SELECT sl,owner,title,created,clicks FROM clilstore WHERE id=:id');
    $stmt->execute([':id'=>$id]);
    if (!($r = $stmt->fetch(PDO::FETCH_ASSOC))) { throw new SM_MDexception("No unit exists for id=$id"); }
    extract($r);
    $unitClicks = $clicks;

    $clicksMessage = ( $created>1395188407 ? '' : ' <span style="color:grey;font-size:80%">(since 2014-03-18)</span' ) ;
    $createdDateTime = timeHtml($created);

    $linkbuttons = <<<EOBUT
<ul class="linkbuts">
<li><a href="./" class="nowordlink" target="_top" title="Clilstore index">Clilstore</a></li>
<li><a href="/cs/$id" title="Back to Unit $id">Unit $id</a></li>
<li><a href="unitinfo.php?id=$id" title="Information on unit $id">Unit info</a></li>
</ul>
EOBUT;

    $priomhTable = '';
    $stmt = $DbMultidict->prepare("SELECT word,clicks,utime FROM csWclick WHERE unit=:id ORDER BY $ordering");
    $stmt->execute([':id'=>$id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        extract($r);
        $timeDateTime = timeHtml($utime);
        $priomhTable .= "<tr><td>$clicks</td><td>$word</td><td>$timeDateTime</td></tr>\n";
    }
    if ($priomhTable) {
        $stmt = $DbMultidict->prepare('SELECT SUM(clicks) AS totClicks, MAX(utime) AS lastTime FROM csWclick WHERE unit=:id');
        $stmt->execute([':id'=>$id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($r);
//        $timeObj = new DateTime("@$lastTime");
//        $timeDateTime = date_format($timeObj, 'Y-m-d H:i:s');
        $timeDateTime = timeHtml($lastTime);
        $priomhTable = <<<EOD_PT
<table id=priomh>
<tr><td>$clicksHead</td><td>$wordHead</td><td>$timeHead</td></td>
</tr>
$priomhTable
<tr><td>$totClicks</td><td>·Total·</td><td>$timeDateTime</td></tr>
</table>
EOD_PT;
    } else {
        $priomhTable = "<p>No words clicked yet in this unit</p>\n";
    }


    echo <<<EOD1
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Words clicked in Clilstore unit $id</title>
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <link rel="StyleSheet" href="/css/smo.css" type="text/css">
    <link rel="StyleSheet" href="style.css?version=2014-04-15" type="text/css">
    <style>
        div.body-indent { clear:both; margin:0 0.25%; padding:0 0.25% 6px 0.25%; border-top:1px solid white; }
        table#fios    { border-collapse:collapse; }
        table#fios td { padding:2px 3px; }
        table#fios td:first-child { white-space:nowrap; }
        table#fios td:first-child { text-align:right; }

        table#priomh { border-collapse:collapse; margin:1.5em 0; border:1px solid black;  }
        table#priomh tr:first-child { background-color:grey; color:yellow; }
        table#priomh tr:first-child a { color:white; }
        table#priomh tr:last-child {  border:1px solid white; border-top:1px solid black; color:grey; font-size:90%; }
        table#priomh td { padding:2px 8px; }
        table#priomh td:first-child { text-align:right; }
        table#priomh td:last-child  { color:#aaa; font-size:90%; }
    </style>
</head>
<body>
$linkbuttons
<div class="body-indent">

<h1 style="font-size:130%">Words clicked in Clilstore unit $id</h1>
<p style="margin:0;font-size:90%;color:#555;font-weight:bold">$title</p>
<p style="margin:0 0 0 2em;font-size:80%;color:#666">Created: $createdDateTime<br>
Total clicks on words: $unitClicks$clicksMessage</p>

$priomhTable

</div>
$linkbuttons
</body>
</html>
EOD1;

  } catch (Exception $e) { echo $e; }

?>
