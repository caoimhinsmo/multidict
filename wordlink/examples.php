<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

    $T = new SM_T('wordlink/examples');
    $T_Example_pages    = $T->h('Example_pages');
    $T_Example_pages_h1 = $T->h('Example_pages_h1');

    $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

    $wikiLinks = '';
    $slArr = SM_WlSession::slArr();
    foreach ($slArr as $sl=>$langInfo) {
        $endonym    = $langInfo['endonym'];
        $wiki       = $langInfo['wiki'];
        $multidicts = $langInfo['multidicts'];
        $class      = $langInfo['pools'];
      if ($class=='omit' or empty($wiki)) { continue; }
        if ($multidicts=='|Google|') { $class = 'googleOnly'; }
        $wikiLinks .= "<div class='wiki $class'><a href='./?sl=$sl&amp;url=https://$wiki.wikipedia.org/' title='$endonym'>$sl</a></div>\n";
    }
    $wikiLinks .= "<div class='wiki tools' style='margin-left:1em'><a href='./?sl=en&amp;url=https://simple.wikipedia.org/' title='Simple English'>simple</a></div>\n";

    echo <<<END_HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wordlink: $T_Example_pages</title>
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="icon" type="image/png" href="/favicons/wordlink.png">
    <style>
        fieldset#wiki { background-color:#eef; border:1px solid #88f;
                        -moz-border-radius:3px; -webkit-border-radius:3px; }
        fieldset#wiki legend { font-weight:bold; background-color:#ddf; border:1px solid blue; color:#006;
                               padding:2px; -moz-border-radius:2px; -webkit-border-radius:2px; }
        fieldset#wiki div { float:left; margin:2px; padding:1px 0; min-width:1.3em; text-align:center;
                            border:1px solid black; background-color:yellow;
                            -moz-border-radius:2px; -webkit-border-radius:2px; }
        fieldset#wiki div.tools  { font-weight:bold; background-color:#0e0; }
        fieldset#wiki div.poolst { font-weight:bold; background-color:#8f3; }
        fieldset#wiki div.problem  a {color:grey; text-decoration:line-through;  }
        fieldset#wiki div.googleOnly a { color:grey; }
        fieldset#wiki a { padding:2px; }
    </style>
</head>
<body>
$mdNavbar
<div class="smo-body-indent">

<h1 class="smo">$T_Example_pages_h1</h1>

<fieldset id="wiki">
<legend>Wikipedia</legend>
$wikiLinks
</fieldset>

<ul>
<li><a href="https://www3.smo.uhi.ac.uk/sengoidelc/donncha/tm/ilteangach/teangacha.php">Tríar Manach</a> - an Old Irish joke translated into many languages
    - See the <img src="/favicons/wordlink.png"> symbols for Wordlinked text
<li style="margin-top:3px">Gàidhlig:
   <a target="_top" href="./?sl=gd&amp;url=https://www3.smo.uhi.ac.uk/gaidhlig/corpus/samhlaidhean/">Samhlaidhean</a>
<li>Gàidhlig:
   <a target="_top" href="./?sl=gd&amp;navsize=1&amp;url=http://danamag.org/">Dàna</a> <i>(online magazine)</i>
<li>Français:
   <a target="_top" href="./?sl=fr&amp;url=https://www.gaulois.ardennes.culture.fr/accessible">Acy-Romance: Les Gaulois des Ardennes</a>
<li>Cree:
   <a target="_top" href="./?sl=cr-Latn&amp;url=https://cr.wikipedia.org/wiki/Maskisin">Maskisin</a>
<li>Ancient Greek:
   <a target="_top" href="./?sl=grc&amp;url=https://www.ellopos.net/elpenor/lessons/greek-pronunciation.asp">Pater Noster</a>
</ul>

</div>
$mdNavbar
</body>
</html>
END_HTML;

?>
