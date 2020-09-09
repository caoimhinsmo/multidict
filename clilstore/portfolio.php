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
    $loggedinUser = $user = $myCLIL->id;

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');

    $unitsHtml = $titleHtml = '';

    $pf = $_REQUEST['pf'] ?? -1;
    if ($pf == -1) {
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
    $edit = ( $user==$loggedinUser ? 1 : 0 ); //$edit=1 indicates that the user has edit rights over the portfolio

    $userSC = htmlspecialchars($user) ?? '';

    if ($pf==0) { $h1 = 'Create a new portfolio'; }
      else      { $h1 = "<span style='font-size:75%;font-style:italic'>$T_Portfolio_for_user_ <span style='color:brown'>$user</span></span><br>" . htmlspecialchars($title); }

    if ($pf==0) {

        $unitsTableHtml = <<<END_unitsTableHtml
<p style="margin-left:3em;text-indent:-1.5em;color:green;font-size:85%">You can create a portfolio to show your teacher:<br>
- what Clilstore units you have worked on,<br>
- what you have learned from them,<br>
- any work you have produced yourself.</p>

<div>
Title of your portfolio<br>
<input id=createTitle value='Portfolio' required style="width:80%;max-width:50em">
</div>

<table style="margin-top:1em"><tr style="vertical-align:top">
<td>Your teacher’s Clilstore id<br>
<input id=createTeacher style="width:16em"></td>
<td style="padding-left:0.5em">
<span style="margin:0;color:green;font-size:80%">
This is optional and can be added later.<br>
Your teacher will be able to see your portfolio.</span></span>
</tr></table>

<p style="margin:1em 0 3em 0"><a class=button onClick="createClicked()">Create</a></p>
END_unitsTableHtml;

    } else {

        $unitToAdd = $_REQUEST['unit'] ?? 0;
        if ($unitToAdd) {
            $stmtAddUnit = $DbMultidict->prepare('INSERT IGNORE INTO cspfUnit (pf,unit) VALUES (:pf,:unit)');
            $stmtAddUnit->execute([ ':pf'=>$pf, ':unit'=>$unitToAdd ]);
        }

        $stmtPfu = $DbMultidict->prepare('SELECT cspfUnit.pfu, cspfUnit.unit AS csUnit, clilstore.title AS csTitle FROM cspfUnit,clilstore'
                                    . ' WHERE pf=:pf AND unit=clilstore.id');
        $stmtPfu->execute([':pf'=>$pf]);
        $pfuRows = $stmtPfu->fetchAll(PDO::FETCH_ASSOC);
        foreach ($pfuRows as $pfuRow) {
            extract($pfuRow);
            $stmtPfuL = $DbMultidict->prepare('SELECT id AS pfuL, learned FROM cspfUnitLearned WHERE pfu=:pfu');
            $stmtPfuL->execute([':pfu'=>$pfu]);
            $learnedHtml = '';
            $unitidHtml = "<a href='/cs/$csUnit'>$csUnit</a>";
            $rowClass = ( $csUnit==$unitToAdd ? 'class=highlight' : '');
            if ($edit) {
                $removeUnitHtml = "<img src='/icons-smo/curAs.png' alt='Delete' title='Remove this unit from the portfolio' onclick=\"removeUnit('$pfu')\">";
                $unitidHtml = "$removeUnitHtml $unitidHtml";
            }
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
            $unitsHtml .= <<<END_unitsHtml
<tr id=row$pfu $rowClass>
<td>$unitidHtml</td>
<td>$csTitle</td>
<td>$learnedHtml <!-- <span id="\$vocid-tick" class=change>✔<span> --></td>
<td>$workHtml</td>
</tr>
END_unitsHtml;
        }
        if (empty($unitsHtml) && $edit) { $unitsHtml = <<<END_nounitsHtml
<tr><td colspan=3>You can add Clilstore units to your portfolio by clicking the ‘P’ button at the top of a unt.</td></tr>
END_nounitsHtml;
       }

        $unitsTableHtml = <<<END_unitsTable
<table id=unitstab>
<tr id=unitstabhead><td>Unit</td><td>Title</td><td>What I have learned</td><td>Links to my work</td></tr>
$unitsHtml
</table>
END_unitsTable;

    }

    $HTML = <<<EOD
<p style="color:red;margin:0">This facility is still under development</p>
<h1 style="font-size:140%;margin:0.5em 0">$h1</h1>

$unitsTableHtml
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
        table#unitstab { border-collapse:collapse; border:1px solid grey; margin-bottom:0.5em; }
        table#unitstab tr#unitstabhead { background-color:grey; color:white; font-weight:bold; }
        table#unitstab tr#unitstabhead td { padding-left:0.7em; }
        table#unitstab tr:nth-child(odd)  { background-color:#ddf; }
        table#unitstab tr:nth-child(even) { background-color:#fff; }
        table#unitstab tr:nth-child(odd):hover  { background-color:#fe6; }
        table#unitstab tr:nth-child(even):hover { background-color:#fe6; }
        table#unitstab tr.highlight { background-color:#ffc; border:2px solid orange; }
        table#unitstab td { padding:0 4px; }
        table#unitstab tr + tr > td { border-left:1px solid #aaa; }
        a#emptyBut { border:0; padding:1px 3px; border-radius:6px; background-color:#27b; color:white; text-decoration:none; }
        a#emptyBut:hover,
        a#emptyBut:active,
        a#emptyBut:focus  { background-color:#f00; color:white; }
    </style>
    <script>
        function createClicked() {
            var title   = document.getElementById('createTitle').value.trim();
            var teacher = document.getElementById('createTeacher').value.trim();
            if (title=='') { alert('You must give your portfolio a name'); return; }
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if       (resp=='OK')     { window.location.href = '/clilstore/portfolio.php'; }
                     else if (resp=='nouser') { alert('There is no such Clilstore userid as '+teacher); }
                     else                     { alert('Error in pfCreate: '+resp); }
                }
            }
            var formData = new FormData();
            formData.append('title',title);
            formData.append('teacher',teacher);
            xhttp.open('POST', 'ajax/pfCreate.php');
            xhttp.send(formData);
        }

        function removeUnit (pfu) {
            if (confirm('Completely remove the unit from the portfolio?')) {
                alert('The unit should now have been removed, if the programming for this been had finished');
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
