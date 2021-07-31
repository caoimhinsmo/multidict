<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");
  header('P3P: CP="CAO PSA OUR"');

  $T = new SM_T('wordlink/index');

  $T_Help           = $T->h('Cobhair');
  $T_About_Wordlink = $T->h('About_Wordlink');
  $T_Process_page   = $T->h('Process_page');
  $T_Compose_page   = $T->h('Compose_page');
  $T_Copy_url_here  = $T->h('Copy_url_here');
  $T_Dictionary_in  = $T->h('Dictionary_in');
  $T_New_tab        = $T->h('New_tab');
  $T_Same_tab       = $T->h('Same_tab');
  $T_Popup          = $T->h('Popup');
  $T_Splitscreen    = $T->h('Splitscreen');
  $T_Esc_title      = $T->h('Esc_title');
  $T_Process        = $T->h('Process');

  $T_Webpage_language        = $T->h('Webpage_language');
  $T_Remove_exist_links      = $T->h('Remove_exist_links');
  $T_Remove_exist_links_info = $T->h('Remove_exist_links_info');
  $T_modeSelect_title        = $T->h('modeSelect_title');

  $T_modeSelect_title = strtr( $T_modeSelect_title, [ '{{Process}}' => '‘' . $T_Process . '’' ] );

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

  $hlSelect = SM_mdNavbar::hlSelect();

  $robots = ( empty($wlSession->url) ? 'index,follow' : 'noindex,nofollow' );
  $servername = $_SERVER['SERVER_NAME'];
  echo <<<EOD1
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wordlink <span navigation frame</title>
    <meta name="robots" content="$robots">
    <link rel="icon" type="image/png" href="/favicons/wordlink.png">
    <style>
        body { height:132px; margin:1px; border:3px solid orange; padding:0px; background-color:#ffd; font-family:Verdana,Tahoma,sans-serif; font-size:12pt; }
        ul.dluth { margin:0; }
        a         { text-decoration:none; }
        a:link    { color: #00f; }
        a:visited { color: #909; }
        a:hover   { color: #ff0; background-color:blue;  }
        a.button { display:inline-block; margin-left:0.8em; padding:0 4px; border-radius:4px; background-color:#75c8fb; color:white; font-size:90%; }
        a.button:hover { background-color:blue; color:white; }
        div.formitem { float:left; margin:3px 2px; }
        select { font-size:80%; }
        select.hlselect { padding:0; font-size2:70%; }
        div.nbLang { font-size:80%; color:#aaa; padding-bottom:1px; }
        div.nbLang img { margin:0 3px; }
        div.nbLang a.box { margin:0 2px; border:1px solid #aaa; padding:0 1px; background-color:white; }
        div.nbLang a:hover { background-color:blue; }
        input[type=submit]:hover { background-color:blue; color:yellow; }
    </style>
        <script>
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
    </script>
</head>
<body>
<div style="width:16px;height:16px;float:right;padding:1px"><a href="about.php" target="_top"><img src="/favicons/eu.png" alt="" title="This project has been funded with support from the EC"/></a></div>

<div>
<p style="margin:0;">
<a class="button" style="float:left;margin:0 7px 0 0;border-radius:0;padding:1px 3px;font-size:80%" href="/" target="_top">$servername</a>
<span style="background-color:orange;color:#bfb;padding:1px 3px;font-weight:bold">Wordlink</span>
<a class="button" href="help.php" target=_top>$T_Help</a>
<a class="button" href="about.php" target="_top">$T_About_Wordlink</a>
$hlSelect
</p>

<form id="wlForm" action="./" target="_top" style="margin:0 0 0 2px" onsubmit="escapeAmpersands('testing');">
<div style="width:100%;height:20px;margin-top:5px">
<p style="float:left;margin:1px 0">
<select name="upload">
<option value="0" selected>$T_Process_page</option>
<option value="2">$T_Compose_page</option>
</select>
<span style="padding:0 0.4em;font-size:90%;color:brown;background-color:white;border:1px solid #999;border-radius:3px" title="$T_Remove_exist_links_info">
<input type="checkbox" name="rmLi"$rmLiHtml/ id="rexl"><label for="rexl">$T_Remove_exist_links</label></span>
<span style="font-size:80%;padding-left:1em"><span style="color:#777">$T_Dictionary_in</span> <select name="mode" title="$T_modeSelect_title">
<option value="nt"$modeHtmlNt>$T_New_tab</option>
<option value="st"$modeHtmlSt>$T_Same_tab</option>
<option value="pu"$modeHtmlPu>$T_Popup</option>
<option value="ss"$modeHtmlSs>$T_Splitscreen</option>
</select>
</span>
</p>
</div>
<p style="margin:0;clear:both"><input type="hidden" name="sid" value="$sid"/>
<input type="text" id="url" name="url" value="$url" title="URL of the webpage" placeholder="$T_Copy_url_here" style="width:99%"/></p>
EOD1;
  echo "<div class=\"formitem\">
<div style=\"font-size:80%;color:#777\">$T_Webpage_language</div>
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
  echo <<<EOD2
<div class="formitem"><div style="font-size:80%">&nbsp;</div><input type="submit" name="go" value="$T_Process"/ style="margin-left:1em"></div>

<div class="nbLang" style="float:right;margin-top:3px;margin-right:3px;font-size:130%">
<a class="box" style="border-radius:5px" title="$T_Esc_title" onclick="escapeWL();">Esc</a>
</div>

</div>
</form>

</div>
EOD2;
?>

</body>
</html>
