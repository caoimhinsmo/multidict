<?php
//Change an option for a user

  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['user']))   { die('user is not set'); }
  if (!isset($_REQUEST['option'])) { die('option is not set'); }
  if (!isset($_REQUEST['value']))  { die('value is not set'); }
  $user   = $_REQUEST['user'];
  $option = $_REQUEST['option'];
  $value  = $_REQUEST['value'];
  if (!in_array($option,['unitLang','highlightRow','record','fullname','email'])) { die("invalid option $option"); }
  $myCLIL = SM_myCLIL::singleton();
  if (!$myCLIL->cead("$user|admin")) { die('not authorized'); }
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmt = $DbMultidict->prepare("UPDATE users SET $option=:value WHERE user=:user");
  $stmt->execute([':value'=>$value,':user'=>$user]);
  echo 'OK';

?>
