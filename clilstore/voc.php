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

  $T = new SM_T('clilstore/voc');

  $T_Language   = $T->h('Language');
  $T_Word       = $T->h('Facal');
  $T_words      = $T->h('facail');
  $T_Meaning    = $T->h('Meaning');
  $T_Error_in   = $T->h('Error_in');
  $T_Hide_all   = $T->h('Hide_all');
  $T_Reveal_all = $T->h('Reveal_all');
  $T_Reveal     = $T->h('Reveal');

  $T_Vocabulary_list_for_user_ = $T->h('Vocabulary_list_for_user_');
  $T_Clicked_in_unit           = $T->h('Clicked_in_unit');
  $T_Delete_instantaneously    = $T->h('Delete_instantaneously');
  $T_Lookup_with_Multidict     = $T->h('Lorg le Multidict');
  $T_No_words_in_voc_list      = $T->h('No_words_in_voc_list');
  $T_No_words_in_voc_list_for_ = $T->h('No_words_in_voc_list_for_');
  $T_No_words_in_voc_list_info = $T->h('No_words_in_voc_list_info');
  $T_Sort_the_column           = $T->h('Sort_the_column');
  $T_Empty_voc_list_question   = $T->h('Empty_voc_list_question');
  $T_Export_to_csv             = $T->h('Export_to_csv');
  $T_Export_to_tsv             = $T->h('Export_to_tsv');
  $T_Write_meaning_here        = $T->h('Write_meaning_here');
  $T_Test_yourself             = $T->h('Test_yourself');
  $T_Empty_voc_list_confirm    = $T->j('Empty_voc_list_confirm');

  $T_No_words_in_voc_list_info = strtr ( $T_No_words_in_voc_list_info, [ '{'=>'<i>', '}'=>'</i>' ] );

  $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

  try {
    $myCLIL->dearbhaich();
    $loggedinUser = $myCLIL->id;
    $user = $_REQUEST['user'] ?? $loggedinUser;
    $userSC = htmlspecialchars($user);

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $vocHtml = $langButHtml = '';

    $stmt = $DbMultidict->prepare('SELECT sl, COUNT(1) AS cnt, endonym FROM csVoc,lang WHERE user=:user AND csVoc.sl=lang.id GROUP BY sl ORDER BY cnt DESC, sl');
    $stmt->execute([':user'=>$user]);
    $vocLangs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($vocLangs)) {
        $langButHtml = '';
        $vocTableHtml = <<<END_noVocTable1
<p>$T_No_words_in_voc_list</p>
<p>$T_No_words_in_voc_list_info</p>
END_noVocTable1;
    } else {
        $slLorg  = $_GET['sl'] ?? $vocLangs[0]['sl'];
        foreach ($vocLangs as $vocLang) {
            extract($vocLang);
            if ($sl==$slLorg) {
                $slLorgEndonym = $endonym;
                $class = 'langbutton selected';
            } else {
               $class = 'langbutton live';
            }
            $langButArray[$sl] = "<a href='voc.php?user=$userSC&amp;sl=$sl' title='$endonym ($cnt $T_words)' class='$class'>$sl</a>";
        }
        $langButHtml = implode(' ',$langButArray);
        $langButHtml = "<p style='margin:0.3em 0'>$langButHtml</p>";
        if (empty($langButArray[$slLorg])) {
            $vocTableHtml = <<<END_noVocTable2
<p>$T_No_words_in_voc_list_for_ &lsquo;$slLorg&rsquo;.</p>
<p>$T_No_words_in_voc_list_info</p>
END_noVocTable2;
        } else {
            $vocidClickOrder   = 'vocid';
            $wordClickOrder    = 'word';
            $meaningClickOrder = 'meaning';
            $order = $_REQUEST['order'] ?? 'vocidDESC';
            if ($order=='vocid') {
                $orderCondition  = 'vocid';
                $vocidClickOrder = 'vocidDESC';
            } elseif ($order=='vocidDESC') {
                $orderCondition = 'vocid DESC';
                $vocidClickOrder = 'vocid';
            } elseif ($order=='word') {
                $orderCondition = 'word';
                $wordClickOrder  = 'wordDESC';
            } elseif ($order=='wordDESC') {
                $orderCondition = 'word DESC';
                $wordClickOrder = 'word';
            } elseif ($order=='meaning') {
                $orderCondition = 'meaning, word';
                $meaningClickOrder  = 'meaningDESC';
            } elseif ($order=='meaningDESC') {
                $orderCondition = 'meaning DESC, word';
                $meaningClickOrder = 'meaning';
            } else {
                $orderCondition = 'vocid';
            }
            $stmt = $DbMultidict->prepare("SELECT vocid,word,calls,head,meaning FROM csVoc WHERE user=:user AND sl=:sl ORDER BY $orderCondition");
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
                $meaningSC  = htmlspecialchars($meaning);
                $vocHtml .= <<<END_vocHtml
<tr id=row$vocid>
<td><img src='/icons-smo/curAs.png' alt='Delete' title='$T_Delete_instantaneously' onclick="deleteVocWord('$vocid')"></td>
<td title='$T_Lookup_with_Multidict'><a href='/multidict/?sl=$slLorg&amp;word=$word' target=vocmdtab><img src=/favicons/multidict.png alt=''> $word</a></td>
<td class=meaning><a class=reveal href="javascript:;" onclick="reveal(this)">$T_Reveal</a>
    <input value='$meaningSC' style='min-width:45em;max-width:55em' onchange="changeMeaning('$vocid',this.value);" title="$T_Write_meaning_here"><span id="$vocid-tick" class=change>✔<span></td>
<td>$unitsHtml</td>
</tr>
END_vocHtml;
            }
            $T_Export_to_csv = strtr ( $T_Export_to_csv,
                                      [ '{' => '<input type=submit value="',
                                        '}' => '" style="padding:0px 8px">',
                                        '|' => '<input name=separator required value="|" maxlength=1 style="width:1em;text-align:center">'
                                      ] );
            $T_Export_to_tsv = strtr ( $T_Export_to_tsv,
                                      [ '{' => '<input type=submit value="',
                                        '}' => '" style="padding:0px 8px">'
                                      ] );
            $T_Empty_voc_list_question = strtr ( $T_Empty_voc_list_question,
                                                [ '{'    => "<a id=emptyBut onclick=\"emptyVocList('$user','$slLorg')\">",
                                                  '}'    => '</a>',
                                                  '[sl]' => "$slLorgEndonym"
                                                ] );
            $exportHtml = <<<END_exportHtml
<div id=export>
<p><form action=vocExport.php>$T_Export_to_csv
<input type=hidden name=user value='$userSC'>
<input type=hidden name=sl value='$slLorg'>
<i>(UTF-8 encoding)</i>
</form></p>
<p><form action=vocExport.php>$T_Export_to_tsv
<input type=hidden name=user value='$userSC'>
<input type=hidden name=sl value='$slLorg'>
<i>(UTF-8 encoding)</i>
</form></p>
<p>$T_Empty_voc_list_question</p>
</div>
END_exportHtml;
            $vocTableHtml = <<<END_vocTable
<table id=vocab>
<tr id=vocabhead>
<td><a href="./voc.php?user=$user&amp;sl=$slLorg&amp;order=$vocidClickOrder" title='$T_Sort_the_column'>*</a></td>
<td><a href="./voc.php?user=$user&amp;sl=$slLorg&amp;order=$wordClickOrder" title='$T_Sort_the_column'>$T_Word</a></td>
<td><a href="./voc.php?user=$user&amp;sl=$slLorg&amp;order=$meaningClickOrder" title='$T_Sort_the_column'>$T_Meaning</a></td>
<td>$T_Clicked_in_unit</td>
</tr>
$vocHtml
</table>
END_vocTable;
        }
    }
    $vocHideRevealHtml = <<<END_vocHideRevealHtml
