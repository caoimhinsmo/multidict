<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {
    $T = new SM_T('multidict/custom');
    $T_No_words_found = $T->h('No_words_found');
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
<p style="margin:0.1em 0 0 9em;font-size:70%">Editing word $idc — Language $sl</p>
<p style="margin:0.5em 0 0.5em 0;font-weight:bold;font-size:120%"><span id=wordTitle>$word</span>
<img src="/icons-smo/curAs2.png" alt="DELETE" title="Delete the word" style="padding-left:1em" onclick="deleteCustom('$idc')"></p>

<table id=formtab>
<tr><td>word</td><td><input id=word value="$wordSC" onfocus="this.oldvalue=this.value" onchange="changeCustom('$idc','word')" style="width:24em">
        <span id="word-tick" class=change>✔</span></td></tr>
<tr><td>disambiguator</td><td><input id=disambig onfocus="this.oldvalue=this.value"  onchange="changeCustom('$idc','disambig')" value="$disambigSC" style="width:8em">
        <span id="disambig-tick" class=change>✔</span></td></tr>
<tr><td>grammar</td><td><input id=gram value="$gramSC" onchange="changeCustom('$idc','gram')" style="width:8em">
        <span id="gram-tick" class=change>✔</span></td></tr>
<tr><td>priority</td><td><input id=pri value="$pri" onchange="changeCustom('$idc','pri')" style="width:8em" type=number min=0 max=100>
        <span id="pri-tick" class=change>✔</span></td></tr>
</table>
END_cHTML;

    $cwfHTML = $cwfTableHead = '';
    $stmtCwf = $DbMultidict->prepare('SELECT idcwf,wf,pri,priWhy FROM customwf WHERE idc=:idc ORDER BY pri,wf');
    $stmtCwf->execute([':idc'=>$idc]);
    $res = $stmtCwf->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($res)) $cwfTableHead = '<tr><td>wordform</td><td>priority</td><td>reason</td><td></td><td></td></tr>';
    foreach ($res as $r) {
        extract($r);
        $wfSC     = htmlspecialchars($wf);
        $priWhySC = htmlspecialchars($priWhy);
        $cwfHTML .= <<<END_cwfHTML
<tr>
<td><input id=wf$idcwf-wf value='$wfSC' onchange="changeCustomwf('$idcwf','wf')"></td>
<td><input id=wf$idcwf-pri value='$pri' onchange="changeCustomwf('$idcwf','pri')" type=number min=1 max=100 step=1></td>
<td><input id=wf$idcwf-priWhy value='$priWhySC' onchange="changeCustomwf('$idcwf','priWhy')"></td></td>
<td><span id="wf$idcwf-tick" class=change>✔</span></td>
<td onclick="deleteCustomwf('$idcwf')">❌</td>
</tr>
END_cwfHTML;
    }
    $cwfHTML = <<<END_cwfHTML2
<p style="margin-bottom:0"><b>Wordforms</b> <span style="font-style:italic;color:#444;font-size:85%">(search-keys)</span>
<table id=wftable>
$cwfTableHead
$cwfHTML
<tr><td><input id=insertCustomwf onchange="insertCustomwf('$idc')"></td><td colspan=4 style="text-align:left;color:#444;font-size:85%">add wordform</td></tr>
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
        $meaningSC = htmlspecialchars($meaning);
        $ctrHTML .= <<<END_ctrHTML
<tr>
<td>$tl</td>
<td><input id=tr$idctr value='$meaningSC' style="width:25em" onchange="changeCustomtr('$idctr')"></td>
<td><span id="tr$idctr-tick" class=change>✔</span></td>
</tr>
END_ctrHTML;
    }
    $ctrHTML = <<<END_ctrHTML2
