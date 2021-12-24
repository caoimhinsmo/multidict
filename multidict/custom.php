<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {
    $T = new SM_T('multidict/custom');
    $T_No_words_found = $T->h('Cha_d_fhuaireadh_facal');
    $T_Deasaich = $T->h('Deasaich');
    $T_Error_in = $T->h('Error_in');

    $sl   = $_REQUEST['sl']   ?? '';
    $tl   = $_REQUEST['tl']   ?? '';
    $word = $_REQUEST['word'] ?? '';  $wordLIKE = strtr($word,'*?','%_');
    if (empty($sl))   { throw new SM_Exception('Missing parameter: ‘sl’'); }
    if (empty($tl))   { throw new SM_Exception('Missing parameter: ‘tl’'); }
    if (empty($word)) { throw new SM_Exception('Missing parameter: ‘word’'); }

    $myCLIL = SM_myCLIL::singleton();
    $user = ( isset($myCLIL->id) ? $myCLIL->id : '' );

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $grp = "cw-$sl";
    $stmtPermission = $DbMultidict->prepare('SELECT 1 FROM userGrp WHERE user=:user AND grp=:grp');
    $stmtPermission->execute([':user'=>$user,':grp'=>$grp]);
    if ($stmtPermission->fetch()) { $editor = TRUE; } else { $editor = FALSE; }

    $editHtml = $resHTML = '';
    if ($editor) {
        $editHtml = " <img src='/icons-smo/peann.png'>";
        $editHtml = "<a href='customEdit.php?idc={idc}' title='$T_Deasaich' style='margin-left:2em'>$editHtml</a>";
    }

    $stmtSELc = $DbMultidict->prepare('SELECT idc, word, disambig, gram FROM custom'
                                    . ' WHERE sl=:sl AND word LIKE :word'
                                    . ' ORDER BY pri, word, disambig');
    $stmtSELc->execute([':sl'=>$sl,':word'=>$wordLIKE]);
    $resc = $stmtSELc->fetchAll(PDO::FETCH_ASSOC);
    $stmtSELcwf = $DbMultidict->prepare('SELECT custom.idc, custom.word, disambig, gram FROM custom, customwf'
                                      . ' WHERE sl=:sl AND wf LIKE :word AND customwf.idc=custom.idc'
                                      . ' ORDER BY customwf.pri, custom.pri, word, disambig');
    $stmtSELcwf->execute([':sl'=>$sl,':word'=>$wordLIKE]);
    $rescwf = $stmtSELcwf->fetchAll(PDO::FETCH_ASSOC);
    $res = [];
    foreach ($resc   as $r) { $idc = $r['idc']; $res[$idc] = $res[$idc] ?? $r; }
    foreach ($rescwf as $r) { $idc = $r['idc']; $res[$idc] = $res[$idc] ?? $r; }
    $stmtSELctr = $DbMultidict->prepare('SELECT meaning FROM customtr WHERE idc=:idc');
    foreach ($res as $r) {
        extract($r);
        $stmtSELctr->execute([':idc'=>$idc]);
        $rm = $stmtSELctr->fetch(PDO::FETCH_ASSOC);
        if (!$rm) { throw new SM_Exception("Word $idc has no translation record"); }
        extract($rm);
      if ($meaning=='' && !$editor) { continue; } //Editors get to see blank meanings and correct them
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
        if (!empty($gram)) { $gram = " <span class=gram>$gram</span>"; }
        $editHtml2 = str_replace('{idc}',$idc,$editHtml);
        $resHTML .= <<<resHTML
<p class=word title="$disambig"><b>$word</b>$gram$editHtml2</p>
<p class=meaning>$meaning</p>
resHTML;
    }
    if (empty($resHTML)) {
        $resHTML = "<p>$T_No_words_found</p>\n";
        if ($editor) {
            $wordSC = htmlspecialchars($word);
            $wordQ = '“'.$wordSC.'”';
            $resHTML .= <<<END_notFoundHTML
<div style="font-size:80%">
<p style="margin-top:1.5em;border-top:1px solid"><b>Editors…</b><br>
$wordQ is not yet in the custom dictionary. Neither as a headword nor as a wordform.<p>

<p>If it is a variant of a headword which is (or ought to be) in the dictionary, you should look up the relevant headword (adding it to the dictionary if necessary) and edit it to add $wordQ to it as a variant wordform.</p>

<p>Otherwise you can, if appropriate, add $wordQ now as a headword to the dictionary by supplying a meaning for it here:</p>
</div>
<form id=newword method=POST onsubmit="insertWord(this); return false;">
<input type=hidden name=word value="$wordSC">
<input type=hidden name=disambig value="">
<input type=hidden name=sl value="$sl"> 
<input type=hidden name=tl value="$tl">
<table>
<tr><td>word ($sl)</td><td style="padding-left:0.5em"><span id=hgword>$word</span></td></tr>
<tr><td>meaning ($tl)</td><td><input name="meaning" required style="width:22em"></td></tr>
<tr><td></td><td><input type=submit value="Add to dictionary"></td></tr>
</table>
</form>
END_notFoundHTML;
        }
    }

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
        span.gram { padding-left:0.3em; font-size:70%; color:red; }
        form#newword table { border-collapse:collapse; border:1px solid; margin:0.2em 0.4em; }
        form#newword table tr td { padding:0.2em; }
        form#newword table tr:last-child td { padding-bottom:0.6em; }
        form#newword table tr td:nth-child(1) { text-align:right; color:#444; font-size:85%; }
    </style>
    <script>
        function insertWord(formEl) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if (resp.substring(0,3)=='OK:') {
                        let idc = resp.substring(3);
                        location.href = location.origin + '/multidict/customEdit.php?idc=' + idc + '&newword';
                    } else {
                        alert('$T_Error_in insertHomograph:'+resp);
                    }
                }
            }
            var formData = new FormData(formEl);
            formData.append('table','custom');
            formData.append('operation','insert');
            xhr.open('POST', 'ajax/customWordOp.php');
            xhr.send(formData);
        }
    </script>
</head>
<body>
<div class="smo-body-indent" style="max-width:80em">

$resHTML

</body>
</html>
END_HTML;

  } catch (Exception $e) { echo $e; }
    
?>
