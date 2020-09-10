<?php
//Make this portfolio the active portfolio by setting prio to utime
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (!isset($_REQUEST['pf']))   { die('Missing pf parameter');   }
  $pf = $_REQUEST['pf'];

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $utime = time();
error_log("\$pf=$pf, \$utime=$utime");
  $stmt = $DbMultidict->prepare('UPDATE cspf SET prio=:prio WHERE pf=:pf');
  $stmt->execute([ ':pf'=>$pf, ':prio'=>$utime ]);
  
  echo 'OK';
?>
