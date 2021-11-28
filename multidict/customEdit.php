<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {
    $T = new SM_T('multidict/custom');
    $T_No_words_found = $T->h('Cha_d_fhuaireadh_facal');
    $T_Deasaich = $T->h('Deasaich');

    function tlLangs($sl) {
        $DbMultidict = SM_DbMultidictPDO::singleton('rw');
        $dict = 'custom';
        $stmtSELtls = $DbMultidict->prepare('SELECT tl FROM dictParam WHERE dict=:dict AND sl=:sl ORDER BY tl');
        $stmtSELtls->execute([':dict'=>$dict,':sl'=>$sl]);
        return $stmtSELtls->fetchAll(PDO::FETCH_COLUMN); //Languages into which this $sl is normally translated
    }

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');

    $idc = $_REQUEST['idc'] ?? '';
    if (empty($idc)) { throw new SM_Exception('Missing parameter: ‘idc’'); }

    if ($idc == -1) { //Flag to create a new word in the dictionary
        $word = $_POST['word'] ?? '';
        $sl   = $_POST['sl']   ?? '';
        $tl   = $_POST['tl']   ?? '';
        $meaning = $_POST['meaning'] ?? '';
        if (empty($word))    { throw new Exception('No value for word');    }
        if (empty($sl))      { throw new Exception('No value for sl');      }
        if (empty($tl))      { throw new Exception('No value for tl');      }
        if (empty($meaning)) { throw new Exception('No value for meaning'); }
        $stmtINSc = $DbMultidict->prepare('INSERT INTO custom (sl,word) VALUES (:sl,:word)');
        $stmtINSc->execute([':sl'=>$sl,':word'=>$word]);
        $idc = $DbMultidict->lastInsertId();
        $tlLangs = tlLangs($sl);
        foreach ($tlLangs as $tlLang) { $meanings[$tlLang] = ''; }
        $meanings[$tl] = $meaning;
        $stmtINSctr = $DbMultidict->prepare('INSERT INTO customtr (idc,tl,meaning) VALUES(:idc,:tl,:meaning)');
        foreach ($meanings as $tlLang=>$tlMeaning) {
            $stmtINSctr->execute([':idc'=>$idc,':tl'=>$tlLang,':meaning'=>$tlMeaning]);
        }
        $server = $_SERVER['SERVER_NAME'];
        header("Location:https://$server/multidict/customEdit.php?idc=$idc&newword");
    }

    $HTML = $newwordMessage = '';

    $stmtC = $DbMultidict->prepare('SELECT sl, word, disambig, gram, pri FROM custom WHERE idc=:idc');
    $stmtC->execute([':idc'=>$idc]);
    $res = $stmtC->fetch(PDO::FETCH_ASSOC);
    if ($res===false) { throw new Exception("No word exists with id $idc"); }
    extract($res);

    $myCLIL = SM_myCLIL::singleton();
    $user = ( isset($myCLIL->id) ? $myCLIL->id : '' );

    $grp = "cw-$sl";
    $stmtPermission = $DbMultidict->prepare('SELECT 1 FROM userGrp WHERE user=:user AND grp=:grp');
    $stmtPermission->execute([':user'=>$user,':grp'=>$grp]);
    if (!$stmtPermission->fetch()) { throw new SM_Exception("User $user has no permission to edit Custom Wordlist entries for language $sl"); }

    $wordSC     = htmlspecialchars($word);
    $disambigSC = htmlspecialchars($disambig);
    $gramSC     = htmlspecialchars($gram);

    $cHTML = <<<END_cHTML
<p style="margin:0.1em 0 0 4em;font-size:70%">Editing word $idc — Language $sl</p>
<p style="margin:0.3em 0 0.3em 0;font-weight:bold;font-size:120%">$word
<img src="/icons-smo/curAs2.png" alt="DELETE" style="padding-left:1em" onclick="deleteWord('$idc')"></p>

<table id=formtab>
<tr><td>Word</td><td><input id=word value="$wordSC" style="width:20em"></td></tr>
<tr><td>Disambiguator</td><td><input id=disambig value="$disambigSC"></td></tr>
<tr><td>Grammar</td><td><input id=gram value="$gramSC"></td></tr>
<tr><td>Priority</td><td><input id=pri value="$pri"></td></tr>
</table>
END_cHTML;

    $cwfHTML = '';
    $stmtCwf = $DbMultidict->prepare('SELECT idcwf,wf,pri,priWhy FROM customwf WHERE idc=:idc ORDER BY pri,wf');
    $stmtCwf->execute([':idc'=>$idc]);
    $res = $stmtCwf->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res as $r) {
        extract($r);
        $wfSC = htmlspecialchars($wf);
        $cwfHTML .= <<<END_cwfHTML
<tr><td><input value='$wfSC'> $pri - $priWhy</td></tr>
END_cwfHTML;
    }
    $cwfHTML = <<<END_cwfHTML2
