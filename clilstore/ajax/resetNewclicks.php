<?php
//Reset to 0 the newclicks counter for each word in a unit

  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['unit']))   { die('unit is not set'); }
  $unit = $_REQUEST['unit'];
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmt = $DbMultidict->prepare('UPDATE csWclick SET newclicks=0 WHERE unit=:unit');
  $stmt->execute([':unit'=>$unit]);
  $utime = time();
  $stmt = $DbMultidict->prepare('UPDATE clilstore SET newclickTime=:utime WHERE id=:unit');
  $stmt->execute(['utime'=>$utime,':unit'=>$unit]);
  echo 'OK';

?>
