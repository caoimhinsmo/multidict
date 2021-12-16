<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {
    $T = new SM_T('multidict/custom');
    $T_No_words_found = $T->h('Cha_d_fhuaireadh_facal');
    $T_Deasaich = $T->h('Deasaich');
    $T_Error_in = $T->h('Error_in');

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
    $user = $myCLIL->id ?? '';
    if (empty($user)) { throw new SM_Exception("You are not logged on"); }

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
<img src="/icons-smo/curAs2.png" alt="DELETE" title="Delete the word" style="padding-left:1em" onclick="deleteWord('$idc')"></p>

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
        $wfSC     = htmlspecialchars($wf);
        $priWhySC = htmlspecialchars($priWhy);
        $cwfHTML .= <<<END_cwfHTML
<tr>
<td><input id=wf$idcwf value='$wfSC' onchange="changewf('$idcwf')"></td>
<td><input value='$pri'></td>
<td><input value='$priWhySC'></td></td>
<td><span id="wf$idcwf-tick" class=change>✔<span></td>
</tr>
END_cwfHTML;
    }
    $cwfHTML = <<<END_cwfHTML2
<p style="margin-bottom:0"><b>Wordforms</b> <i>(search-keys)</i></p>
<table id=wftable>
<tr><td>wordform</td><td>priority</td><td>why</td><td></td></tr>
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
<tr>
<td>$tl</td>
<td><input id=tr$idctr value='$meaning' style="width:25em" onchange="changetr('$idctr')"></td>
<td><span id="tr$idctr-tick" class=change>✔<span></td>
</tr>
END_ctrHTML;
    }
    $ctrHTML = <<<END_ctrHTML2
<p style="margin-bottom:0"><b>Translation</b></p>
<table id=trtable>
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
        table#wftable { border-collapse:collapse; margin-left:0.8em; }
        table#wftable tr:nth-child(1) td { padding:0 0.2em; }
        table#wftable td:nth-child(2) { text-align:right; }
        table#wftable td:nth-child(1) input { width:14em; }
        table#wftable td:nth-child(2) input { width:5em; text-align:right; }
        table#wftable td:nth-child(3) input { width:6em; }
        table#trtable { border-collapse:collapse; margin-left:0.8em; }
        table#trtable td:nth-child(1) { padding-right:0.3em; }
    </style>
    <script>
        function deleteWord(idc) {
            if (!confirm('Really delete this word?\\r\\n (together with any wordforms and translations)')) { return; }
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if (resp=='OK') {
                        let bodyEl = document.querySelector('body');
                        bodyEl.innerHTML = '<p>The word has been deleted</p>';
                    } else {
                        alert('$T_Error_in deleteWord: '+resp);
                    }
                }
            }
            var formData = new FormData();
            formData.append('id',idc);
            formData.append('operation','deleteWord');
            xhr.open('POST', 'ajax/customWordOp.php');
            xhr.send(formData);
        }

        function changetr(idctr) {
            let trel = document.getElementById('tr'+idctr);
            let newval = trel.value;
            alert('changetr ' + idctr + '→' + newval);
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if (resp=='OK') {
                        alert('OK');
                        var tickel = document.getElementById('tr'+idctr+'-tick');
                        tickel.classList.remove('changed'); //Remove the class (if required) and add again after a tiny delay, to restart the animation
                        setTimeout(function(){tickel.classList.add('changed');},50);
                    } else {
                        alert('$T_Error_in changetr: '+resp);
                    }
                }
            }
            var formData = new FormData();
            formData.append('id',idctr);
            formData.append('operation','changetr');
            formData.append('newval',newval);
            xhr.open('POST', 'ajax/customWordOp.php');
            xhr.send(formData);
        }

        function changewf(idcwf) {
            let wfel = document.getElementById('wf'+idcwf);
            let newval = wfel.value;
            alert('changewf ' + idcwf + '→' + newval);
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if (resp=='OK') {
                        alert('OK');
                        var tickel = document.getElementById('wf'+idcwf+'-tick');
                        tickel.classList.remove('changed'); //Remove the class (if required) and add again after a tiny delay, to restart the animation
                        setTimeout(function(){tickel.classList.add('changed');},50);
                    } else {
                        alert('$T_Error_in changewf: '+resp);
                    }
                }
            }
            var formData = new FormData();
            formData.append('id',idcwf);
            formData.append('operation','changewf');
            formData.append('newval',newval);
            xhr.open('POST', 'ajax/customWordOp.php');
            xhr.send(formData);
        }
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
