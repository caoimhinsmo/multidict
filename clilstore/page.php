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

  $T = new SM_T('clilstore/page');

  $T_total      = $T->h('total');
  $T_Unit_info  = $T->h('Unit_info');
  $T_Edit       = $T->h('Edit');
  $T_Error_in   = $T->j('Error_in');
  $T_totalj     = $T->j('total');
  $T_Voc_Click_to_enable  = $T->h('Voc_Click_to_enable');
  $T_Voc_Click_to_disable = $T->h('Voc_Click_to_disable');
  $T_Edit_this_unit       = $T->h('Edit_this_unit');
  $T_Unit_info_title      = $T->h('Unit_info_title');
  $T_Open_vocabulary_list = $T->h('Open_vocabulary_list');
  $T_Add_to_portfolio     = $T->h('Add_to_portfolio');
$hl = $T->hl();
error_log("\$hl=$hl");
error_log("\$T_Add_to_portfolio=$T_Add_to_portfolio");

  try {
    if (!isset($_GET['id'])) { throw new SM_MDexception('No id parameter'); }
    $id = $_GET['id'];
    if (!is_numeric($id)) { throw new SM_MDexception('id parameter is not numeric'); }

    $serverhome = SM_myCLIL::serverhome();
    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $stmt = $DbMultidict->prepare('SELECT sl,owner,title,text,medembed,medfloat FROM clilstore WHERE id=:id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if (!($r = $stmt->fetch(PDO::FETCH_ASSOC))) { throw new SM_MDexception("No unit exists for id=$id"); }
    extract($r);

    if ($sl<>'ar') { $left = 'left';  $right = 'right'; }
     else          { $left = 'right'; $right = 'left';  }

    //Prepare media (or picture)
    if ($medfloat=='') { $medfloat = 'none'; }
    $scroll = $recordVocHtml = $portfolioHtml = $likeHtml = '';
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
                  : "<li class=right><a href='edit.php?id=$id&amp;view' class='nowordlink' target='_top'><img src='/icons-smo/peann.png' alt='$T_Edit' title='$T_Edit_this_unit'></a></li>"
                  );
    $stmt = $DbMultidict->prepare('SELECT record FROM users WHERE user=:user');
    $stmt->execute([':user'=>$user]);
    if (!empty($user)) {
        $record = $stmt->fetch(PDO::FETCH_COLUMN);
        $vocClass = ( $record ? 'vocOn' : 'vocOff');
        $recordVocHtml = "<li class=right><span class=$vocClass onclick='vocClicked(this.className);'>"
        ."<img src='/favicons/recordOff.png' alt='VocOff' title='$T_Voc_Click_to_enable'>"
                        ."<img src='/favicons/recordOn.png' alt='VocOn' title='$T_Voc_Click_to_disable'>"
                        ."</span></li>"
                        ."<li class=right><a class=$vocClass href='voc.php?user=$user&amp;sl=$sl' data-nowordlink target=voctab title='$T_Open_vocabulary_list'>V</a>";
       $stmt = $DbMultidict->prepare('SELECT pf FROM cspf WHERE user=:user ORDER BY prio DESC LIMIT 1');
       $stmt->execute([':user'=>$user]);
       if ($row  = $stmt->fetch(PDO::FETCH_ASSOC)) {
           extract($row);
           $portfolioHtml = "<li class=right><a href=portfolio.php?unit=$id target=pftab  onClick=\"pfAddUnit('$id');\" title='$T_Add_to_portfolio'>P</a>";
       }
       $stmtGetLike  = $DbMultidict->prepare('SELECT likes FROM user_unit WHERE unit=:id AND user=:user');
       $stmtGetLikes = $DbMultidict->prepare('SELECT SUM(likes) FROM user_unit WHERE unit=:id');
       $stmtGetLike->execute([':id'=>$id,':user'=>$user]);
       $stmtGetLikes->execute([':id'=>$id]);
       if ($stmtGetLike->fetchColumn()>0) { $likeClass = 'liked'; } else { $likeClass = 'unliked'; }
       $likes = $stmtGetLikes->fetchColumn() ?? 0;
       $likesHtml = ( $likes ? $likes : '');
       $likeHtml = "<li id=likeLI class=$likeClass onclick=likeClicked() title='$likes $T_total'>"
                  ."<img id=heartUnliked src='/favicons/heartUnliked.png' alt='unlike'>"
                  ."<img id=heartLiked src='/favicons/heartLiked.png' alt='like'>"
                  ."<span id='likesBadge' class='badge'>$likesHtml</span>";
    }
    $sharebuttonFB = "<iframe src='https://www.facebook.com/plugins/share_button.php?href=$serverhome/cs/$id&layout=button&size=small&mobile_iframe=true&width=60&height=20&appId [www.facebook.com]' width='60' height='20' style='border:none;overflow:hidden' scrolling='no' frameborder='0' allowTransparency='true'></iframe>";
    $shareTitle = 'Clilstore unit: ' . urlencode($title);
    $shareURL = urlencode("https://multidict.net/cs/$id");
    $sharebuttonTw = "<a id=sharebuttonTw class='nowordlink' target=_blank href='https://twitter.com/intent/tweet?text=$shareTitle&amp;url=$shareURL' title='Share via Twitter'><img src='/favicons/twitter.png'></a>";
    $sharebuttonWA = "<a id=sharebuttonWA class='nowordlink' target=_blank href='whatsapp://send?text=$shareTitle $shareURL' title='Share via Whatsapp'><img src='/favicons/whatsapp.png' alt='WA'></a>";
//    if (stripos('Mobi',$_SERVER['HTTP_USER_AGENT'])===false) { $sharebuttonWA = ''; }
    $navbar1 = <<<EOD_NB1
<ul class="linkbuts">
<li><a href="./" class="nowordlink" target="_top" title="Clilstore index page">Clilstore</a></li>
<li>$sharebuttonFB
<li>$sharebuttonTw
<li>$sharebuttonWA
$likeHtml
<li class="right"><a href="unitinfo.php?id=$id" class="nowordlink" target="_top"
    title="$T_Unit_info_title">$T_Unit_info</a></li>
$buttonedit
$recordVocHtml
$portfolioHtml
</ul>
EOD_NB1;
    $navbar2 = <<<EOD_NB2
<ul class="linkbuts">
<li><a href="./" class="nowordlink" target="_top" title="Clilstore index page">C</a></li>
$buttonsHtml
</ul>
EOD_NB2;

    echo <<<EOD1
<!DOCTYPE html>
<html lang="$sl">
<head>
    <meta charset="UTF-8">
    <title>CLILstore unit $id: $title</title>
    <link rel="StyleSheet" href="style.css?version=2014-04-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        html,body { height:100%; width:100%; overflow:auto; }
        div.none  { margin:0.5em; }
        div.left  { float:left;  margin:0.5em; }
        div.right { float:right; margin:0.5em; }
        div.text  { margin-bottom:3px; }
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
        a#sharebuttonTw       { background-color:white; }
        a#sharebuttonTw:hover { background-color:#0ff; }
        a#sharebuttonWA       { background-color:white; }
        a#sharebuttonWA:hover { background-color:#0ff; }
        span.vocOff img:nth-child(1) { display:inline; }
        span.vocOff img:nth-child(2) { display:none; }
        span.vocOn  img:nth-child(1) { display:none; }
        span.vocOn  img:nth-child(2) { display:inline; }
        a.vocOff { display:none; }
        li#likeLI.liked   #heartLiked   { display:inline; }
        li#likeLI.liked   #heartUnliked { display:none;   }
        li#likeLI.unliked #heartLiked   { display:none;   }
        li#likeLI.unliked #heartUnliked { display:inline; }
        li.liked   span.badge { color:red;  }
        li.unliked span.badge { color:grey; }
    </style>
    <script>
        function likeClicked() {
            var likeEl = document.getElementById('likeLI');
            var newLikeStatus = 'unliked', increment = -1;
            if (likeEl.className=='unliked') { newLikeStatus = 'liked'; increment = 1; }
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                var found;
                if ( this.status!=200 || !(found = this.responseText.match(/^OK:(\d+)$/)) )
                    { alert('$T_Error_in likeClicked:'+this.status+' '+this.responseText); return; }
                var likesTotal = found[1];
                likeEl.className = newLikeStatus;
                likeEl.title = likesTotal + ' $T_totalj';
                var lbEl = document.getElementById('likesBadge');
                lbEl.innerHTML = likesTotal;
            }
            xhr.open('GET', '/clilstore/ajax/setLike.php?unit=$id&newLikeStatus=' + newLikeStatus);
            xhr.send();
        }

        function vocClicked(cl) {
            var clnew, i;
            if (cl=='vocOff') { clnew = 'vocOn';  }
                         else { clnew = 'vocOff'; }
            var vocTogReq = new XMLHttpRequest();
            vocTogReq.onload = function() {
                if (this.status!=200 || this.responseText!='OK') { alert('$T_Error_in vocClicked:'+this.status+' '+this.responseText); return; }
                var vocEls = document.getElementsByClassName(cl);
                while (vocEls.length>0) { vocEls[0].className = clnew; }
            }
            vocTogReq.open('GET', '/clilstore/ajax/setVocRecord.php?vocRecord=' + clnew);
            vocTogReq.send();
        }

        function pfAddUnit(unit) {
            var xhr = new XMLHttpRequest();
            xhr.onload = function() {
                if (this.status!=200 || this.responseText!='OK') { alert('$T_Error_in pfAddUnit:'+this.status+' '+this.responseText); return; }xc
            }
            xhr.open('GET', '/clilstore/ajax/pfAddUnit.php?unit='+unit);
            xhr.send();
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
<body lang="$sl">
$navbar1
<div class="body-indent">
<wordlink noshow><p class="noshow" style="direction:ltr"><span class="csinfo">  <!--class="noshow" is for stupid IE7. Delete when IE7 is dead-->
This is a <a href="$serverhome/clilstore" class="csinfo">Clilstore</a> unit.
You can <span class="csbutton"><a href="$serverhome/cs/$id" class="csinfo">link all words to dictionaries</a></span>.
</span></p></wordlink>

<h1 style="margin:3px 0">$title</h1>

$medembedHtml
<div class="text $scroll" id="textDiv">
$text
</div>

</div>
$navbar2
<p style="clear:both;font-size:70%;margin:0;text-align:center">Short url:&nbsp;&nbsp; $serverhome/cs/$id</p>
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
