<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {
    $T = new SM_T('multidict/wordlistdict');
    $T_No_words_found = $T->h('Cha_d_fhuaireadh_facal');

    $sl   = $_REQUEST['sl']   ?? '';
    $tl   = $_REQUEST['tl']   ?? '';
    $word = $_REQUEST['word'] ?? '';  $wordLIKE = strtr($word,'*?','%_');
    if (empty($sl))   { throw new SM_Exception('Missing parameter: ‘sl’'); }
    if (empty($tl))   { throw new SM_Exception('Missing parameter: ‘tl’'); }
    if (empty($word)) { throw new SM_Exception('Missing parameter: ‘word’'); }

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $stmt = $DbMultidict->prepare('SELECT word,disambig,gram,meaning FROM wordlistdict WHERE sl=:sl and tl=:tl and word LIKE :word ORDER BY word,disambig');
    $stmt->execute(['sl'=>$sl,':tl'=>$tl,'word'=>$wordLIKE]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $resHTML = '';
    foreach ($res as $r) {
       extract($r);
       $word = htmlspecialchars($word);
       $title = '';
       if (!empty($disambig)) {
           $title = htmlspecialchars($disambig);
           $title = " title='$title'";
       }
       if (!empty($gram)) { $gram = " $gram"; }
       $resHTML .= <<<resHTML
<p class=word title="$disambig">$word$gram</p>
<p class=meaning>$meaning</p>
resHTML;
    }
    if (empty($resHTML)) { $resHTML = "<p>$T_No_words_found</p>\n"; }

    echo <<<END_HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wordlist Dictionary</title>
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="icon" type="image/png" href="/favicons/multidict.png">
    <style>
        p.word { margin:0.2em 0; font-weight:bold; }
        p.meaning { margin:0 0 1em 1em; }
    </style>
</head>
<body>
<div class="smo-body-indent" style="max-width:80em">

$resHTML

</body>
</html>
END_HTML;

  } catch (Exception $e) { echo $e; }
    
?>
