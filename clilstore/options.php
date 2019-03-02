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

  echo <<<EOD_BARR
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User vocabulary on Clilstore</title>
    <link rel="stylesheet" href="/css/smo.css" type="text/css">
    <link rel="stylesheet" href="style.css?version=2014-04-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style type="text/css">
        div.message { margin:0.5em 0; font-weight:bold; }
        fieldset.opts { border:2px solid #61abec; border-radius:6px; padding:6px; margin-bottom:2em; }
        fieldset.opts legend { border:2px solid #61abec; border-radius:4px; padding:1px 4px; background-color:#eef; color:#55a8eb; font-weight:bold; }
        span.info { color:green; font-size:70%; }
        table#opttab tr td:first-child { text-align:right; }
        table#transferTable { border-collapse:collapse; }
        table#transferTable td { padding:5px; }
    </style>
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
    if ($loggedinUser<>$user && $loggedinUser<>'admin') { throw new SM_MDexception('sgrios|bog|Attempt to change another user’s options<br>'
                                 . "You are logged in as $loggedinUser and have attempted to change the options for $userSC"); }

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $errorMessage = $successMessage = $transferHtml = '';

    if (!empty($_REQUEST['save'])) {
        $unitLang     = $_REQUEST['unitLang'];
        $highlightRow = $_REQUEST['highlightRow'];
        $record       = $_REQUEST['record'];
        $stmt0 = $DbMultidict->prepare('SELECT id FROM lang WHERE id=:id');
        $stmt0->execute(array('id'=>$unitLang));
        if (!($stmt0->fetch()) && !empty($unitLang)) {
            $unitLangSC = htmlspecialchars($unitLang);
            $errorMessage = "Clilstore does not recognise language code “<b>$unitLangSC</b>”";
        } else {
            $stmt1 = $DbMultidict->prepare('UPDATE users SET unitLang=:unitLang, highlightRow=:highlightRow, record=:record WHERE user=:user');
            if (!$stmt1->execute(array('unitLang'=>$unitLang,'highlightRow'=>$highlightRow,':record'=>$record,'user'=>$user))) { throw new SM_MDexception('Failed to update database'); }
            $successMessage = 'Changes saved';
//            SM_csSess::logWrite($user,'options',"Changed options to unitLang=$unitLang, highlightRow=$highlightRow");  //Delete sometime
        }
    }

    if (!empty($_REQUEST['trResponse'])) {
        if (!empty($_REQUEST['trId'])) { $trId = $_REQUEST['trId']; } else { throw new SM_MDexception('Missid parameter trId'); }
        $trResponse = $_REQUEST['trResponse'];
        $trId       = $_REQUEST['trId'];
        if ($trResponse=='Reject') {
            $stmt = $DbMultidict->prepare('UPDATE clilstore SET offerTime=0 WHERE id=:id AND offer=:offer');
            $stmt->execute(array(':id'=>$trId,':offer'=>$user));
            SM_csSess::logWrite($user,'offerReject',"User $user rejected the offer of unit $trId");
        } elseif ($trResponse=='Accept') {
            $stmt = $DbMultidict->prepare('UPDATE clilstore SET owner=:owner, offer=null, offerTime=0 WHERE id=:id AND offer=:offer');
            $stmt->execute(array(':owner'=>$user,':id'=>$trId,':offer'=>$user));
            SM_csSess::logWrite($user,'offerAccept',"User $user accepted the offer of unit $trId");
        }
    }

    $stmt = $DbMultidict->prepare('SELECT id,owner,title FROM clilstore WHERE offer=:offer AND offerTime<>0 ORDER BY id');
    $stmt->execute(array(':offer'=>$user));
    $transfers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $ntransfers = sizeof($transfers);
    foreach ($transfers as $tr) {
        $trId    = $tr->id;
        $trOwner = $tr->owner;
        $trTitle = $tr->title;
        $transferHtml .= "<tr>"
                       . "<td><a href='/cs/$trId'>$trId</a></td>"
                       . "<td><a href='userinfo.php?user=$trOwner'>$trOwner</a></td>"
                       . "<td><a href='/cs/$trId'>$trTitle</a></td>"
                       . "<td><form method='POST' class='transfer'>"
                          . " <input type='hidden' name='trId' value='$trId'>"
                          . " <input type='hidden' name='user' value='$user'>"
                          . " <input type='submit' name='trResponse' value='Accept' title='Accept ownership of unit $trId'>"
                          . " <input type='submit' name='trResponse' value='Reject' title='Reject the offer'>"
                          . "</form></td>"
                       . "</tr>\n";
    }
    if (!empty($transferHtml)) {
        $legend = ( $ntransfers==1 ? 'The following unit is on offer to you' : 'The following units are on offer to you' );
        $transferHtml = <<<EODtransferHtml
<fieldset class="opts">
<legend>Ownership transfer - $legend</legend>
<table id="transferTable">
<tr style="font-weight:bold;background-color:#eee"><td>unit</td><td>owner</td><td>title</td><td></td></tr>
$transferHtml
</table>
<span class="info">If you accept a unit, you accept responsibility for ensuring it does not infringe copyright</span>
</fieldset>
EODtransferHtml;
    }

    $stmt2 = $DbMultidict->prepare('SELECT unitLang,highlightRow,record FROM users WHERE user=:user');
    $stmt2->execute(array('user'=>$user));
    if (!($row = $stmt2->fetch(PDO::FETCH_ASSOC))) { throw new SM_MDexception("Failed to fetch information on user $userSC"); }
    extract($row);
//    $unitLang     = $row->unitLang;
//    $highlightRow = $row->highlightRow;
    

    function optionsHtml($valueOptArr,$selectedValue) {
     //Creates the options html for a select in a form, based on value=>text array and the value to be selected
        $htmlArr = array();
        foreach ($valueOptArr as $value=>$option) {
            $selected = ( $value==$selectedValue ? ' selected=selected' : '' );
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
    $recordArr = array(
         '0' => 'No',
         '1' => 'Yes');
    $recordHtml = optionsHtml($recordArr,$record);

    $errorMessage   = ( empty($errorMessage)   ? '' : '<div class="message" style="color:red">' . $errorMessage   . '<br>No changes saved</div>' );
    $successMessage = ( empty($successMessage) ? '' : '<div class="message" style="color:green"><span style="font-size:200%">✔</span> ' . $successMessage . '</div>' );

    echo <<<ENDform
<h1 class="smo">Clilstore options for user <span style="color:brown">$user</span></h1>

$errorMessage
$successMessage

<p style="margin:1.7em 0"><a class="button" href="changePassword.php?user=$user">Change password…</a>

<form method="POST">
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
<tr><td>Add words you click to your vocabulary list?</td><td>
<select name="record">
$recordHtml
</select>
</td></tr>
<tr><td><input type ="hidden" name="user" value="$userSC">
</td><td><input type="submit" name="save" value="Save" style="font-size:120%"></td></tr>
</table>
</fieldset>
</form>

<p style="margin:1.7em 0"><a class="button" href="voc.php?user=$user">My vocabulary…</a>

$transferHtml
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
