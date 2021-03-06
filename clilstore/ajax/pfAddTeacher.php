<?php
//Add a teacher to a portfolio, by adding a record in pfPermit
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (empty($_REQUEST['pf']))      { die('Missing pf parameter');      }
  if (empty($_REQUEST['teacher'])) { die('Missing teacher parameter'); }
  $pf =      $_REQUEST['pf'];
  $teacher = $_REQUEST['teacher'];

  $myCLIL = SM_myCLIL::singleton();
  $user = ( empty($myCLIL->id) ? '' : $myCLIL->id );

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $stmtCHECK = $DbMultidict->prepare('SELECT user FROM cspf WHERE pf=:pf');
  $stmtCHECK->execute([ ':pf'=>$pf ]);
  if (! ($owner = $stmtCHECK->fetchColumn()) ) { die('No such unit'); }
  if ($owner<>$user) { die('You can only modify portfolios owned by you yourself'); }

  $stmt0 = $DbMultidict->prepare('SELECT 1 FROM users WHERE user=:user');
  $stmt0->execute([':user'=>$teacher]);
  if (!$stmt0->fetch()) { die('nouser'); }

  $stmt1 = $DbMultidict->prepare('SELECT 1 FROM cspfPermit WHERE pf=:pf AND teacher=:teacher');
  $stmt1->execute([ ':pf'=>$pf, ':teacher'=>$teacher ]);
  if ($stmt1->fetch()) { die('duplicate'); }

  $stmt2 = $DbMultidict->prepare('INSERT INTO cspfPermit (pf,teacher) VALUES (:pf,:teacher)');
  $result2 = $stmt2->execute([ ':pf'=>$pf, ':teacher'=>$teacher ]);
  if (!$result2) { die('Add teacher failed'); }
  
  echo 'OK';
?>
