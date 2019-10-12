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
    <title>Clilstore options for user</title>
    <link rel="stylesheet" href="/css/smo.css">
    <link rel="stylesheet" href="style.css?version=2014-04-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        div.message { margin:0.5em 0; font-weight:bold; }
        fieldset.opts { border:2px solid #61abec; border-radius:6px; padding:6px; margin-bottom:2em; }
        fieldset.opts legend { border:2px solid #61abec; border-radius:4px; padding:1px 4px; background-color:#eef; color:#55a8eb; font-weight:bold; }
        span.info { color:green; font-size:70%; }
        table#opttab tr td:first-child { text-align:right; vertical-align:top; }
        table#transferTable { border-collapse:collapse; }
        table#transferTable td { padding:5px; }
        span.change { opacity:0; color:white; }
        span.change.changed { color:green; animation:appearFade 5s; }
        @keyframes appearFade { from { opacity:1; background-color:yellow; } 20% { opacity:0.8; background-color:white; to { opacity:0; } }
    </style>
    <script>
        function changeUserOption(user,option,value) {
            if (option=='fullname' && value.length<8) { alert('Not changed. Invalid: Not long enough'); return; }
            if (option=='email' && !/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/.test(value)) { alert('Not changed. This is not a valid email address'); return; }
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onload = function() {
                if (this.status!=200 || this.responseText.substring(0,2)!='OK') { alert('Error in changeUserOption: '+this.responseText); return; }
                if (this.responseText=='OK-null') { return; }
                if (option=='email') {
                    vmEl = document.getElementById('verMess');
                    newEl = document.createElement('span');
                    newEl.appendChild(document.createTextNode("Unverified"));
                    newEl.style.color = 'red';
                    vmEl.parentNode.replaceChild(newEl,vmEl);
                    alert('Your have changed your email address.  You will need to reverify it.');
                    location.reload();
                }
                var el = document.getElementById(option+'Changed');
                el.classList.remove('changed'); //Remove the class (if required) and add again after a tiny delay, to restart the animation
                setTimeout(function(){el.classList.add('changed');},50);
            }
            xmlhttp.open('GET', 'ajax/changeUserOption.php?user=' + user + '&option=' + option + '&value=' + value);
            xmlhttp.send();
        }
        function verifyEmail(email) {
            var emailEnc = encodeURIComponent(email);
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onload = function() {
                if (this.status!=200 || this.responseText!='OK') { alert('Error in verifyEmail: '+this.responseText); return; }
            }
            xmlhttp.open('GET', 'ajax/verifyEmail.php?email=' + emailEnc);
            xmlhttp.send();
            alert('An email has been sent to ' + email + ' to request confirmation of the address.  Remember to check for it in your spam folder too.');
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
    if ($loggedinUser<>$user && $loggedinUser<>'admin') { throw new SM_MDexception('sgrios|bog|Attempt to change another user’s options<br>'
                                 . "You are logged in as $loggedinUser and have attempted to change the options for $userSC"); }

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $errorMessage = $successMessage = $transferHtml = '';

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

    $stmt = $DbMultidict->prepare('SELECT fullname,email,emailVerUtime,unitLang,highlightRow,record FROM users WHERE user=:user');
    $stmt->execute(array('user'=>$user));
    if (!($row = $stmt->fetch(PDO::FETCH_ASSOC))) { throw new SM_MDexception("Failed to fetch information on user $userSC"); }
    extract($row);
    $fullnameSC = htmlspecialchars($fullname);
    $emailSC    = htmlspecialchars($email);
    if ($emailVerUtime==0) {
        $verMessage = "<span id=verMess style='color:red'>Unverified</span>";
        $verLink = 'Verify now';
    } else {
        date_default_timezone_set('UTC');
        $verTimeObj = new DateTime("@$emailVerUtime");
        $verDateTime = date_format($verTimeObj, 'Y-m-d H:i:s');
        $verMessage = "<span id=verMess title='Verified at $verDateTime UT'><span style='color:green'>✓</span> Verified</span>";
        $verLink = 'Reverify';
    }
    $verLink = "<a class=button style='padding:0 10px;margin-left:0.5em;font-weight:normal' onclick=verifyEmail('$email')>$verLink</a>";
    $verMessage = "<span style='font-size:80%;padding-left:0.8em'>$verMessage $verLink</span>";

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

<p style="margin:1.7em 0">
<a class="button" href="voc.php?user=$user" style="margin:1em 3em 1em 1.5em">My vocabulary…</a>
<a class="button" href="changePassword.php?user=$user">Change password…</a>
</p>

<form method="POST" id=optForm>
<fieldset class="opts">
<legend>Options</legend>
<table id="opttab">
<tr><td>Default language code for units you create</td><td>
<select name="unitLang" onchange="changeUserOption('$user','unitLang',this.value)">
$unitLangHtml
</select>
<span id=unitLangChanged class="change">✔ changed<span>
</td></tr>
<tr><td>Highlight row of Clilstore index on mouse hover?</td><td>
<select name="highlightRow" onchange="changeUserOption('$user','highlightRow',this.value)">
$highlightRowHtml
</select>
<span id=highlightRowChanged class="change">✔ changed</span>
</td></tr>
<tr><td>Add words you click to your vocabulary list?</td><td>
<select name="record" onchange="changeUserOption('$user','record',this.value)">
$recordHtml
</select>
<span id=recordChanged class="change">✔ changed</span>
</td></tr>
<tr><td>Full name</td><td><input value="$fullnameSC" pattern=".{8,}" title="Your real name (visible to other users)"  style="width:22em" onchange="changeUserOption('$user','fullname',this.value)">
<span id=fullnameChanged class="change">✔ changed</span>
</td></tr>
<tr><td>Email address</td><td><input value="$emailSC" style="width:22em" pattern="^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" onchange="changeUserOption('$user','email',this.value)">
<span id=emailChanged class="change">✔ changed</span>
<br>$verMessage
</td></tr>
</table>
</fieldset>
</form>

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
