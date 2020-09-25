<?php
// Clears the filter conditions on field fd
  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['fd']))   { die('fd is not set'); }
  $fd = $_REQUEST['fd'];
  $csSess   = SM_csSess::singleton();
  $csSess->clearFilter($fd);
  echo 'OK';
?>
