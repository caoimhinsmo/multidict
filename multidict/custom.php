<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {
    $T = new SM_T('multidict/custom');
    $T_No_words_found = $T->h('Cha_d_fhuaireadh_facal');

    $sl   = $_REQUEST['sl']   ?? '';
    $tl   = $_REQUEST['tl']   ?? '';
    $word = $_REQUEST['word'] ?? '';  $wordLIKE = strtr($word,'*?','%_');
    if (empty($sl))   { throw new SM_Exception('Missing parameter: ‘sl’'); }
    if (empty($tl))   { throw new SM_Exception('Missing parameter: ‘tl’'); }
    if (empty($word)) { throw new SM_Exception('Missing parameter: ‘word’'); }

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $query = 'SELECT word,disambig,gram,meaning,0 AS pri FROM custom WHERE sl=:sl and tl=:tl and word LIKE :word'
           . ' UNION '
           . 'SELECT custom.word,disambig,gram,meaning,pri FROM custom,customwf'
           . ' WHERE sl=:sl AND tl=:tl AND customwf.lang=sl AND customwf.word=custom.word AND customwf.wf=:word'
           . ' ORDER BY pri,word,disambig';
    $stmt = $DbMultidict->prepare($query);
    $stmt->execute(['sl'=>$sl,':tl'=>$tl,'word'=>$wordLIKE]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $resHTML = '';
    foreach ($res as $r) {
       extract($r);
       $word = htmlspecialchars($word);
       $word = strtr ( $word,
        [ '&lt;ruby&gt;'  => '<ruby>',
          '&lt;/ruby&gt;' => '</ruby>',
          '&lt;rt&gt;'    => '<rt>',
          '&lt;/rt&gt;'   => '</rt>',
          '&lt;rp&gt;'    => '<rp>',
          '&lt;/rp&gt;'   => '</rp>' ] ); //Restore ruby markup which was messed up by htmlspecialchars
       $title = '';
       if (!empty($disambig)) {
           $title = htmlspecialchars($disambig);
           $title = " title='$title'";
       }
       if (!empty($gram)) { $gram = " $gram"; }
       $resHTML .= <<<resHTML
<p class=word title="$disambig"><b>$word</b>$gram</p>
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
        p.word { margin:0.2em 0; }
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