<p>
$T_Test_yourself
<a class=button href='javascript:hideAll();'>$T_Hide_all</a>
<a class=button href='javascript:revealAll();'>$T_Reveal_all</a>
</p>
END_vocHideRevealHtml;
    $HTML = <<<EOD
<h1 style="font-size:140%;margin:0;padding-top:0.5em">$T_Vocabulary_list_for_user_ <span style="color:brown">$user</span></h1>

$langButHtml
<p style="margin:0">$T_Language: $slLorgEndonym</p>
$exportHtml
$vocHideRevealHtml
$vocTableHtml
EOD;

  } catch (Exception $e) { $HTML = $e; }

  $HTMLDOC = <<<END_HTMLDOC
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>$T_Vocabulary_list_for_user_ $user</title>
    <link rel="stylesheet" href="/css/smo.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        span.callcount { font-size:75%; color:grey; }
        table#vocab { border-collapse:collapse; border:1px solid grey; margin-bottom:0.5em; white-space:nowrap; }
        table#vocab tr#vocabhead { background-color:grey; color:white; font-weight:bold; }
        table#vocab tr#vocabhead a { color:white; }
        table#vocab tr:nth-child(odd)  { background-color:#ddf; }
        table#vocab tr:nth-child(even) { background-color:#fff; }
        table#vocab tr:nth-child(odd):hover  { background-color:#fe6; }
        table#vocab tr:nth-child(even):hover { background-color:#fe6; }
        table#vocab td { padding:0px 3px; }
        table#vocab td:nth-child(1) { padding:0 0.4em; }
        table#vocab td:nth-child(2) a { color:blue; }
        table#vocab td:nth-child(2) a:hover { color:white; background-color:black; }
        table#vocab td:nth-child(3) { padding:0; }
        table#vocab tr + tr > td { border-left:1px solid #aaa; }
        table#vocab td.meaning a.reveal { display:none; margin-left:1em; font-size:80%; padding:0 5px; background-color:#5ae; color:white; border-radius:4px; }
        table#vocab td.meaning a.reveal:hover {background-color:blue; }
        table#vocab td.meaning.hide input { display:none }
        table#vocab td.meaning.hide a.reveal { display:inline; }
        a.langbutton { margin:1px 7px; background-color:#55a8eb; color:white; font-weight:bold; padding:2px 8px; border:1px solid white; border-radius:8px; }
        a.langbutton.selected { border-color:#55a8eb; background-color:yellow; color:#55a8eb; }
        a.langbutton.live:hover { background-color:blue; }
        a#emptyBut { border:0; padding:1px 3px; border-radius:6px; background-color:#27b; color:white; text-decoration:none; }
        a#emptyBut:hover,
        a#emptyBut:active,
        a#emptyBut:focus  { background-color:#f00; color:white; }
        div#export { margin:0.6em 4em; border:1px solid grey; border-radius:0.5em; background-color:#eef; padding:0.1em 0.6em; font-size:70%; }
        div#export p { margin:0.5em 0; }
        div#export i { font-size:70%; color:#333; }
    </style>
    <script>
        function deleteVocWord(vocid) {
            var xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                var resp = this.responseText;
                if (resp!='OK') { alert('$T_Error_in deleteVocWord:'+resp); return; }
                var el = document.getElementById('row'+vocid);
                el.parentNode.removeChild(el);
            }
            xhttp.open('GET', 'ajax/deleteVocWord.php?vocid=' + vocid);
            xhttp.send();
        }
        function changeMeaning(vocid,meaning) {
            var xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                var resp = this.responseText;
                if (resp!='OK') { alert('$T_Error_in changeMeaning:'+resp); return; }
                var tickel = document.getElementById(vocid+'-tick');
                tickel.classList.remove('changed'); //Remove the class (if required) and add again after a tiny delay, to restart the animation
                setTimeout(function(){tickel.classList.add('changed');},50);
            }
            xhttp.open('GET', 'ajax/changeMeaning.php?vocid=' + vocid + '&meaning=' + encodeURI(meaning));
            xhttp.send();
        }
        function emptyVocList(user,sl) {
            var confirmMessage = "$T_Empty_voc_list_confirm".replace('[sl]','‘'+sl+'’');
            var r = confirm(confirmMessage);
            if (r==true) {
                var xhttp = new XMLHttpRequest();
                xhttp.onload = function() {
                    var resp = this.responseText;
                    if (resp!='OK') { alert('$T_Error_in emptyVocList:'+resp); return; }
                    window.location = window.location.href.split('?')[0];
                }
                xhttp.open('GET', 'ajax/emptyVocList.php?user='+user+'&sl=' +sl,true);
                xhttp.send();
                return false;
            }
        }
        function hideAll() {
            var inputEls = document.querySelectorAll('table#vocab td.meaning input');
            for (let inputEl of inputEls) {
                if (inputEl.value > '') { inputEl.closest('td.meaning').classList.add('hide'); }
            }
        }
        function revealAll() {
            var inputEls = document.querySelectorAll('table#vocab td.meaning input');
            for (let inputEl of inputEls) {
                if (inputEl.value > '') { inputEl.closest('td.meaning').classList.remove('hide'); }
            }
        }
        function reveal(el) {
            el.closest('td.meaning').classList.remove('hide');
            return false;
        }
    </script>
</head>
<body>
$mdNavbar
<div class="smo-body-indent">

$HTML

</div>
$mdNavbar
</body>
</html>
END_HTMLDOC;

  echo $HTMLDOC;
?>
