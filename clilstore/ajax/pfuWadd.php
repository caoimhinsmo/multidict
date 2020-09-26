<?php
//Adds a new work item to portfolio-unit pfu
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (empty($_REQUEST['pfu']))     { die('Missing pfu parameter');     }
  if (empty($_REQUEST['newWork'])) { die('Missing newWork parameter'); }
  if (empty($_REQUEST['newURL']))  { die('Missing newURL parameter');  }
  $pfu     = $_REQUEST['pfu'];
  $newWork = $_REQUEST['newWork'];
  $newURL  = $_REQUEST['newURL'];

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $utime = time();
  $stmt = $DbMultidict->prepare('INSERT INTO cspfUnitWork (pfu,work,url,ord) VALUES (:pfu,:work,:url,:ord)');
  $result = $stmt->execute([ ':pfu'=>$pfu, ':work'=>$newWork, ':url'=>$newURL, ':ord'=>$utime ]);
  if (!$result) { die('pfuWadd failed to insert the new text'); }
  
  $id = $DbMultidict->lastInsertId();
  echo "OK:$id";
?>
