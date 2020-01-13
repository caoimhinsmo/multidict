<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

    $T = new SM_T('multidict/help');

    $T_Help     = $T->h('Cobhair');
    $T_basic    = $T->h('basic');
    $T_advanced = $T->h('advanced');

    $T_Multidict_help_text0  = $T->h('Multidict_help_text0');
    $T_Multidict_help_text1  = $T->h('Multidict_help_text1');
    $T_Multidict_help_text2  = $T->h('Multidict_help_text2');
    $T_Multidict_help_text3  = $T->h('Multidict_help_text3');
    $T_Multidict_help_text4  = $T->h('Multidict_help_text4');
    $T_Multidict_help_text5  = $T->h('Multidict_help_text5');
    $T_Multidict_help_text6  = $T->h('Multidict_help_text6');
    $T_Multidict_help_text7  = $T->h('Multidict_help_text7');
    $T_Multidict_help_text8  = $T->h('Multidict_help_text8');
    $T_Multidict_help_text9  = $T->h('Multidict_help_text9');
    $T_Multidict_help_textJS = $T->h('Multidict_help_textJS');

    $T_Multidict_help_text2 = strtr ( $T_Multidict_help_text2, [ '{{basic}}' => '“' . $T_basic . '”', '{{advanced}}' => '“' . $T_advanced . '”' ] );
    $T_Multidict_help_text9 = strtr ( $T_Multidict_help_text9, [ '{' => "<a href='languages.php'>", '}' => '</a>' ] );

    $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

    echo <<<END_HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Multidict: $T_Help</title>
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="icon" type="image/png" href="/favicons/multidict.png">
    <style>
        div.box { margin:2px; padding:0.2em; border:1px solid #393; border-radius:0.3em;
                  background-color:#efe; color:green; font-size:80%; text-align:justify }
        table#mainTable { border-collapse:collapse; }
        table#mainTable td { padding:0.2em; vertical-align:top; }
        table#mainTable tr td:first-child { color:red; font-size:140%; }
    </style>
</head>
<body>
$mdNavbar
<div class="smo-body-indent" style="max-width:80em">

<h1 style="font-size:120%">$T_Help</h1>

<p>$T_Multidict_help_text0</p>

<div><img src="help.jpg" style="width:50em"></div>

<table id='mainTable'>
<tr><td>①</td><td>$T_Multidict_help_text1</td></tr>
<tr><td>②</td><td>$T_Multidict_help_text2</td></tr>
<tr><td>③</td><td>$T_Multidict_help_text3</td></tr>
<tr><td>④</td><td>$T_Multidict_help_text4</td></tr>
<tr><td>⑤</td><td>$T_Multidict_help_text5</td></tr>
<tr><td>⑥</td><td>$T_Multidict_help_text6</td></tr>
<tr><td>⑦</td><td>$T_Multidict_help_text7</td></tr>
<tr><td>⑧</td><td>$T_Multidict_help_text8</td></tr>
</table>

<p>$T_Multidict_help_text9</p>

<div class="box">$T_Multidict_help_textJS</div>

</div>
$mdNavbar
</body>
</html>
END_HTML;

?>
