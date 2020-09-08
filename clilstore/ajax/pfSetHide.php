<?php
//Change the hide flag on or off, to make a portfolio invisible or visible in the portfolio list of a teacher
  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  try {
      $myCLIL = SM_myCLIL::singleton();
      $user = ( isset($myCLIL->id) ? $myCLIL->id : '' );
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }
  if (!isset($_REQUEST['pid']))    { die('Missing pid parameter'); }
  if (!isset($_REQUEST['hidden'])) { die('Missing hide parameter'); }
  $pid    = $_REQUEST['pid'];
  $hidden = $_REQUEST['hidden'];
  $h = ( $hidden=='true' ? 1 : 0 );
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmt = $DbMultidict->prepare('UPDATE cspfPermit SET hidden=:hidden Where id=:id');
  $stmt->execute([':hidden'=>$h,':id'=>$pid]);
  echo 'OK';
?>
