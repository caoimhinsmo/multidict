<?php
//Record a like, or an unlike, by the user on a unit
  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  try {
      $myCLIL = SM_myCLIL::singleton();
      $user = ( isset($myCLIL->id) ? $myCLIL->id : '' );
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }
  if (!isset($_REQUEST['unit']))          { die('Missing unit parameter'); }
  if (!isset($_REQUEST['newLikeStatus'])) { die('Missing newLikeStatus parameter'); }
  $unit = $_REQUEST['unit'];
  $likes = ( $_REQUEST['newLikeStatus']=='liked' ? 1 : 0 );
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmt = $DbMultidict->prepare('REPLACE INTO user_unit(user,unit,likes) VALUES (:user,:unit,:likes)');
  $stmt->execute([':user'=>$user,':unit'=>$unit,':likes'=>$likes]);
  $stmtTotal = $DbMultidict->prepare('UPDATE clilstore SET likes = (SELECT SUM(likes) FROM user_unit WHERE unit=:unit) WHERE id=:unit');
  $stmtTotal->execute([':unit'=>$unit]); 
  echo 'OK';
?>
