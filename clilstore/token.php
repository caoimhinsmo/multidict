<?php
  try {
    if (!include('autoload.inc.php')) { throw new Exception('Failed to find autoload.inc.php'); }
    header('Cache-Control:max-age=0');

    if (empty($_GET['token'])) { throw new Exception('This page requires a token= parameter'); }
    $token = $_GET['token'];

    $refresh = '';
    $scheme = ( $_SERVER['HTTPS'] ? 'https' : 'http' );
    $servername = $_SERVER['SERVER_NAME'];
    $nextpage = "$scheme://$servername/clilstore/options.php";

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $utime = time();
    $DbMultidict->prepare("DELETE FROM tokens WHERE expires<$utime")->execute();
    $stmt = $DbMultidict->prepare('SELECT * FROM tokens WHERE token=:token');
    $stmt->execute([':token'=>$token]);
    if (!($row = $stmt->fetch(PDO::FETCH_ASSOC))) { throw new Exception('The token is invalid or has expired'); }
    extract($row);

    if ($purpose=='verifyEmail') {
        $email = $data;
        $emailEnc = htmlspecialchars($email);
        $stmtUPD = $DbMultidict->prepare('UPDATE users SET emailVerUtime=:utime WHERE user=:user AND email=:email');
        $stmtUPD->execute([':utime'=>$utime,':user'=>$user,':email'=>$email]);
        if ($stmtUPD->rowCount()==0) { throw new Exception("Error while verifying email address: $emailEnc"); }
        $tick = "<span style='color:green;font-weight:bold'>✔</span>";
        $HTML = "<p>$tick Successfully verified email address:&nbsp; $emailEnc</p>\n";
        $refresh = "<meta http-equiv='refresh' content='2;URL=$nextpage?user=$user'>";
    } else {
        throw new Exception('This token has an unrecognised purpose: '.htmlspecialchars($purpose));
    }

  } catch (Exception $e) {
    $exceptionMessage = $e->getMessage();
    $HTML = "<div class=exception>$exceptionMessage</div>";
  }

  echo <<<EOD_PAGE
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> $refresh
    <title>Clilstore: Act on a token</title>
    <link rel="stylesheet" href="/css/smo.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
</head>
<body>

<ul class="smo-navlist">
<li><a href="./">Clilstore</a></li>
</ul>
<div class="smo-body-indent">

$HTML

</div>
<ul class="smo-navlist">
<li><a href="./">Clilstore</a></li>
</ul>

</body>
</html>
EOD_PAGE;

?>