<p style="margin-bottom:0"><b>Translation</b></p>
<table id=trtable>
$ctrHTML
</table>
END_ctrHTML2;

    if (isset($_REQUEST['newword']))
      { $newwordMessage = '<p id=message>New word added <span style="color:green">✓</span> &nbsp; You can now edit it further if you wish</p>'; }

    echo <<<END_HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wordlist Dictionary - edit word $idc</title>
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="icon" type="image/png" href="/favicons/multidict.png">
    <style>
        p#message { margin:0; padding:0.1em 0.3em; background-color:black; color:white; }

        p.word { margin:0.2em 0; }
        p.meaning { margin:0 0 1em 1em; }
        span.gram { padding-left:0.3em; font-size:70%; color:red; }

        table#formtab { margin-left:0.4em; }
        table#formtab td:nth-child(1) { text-align:right; color:#444; font-size:85%; }

        div#homograph table { border-collapse:collapse; border:1px solid; margin:0.2em 0.4em; }
        div#homograph table tr td { padding:0.2em; }
        div#homograph table tr:last-child td { padding-bottom:0.6em; }
        div#homograph table tr td:nth-child(1) { text-align:right; color:#444; font-size:85%; }

        table#trtable { border-collapse:collapse; margin-left:0.8em; }
        table#trtable td:nth-child(1) { padding-right:0.3em; color:#444; font-size:85%; }

        table#wftable { border-collapse:collapse; margin-left:0.8em; }
        table#wftable tr:nth-child(1) td { padding:0 0.2em; color:#444; font-size:85%; }
        table#wftable td:nth-child(2) { text-align:right; }
        table#wftable td:nth-child(1) input { width:14em; }
        table#wftable tr:last-child td { padding-top:0.5em; }
        table#wftable td:nth-child(2) input { width:5em; text-align:right; }
        table#wftable td:nth-child(3) input { width:6em; }
        table#wftable td:nth-child(5) { font-size:60%; }
        table#wftable td:nth-child(5):hover { background-color:pink; }

        div#homograph p { margin-bottom:0.2em; }
        div#homograph.closed form { display:none; }
        div#homograph.open   form { display:block; }
        div#homograph.closed span#hgbutton { background-color:#73c8fb; color:white; padding:0.1em 0.2em; border-radius:0.2em; }
        div#homograph.open   span#hgbutton { margin-bottom:0; font-weight:bold; } 
        div#homograph.closed span#hgbutton:hover { background-color:blue; }
        div#homograph.closed span#hgdots { display:inline; }
        div#homograph.open   span#hgdots { display:none; }
    </style>
    <script>
        function deleteCustom(idc) {
            if (!confirm('Really delete this word?\\r\\n (together with any wordforms and translations)')) { return; }
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if (resp=='OK') {
                        let bodyEl = document.querySelector('body');
                        bodyEl.innerHTML = '<p>The word has been deleted</p>';
                    } else {
                        alert('$T_Error_in deleteCustom: '+resp);
                    }
                }
            }
            var formData = new FormData();
            formData.append('table','custom');
            formData.append('id',idc);
            formData.append('operation','delete');
            xhr.open('POST', 'ajax/customWordOp.php');
            xhr.send(formData);
        }

        function deleteCustomwf(idcwf) {
            if (!confirm('Really delete this wordform?')) { return; }
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if (resp=='OK') {
                        window.location.reload(true);
                    } else {
                        alert('$T_Error_in deleteCustomwf: '+resp);
                    }
                }
            }
            var formData = new FormData();
            formData.append('table','customwf');
            formData.append('id',idcwf);
            formData.append('operation','delete');
            xhr.open('POST', 'ajax/customWordOp.php');
            xhr.send(formData);
        }

        function changeCustom(idc,field) {
            let el = document.getElementById(field);
            let newval = el.value.trim();
            if (field=='word' && newval=='') { alert('Change refused. You cannot delete a word by setting it to blank.'); return; }
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if (resp=='OK') {
                        var tickel = document.getElementById(field + '-tick');
                        tickel.classList.remove('changed'); //Remove the class (if required) and add again after a tiny delay, to restart the animation
                        setTimeout(function(){tickel.classList.add('changed');},50);
                        if (field=='word') {
                            document.getElementById('wordTitle').innerHTML = newval;
                            document.getElementById('hgword').innerHTML = newval;
                        }
                    } else {
                        alert('$T_Error_in changeCustomwf: '+resp);
                        el.value = el.oldvalue;
                    }
                }
            }
            var formData = new FormData();
            formData.append('table','custom');
            formData.append('id',idc);
            formData.append('operation','change');
            formData.append('field',field);
            formData.append('newval',newval);
            xhr.open('POST', 'ajax/customWordOp.php');
            xhr.send(formData);
        }

        function changeCustomtr(idctr) {
            let trel = document.getElementById('tr'+idctr);
            let newval = trel.value;
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if (resp=='OK') {
                        var tickel = document.getElementById('tr'+idctr+'-tick');
                        tickel.classList.remove('changed'); //Remove the class (if required) and add again after a tiny delay, to restart the animation
                        setTimeout(function(){tickel.classList.add('changed');},50);
                    } else {
                        alert('$T_Error_in changeCustomtr: '+resp);
                    }
                }
            }
            var formData = new FormData();
            formData.append('table','customtr');
            formData.append('id',idctr);
            formData.append('operation','change');
            formData.append('newval',newval);
            xhr.open('POST', 'ajax/customWordOp.php');
            xhr.send(formData);
        }

        function changeCustomwf(idcwf,field) {
            let wfel = document.getElementById('wf'+idcwf+'-'+field);
            let newval = wfel.value.trim();
            if (newval=='') { alert('Change refused. You cannot delete a wordform by setting it to blank.'); return; }
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if (resp=='OK') {
                        var tickel = document.getElementById('wf'+idcwf+'-tick');
                        tickel.classList.remove('changed'); //Remove the class (if required) and add again after a tiny delay, to restart the animation
                        setTimeout(function(){tickel.classList.add('changed');},50);
                    } else {
                        alert('$T_Error_in changeCustomwf: '+resp);
                    }
                }
            }
            var formData = new FormData();
            formData.append('table','customwf');
            formData.append('id',idcwf);
            formData.append('operation','change');
            formData.append('field',field);
            formData.append('newval',newval);
            xhr.open('POST', 'ajax/customWordOp.php');
            xhr.send(formData);
        }

        function insertCustomwf(idc) {
            let newval = document.getElementById('insertCustomwf').value.trim();
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if (resp=='OK') {
                        window.location.reload(true);
                    } else {
                        alert('$T_Error_in insertCustomwf: '+resp);
                    }
                }
            }
            var formData = new FormData();
            formData.append('table','customwf');
            formData.append('id',idc);
            formData.append('operation','insert');
            formData.append('newval',newval);
            xhr.open('POST', 'ajax/customWordOp.php');
            xhr.send(formData);
        }

        function checkDisambig(idc,newval) {
         // Checks whether it would be ok to give word idc the disambiguator newval.
         // Returns false iff a word, other than idc itself, with the same spelling already has that disambiguator.
            alert('idc='+idc+', newval='+newval);
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
alert('readystatechange: this.status='+this.status+', this.readyState='+this.readyState);
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if (resp=='OK') { return true; } else { return false; }
                }
            }
            var formData = new FormData();
            formData.append('table','custom');
            formData.append('id',idc);
            formData.append('operation','checkDisambig');
            formData.append('newval',newval);
