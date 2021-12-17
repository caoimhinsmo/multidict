<?php
//Takes parameters id, operation

  if (!include('autoload.inc.php'))   { die('include autoload failed'); }
  if (!isset($_REQUEST['table']))     { die('table is not set'); }
  if (!isset($_REQUEST['id']))        { die('id is not set'); }
  if (!isset($_REQUEST['operation'])) { die('operation is not set'); }

  $T =  new SM_T('multidict/customWordOp');
//  $T_You_have_no_permission = $T->h('You_have_no_permission');
//  $T_Invalid_language_code  = $T->h('Cod_canain_mi_iom');

  $table     = $_REQUEST['table'];
  $id        = $_REQUEST['id'];
  $operation = $_REQUEST['operation'];
  if (!in_array($table,['custom','customwf','customtr'])) { die("Invalid table parameter: $table"); }
  if (!in_array($operation,['delete','change','insert']))          { die("Invalid operation parameter: $operation"); }
  if (!preg_match('/^\d+$/',$id)) { die("The id parameter $id is not a postive integer"); }

  $myCLIL = SM_myCLIL::singleton();
  $user = $myCLIL->id ?? '';
  if (empty($user)) { die('You are not logged on'); }

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  if ($table=='custom') {
      $idc = $id;
  } elseif ($table=='customtr') {
      $stmtSELidctr = $DbMultidict->prepare("SELECT idctr,idc FROM customtr WHERE idctr=:idctr");
      $stmtSELidctr->execute([':idctr'=>$id]);
      $result = $stmtSELidctr->fetch(PDO::FETCH_ASSOC);
      extract($result);
  } elseif ($table=='customwf') {
      $stmtSELidcwf = $DbMultidict->prepare("SELECT idcwf,idc FROM customwf WHERE idcwf=:idcwf");
      $stmtSELidcwf->execute([':idcwf'=>$id]);
      $result = $stmtSELidcwf->fetch(PDO::FETCH_ASSOC);
      extract($result);
  } else {
      die("Invalid table parameter: $table");
  }
  $stmtSELidc = $DbMultidict->prepare("SELECT sl FROM custom WHERE idc=:idc");
  $stmtSELidc->execute([':idc'=>$idc]);
  $result = $stmtSELidc->fetch(PDO::FETCH_ASSOC); 
  extract($result);
  $grp = "cw-$sl";
  $stmtPermission = $DbMultidict->prepare('SELECT 1 FROM userGroup WHERE user=:user AND grp=:grp');
  $stmtPermission->execute([':user'=>$user,':grp'=>$grp]);
  if (!$stmtPermission->fetch()) { die("User $user has no permission to edit Custom Wordlist entries for language $sl"); }
  if ($operation=='delete' && $table=='custom') {
      $stmtDEL = $DbMultidict->prepare("DELETE FROM custom WHERE idc=:idc");
      $stmtDEL->execute([':idc'=>$idc]);
  } elseif ($operation=='change' && $table=='customtr') {
      if (!isset($_REQUEST['newval'])) { die('newval is not set'); }
      $newval = $_REQUEST['newval'];
      $stmtUPDtr = $DbMultidict->prepare("UPDATE customtr SET meaning=:newval WHERE idctr=:idctr");
      $stmtUPDtr->execute([':newval'=>$newval,':idctr'=>$idctr]);
  } elseif ($operation=='change' && $table=='customwf') {
      if (!isset($_REQUEST['field']))  { die('field is not set');  }
      if (!isset($_REQUEST['newval'])) { die('newval is not set'); }
      $field  = $_REQUEST['field'];
      $newval = $_REQUEST['newval'];
      if (!in_array($field,['wf','pri','priWhy'])) { die("Invalid field parameter; $field"); }
      $stmtUPDwf = $DbMultidict->prepare("UPDATE customwf SET $field=:newval WHERE idcwf=:idcwf");
      $stmtUPDwf->execute([':newval'=>$newval,':idcwf'=>$idcwf]);
  } elseif ($operation=='insert' && $table=='customwf') {
      if (!isset($_REQUEST['newval'])) { die('newval is not set'); }
      $newval = $_REQUEST['newval'];
      $stmtINSwf = $DbMultidict->prepare("INSERT INTO customwf (wf, idc) VALUES (:wf,:idc)");
      $stmtINSwf->execute([':wf'=>$newval,':idc'=>$id]);
  } else {
      die("Invalid combination \$table=$table, \$operation=$operation");
  }
  echo 'OK';
?>
