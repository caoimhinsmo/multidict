<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Pragma: no-cache");

  $cs = $_REQUEST['cs'] ?? 0;  //$cs=1 ⇒ page tailored for Clilstore

  try {
    $T = new SM_T('wordlink/startAdvice');
    $T_startAdvice1           = $T->h('startAdvice1');
    $T_startAdvice1cs         = $T->h('startAdvice1cs');
    $T_startAdvice2           = $T->h('startAdvice2');
    $T_startAdvice3           = $T->h('startAdvice3');
    $T_startAdviceNoteWords   = $T->h('startAdviceNoteWords');
    $T_startAdviceNoteForms   = $T->h('startAdviceNoteForms');

    $T_startAdvice1cs = strtr ( $T_startAdvice1cs, [ '{*}' => '<sup>*</sup>' ] );

    if ($cs==1) { $HTMLinner = <<<END_HTMLinner_cs
<p>$T_startAdvice1cs</p>
<p>$T_startAdvice2</p>
<p>$T_startAdvice3</p>
<p style="font-size:70%;margin:2px 0 1em 1em">(*$T_startAdviceNoteWords).</p>
END_HTMLinner_cs;
   } else { $HTMLinner = <<<END_HTMLinner
<p>$T_startAdvice1</p>
<p>$T_startAdvice2</p>
<p>$T_startAdvice3</p>

<hr style="margin:0;width:5em;height:1px;color:green;background-color:green">
<p style="font-size:70%;margin:2px 0 1em 0">1. $T_startAdviceNoteForms<br>
2. $T_startAdviceNoteWords</p>
END_HTMLinner;
    }

    $HTML = <<<END_HTML
<body>
<div class="smo-body-indent" style="margin-left:1em">

<div style="margin:200px 0.5em 0 0.5em;border: 1px solid green;border-radius:0.5em;border-bottom-left-radius:0;padding:0 0.5em; background-color:#efe;color:green">
$HTMLinner
</div>

</div>
</body>
END_HTML;

  } catch (Exception $e) {
    $HTML = $e->getMessage();
  }

  $HTMLdoc = <<<END_HTMLdoc
<!DOCTYPE html>
<html>
<head>
    <title>Wordlink start advice</title>
    <link rel="StyleSheet" href="/css/smo.css">
</head>
$HTML
</html>
END_HTMLdoc;

  echo $HTMLdoc;
?>
