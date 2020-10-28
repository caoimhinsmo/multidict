<?php
//Add a unit to the current portfolio
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  $myCLIL = SM_myCLIL::singleton();
  $loggedinUser = $user = $myCLIL->id;
  if (empty($loggedinUser)) { die('Not logged in'); }

  if (!isset($_REQUEST['unit']))   { die('Missing unit parameter');   }
  $unit = $_REQUEST['unit'];

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

 //Find the user’s current portfolio
  $stmtFindPf = $DbMultidict->prepare('SELECT pf FROM cspf WHERE user=:user ORDER BY prio DESC LIMIT 1');
  $stmtFindPf->execute([':user'=>$loggedinUser]);
  $pf = $stmtFindPf->fetchColumn();
  if (!$pf) { die('You have no portfolio'); }

 //Add the unit
  $ord = time();
  $stmtAddUnit = $DbMultidict->prepare('INSERT IGNORE INTO cspfUnit (pf,unit,ord) VALUES (:pf,:unit,:ord)');
  $stmtAddUnit->execute([ ':pf'=>$pf, ':unit'=>$unit, ':ord'=>$ord ]);
  
  echo 'OK';
?>
