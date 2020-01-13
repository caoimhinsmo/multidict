<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Pragma: no-cache");

  try {
    $T = new SM_T('wordlink/help');

    $T_Help        = $T->h('Cobhair');
    $T_Splitscreen = $T->h('Splitscreen');

    $T_Wordlink_help_text1 = $T->h('Wordlink_help_text1');
    $T_Wordlink_help_text2 = $T->h('Wordlink_help_text2');
    $T_Wordlink_help_text3 = $T->h('Wordlink_help_text3');
    $T_Wordlink_help_text4 = $T->h('Wordlink_help_text4');
    $T_Wordlink_help_text5 = $T->h('Wordlink_help_text5');
    $T_Wordlink_help_wa1   = $T->h('Wordlink_help_wa1');
    $T_Wordlink_help_wa2   = $T->h('Wordlink_help_wa2');
    $T_Using_Wordlink      = $T->h('Using_Wordlink');
    $T_Remove_exist_links  = $T->h('Remove_exist_links');
    $T_For_web_authors     = $T->h('For_web_authors');
    
    $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

    $T_Wordlink_help_text1 = strtr( $T_Wordlink_help_text1, [ '{Multidict}' => '<a href="/multidict/">Multidict</a>' ] );
    $T_Wordlink_help_text2 = strtr( $T_Wordlink_help_text2, [ '{{Remove existing links}}' => '“' . $T_Remove_exist_links . '”' ] );
    $T_Wordlink_help_text4 = strtr( $T_Wordlink_help_text4, [ '{{Splitscreen}}' => '“' . $T_Splitscreen . '”' ] );
    $T_Wordlink_help_text5 = strtr( $T_Wordlink_help_text5, [ '{' => '<a href="examples.php">', '}' => '</a>' ] );

    $HTML = <<<END_HTML
$mdNavbar
<div class="smo-body-indent" style="max-width:75em">

<h1 style="font-size:120%">$T_Using_Wordlink</h1>

<p>$T_Wordlink_help_text1</p>

<p>$T_Wordlink_help_text2</p>

<p>$T_Wordlink_help_text3</p>

<p>$T_Wordlink_help_text4</p>

<p>$T_Wordlink_help_text5</p>

<h2>$T_For_web_authors</h2>

<p>$T_Wordlink_help_wa1 href="http://multidict.net/wordlink/?sl=…&ampurl=…"</p>

<p>$T_Wordlink_help_wa2</p>

</div>
$mdNavbar
END_HTML;

  } catch (Exception $e) {
    $HTML = $e->getMessage();
  }

  $HTMLdoc = <<<END_HTMLdoc
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wordlink: $T_Help</title>
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="icon" type="image/png" href="/favicons/wordlink.png">
    <style>
        h2 { font-size:110%; margin-top:2em; }
    </style>
</head>
<body>

$HTML

</body>
</html>
END_HTMLdoc;

  echo $HTMLdoc;
?>
