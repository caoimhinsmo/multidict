<?php
//Change the title of a portfolio
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (empty($_REQUEST['pf']))    { die('Missing pf parameter');      }
  if (empty($_REQUEST['title'])) { die('Missing title parameter'); }
  $pf    = $_REQUEST['pf'];
  $title = $_REQUEST['title'];

  $myCLIL = SM_myCLIL::singleton();
  $user = ( empty($myCLIL->id) ? '' : $myCLIL->id );

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $stmtCHECK = $DbMultidict->prepare('SELECT user FROM cspf WHERE pf=:pf');
  $stmtCHECK->execute([ ':pf'=>$pf ]);
  if (! ($owner = $stmtCHECK->fetchColumn()) ) { die('No such unit'); }
  if ($owner<>$user) { die('You can only modify portfolios owned by you yourself'); }

  $stmt1 = $DbMultidict->prepare('UPDATE cspf SET title=:title WHERE pf=:pf');
  $stmt1->execute([ ':pf'=>$pf, ':title'=>$title ]);
  
  echo 'OK';
?>
