<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");
  header("Cache-Control:max-age=0");

  try {

    $T = new SM_T('wordlink/compose');
    $T_Text         = $T->h('Text');
    $T_Compose_page = $T->h('Compose_page');

    if(!isset($_GET['sid'])) { throw new SM_MDexception("No sid parameter"); }
    $sid = $_GET['sid'];
    if(!is_numeric($sid)) { throw new SM_MDexception("Non numeric sid parameter: $sid"); }
    $sid = (string)(int)$sid;

    echo <<<EOD1
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex,nofollow"/>
    <link rel="icon" type="image/png" href="/favicons/wordlink.png">
</head>
<body>

<form method="POST" action="wordlink.php?sid=$sid&composed=1">
$T_Text<br/>
<textarea name="text" style="width:96%;height:20em;margin:0.3em"></textarea>
<br>
<input type="submit" name="Cruthaich" value="$T_Compose_page"/>
</form>
</body>
</html>
EOD1;

  } catch (Exception $e) { echo $e; }

?>
