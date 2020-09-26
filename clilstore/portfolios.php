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

  $T = new SM_T('clilstore/portfolios');

  $T_Portfolio    = $T->h('Portfolio');
  $T_Hide         = $T->h('Hide');
  $T_hideShow     = $T->h('hideShow');
  $T_Student_id   = $T->h('Student_id');
  $T_Student_name = $T->h('Student_name');
  $T_Error_in     = $T->j('Error_in');
  $T_Portfolios_viewable_by = $T->h('Portfolios_viewable_by');
  $T_Show_hidden_portfolios = $T->h('Show_hidden_portfolios');

  $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

  try {
    $myCLIL->dearbhaich();
    $loggedinUser = $myCLIL->id;

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');

    $pfHtml = '';

    $user = $_REQUEST['teacher'] ?? $loggedinUser;
    $userSC = htmlspecialchars($user);

    $stmtPfs = $DbMultidict->prepare('SELECT cspf.pf, hidden, cspf.user AS student, fullname, title, cspfPermit.id AS pid'
                                   . ' FROM cspf, cspfPermit, users'
                                   . ' WHERE teacher=:teacher AND cspf.pf=cspfPermit.pf AND cspf.user=users.user ORDER BY student ASC, prio DESC');
    $stmtPfs->execute([':teacher'=>$user]);
    $rows = $stmtPfs->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        extract($row);
        $studentSC  = htmlspecialchars($student);
        $fullnameSC = htmlspecialchars($fullname);
        $titleSC    = ( empty($title) ? 'Portfolio' : htmlspecialchars($title) );
        $rowClass = $hiddenChecked = '';
        if ($hidden) { $rowClass= 'class=hidden'; $hiddenChecked = 'checked'; }
        $pfHtml .= <<<END_pfHtml
<tr id=row$pid $rowClass>
<td>
<label class=toggle-switchy for=hidden$pid data-size=xxs onChange="toggleHidden('$pid')">
  <input type=checkbox id=hidden$pid $hiddenChecked>
  <span class=toggle title='$T_hideShow'><span class=switch></span></span>
</label>
</td>
<td><a href="userinfo.php?user=$student">$studentSC</a></td>
<td>$fullnameSC</td>
<td><a href="portfolio.php?pf=$pf">$titleSC</a></td>
</tr>
END_pfHtml;
    }

    $pfTableHtml = <<<END_pfTable
<table id=pftab class=hiding>
<tr id=pftabhead><td style="font-size:70%;font-weight:normal;text-align:center">$T_Hide</td><td>$T_Student_id</td><td>$T_Student_name</td><td>$T_Portfolio</td></tr>
$pfHtml
</table>
END_pfTable;

    $HTML = <<<EOD
<h1 style="font-size:140%;margin:0.5em 0">$T_Portfolios_viewable_by <span style="color:brown">$userSC</span></h1>

$pfTableHtml
<label class=toggle-switchy for=showHidden data-size=xxs style="margin:1em 0 1.5em 0" onChange="toggleHiding()">
  <input type=checkbox id=showHidden>
  <span class=toggle><span class=switch></span></span>
  <span class=label style="font-size:70%">$T_Show_hidden_portfolios</span>
</label>
EOD;

  } catch (Exception $e) { $HTML = $e; }

  $HTMLDOC = <<<END_HTMLDOC
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>$T_Portfolios_viewable_by $userSC</title>
    <link rel="stylesheet" href="/css/smo.css">
    <link rel="stylesheet" href="style.css">
    <link rel="StyleSheet" href="/css/toggle-switchy.css">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        table#pftab { border-collapse:collapse; border:1px solid grey; margin-bottom:0.3em; }
        table#pftab tr#pftabhead { background-color:grey; color:white; font-weight:bold; }
        table#pftab tr:nth-child(odd)  { background-color:#ddf; }
        table#pftab tr:nth-child(even) { background-color:#fff; }
        table#pftab tr:nth-child(odd):hover  { background-color:#fe6; }
        table#pftab tr:nth-child(even):hover { background-color:#fe6; }
        table#pftab td { padding:1px 5px; }
        table#pftab td:nth-child(1) { padding:1px; text-align:center; }
        table#pftab tr + tr > td { border-left:1px solid #aaa; }
        table#pftab.hiding tr.hidden { display:none; }
        a#emptyBut { border:0; padding:1px 3px; border-radius:6px; background-color:#27b; color:white; text-decoration:none; }
        a#emptyBut:hover,
        a#emptyBut:active,
        a#emptyBut:focus  { background-color:#f00; color:white; }
    </style>
    <script>
        function toggleHiding() {
            var ch  = document.getElementById('showHidden').checked;
            var tab = document.getElementById('pftab');
            if (ch) { tab.classList.remove('hiding'); }
              else  { tab.classList.add('hiding');    }
        }
        function toggleHidden(pid) {
            var ch  = document.getElementById('hidden'+pid).checked
            var xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                var resp = this.responseText;
                if (resp!='OK') { alert('$T_Error_in toggleHidden:'+resp); return; }
                var row = document.getElementById('row'+pid);
                if (ch) { row.classList.add('hidden'); }
                  else  { row.classList.remove('hidden'); }
            }
            xhttp.open('GET', 'ajax/pfSetHide.php?pid=' + pid + '&hidden=' + ch);
            xhttp.send();
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
