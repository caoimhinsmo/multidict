<?php
//Change the meaning of a word in the user’s vocabulary

  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['user']))    { die('user is not set'); }
  if (!isset($_REQUEST['sl']))      { die('sl is not set'); }
  if (!isset($_REQUEST['word']))    { die('word is not set'); }
  if (!isset($_REQUEST['meaning'])) { die('meaning is not set'); }
  $user    = $_REQUEST['user'];
  $sl      = $_REQUEST['sl'];
  $word    = $_REQUEST['word'];
  $meaning = $_REQUEST['meaning'];
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmt = $DbMultidict->prepare('UPDATE csVocab SET meaning=:meaning WHERE user=:user AND sl=:sl AND word=:word');
  $stmt->execute([':user'=>$user,':sl'=>$sl,':word'=>$word,':meaning'=>$meaning]);
  echo 'OK';

?>
