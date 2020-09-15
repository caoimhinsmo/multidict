<?php
//Adds a new learned item to portfolio-unit pfu
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (empty($_REQUEST['pfu']))     { die('Missing pfu parameter');      }
  if (empty($_REQUEST['newText'])) { die('Missing newText parameter'); }
  $pfu     = $_REQUEST['pfu'];
  $newText = $_REQUEST['newText'];

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $stmt = $DbMultidict->prepare('INSERT INTO cspfUnitLearned (pfu,learned) VALUES (:pfu,:learned)');
  $result = $stmt->execute([ ':pfu'=>$pfu, ':learned'=>$newText ]);
  if (!$result) { die('pfuLadd failed to insert the new text'); }
  
  $id = $DbMultidict->lastInsertId();
  echo "OK:$id";
?>
