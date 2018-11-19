<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

/* Sean - Sguab seo às
  $sid = ( !empty($_GET['sid']) ? $_GET['sid'] : null);
  $wlSession = new SM_WlSession($sid);
  $wlSession->readGetvars();
  $wlSession->storeVars();
  $sid  = $wlSession->sid;
  $sl   = $wlSession->sl;
  $url  = $wlSession->url;
*/

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
    <meta name="robots" content="noindex,nofollow">
</head>
<body>
<div>

<form enctype="multipart/form-data" method="POST" action="wordlink.php?sid=$sid&uploaded=1">
<fieldset style="background-color:#eef">
<legend>Upload an html file from your computer to Wordlink</legend>

<input type="hidden" name="MAX_FILE_SIZE" value="1000000"/>
<input name="wordlinkUpload$sid" type="file" accept="text/html" style="width:40em;"/>
&nbsp;&nbsp;Encoding:&nbsp;<select name="encoding">
<option value="UTF-8" selected="selected">UTF-8</option>
<option value="utf-16">utf-16</option>
<option value="ISO-8859-1">ISO-8859-1</option>
<option value="ISO-8859-2">ISO-8859-2</option>
<option value="ISO-8859-3">ISO-8859-3</option>
<option value="ISO-8859-4">ISO-8859-4</option>
<option value="ISO-8859-5">ISO-8859-5</option>
<option value="ISO-8859-6">ISO-8859-6</option>
<option value="ISO-8859-7">ISO-8859-7</option>
<option value="ISO-8859-8">ISO-8859-8</option>
<option value="windows-1251">windows-1251</option>
<option value="windows-1252">windows-1252</option>
<option value="windows-1253">windows-1253</option>
</select><br>
<input name="Tairg" value="Upload" type="submit"/>
</fieldset>
</form>

<p>(Afterwards you can save the Wordlinked file from the browser frame back to your own computer)</p>
EOD1;

  } catch (Exception $e) { echo $e; }

?>
