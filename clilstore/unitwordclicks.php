<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  $T = new SM_T('clilstore/unitwordclicks');

  $T_since_    = $T->h('since_');
  $T_Created   = $T->h('csCol_created');
  $T_Clicks    = $T->h('csCol_clicks');
  $T_New       = $T->h('New');
  $T_Word      = $T->h('Facal');
  $T_Last_time = $T->h('Last_time');
  $T_Total     = $T->h('Iomlan');
  $T_on_DATE   = $T->h('on_DATE');
  $T_Reset_now = $T->h('Reset_now');
  $T_Words_clicked_in_unit_ = $T->h('Words_clicked_in_unit_');
  $T_Total_clicks_on_words  = $T->h('Total_clicks_on_words');
  $T_Click_to_sort          = $T->h('Click_to_sort');
  $T_The_New_column_shows_  = $T->h('The_New_column_shows_');
  $T_No_words_clicked_yet   = $T->h('No_words_clicked_yet');

  $T_The_New_column_shows_ = strtr($T_The_New_column_shows_, ['{xxx}'=>"‘{$T_New}’"]);

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
      global $T_since_;
      if ($created>$countStarted) { return ''; }
      return sprintf($T_since_,timeHtml($countStarted));
  }

  $id = $_REQUEST['id'] ?? NULL;
  $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan,$id);
  $T_Words_clicked_in_unit_ = strtr($T_Words_clicked_in_unit_, ['{unitNo}'=>$id]);

  try {
    $HTML = $resetMessage = $resetButton = '';

    if (!isset($_REQUEST['id'])) { throw new SM_MDexception('No id parameter'); }
    $id = $_REQUEST['id'];
    if (!is_numeric($id)) { throw new SM_MDexception('id parameter is not numeric'); }

    $sort = $_REQUEST['sort'] ?? 'clicks';
    $clicksHead = "<a href='unitwordclicks.php?id=$id&amp;sort=clicks' title='$T_Click_to_sort'>$T_Clicks</a>";
    $newHead    = "<a href='unitwordclicks.php?id=$id&amp;sort=new' title='$T_Click_to_sort'>$T_New</a>";
    $wordHead   = "<a href='unitwordclicks.php?id=$id&amp;sort=word' title='$T_Click_to_sort'>$T_Word</a>";
    $timeHead   = "<a href='unitwordclicks.php?id=$id&amp;sort=time' title='$T_Click_to_sort'>$T_Last_time</a>";
    if ($sort=='clicks') {
        $ordering = 'clicks DESC,word';
        $clicksHead = $T_Clicks;
    } elseif ($sort=='new') {
        $ordering = 'newclicks DESC,clicks DESC,word';
        $newHead = $T_New;
    } elseif ($sort=='word') {
        $ordering = 'word';
        $wordHead = $T_Word;
    } elseif ($sort=='time') {
        $ordering = 'utime DESC';
        $timeHead = $T_Last_time;
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
        $resetMessage = " $T_on_DATE $newclickDateTime";
    }
    if ($user==$owner) { $resetButton = " &nbsp;<a id=rnc href='' onclick=\"resetNewclicks('$id');\">$T_Reset_now</a>"; }

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
<p style="margin:0 0 0 2em;font-size:80%;color:#666">$T_Created: $createdDateTime<br>
$T_Total_clicks_on_words: $unitClicks$unitClicksMessage</p>

<table id=priomh>
<tr><td>$clicksHead</td><td>$newHead</td><td>$wordHead</td><td>$timeHead</td></td>
</tr>
$HTML
<tr><td>$totClicks</td><td>$totNewclicks</td><td>·{$T_Total}·</td><td>$timeDateTime</td></tr>
</table>

<p style="font-size:85%;color:#0b0">$T_The_New_column_shows_$resetMessage$resetButton</p>
EOD_PT;
    } else {
        $HTML = "<p>$T_No_words_clicked_yet</p>\n";
    }


    echo <<<EOD1
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>$T_Words_clicked_in_unit_</title>
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="StyleSheet" href="style.css?version=2014-04-15">
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
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onload = function() { if (this.status!=200) { alert('Error in resetNewclicks:'+this.status); } }
        xmlhttp.open('GET', 'ajax/resetNewclicks.php?unit=' + unit);
        xmlhttp.send();
        window.location.href = window.location.href;
      }
    </script>
</head>
<body>
$mdNavbar
<div class="body-indent">

<h1 style="font-size:130%;margin-bottom:0">$T_Words_clicked_in_unit_</h1>
$clicksMessage
$HTML
</div>
$mdNavbar
</body>
</html>
EOD1;

  } catch (Exception $e) { echo $e; }

?>
