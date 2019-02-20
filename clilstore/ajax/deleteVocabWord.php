<?php
//Delete a word from the user’s vocabulary

  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['user']))   { die('user is not set'); }
  if (!isset($_REQUEST['sl']))     { die('sl is not set'); }
  if (!isset($_REQUEST['word']))   { die('word is not set'); }
  $user = $_REQUEST['user'];
  $sl   = $_REQUEST['sl'];
  $word = $_REQUEST['word'];
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmtDEL = $DbMultidict->prepare('DELETE FROM csVocab WHERE user=:user AND sl=:sl AND word=:word');
  $stmtDEL->execute([':user'=>$user,':sl'=>$sl,':word'=>$word]);
  $stmtDELu = $DbMultidict->prepare('DELETE FROM csVocabUnit WHERE user=:user AND sl=:sl AND word=:word');
  $stmtDELu->execute([':user'=>$user,':sl'=>$sl,':word'=>$word]);
  echo 'OK';

?>
