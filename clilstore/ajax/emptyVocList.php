<?php
//Delete every word from the user’s vocabulary list for a given language

  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['user'])) { die('user is not set'); }
  if (!isset($_REQUEST['sl']))   { die('sl is not set'); }
  $user = $_REQUEST['user'];
  $sl   = $_REQUEST['sl'];
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmtDEL = $DbMultidict->prepare('DELETE FROM csVoc WHERE user=:user AND sl=:sl');
  $stmtDEL->execute([':user'=>$user,':sl'=>$sl]);
  echo 'OK';
?>
