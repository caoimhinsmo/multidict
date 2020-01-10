<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Pragma: no-cache");

  try {
    $T = new SM_T('multidict/about');
    $T_Disclaimer             = $T->h('Disclaimer');
    $T_Disclaimer_EuropeanCom = $T->h('Disclaimer_EuropeanCom');
    $T_About_Multidict        = $T->h('About_Multidict');
    $T_About_Multidict_text1  = $T->h('About_Multidict_text1');
    $T_About_Multidict_text2  = $T->h('About_Multidict_text2');
    $T_About_Multidict_text3  = $T->h('About_Multidict_text3');
    $T_Note_for_dict_owners   = $T->h('Note_for_dict_owners');
    
    $T_About_Multidict_text1 = strtr($T_About_Multidict_text1,
      [ '{Multidict}' => '<a href="/multidict/">Multidict</a>',
        '{Caoimhín Ó Donnaíle}' => '<a href="http://www.smo.uhi.ac.uk/~caoimhin/">Caoimhín Ó Donnaíle</a>',
        '{Sabhal Mòr Ostaig}' => '<a href="http://www.smo.uhi.ac.uk/">Sabhal Mòr Ostaig</a>',
        '{Wordlink}' => '<a href="/wordlink/">Wordlink</a>',
        '{POOLS-T}' => '<a href="https://www.languages.dk/pools-t/">POOLS-T</a>',
        '{TOOLS}' => '<a href="https://www.languages.dk/tools/">TOOLS</a>',
        '{COOL}' => '<a href="https://www.languages.dk/#Cool">COOL</a>'
      ]);
    $T_About_Multidict_text3 = strtr($T_About_Multidict_text3,
      [ '{caoimhin@smo.uhi.ac.uk}' => '<a href="mailto:caoimhin@smo.uhi.ac.uk">caoimhin@smo.uhi.ac.uk</a>' ]);

    $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

    $EUlogo = '/EUlogos/' . SM_T::hl0() . '.jpg';
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $EUlogo)) { $EUlogo = '/EUlogos/en.jpg'; }

    $HTML = <<<END_HTML
$mdNavbar
<div class="smo-body-indent" style="max-width:75em">


<h1 class="smo">$T_About_Multidict</h1>

<p>$T_About_Multidict_text1</p>

<p>$T_About_Multidict_text2</p>

<p>$T_About_Multidict_text3</p>

<ul>
<li style="font-size:90%;margin-top:2em"><a href="dictionary_owners.php">$T_Note_for_dict_owners</a></li>
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
