<?php
//Adds a new learned item or work item to portfolio-unit pfu
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (empty($_REQUEST['liId'])) { die('Missing liId parameter');      }
  $liId = $_REQUEST['liId'];

  if       ( preg_match('/^pfuL(\d+)$/', $liId, $matches)  ) { 
      $table = 'cspfUnitLearned';
  } elseif ( preg_match('/^pfuW(\d+)$/', $liId, $matches)  ) {
      $table  = 'cspfUnitWork';
  } else {
      die('Invalid liId paramter');
  }
  $id = $matches[1];

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $stmt = $DbMultidict->prepare("DELETE FROM $table WHERE id=:id");
  $stmt->execute([ ':id'=>$id ]);
  $rowCount = $stmt->rowCount();
  if ($rowCount<>1) { die("$rowCount rows deleted"); }
  
  echo 'OK';
?>
