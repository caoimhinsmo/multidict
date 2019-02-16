<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {

    if (empty($_GET['url']))    { throw new SM_Exception('safeBrowsingError.php was called without a url= parameter'); }
    if (empty($_GET['result'])) { throw new SM_Exception('safeBrowsingError.php was called without a result= parameter'); }

    $url    = $_GET['url'];
    $result = $_GET['result'];

    if (substr($result,0,18)=='GSB lookup failure') { throw new SM_MDexception("<p>Wordlink always checks URLs with the Google Safe Browsing database before processing them, but was unable to do this for some reason - perhaps because we have reached our daily quota.  This should not happen, so it would be good to let us know quickly if possible.  The error code was:</p><p style='margin-left:3em'>$result</p>"); }

    if (preg_match('/malware/i',$result)) { $errorMess = <<<EOD_MALWARE
<p style="margin-top:0.5em"><b>Warning — Visiting this web site may harm your computer.</b> This page appears to contain malicious code that could be downloaded to your computer without your consent. You can learn more about harmful web content including viruses and other malicious code and how to protect your computer at: <a href="https://www.stopbadware.org/">StopBadware.org</a></p>
EOD_MALWARE;
    } else { $errorMess = <<<EOD_PHISHING
<p style="margin-top:0.5em"><b>Warning—Suspected phishing page.</b> This page may be a forgery or imitation of another website, designed to trick users into sharing personal or financial information. Entering any personal information on this page may result in identity theft or other abuse. You can find out more about phishing from: <a href="https://www.antiphishing.org/">www.antiphishing.org</a></p>
EOD_PHISHING;
    }

    echo <<<EODHTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wordlink: Safe browsing check failure</title>
    <link rel="StyleSheet" href="/css/smo.css" type="text/css">
</head>
<body style="padding:3px;background-color:#f55;color:white">
<h1 style="font-size:120%;text-align:center">Wordlink has refused to process this page because it is suspect</h1>

<p style="font-size:90%;text-align:center"><span style="display:inline-block;padding:2px 6px;border-radius:4px;background-color:white;color:black">$url</span><br><br>
To protect both you and itself, Wordlink checks the url against a Google database of suspect sites before processing the page</p>

<div style="margin:1.5em 0.5em 0 0.5em;padding:0.5em 0.8em;border:2px solid white;border-radius:0.5em">
$errorMess
<p style="font-size:85%"><b><a href="https://code.google.com/apis/safebrowsing/safebrowsing_faq.html#whyAdvisory">Advisory provided by Google</a></b> - Database check returned: <b>$result</b><br>
Google works to provide the most accurate and up-to-date phishing and malware information. However, Google cannot guarantee that its information is comprehensive and error-free: some risky sites may not be identified, and some safe sites may be identified in error.</p>
</div>

</body>
</html>
EODHTML;

  } catch (Exception $e) { echo $e; }

?>
