<?php
//Create a new (non-completed, with test=2) unit with a given title. Return the module number
  if (!include('autoload.inc.php')) { die('include autoload failed'); }
  if (!isset($_REQUEST['title']))   { die('title is not set'); }
  $title = $_REQUEST['title'];
  $DbMultidict = SM_DbMultidictPDO::singleton('rw');

  $myCLIL = SM_myCLIL::singleton();
  if (!$myCLIL->cead('{logged-in}')) { die('You are not logged in to Clilstore'); }
  $user = $myCLIL->id;

 //Defaults
  $sl = $text = $medembed = $summary = $langnotes = '';
  $level = -1;
  $medfloat = 'scroll';
  $medtype = $medlen = $words = 0;
  $created = $changed = time();
  $licence = 'BY-SA';
  $test = 2;

 //See if the user has a default language set for new units
  $stmtUL = $DbMultidict->prepare('SELECT unitLang FROM users WHERE user=:user');
  $stmtUL->execute(array('user'=>$user));
  if ($row = $stmtUL->fetch()) { $sl = $row['unitLang']; }

  $query = 'INSERT INTO clilstore('
          . 'owner,sl,level,title,text,medembed,medfloat,medtype,medlen,words,created,changed,summary,langnotes,licence,test'
          . ') VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
  $stmtINS = $DbMultidict->prepare($query);
  $stmtINS->execute(array($user,$sl,$level,$title,$text,$medembed,$medfloat,$medtype,$medlen,$words,$created,$changed,$summary,$langnotes,$licence,$test));
  $id = $DbMultidict->lastInsertId();
  echo "Created:$id";
?>
