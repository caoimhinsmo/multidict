<?php
//Delete the record with id pfu in the cspfUnit table, and thereby remove the unit from the portfolio
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (!isset($_REQUEST['pfu']))   { die('Missing pfu parameter');   }
  $pfu = $_REQUEST['pfu'];

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $stmt = $DbMultidict->prepare('DELETE FROM cspfUnit WHERE pfu=:pfu');
  $stmt->execute([ ':pfu'=>$pfu ]);
  
  echo 'OK';
?>
