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

  $T_Clilstore_unit = $T->h('Clilstore_unit');
  $T_userid         = $T->h('userid');
  $T_Add_a_teacher  = $T->h('Add_a_teacher');
  $T_My_portfolios  = $T->h('My_portfolios');
  $T_Create         = $T->h('Create');
  $T_Promote        = $T->h('Promote');
  $T_Move_up        = $T->h('Move_up');
  $T_Move_down      = $T->h('Move_down');
  $T_Error_in       = $T->j('Error_in');
  $T_Is_this_OK     = $T->j('Is_this_OK');

  $T_Portfolio_for_user_     = $T->h('Portfolio_for_user_');
  $T_What_I_have_learned     = $T->h('What_I_have_learned');
  $T_Links_to_my_work        = $T->h('Links_to_my_work');
  $T_Delete_instantaneously  = $T->h('Delete_instantaneously');
  $T_Create_a_new_portfolio  = $T->h('Create_a_new_portfolio');
  $T_Title_of_your_portfolio = $T->h('Title_of_your_portfolio');
  $T_New_portfolio_advice    = strtr( $T->h('New_portfolio_advice'), ['&lt;br&gt;'=>'<br>'] );
  $T_New_teacher_advice      = strtr( $T->h('New_teacher_advice'),   ['&lt;br&gt;'=>'<br>'] );
  $T_active_portfolio        = $T->h('active_portfolio');
  $T_this_portfolio          = $T->h('this_portfolio');
  $T_Delete_this_portfolio   = $T->h('Delete_this_portfolio');
  $T_Delete_this_item        = $T->h('Delete_this_item');
  $T_Edit_unit_in_portfolio  = $T->h('Edit_unit_in_portfolio');
  $T_Completely_remove_unit  = $T->h('Completely_remove_unit');
  $T_A_URL_must_begin_with_  = $T->j('A_URL_must_begin_with_');
  $T_No_such_userid_as_      = $T->j('No_such_userid_as_');
  $T_Have_changed_URL_to_    = $T->j('Have_changed_URL_to_');

  $T_No_teachers_can_yet_view       = $T->h('No_teachers_can_yet_view');
  $T_Following_teacher_can_view     = $T->h('Following_teacher_can_view');
  $T_Following_2_teachers_can_view  = $T->h('Following_2_teachers_can_view');
  $T_Following_teachers_can_view    = $T->h('Following_teachers_can_view');
  $T_Your_teachers_Clilstore_id     = $T->h('Your_teachers_Clilstore_id');
  $T_Promote_to_active_portfolio    = $T->h('Promote_to_active_portfolio');
  $T_Portfolio__does_not_exist      = $T->h('Portfolio__does_not_exist');
  $T_You_do_not_have_read_access    = $T->h('You_do_not_have_read_access');
  $T_Portfolio_contains_no_units    = $T->h('Portfolio_contains_no_units');
  $T_You_can_add_Clilstore_units    = $T->h('You_can_add_Clilstore_units');
  $T_Remove_permission_from_teacher = $T->h('Remove_permission_from_teacher');
  $T_Remove_unit_from_portfolio     = $T->h('Remove_unit_from_portfolio');
  $T_Completely_delete_portfolio    = $T->j('Completely_delete_portfolio');
  $T_Teacher_already_has_access     = $T->j('Teacher_already_has_access');

  $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

  try {
    $myCLIL->dearbhaich();
    $loggedinUser = $user = $myCLIL->id;

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');

    $unitsHtml = $titleHtml = $permitTableHtml = $pfsTableHtml = $addTeacherHtml = $itemEditHtml = '';

    $pf = $_REQUEST['pf'] ?? -1;
    if ($pf == -1) {
        $stmt = $DbMultidict->prepare('SELECT pf,title FROM cspf WHERE user=:user ORDER BY prio DESC LIMIT 1');
        $stmt->execute([':user'=>$user]);
        if (!($row = $stmt->fetch(PDO::FETCH_ASSOC))) { $pf = 0; }
        extract($row);
    } elseif ( $pf <> 0 ) {
        $stmt = $DbMultidict->prepare('SELECT title,user FROM cspf WHERE pf=:pf');
        $stmt->execute([':pf'=>$pf]);
        if (!($row = $stmt->fetch(PDO::FETCH_ASSOC))) { throw new SM_MDexception(strtr("$T_Portfolio__does_not_exist",['{pf}'=>$pf])); }
        extract($row);
        if ($user<>$loggedinUser) {
            $stmt2 = $DbMultidict->prepare('SELECT id FROM cspfPermit WHERE pf=:pf AND teacher=:teacher');
            $stmt2->execute([':pf'=>$pf,':teacher'=>$loggedinUser]);
            if (!$stmt2->fetch()) { throw new SM_MDexception("$T_You_do_not_have_read_access"); }
        }
    }
    $edit = ( in_array($loggedinUser, [$user,'admin']) ? 1 : 0 ); //$edit=1 indicates that the user has edit rights over the portfolio
    if ($edit) {
        $itemEditHtml = "<span class=upArrow title='$T_Move_up' onClick=moveItem(this,'up')>⇧</span>"
                      . "<span class=downArrow title='$T_Move_down' onClick=moveItem(this,'down')>⇩</span> "
                      . "<img src='/icons-smo/curAs.png' alt='Delete' title='$T_Delete_this_item' onClick='itemDelete(this)'>";
        $LitemEditHtml = "<img src='/icons-smo/peann.png' class=editIcon alt='Edit' title='Edit this item' onClick='LitemEdit(this)'>"
                       . "<img src='/icons-smo/floppydisk.png' class=saveIcon alt='Save' title='Save your edits' onClick='LitemSave(this)'> "
                       . $itemEditHtml;
        $itemEditHtml  = "<span class=edit>$itemEditHtml</span>";
        $LitemEditHtml = "<span class=edit>$LitemEditHtml</span>";
    }

    $userSC = htmlspecialchars($user) ?? '';

    if ($pf==0) { $h1 = $T_Create_a_new_portfolio; }
      else      { $h1 = "<span style='font-size:75%;font-style:italic'>$T_Portfolio_for_user_ <span style='color:brown'>$user</span></span><br>" . htmlspecialchars($title); }

    if ($pf==0) {

        $unitsTableHtml = <<<END_unitsTableHtml
<div style="float:left;margin:0 0 1em 5em;border:1px solid green;background-color:#dfd;border-radius:0.3em">
<p style="margin:0;padding:0.5em 0.5em 0.5em 2em;text-indent:-1.5em;color:green;font-size:85%">$T_New_portfolio_advice</p>
</div>

<div style="clear:both">
$T_Title_of_your_portfolio<br>
<input id=createTitle required style="width:80%;max-width:50em">
</div>

<table style="margin-top:1em"><tr style="vertical-align:top">
<td>$T_Your_teachers_Clilstore_id<br>
<input id=createTeacher style="width:16em"></td>
<td style="padding-left:0.5em">
<div style="float:left;border:1px solid green;border-radius:0.3em;padding:0.2em 0.4em;margin:0;color:green;font-size:80%;background-color:#dfd">
$T_New_teacher_advice</div>
</tr></table>

<p style="margin:1em 0 3em 0"><a class=button onClick="createPortfolio()">$T_Create</a></p>
END_unitsTableHtml;

    } else {

        $unitToEdit = $_REQUEST['unit'] ?? 0;

        $stmtPfu = $DbMultidict->prepare('SELECT cspfUnit.pfu, cspfUnit.unit AS csUnit, clilstore.title AS csTitle FROM cspfUnit,clilstore'
                                    . ' WHERE pf=:pf AND unit=clilstore.id ORDER BY ord');
        $stmtPfu->execute([':pf'=>$pf]);
        $pfuRows = $stmtPfu->fetchAll(PDO::FETCH_ASSOC);
        foreach ($pfuRows as $pfuRow) {
            extract($pfuRow);
            $stmtPfuL = $DbMultidict->prepare('SELECT id AS pfuL, learned FROM cspfUnitLearned WHERE pfu=:pfu ORDER BY ord');
            $stmtPfuL->execute([':pfu'=>$pfu]);
            $learnedHtml = $workHtml = $newLearnedItem = $newWorkItem = '';
            $unitidHtml = str_pad($csUnit, 5, '_', STR_PAD_LEFT);
            $unitidHtml = str_replace('_','&nbsp;',$unitidHtml);
            $rowClass = ( $csUnit==$unitToEdit ? 'class=edit' : '');
            if ($edit) {
                $removeUnitHtml = "<img src='/icons-smo/bin.png' alt='Remove' title='$T_Remove_unit_from_portfolio' onclick=\"removeUnit('$pfu')\">";
                $editUnitHtml   = "<img src='/icons-smo/peann.png' alt='Edit' title='$T_Edit_unit_in_portfolio' onClick=\"toggleUnitEdit('$pfu')\">";
                $moveUnitHtml   = "<span class=upArrowUnit title='$T_Move_up' onClick=moveUnit(this,'up')>⇧</span>"
                                . "<span class=downArrowUnit title='$T_Move_down' onClick=moveUnit(this,'down')>⇩</span>";
                $editToolsHtml = "$editUnitHtml &nbsp;&nbsp; <span class=edit>$moveUnitHtml &nbsp;&nbsp; $removeUnitHtml</span>";
            }
            $pfuLRows = $stmtPfuL->fetchAll(PDO::FETCH_ASSOC);
            foreach ($pfuLRows as $pfuLRow) {
                extract ($pfuLRow);
                $learnedHtml .= "<li id=pfuL$pfuL><span id=pfuLtext$pfuL onKeypress='keypress(event,this)'>$learned</span> $LitemEditHtml\n";
            }
            if ($edit) { $newLearnedItem = "<input id=pfuLnew$pfu class=edit placeholder='Add an item' onChange=\"pfuLadd('$pfu')\">"; }
            $learnedHtml = <<<END_learnedHtml
<ul id=pfuLul$pfu>
$learnedHtml
</ul>
$newLearnedItem
END_learnedHtml;
            $stmtPfuW = $DbMultidict->prepare('SELECT id AS pfuW, work, url AS workurl FROM cspfUnitWork WHERE pfu=:pfu ORDER BY ord');
            $stmtPfuW->execute([':pfu'=>$pfu]);
            $pfuWRows = $stmtPfuW->fetchAll(PDO::FETCH_ASSOC);
            foreach ($pfuWRows as $pfuWRow) {
                extract ($pfuWRow);
                $workHtml .= "<li id=pfuW$pfuW><a href='$workurl'>$work</a> $itemEditHtml\n";
            }
            if ($edit) {
                $newWorkItem = "<input placeholder='Work' id=pfuWnewWork$pfu><br><input placeholder='URL' id=pfuWnewURL$pfu>";
                $newWorkItem = "<span class=edit onChange=\"pfuWadd('$pfu')\">$newWorkItem</span>";
            }
            $workHtml = <<<END_workHtml
<ul id=pfuWul$pfu>
$workHtml
</ul>
$newWorkItem
END_workHtml;
            $unitsHtml .= <<<END_unitsHtml
<tr id=pfuRow$pfu $rowClass>
<td><p style="margin:0"><a href='/cs/$csUnit'>$unitidHtml<br>$csTitle</a></p><p style="margin:0.7em 0 0.3em 0.7em">$editToolsHtml</p></td>
<td>$learnedHtml <!-- <span id="\$vocid-tick" class=change>✔<span> --></td>
<td>$workHtml</td>
</tr>
END_unitsHtml;
        }
        if (empty($unitsHtml)) {
            if ($edit) { $unitsHtml = "<i>$T_You_can_add_Clilstore_units</i>"; }
             else      { $unitsHtml = "<i>$T_Portfolio_contains_no_units</i>"; }
           $unitsHtml = "<tr><td colspan=3>$unitsHtml</td></tr>";
       }

        $unitsTableHtml = <<<END_unitsTable
<table id=unitstab>
<caption>$h1</caption>
<col style="width:25%"><col><col>
<tr id=unitstabhead><td>$T_Clilstore_unit</td><td>$T_What_I_have_learned</td><td>$T_Links_to_my_work</td></tr>
$unitsHtml
</table>
END_unitsTable;

        $stmtPermit = $DbMultidict->prepare('SELECT cspfPermit.id AS permitId, teacher,fullname FROM cspfPermit,users WHERE teacher=user AND pf=:pf ORDER BY teacher');
        $stmtPermit->execute([':pf'=>$pf]);
        $rows = $stmtPermit->fetchall(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            extract($row);
            $editHtml = '';
            if ($edit) {
                $editHtml = "<img src='/icons-smo/curAs.png' alt='Remove' title='$T_Remove_permission_from_teacher' onclick=\"pfRemovePermit('$permitId')\">";
            }
            $permitTableHtml .= "<tr id=permitRow$permitId><td>$editHtml $teacher ($fullname)</td></tr>\n";
        }
        $nTeachers = count($rows);
        if      ($nTeachers==0) { $teachersMessage = "$T_No_teachers_can_yet_view";      }
         elseif ($nTeachers==1) { $teachersMessage = "$T_Following_teacher_can_view";    }
         elseif ($nTeachers==2) { $teachersMessage = "$T_Following_2_teachers_can_view"; }
         else                   { $teachersMessage = "$T_Following_teachers_can_view";   }
        if ($edit) { $addTeacherHtml = <<<END_addTeacher
<tr><td><input id=addTeacher placeholder="$T_userid" style="margin-left:1em" onChange="pfAddTeacher('$pf')"> $T_Add_a_teacher</td></tr>
END_addTeacher;
        }
        $permitTableHtml = <<<END_pt
<p style="margin:1.7em 0 0 0.5em">$teachersMessage</p>
<table style="margin-left:2em">
$permitTableHtml
$addTeacherHtml
</table>
END_pt;

        if ($edit) {
            $stmtPfs = $DbMultidict->prepare('SELECT pf AS portf,title FROM cspf WHERE user=:user ORDER BY prio DESC');
            $stmtPfs->execute([':user'=>$user]);
            $rows = $stmtPfs->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $n=>$row) {
                extract($row);
                if ($n==0) { $promoteHtml = "- <b>$T_active_portfolio</b>";
                             $title = "<b>$title</b>"; }
                 else      { $promoteHtml = "<a title='$T_Promote_to_active_portfolio' onClick=\"pfPromote('$portf')\">↑ $T_Promote</a>"; }
                $editHtml = "<img src='/icons-smo/curAs.png' alt='Delete' title='$T_Delete_this_portfolio' onclick=\"pfDelete('$portf','$pf','$n')\">";
                if ($portf==$pf) { $promoteHtml .= " &nbsp; [$T_this_portfolio]"; }
                $promoteHtml = "<span style='font-size:75%'>$promoteHtml</span>";
                $pfsTableHtml .= "<tr id=pfsRow$portf><td>$editHtml <a href='./portfolio.php?pf=$portf'>$title</a> $promoteHtml</td></tr>\n";
            }
            $pfsTableHtml = <<<END_pfstab
<p style="margin:3em 0 0 0; border-top:2px solid grey; font-weight:bold">$T_My_portfolios</p>
<table style="margin-left:2em">
$pfsTableHtml
<tr><td><a class=button href="portfolio.php?pf=0" style="font-size:75%">$T_Create_a_new_portfolio</a></td></tr>
</table>
END_pfstab;
        }
    }

    $HTML = <<<EOD
