<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  $T = new SM_T('clilstore/register');
  $T_Password = $T->h('Password');
  $T_Fullname = $T->h('Fullname');

  $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

  function validEmail($email) {
  // Returns true if the email address has the email address format and the domain exists. From http://www.linuxjournal.com/article/9585
     $isValid = true;
     $atIndex = strrpos($email, "@");
     if (is_bool($atIndex) && !$atIndex) {
        $isValid = false;
     } else {
        $domain = substr($email, $atIndex+1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);
        if ($localLen < 1 || $localLen > 64) {
           $isValid = false;  //local part length exceeded
        } elseif ($domainLen < 1 || $domainLen > 255) {
           $isValid = false;  //domain part length exceeded
        } elseif ($local[0] == '.' || $local[$localLen-1] == '.') {
           $isValid = false;  //local part starts or ends with '.'
        } elseif (preg_match('/\\.\\./', $local)) {
           $isValid = false;  //local part has two consecutive dots
        } elseif (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
           $isValid = false;  //character not valid in domain part
        } elseif (preg_match('/\\.\\./', $domain)) {
           $isValid = false;  //domain part has two consecutive dots
        } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
           // character not valid in local part unless local part is quoted
           if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) { $isValid = false; }
        }
        if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
           $isValid = false;  //domain not found in DNS
        }
     }
     return $isValid;
  }

  try {
    echo <<<EOD1
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register a new userid on clilstore</title>
    <link rel="stylesheet" href="/css/smo.css">
    <link rel="stylesheet" href="style.css?version=2014-04-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        span.info { color:green; font-size:70%; }
    </style>
</head>
<body>
$mdNavbar
<div class="smo-body-indent">

<h1>Register a new userid</h1>
EOD1;

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $formRequired = 1;
    $user   = $fullname   = $email   = $password   = $errorMessage = '';
    $userSC = $fullnameSC = $emailSC = $passwordSC = $password2SC  = '';
    if (!empty($_POST['user'])) {
        $user      = trim($_POST['user']);      $userSC      = htmlspecialchars($user);
        $fullname  = trim($_POST['fullname']);  $fullnameSC  = htmlspecialchars($fullname);
        $email     = trim($_POST['email']);     $emailSC     = htmlspecialchars($email);
        $password  = trim($_POST['password']);  $passwordSC  = htmlspecialchars($password);
        $password2 = trim($_POST['password2']); $password2SC = htmlspecialchars($password2);

        $stmtEmail = $DbMultidict->prepare('SELECT user FROM users WHERE email=:email');
        $stmtEmail->bindParam(':email',$email);
        $stmtUser = $DbMultidict->prepare('SELECT user FROM users WHERE user=:user');
        $stmtUser->bindParam(':user',$user);

        if (empty($fullname) || strlen($fullname)<8) {
            $errorMessage = 'You have not given your full name';
        } elseif (empty($email)) {
            $errorMessage = 'You have not given your e-mail address';
        } elseif (validEmail($email)==0) {
            $errorMessage = 'This is not a valid e-mail address';
        } elseif ($stmtEmail->execute() && $stmtEmail->bindColumn(1,$prevUser) && $stmtEmail->fetch()) {
            $errorMessage = "You already have a Clilstore userid “<b>$prevUser</b>” registered for this e-mail address.";
            $errorMessage .= "<br>You should <a href=\"login.php?user=$prevUser\" style=\"border:1px solid;border-radius:3px;padding:0 3px\">login</a> as $prevUser.";
            $errorMessage .= "<br><br><br>Or you can continue and register another Clilstore userid against a <i>different</i> e-mail address if you have one. But this is generally not recommended.";
        } elseif ($stmtUser->execute() && $stmtUser->fetch()) {
            $errorMessage = "Sorry, the userid “<b>$userSC</b>” is already taken.  You’ll have to choose something else.";
        } elseif (strlen($user)<3) {
            $errorMessage = 'Userids must be at least 3 characters long.  You’ll have to choose something longer.';
        } elseif (strlen($user)>16) {
            $errorMessage = 'Userids cannot be over 16 characters long.  You’ll have to choose something shorter.';
        } elseif ($password<>$password2) {
            $errorMessage = 'The retyped password does not match. Try again.';
        } elseif (strlen($password)<8) {  //Change sometime to more sophisticed check using PHP function crack_check.  Tried, but “pecl install crack” failed.
            $errorMessage = 'This password is too short and insecure';
        } else {
            $utime = time();
            $passwordCrypt = crypt($password,'$2a$07$rudeiginLanLanAmaideach');
            
            $stmtInsert = $DbMultidict->prepare('INSERT INTO users(user,fullname,email,joined,password) VALUES (:user,:fullname,:email,:joined,:password)');
            $stmtInsert->bindParam(':user',    $user);
            $stmtInsert->bindParam(':fullname',$fullname);
            $stmtInsert->bindParam(':email',   $email);
            $stmtInsert->bindParam(':joined',  $utime);
            $stmtInsert->bindParam(':password',$passwordCrypt);
            if (!$stmtInsert->execute()) { throw new SM_MDexception('Failed to insert user record'); }
            echo <<<ENDsuccess
<p>Userid <b>$user</b> has been successfully registered.</p>
<p>You may now <a href="login.php">login</a>.</p>
ENDsuccess;
            $formRequired = 0;
        }
    }

    if ($formRequired) { echo <<<ENDform
<div style="color:red">$errorMessage</div>
<form method="POST">
<table style="margin-bottom:2em">
<tr><td>UserID</td><td><input name="user" value="$userSC" required pattern=".{3,16}" autofocus placeholder="Choose a unique userid"> <span class="info">At least three characters long, preferably more (but not more that 16)</span></td></tr>
<tr><td>$T_Fullname</td><td><input name="fullname" value="$fullnameSC" required pattern=".{8,}" placeholder="Your full name" style="width:22em"> <span class="info">Your real name - This will be visible to other users</span></td></tr>
<tr><td>Email:</td><td><input type="email" name="email" value="$emailSC" style="width:22em"> <span class="info">This is kept private</span></td></tr>
<tr><td>$T_Password</td><td><input type="password" name="password"  value="$passwordSC"  required pattern=".{8,}"> <span class="info">Set a password (at least 8 characters long)</span></td></tr>
<tr><td>$T_Password</td><td><input type="password" name="password2" value="$password2SC" required pattern=".{8,}" placeholder="Retype to confirm"> <span class="info">Reenter the password</span></td></tr>
<tr><td></td><td><input type="submit" value="Register"></td></tr>
</table>
</form>
ENDform;
    }

    echo <<<EOD2
<p style="margin:5em 0 0 0;font-size:85%"><a href="privacyPolicy.php">⇒ Privacy policy</a></p>
</div>
<ul class="smo-navlist">
<li><a href="./">Clilstore</a></li>
</ul>

</body>
</html>
EOD2;

  } catch (Exception $e) { echo $e; }

?>
