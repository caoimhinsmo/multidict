<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  try {
      $myCLIL = SM_myCLIL::singleton();
      if (!$myCLIL->cead('{logged-in}')) { $myCLIL->diultadh(''); }
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }

  try {
    $myCLIL->dearbhaich();
    $user = $myCLIL->id;
    $servername = SM_myCLIL::servername();
    $serverhome = SM_myCLIL::serverhome();

    if (isset($_GET['cancel'])) { header("Location:$serverhome/clilstore/"); }

    if (empty($_GET['id'])) { throw new SM_MDexception('No id parameter'); }
    $id = $_GET['id'];

    if (empty($_GET['delete'])) {
        $DbMultidict = SM_DbMultidictPDO::singleton('r');
        $stmt1 = $DbMultidict->prepare('SELECT title,owner FROM clilstore WHERE id=:id');
        $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt1->execute();
        $stmt1->bindColumn(1,$title);
        $stmt1->bindColumn(2,$owner);
        $stmt1->fetch();
        $stmt1 = null;
        if ($user<>$owner && $user<>'admin') { throw new SM_MDexception("Your are trying to delete a unit belonging to another user, $owner - This is not allowed"); }
        $stmt2 = $DbMultidict->prepare('SELECT filename FROM csFiles WHERE id=:id');
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $filenameArr = $stmt2->fetchAll(PDO::FETCH_COLUMN, 0);
        $stmt2 = null;
        if (!empty($filenameArr)) {
            $filesInfo = "This unit has the following attached files <i>(which will all be permanently deleted if you delete the unit)</i>:<br>\n";
            foreach ($filenameArr as $filename) { $filesInfo .= "&nbsp;&nbsp;&nbsp;<b>$filename</b><br>\n"; }
            $filesInfo .= "<br>\n";
        }

        echo <<<EOD1
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete a clilstore unit</title>
    <link rel="stylesheet" href="/css/smo.css">
    <link rel="stylesheet" href="style.css?version=2014-04-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        input[type=submit]       { font-size:112%; background-color:#55a8eb; color:white; font-weight:bold; padding:3px 10px; border:0; border-radius:8px; }
        input[type=submit]:hover { background-color:blue; }
        input#delete       { background-color:#f84; }
        input#delete:hover { background-color:red; font-weight:bold; }
    </style>
</head>
<body>

<ul class="linkbuts">
<li><a href="./" title="Clilstore index page">Clilstore</a>
<li><a href="/cs/$id" title="Back to Unit $id">Unit $id</a></li>
</ul>
<div class="smo-body-indent" style="padding-top:2em;padding-bottom:2em">

<form method="get">
Delete Unit $id: “<b>$title</b>”<br><br>
$filesInfo
Really delete unit this unit?
<input type="hidden" name="id" value="$id"/>
<input type="submit" name="delete" value="Yes" id="delete">
<br><br>
Or else
<input type="submit" name="cancel" value="Cancel"/>
</form>

</div>
<ul class="linkbuts">
<li><a href="./" title="Clilstore index page">Clilstore</a>
<li><a href="/cs/$id" title="Back to Unit $id">Unit $id</a></li>
</ul>

</body>
</html>
EOD1;

    } else {
        $DbMultidict = SM_DbMultidictPDO::singleton('rw');
        try {
            $DbMultidict->beginTransaction();
            $DbMultidict->prepare('DELETE FROM csButtons WHERE id=:id')->execute(array('id'=>$id));
            $DbMultidict->prepare('DELETE FROM csFiles WHERE id=:id')->execute(array('id'=>$id));
            $owner = ( $user=='admin' ? '%' : $user );
            $stmt5 = $DbMultidict->prepare('DELETE FROM clilstore WHERE id=:id AND owner LIKE :owner');
            $stmt5->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt5->bindParam(':owner',$owner);
            $stmt5->execute();
            $stmt5 = null;
            $DbMultidict->commit();
            $message = '<p><img src="/icons-smo/sgudal.png" alt="">Unit deleted</p>';
        } catch (PDOException $ex) {
            $DbMultidict->rollBack();
            throw new SM_MDexception("Failed to delete unit $id<br>".$ex->getMessage());
        }

        echo <<<EOD2
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="refresh" content="2; url=$serverhome/clilstore/">
    <title>Delete a clilstore page - result</title>
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
</head>
<body>

$message

</body>
</html>
EOD2;

    }

  } catch (Exception $e) { echo $e; }

?>