<h1 style="font-size:140%;margin:0.5em 0">$h1</h1>

$unitsTableHtml
$permitTableHtml
$pfsTableHtml
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
        table#unitstab { border-collapse:collapse; border:2px solid grey; margin-bottom:0.5em; }
        table#unitstab tr#unitstabhead { background-color:grey; color:white; font-weight:bold; font-size:120% }
        table#unitstab tr#unitstabhead td { padding-left:0.7em; }
        table#unitstab tr:nth-child(odd)  { background-color:#f8f8ff; }
        table#unitstab tr:nth-child(even) { background-color:#fff; }
        table#unitstab tr:nth-child(odd):hover  { background-color:#eff; }
        table#unitstab tr:nth-child(even):hover { background-color:#eff; }
        table#unitstab tr.edit { background-color:#ffc; border:2px solid red; }
        table#unitstab tr.edit { background-color:#ffb; }
        table#unitstab td { padding:0 4px; vertical-align:top; }
        table#unitstab tr + tr > td { border-left:1px solid #aaa; border-top:1px solid #333; }
        table#unitstab tr      td .edit { display:none; }
        table#unitstab tr.edit td .edit { display:inline; }
        table#unitstab input { width:90%; min-width:20em; }
        a#emptyBut { border:0; padding:1px 3px; border-radius:6px; background-color:#27b; color:white; text-decoration:none; }
        a#emptyBut:hover,
        a#emptyBut:active,
        a#emptyBut:focus  { background-color:#f00; color:white; }
        li img.editIcon { display:inline; }
        li img.saveIcon { display:none; }
        li.editing span:first-child { background-color:white; padding:0 0.3em; border:1px solid black; }
        li.editing img.editIcon { display:none; }
        li.editing img.saveIcon { display:inline; }
        li:first-child span.upArrow { display:none; }
        li:last-child span.downArrow { display:none; }
        tr:nth-child(2) span.upArrowUnit { display:none; }
        tr:last-child span.downArrowUnit { display:none; }
    </style>
    <script>
        function createPortfolio() {
            var title   = document.getElementById('createTitle').value.trim();
            var teacher = document.getElementById('createTeacher').value.trim();
            if (title=='') { alert('You must give your portfolio a name'); return; }
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    if       (resp=='OK')     { window.location.href = '/clilstore/portfolio.php'; }
                     else if (resp=='nouser') { alert('There is no such Clilstore userid as '+teacher); }
                     else                     { alert('$T_Error_in pfCreate: '+resp); }
                }
            }
            var formData = new FormData();
            formData.append('title',title);
            formData.append('teacher',teacher);
            xhr.open('POST', 'ajax/pfCreate.php');
            xhr.send(formData);
        }

        function removeUnit (pfu) {
            if (confirm('$T_Completely_remove_unit')) {
                var xhr = new XMLHttpRequest();
                xhr.onload = function() {
                    var resp = this.responseText;
                    if (resp!='OK') { alert('$T_Error_in portfolio.php removeUnit\\r\\n\\r\\n'+resp); return; }
                    var el = document.getElementById('pfuRow'+pfu);
                    el.parentNode.removeChild(el);
                }
                xhr.open('GET', 'ajax/pfRemoveUnit.php?pfu='+pfu);
                xhr.send();
            }
        }

        function pfDelete (pf,thisPf,n) {
            var pfsRow = document.getElementById('pfsRow'+pf);
            pfsRow.style.backgroundColor = 'pink';
            if (confirm('$T_Completely_delete_portfolio')) {
                var xhr = new XMLHttpRequest();
                xhr.onload = function() {
                    var resp = this.responseText;
                    var nl = '\\r\\n'; //newline
                    if (resp!='OK') { alert('$T_Error_in portfolio.php pfDelete'+nl+nl+resp+nl); return; }
                    if (pf==thisPf) { window.location.href = 'portfolio.php'; } //this portfolio has now been deleted
                     else if (n==0) { window.location.href = 'portfolio.php?pf='+thisPf; } // the active portfolio has now been deleted, but but not this one
                     else           { pfsRow.parentNode.removeChild(pfsRow); }
                }
                xhr.open('GET', 'ajax/pfDelete.php?pf='+pf);
                xhr.send();
            } else { pfsRow.style.backgroundColor = ''; }
        }

        function pfPromote (pf) {
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                var resp = this.responseText;
                var nl = '\\r\\n'; //newline
                if (resp!='OK') { alert('$T_Error_in portfolio.php pfPromote'+nl+nl+resp+nl); return; }
                window.location.href = 'portfolio.php';
            }
            xhr.open('GET', 'ajax/pfPromote.php?pf='+pf);
            xhr.send();
        }

        function pfAddTeacher (pf) {
            var teacher = document.getElementById('addTeacher').value.trim();
            if (teacher=='') { return; }
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    var nl = '\\r\\n'; //newline
                    if       (resp=='OK')        { window.location.href = location.href; }
                     else if (resp=='nouser')    { alert('$T_No_such_userid_as_'.replace('{userid}',teacher)); }
                     else if (resp=='duplicate') { alert('$T_Teacher_already_has_access'); }
                     else                        { alert('$T_Error_in pfAddTeacher.php'+nl+nl+resp+nl); }
                }
            }
            var formData = new FormData();
            formData.append('pf',pf);
            formData.append('teacher',teacher);
            xhr.open('POST', 'ajax/pfAddTeacher.php'); //Safer to use POST in case of rubbish in teacher userid
            xhr.send(formData);
        }

        function pfRemovePermit (permitId) {
            var xhr = new XMLHttpRequest();
            var permitRow = document.getElementById('permitRow'+permitId);
            xhr.onload = function() {
                var resp = this.responseText;
                var nl = '\\r\\n'; //newline
                if (resp!='OK') { alert('$T_Error_in portfolio.php pfRemovePermit'+nl+nl+resp+nl); return; }
                 else           { permitRow.parentNode.removeChild(permitRow); }
            }
            xhr.open('GET', 'ajax/pfRemovePermit.php?permitId='+permitId);
            xhr.send();
        }

        function toggleUnitEdit(pfu) {
            rowEl = document.getElementById('pfuRow'+pfu);
            rowEl.classList.toggle('edit');
        }

        function pfuLadd(pfu) {
            var xhr = new XMLHttpRequest();
            var inputEl = document.getElementById('pfuLnew'+pfu);
            var newText = inputEl.value.trim();
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var nl = '\\r\\n'; //newline
                    var resp = this.responseText;
                    var found = resp.match(/^OK:(\d+)$/)
                    if (!found) { alert('$T_Error_in pfuLadd.php'+nl+nl+resp+nl); return; }
                    var newLI = document.createElement('li');
                    var pfuL = found[1];
                    newLI.id = 'pfuL' + pfuL;
                    newLI.innerHTML = '<span id=pfuLtext' + pfuL + ' onKeypress=keypress(event)>' + newText + '</span> ' + "$LitemEditHtml";
                    document.getElementById('pfuLul'+pfu).appendChild(newLI);
                    inputEl.value = '';
                }
            }
            var formData = new FormData();
            formData.append('pfu',pfu);
            formData.append('newText',newText);
            xhr.open('POST', 'ajax/pfuLadd.php'); //Safer to use POST in case of rubbish in the text
            xhr.send(formData);
        }

        function pfuWadd(pfu) {
            var xhr = new XMLHttpRequest();
            var workEl = document.getElementById('pfuWnewWork'+pfu);
            var urlEl  = document.getElementById('pfuWnewURL'+pfu);
            var newWork = workEl.value.trim();
            var newURL  = urlEl.value.trim();
            var nl = '\\r\\n'; //newline
            var validURLpattern = new RegExp('^http://|^https://','i');
            if ( newURL!='' && !validURLpattern.test(newURL) ) {
                if ( ! /\//.test(newURL) ) { newURL = '/' + newURL; } //Add a leading / to newURL if necessary
                newURL = 'http:/' + newURL;
                urlEl.value = newURL;
                var message = '$T_A_URL_must_begin_with_'.replace('{http}','http://').replace('{https}','https;//') + nl+nl
                            + '$T_Have_changed_URL_to_' + nl+nl + newURL + nl+nl 
                            + '$T_Is_this_OK';
                if (!confirm(message)) { return; }
            }
            if (newWork=='' || newURL=='') return;
            xhr.onreadystatechange = function() {
                if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                    var resp = this.responseText;
                    var found = resp.match(/^OK:(\d+)$/)
                    if (!found) { alert('$T_Error_in pfuWadd.php'+nl+nl+resp+nl); return; }
                    var newLI = document.createElement('li');
                    newLI.id = 'pfuW' + found[1];
                    newLI.innerHTML = '<a href="' + newURL + '">' + newWork + '</a> ' + "$itemEditHtml";
                    document.getElementById('pfuWul'+pfu).appendChild(newLI);
                    workEl.value = '';
                    urlEl.value  = '';
                }
            }
            var formData = new FormData();
            formData.append('pfu',pfu);
            formData.append('newWork',newWork);
            formData.append('newURL',newURL);
            xhr.open('POST', 'ajax/pfuWadd.php'); //Safer to use POST in case of rubbish in the text
            xhr.send(formData);
        }

        function itemDelete(el) {
            var liEl = el.closest('li');
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                var resp = this.responseText;
                if (resp!='OK') { alert('$T_Error_in pfItemDelete.php\\r\\n\\r\\n'+resp); return; }
                liEl.parentNode.removeChild(liEl);
            }
            xhr.open('GET', 'ajax/pfItemDelete.php?liId='+liEl.id);
            xhr.send();
        }

        function LitemEdit(el) {
            var liEl = el.closest('li');
            var pfuL = liEl.id.substring(4);
            var textEl = document.getElementById('pfuLtext'+pfuL);
            liEl.classList.add('editing');
            textEl.setAttribute('contenteditable','true');
        }

        function LitemSave(el) {
            var liEl = el.closest('li');
            var pfuL = liEl.id.substring(4);
            var textEl = document.getElementById('pfuLtext'+pfuL);
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                var resp = this.responseText;
                if (resp!='OK') { alert('$T_Error_in pfuLsave.php\\r\\n\\r\\n'+resp); return; }
                liEl.classList.remove('editing');
                textEl.setAttribute('contenteditable','false');
            }
            var formData = new FormData();
            formData.append('pfuL',pfuL);
            formData.append('text',textEl.innerText);
            xhr.open('POST', 'ajax/pfuLsave.php'); //Safer to use POST in case of rubbish in the text
            xhr.send(formData);
        }

        function keypress(event,el) {
            if (event.keyCode === 13) {
                event.preventDefault();
                LitemSave(el);
            }
        }

        function moveItem(el,direction) {
            var liEl = el.closest('li');
            if       (direction=='up')   { var swopEl = liEl.previousElementSibling; }
             else if (direction=='down') { var swopEl = liEl.nextElementSibling;     }
             else { alert('$T_Error_in moveItem. Invalid direction:'+direction); }
            if (swopEl == null) { return; } //This shouldn’t happen anyway
            var id = liEl.id;
            var swopId = swopEl.id;
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                var resp = this.responseText;
                if (resp!='OK') { alert('$T_Error_in pfItemSwop.php\\r\\n\\r\\n'+resp); return; }
                if (direction=='up') { liEl.parentNode.insertBefore(liEl,swopEl); }
                 else                { liEl.parentNode.insertBefore(swopEl,liEl); }
            }
            xhr.open('GET', 'ajax/pfItemSwop.php?id='+id+'&swopId='+swopId);
            xhr.send();
        }

        function moveUnit(el,direction) {
            var trEl = el.closest('tr');
            if       (direction=='up')   { var swopEl = trEl.previousElementSibling; }
             else if (direction=='down') { var swopEl = trEl.nextElementSibling;     }
             else { alert('$T_Error_in moveItem. Invalid direction:'+direction); }
            if (swopEl == null) { return; } //This shouldn’t happen anyway
            var id = trEl.id;
            var swopId = swopEl.id;
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                var resp = this.responseText;
                if (resp!='OK') { alert('$T_Error_in pfItemSwop.php\\r\\n\\r\\n'+resp); return; }
                if (direction=='up') { trEl.parentNode.insertBefore(trEl,swopEl); }
                 else                { trEl.parentNode.insertBefore(swopEl,trEl); }
            }
            xhr.open('GET', 'ajax/pfUnitSwop.php?id='+id+'&swopId='+swopId);
            xhr.send();
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
