<?php
  if (!include('autoload.inc.php'))
    header("Location:https://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  try {
      $myCLIL = SM_myCLIL::singleton();
      $user = ( isset($myCLIL->id) ? $myCLIL->id : '' );
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }
  if (isset($_GET['user'])) { $user = $_GET['user']; } //for generating edit button

  try {
    if (!isset($_GET['id'])) { throw new SM_MDexception('No id parameter'); }
    $id = $_GET['id'];
    if (!is_numeric($id)) { throw new SM_MDexception('id parameter is not numeric'); }

    $serverUrl = ( empty($_SERVER['HTTPS']) ? 'https' : 'http' ) . '://' . $_SERVER['SERVER_NAME'];
    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $stmt = $DbMultidict->prepare('SELECT sl,owner,title,text,medembed,medfloat FROM clilstore WHERE id=:id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if (!($r = $stmt->fetch(PDO::FETCH_OBJ))) { throw new SM_MDexception("No unit exists for id=$id"); }
    $stmt = null;
    $sl       = $r->sl;
    $owner    = $r->owner;
    $title    = $r->title;
    $text     = $r->text;
    $medembed = $r->medembed;
    $medfloat = $r->medfloat;

    if ($sl<>'ar') { $left = 'left';  $right = 'right'; }
     else          { $left = 'right'; $right = 'left';  }

    //Prepare media (or picture)
    if ($medfloat=='') { $medfloat = 'none'; }
    $scroll = $recordVocHtml = '';
    if ($medfloat=='scroll') { $medfloat = 'none'; $scroll='scroll'; }
    $medembedHtml = ( empty($medembed) ? '' : "<div class=\"$medfloat\">$medembed</div>" );

    //Prepare linkbuttons
    $buttonsHtml = '';
    $stmt = $DbMultidict->prepare('SELECT but,wl,new,link FROM csButtons WHERE id=:id ORDER BY ord');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $stmt->bindColumn(1,$but);
    $stmt->bindColumn(2,$wl);
    $stmt->bindColumn(3,$new);
    $stmt->bindColumn(4,$link);
    while ($stmt->fetch()) { 
       if (empty($but) or empty($link)) { continue; }
        if (is_numeric($link))          { $link = "/cs/$link"; unset($wl); } //a link to another unit
        if (substr($link,0,5)=='file:') { $link = "/cs/$id/".substr($link,5); }         //a link to an attached file
        if (empty($wl)) { $class = 'class="authorbut nowordlink"'; }
         else           { $class = 'class="authorbut"';  }
        if ($new) { $target = 'target="_blank"'; }
         else     { $target = 'target="_top"';   }
        $buttonsHtml .= "<li><a href=\"$link\" $class $target>$but</a></li>\n";
    }
    $buttonedit = ( $user<>$owner && $user<>'admin'
                  ? ''
                  : "<li class=right><a href=\"edit.php?id=$id&amp;view\" class=\"nowordlink\" target=\"_top\"><img src=\"/icons-smo/peann.png\" alt=\"Edit\" title=\"Edit this unit\"></a></li>"
                  );
    $stmt = $DbMultidict->prepare('SELECT record FROM users WHERE user=:user');
    $stmt->execute([':user'=>$user]);
    if (!empty($user)) {
        $record = $stmt->fetch(PDO::FETCH_COLUMN);
        $vocClass = ( $record ? 'vocOn' : 'vocOff');
        $recordVocHtml = "<li class=right><span class=$vocClass onclick='vocClicked(this.className);'>"
                        ."<img src='/favicons/recordOff.png' alt='VocOff' title='Vocabulary recording is currently disabled - Click to enable'>"
                        ."<img src='/favicons/recordOn.png' alt='VocOn' title='Vocabulary recording is currently enabled - Click to disable'>"
                        ."</span></li>"
                        ."<li class=right><a class=$vocClass href='voc.php?user=$user&amp;sl=$sl' nowordlink target=voctab title='Open your vocabulary list in a separate tab'>V</a>";
    }
    $linkbuttons = <<<EOBUT
<ul class="linkbuts" title="Navigate to other pages (Right-click to open in a new browser tab or window)">
<li><a href="./" class="nowordlink" target="_top" title="Clilstore index page">Clilstore</a></li>
$buttonsHtml
<li><a href="$serverUrl/wordlink/?navsize=1&amp;sl=$sl&amp;url=referer"
    title="Wordlink this page (link it word by word to dictionaries)">Wordlink</a></li>
<li class="right"><a href="unitinfo.php?id=$id" class="nowordlink" target="_top"
    title="Summary and other details on this unit">Unit info</a></li>
$buttonedit
$recordVocHtml
</ul>
EOBUT;

    echo <<<EOD1
<!DOCTYPE html>
<html lang="$sl">
<head>
    <meta charset="UTF-8">
    <title>CLILstore unit $id: $title</title>
    <link rel="StyleSheet" href="style.css?version=2014-04-15" type="text/css">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style type="text/css">
        html,body { height:100%; width:100%; overflow:auto; }
        div.none  { margin:0.5em; }
        div.left  { float:left;  margin:0.5em; }
        div.right { float:right; margin:0.5em; }
        div.text  { margin-bottom:3px; }
/* Previous version
        div.scroll{ width:100%; height:400px; border:1px solid #797; background-color:#efe; padding:2px 1px 1px 2px; overflow:auto; -webkit-overflow-scrolling2:touch; }
*/
        div.scroll{ width:100%; height:400px; padding:2px 1px 1px 2px; overflow:auto; }
        ul.linkbuts { float:$left; }
        ul.linkbuts li { float:$left; }
        ul.linkbuts li.right { float:$right; }
        body { margin:0; padding:0; font-family:Tahoma,Verdana,Ariel,Helvetica,sans-serif; }
        div.body-indent { clear:both; margin:0 0.25%; padding:0 0.25% 0px 0.25%; border-top:1px solid white; }
        div.body-indent:lang(ar),
        div.body-indent:lang(ur) { direction:rtl; font-size:140%; line-height:1.25em; font-family:"Times Roman","Times New Roman"; }
        h1:lang(ar) { font-size:150%; }
        ul.linkbuts:lang(ar) { font-size:120%; }
        span.csinfo      { border:1px solid green; border-radius:5px; padding:3px 5px; background-color:#ff0; }
        span.csbutton    { padding:0 1px 0 3px; background-color:#ff6; font-weight:bold; }
        span.csinfo a    { text-decoration:none; }
        a.csinfo:link    { color:#00f; }
        a.csinfo:visited { color:#909; }
        a.csinfo:hover   { color:#ff0; background-color:blue; text-decoration:underline; }
        span.vocOff img:nth-child(1) { display:inline; }
        span.vocOff img:nth-child(2) { display:none; }
        span.vocOn  img:nth-child(1) { display:none; }
        span.vocOn  img:nth-child(2) { display:inline; }
        a.vocOff { display:none; }
    </style>
    <script>
        function vocClicked(cl) {
            var clnew, i;
            if (cl=='vocOff') { clnew = 'vocOn';  }
                         else { clnew = 'vocOff'; }
            var url = '$serverUrl/clilstore/ajax/setVocRecord.php?vocRecord=' + clnew;
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open('GET', url, false);
            xmlhttp.send();
            var resp = xmlhttp.responseText;
            if (resp!='OK') { alert('Error in vocClicked: ' + resp); }
            var vocEls = document.getElementsByClassName(cl);
            while (vocEls.length>0) { vocEls[0].className = clnew; }
        }

        function sizeTextDiv() {
            if (document.getElementById('textDiv').className.indexOf('scroll')==-1) { return; } //No need to do anything if non-scrolling
            var winH = 460;
           //Find window height.  Next three lines copied from Internet bulletin board.  Should suit most browsers.
            if (document.body && document.body.offsetWidth) { winH = document.body.offsetHeight; }
            if (document.compatMode=='CSS1Compat' && document.documentElement && document.documentElement.offsetWidth ) { winH = document.documentElement.offsetHeight; }
            if (window.innerWidth && window.innerHeight) { winH = window.innerHeight; }
           //Find top of scrolling area and work out what is still available for it.
            var rectTop = document.getElementById('textDiv').getBoundingClientRect().top;
            var available = winH - rectTop -70;
            if (available<160) { available = 160; }
            document.getElementById('textDiv').style.height = available+'px';
/*
//Unsuccessful attempts to get momentum scrolling back working on the iPad after the resize of the scrolling area.
//Delete all this sometime, or perhaps continue again sometime with trying to find a method which works.
            var userAgent = navigator.userAgent;
            alert('userAgent='+userAgent);
            if ( userAgent.indexOf('iPad') > -1 ) {
                alert('Have guessed this is an iPad');
                document.getElementById('textDiv').style.overflow = 'scroll';
                document.getElementById('textDiv').style.WebkitOverflowScrolling = 'touch';
                document.getElementById('textDiv').style.backgroundColor = '#f99';
            }
*/
        }

        window.addEventListener('load',sizeTextDiv);

//Causes too many resizes on tablets
//        function resizeAlert() {
//            alert('Window resize detected.  About to resize the scrolling area to fit the window height.');
//            sizeTextDiv();
//        }
//        window.addEventListener('resize',resizeAlert);


//Doesn’t seem to be working
//        function reorientAlert() {
//            alert('Orientation change detected.  About to resize the scrolling area to fit the window height.');
//            sizeTextDiv();
//        }
//        window.addEventListener('orientationchange',reorientAlert);

    </script>
</head>
<body lang="$sl" onload2="loadAlert();">
$linkbuttons
<div class="body-indent">
<wordlink noshow><p class="noshow" style="direction:ltr"><span class="csinfo">  <!--class="noshow" is for stupid IE7. Delete when IE7 is dead-->
This is a <a href="$serverUrl/clilstore" class="csinfo">Clilstore</a> unit.
You can <span class="csbutton"><a href="$serverUrl/cs/$id" class="csinfo">link all words to dictionaries</a></span>.
</span></p></wordlink>

<h1 style="margin:3px 0">$title</h1>

$medembedHtml
<div class="text $scroll" id="textDiv">
$text
</div>

</div>
$linkbuttons
<p style="clear:both;font-size:70%;margin:0;text-align:center">Short url:&nbsp;&nbsp; $serverUrl/cs/$id</p>
</body>
</html>
EOD1;

    //Update hit count
    $stmt = $DbMultidict->prepare('UPDATE clilstore SET views=views+1 WHERE id=:id');
    $stmt->bindParam(':id',$id);
    $stmt->execute();
    $stmt = null;
    

  } catch (Exception $e) { echo $e; }

?>
