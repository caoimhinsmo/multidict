<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  try {
    if(!isset($_GET['sid'])) { throw new SM_MDexception("No sid parameter"); }
    $sid = $_GET['sid'];
    if(!is_numeric($sid)) { throw new SM_MDexception("Non numeric sid parameter: $sid"); }
    $sid = (string)(int)$sid;

    echo <<<EOD1
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wordlink file upload</title>
    <meta name="robots" content="noindex,nofollow"/>
    <link rel="icon" type="image/png" href="/favicons/wordlink.png">
</head>
<body>
<div>

<form method="POST" action="wordlink.php?sid=$sid&composed=1" style="margin-top:2em">
<fieldset style="background-color:#eef">
<legend>Or else compose a new page now</legend>
Title<br/>
<input type="text" name="title" style="width:96%"/><br/>
Text<br/>
<textarea name="text" style="width:96%;height:400px"></textarea><br/>
<input type="submit" name="Cruthaich" value="Compose"/>

<p>(Afterwards you can save the Wordlinked file from the browser frame back to your own computer)</p>
</div>
</body>
</html>
EOD1;

  } catch (Exception $e) { echo $e; }

?>
