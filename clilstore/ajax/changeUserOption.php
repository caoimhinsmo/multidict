<?php
//Change an option for a user

  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['user']))   { die('user is not set'); }
  if (!isset($_REQUEST['option'])) { die('option is not set'); }
  if (!isset($_REQUEST['value']))  { die('value is not set'); }
  $user   = $_REQUEST['user'];
  $option = $_REQUEST['option'];
  $value  = $_REQUEST['value'];
  if (!in_array($option,['unitLang','record','fullname','email'])) { die("invalid option $option"); }
  $myCLIL = SM_myCLIL::singleton();
  if (!$myCLIL->cead("$user|admin")) { die('not authorized'); }
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $zeroVerTime = '';
  if ($option=='email') {
      $stmt = $DbMultidict->prepare('SELECT user AS userPrev FROM users WHERE email=:email');
      $stmt->execute([':email'=>$value]);
      $userPrev = $stmt->fetchColumn();
      if (!empty($userPrev)) {
          if ($userPrev==$user) { die('OK-null'); } //Nothing to change
          die('There is already another Clilstore user with this email address.  Nothing changed.');
      }
      $zeroVerTime = ',emailVerUtime=0';
  }
  $stmt2 = $DbMultidict->prepare("UPDATE users SET $option=:value$zeroVerTime WHERE user=:user");
  $stmt2->execute([':value'=>$value,':user'=>$user]);
  echo 'OK';

?>
