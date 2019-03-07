<?php
//Set the user’s record flag to ON if vocRecord=vocOn, or OFF if vocRecord=vocOff
  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['vocRecord'])) { die('vocRecord is not set'); }
  try {
      $myCLIL = SM_myCLIL::singleton();
      $user = ( isset($myCLIL->id) ? $myCLIL->id : '' );
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }
  $record = ( $_REQUEST['vocRecord']=='vocOn' ? 1 : 0 );
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmt = $DbMultidict->prepare('UPDATE users SET record=:record WHERE user=:user');
  $stmt->execute([':record'=>$record,':user'=>$user]);
  echo 'OK';

?>
