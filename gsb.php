<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header('Cache-Control: no-cache, no-store, must-revalidate');
  header("Cache-Control:max-age=0");

  try {

    if (empty($_REQUEST['url'])) {
        $myUrl = '';
        $result = '';
    } else {
        $myUrl = $_REQUEST['url'];
        $result = SM_WlSession::checkSafeBrowsing($myUrl);
    }
    if (empty($result)) { $result = '<span style="color:green">passed</span>'; }
     else               { $result = "<span style=\"color:red\">$result</span"; }

    echo <<<EODHTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check safe browsing</title>
    <link rel="StyleSheet" href="/css/smo.css">
</head>
<body style="padding:3px">
<h1 style="font-size:120%">Test a url for suspected malware or phishing using the Google Safe Browsing API</h1>
<form>
<table>
<tr><td>url:</td><td><input name="url" value="$myUrl" placeholder="Enter a url to test" style="width:72em"></td></tr>
<tr><td></td><td><input type="submit" value="Test"></td></tr>
</table>
</form>

<p>$result</p>

</body>
</html>
EODHTML;

  } catch (Exception $e) { echo $e; }

?>
