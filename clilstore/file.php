<?php
// Takes parameters ?id=<id?&file=<file>, and returns the file with filename <file> attached to Clilstore unit <id>
// Respects partial content HTTP requests

  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {

    if (empty($_GET['id']))   { throw new SM_MDexception('file.php requires an id= parameter');  }
    if (empty($_GET['file'])) { throw new SM_MDexception('file.php requires a file= parameter'); }
    $id   = $_GET['id'];
    $file = $_GET['file'];

    if (isset($_SERVER['HTTP_RANGE'])) {
        list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);
        if ($size_unit<>'bytes') { throw new SM_MDexception('Requested Range Not Satisfiable'); }
        //multiple ranges could be specified at the same time, but for simplicity only serve the first range
        $rangeArr = explode(',',$range_orig);
        $range = $rangeArr[0];
    } else {
        $range = '';
    }
    list($start_byte, $end_byte) = explode('-', $range, 2); //figure out download piece from range (if set)

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
    $file_size = strlen($bloigh);

    //set start and end based on range (if set), else set defaults
    //also check for invalid ranges.
    $end_byte   = (empty($end_byte)) ? ($file_size - 1) : min(abs(intval($end_byte)),($file_size - 1));
    $start_byte = (empty($start_byte) || $end_byte < abs(intval($start_byte))) ? 0 : max(abs(intval($start_byte)),0);

    header("Content-type: $mime");
    header("Content-disposition: inline;filename=\"$file\"");
    //Only send partial content header if downloading a piece of the file (IE workaround)
    if ($start_byte > 0 || $end_byte < ($file_size - 1)) {
        header('HTTP/1.1 206 Partial Content');
        header('Content-Range: bytes '.$start_byte.'-'.$end_byte.'/'.$file_size);
        header('Content-Length: '.($end_byte - $start_byte + 1));
        $bloigh = substr( $bloigh, $start_byte-1, $end_byte-$start_byte+1 );
    } else {
        header("Content-Length: $file_size");
    }
    header('Accept-Ranges: bytes');
    header('Cache-Control: max-age=3600');
    echo $bloigh;

  } catch (Exception $e) {
     echo "<html>\n<body>\n";
     echo $e;
     echo "</body>\n</html>\n";
  }
?>
