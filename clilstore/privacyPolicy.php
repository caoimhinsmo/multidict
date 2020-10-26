<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");
  header("Cache-Control:no-cache,must-revalidate");

  try {
    $myCLIL = SM_myCLIL::singleton();

    $T = new SM_T('clilstore/privacyPolicy');

    $T_Privacy_policy        = $T->h('Privacy_policy');
    $T_privacy_policy_msg1   = $T->h('privacy_policy_msg1');
    $T_privacy_policy_msg2   = $T->h('privacy_policy_msg2');
    $T_privacy_policy_msg3   = $T->h('privacy_policy_msg3');
    $T_GDPR_policy_statement = $T->h('GDPR_policy_statement');

    $GDPR_link_template = "/clilstore/GDPR_statements/GDPR_COOL_{hl}.pdf";
    $hl = SM_T::hl0();
    $GDPR_link = str_replace('{hl}',$hl,$GDPR_link_template);
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $GDPR_link)) {
        $GDPR_link = str_replace('{hl}','en',$GDPR_link_template); //Default to English if no local translation
    }

   $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

    $HTMLbody = <<<END_HTML_body
$mdNavbar
<div class="smo-body-indent" style="max-width:1000px">

<h1 class="smo">$T_Privacy_policy</h1>

<p>$T_privacy_policy_msg1</p>
<p>$T_privacy_policy_msg2</p>
<p>$T_privacy_policy_msg3</p>

<p style="margin:1.5em 0 0.5em 0"><a href="$GDPR_link">$T_GDPR_policy_statement ⇒</a></p>

</div>
$mdNavbar
END_HTML_body;

  } catch (Exception $e) {
      $HTMLbody = $e;
  }

  $HTML = <<<END_HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>$T_Privacy_policy</title>
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="StyleSheet" href="style.css">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
</head>
<body>
$HTMLbody
</body>
</html>
END_HTML;

  echo $HTML;
?>
