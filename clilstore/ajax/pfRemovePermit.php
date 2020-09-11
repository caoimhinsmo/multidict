<?php
//Remove a teacher from a portfolio by deleting a record in cspfPermit
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (!isset($_REQUEST['permitId']))   { die('Missing permitId parameter');   }
  $permitId = $_REQUEST['permitId'];

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $stmt = $DbMultidict->prepare('DELETE FROM cspfPermit WHERE id=:id');
  $stmt->execute([ ':id'=>$permitId ]);
  
  echo 'OK';
?>
