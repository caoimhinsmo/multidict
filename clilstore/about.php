<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Pragma: no-cache");

  try {
    $T = new SM_T('clilstore/about');
    $T_Disclaimer             = $T->_('Disclaimer');
    $T_Disclaimer_EuropeanCom = $T->_('Disclaimer_EuropeanCom');
    
    $csNavbar = SM_csNavbar::csNavbar($T->domhan);

    $EUlogo = '/EUlogos/' . SM_T::hl0() . '.jpg';
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $EUlogo)) { $EUlogo = '/EUlogos/en.jpg'; }

    $HTML = <<<END_HTML
$csNavbar
<div class="smo-body-indent" style="max-width:1000px">

<h1 class="smo">Clilstore</h1>

<p>Clilstore is a store of copyleft <u>content and language integrated</u> teaching materials.</p>

<p>It is being developed by <a href="http://~caoimhin/">Caoimhín Ó Donnaíle</a> at <a href="http://www.smo.uhi.ac.uk/">Sabhal Mòr Ostaig</a> in Skye
as part of the European funded <a href="http://www.languages.dk/">COOL</a> project (2018-2021),
led by <a href="//agimeno.webs.upv.es/">Ana Gimeno</a> at <a href="//www.upv.es/">Universitat Politècnica de València</a> in Spain,
assisted by Kent Andersen at <a href="//faz.dk/">FAZ</a> in Denmark,
and with other partners from
<a href="https://www.ulster.ac.uk/">Ollscoil Uladh</a> in Ireland,
<a href="https://kroggaardsskolen.skoleporten.dk/">Kroggårdsskolen</a> in Odense in Denmark,
<a href="https://www.etimalta.com/">ETI Malta (Executive Training Institute)</a>, and
<a href="https://www.eliovittorini.gov.it/wordpress/">Liceo Scientifico Statale Elio Vittorini</a> in Milan in Italy.</p>

<p>It was originally developed by Caoimhín Ó Donnaíle at Sabhal Mòr Ostaig
as part of the European funded <a href="http://www.languages.dk/tools/">TOOLS</a> project (2012-2014), led by Kent Andersen in
<a href="https://www.sde.dk/">Denmark</a> and with other partners from
<a href="https://www.sdu.dk/en/Om_SDU/Institutter_centre/C_Mellemoest">Denmark</a>,
<a href="https://www.ulster.ac.uk/">Ireland</a>,
<a href="https://www.mprc.lt/">Lithuania</a>,
<a href="https://www.uevora.pt/">Portugal</a>,
<a href="https://www.upv.es/">Spain</a> and
<a href="https://www.eurocall-languages.org/">EuroCALL</a>.</p>

<p>Clilstore uses <a href="/wordlink/">Wordlink</a>, a WWW based facility which links arbitrary webpages automatically, word by word with online dictionaries.  And Wordlink in turn uses  <a href="/multidict/" target="_top">Multidict</a>, a multiple dictionary lookup facility which makes use of freely available online dictionaries.  Both Wordlink and Multidict were developed as part of the European funded <a href="//www.languages.dk/pools-t/">POOLS-T</a> project (2008-2010) and their development is continuing as part of the present COOL project.</p>

<p>We would be very glad indeed to receive comments or suggestions on this facility - Simply send them by e-mail (in any language) to <a href="mailto:caoimhin@smo.uhi.ac.uk">caoimhin@smo.uhi.ac.uk</a>.  If you have suggestions as to other online dictionaries which you think would be worth adding to Multidict, we would be very happy to consider them.</p>

<p>COOL is a social media friendly project, and you are warmly invited to make suggestions and give feedback via
<a href="//www.facebook.com/tools4clil"><img src="/favicons/facebook.png" alt=""> Facebook</a>,
<a href="//www.linkedin.com/groups/Tools4Clil-4269787"><img src="/favicons/linkedin.png" alt=""> Linkedin</a>,
<a href="//www.twitter.com/tools4clil"><img src="/favicons/twitter.png" alt=""> Twitter</a>, and our
<a href="//tools4clil.wordpress.com/"><img src="/favicons/wordpress.png" alt=""> Blog</a>.
You can see our <a href="//www.facebook.com/tools4clil/photos_albums">photo albums</a> on Facebook.</p>

<div style="min-height:65px;border:2px solid #47d;margin:4em 0 0.5em 0;border-radius:4px;color:#47d;font-size:95%">
<div style="float:left;margin-right:1.5em">
<a href="//eacea.ec.europa.eu/erasmus-plus_en"><img src="$EUlogo" alt="" style="margin:3px"></a>
</div>
<div style="min-height:59px">
<p style="margin:0.3em 0;color:#1e4d9f;font-size:75%"><i>$T_Disclaimer:</i> $T_Disclaimer_EuropeanCom</p>
</div>
</div>
<p style="font-size:75%;margin:0">Clilstore is a well-behaved, responsible website - See our short and simple <a href="privacyPolicy.php">privacy policy</a>.</p>

</div>
$csNavbar
END_HTML;

  } catch (Exception $e) {
    $HTML = $e->getMessage();
  }

  $HTMLdoc = <<<END_HTMLdoc
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Clilstore</title>
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="StyleSheet" href="style.css">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
</head>
<body>

$HTML

</body>
</html>
END_HTMLdoc;

  echo $HTMLdoc;
?>
