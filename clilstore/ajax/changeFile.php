<?php
// Either change the name of a file attached to a unit (if passed a filename paramter)
// or delete the file (if passed a delete paramter)

  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['fileid']))   { die('fileid is not set'); }
  $fileid   = $_REQUEST['fileid'];

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $stmtUpdateFileCount = $DbMultidict->prepare('update clilstore set files= (select count(1) from csFiles where id=:id) where id=:id');

  $stmtUnit = $DbMultidict->prepare('SELECT clilstore.id,owner FROM csFiles, clilstore WHERE fileid=:fileid AND csFiles.id=clilstore.id');
  $stmtUnit->execute(['fileid'=>$fileid]);
  if (!($unitResult = $stmtUnit->fetch(PDO::FETCH_ASSOC))) { die('no file exists with fileid='+fileid); }
  extract($unitResult);
  $myCLIL = SM_myCLIL::singleton();
  if (!$myCLIL->cead("$owner|admin")) { die('not authorized'); }

  if (isset($_REQUEST['delete'])) {
      $stmtDelete = $DbMultidict->prepare('DELETE FROM csFiles WHERE fileid=:fileid');
      $stmtDelete->execute([':fileid'=>$fileid]);
      if ($stmtDelete->rowCount()==0) { die('Failed to delete the file with fileid='+fileid); }
      $stmtUpdateFileCount->execute([':id'=>$id]);
  } elseif (isset($_REQUEST['filename'])) {
      $filename = $_REQUEST['filename'];
      if (empty($filename)) { die('Null string supplied as filename'); }
      $stmtUpdate = $DbMultidict->prepare('UPDATE csFiles SET filename=:filename WHERE fileid=:fileid');
      $stmtUpdate->execute([':fileid'=>$fileid,
                            ':filename'=>$filename]);
  } else {
      die('Either a delete or a name paramter is required');
  }

  echo 'OK';

?>
