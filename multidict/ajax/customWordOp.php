<?php
//Takes parameters id, operation

  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  $myCLIL = SM_myCLIL::singleton();
  $user = $myCLIL->id ?? '';
  if (empty($user)) { die('You are not logged on'); }
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $T =  new SM_T('multidict/customWordOp');
//  $T_You_have_no_permission = $T->h('You_have_no_permission');
//  $T_Invalid_language_code  = $T->h('Cod_canain_mi_iom');

  if (!isset($_REQUEST['table']))     { die('table is not set'); }
  if (!isset($_REQUEST['operation'])) { die('operation is not set'); }
  $table     = $_REQUEST['table'];
  $operation = $_REQUEST['operation'];
  if (!in_array($table,['custom','customtr','customwf'])) { die("Invalid table parameter: $table"); }
  if (!in_array($operation,['delete','change','insert'])) { die("Invalid operation parameter: $operation"); }

  if ($table=='custom' && $operation=='insert') {
      if (!isset($_REQUEST['sl'])) { die('sl is not set'); }
      $sl = $_REQUEST['sl'];
  } else {
      if (!isset($_REQUEST['id'])) { die('id is not set'); }
      $id = $_REQUEST['id'];
      if ($id<>-1 && !preg_match('/^\d+$/',$id)) { die("The id parameter $id is not a postive integer"); }
      if ($table=='custom') {
          $idc = $id;
      } elseif ($table=='customtr') {
          $stmtSELidctr = $DbMultidict->prepare("SELECT idctr,idc FROM customtr WHERE idctr=:idctr");
          $stmtSELidctr->execute([':idctr'=>$id]);
          $result = $stmtSELidctr->fetch(PDO::FETCH_ASSOC);
          extract($result);
      } elseif ($table=='customwf' && $operation=='insert') {
          $idc = $id;
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
  }
  $grp = "cw-$sl";
  $stmtPermission = $DbMultidict->prepare('SELECT 1 FROM userGrp WHERE user=:user AND grp=:grp');
  $stmtPermission->execute([':user'=>$user,':grp'=>$grp]);
  if (!$stmtPermission->fetch()) { die("User $user has no permission to edit Custom Wordlist entries for language $sl"); }

  if ($operation=='delete' && $table=='custom') {
      $stmtDELc = $DbMultidict->prepare("DELETE FROM custom WHERE idc=:idc");
      $stmtDELc->execute([':idc'=>$idc]);
  } elseif ($operation=='delete' && $table=='customwf') {
      $stmtDELcwf = $DbMultidict->prepare("DELETE FROM customwf WHERE idcwf=:idcwf");
      $stmtDELcwf->execute([':idcwf'=>$idcwf]);
  } elseif ($operation=='change' && $table=='custom') {
      if (!isset($_REQUEST['field']))  { die('field is not set');  }
      if (!isset($_REQUEST['newval'])) { die('newval is not set'); }
      $field  = $_REQUEST['field'];
      $newval = $_REQUEST['newval'];
      if (!in_array($field,['word','disambig','gram','pri'])) { die("Invalid field parameter; $field"); }
      $stmtUPDcwf = $DbMultidict->prepare("UPDATE IGNORE custom SET $field=:newval WHERE idc=:idc");
      $stmtUPDcwf->execute([':newval'=>$newval,':idc'=>$idc]);
      if ($stmtUPDcwf->rowcount()==0 && in_array($field,['word','disambig'])) {
          die('Change refused, because this would result in a duplicate. You must ensure that homograph words each have a unique disambiguator.');
      }
  } elseif ($operation=='change' && $table=='customtr') {
      if (!isset($_REQUEST['newval'])) { die('newval is not set'); }
      $newval = $_REQUEST['newval'];
      $stmtUPDctr = $DbMultidict->prepare("UPDATE customtr SET meaning=:newval WHERE idctr=:idctr");
      $stmtUPDctr->execute([':newval'=>$newval,':idctr'=>$idctr]);
  } elseif ($operation=='change' && $table=='customwf') {
      if (!isset($_REQUEST['field']))  { die('field is not set');  }
      if (!isset($_REQUEST['newval'])) { die('newval is not set'); }
      $field  = $_REQUEST['field'];
      $newval = $_REQUEST['newval'];
      if (!in_array($field,['wf','pri','priWhy'])) { die("Invalid field parameter; $field"); }
      $stmtUPDcwf = $DbMultidict->prepare("UPDATE customwf SET $field=:newval WHERE idcwf=:idcwf");
      $stmtUPDcwf->execute([':newval'=>$newval,':idcwf'=>$idcwf]);
  } elseif ($operation=='insert' && $table=='custom') {
      if (!isset($_REQUEST['word']))     { die('word is not set');  }
      if (!isset($_REQUEST['disambig'])) { die('disambig is not set');  }
      if (!isset($_REQUEST['tl']))       { die('tl is not set');  }
      if (!isset($_REQUEST['meaning']))  { die('meaning is not set');  }
      $word     = trim($_REQUEST['word']);
      $disambig = trim($_REQUEST['disambig']);
      $tl       = trim($_REQUEST['tl']);
      $meaning  = trim($_REQUEST['meaning']);
      $stmtINSc = $DbMultidict->prepare("INSERT IGNORE into custom (sl,word,disambig) VALUES (:sl,:word,:disambig)");
      $stmtINSc->execute([':sl'=>$sl,':word'=>$word,':disambig'=>$disambig]);
      if ($stmtINSc->rowcount()==0)
        { die('Refused. Not inserted, because this would result in a duplicate. You need to give homograph words a unique disambiguator.'); }
      $idc = $DbMultidict->lastInsertId();
      $stmtSELtls = $DbMultidict->prepare("SELECT tl FROM dictParam WHERE dict='custom' AND sl=:sl ORDER BY tl");
      $stmtSELtls->execute([':sl'=>$sl]);
      $tlLangs = $stmtSELtls->fetchAll(PDO::FETCH_COLUMN); //Languages into which this $sl is normally translated
      $meanings = [];
      foreach ($tlLangs as $tlLang) { $meanings[$tlLang] = ''; }
      $meanings[$tl] = $meaning;
      $stmtINSctr = $DbMultidict->prepare('INSERT INTO customtr (idc,tl,meaning) VALUES(:idc,:tl,:meaning)');
      foreach ($meanings as $tlLang=>$tlMeaning) {
          $stmtINSctr->execute([':idc'=>$idc,':tl'=>$tlLang,':meaning'=>$tlMeaning]);
      }
      echo "OK:$idc"; return;
  } elseif ($operation=='insert' && $table=='customwf') {
      if (!isset($_REQUEST['newval'])) { die('newval is not set'); }
      $newval = $_REQUEST['newval'];
      $stmtINScwf = $DbMultidict->prepare("INSERT INTO customwf (wf,idc) VALUES (:wf,:idc)");
      $stmtINScwf->execute([':wf'=>$newval,':idc'=>$id]);
  } else {
      die("Invalid combination \$table=$table, \$operation=$operation");
  }
  echo 'OK';
?>
