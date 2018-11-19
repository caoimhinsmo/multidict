<?php
// Takes parameters ?id=<id?&file=<file>, and returns the file with filename <file> attached to Clilstore unit <id>


  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {

    if (empty($_GET['id']))   { throw new SM_MDexception('file.php requires an id= parameter');  }
    if (empty($_GET['file'])) { throw new SM_MDexception('file.php requires a file= parameter'); }
    $id   = $_GET['id'];
    $file = $_GET['file'];

    $DbMultidict = SM_DbMultidictPDO::singleton('r');
    $stmt = $DbMultidict->prepare('SELECT bloigh,mime FROM csFiles WHERE id=:id AND filename=:filename');
    $stmt->bindParam(':id',$id,PDO::PARAM_INT);
    $stmt->bindParam(':filename',$file);
    $stmt->execute();
    $stmt->bindColumn(1,$bloigh);
    $stmt->bindColumn(2,$mime);
    if (!$stmt->fetch()) { throw new SM_MDexception("Attached file &ldquo;$file&rdquo; not found"); }
    if (empty($bloigh))  { throw new SM_MDexception('The attached file was totally empty (zero length)'); }
    if (empty($mime))    { throw new SM_MDexception('Mime type missing'); }
    header("Content-type: $mime");
    header("Content-disposition: filename=\"$file\"");
    echo $bloigh;

  } catch (Exception $e) {
     echo "<html>\n<body>\n";
     echo $e;
     echo "</body>\n</html>\n";
  }
?>
