<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");
  header("Cache-Control:no-cache,must-revalidate");

  try {
    $myCLIL = SM_myCLIL::singleton();
    $csSess = SM_csSess::singleton();
    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $servername = SM_myCLIL::servername();
    $serverhome = SM_myCLIL::serverhome();

    $T = new SM_T('clilstore/login');
    $T_Email    = $T->h('E-mail');
    $T_UserID   = $T->h('UserID');
    $T_Password = $T->h('Password');
    $T_Login    = $T->h('Log_air');
    $T_Register = $T->h('Register');
    $T_Go_to    = $T->h('Go_to');
    $T_No_account_yet          = $T->h('No_account_yet');
    $T_Forgotten_your_password = $T->h('Forgotten_your_password');
    $T_Recover_it              = $T->h('Recover_it');
    $T_Login_to_Clilstore      = $T->h('Login_to_Clilstore');
    $T_Successfully_logged_in  = $T->h('Successfully_logged_in');
    $T_Userid_or_pw_incorrect  = $T->h('Userid_or_pw_incorrect');

    $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

    $formRequired = TRUE;
    $successMessage = $refreshHeader = $formHtml = '';
    $userAsTyped = $passwordAsTyped = $userAutofocus = $passwordAutofocus = '';

    if      (!empty($_REQUEST['returnTo']))    { $refreshURL = $_REQUEST['returnTo'];
                                                   if (substr($refreshURL,0,4)<>'http') { $refreshURL = $serverhome . $refreshURL; }
                                               }
     elseif (!empty($_SERVER['HTTP_REFERER'])) { $refreshURL = $_SERVER['HTTP_REFERER']; }
     else                                      { $refreshURL = "$serverhome/clilstore"; }

    if (!empty($csSess->getCsSession()->user)) { $userAsTyped = $csSess->getCsSession()->user; }
    if (!empty($_REQUEST['user'])) {
        $userAsTyped     = trim($_REQUEST['user']);
        $passwordAsTyped = $_POST['password'] ?? '';
        $stmt1 = $DbMultidict->prepare('SELECT user,password,csid FROM users WHERE user=:user OR email=:email');
        $stmt1->bindParam(':user',$userAsTyped);
        $stmt1->bindParam(':email',$userAsTyped);
        $stmt1->bindColumn(1,$user);
        $stmt1->bindColumn(2,$password);
        $stmt1->bindColumn(3,$csid);
        if  ($stmt1->execute()
          && $stmt1->fetch()
          && (crypt($passwordAsTyped,$password)==$password || $password=='')) {
           //Copy filter parameters from the most recent previous Clilstore session (if any) for this user. Remember the new csid.
            $newCsid = $_COOKIE['csSessionId'];
            if (!empty($csid)) {
                $stmt2 = $DbMultidict->prepare('REPLACE INTO csFilter(csid,fd,m0,m1,m2,m3,sortpri,sortord,val1,val2)'
                                                        . ' SELECT :newCsid,fd,m0,m1,m2,m3,sortpri,sortord,val1,val2 FROM csFilter WHERE csid=:csid');
                $stmt2->execute([':newCsid'=>$newCsid,':csid'=>$csid]);
                $stmt3 = $DbMultidict->prepare('SELECT mode from csSession WHERE csid=:csid');
                $stmt3->execute([':csid'=>$csid]);
                $oldMode = $stmt3->fetchColumn();
                $csSess->setmode($oldMode);
            }
            $stmt4 = $DbMultidict->prepare('UPDATE users SET csid=:newCsid WHERE user=:user');
            $stmt4->execute([':newCsid'=>$newCsid,':user'=>$user]);
           //Create cookie
            $cookieDomain = $servername;
            if (preg_match('|www\d*\.(.*)|',$cookieDomain,$matches)) { $cookieDomain = $matches[1]; }   // Remove www., www2., etc. e.g. www2.smo.uhi.ac.uk->smo.uhi.ac.uk
            $myCLIL::cuirCookie('myCLIL_authentication',$user,0,108000); //Cookie expires at session end, or max 30 hours
            $csSess->setUser($user);  //Remember $user, to make the next login easier
            SM_csSess::logWrite($user,'login','multidict.net');
            $successMessage = <<<ENDsuccess
<p style="color:green"><span style="font-size:200%">✔</span> $T_Successfully_logged_in</p>
<p style="margin-left:1em">⇨ $T_Go_to <a href="./" style="font-weight:bold">Clilstore</a></p>
ENDsuccess;
            $formRequired = FALSE;
            $refreshHeader =  "<meta http-equiv=refresh content='1; url=$refreshURL'>";
        } elseif (!isset($_GET['user'])) {
            $successMessage = <<<ENDfailure
<p style="color:red">$T_Userid_or_pw_incorrect</p>
ENDfailure;
        }
    }

    if ($formRequired) {
        $userSC     = htmlspecialchars($userAsTyped);
        $passwordSC = htmlspecialchars($passwordAsTyped);
        if (empty($userSC)) { $userAutofocus = 'autofocus'; } else { $passwordAutofocus = 'autofocus'; }
        $formHtml = <<<ENDform
<form method="POST" action="/clilstore/login.php?returnTo=$refreshURL">
<table id=formTable>
<tr><td>$T_Email / $T_UserID </td><td><input name="user" value="$userSC" required $userAutofocus>
<tr><td>$T_Password</td><td><input type="password" name="password" value="$passwordSC" required $passwordAutofocus></td></tr>
<tr><td></td><td><input type="submit" value="$T_Login"></td></tr>
</table>
</form>

<div id=extra>
<p>$T_No_account_yet <a href="register.php">$T_Register</a></p>
<p>$T_Forgotten_your_password <a href="forgotPassword.php">$T_Recover_it</a></p>
</div>
ENDform;
    }
 
   echo <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>$T_Login_to_Clilstore</title>
    <link rel="stylesheet" href="/css/smo.css">
    <link rel="stylesheet" href="style.css?version=2014-04-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        table#formTable { border-collapse: collapse; }
        table#formTable td { padding:0.3em; }
        table#formTable td:first-child { text-align:right; }
        div#extra { margin-top:3em; font-size:90%; }
        div#extra p { margin:0.4em 0; }
        div#extra a { border:1px solid grey; padding:1px 3px; border-radius:3px; font-weight:bold; }
    </style>
$refreshHeader
</head>
<body>
$mdNavbar
<div class="smo-body-indent">

<h1>$T_Login_to_Clilstore</h1>

$successMessage
$formHtml

</div>
$mdNavbar
</body>
</html>
EOD;

  } catch (Exception $e) { echo $e; }

?>
