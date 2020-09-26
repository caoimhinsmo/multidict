<?php
//In a portfolio, swops the order of of two items (either learned items or work items)
//by swoping their ord fields in the appropriate table.
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (empty($_REQUEST['id']))     { die('Missing id parameter');     }
  if (empty($_REQUEST['swopId'])) { die('Missing swopId parameter'); }
  $id     = $_REQUEST['id'];
  $swopId = $_REQUEST['swopId'];

  if       ( preg_match('/^pfuL(\d+)$/', $id, $matches)  ) { 
      $table = 'cspfUnitLearned';
  } elseif ( preg_match('/^pfuW(\d+)$/', $id, $matches)  ) {
      $table  = 'cspfUnitWork';
  } else {
      die('Invalid id paramter');
  }
  $id1 = $matches[1];
  $id2 = substr($swopId,4);

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $stmtGetOrd = $DbMultidict->prepare("SELECT ord FROM $table WHERE id=:id");
  $stmtGetOrd->execute([ ':id'=>$id1 ]);
  $ord1 = $stmtGetOrd->fetchColumn();
  $stmtGetOrd->execute([ ':id'=>$id2 ]);
  $ord2 = $stmtGetOrd->fetchColumn();

  $stmtSetOrd = $DbMultidict->prepare("UPDATE $table SET ord=:ord WHERE id=:id");
  $stmtSetOrd->execute([ ':id'=>$id1, ':ord'=>$ord2 ]);
  $stmtSetOrd->execute([ ':id'=>$id2, ':ord'=>$ord1 ]);

  echo 'OK';
?>
