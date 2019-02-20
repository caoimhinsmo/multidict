<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  try {
      $myCLIL = SM_myCLIL::singleton();
      if (!$myCLIL->cead('{logged-in}')) { $myCLIL->diultadh(''); }
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }

  $clilstoreUrl = ( $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://' . $_SERVER['SERVER_NAME'] . '/clilstore/';

  echo <<<EOD_BARR
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change user options on Clilstore</title>
    <link rel="stylesheet" href="/css/smo.css" type="text/css">
    <link rel="stylesheet" href="style.css?version=2014-04-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style type="text/css">
        div.message { margin:0.5em 0; font-weight:bold; }
        fieldset.opts { border:2px solid #61abec; border-radius:6px; padding:6px; margin-bottom:2em; }
        fieldset.opts legend { border:2px solid #61abec; border-radius:4px; padding:1px 4px; background-color:#eef; color:#55a8eb; font-weight:bold; }
        span.info { color:green; font-size:70%; }
        span.callcount { font-size:75%; color:grey; }
        table#opttab tr td:first-child { text-align:right; }
        table#transferTable { border-collapse:collapse; }
        table#transferTable td { padding:5px; }

        table#vocab { border-collapse:collapse; border:1px solid grey; }
        table#vocab tr#vocabhead { background-color:grey; color:white; font-weight:bold; }
        table#vocab tr:nth-child(odd)  { background-color:#ddf; }
        table#vocab tr:nth-child(even) { background-color:#fff; }
        table#vocab tr:nth-child(odd):hover  { background-color:#fe6; }
        table#vocab tr:nth-child(even):hover { background-color:#fe6; }
        table#vocab td.meaning { min-width:40em; }
        table#vocab td { padding:1px 3px; }
        table#vocab tr + tr > td { border-left:1px solid #aaa; }

        a.langbutton { margin:1px 7px; background-color:#55a8eb; color:white; font-weight:bold; padding:2px 8px; border:1px solid white; border-radius:8px; }
        a.langbutton.selected { border-color:#55a8eb; background-color:yellow; color:#55a8eb; }
        a.langbutton.live:hover { background-color:blue; }
    </style>
    <script>
        function deleteVocabWord(user,sl,word) {
//            alert('In deleteWord: user=' + user + ', sl=' + sl + ' word=' + word);
            var url = '$clilstoreUrl/ajax/deleteVocabWord.php?user=' + user + '&sl=' + sl + '&word=' + word;
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open('GET', url, false);
            xmlhttp.send();
            var resp = xmlhttp.responseText;
            if (resp!='OK') { alert('Error in sguabFbhoD: ' + resp); }
            location.reload();
        }
        function changeMeaning(user,sl,word,meaning) {
//            alert(user+' '+sl+' '+word+' '+meaning);
            var url = '$clilstoreUrl/ajax/changeMeaning.php?user=' + user + '&sl=' + sl + '&word=' + word + '&meaning=' + encodeURI(meaning);
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open('GET', url, false);
            xmlhttp.send();
            var resp = xmlhttp.responseText;
            if (resp!='OK') { alert('Error in sguabFbhoD: ' + resp); }
            location.reload();
        }
    </script>
</head>
<body>

<ul class="smo-navlist">
<li><a href="./">Clilstore</a></li>
</ul>
<div class="smo-body-indent">
EOD_BARR;

  try {
    $myCLIL->dearbhaich();
    $loggedinUser = $myCLIL->id;

    $user = @$_REQUEST['user'] ?:null;
    $userSC = htmlspecialchars($user);
    if (empty($user)) { throw new SM_MDexception('Parameter ‘user=’ is missing'); }
//    if ($loggedinUser<>$user && $loggedinUser<>'admin') { throw new SM_MDexception('sgrios|bog|Attempt to change another user’s options<br>'
//                                 . "You are logged in as $loggedinUser and have attempted to change the options for $userSC"); }

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $errorMessage = $successMessage = $vocabHtml = '';

    $stmt = $DbMultidict->prepare('SELECT sl, SUM(calls) AS slSum FROM csVocab WHERE user=:user GROUP BY sl ORDER BY slSum DESC, sl');
    $stmt->execute([':user'=>$user]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $slLorg = $_GET['sl'] ?: $rows[0]['sl'];
    foreach ($rows as $row) {
        extract($row);
        if ($sl==$slLorg) { $class = 'langbutton selected'; }
         else             { $class = 'langbutton live';     }
        $langButArray[] = "<a href=vocab.php?user=$userSC&amp;sl=$sl class='$class'>$sl</a>";
    }
    $langButHtml = implode(' ',$langButArray);
    $langButHtml = "<p>$langButHtml</p>";

    $stmt = $DbMultidict->prepare('SELECT word,calls,meaning FROM csVocab WHERE user=:user AND sl=:sl');
    $stmt->execute([':user'=>$user,':sl'=>$slLorg]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        extract($row);
        $queryVU = 'SELECT unit, calls AS callsVU, title FROM csVocabUnit, clilstore WHERE user=:user AND clilstore.sl=:sl AND word=:word AND unit=id ORDER BY unit';
        $stmtVU = $DbMultidict->prepare($queryVU);
        $stmtVU->execute([':user'=>$user,':sl'=>$slLorg,':word'=>$word]);
        $rowsVU = $stmtVU->fetchAll(PDO::FETCH_ASSOC);
        $unitHtmlArr = [];
        foreach ($rowsVU as $rowVU) {
            extract($rowVU);
            $title = addslashes($title);
            $callsVUhtml = ( $callsVU==1 ? '' : "<span class=callcount>×$callsVU</span>" );
            $unitHtmlArr[] = "<a href='/cs/$unit' title='$title'>$unit</a>$callsVUhtml";
        }
        $unitsHtml = implode(' ',$unitHtmlArr);
        $wordEnc = urlencode($word);
        $deleteVocabWordHtml = "<img src='/icons-smo/curAs.png' alt='Delete' style='padding:0 0.5em' title='Delete instantaneously' onclick=\"deleteVocabWord('$user','$slLorg','$wordEnc')\">";
        $meaningSC  = htmlspecialchars($meaning);
        $meaningHtml = "<input value='$meaningSC' style='width:95%' onchange=\"changeMeaning('$user','$slLorg','$wordEnc',this.value);\">";
        $vocabHtml .= "<tr><td>$deleteVocabWordHtml</td><td>$word</td><td class=meaning>$meaningHtml</td><td>$unitsHtml</td></tr>\n";
    }

    $stmt = $DbMultidict->prepare('SELECT id,owner,title FROM clilstore WHERE offer=:offer AND offerTime<>0 ORDER BY id');
    $stmt->execute(array(':offer'=>$user));
    $transfers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $ntransfers = sizeof($transfers);

    $stmt2 = $DbMultidict->prepare('SELECT unitLang,highlightRow FROM users WHERE user=:user');
    $stmt2->execute(array('user'=>$user));
    if (!($row = $stmt2->fetch(PDO::FETCH_OBJ))) { throw new SM_MDexception("Failed to fetch information on user $userSC"); }
    $unitLang     = $row->unitLang;
    $highlightRow = $row->highlightRow;

    function optionsHtml($valueOptArr,$selectedValue) {
     //Creates the options html for a select in a form, based on value=>text array and the value to be selected
        $htmlArr = array();
        foreach ($valueOptArr as $value=>$option) {
            $selected = ( $value==$selectedValue ? ' selected' : '' );
            $htmlArr[] = "<option value='$value'$selected>$option</option>\n";
        }
        return implode("\r",$htmlArr);
    }

    $langArr[''] = '';
    $stmt3 = $DbMultidict->prepare("SELECT id,endonym FROM lang WHERE id<>'¤' AND id<>'x' ORDER BY endonym,id");
    $stmt3->execute();
    $stmt3->bindColumn(1,$id);
    $stmt3->bindColumn(2,$endonym);
    while ($stmt3->fetch()) { $langArr[$id] = "$endonym ($id)"; }
    $unitLangHtml = optionsHtml($langArr,$unitLang);

    $highlightRowArr = array(
        '-1' => 'Never',
         '0' => 'Only in “Author page - more options”',
         '1' => 'Always'); 
    $highlightRowHtml = optionsHtml($highlightRowArr,$highlightRow);

    $errorMessage   = ( empty($errorMessage)   ? '' : '<div class="message" style="color:red">' . $errorMessage   . '<br>No changes saved</div>' );
    $successMessage = ( empty($successMessage) ? '' : '<div class="message" style="color:green"><span style="font-size:200%">✔</span> ' . $successMessage . '</div>' );

    echo <<<ENDform
<h1 class="smo">Vocabulary list for user <span style="color:brown">$user</span></h1>

$errorMessage
$successMessage

<p style="DISPLAY:NONE;margin:1.7em 0"><a class="button" href="changePassword.php?user=$user">Change password…</a>

<form method="POST" style="DISPLAY:NONE">
<fieldset class="opts">
<legend>Options</legend>
<table id="opttab">
<tr><td>Default language code for units you create:</td><td>
<select name="unitLang">
$unitLangHtml
</select>
</td></tr>
<tr><td>Highlight row of Clilstore index on mouse hover?</td><td>
<select name="highlightRow">
$highlightRowHtml
</select>
</td></tr>
<tr><td><input type ="hidden" name="user" value="$userSC">
</td><td><input type="submit" name="save" value="Save" style="font-size:120%"></td></tr>
</table>
</fieldset>
</form>

$langButHtml
<table id=vocab>
<tr id=vocabhead><td></td><td>Word</td><td>Meaning</td><td>Clicked in units</td></tr>
$vocabHtml
</table>
ENDform;

  } catch (Exception $e) { echo $e; }

  echo <<<EOD_BONN
</div>
<ul class="smo-navlist">
<li><a href="./">Clilstore</a></li>
</ul>

</body>
</html>
EOD_BONN;
?>
