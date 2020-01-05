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

  $T = new SM_T('clilstore/delete');

  $T_Unit     = $T->h('Unit');  
  $T_Cancel   = $T->h('Sguir');
  $T_Or_else  = $T->h('Air neo');
  $T_Delete   = $T->h('Sguab às');

  $T_Really_delete_unit  = $T->h('Really_delete_unit');
  $T_Parameter_p_a_dhith = $T->h('Parameter_p_a_dhith');
  $T_Error_Not_owner     = $T->h('Error_Not_owner');
  $T_Attached_files_info = $T->h('Attached_files_info');
  $T_Unit_d_deleted      = $T->h('Unit_d_deleted');
  $T_Failed_to_delete_d  = $T->h('Failed_to_delete_d');

  $hl0 = $T->hl0();

  try {
    $myCLIL->dearbhaich();
    $user = $myCLIL->id;
    $servername = SM_myCLIL::servername();
    $serverhome = SM_myCLIL::serverhome();

    if (isset($_GET['cancel'])) { header("Location:$serverhome/clilstore/"); }

    if (empty($_GET['id'])) { throw new SM_MDexception(sprintf($T_Parameter_p_a_dhith,'id')); }
    $id = $_GET['id'];
    $csNavbar = SM_csNavbar::csNavbar($T->domhan,$id);

    if (empty($_GET['delete'])) {
        $DbMultidict = SM_DbMultidictPDO::singleton('r');
        $stmt1 = $DbMultidict->prepare('SELECT title,owner FROM clilstore WHERE id=:id');
        $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt1->execute();
        $stmt1->bindColumn(1,$title);
        $stmt1->bindColumn(2,$owner);
        $stmt1->fetch();
        $stmt1 = null;
        if ($user<>$owner && $user<>'admin') { throw new SM_MDexception(sprintf($T_Error_Not_owner,$owner)); }
        $stmt2 = $DbMultidict->prepare('SELECT filename FROM csFiles WHERE id=:id');
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();
        $filenameArr = $stmt2->fetchAll(PDO::FETCH_COLUMN, 0);
        $stmt2 = null;
        if (!empty($filenameArr)) {
            $T_Attached_files_info = strtr($T_Attached_files_info,
                                           [ '(' => '<i style=color:red>(',
                                             ')' => ')</i>'
                                           ]);
            $filesInfo = "$T_Attached_files_info<br>\n";
            foreach ($filenameArr as $filename) { $filesInfo .= "&nbsp;&nbsp;&nbsp;<b>$filename</b><br>\n"; }
            $filesInfo .= "<br>\n";
        } else { $filesInfo = ''; }

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

$csNavbar
<div class="smo-body-indent" style="padding-top:2em;padding-bottom:2em">

<form method="get">
$T_Delete $T_Unit $id: “<b>$title</b>”<br><br>
$filesInfo
$T_Really_delete_unit
<input type="hidden" name="id" value="$id"/>
<input type="submit" name="delete" value="$T_Delete" id="delete">
<br><br>
$T_Or_else
<input type="submit" name="cancel" value="$T_Cancel"/>
</form>

</div>
$csNavbar

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
            $message = '<p><img src="/icons-smo/sgudal.png" alt="">' . sprintf($T_Unit_d_deleted,$id) . '</p>';
        } catch (PDOException $ex) {
            $DbMultidict->rollBack();
            throw new SM_MDexception(sprintf($T_Failed_to_delete_d,$id).'<br>'.$ex->getMessage());
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
