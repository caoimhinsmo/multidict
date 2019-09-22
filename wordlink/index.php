<?php
  if (!include('autoload.inc.php'))
    header("Location:https://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {

    header("Cache-Control:max-age=0");
    header('P3P: CP="CAO PSA OUR"');


//---- Experimental code to clean up the URL a bit ----
    $qs0 = $_SERVER['QUERY_STRING'];
    $qs = '&' . $qs0;
    $qs = str_replace('&mode=ss','',$qs);
    $qs = str_replace('&upload=0','',$qs);
    $qs = str_replace('&go=Go','',$qs);
    $qs = substr($qs,1);
    if ($qs<>$qs0) {
        $cleaned_location = 'http://' . $_SERVER['SERVER_NAME']
                          . $script_name = str_replace('index.php','',$_SERVER['SCRIPT_NAME'])
                          . '?' . $qs;
        header("Location:$cleaned_location");
    }
//---- End of experimental code -----------------------

    $sid = ( !empty($_GET['sid']) ? $_GET['sid'] : null );
    $wlSession = new SM_WlSession($sid);
    $sid     = $wlSession->sid;
    $wlSession->rmLi = ( empty($_GET['rmLi']) ? 0 : 1 );
    $mode    = $wlSession->mode;
    $navsize = $wlSession->navsize;  if ($navsize == -1) { $navsize = 140; }
    $url  = $wlSession->url;
    $csid = $wlSession->csid();
    if ($csid==-1)     { $startAdvice = ''; }
     elseif ($csid==0) { $startAdvice = 'src="startAdvice.html"'; }
     else              { $startAdvice = 'src="startAdviceCs.html"'; }
    if (!empty($_GET['upload'])) {
        if ($_GET['upload']==1) { $wlSession->url = '{upload}'; }
        if ($_GET['upload']==2) { $wlSession->url = '{compose}'; }
    }
    $robots = ( empty($wlSession->url) ? 'index,follow' : 'noindex,nofollow' );
    $wlSession->storeVars();
    if ($csid) {
        $favicon = 'clilstore';
        $noframes = "Clilstore unit $csid<br>(Clilstore and Wordlink link webpages word-by-word to online dictionaries)";
        $logo = '/lonelogo/clilstore-blue.png';
        $pagetitle = "Clilstore unit $csid";
        $description = htmlspecialchars(SM_csSess::csTitle($csid));
    } else {
        $favicon = 'wordlink';
        $noframes = 'Wordlink links webpages word-by-word to onine dictionaries';
        $logo = '/lonelogo/wordlink-blue.png';
        $pagetitle = 'Wordlink';
        $description = "Wordlink is a facility which links webpages word-by-word to online dictionaries";
    }
    if ($mode=='ss') { echo <<<EOD1
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
<head>
    <title>$pagetitle</title>
    <meta name="description" content="$description">
    <meta name="robots" content="$robots"/>
    <link rel="icon" type="image/png" href="/favicons/$favicon.png">
</head>
<frameset cols="61%,39%" id="WL$sid" name="WL$sid">
    <frameset rows="$navsize,*" frameborder="1">
        <frame id="WLnavframe$sid"  name="WLnavframe$sid"  src="navigation.php?sid=$sid" frameborder="0" scrolling="no">
        <frame id="WLmainframe$sid" name="WLmainframe$sid" src="wordlink.php?sid=$sid"   frameborder="0">
    </frameset>
    <frame id="MD$sid" name="MD$sid" frameborder="1" $startAdvice>
    <noframes>
        <img src='$logo' alt=''>
        <p>$noframes</p>
    </noframes>
</frameset>
</html>
EOD1;
    } else { echo <<<EOD2
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
<head>
    <title>$pagetitle</title>
    <meta name="description" content="$description"/>
    <meta name="robots" content="$robots"/>
</head>
<frameset rows="$navsize,*">
    <frame id="WLnavframe$sid"  name="WLnavframe$sid"  src="navigation.php?sid=$sid" frameborder="0" scrolling="no" />
    <frame id="WLmainframe$sid" name="WLmainframe$sid" src="wordlink.php?sid=$sid"   frameborder="0" />
    <noframes>
        <img src='$logo' alt=''>
        <p>$noframes</p>
    </noframes>
</frameset>
</html>
EOD2;
    }

  } catch (exception $e) { echo <<<EOD2
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
<head>
    <title>Wordlink error</title>
</head>
<body>
$e
</body>
</html>
EOD2;
  }
?>
