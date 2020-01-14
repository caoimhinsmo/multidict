<?php
//Takes parameters id, sltl, altList
//Updates the altSl or altTL languages table for language id

  if (!include('autoload.inc.php')) { die('include autoload failed'); }

  $T =  new SM_T('multidict/updateAlt');
  $T_You_have_no_permission = $T->h('You_have_no_permission');
  $T_Invalid_language_code  = $T->h('Cod_canain_mi_iom');

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $myCLIL = SM_myCLIL::singleton();
  if (!$myCLIL->cead(SM_myCLIL::LUCHD_EADARTHEANGACHAIDH)) { die("$T_You_have_no_permission"); }
  $smid = $myCLIL->id;
  if (!isset($_REQUEST['id']))      { die('id is not set'); }
  if (!isset($_REQUEST['sltl']))    { die('sltl is not set'); }
  if (!isset($_REQUEST['altList'])) { die('altList is not set'); }
  $id      = $_REQUEST['id'];
  $sltl    = $_REQUEST['sltl'];
  $altList = $_REQUEST['altList'];
  if (!SM_WlSession::langValid($id)) { die("$T_Invalid_language_code: $id"); }
  $altArr = ( empty($altList) ? [] : explode('|',$altList) );
  foreach ($altArr as $v) { if (!SM_WlSession::langValid($v)) { die("$T_Invalid_language_code: $v"); } }
  if      ($sltl=='sl')  { $altTable = 'langAltSl'; }
    elseif ($sltl=='tl') { $altTable = 'langAltTl'; }
    else { die("Invalid sltl: $sltl"); }
  $stmtDEL = $DbMultidict->prepare("DELETE FROM $altTable WHERE id=:id");
  $stmtDEL->execute([':id'=>$id]);
  $stmtINS = $DbMultidict->prepare("INSERT INTO $altTable(id,alt,ord) VALUES(?,?,?)");
  for ($i=0;$i<count($altArr);$i++) { $stmtINS->execute(array($id,$altArr[$i],$i)); }
  echo 'OK';
?>
