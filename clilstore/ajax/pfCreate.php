<?php
//Accept title and teacher POST parameters and create a new student portfolio
  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  try {
      $myCLIL = SM_myCLIL::singleton();
      $user = ( isset($myCLIL->id) ? $myCLIL->id : '' );
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }
  if (!isset($_REQUEST['title']))   { die('Missing title parameter');   }
  if (!isset($_REQUEST['teacher'])) { die('Missing teacher parameter'); }
  $title   = $_REQUEST['title'];
  $teacher = $_REQUEST['teacher'];
  $utime = time();

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  if (!empty($teacher)) {
      $stmt0 = $DbMultidict->prepare('SELECT 1 FROM users WHERE user=:user');
      $stmt0->execute([':user'=>$teacher]);
      if (!$stmt0->fetch()) { die('nouser'); }
  }

  $stmt = $DbMultidict->prepare('INSERT INTO cspf(user,title,prio) VALUES (:user,:title,:prio)');
  $result = $stmt->execute([ ':user'=>$user, ':title'=>$title, ':prio'=>$utime ]);
  if (!$result) { die('Create portfolio failed'); }

  if (!empty($teacher)) {
      $pf = $DbMultidict->lastInsertId();
      $stmt2 = $DbMultidict->prepare('INSERT INTO cspfPermit (pf,teacher) VALUES (:pf,:teacher)');
      $result2 = $stmt2->execute([ ':pf'=>$pf, ':teacher'=>$teacher ]);
      if (!$result2) { die('Add teacher failed'); }
  }
  
  echo 'OK';
?>
