<?php
  if (!include('autoload.inc.php')) { die('autoload failed'); }

  if (empty($_REQUEST['sl'])) { die('Missing sl parameter'); }
    else { $sl = $_REQUEST['sl']; }
  if (empty($_REQUEST['user'])) { die('Missing user parameter'); }
    else { $user = $_REQUEST['user']; }
  if (empty($_REQUEST['separator'])) { die('Missing separator parameter'); }
    else { $separator = $_REQUEST['separator']; }

  $myCLIL = SM_myCLIL::singleton();
  if (!$myCLIL->cead('{logged-in}')) { die('Not logged in'); }
  if ($myCLIL->id <> $user) { die('Not authorized'); }

  $myCLIL->dearbhaich();
  $loggedinUser = $myCLIL->id;

  $DbMultidict = SM_DbMultidictPDO::singleton('rw');
  $vocHtml = '';

  $stmt = $DbMultidict->prepare('SELECT DISTINCT word,meaning FROM csVoc WHERE user=:user AND sl=:sl ORDER BY word');
  $stmt->execute([':user'=>$user,':sl'=>$sl]);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($rows as $row) {
      extract($row);
      $vocHtml .= "$word$separator$meaning\n";
  }

  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Pragma: no-cache");
  header('Content-Type: text/plain; charset=UTF-8');
  header("Content-disposition: attachment; filename=\"voc-$sl.csv\"");

  echo $vocHtml;
?>
