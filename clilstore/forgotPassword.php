<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");
  header("Cache-Control:no-cache,must-revalidate");

  try {
    $myCLIL = SM_myCLIL::singleton();

    $T = new SM_T('clilstore/forgotPassword');

    $T_Email           = $T->h('E-mail');
    $T_Send_reset_link = $T->h('Send_reset_link');
    $T_No_such_user    = $T->h('No_such_user');

    $T_Forgotten_your_password           = $T->h('Forgotten_your_password');
    $T_forgotPassword_email_request_info = $T->h('forgotPassword_email_request_info');
    $T_forgotPassword_junkmail_reminder  = $T->h('forgotPassword_junkmail_reminder');
    $T_email_registered_with_Clilstore   = $T->h('email_registered_with_Clilstore');
    $T_or_your_userid                    = $T->h('or_your_userid');
    $T_Link_sent_confirmation            = $T->h('Link_sent_confirmation');

    $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

    echo <<<EOD_barr
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>$T_Forgotten_your_password</title>
    <link rel="stylesheet" href="/css/smo.css">
    <link rel="stylesheet" href="style.css?version=2014-04-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        div.errorMessage { color:red; font-weight:bold; margin:1em 0; }
        span.info { color:green; font-size:70%; font-style:italic; }
    </style>
</head>
<body>

$mdNavbar
<div class="smo-body-indent">

<h1 style="margin-bottom:0.2em">$T_Forgotten_your_password</h1>
<p style="margin-top:0.2em">$T_forgotPassword_email_request_info<br>&nbsp;$T_forgotPassword_junkmail_reminder</p>

EOD_barr;

    $formRequired = TRUE;
    $errorMessage = '';
    $findme = ( isset($_REQUEST['findme']) ? trim($_REQUEST['findme']) : ''); 
    $findmeSC = htmlspecialchars($findme);
    if (isset($_POST['sendLink']) && !empty($findme)) {
        $DbMultidict = SM_DbMultidictPDO::singleton('r');
        $stmt = $DbMultidict->prepare('SELECT user,email FROM users WHERE (user=:user OR email=:email) AND email IS NOT NULL');
        $stmt->execute(array('user'=>$findme,'email'=>$findme));
        if (!($row=$stmt->fetch())) {
            $errorMessage = $T_No_such_user;
        } else {
            $user = $row['user'];
            $email= $row['email'];
            $utime = time();
            $md5 = md5("moSgudal$user$utime");
            $servername = SM_myCLIL::servername();
            $link = "http://$servername/clilstore/changePassword.php?user=$user&t=$utime&md5=$md5";
            mail($email,'Clilstore - link to reset password (valid for 24 hours)',$link,"From:no-reply@multidict.net\r\n");
            echo "<p style='color:green'><span style='font-size:200%'>✔</span> $T_Link_sent_confirmation</p>\n";
            $formRequired = FALSE;
        }
    }

    if ($formRequired) {
        echo <<<ENDform
<div class="errorMessage">$errorMessage</div>
<form method="POST">
<table style="margin:2em 0 1em 0">
<tr style="vertical-align:top">
 <td>$T_Email</td>
 <td><input name="findme" value="$findmeSC" required autofocus style="width:21em"></td>
 <td rowspan=2 style="color:green">$T_email_registered_with_Clilstore <span style="font-size:85%;font-style:italic">($T_or_your_userid)</span></td>
</tr>
<tr><td></td><td><input name="sendLink" type="submit" value="$T_Send_reset_link"></td></tr>
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
