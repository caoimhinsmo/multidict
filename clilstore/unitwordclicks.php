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
  function sinceHtml($created,$countStarted) {
      if ($created>$countStarted) { return ''; }
      return 'since ' . timeHtml($countStarted);
  }

  try {
    $HTML = $resetMessage = $resetButton = '';

    if (!isset($_REQUEST['id'])) { throw new SM_MDexception('No id parameter'); }
    $id = $_REQUEST['id'];
    if (!is_numeric($id)) { throw new SM_MDexception('id parameter is not numeric'); }

    $sort = $_REQUEST['sort'] ?? 'clicks';
    $clicksHead = "<a href='unitwordclicks.php?id=$id&amp;sort=clicks' title='Sort'>Clicks</a>";
    $newHead    = "<a href='unitwordclicks.php?id=$id&amp;sort=new' title='Sort'>New</a>";
    $wordHead   = "<a href='unitwordclicks.php?id=$id&amp;sort=word' title='Sort'>Word</a>";
    $timeHead   = "<a href='unitwordclicks.php?id=$id&amp;sort=time' title='Sort'>Last click</a>";
    if ($sort=='clicks') {
        $ordering = 'clicks DESC,word';
        $clicksHead = 'Clicks';
    } elseif ($sort=='new') {
        $ordering = 'newclicks DESC,clicks DESC,word';
        $newHead = 'New';
    } elseif ($sort=='word') {
        $ordering = 'word';
        $wordHead = 'Word';
    } elseif ($sort=='time') {
        $ordering = 'utime DESC';
        $timeHead = 'Last time';
    } else { throw new SM_MDexception("Invalid parameter $ordering"); }

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $stmt = $DbMultidict->prepare('SELECT sl,owner,title,created,clicks,newclickTime FROM clilstore WHERE id=:id');
    $stmt->execute([':id'=>$id]);
    if (!($r = $stmt->fetch(PDO::FETCH_ASSOC))) { throw new SM_MDexception("No unit exists for id=$id"); }
    extract($r);
    $unitClicks = $clicks;

    $clicksMessage     = sinceHtml($created,1551554400);
    $unitClicksMessage = sinceHtml($created,1395188407);
    if ($clicksMessage)     { $clicksMessage = "<p style='margin:0 0 0 2em;color:#666;font-size:80%'>($clicksMessage)</p>"; };
    if ($unitClicksMessage) { $unitClicksMessage = " <span style='color:grey;font-size:80%'>($unitClicksMessage)</span>"; }
    $createdDateTime  = timeHtml($created);
    if ($newclickTime) {
        $newclickDateTime = timeHtml($newclickTime);
        $resetMessage = " on $newclickDateTime";
    }
    if ($user==$owner) { $resetButton = " &nbsp;<a id=rnc href='' onclick=\"resetNewclicks('$id');\">Reset now</a>"; }
    $linkbuttons = <<<EOBUT
<ul class="linkbuts">
<li><a href="./" class="nowordlink" target="_top" title="Clilstore index">Clilstore</a></li>
<li><a href="/cs/$id" title="Back to Unit $id">Unit $id</a></li>
<li><a href="unitinfo.php?id=$id" title="Information on unit $id">Unit info</a></li>
</ul>
EOBUT;

    $stmt = $DbMultidict->prepare("SELECT word,clicks,newclicks,utime FROM csWclick WHERE unit=:id ORDER BY $ordering");
    $stmt->execute([':id'=>$id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        extract($r);
        $timeDateTime = timeHtml($utime);
        $newclicksHtml = ( $newclicks ? $newclicks : '' );
        $HTML .= "<tr><td>$clicks</td><td>$newclicksHtml</td><td>$word</td><td>$timeDateTime</td></tr>\n";
    }
    if ($HTML) {
        $stmt = $DbMultidict->prepare('SELECT SUM(clicks) AS totClicks, SUM(newclicks) AS totNewclicks, MAX(utime) AS lastTime FROM csWclick WHERE unit=:id');
        $stmt->execute([':id'=>$id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($r);
        $timeDateTime = timeHtml($lastTime);
        $HTML = <<<EOD_PT
<p style="margin:1.5em 0 0 0;font-size:90%;color:#555;font-weight:bold">$title</p>
<p style="margin:0 0 0 2em;font-size:80%;color:#666">Created: $createdDateTime<br>
Total clicks on words: $unitClicks$unitClicksMessage</p>

<table id=priomh>
<tr><td>$clicksHead</td><td>$newHead</td><td>$wordHead</td><td>$timeHead</td></td>
</tr>
$HTML
<tr><td>$totClicks</td><td>$totNewclicks</td><td>·Total·</td><td>$timeDateTime</td></tr>
</table>

<p style="font-size:85%;color:#0b0">The ‘New’ column shows the number of clicks since the ‘new’ counter was last reset by the owner of the unit$resetMessage$resetButton</p>
EOD_PT;
    } else {
        $HTML = "<p>No words clicked yet in this unit</p>\n";
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
        table#priomh { border-collapse:collapse; margin:1.5em 0; border:1px solid black;  }
        table#priomh tr:first-child td { background-color:grey; color:yellow; }
        table#priomh tr:first-child a { color:white; }
        table#priomh tr:last-child {  border:1px solid white; border-top:1px solid black; color:grey; font-size:90%; }
        table#priomh td { padding:2px 6px; }
        table#priomh td:nth-child(1) { text-align:right; }
        table#priomh td:nth-child(2) { text-align:right; color:#0b0 }
        table#priomh td:last-child  { color:#aaa; font-size:90%; }
        a#rnc { border:0; padding:2px 4px; border-radius:6px; background-color:#27b; color:white; text-decoration:none; }
        a#rnc:hover,
        a#rnc:active,
        a#rnc:focus  { background-color:#f00; color:white; }
    </style>
    <script>
      function resetNewclicks(unit) {
        var url = '/clilstore/ajax/resetNewclicks.php?unit=' + unit;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open('GET', url, false);
        xmlhttp.send();
        var resp = xmlhttp.responseText;
        if (resp!='OK') { alert('Error in resetNewclicks: ' + resp); }
        location.reload();
      }
    </script>
</head>
<body>
$linkbuttons
<div class="body-indent">

<h1 style="font-size:130%;margin-bottom:0">Words clicked in Clilstore unit $id</h1>
$clicksMessage
$HTML
</div>
$linkbuttons
</body>
</html>
EOD1;

  } catch (Exception $e) { echo $e; }

?>
