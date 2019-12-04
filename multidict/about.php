<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Pragma: no-cache");

  try {
    $T = new SM_T('multidict/about');
    $T_Disclaimer             = $T->_('Disclaimer');
    $T_Disclaimer_EuropeanCom = $T->_('Disclaimer_EuropeanCom');
    
    $csNavbar = SM_csNavbar::csNavbar($T->domhan);

    $EUlogo = '/EUlogos/' . SM_T::hl0() . '.jpg';
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $EUlogo)) { $EUlogo = '/EUlogos/en.jpg'; }

    $HTML = <<<END_HTML
<ul class="smo-navlist">
<li><a href="./" title="Wordlink - a flexible system to search multiple dictionaries">Multidict</a></li>
</ul>
<div class="smo-body-indent" style="max-width:75em">


<h1 class="smo">About Multidict</h1>

<p><a href="./">Multidict</a> is a multiple dictionary lookup facility.  It was developed by <a href="http://www.smo.uhi.ac.uk/~caoimhin/">Caoimhín Ó Donnaíle</a>
at <a href="http://www.smo.uhi.ac.uk/">Sabhal Mòr Ostaig</a> to work in conjuction with <a href="/wordlink/about.html">Wordlink</a> (and historically with Kent Andersen’s
<a href="http://www.languages.dk/tools/index.htm#The_Web_Page_Text_Blender">TextBlender</a>), 
as part of the European funded <a href="http://www.languages.dk/pools-t/">POOLS-T</a> project (2008-2010). Development continued as part of the European funded <a href="http://www.languages.dk/tools/">TOOLS</a> project (2012-2014).  It is now being further developed as part of the European funded <a href="http://www.languages.dk/cool/">COOL</a> project (2018-2021).</p>

<p>Multidict allows the user to select easily from a choice of online dictionaries.  Built into it is a large database of dictionaries
with information on the languages they cater for and the parameters they require.  Multidict attempts to remember the user’s previous
dictionary choices so as to speed up subsequent selection.  Multidict also includes a facility to link to dictionaries in page-image format
at the Web Archive, Google Books, etc, via an index of the initial word on each page.</p>

<p>We would be very glad indeed to receive comments or suggestions on this facility - Simply send them by e-mail (in any language)
to <a href="mailto:caoimhin@smo.uhi.ac.uk">caoimhin@smo.uhi.ac.uk</a>.  If you have suggestions as to other online dictionaries
which you think would be worth adding to the system, we would be very happy to consider them.</p>

<ul>
<li style="font-size:90%"><a href="for_dictionary_owners.html">A note for dictionary owners</a></li>
</ul>

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
<li><a href="./" title="Wordlink - a flexible system to search multiple dictionaries">Multidict</a></li>
</ul>
END_HTML;

  } catch (Exception $e) {
    $HTML = $e->getMessage();
  }

  $HTMLdoc = <<<END_HTMLdoc
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Multidict</title>
    <link rel="StyleSheet" href="/css/smo.css">
</head>
<body>

$HTML

</body>
</html>
END_HTMLdoc;

  echo $HTMLdoc;
?>
