<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Pragma: no-cache");

    $T = new SM_T('multidict/dictionary_owners');
    $T_Note_for_dict_owners    = $T->h('Note_for_dict_owners');
    $T_Dictionary_owners_text1 = $T->h('Dictionary_owners_text1');
    $T_Dictionary_owners_text2 = $T->h('Dictionary_owners_text2');
    $T_Dictionary_owners_text3 = $T->h('Dictionary_owners_text3');

    $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

    $HTML = <<<End_HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Multidict: $T_Note_for_dict_owners</title>
    <link rel="StyleSheet" href="/css/smo.css" >
</head>
<body>
$mdNavbar
<div class="smo-body-indent" style="max-width:75em"	>

<h1 class="smo">$T_Note_for_dict_owners</h1>

<p>$T_Dictionary_owners_text1</p>

<p>$T_Dictionary_owners_text2</p>

<p>$T_Dictionary_owners_text3</p>

</div>
$mdNavbar
</body>
</html>
End_HTML;

    echo $HTML;
?>
