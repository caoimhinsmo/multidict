<?php
//Delete a portfolio
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (!isset($_REQUEST['pf']))   { die('Missing pf parameter');   }
  $pf = $_REQUEST['pf'];

  $myCLIL = SM_myCLIL::singleton();
  $user = ( empty($myCLIL->id) ? '' : $myCLIL->id );

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $stmtCHECK = $DbMultidict->prepare('SELECT user FROM cspf WHERE pf=:pf');
  $stmtCHECK->execute([ ':pf'=>$pf ]);
  if (! ($owner = $stmtCHECK->fetchColumn()) ) { die('No such unit'); }
  if ($owner<>$user) { die('You can only delete portfolios owned by you yourself'); }

  $stmtDEL = $DbMultidict->prepare('DELETE FROM cspf WHERE pf=:pf');
  $stmtDEL->execute([ ':pf'=>$pf ]);
  
  echo 'OK';
?>
