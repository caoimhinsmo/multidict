<?php
//Takes parameters id, operation

  if (!include('autoload.inc.php'))   { die('include autoload failed'); }
  if (!isset($_REQUEST['id']))        { die('id is not set'); }
  if (!isset($_REQUEST['operation'])) { die('operation is not set'); }

  $T =  new SM_T('multidict/customWordOp');
//  $T_You_have_no_permission = $T->h('You_have_no_permission');
//  $T_Invalid_language_code  = $T->h('Cod_canain_mi_iom');

  $id        = $_REQUEST['id'];
  $operation = $_REQUEST['operation'];
  if (!preg_match('/^\d+$/',$id)) { die("The id parameter $id is not a postive integer"); }
  if (!in_array($operation,['deleteWord','changetr'])) { die("Invalid operation: $operation"); }

  $myCLIL = SM_myCLIL::singleton();
  $user = $myCLIL->id ?? '';
  if (empty($user)) { die('You are not logged on'); }

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  if ($operation=='deleteWord') {
      $idc = $id;
  } elseif ($operation=='changetr') {
      $stmtSELidctr = $DbMultidict->prepare("SELECT idctr,idc FROM customtr WHERE idctr=:idctr");
      $stmtSELidctr->execute([':idctr'=>$id]);
      $result = $stmtSELidctr->fetch(PDO::FETCH_ASSOC);
      extract($result);
  } else {
      die("Invalid operation: $operation");
  }
  $stmtSELidc = $DbMultidict->prepare("SELECT sl FROM custom WHERE idc=:idc");
  $stmtSELidc->execute([':idc'=>$idc]);
  $result = $stmtSELidc->fetch(PDO::FETCH_ASSOC); 
  extract($result);
  $grp = "cw-$sl";
  $stmtPermission = $DbMultidict->prepare('SELECT 1 FROM userGrp WHERE user=:user AND grp=:grp');
  $stmtPermission->execute([':user'=>$user,':grp'=>$grp]);
  if (!$stmtPermission->fetch()) { die("User $user has no permission to edit Custom Wordlist entries for language $sl"); }
  if ($operation=='deleteWord') {
      $stmtDEL = $DbMultidict->prepare("DELETE FROM custom WHERE idc=:idc");
      $stmtDEL->execute([':idc'=>$idc]);
  } elseif ($operation=='changetr') {
      if (!isset($_REQUEST['newval'])) { die('newval is not set'); }
      $newval = $_REQUEST['newval'];
      $stmtUPDtr = $DbMultidict->prepare("UPDATE customtr SET meaning=:newval WHERE idctr=:idctr");
      $stmtUPDtr->execute([':newval'=>$newval,':idctr'=>$idctr]);
  }
  echo 'OK';
?>
