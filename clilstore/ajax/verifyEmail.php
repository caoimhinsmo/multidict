<?php
//Send an email to verify a user’s email address

  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['email']))   { die('email is not set'); }
  $email = $_REQUEST['email'];

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmt = $DbMultidict->prepare('SELECT user FROM users WHERE email=:email');
  $stmt->execute([':email'=>$email]);
  $user = $stmt->fetchColumn();
  $myCLIL = SM_myCLIL::singleton();
  if (!$myCLIL->cead("$user|admin")) { die('not authorized'); }

  $charpool = array_merge(range(0,9), range('a', 'z'),range('A', 'Z'));
  $token = '';
  for($i=0;$i<20;$i++) { $token .= $charpool[mt_rand(0, count($charpool) - 1)]; }
  $scheme = ( empty($_SERVER['HTTPS']) ? 'http' : 'https' );
  $servername = $_SERVER['SERVER_NAME'];

  $expires = time() + 86400;  //Valid for 24 hours
  $stmtINS = $DbMultidict->prepare("INSERT INTO tokens(token, expires, user, purpose, data)"
                                    ." VALUES(:token, :expires, :user, 'verifyEmail', :email)");
  $stmtINS->execute([':token'=>$token, ':expires'=>$expires, ':user'=>$user, ':email'=>$email]);

  $message = <<<EOD_MESSAGE
Click the link or copy it to your browser to verify your email address to Clilstore:

$scheme://$servername/clilstore/token.php?token=$token

(valid for 24 hours)
EOD_MESSAGE;
  mail($email,'Clilstore - link to verify email address (valid 24 hours)',$message,"From:no-reply@multidict.net\r\n");
  echo 'OK';

?>
