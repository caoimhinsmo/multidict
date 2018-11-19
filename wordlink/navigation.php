<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");
  header('P3P: CP="CAO PSA OUR"');

  // Handy abbreviations
  $checked  = ' checked="checked"';
  $selected = ' selected="selected"';

  $sid = ( !empty($_GET['sid']) ? $_GET['sid'] : null);
  $wlSession = new SM_WlSession($sid);
  $wlSession->storeVars();
  $sid  = $wlSession->sid;
  $sl   = $wlSession->sl;
  $url  = $wlSession->url;
  $rmLi = $wlSession->rmLi;
  $mode = $wlSession->mode;

  if (empty($mode)) { $mode = 'ss'; }
  $rmLiHtml = ( $rmLi ? $checked : '' );
  $modeHtmlNt = ( ($mode=='nt') ? $selected : '');
  $modeHtmlSt = ( ($mode=='st') ? $selected : '');
  $modeHtmlPu = ( ($mode=='pu') ? $selected : '');
  $modeHtmlSs = ( ($mode=='ss') ? $selected : '');
  $nbSlHtml = $wlSession->nbSlHtml();

  $robots = ( empty($wlSession->url) ? 'index,follow' : 'noindex,nofollow' );
  echo <<<EOD1
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wordlink navigation frame</title>
    <meta name="robots" content="$robots">
    <link rel="icon" type="image/png" href="/favicons/wordlink.png">
    <style type="text/css">
        body { height:132px; margin:1px; border:3px solid orange; padding:0px; background-color:#ffd; font-family:Verdana,Tahoma,sans-serif; font-size:12pt; }
        ul.dluth { margin:0; }
        a         { text-decoration:none; }
        a:link    { color: #00f; }
        a:visited { color: #909; }
        a:hover   { color: #ff0; background-color:blue;  }
        a.about { display:inline-block; margin-left:0.8em; padding:0 4px; border-radius:4px; background-color:#75c8fb; color:white; font-size:90%; }
        a.about:hover { background-color:blue; color:white; }
        div.formitem { float:left; margin:3px 2px; }
        select { max-height:1.6em; }
        option { max-height:1.5em; }
        div.nbLang { font-size:80%; color:#aaa; padding-bottom:1px; }
        div.nbLang img { margin:0 3px; }
        div.nbLang a.box { margin:0 2px; border:1px solid #aaa; padding:0 1px; background-color:white; }
        div.nbLang a:hover { background-color:blue; }
    </style>
    <script type="text/javascript">
<!--
    function escapeAmpersands() {
        document.getElementById('url').value = document.getElementById('url').value.replace('&','{and}');
        return;
    }
    function submitForm() {
        document.getElementById('wlForm').submit();
    }
    function slChange(lang) {
        document.getElementById('sl').value = lang;
        submitForm();
    }
    function escapeWL() {
        window.top.location = document.getElementById('url').value;
    }
//-->
    </script>
</head>
<body>
<div style="width:16px;height:16px;float:right;padding:1px"><a href="about.html" target="_top"><img src="/favicons/eu.png" alt="" title="This project has been funded with support from the EC"/></a></div>

<div>
<p style="margin:0">
<span style="background-color:orange;color:white;padding:1px 2px"><span style="font-weight:bold;color:#bfb">Wordlink</span> navigation frame</span>
<a class="about" href="help.html" target="WLmainframe$sid">Help</a>
<a class="about" href="about.html" target="_top">About Wordlink</a>
<a class="about" href="examples.php" target="_top">Example pages</a>
</p>

<form id="wlForm" action="./" target="_top" style="margin:0 0 0 2px" onsubmit="escapeAmpersands('testing');">
<div style="width:100%;height:20px;margin-top:5px">
<p style="float:left;margin:1px 0">
<select name="upload">
<option value="0" selected="selected"/>Process the following webpage</option>
<!-- <option value="1">Upload an html page</option> --Option deleted, not working and probably not needed anyway -->
<option value="2">Compose a page</option>
</select>
<span style="padding:0 0.4em;font-size:90%;color:brown" title="If you tick this box, existing links in the document will be removed so that the link words can be linked to dictionaries">
<input type="checkbox" name="rmLi"$rmLiHtml/ id="rexl"><label for="rexl">Remove existing links</label></span>
<span style="font-size:80%"><span style="color:#777">Dictionary in</span> <select name="mode" title="Changes only take effect after clicking 'Go'">
<option value="nt"$modeHtmlNt>New tab</option>
<option value="st"$modeHtmlSt>Same tab</option>
<option value="pu"$modeHtmlPu>Popup</option>
<option value="ss"$modeHtmlSs>Splitscreen</option>
</select>
</span>
</p>
</div>
<p style="margin:0;clear:both"><input type="hidden" name="sid" value="$sid"/>
<input type="text" id="url" name="url" value="$url" title="URL of the webpage" placeholder="Type or copy the webpage address (url) to here" style="width:99%"/></p>
EOD1;
  echo "<div class=\"formitem\">
<div style=\"font-size:80%;color:#777\">Webpage language</div>
<select name=\"sl\" id=\"sl\" title=\"The language the above page is written in\" style=\"margin-bottom:2px\" onchange=\"submitForm()\">\n";
  $slArr = SM_WlSession::slArr();
  foreach ($slArr as $lang=>$langInfo) { $slArray[$lang] = $langInfo['endonym']; }
  setlocale(LC_COLLATE,'en_GB.UTF-8');
  uasort($slArray,'strcoll');
  $slArray = array_merge ( array(''=>'- Choose -'), $slArray, array('null'=>'--null--') );
  foreach ($slArray as $lang=>$endonym) {
      $selectHtml = ( $sl==$lang ? ' selected="selected"' : '');
      echo "  <option value=\"$lang\"$selectHtml>$endonym</option>\n";
  }
  echo "</select>$nbSlHtml</div>\n";
  echo <<<EOD2
<div class="formitem"><div style="font-size:80%">&nbsp;</div><input type="submit" name="go" value="Go"/ style="margin-left:1em"></div>

<div class="nbLang" style="float:right;margin-top:3px;margin-right:3px;font-size:130%">
<a class="box" style="border-radius:5px" title="Escape from Wordlink and go to the webpage itself" onclick="escapeWL();">Esc</a>
</div>

</div>
</form>

</div>
EOD2;
?>

</body>
</html>
