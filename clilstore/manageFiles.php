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

  function scriptscan($text) {
      if (preg_match('|<\?php|iu', $text)) { return 'PHP'; }
      if (preg_match('|<\?|iu',    $text)) { return 'scripting'; }
      if (preg_match('|<script|iu',$text)) { return 'scripting'; }
      return '';
  }

  try {
    $myCLIL->dearbhaich();
    $user = $myCLIL->id;
    if (!isset($_REQUEST['id'])) { throw new SM_MDexception('No id parameter'); }
    $id = $_REQUEST['id'];

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    //Initialisations
    $errorMessage = $happyMessage = '';
    $scriptingMessage = 'Scripting (PHP, Javascript, etc) is not allowed. You appear to have %s in the %s.';

    //Check that the unit exists and that the user is owner of it
    $stmt = $DbMultidict->prepare('SELECT owner FROM clilstore WHERE id=:id');
    $stmt->execute(array('id'=>$id));
    $stmt->bindColumn(1,$owner);
    if (!$stmt->fetch()) { throw new SM_MDexception("No unit found for id=$id"); }
    if ($user<>$owner && $user<>'admin') { throw new SM_MDexception('You may only edit your own units'); }

    $stmtUpdateFileCount = $DbMultidict->prepare('update clilstore set files= (select count(1) from csFiles where id=?) where id=?');

    if (!empty($_REQUEST['save'])) {  //A form has been submitted, so deal with it
        $action = $_REQUEST['save'];
        if ($action=='delete') {
            if (!isset($_REQUEST['fileid'])) { throw new SM_MDexception('Missing fileid= parameter'); }
            $fileid = $_REQUEST['fileid'];
            $stmt = $DbMultidict->prepare('DELETE FROM csFiles WHERE id=:id AND fileid=:fileid');
            $stmt->execute(array(':id'=>$id,':fileid'=>$fileid));
            if ($stmt->rowcount()) { $happyMessage = '✔ File deleted'; }
                             else  { $errorMessage = 'Failed to delete file'; }
            $stmt = null;
            $stmtUpdateFileCount->execute(array($id,$id));
        } elseif ($action=='Save any name changes') {
            $fileidArr   = $_POST['fileid'];
            $filenameArr = $_POST['filename'];
            $stmt = $DbMultidict->prepare('UPDATE csFiles SET filename=:filename WHERE id=:id AND fileid=:fileid');
            foreach ($fileidArr as $i=>$fileid) {
                $filename = $filenameArr[$i];
                $stmt->execute(array(':filename'=>$filename,':id'=>$id,':fileid'=>$fileid));
                if ($stmt->rowcount()) { $happyMessage .= "✔ Filename changed to &ldquo;$filename&rdquo;<br>\n"; }
            }
        } elseif ($action=='Upload') {
            if (empty($_FILES['bloigh']))  { throw new SM_MDexception('No uploaded file was received'); }
            $bloighInfo = $_FILES['bloigh'];
            $bloighError = $bloighInfo['error'];
            $bloighSize  = $bloighInfo['size'];
            $bloighType  = $bloighInfo['type'];
            $tmp_name    = $bloighInfo['tmp_name'];
            if      ($bloighError==4) { $bloighError = 'Looks like you hadn’t chosen any file'; }
             elseif ($bloighError==2) { $bloighError = 'File too large - exceeds Clilstore upload limit of 3MB'; }
             elseif ($bloighError==3) { $bloighError = 'Failed partway through'; }
            if     (!empty($bloighError)) { $errorMessage = "Upload error: $bloighError"; }
            elseif ($bloighSize>1048000)  { $errorMessage = "File exceeds the 1MB limit for files in Clilstore"; }
            else {
                $bloigh = file_get_contents($tmp_name);
                $filename = $_POST['filenameUpl'];
                $stmt = $DbMultidict->prepare('SELECT filename FROM csFiles WHERE id=:id AND filename=:filename');
                $stmt->execute(array('id'=>$id,'filename'=>$filename));
                if ($stmt->fetch()) { $errorMessage = "You already have an attached file called &ldquo;$filename&rdquo;<br>"
                                                     ."If you want to replace it, you need to delete the old version first"; }
                else {
                    $stmt = $DbMultidict->prepare('INSERT into csFiles(id,filename,bloigh,mime) VALUES (?,?,?,?)');
                    $stmt->bindParam(1,$id);
                    $stmt->bindParam(2,$filename);
                    $stmt->bindParam(3,$bloigh,PDO::PARAM_LOB);
                    $stmt->bindParam(4,$bloighType);
                    $stmt->execute();
                    if ($stmt->rowcount()) { $happyMessage = '✔ File uploaded'; }
                                     else  { $errorMessage = 'Failed to load the file into Clilstore'; }
                }
            }
            $stmtUpdateFileCount->execute(array($id,$id));
        }
    }


 // Create a form
    $stmt = $DbMultidict->prepare('SELECT fileid, filename, LENGTH(bloigh) FROM csFiles WHERE id=:id ORDER BY filename');
    $stmt->execute(array('id'=>$id));
    $stmt->bindColumn(1,$fileid);
    $stmt->bindColumn(2,$filename);
    $stmt->bindColumn(3,$filesize);
    $filesHtml = '';
    while ($r = $stmt->fetch()) {
        if ($filesize<10000) { $filesize .= ' bytes'; } else { $filesize = round($filesize/1024) . 'KB'; }
        $filesHtml .= <<<EODeditFile
<tr>
<td><input name="filename[]" value="$filename" title="filesize $filesize"><input type="hidden" name="fileid[]" value="$fileid"></td>
<td><a href="/cs/$id/$filename"><img src="/icons-smo/td.gif" title="View this file" alt="View"></a></td>
<td><a href="./manageFiles.php?save=delete&amp;id=$id&amp;fileid=$fileid"><img src="/icons-smo/curAs.png" title="Delete this file (immediately and permanently)" alt="Delete"></a></td>
</tr>
EODeditFile;
    }
    if (empty($filesHtml)) {
        $mainForm = '<p>The unit currently has no attached files</p>';
    } else {
        $mainForm = <<<EODmainForm
<form method="post" name="mainForm">
<fieldset style="margin:6px 0 0 0;border:1px solid grey;padding:10px;border-radius:5px;background-color:#fafaff">
<legend style="font-weight:bold">Rename or delete existing files</legend>
<table id="filesbuts">
<tr style="font-size:85%">
<td>File <span class="info">(you can edit its name here to change it)</span></td>
<td>View</td>
<td>Delete</td>
</tr>
$filesHtml
</table>
<div style="margin-top:2px">
<input type="submit" name="save" id="save" value="Save any name changes">
</div>
</fieldset>
</form>
EODmainForm;
    }

    $legend = "Manage files attached to Clilstore unit $id";

    if (!empty($errorMessage)) { $errorMessage = '<div class="errorMessage">'.$errorMessage.'<br><br>No changes were made</div>'; }
    if (!empty($happyMessage)) { $happyMessage = '<div class="happyMessage">'.$happyMessage.'</div>'; }

    echo <<<EOD1
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>$legend</title>
    <link rel="stylesheet" href="/css/smo.css" type="text/css">
    <link rel="stylesheet" href="style.css?version=2014-04-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style type="text/css">
        table#filesbuts { margin-bottom:0.2em; }
        table#filesbuts td:nth-child(1) input { min-width:20em; background-color:#bfe; color:black; font-size:100%;
                                                   padding:3px 6px; border:1px solid green; border-radius:4px; }
        table#filesbuts td:nth-child(2)       { width:1.5em; text-align:center; }
        table#filesbuts td:nth-child(3)       { width:1.8em; text-align:center; }
        div.box { border:1px solid black; padding:4px; border-radius:4px; background-color:#ffd; }
        div.errorMessage { margin:0.5em 0; color:red;   font-weight:bold; }
        div.happyMessage { margin:0.5em 0; color:green; font-weight:bold; }
        input[type=submit], a.button { font-size:112%; background-color:#55a8eb; color:white; font-weight:bold; padding:3px 10px; border:0; border-radius:8px; }
        input[type=submit]:hover, a.button:hover { background-color:blue; }
        .info { color:green; font-size:80%; }
    </style>
    <script>
        <!--
        function makeNiceFilename(value) {
            var niceName = value.split("\\\\").pop().split("/").pop();
            var el = document.getElementById('filenameUpl');
            el.value = niceName;
            document.getElementById('nicenameDiv').style.display = 'block';
        }
        //-->
    </script>
</head>
<body>

<ul class="linkbuts">
<li><a href="./" title="Clilstore index page">Clilstore</a>
<li><a href="./unit.php?id=$id">Unit $id</a>
<li><a href="./edit.php?id=$id">Edit unit</a>
</ul>
<div class="smo-body-indent">
$errorMessage
$happyMessage

<div style="margin:0.5em 2em;padding:0 0.5em;border:1px solid green;border-radius:0.5em">
<p class="info">You can upload files which will be attached to this unit.  Make sure that you name them with the correct filename extension: <b>.html</b> or <b>.docx</b> or <b>.pdf</b> or whatever, as appropriate to their file type.<br>
And give them sensible “computer-friendly” names such as “<b>Exercise1.html</b>” rather than the likes of like “<b>Exercise écrit, N°1. - Très important!.html</b>”, which would very likely give the computer indigestion.</p>

<p class="info">If you have an attached file called “<b>Exercise.pdf</b>”, for example, you can link to it from one of the link buttons on the unit by writing “<b>file:Exercise.pdf</b>”.<br>
If you have an attached picture called “<b>photo.jpg</b>?”, you can incorporate it within the unit via the url “<b>http://multidict.net/cs/<i>nnnn</i>/photo.jpg</b>”, where <i>nnnn</i> is the unit number.</p>
</div>

<fieldset style="background-color:#eef;border:8px solid #55a8eb;border-radius:10px">
<legend style="background-color:#55a8eb;color:white;padding:2px 4px;border:0;border-radius:4px">$legend</legend>

$mainForm

<fieldset style="margin:1.5em 0 0 0;border:1px solid grey;padding:10px;border-radius:5px;background-color:#fafaff">
<legend style="font-weight:bold">Upload a new file</legend>
<form id="upload_form" enctype="multipart/form-data" method="post">
<div>
    <label for="bloigh">Choose the file on your computer</label>
    <input type="hidden" name="MAX_FILE_SIZE" value="3000000">
    <input type="file" name="bloigh" id="bloigh" style="width:16em" onchange="makeNiceFilename(this.value)">
</div>
<div style="display:none" id="nicenameDiv">
    <label for="filenameUpl">and the name it will have in Clilstore</legend>
    <input name="filenameUpl" id="filenameUpl" style="width:16em"> <span class="info">You can change this recommendation if you want, but the filename must be something sensible for a computer file - See above.<br></span>
</div>
<div style="margin-top:0.4em">
    <input type="submit" name="save" value="Upload">
    <span class="info">Remember that you must not upload copyrighted material</span>
</div>
</form>
</fieldset>

</fieldset>

</div>
<ul class="linkbuts" style="margin-top:1.5em">
<li><a href="./" title="Clilstore index page">Clilstore</a></li>
<li><a href="./unit.php?id=$id">Unit $id</a>
<li><a href="./edit.php?id=$id">Edit unit</a>
</ul>

</body>
</html>
EOD1;

  } catch (Exception $e) { echo $e; }

?>
