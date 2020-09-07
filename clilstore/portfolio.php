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

  $T = new SM_T('clilstore/portfolio');

  $T_Error_in = $T->h('Error_in');

  $T_Portfolio_for_user_       = $T->h('Portfolio_for_user_');
  $T_Delete_instantaneously    = $T->h('Delete_instantaneously');

  $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

  try {
    $myCLIL->dearbhaich();
    $loggedinUser = $myCLIL->id;

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');

    $pfHtml = $titleHtml = '';

    $pf = $_REQUEST['pf'] ?? -1;
    if ($pf == -1) {
        $user = $loggedinUser;
        $stmt = $DbMultidict->prepare('SELECT pf,title FROM cspf WHERE user=:user ORDER BY prio DESC LIMIT 1');
        $stmt->execute([':user'=>$user]);
        if (!($row = $stmt->fetch(PDO::FETCH_ASSOC))) { $pf = 0; }
        extract($row);
    } elseif ( $pf <> 0 ) {
        $stmt = $DbMultidict->prepare('SELECT title,user FROM cspf WHERE pf=:pf');
        $stmt->execute([':pf'=>$pf]);
        if (!($row = $stmt->fetch(PDO::FETCH_ASSOC))) { throw new SM_MDexception(sprintf("Portfolio $pf does not exist")); }
        extract($row);
        if ($user<>$loggedinUser) {
            $stmt2 = $DbMultidict->prepare('SELECT id FROM cspfPermit WHERE pf=:pf AND teacher=:teacher');
            $stmt2->execute([':pf'=>$pf,':teacher'=>$loggedinUser]);
            if (!$stmt2->fetch()) { throw new SM_MDexception('You do not have read access to this portfolio'); }
        }
    }

    $userSC = htmlspecialchars($user);

    if ($pf==0) { $title = 'Create a new portfolio'; }
    $titleHtml = '<br>' . htmlspecialchars($title);

    if ($pf==0) {

        $pfTableHtml = <<<END_pfTableHtml
<p>(There will be a form here to create a new Portfolio)</p>
END_pfTableHtml;

    } else {

        $stmtPfu = $DbMultidict->prepare('SELECT cspfUnit.pfu, cspfUnit.unit AS csUnit, clilstore.title AS csTitle FROM cspfUnit,clilstore'
                                    . ' WHERE pf=:pf AND unit=clilstore.id');
        $stmtPfu->execute([':pf'=>$pf]);
        $pfRows = $stmtPfu->fetchAll(PDO::FETCH_ASSOC);
        foreach ($pfRows as $pfRow) {
            extract($pfRow);
            $stmtPfuL = $DbMultidict->prepare('SELECT id AS pfuL, learned FROM cspfUnitLearned WHERE pfu=:pfu');
            $stmtPfuL->execute([':pfu'=>$pfu]);
            $learnedHtml = '';
            $pfuLRows = $stmtPfuL->fetchAll(PDO::FETCH_ASSOC);
            foreach ($pfuLRows as $pfuLRow) {
                extract ($pfuLRow);
                $learnedHtml .= "<li>$learned\n";
            }
            $learnedHtml = "<ul>\n$learnedHtml\n</ul>\n";
            $stmtPfuW = $DbMultidict->prepare('SELECT id AS pfuW, work, url AS workurl FROM cspfUnitWork WHERE pfu=:pfu');
            $stmtPfuW->execute([':pfu'=>$pfu]);
            $workHtml = '';
            $pfuWRows = $stmtPfuW->fetchAll(PDO::FETCH_ASSOC);
            foreach ($pfuWRows as $pfuWRow) {
                extract ($pfuWRow);
                $workHtml .= "<li><a href='$workurl'>$work</a>\n";
            }
            $workHtml = "<ul>\n$workHtml\n</ul>\n";
            $pfHtml .= <<<END_pfHtml
<tr id=row$pfu>
<td><img src='/icons-smo/curAs.png' alt='Delete' title='$T_Delete_instantaneously' onclick="deleteVocWord('$pfu')"></td>
<td><a href='/cs/$csUnit'>$csUnit</a></td>
<td>$csTitle</td>
<td>$learnedHtml <!-- <span id="\$vocid-tick" class=change>✔<span> --></td>
<td>$workHtml</td>
</tr>
END_pfHtml;
        }

        $pfTableHtml = <<<END_pfTable
<table id=pftab>
<tr id=pftabhead><td></td><td>Unit</td><td>Title</td><td>What I have learned</td><td>Links to my work</td></tr>
$pfHtml
</table>
END_pfTable;

    }

    $HTML = <<<EOD
<p style="color:red;margin:0">This facility is still under development</p>
<h1 style="font-size:140%;margin:0;padding-top:0.5em">$T_Portfolio_for_user_ <span style="color:brown">$user</span>$titleHtml</h1>

$pfTableHtml
EOD;

  } catch (Exception $e) { $HTML = $e; }

  $HTMLDOC = <<<END_HTMLDOC
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>$T_Portfolio_for_user_ $user</title>
    <link rel="stylesheet" href="/css/smo.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        span.callcount { font-size:75%; color:grey; }
        table#pftab { border-collapse:collapse; border:1px solid grey; margin-bottom:0.5em; white-space:nowrap; }
        table#pftab tr#pftabhead { background-color:grey; color:white; font-weight:bold; }
        table#pftab tr:nth-child(odd)  { background-color:#ddf; }
        table#pftab tr:nth-child(even) { background-color:#fff; }
        table#pftab tr:nth-child(odd):hover  { background-color:#fe6; }
        table#pftab tr:nth-child(even):hover { background-color:#fe6; }
        table#pftab td { padding:0px 3px; }
        table#pftab td:nth-child(1) { padding:0 0.4em; }
        table#pftab td:nth-child(2) a:hover { color:white; background-color:black; }
        table#pftab td:nth-child(3) { padding:0; }
        table#pftab tr + tr > td { border-left:1px solid #aaa; }
        a#emptyBut { border:0; padding:1px 3px; border-radius:6px; background-color:#27b; color:white; text-decoration:none; }
        a#emptyBut:hover,
        a#emptyBut:active,
        a#emptyBut:focus  { background-color:#f00; color:white; }
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
            var confirmMessage = "Empty_voc_list_confirm".replace('[sl]','‘'+sl+'’');
            var r = confirm(confirmMessage);
            if (r==true) {
                var xhttp = new XMLHttpRequest();
                xhttp.onload = function() {
                    var resp = this.responseText;
                    if (resp!='OK') { alert('$T_Error_in emptyVocList:'+resp); return; }
                    location.reload();
                }
                xhttp.open('GET', 'ajax/emptyVocList.php?user='+user+'&sl=' +sl,true);
                xhttp.send();
                return false;
            }
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
