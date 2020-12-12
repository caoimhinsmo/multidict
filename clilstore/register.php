<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  $T = new SM_T('clilstore/register');

  $T_UserID   = $T->h('UserID');
  $T_Fullname = $T->h('Fullname');
  $T_Email    = $T->h('E-mail');
  $T_Password = $T->h('Password');
  $T_Register = $T->h('Register');
  $T_Login    = $T->h('Log_air'); 
  $T_Retype_password  = $T->h('Retype_password');
  $T_UserID_advice    = $T->h('UserID_advice');
  $T_Fullname_advice  = $T->h('Fullname_advice');
  $T_Email_advice     = $T->h('Email_advice');
  $T_Password_advice  = $T->h('Password_advice');
  $T_Retype_pw_advice = $T->h('Retype_pw_advice');
  $T_Privacy_policy   = ucfirst($T->h('privacy_policy'));
  $T_Choose_unique_userid    = $T->h('Choose_unique_userid');
  $T_Register_on_Clilstore   = $T->h('Register_on_Clilstore');
  $T_Not_a_valid_email       = $T->h('Not_a_valid_email');
  $T_User_exists_for_email_1 = $T->h('User_exists_for_email_1');
  $T_User_exists_for_email_2 = $T->h('User_exists_for_email_2');
  $T_User_exists_for_email_3 = $T->h('User_exists_for_email_3');
  $T_User_already_taken      = $T->h('User_already_taken');
  $T_Retyped_pw_mismatch     = $T->h('Retyped_pw_mismatch');
  $T_Userid_successfully_reg = $T->h('Userid_successfully_reg');
  $T_You_may_now_            = $T->h('You_may_now_');

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
    <title>$T_Register_on_Clilstore</title>
    <link rel="stylesheet" href="/css/smo.css">
    <link rel="stylesheet" href="style.css?version=2014-04-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        span.info { color:green; font-size:70%; }
        table#formTable td:first-child { text-align:right; }
        a.button { background-color:#55a8eb; color:white; font-weight:bold; padding:3px 10px; border:0; border-radius:8px; }
        a.button:hover { background-color:blue; }
    </style>
</head>
<body>
$mdNavbar
<div class="smo-body-indent">

<h1>$T_Register_on_Clilstore</h1>
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

        if ( $user <> strtr($user,'^!£$%^&"*()=+{}[]\\/|:;\'@~#<>,?',
                                  '________________________________') ) {
            $errorMessage = 'This username contains banned punctuation characters';
        } elseif (empty($fullname) || strlen($fullname)<8) {
            $errorMessage = 'You have not given your full name';
        } elseif (empty($email)) {
            $errorMessage = 'You have not given your e-mail address';
        } elseif (validEmail($email)==0) {
            $errorMessage = $T_Not_a_valid_email;
        } elseif ($stmtEmail->execute() && $stmtEmail->bindColumn(1,$prevUser) && $stmtEmail->fetch()) {
            $User_exists_for_email_1 = strtr( $T_User_exists_for_email_1, ['[xxxxxx]' => "<b>$prevUser</b>"] );
            $User_exists_for_email_2 = strtr( $T_User_exists_for_email_2,
                                             ['[xxxxxx]' => "<b>$prevUser</b>",
                                              '{' => "<a href=\"login.php?user=$prevUser\" style='border:1px solid;border-radius:3px;padding:0 3px'>",
                                              '}' => '</a>'] );
            $User_exists_for_email_3 = strtr( $T_User_exists_for_email_3, ['{'=>'<i>','}'=>'</i>'] );
            $errorMessage = "$User_exists_for_email_1<br>$User_exists_for_email_2<br><br><br>$User_exists_for_email_3";
        } elseif ($stmtUser->execute() && $stmtUser->fetch()) {
            $errorMessage = strtr( $T_User_already_taken, ['[xxxxxx]' => "<b>$userSC</b>"] );
        } elseif (strlen($user)<3) {
            $errorMessage = 'Userids must be at least 3 characters long.  You’ll have to choose something longer.';
        } elseif (strlen($user)>16) {
            $errorMessage = 'Userids cannot be over 16 characters long.  You’ll have to choose something shorter.';
        } elseif ($password<>$password2) {
            $errorMessage = $T_Retyped_pw_mismatch;
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
            $T_Userid_successfully_reg = strtr($T_Userid_successfully_reg, ['{#userid}'=>"<b>$userSC</b>"]);
            echo <<<ENDsuccess
<p>$T_Userid_successfully_reg.</p>
<P>$T_You_may_now_ <a class=button href='login.php?user=$userSC&amp;returnTo=/clilstore/'>$T_Login</a></p>
ENDsuccess;
            $formRequired = 0;
        }
    }

    if ($formRequired) { echo <<<ENDform
<div style="color:red">$errorMessage</div>
<form method="POST">
<table id=formTable style="margin-bottom:2em">
<tr><td>$T_UserID</td><td><input name="user" value="$userSC" required pattern="[^!£$%^&amp;&quot;*()=+{}\[\]\\\/|:;'@~#<>,?]{3,16}" autofocus placeholder="$T_Choose_unique_userid" style="width:16em"> <span class="info">$T_UserID_advice</span></td></tr>
<tr><td>$T_Fullname</td><td><input name="fullname" value="$fullnameSC" required pattern=".{8,}"   style="width:22em"> <span class="info">$T_Fullname_advice</span></td></tr>
<tr><td>$T_Email</td><td><input type="email" name="email" value="$emailSC" style="width:22em"> <span class="info">$T_Email_advice</span></td></tr>
<tr><td>$T_Password</td><td><input type="password" name="password"  value="$passwordSC"  required pattern=".{8,}"> <span class="info">$T_Password_advice</span></td></tr>
<tr><td>$T_Retype_password</td><td><input type="password" name="password2" value="$password2SC" required pattern=".{8,}"> <span class="info">$T_Retype_pw_advice</span></td></tr>
<tr><td></td><td><input type="submit" value="$T_Register"></td></tr>
</table>
</form>
ENDform;
    }

    echo <<<EOD2
<p style="margin:5em 0 0 0;font-size:85%"><a href="privacyPolicy.php">⇒ $T_Privacy_policy</a></p>
</div>
<ul class="smo-navlist">
<li><a href="./">Clilstore</a></li>
</ul>

</body>
</html>
EOD2;

  } catch (Exception $e) { echo $e; }

?>
