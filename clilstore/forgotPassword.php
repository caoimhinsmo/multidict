<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");
  header("Cache-Control:no-cache,must-revalidate");

  try {
    $myCLIL = SM_myCLIL::singleton();

    echo <<<EOD_barr
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login to Clilstore</title>
    <link rel="stylesheet" href="/css/smo.css">
    <link rel="stylesheet" href="style.css?version=2014-04-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        div.errorMessage { color:red; font-weight:bold; margin:1em 0; }
        span.info { color:green; font-size:70%; font-style:italic; }
    </style>
</head>
<body>

<ul class="smo-navlist">
<li><a href="./">Clilstore</a></li>
</ul>
<div class="smo-body-indent">

<h1 style="margin-bottom:0.2em">Forgotten your password?</h1>
<p style="margin-top:0.2em">You can ask for a link to be e-mailed to you which will allow you to reset your password</p>

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
            $errorMessage = 'There’s no user with this e-mail address or userid';
        } else {
            $user = $row['user'];
            $email= $row['email'];
            $utime = time();
            $md5 = md5("moSgudal$user$utime");
            $servername = SM_myCLIL::servername();
            $link = "http://$servername/clilstore/changePassword.php?user=$user&t=$utime&md5=$md5";
            mail($email,'Clilstore - link to reset password (valid for 24 hours)',$link,"From:no-reply@multidict.net\r\n");
            echo '<p style="color:green"><span style="font-size:200%">✔</span> A link allowing you to reset your password has been sent to your e-mail address. This will be valid for 24 hours.</p>' ."\n";
            $formRequired = FALSE;
        }
    }

    if ($formRequired) {
        $userSC     = htmlspecialchars($userAsTyped);
        echo <<<ENDform
<div class="errorMessage">$errorMessage</div>
<form method="POST">
<table>
<tr style="vertical-align:top">
 <td>E-mail:</td>
 <td><input name="findme" value="$findmeSC" required utofocus style="width:21em"></td>
 <td rowspan=2 style="color:green">The e-mail address you registered with Clilstore <span style="font-size:85%;font-style:italic">(or your userid is also acceptable)</span></td>
</tr>
<tr><td></td><td><input name="sendLink" type="submit" value="Send reset link"></td></tr>
</table>
</form>
ENDform;
    }

  } catch (Exception $e) { echo $e; }

  echo <<<EOD_bonn
</div>
<ul class="smo-navlist">
<li><a href="./">Clilstore</a></li>
</ul>

</body>
</html>
EOD_bonn;
?>
