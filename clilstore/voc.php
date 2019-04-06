<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Pragma: no-cache");

  try {
      $myCLIL = SM_myCLIL::singleton();
      if (!$myCLIL->cead('{logged-in}')) { $myCLIL->diultadh(''); }
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }

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
        span.callcount { font-size:75%; color:grey; }
        table#vocab { border-collapse:collapse; border:1px solid grey; margin-bottom:0.5em; }
        table#vocab tr#vocabhead { background-color:grey; color:white; font-weight:bold; }
        table#vocab tr:nth-child(odd)  { background-color:#ddf; }
        table#vocab tr:nth-child(even) { background-color:#fff; }
        table#vocab tr:nth-child(odd):hover  { background-color:#fe6; }
        table#vocab tr:nth-child(even):hover { background-color:#fe6; }
        table#vocab td.vocword a:hover { color:white; background-color:black; }
        table#vocab td.meaning { min-width:40em; }
        table#vocab td { padding:1px 3px; }
        table#vocab tr + tr > td { border-left:1px solid #aaa; }
        table#vocab tr.deleted { display:none; }
        a.langbutton { margin:1px 7px; background-color:#55a8eb; color:white; font-weight:bold; padding:2px 8px; border:1px solid white; border-radius:8px; }
        a.langbutton.selected { border-color:#55a8eb; background-color:yellow; color:#55a8eb; }
        a.langbutton.live:hover { background-color:blue; }
        a#emptyBut { border:0; padding:1px 3px; border-radius:6px; background-color:#27b; color:white; text-decoration:none; }
        a#emptyBut:hover,
        a#emptyBut:active,
        a#emptyBut:focus  { background-color:#f00; color:white; }
    </style>
    <script>
        function deleteVocWord(vocid) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onload = function() {
                if (this.status!=200) { alert('Error in deleteVocWord:'+this.status); return; }
                var el = document.getElementById('row'+vocid);
                el.classList.add('deleted');
            }
            xmlhttp.open('GET', 'ajax/deleteVocWord.php?vocid=' + vocid);
            xmlhttp.send();
        }
        function changeMeaning(vocid,meaning) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onload = function() { if (this.status!=200) { alert('Error in changeMeaning:'+this.status); } } 
            xmlhttp.open('GET', 'ajax/changeMeaning.php?vocid=' + vocid + '&meaning=' + encodeURI(meaning));
            xmlhttp.send();
        }
        function emptyVocList(user,sl) {
            var r = confirm("Do you really want to delete every word from this vocabulary list?");
            if (r==true) {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onload = function() { if (this.status!=200) { alert('Error in emptyVocList:'+this.status); return; } }
                xmlhttp.open('GET', 'ajax/emptyVocList.php?user='+user+'&sl=' +sl);
                xmlhttp.send();
                window.location.href = window.location.href;
            }
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

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $vocHtml = $langButHtml = $slLorg = '';

    $stmt = $DbMultidict->prepare('SELECT sl, SUM(calls) AS slSum FROM csVoc WHERE user=:user GROUP BY sl ORDER BY slSum DESC, sl');
    $stmt->execute([':user'=>$user]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($_GET['sl'])) { $slLorg = $_GET['sl']; }
    else if (!empty($rows))  { $slLorg = $rows[0]['sl']; }
    if (count($rows)>1) {
        foreach ($rows as $row) {
            extract($row);
            if ($sl==$slLorg) { $class = 'langbutton selected'; }
             else             { $class = 'langbutton live';     }
            $langButArray[] = "<a href=voc.php?user=$userSC&amp;sl=$sl class='$class'>$sl</a>";
        }
        $langButHtml = implode(' ',$langButArray);
        $langButHtml = "<p>$langButHtml</p>";
    }

    $stmt = $DbMultidict->prepare('SELECT vocid,word,calls,head,meaning FROM csVoc WHERE user=:user AND sl=:sl');
    $stmt->execute([':user'=>$user,':sl'=>$slLorg]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        extract($row);
        $queryVU = 'SELECT unit, calls AS callsVU, title FROM csVocUnit, clilstore WHERE vocid=:vocid AND clilstore.id=unit ORDER BY unit';
        $stmtVU = $DbMultidict->prepare($queryVU);
        $stmtVU->execute([':vocid'=>$vocid]);
        $rowsVU = $stmtVU->fetchAll(PDO::FETCH_ASSOC);
        $unitHtmlArr = [];
        foreach ($rowsVU as $rowVU) {
            extract($rowVU);
            $title = addslashes($title);
            $callsVUhtml = ( $callsVU==1 ? '' : "<span class=callcount>×$callsVU</span>" );
            $unitHtmlArr[] = "<a href='/cs/$unit' title='$title'>$unit</a>$callsVUhtml";
        }
        $unitsHtml = implode(' ',$unitHtmlArr);
        $deleteVocWordHtml = "<img src='/icons-smo/curAs.png' alt='Delete' style='padding:0 0.5em' title='Delete instantaneously' onclick=\"deleteVocWord('$vocid')\">";
        $meaningSC  = htmlspecialchars($meaning);
        $meaningHtml = "<input value='$meaningSC' style='width:95%' onchange=\"changeMeaning('$vocid',this.value);\">";
        $multidictHtml = "<a href='/multidict/?sl=$slLorg&amp;word=$word' target=vocmdtab><img src=/favicons/multidict.png alt=''></a>";
        $wordHtml      = "<a href='/multidict/?sl=$slLorg&amp;word=$word' target=vocmdtab>$word</a>";
        $vocHtml .= "<tr id=row$vocid><td>$deleteVocWordHtml</td><td class=vocword>$multidictHtml $wordHtml</td><td class=meaning>$meaningHtml</td><td>$unitsHtml</td></tr>\n";
    }

    function optionsHtml($valueOptArr,$selectedValue) {
     //Creates the options html for a select in a form, based on value=>text array and the value to be selected
        $htmlArr = array();
        foreach ($valueOptArr as $value=>$option) {
            $selected = ( $value==$selectedValue ? ' selected' : '' );
            $htmlArr[] = "<option value='$value'$selected>$option</option>\n";
        }
        return implode("\r",$htmlArr);
    }

    if (!empty($vocHtml)) { $vocTableHtml = <<<EOD_vocTable
<table id=vocab>
<tr id=vocabhead><td></td><td>Word</td><td>Meaning</td><td>Clicked in units</td></tr>
$vocHtml
</table>

<p style="margin:3.5em 0 0 0;font-size:85%">If you you really want to, you can <a id=emptyBut onclick="emptyVocList('$user','$slLorg')">empty</a> the list to delete every single word in this vocabulary list.</p>
EOD_vocTable;
    } else { $vocTableHtml = <<<EOD_noVocTable
<p>There are no words in the vocabulary list</p>

<p>You can add words to your vocabulary list by clicking on them in a Clilstore unit (with <i>Vocabulary record</i> switched on).</p>
EOD_noVocTable;
    }

    $slLorgShow = ( $slLorg=='' ? 'any' : $slLorg );
    echo <<<EOD
<h1 style="font-size:160%;margin:0;padding-top:0.5em">Vocabulary list for user <span style="color:brown">$user</span></h1>
<p style="font-size:85%;color:grey;margin:0 0 1em 0">Language: $slLorgShow</p>

$langButHtml

$vocTableHtml
EOD;

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