alert('gus xhr.open');
            xhr.open('POST', 'ajax/customWordOp.php');
alert('gus xhr.send');
            xhr.send(formData);
        }

        function insertHomograph(formEl) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if (resp.substring(0,3)=='OK:') {
                        let idc = resp.substring(3);
                        location.href = location.origin + location.pathname + '?idc=' + idc + '&newword';
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
<body id=body>
<div class="smo-body-indent" style="max-width:80em">

$newwordMessage
$cHTML
$ctrHTML
$cwfHTML

<div id=homograph class=closed onclick="this.className='open'">
<p><span id=hgbutton title="Add another word with the same spelling but a different meaning">Add a homograph<span id=hgdots>...</span></span></p>
<form method=POST onsubmit="insertHomograph(this); return false;">
<input type=hidden name=word value="$wordSC">
<input type=hidden name=sl value="$sl"> 
<input type=hidden name=tl value="$tl">
<table>
<tr><td>word ($sl)</td><td style="padding-left:0.5em"><span id=hgword>$word</span></td></tr>
<tr><td>disambiguator</td><td><input id=hgdisambig name="disambig" required style="width:8em"></td></tr>
<tr><td>meaning ($tl)</td><td><input name="meaning" required style="width:22em"></td></tr>
<tr><td></td><td><input type=submit value="Add to dictionary"></td></tr>
</table>
</form>
</div>

</body>
</html>
END_HTML;

  } catch (Exception $e) { echo $e; }
    
?>
