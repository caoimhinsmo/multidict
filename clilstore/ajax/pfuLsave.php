<?php
//Saves a learned item to pfuL after it has been edited
  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  if (empty($_REQUEST['pfuL'])) { die('Missing pfuL parameter');      }
  if (empty($_REQUEST['text'])) { die('Missing text parameter'); }
  $pfuL = $_REQUEST['pfuL'];
  $text = $_REQUEST['text'];

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $stmt = $DbMultidict->prepare('UPDATE cspfUnitLearned SET learned=:learned WHERE id=:id');
  $result = $stmt->execute([ ':id'=>$pfuL, ':learned'=>$text ]);
  if (!$result) { die('pfuLsave failed to save the edit'); }
  
  echo 'OK';
?>
