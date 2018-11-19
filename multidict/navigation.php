<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");
  header('P3P: CP="CAO PSA OUR"');

  $sid = ( !empty($_GET['sid']) ? $_GET['sid'] : null);
  $wlSession = new SM_WlSession($sid);
  $wlSession->bestDict();
  $wlSession->storeVars();
  $sid = $wlSession->sid;
  $sl  = $wlSession->sl;
  $tl  = $wlSession->tl;
  $dict= $wlSession->dict;
  $word= $wlSession->word;
  $wfs = $wlSession->wfs;
  $mode= $wlSession->mode;

  $robots = ( empty($wlSession->word) ? 'index,follow' : 'noindex,nofollow' );
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Multidict navigation frame</title>
    <meta name="robots" content="<?php echo $robots; ?>"/>
    <style type="text/css">
        body { height:132px; margin:1px; border:3px solid orange; padding:0px; background-color:#e3ffe3; font-family:Tahoma,sans-serif; font-size:12pt; }
        input { padding:1px 4px; }
        /*--- for stupid Internet Explorer ---*/
        * {margin-top:0    !important;}
        * {margin-bottom:0 !important;}
        select { margin-bottom:1px !important;}
        /*------------------------------------*/
        ul.dluth { margin:0; }
        div.label  { font-size:80%; color:#777; }
        div.nbLang { font-size:80%; color:#aaa; padding-bottom:1px; }
        div.nbLang img { margin:0 3px; }
        div.nbLang a.box { margin:0 2px; border:1px solid #aaa; padding:0 1px; background-color:white; }
        div.nbLang a:hover { background-color:blue; }
        a         { text-decoration:none; }
        a:link    { color: #00f; }
        a:visited { color: #909; }
        a:hover   { color: #ff0; background-color:blue; }
        a.lemmalink:link,
        a.lemmalink:visited { color:brown; }
        a.lemmalink:hover   {color:#ff0; }
        a.about { display:inline-block; margin-left:0.8em; padding:0 4px; border-radius:4px; background-color:#75c8fb; color:white; font-size:90%; }
        a.about:hover { background-color:blue; color:white; }
        span.lemma0 { font-weight:bold; text-decoration:underline; color:#bb2020; }
        span.lemmaword { font-style:italic; }
        a#esc span { border:1px solid grey; border-radius:3px; padding: 0 2px; color:grey; background-color:white; }
        a#esc:hover { background-color:inherit; }
        a#esc:hover span { color:yellow; background-color:blue; }
        div.formItem { float:left; margin:1px; white-space:nowrap; overflow:hidden; }
        select { max-height:1.8em; margin-bottom:2px; margin-top:1px; }
        select[name="sl"],select[name="tl"] { width:100%; }
        select[name="dict"] { width:100%; }
        option { max-height:1.5em; }
        div#dictIcons         { height:18px; display:block; }
        div#dictIcons img     { width:16px; height:16px; margin:1px; border:none; padding:0 0 3px 0; border:none; }
        div#dictIcons img.sel { border-left: 3px solid red; border-right:3px solid red; }
        div#dictIcons img.m   { padding:1px 0 0 0; border-top:2px solid red; }         /* mini */
        div#dictIcons img.p   { padding:0 0 1px 0; border-bottom:2px solid blue; }     /* page-image */
        div#dictIcons img.pw  { padding:0 0 1px 0; border-bottom:2px solid green; }    /* Web Archive */
        div#dictIcons img.pg  { padding:0 0 1px 0; border-bottom:2px solid red; }      /* Google Books */
        div#dictIcons img.s   { padding:0 0 1px 0; border-bottom:2px dotted black; }   /* Special */
    </style>
    <script type="text/javascript">
<!--
    function bodyLoad() {
        document.getElementById('noJSinfo').style.display = 'none';
        document.getElementById('dictIcons').style.display = 'block';
        document.getElementById('swop').style.display = 'block';
    }
    function submitForm(langChanged) {
        if (langChanged=='sl') { document.getElementById('tl').value   = ''; }
        if (langChanged>'')    { document.getElementById('dict').value = ''; }
        document.getElementById('mdForm').submit();
    }
    function changeDict(dict) {
        document.getElementById('dict').value = dict;
        submitForm();
    }
    function swopLangs() {
        var slSelect = document.getElementById('sl');
        var tlSelect = document.getElementById('tl');
        var sl = slSelect.value;
        var tl = tlSelect.value;
        opt = document.createElement('option'); //Cruthaich option ùr airson sl, gus a bhith cinnteach gu bheil e sa liosta airson tl
        opt.setAttribute('value',sl);
        opt.appendChild(document.createTextNode(sl));
        tlSelect.appendChild(opt);
        slSelect.value = tl;
        tlSelect.value = sl;
        submitForm();
    }
    function slChange(lang) {
        document.getElementById('sl').value = lang;
        submitForm();
    }
    function tlChange(lang) {
        document.getElementById('tl').value = lang;
        submitForm();
    }
//-->
    </script>
</head>
<body onload="bodyLoad();">

<?php
  try {
  $cookieIcon = ( isset($_COOKIE['SM_wlUser'])
                ? '<img src="/favicons/cookie.png" alt="" title="You have cookies enabled - Good!"/>'
                : ( time()%5==0
                  ? '<div style="background-color:yellow;padding:0 0 1px 1px"><a href="cookies.html" target="MD$sid">Enable cookies</a><br>'
                   .'<span font-size="80%">for best results</span></div>'
                  : '<a href="cookies.html" target="MD$sid"><img src="/icons-smo/bronach.gif" alt="" title="No cookies? Click for advice!"/></a>'
                  )
                );
  $nbSlHtml = $wlSession->nbSlHtml();
  $wordformArr = explode('|',$wlSession->wfs);
  if (sizeof($wordformArr)<2) { $wordformHtmlFull = ''; }
  else {
      foreach ($wordformArr as $key=>&$wf) {
          if ($wf==$word) { $wf = '<span class="lemmaword">' . $wf . '</span>';}
          if ($key==0)    { $wf = '<span class="lemma0">' . $wf . '</span>'; }
          if ($key<>0)    { $wf = "<a href=\"/multidict/?sid=$sid&amp;word=$word&amp;rot=$key\" target=\"MD$sid\" class=\"lemmalink\">$wf</a>"; }
      }
      $wordformHtml = implode(' <span dir="ltr">←</span> ',$wordformArr);
      $wordformHtmlFull = <<<EODWFFH
<div class="formItem" style="margin:0 0 0 0.5%px;width:63%">
<div class="label" style="padding:4px 0 1px 0;overflow:hidden">Multidict will try these wordforms in rotation (on reclick)</div>
<div style="font-size:85%;color:brown">$wordformHtml ↩</div>
</div>
EODWFFH;
  }
  $dictClass = $wlSession->dictClass();
  if (substr($dictClass,0,1)=='p') { $pageNav = <<<EODpageNav
<input type="submit" name="go" value="<" formtarget="MD$sid" style="padding:0 3px;margin-left:1.2em" title="Page back">
<input type="submit" name="go" value=">" formtarget="MD$sid" style="padding:0 3px" title="Page forward">
EODpageNav;
  } else { $pageNav = ''; }
  echo <<<EOD1
<div style="float:right;padding:1px">$cookieIcon</div>
<p style="margin:0 0 1px 0">
<span style="background-color:orange;color:white;padding:1px 1px"><span style="font-weight:bold;color:#bfb">Multidict</span> navigation frame</span>
<a class="about" href="help.html" target="MD$sid">Help</a>
<a class="about" href="about.html" target="MD$sid">About</a></p>

<form id="mdForm" action="./" target="MD$sid" style="margin:0 0 0 2px;padding-top:1px">
<div style="width:100%;padding-top:1px">
<div class="formItem" style="width:35%;max-width:300px"><input type="hidden" name="sid" value="$sid">
<div class="label">Word &nbsp;<input type="submit" name="go" value="Go" formtarget="MD$sid" style="padding:0 3px">
$pageNav</div>
<input type="text" name="word" value="$word" title="The word to lookup in the dictionary" placeholder="Word to translate" style="width:94%">
</div>
$wordformHtmlFull
</div>
<div class="formItem" style="clear:both;min-width:95px;max-width:28%">
<div id="swop" class="label" style="float:right;padding-right:1.5em;font-weight:bold;display:none" title="swop" onclick="swopLangs();"><a>&#x2194;</a></div>
<div class="label">From</div>
<select name="sl" id="sl" title="Source language" onchange="submitForm('sl');">
EOD1;

  $slArr = SM_WlSession::slArr();
  foreach ($slArr as $lang=>$langInfo) { $slArray[$lang] = $langInfo['endonym']; }
  setlocale(LC_ALL,'en_GB.UTF-8');
  uasort($slArray,'strcoll');
  $slArray = array_merge(array(''=>'-Choose-'),$slArray);
  foreach ($slArray as $code=>$name) {
      $selectHtml = ( $sl==$code ? ' selected="selected"' : '');
      echo "  <option value=\"$code\"$selectHtml>$name</option>\n";
  }
  echo <<<EOD2
</select>$nbSlHtml
</div>
EOD2;

  $tlSelectHtml = '';
  $tlArray = $wlSession->tlArr();
  setlocale(LC_ALL,'en_GB.UTF-8');
  uasort($tlArray,'strcoll');
  foreach ($tlArray as $code=>$name) {
      $selectedHtml = ( $tl==$code ? ' selected="selected"' : '');
      $tlSelectHtml .= "  <option value=\"$code\"$selectedHtml>$name</option>\n";
  }
  $dictSelectHtml = $wlSession->dictSelectHtml();
  $dictIconsHtml  = $wlSession->dictIconsHtml();
  $dictIconHtml   = $wlSession->dictIconHtml();
  $nbTlHtml       = $wlSession->nbTlHtml();
  $formItems = <<<EOD3
<div class="formItem" style="min-width:95px;max-width:28%"><div class="label">To</div>
<select name="tl" id="tl" title="Choose a target language" onchange="submitForm('tl');">
  <option value="">-Choose-</option>
$tlSelectHtml
</select>$nbTlHtml
</div>
<div class="formItem" style="min-width:110px;max-width:40%;overflow:visible"><div class="label">Dictionary $dictIconHtml</div>
<select id="dict" name="dict" onchange="submitForm();" title="Choose a dictionary (but reselect target language first if need be)">
  <option value="">-Choose-</option>
$dictSelectHtml
</select><br>
<div id="dictIcons">$dictIconsHtml</div>
</div>
<div id="noJSinfo" style="position:absolute;bottom:4px;left:6px;font-size:55%;color:green;white-space:normal">
If JavaScript is disabled you must click Go after each language change</div>
EOD3;
  if (!empty($sl)) { echo $formItems; }
?>
</form>

<?php
  } catch (exception $e) { echo $e; }
?>

</body>
</html>
