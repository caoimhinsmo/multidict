<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Pragma: no-cache");

  try {
    $T = new SM_T('clilstore/about');
    $T_Disclaimer             = $T->h('Disclaimer');
    $T_Disclaimer_EuropeanCom = $T->h('Disclaimer_EuropeanCom');
    $T_About_Clilstore        = $T->h('mu_Clilstore');
    $T_About_Clilstore_text1  = $T->h('About_Clilstore_text1');
    $T_About_Clilstore_text2  = $T->h('About_Clilstore_text2');
    $T_About_Clilstore_text3  = $T->h('About_Clilstore_text3');
    $T_About_Clilstore_text4  = $T->h('About_Clilstore_text4');
    $T_About_Clilstore_text5  = $T->h('About_Clilstore_text5');
    $T_About_Clilstore_text6  = $T->h('About_Clilstore_text6');
    $T_About_Clilstore_text7  = $T->h('CS_is_well_behaved');
    $T_Denmark                = $T->h('Denmark');
    $T_Ireland                = $T->h('Ireland');
    $T_Lithuania              = $T->h('Lithuania');
    $T_Portugal               = $T->h('Portugal');
    $T_Spain                  = $T->h('Spain');
    $T_photo_albums           = $T->h('photo_albums');

    $T_About_Clilstore_text1 = strtr($T_About_Clilstore_text1,
      [ '{' => '<i>',
        '}' => '</i>'
      ]);
    $T_About_Clilstore_text2 = strtr($T_About_Clilstore_text2,
      [ '{Caoimhín Ó Donnaíle}' => '<a href="http://www.smo.uhi.ac.uk/~caoimhin/">Caoimhín Ó Donnaíle</a>',
        '{Sabhal Mòr Ostaig}' => '<a href="http://www.smo.uhi.ac.uk/">Sabhal Mòr Ostaig</a>',
        '{COOL}' => '<a href="http://www.languages.dk/">COOL</a>',
        '{Ana Gimeno}' => '<a href="https://agimeno.webs.upv.es/">Ana Gimeno</a>',
        '{Universitat Politècnica de València}' => '<a href="https://www.upv.es/">Universitat Politècnica de València</a>',
        '{FAZ}' => '<a href="https://faz.dk/">FAZ</a>',
        '{Ollscoil Uladh}' => '<a href="https://www.ulster.ac.uk/">Ollscoil Uladh</a>',
        '{Kroggårdsskolen}' => '<a href="https://kroggaardsskolen.skoleporten.dk/">Kroggårdsskolen</a>',
        '{ETI Malta (Executive Training Institute)}' => '<a href="https://www.etimalta.com/">ETI Malta (Executive Training Institute)</a>',
        '{Liceo Scientifico Statale Elio Vittorini}' => '<a href="https://www.eliovittorini.gov.it/wordpress/">Liceo Scientifico Statale Elio Vittorini</a>'
      ]);
    $T_About_Clilstore_text3 = strtr($T_About_Clilstore_text3,
      [ '{TOOLS}' => '<a href="http://www.languages.dk/tools/">TOOLS</a>',
        '{{Denmark1}}' => "<a href='https://www.sde.dk/'>$T_Denmark</a>",
        '{{Denmark2}}' => "<a href='https://www.sdu.dk/en/Om_SDU/Institutter_centre/C_Mellemoest'>$T_Denmark</a>",
        '{{Ireland}}' => "<a href='https://www.ulster.ac.uk/'>$T_Ireland</a>",
        '{{Lithuania}}' => "<a href='https://www.mprc.lt/'>$T_Lithuania</a>",
        '{{Portugal}}' => "<a href='https://www.uevora.pt/'>$T_Portugal</a>",
        '{{Spain}}' => "<a href='https://www.upv.es/'>$T_Spain</a>",
        '{EuroCALL}' => '<a href="https://www.eurocall-languages.org/">EuroCALL</a>'
      ]);
    $T_About_Clilstore_text4 = strtr($T_About_Clilstore_text4,
      [ '{Wordlink}' => '<a href="/wordlink/">Wordlink</a>',
        '{Multidict}' => '<a href="/multidict/">Multidict</a>',
        '{POOLS-T}' => '<a href="https://www.languages.dk/pools-t/">POOLS-T</a>'
      ]);
    $T_About_Clilstore_text5 = strtr($T_About_Clilstore_text5,
      [ '{caoimhin@smo.uhi.ac.uk}' => '<a href="mailto:caoimhin@smo.uhi.ac.uk">caoimhin@smo.uhi.ac.uk</a>'
      ]);
    $T_About_Clilstore_text6 = strtr($T_About_Clilstore_text6,
      [ '{Facebook}' => '<a href="//www.facebook.com/tools4clil" style="white-space:nowrap"><img src="/favicons/facebook.png" alt=""> Facebook</a>',
        '{Linkedin}' => '<a href="//www.linkedin.com/groups/Tools4Clil-4269787" style="white-space:nowrap"><img src="/favicons/linkedin.png" alt=""> Linkedin</a>',
        '{Twitter}' => '<a href="//www.twitter.com/tools4clil" style="white-space:nowrap"><img src="/favicons/twitter.png" alt=""> Twitter</a>',
        '{Wordpress}' => '<a href="//tools4clil.wordpress.com/" style="white-space:nowrap"><img src="/favicons/wordpress.png" alt=""> Wordpress</a>',
        '{photo albums}' => "<a href='//www.facebook.com/tools4clil/photos_albums'>$T_photo_albums</a>"
     ]);
    $T_About_Clilstore_text7 = strtr($T_About_Clilstore_text7,
      [ '{' => '<a href="/clilstore/privacyPolicy.php">',
        '}' => '</a>'
      ]);

    $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

    $EUlogo = '/EUlogos/' . SM_T::hl0() . '.jpg';
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $EUlogo)) { $EUlogo = '/EUlogos/en.jpg'; }

    $HTML = <<<END_HTML
$mdNavbar
<div class="smo-body-indent" style="max-width:1000px">

<h1 class="smo">$T_About_Clilstore</h1>

<p>$T_About_Clilstore_text1</p>

<p>$T_About_Clilstore_text2</p>

<p>$T_About_Clilstore_text3</p>

<p>$T_About_Clilstore_text4</p>

<p>$T_About_Clilstore_text5</p>

<p>$T_About_Clilstore_text6</p>

<div style="min-height:65px;border:2px solid #47d;margin:4em 0 0.5em 0;border-radius:4px;color:#47d;font-size:95%">
<div style="float:left;margin-right:1.5em">
<a href="//eacea.ec.europa.eu/erasmus-plus_en"><img src="$EUlogo" alt="" style="margin:3px"></a>
</div>
<div style="min-height:59px">
<p style="margin:0.3em 0;color:#1e4d9f;font-size:75%"><i>$T_Disclaimer:</i> $T_Disclaimer_EuropeanCom</p>
</div>
</div>

<p style="font-size:75%;margin:0">$T_About_Clilstore_text7</p>

</div>
$mdNavbar
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
