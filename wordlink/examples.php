<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");
  $servername = $_SERVER['SERVER_NAME'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wordlink example pages</title>
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

<ul class="smo-navlist">
<li><a href="/"><?php echo $servername; ?></a></li>
<li><a href="./" title="Wordlink - a facility to link web pages automatically word-by-word to online dictionaries">Wordlink</a></li>
</ul>
<div class="smo-body-indent">


<h1 class="smo">Some example pages to show the working of Wordlink</h1>

<fieldset id="wiki">
<legend>Wikipedia</legend>
<?php
  try {
    $DbMultidict = SM_DbMultidictPDO::singleton('r');
    $slArr = SM_WlSession::slArr();
    foreach ($slArr as $sl=>$langInfo) {
        $endonym    = $langInfo['endonym'];
        $wiki       = $langInfo['wiki'];
        $multidicts = $langInfo['multidicts'];
        $class      = $langInfo['pools'];
      if ($class=='omit' or empty($wiki)) { continue; }
        if ($multidicts=='|Google|') { $class = 'googleOnly'; }
        echo "<div class=\"wiki $class\"><a target=\"_top\" href=\"./?sl=$sl&amp;url=http://$wiki.wikipedia.org/\" title=\"$endonym\">$sl</a></div>\n";
    }
    $stmt = null;
    echo "<div class=\"wiki tools\" style=\"margin-left:1em\"><a target=\"_top\" href=\"./?sl=en&amp;url=http://simple.wikipedia.org/\" title=\"Simple English\">simple</a></div>\n";

  } catch (exception $e) { echo $e; }
?>
</fieldset>

<ul>
<li><a href="http://www.smo.uhi.ac.uk/sengoidelc/donncha/tm/ilteangach/teangacha.php">Tríar Manach</a> - an Old Irish joke translated into many languages
    - See the <img src="/favicons/wordlink.png"> symbols for Wordlinked text
<li style="margin-top:3px">Gàidhlig:
   <a target="_top" href="./?sl=gd&amp;url=http://www.smo.uhi.ac.uk/gaidhlig/corpus/samhlaidhean/">Samhlaidhean</a>
<li>Gàidhlig:
   <a target="_top" href="./?sl=gd&amp;navsize=1&amp;url=http://danamag.org/">Dàna</a> <i>(online magazine)</i>
<li>Dansk:
   <a target="_top" href="./?sl=da&amp;url=http://www.rockmusical.dk/">Rockmusical</a>
<li>Français:
   <a target="_top" href="./?sl=fr&amp;url=http://www.gaulois.ardennes.culture.fr/accessible">Acy-Romance: Les Gaulois des Ardennes</a>
<li>Español:
   <a torget="_top" href="./?sl=es&amp;url=http%3A%2F%2Fwww.practicaespanol.com%2Fnoticias-en-practica-espanol">Noticias en Practica Español</a>
<li>Cree:
   <a target="_top" href="./?sl=cr-Latn&amp;url=http://cr.wikipedia.org/wiki/Maskisin">Maskisin</a>
<li>Ancient Greek:
   <a target="_top" href="./?sl=grc&amp;url=http%3A%2F%2Fwww.ellopos.net%2Felpenor%2Flessons%2Fgreek-pronunciation.asp">Pater Noster</a>
</ul>

</div>
<ul class="smo-navlist">
<li><a href="/"><?php echo $servername ?></a>
<li><a href="./" title="Wordlink - a facility to link web pages automatically word-by-word to online dictionaries">Wordlink</a>
</ul>

<div class="smo-latha">2015-12-02 <a href="/~caoimhin/cpd.html">CPD</a></div>
</body>
</html>
