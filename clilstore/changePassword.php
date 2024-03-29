<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header('Cache-Control:max-age=0');

  $T = new SM_T('clilstore/changePassword');
  $T_Change_password = $T->h('Change_password');
  $T_Old_password    = $T->h('Old_password');
  $T_New_password    = $T->h('New_password');
  $T_Retype_password = $T->h('Retype_password');
  $T_Retype_new_password      = $T->h('Retype_new_password');
  $T_Retype_to_confirm        = $T->h('Retype_to_confirm');
  $T_Change_password_for_user = $T->h('Change_password_for_user_');
  $T_Retyped_pw_mismatch      = $T->h('Retyped_pw_mismatch');
  $T_Old_password_missing     = $T->h('Old_password_missing');
  $T_Old_password_incorrect   = $T->h('Old_password_incorrect');
  $T_No_new_password          = $T->h('No_new_password');
  $T_No_retyped_new_password  = $T->h('No_retyped_new_password');

  $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

  try {

    echo <<<EOD_barr
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>$T_Change_password</title>
    <link rel="stylesheet" href="/css/smo.css">
    <link rel="stylesheet" href="style.css?version=2014-04-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        div.errorMessage { margin:0.5em 0; color:red; font-weight:bold; }
        span.info { color:green; font-size:70%; }
        input { width:17em; }
    </style>
</head>
<body>
$mdNavbar
<div class="smo-body-indent">
EOD_barr;

    $myCLIL = SM_myCLIL::singleton();
    if (isset($_REQUEST['user'])) { $user = $_REQUEST['user']; }
     else { throw new SM_MDexception('This page has been called without the required ‘user=’ parameter'); }
    $userSC = htmlspecialchars($user);
    if (isset($_GET['md5'])) {
        $vialink = TRUE;
        $md5       = $_GET['md5'];
        $timestamp = $_GET['t'];
        if (md5("moSgudal$user$timestamp")<>$md5) { throw new SM_MDexception('Corrupt link.  Not authorised.'); }
        if (time()-$timestamp > 86400) { throw new SM_MDexception('The link is stale (over one day old).  You’ll have to go to Clilstore and request another password reset link.'); }
    } elseif (!empty($myCLIL)) {
        $vialink = FALSE;
        $loggedinUser = $myCLIL->id;
        if ($loggedinUser<>$user && $loggedinUser<>'admin') { throw new SM_MDexception('Attempt to change another user’s password<br>'
                                     . "You are logged in as $loggedinUser and have attempted to change the password of $userSC"); }
    } else {
        throw new SM_MDexception('You are not logged in and have no authorisation');
    }

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $errorMessage = $oldpass = $password = $password2 = '';
    $formRequired = 1;

    $stmt = $DbMultidict->prepare('SELECT password FROM users WHERE user=:user');
    $stmt->execute(array('user'=>$user));
    if (!($row = $stmt->fetch())) { throw new SM_MDexception("User $user does not exist"); }
    $stmt = null;

    if (!empty($_POST['change'])) {
        $oldpass   = @$_POST['oldpass'];
        $password  = @$_POST['password'];
        $password2 = @$_POST['password2'];

        if (!$vialink && empty($oldpass)) {
            $errorMessage = $T_Old_password_missing;
        } elseif (!$vialink && !($loggedinUser=='admin' && substr($oldpass,3,3)=='Lin') && (crypt($oldpass,$row['password']) <> $row['password'])) {
            $errorMessage = $T_Old_password_incorrect;                                 //Crude functionality for admin - improve sometime
        } elseif (empty($password)) {
            $errorMessage = 'You have not specified a new password';
        } elseif (empty($password2)) {
            $errorMessage = 'You have not retyped your new password';
        } elseif ($password<>$password2) {
            $errorMessage = $T_Retyped_pw_mismatch;
        } else {
            $passwordCrypt = crypt($password,'$2a$07$rudeiginLanLanAmaideach');
            $stmt = $DbMultidict->prepare('UPDATE users SET password =:pw WHERE user=:user');
            if (!$stmt->execute(array('pw'=>$passwordCrypt,'user'=>$user))) { throw new SM_MDexception('Failed to update password'); }
            echo <<<ENDsuccess
<p>Your password has been changed.</p>
<p>You may now <a href="login.php?user=$user">login</a>.</p>
ENDsuccess;
            $formRequired = 0;
        }
    }


    if ($formRequired) {
        if ($vialink) {
            $oldpassRow = '';
        } else {
            $oldpassSC   = htmlspecialchars($oldpass);
            $oldpassRow = "<tr><td>$T_Old_password:</td><td><input type=password name=oldpass value='$oldpassSC' required</td></tr>";
        }
        $passwordSC  = htmlspecialchars($password);
        $password2SC = htmlspecialchars($password2);
        echo <<<ENDform
<h1 class="smo">$T_Change_password_for_user $user</h1>
<div class="errorMessage">$errorMessage</div>

<form method="POST">
<table style="margin-bottom:2em">
$oldpassRow
<tr style="height:0.5em"></tr>
<tr style="height:0.5em"></tr>
<tr><td>$T_New_password:</td><td><input type="password" name="password"  value="$passwordSC"  required pattern=".{8,}"></td></tr>
<tr><td>$T_New_password:</td><td><input type="password" name="password2" value="$password2SC" required pattern=".{8,}" placeholder="$T_Retype_to_confirm"> <span class="info">$T_Retype_new_password</span></td></tr>
<tr style="height:0.7em"></tr>
<tr><td><input type="hidden" name="user" value="$userSC"></td><td><input type="submit" name="change" value="$T_Change_password"></td></tr>
</table>
</form>
ENDform;
    }

  } catch (Exception $e) { echo $e; }

    echo <<<EOD_bonn
</div>
$mdNavbar
</body>
</html>
EOD_bonn;

?>
