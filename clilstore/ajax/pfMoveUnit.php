<?php
//Move a unit to another portfolio
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (!isset($_REQUEST['pfu'])) { die('Missing pf parameter pfu'); }
  $pfu = $_REQUEST['pfu'];
  if (!isset($_REQUEST['pf']))  { die('Missing pf parameter pf');  }
  $pf = $_REQUEST['pf'];

  $myCLIL = SM_myCLIL::singleton();
  $user = ( empty($myCLIL->id) ? '' : $myCLIL->id );

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $stmtCHECK = $DbMultidict->prepare('SELECT user FROM cspf WHERE pf=:pf');
  $stmtCHECK->execute([ ':pf'=>$pf ]);
  if (! ($owner = $stmtCHECK->fetchColumn()) ) { die('No such portfolio'); }
  if ($owner<>$user) { die('You can only alter portfolios owned by you yourself'); }

  $timeNow = time();
  $stmtUPDATE = $DbMultidict->prepare('UPDATE IGNORE cspfUnit SET pf=:pf, ord=:ord WHERE pfu=:pfu');
  $stmtUPDATE->execute([ ':pfu'=>$pfu, ':pf'=>$pf, ':ord'=>$timeNow ]);
  if ($stmtUPDATE->rowcount()==0) { echo 'KO'; }
                             else { echo 'OK'; }
?>
