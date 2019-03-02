<?php
//Change the meaning of a word in the user’s vocabulary

  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['vocid']))   { die('vocid is not set'); }
  if (!isset($_REQUEST['meaning'])) { die('meaning is not set'); }
  $vocid   = $_REQUEST['vocid'];
  $meaning = $_REQUEST['meaning'];
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmt = $DbMultidict->prepare('UPDATE csVoc SET meaning=:meaning WHERE vocid=:vocid');
  $stmt->execute([':vocid'=>$vocid,':meaning'=>$meaning]);
  echo 'OK';

?>
