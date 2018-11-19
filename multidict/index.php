<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header('P3P: CP="CAO PSA OUR"');

  try {

    header("Cache-Control:max-age=0");

    $sid = ( !empty($_GET['sid']) ? $_GET['sid'] : null );
    $wlSession = new SM_WlSession($sid);
    $sid = $wlSession->sid;
    $wlSession->bestDict();
    $wlSession->storeVars();
    $wlSession->csClickCounter(); //If called from a Clilstore unit, add 1 to the click count
    $sl = $wlSession->sl;
    $tl = $wlSession->tl;
    $robots = ( empty($wlSession->word) ? 'index,follow' : 'noindex,nofollow' );
    echo <<<EOD1
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
<head>
    <title>Multidict</title>
    <meta name="description" content="Multdict is a facility which allows a word to be searched for easily in a selection of online dictionaries"/>
    <meta name="robots" content="$robots"/>
    <script type="text/javascript">
        this.name = 'MD$sid';
    </script>
</head>
<frameset rows="140,*" id="MD$sid" name="MD$sid">
    <frame id="MDnavframe$sid"  name="MDnavframe$sid"  src="navigation.php?sid=$sid&amp;sl=$sl&amp;tl=$tl" frameborder="0" scrolling="no" />
    <frame id="MDmainframe$sid" name="MDmainframe$sid" src="multidict.php?sid=$sid&amp;sl=$sl&amp;tl=$tl"  frameborder="0" />
    <noframes>
        Multdict is a facility which allows a word to be searched for easily in a selection of online dictionaries.  It works using frames.
    </noframes>
</frameset>
</html>
EOD1;

  } catch (exception $e) { echo <<<EOD2
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html>
<head>
    <title>Multidict error</title>
</head>
<body>
$e
</body>
</html>
EOD2;
  }
?>
