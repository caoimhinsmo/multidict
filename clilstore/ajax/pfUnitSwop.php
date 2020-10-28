<?php
//In a portfolio, swops the order of of two units by swoping their ord fields in the cspfUnit table.
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (empty($_REQUEST['id']))     { die('Missing id parameter');     }
  if (empty($_REQUEST['swopId'])) { die('Missing swopId parameter'); }
  $id     = $_REQUEST['id'];
  $swopId = $_REQUEST['swopId'];

  $id1 = substr($id,6);
  $id2 = substr($swopId,6);

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $stmtGetOrd = $DbMultidict->prepare("SELECT ord FROM cspfUnit WHERE pfu=:id");
  $stmtGetOrd->execute([ ':id'=>$id1 ]);
  $ord1 = $stmtGetOrd->fetchColumn();
  $stmtGetOrd->execute([ ':id'=>$id2 ]);
  $ord2 = $stmtGetOrd->fetchColumn();

  $stmtSetOrd = $DbMultidict->prepare("UPDATE cspfUnit SET ord=:ord WHERE pfu=:id");
  $stmtSetOrd->execute([ ':id'=>$id1, ':ord'=>$ord2 ]);
  $stmtSetOrd->execute([ ':id'=>$id2, ':ord'=>$ord1 ]);

  echo 'OK';
?>
