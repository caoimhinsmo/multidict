<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Pragma: no-cache");

  try {
    $T = new SM_T('wordlink/help');

    $T_Help  = $T->h('Cobhair');
    $T_Using_Wordlink      = $T->h('Using_Wordlink');
    $T_Wordlink_help_text1 = $T->h('Wordlink_help_text1');
    $T_Wordlink_help_text2 = $T->h('Wordlink_help_text2');
    $T_Wordlink_help_text3 = $T->h('Wordlink_help_text3');
    $T_Wordlink_help_text4 = $T->h('Wordlink_help_text4');
    $T_Wordlink_help_text5 = $T->h('Wordlink_help_text5');
    $T_Wordlink_help_wa1   = $T->h('Wordlink_help_wa1');
    $T_Wordlink_help_wa2   = $T->h('Wordlink_help_wa2');
    
    $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

    $T_Wordlink_help_text1 = strtr( $T_Wordlink_help_text1, [ '{Multidict}' => '<a href="/multidict/">Multidict</a>' ] );
    $T_Wordlink_help_text5 = strtr( $T_Wordlink_help_text5, [ '{' => '<a href="examples.php">', '}' => '</a>' ] );

    $HTML = <<<END_HTML
$mdNavbar
<div class="smo-body-indent" style="max-width:75em">

<h1 style="font-size:120%">$T_Using_Wordlink</h1>

<p>$T_Wordlink_help_text1</p>

<p>$T_Wordlink_help_text2</p>

<p>$T_Wordlink_help_text3</p>
<ul style="margin-top:0">
<li>Very “flashy” webpages using lots of Flash or Javascript;</li>
<li>Pages with very broken invalid html.  (Often very old web pages are in this cateogory);</li>
<li>Pages which lie behind login systems.  So unfortunately, Wordlink will not currently work with social network sites or VLE’s.</li>
</ul>

<p>$T_Wordlink_help_text4</p>

<p>$T_Wordlink_help_text5</p>

<h2>For web authors</h2>

<p>$T_Wordlink_help_wa1</p>
<p>You can help readers who are not fluent in your language by providing a link to Wordlink on your pages.  Specify the source language (sl) and the page address (url) as parameters in the link to Wordlink - i.e. src="http://multidict.net/wordlink/?sl=...&amp;url=...."</p>

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
