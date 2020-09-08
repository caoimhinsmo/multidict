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
    $edit = ( $user==$loggedinUser ? 1 : 0 ); //$edit=1 indicates that the user has edit rights over the portfolio

    $userSC = htmlspecialchars($user) ?? '';

    if ($pf==0) { $h1 = 'Create a new portfolio'; }
      else      { $h1 = "<span style='font-size:75%;font-style:italic'>$T_Portfolio_for_user_ <span style='color:brown'>$user</span></span><br>" . htmlspecialchars($title); }

    if ($pf==0) {

        $pfTableHtml = <<<END_pfTableHtml
<p style="margin-left:3em;text-indent:-1.5em;color:green;font-size:85%">You can create a portfolio to show your teacher:<br>
- what Clilstore units you have worked on,<br>
- what you have learned from them,<br>
- any work you have produced yourself.</p>

<form method=post action="createPortfolio.php">
<div>
Title of your portfolio<br>
<input id=createTitle value='Portfolio' style="width:80%;max-width:50em">
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
</form>
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
        if (empty($pfHtml) && $edit) { $pfHtml = <<<END_pfHtmlNoUnits
<tr><td colspan=4>You can add Clilstore units to your portfolio by clicking the ‘P’ button at the top of a unt.</td></tr>
END_pfHtmlNoUnits;
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
<h1 style="font-size:140%;margin:0.5em 0">$h1</h1>

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
        table#pftab { border-collapse:collapse; border:1px solid grey; margin-bottom:0.5em; }
        table#pftab tr#pftabhead { background-color:grey; color:white; font-weight:bold; }
        table#pftab tr:nth-child(odd)  { background-color:#ddf; }
        table#pftab tr:nth-child(even) { background-color:#fff; }
        table#pftab tr:nth-child(odd):hover  { background-color:#fe6; }
        table#pftab tr:nth-child(even):hover { background-color:#fe6; }
        table#pftab td { padding:0 4px; }
        table#pftab tr + tr > td { border-left:1px solid #aaa; }
    </style>
    <script>
        function createClicked() {
            var title   = document.getElementById('createTitle').value;
            var teacher = document.getElementById('createTeacher').value;
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
