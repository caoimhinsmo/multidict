<?php
//This sets mdadv for session sid

  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['sid']))   { die('sid is not set'); }
  if (!isset($_REQUEST['mdadv'])) { die('mdadv is not set'); }
  $sid   = $_REQUEST['sid'];
  $mdadv = $_REQUEST['mdadv'];
error_log("setMDadv: \$sid=$sid, \$mdadv=$mdadv");
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmt = $DbMultidict->prepare('UPDATE wlSession SET mdadv=:mdadv WHERE sid=:sid');
  $stmt->execute(array(':mdadv'=>$mdadv,':sid'=>$sid));
  echo 'OK';

?>
