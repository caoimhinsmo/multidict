<?php
// Add a column to display in the current mode by by setting its display value to 1
  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['fd']))   { die('fd is not set'); }
  $fd = $_REQUEST['fd'];
  $csSess   = SM_csSess::singleton();
  $csSess->addCol($fd);
  echo 'OK';
?>