<p style="margin-bottom:0"><b>Wordforms</b> <i>(search-keys)</i></p>
<table>
$cwfHTML
</table>
END_cwfHTML2;

    $ctrHTML = '';
    $meanings = [];
    $tlLangs = tlLangs($sl);
    foreach ($tlLangs as $tlLang) { $meanings[$tlLang] = ''; }
    $stmtCtr = $DbMultidict->prepare('SELECT idctr,tl,meaning FROM customtr WHERE idc=:idc');
    $stmtCtr->execute([':idc'=>$idc]);
    $res = $stmtCtr->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res as $r) {
        extract($r);
        $meanings[$tl] = htmlspecialchars($meaning);
    }
    foreach ($meanings as $tl=>$meaning) {
        $ctrHTML .= <<<END_ctrHTML
<tr><td>$tl</td><td><input value='$meaning' style="width:25em"></td></tr>
END_ctrHTML;
    }
    $ctrHTML = <<<END_ctrHTML2
<p style="margin-bottom:0"><b>Translation</b></p>
<table>
$ctrHTML
</table>
END_ctrHTML2;

if (isset($_REQUEST['newword'])) { $newwordMessage = '<p id=message>New word added <span style="color:green">✓</span> &nbsp; You can now edit it further if you wish</p>'; }

/*
       $word = strtr ( $word,
        [ '&lt;ruby&gt;'  => '<ruby>',
          '&lt;/ruby&gt;' => '</ruby>',
          '&lt;rt&gt;'    => '<rt>',
          '&lt;/rt&gt;'   => '</rt>',
          '&lt;rp&gt;'    => '<rp>',
          '&lt;/rp&gt;'   => '</rp>' ] ); //Restore ruby markup which was messed up by htmlspecialchars
*/

    echo <<<END_HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wordlist Dictionary - edit word $idc</title>
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="icon" type="image/png" href="/favicons/multidict.png">
    <style>
        table#formtab td:nth-child(1) { text-align:right; }
        p#message { margin:0; padding:0.1em 0.3em; background-color:black; color:white; }

        p.word { margin:0.2em 0; }
        p.meaning { margin:0 0 1em 1em; }
        span.gram { padding-left:0.3em; font-size:70%; color:red; }
    </style>
    <script>
        function deleteWord(idc) {
            if (!confirm('Really delete this word?\\r\\n (together with any wordforms and translations)')) { return; }
            alert('Delete:' + idc);
            let bodyEl = document.querySelector('body');
            bodyEl.innerHTML = '<p>The word has been deleted</p>';
        }

/* Canibalize this to make build new AJAX function
        function createPortfolio() {
            var title   = document.getElementById('createTitle').value.trim();
            var teacher = document.getElementById('createTeacher').value.trim();
            if (title=='') { alert('You must give your portfolio a name'); return; }
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if       (resp=='OK')     { window.location.href = '/clilstore/portfolio.php'; }
                     else if (resp=='nouser') { alert('There is no such Clilstore userid as '+teacher); }
                     else                     { alert('$T_Error_in pfCreate: '+resp); }
                }
            }
            var formData = new FormData();
            formData.append('title',title);
            formData.append('teacher',teacher);
            xhr.open('POST', 'ajax/pfCreate.php');
            xhr.send(formData);
        }
*/
    </script>
</head>
<body id=body>
<div class="smo-body-indent" style="max-width:80em">

$newwordMessage
$cHTML
$cwfHTML
$ctrHTML

</body>
</html>
END_HTML;

  } catch (Exception $e) { echo $e; }
    
?>
