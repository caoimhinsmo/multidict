<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Pragma: no-cache");

  try {
    $T = new SM_T('wordlink/about');
    $T_Disclaimer             = $T->_('Disclaimer');
    $T_Disclaimer_EuropeanCom = $T->_('Disclaimer_EuropeanCom');
    
    $csNavbar = SM_csNavbar::csNavbar($T->domhan);

    $EUlogo = '/EUlogos/' . SM_T::hl0() . '.jpg';
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $EUlogo)) { $EUlogo = '/EUlogos/en.jpg'; }

    $HTML = <<<END_HTML
<ul class="smo-navlist">
<li><a href="./" title="Wordlink - a facility to link web pages automatically word-by-word to online dictionaries">Wordlink</a></li>
</ul>
<div class="smo-body-indent" style="max-width:72em">

<h1 class="smo">Wordlink</h1>

<p><a href="./" target="_top">Wordlink</a> is a WWW based facility which links arbitrary webpages automatically, word by word with online dictionaries.
It was developed by <a href="https://www.smo.uhi.ac.uk/~caoimhin/">Caoimhín Ó Donnaíle</a> at <a href="http://www.smo.uhi.ac.uk/">Sabhal Mòr Ostaig</a>
as part of the European funded <a href="http://www.languages.dk/pools-t/">POOLS-T</a> project (2008-2010), development was continued as part of the
European funded <a href="https://www.languages.dk/tools/">TOOLS</a> project (2012-2014), and is being further continued as part of the European funded
<a href="https://languages.dk/">COOL</a> project (2018-2019).</p>

<p>It works in conjunction with <a href="/multidict/" target="_top">Multidict</a>, a multiple dictionary lookup facility, and it is the basis of <a href="/clilstore/" target="_top">Clilstore</a>, a store of copyleft content and language integrated teaching materials.</p>

<p>We would be very glad indeed to receive comments or suggestions on this facility - Simply send them by e-mail (in any language)
to <a href="mailto:caoimhin@smo.uhi.ac.uk">caoimhin@smo.uhi.ac.uk</a>.  
If you have suggestions as to other online dictionaries which you think would be worth adding to the system, we would be very happy to consider them.</p>

<div style="min-height:65px;border:2px solid #47d;margin:4em 0 0.5em 0;border-radius:4px;color:#47d;font-size:95%">
<div style="float:left;margin-right:1.5em">
<a href="http://eacea.ec.europa.eu/llp/index_en.php"><img src="$EUlogo" alt="" style="margin:3px"></a>
</div>
<div style="min-height:59px">
<p style="margin:0.3em 0;color:#1e4d9f;font-size:75%"><i>$T_Disclaimer:</i> $T_Disclaimer_EuropeanCom</p>
</div>
</div>

</div>
<ul class="smo-navlist">
<li><a href="./" title="Wordlink - a facility to link web pages automatically word-by-word to online dictionaries">Wordlink</a></li>
</ul>
END_HTML;

  } catch (Exception $e) {
    $HTML = $e->getMessage();
  }

  $HTMLdoc = <<<END_HTMLdoc
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>About Wordlink</title>
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="icon" type="image/png" href="/favicons/wordlink.png">
</head>
<body>

$HTML

</body>
</html>
END_HTMLdoc;

  echo $HTMLdoc;
?>
