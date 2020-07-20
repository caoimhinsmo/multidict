<?php
    if (!include('autoload.inc.php'))
      header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

    $myCLIL = SM_myCLIL::singleton();
    $user = $myCLIL->id;

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    if (empty($_POST['id'])) { die('No id parameter'); }
    $id = $_POST['id'];
    $stmt = $DbMultidict->prepare('SELECT owner FROM clilstore WHERE id=:id');
    $stmt->execute([':id'=>$id]);
    $owner = $stmt->fetchColumn();
    if ($user<>$owner && $user<>'admin') { die('Unauthorized'); }

    if (empty($_FILES['bloigh']))  { die('No uploaded file was received'); }
    $bloighInfo = $_FILES['bloigh'];
    $bloighError = $bloighInfo['error'];
    $bloighSize  = $bloighInfo['size'];
    $bloighType  = $bloighInfo['type'];
    $bloighName  = $bloighInfo['name'];
    $tmp_name    = $bloighInfo['tmp_name'];
    if      ($bloighError==1) { die('File too large - exceeds the website’s php.ini limit'); }
     elseif ($bloighError==2) { die('File too large - exceeds Clilstore upload limit of 3MB'); }
     elseif ($bloighError==3) { die('Failed partway through'); }
     elseif ($bloighError==4) { die('Looks like you hadn’t chosen any file'); }
     elseif ($bloighSize>1048000)  { die('File exceeds the 1MB limit for files in Clilstore'); }
    else {
        $bloigh = file_get_contents($tmp_name);
        $filename = $bloighName;
        $stmt = $DbMultidict->prepare('SELECT filename FROM csFiles WHERE id=:id AND filename=:filename');
        $stmt->execute(array('id'=>$id,'filename'=>$filename));
        if ($stmt->fetch()) { die("You already have an attached file called [$filename]\n"
                                 ."If you want to replace it, you need to delete the old version first"); }
        else {
            $stmt = $DbMultidict->prepare('INSERT into csFiles(id,filename,bloigh,mime) VALUES (?,?,?,?)');
            $stmt->bindParam(1,$id);
            $stmt->bindParam(2,$filename);
            $stmt->bindParam(3,$bloigh,PDO::PARAM_LOB);
            $stmt->bindParam(4,$bloighType);
            $stmt->execute();
            if ($stmt->rowcount()==0) { die('Failed to load the file into Clilstore'); }
            $fileid = $DbMultidict->lastInsertId();
        }
    }
    $stmtUpdateFileCount = $DbMultidict->prepare('update clilstore set files= (select count(1) from csFiles where id=:id) where id=:id');
    $stmtUpdateFileCount->execute([':id'=>$id]);
    echo "OK-$fileid";
?>