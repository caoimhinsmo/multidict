<?php
//Delete a word from the user’s vocabulary
  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['vocid']))   { die('vocid is not set'); }
  $vocid = $_REQUEST['vocid'];
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmtDELu = $DbMultidict->prepare('DELETE FROM csVocUnit WHERE vocid=:vocid');
  $stmtDELu->execute([':vocid'=>$vocid]);
  $stmtDEL = $DbMultidict->prepare('DELETE FROM csVoc WHERE vocid=:vocid');
  $stmtDEL->execute([':vocid'=>$vocid]);
  echo 'OK';
?>
