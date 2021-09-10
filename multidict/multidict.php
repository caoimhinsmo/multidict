<?php
  if (!include('autoload.inc.php'))
    header("Location:https://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

   header('P3P: CP="CAO PSA OUR"');

  function getDictLangInfo($dict,$lang,&$langCode,&$encoding) {
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $query = "SELECT langCode,encoding FROM dictLang WHERE dict=:dict AND lang=:lang";
      $stmt = $DbMultidict->prepare($query);
      $stmt->execute(array(':dict'=>$dict,':lang'=>$lang));
      $row = $stmt->fetch(PDO::FETCH_OBJ);
      if ($row===FALSE) { throw new SM_MDexception("Multidictionary $dict does not cater for language $lang"); }
      $langCode = $row->langCode;
      $encoding = $row->encoding;
      $stmt = null;
      if (empty($langCode)) { $langCode = $lang; }
      return;
  }

  function lemmatize($word,$sl) {
/* This function now does nothing. It has been replaced by the more general dictionary headwords mechanism.
      if ((($sl=='gd') or ($sl=='ga') or ($sl=='sga')) and (substr($word,1,1)=='h')) { $word = substr($word,0,1).substr($word,2); }
      if ($sl=='ga') {
          $first    = substr($word,0,1); //first letter
          $second   = substr($word,1,1); //second letter
          $firstTwo = substr($word,0,2); //first two letters
          if ($firstTwo=='mb') { $word = 'b'.substr($word,2); }
          if ($firstTwo=='gc') { $word = 'c'.substr($word,2); }
          if ($firstTwo=='nd') { $word = 'd'.substr($word,2); }
          if ($firstTwo=='bf') { $word = 'f'.substr($word,2); }
          if ($firstTwo=='ng') { $word = 'g'.substr($word,2); }
          if ($firstTwo=='dt') { $word = 't'.substr($word,2); }
          if ($firstTwo=='ts') { $word = 's'.substr($word,2); }
          if ($firstTwo=='mB') { $word = 'B'.substr($word,2); }
          if ($firstTwo=='gC') { $word = 'C'.substr($word,2); }
          if ($firstTwo=='nD') { $word = 'D'.substr($word,2); }
          if ($firstTwo=='bF') { $word = 'F'.substr($word,2); }
          if ($firstTwo=='nG') { $word = 'G'.substr($word,2); }
          if ($firstTwo=='dT') { $word = 'T'.substr($word,2); }
          if ($firstTwo=='tS') { $word = 'S'.substr($word,2); }
          if ( ($first=='n') and (strpos('AEIOUÁÉÍÓÚ',$second)  !== false )) { $word = substr($word,1); }
          if ( ($first=='h') and (stripos('AEIOUÁÉÍÓÚ',$second) !== false )) { $word = substr($word,1); }
      }
*/
      return $word;
  }

  function toEntities($word) {
      // Converts 'é', for example, in the middle of a word to '&#233;' (and urlencodes it), for dictionaries which need such rubbish
      $newWord = '';
      preg_match_all('/./u',$word,$charArr);
      $charArr = $charArr[0];
      foreach ($charArr as $char) {
          if (strlen($char)==1) { $newWord .= $char; }
                           else { $c = unpack("N", mb_convert_encoding($char, 'UCS-4BE', 'UTF-8'));
                                  $newWord .= '%26%23'.$c[1].'%3B'; }
      }
      return $newWord;
  }

  function createForm($url,$pparams) {
      // This will create a form and use Javascript to submit it automatically
      // (Probably only works at present for dictionaries using UTF-8)
      $fieldsHtml = '';
      $pparams_arr = explode('&',$pparams);
      foreach ($pparams_arr as $pparam) {
          list($key,$value) = explode('=',$pparam,2);
          $fieldsHtml .= "<tr><td>$key</td><td><input name=\"$key\" value=\"$value\"></td></tr>\n";
      }
      $html = <<<FORMHTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Form to lookup word in dictionary</title>
</head>
<body>
<p>Form to look up a word in the dictionary</p>
<form id="lookupForm" name="lookupForm" method="post" action="$url">
<table style="margin-left:2em">
$fieldsHtml
<tr><td></td><td><input name="cuir" type="submit" value="Submit"></td></tr>
</table>
</form>
<script>
    document.getElementById('lookupForm').submit();
</script>

<p style="color:red;font-size:85%">
(This form should have been submitted automatically.  If you need to click “Submit” yourself, you must have
Javascript disabled in your browser.)</p>
</body>
</html>
FORMHTML;
      return $html;
  }

  function createClick($url,$message,$sid) {
      // This will create a page with a button to click to open the dictionary results manually
      if ($message) { $message = "<br>\n<span style=color:red>$message</span>"; }
      $html = <<<CLICKHTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Form to click lookup word in dictionary</title>
    <style>
        a.button { display:inline-block; margin-left:0.8em; padding:0 4px; border-radius:4px; background-color:#75c8fb; color:white; text-decoration:none; }
        a.button:hover { background-color:blue; color:white; }
    </style>
</head>
<body style='font-family:Verdana,sans-serif'>
<p>Click “Submit” to look up the word in the dictionary</p>
<p><a href='$url' target='dictab$sid' class=button>Submit</a> - <i>The results will be opened in a new tab$message</i></p>
<p><i>or</i></p>
<p><a href='$url' target='_top' class=button>Submit2</a> - <i>The results will be opened in this tab$message</i></p>
<p>$message</p>
</body>
</html>
CLICKHTML;
      return $html;
  }

  function createPopup($url,$message,$sid) {
      // This will create a button to click to open the dictionary results in a popup window
      if ($message) { $message = "<br>\n<span style=color:red>$message</span>"; }
      $html = <<<POPUPHTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Form to click lookup word in dictionary</title>
    <style>
        a.button { display:inline-block; margin-left:0.8em; padding:0 4px; border-radius:4px; background-color:#75c8fb; color:white; text-decoration:none; }
        a.button:hover { background-color:blue; color:white; }
    </style>
    <script>
      function popup(url) {
          var dictLeft=0,dictTop=0,dictRight=200,dictBottom=200;

          var dpr = window.devicePixelRatio;
          var sX = window.screenX;
          var sY = window.screenY;
          var oh = window.outerHeight;
          var ih = window.innerHeight;
          var ow = window.outerWidth;
          var iw = window.innerWidth;

          var popupLeft, popupTop, popupWidth, popupHeight;
          if (window.mozInnerScreenX) { //Firefox
              popupLeft = window.mozInnerScreenX;
              popupTop  = window.mozInnerScreenY;
              popupWidth  = iw;
              popupHeight = ih*0.9;
          } else {
              popupLeft = sX + ow - (iw*dpr);
              popupTop  = sY + oh - (ih*dpr);
              popupWidth  = iw*dpr;
              popupHeight = ih*dpr*0.9;
          }
          var popupDimensions = 'left='+popupLeft + ',top='+popupTop + ',width='+popupWidth + ',height='+popupHeight;
          window.open(encodeURI(url), 'dictwin', popupDimensions);
      }
    </script>
</head>
<body onload="popup('$url')">
<p>Click “Submit” to look up the word in the dictionary</p>
<p><a href="javascript:popup('$url')" target2='dictab$sid' class=button>Submit</a></p>
<p>The results will be opened in a new popup window$message</p>
</body>
</html>
POPUPHTML;
      return $html;
  }


  try {

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $sid = ( !empty($_GET['sid']) ? $_GET['sid'] : null);
    $wlSession = new SM_WlSession($sid);
    $wlSession->bestDict();
    $wlSession->storeVars();
    $uid  = $wlSession->uid;
    $sl   = $wlSession->sl;
    $tl   = $wlSession->tl;
    $dict = $wlSession->dict;
    $inc  = $wlSession->inc;
//    $word = $wlSession->word;  //Previous methodology, with no lemmatization
    $word = explode('|',$wlSession->wfs)[0]; //Take the first wordform

    header("Cache-Control:max-age=0");

    if (empty($word)) { throw new SM_MDexception("{blankpage}"); }
    if (empty($sl))   { throw new SM_MDexception("You need to choose a source language"); }
    if (empty($tl))   { throw new SM_MDexception("No target language specified"); }

    $query = "SELECT sl,url,urlc,pparams,encoding,charextra,handling,message FROM dictParam "
           . " WHERE (dict LIKE ? AND sl LIKE ? and tl LIKE ?)"
           .    " OR (dict LIKE ? AND sl LIKE '¤')"
           . " ORDER BY quality DESC";
    $stmt = $DbMultidict->prepare($query);
    $stmt->bindParam(1,$dict);
    $stmt->bindParam(2,$sl);
    $stmt->bindParam(3,$tl);
    $stmt->bindParam(4,$dict);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_OBJ);
    if ($row===FALSE) { throw new SM_MDexception("Multidict knows of no such dictionary spec: $dict|$sl|$tl"); }
    $slDict       = $row->sl;
    $url          = $row->url;
    $urlc         = $row->urlc;
    $pparams      = $row->pparams;
    $paramEncoding= $row->encoding;
    $charExtra    = $row->charextra;
    $handling     = $row->handling;
    $message      = $row->message;
    $stmt = null;

    SM_WlSession::updateCalls($sl,$tl,$dict);
    SM_WlSession::updateWlUserSlTl($uid,$sl,$tl,$dict);

    if ($slDict=='¤') {  //Multidictionary - Substitute for {sl} and {tl} in $url and $pparams
        getDictLangInfo($dict,$sl,$slCode,$slEncoding);
        getDictLangInfo($dict,$tl,$tlCode,$tlEncoding);
        if ($dict=='Dicts.info' && $slCode>$tlCode) { //Dicts.info requires language names in alphabetical order.  Swop if need be.
            $tmp = $slCode;
            $slCode = $tlCode;
            $tlCode = $tmp;
        }
        $url     = str_replace('{sl}',$slCode,$url);
        $url     = str_replace('{tl}',$tlCode,$url);
        $pparams = str_replace('{sl}',$slCode,$pparams);
        $pparams = str_replace('{tl}',$tlCode,$pparams);
        if (!empty($slEncoding)) { $paramEncoding = $slEncoding; }
    }
    if (empty($paramEncoding)) { $paramEncoding = 'UTF-8'; }

    $charExtraItems = explode('|',$charExtra);
    foreach ($charExtraItems as $charExtraItem) {
        $charExtraItem = trim ($charExtraItem);
        if ($charExtra=='entity')          { $word = toEntities($word); }
        if ($charExtraItem=='stripAccent') { setlocale(LC_CTYPE, 'en_GB.utf8');  $word = iconv('UTF-8','US-ASCII//TRANSLIT',$word); }
        if ($charExtraItem=='urlencode')   { $word = urlencode(iconv('UTF-8',$paramEncoding,$word)); }
        if ($charExtraItem=='lc')          { $word = strtolower($word); }
        if ($charExtraItem=='Bosworth')    { $word = strtr($word, array('æ'=>'ae','Æ'=>'ae','ǽ'=>'ae','þ'=>'th','ð'=>'th','ȝ'=>'g'));
                                             $word = preg_replace('/^th(.*)/',"þ$1",$word,1); }
        if ($charExtraItem=='PokornyFill') { $word = 'P'.sprintf('%04d',$word); }
        if ($charExtraItem=='Maori')       { $word = strtr($word, array('ng'=>'ny','wh'=>'wy')); }  //Expedient to deal with unusual alphabetisation of ng and wh
        if ($charExtraItem=='lemmatize')   { $word = lemmatize($word,$sl); }
        if ($charExtraItem=='lod')         { $word = strtoupper($word).'1'; } //Ad-hoc for this one dictionary, which only partially works anyway
        if ($charExtraItem=='strixpMiddot') { $word = strtr($word, array('·'=>'')); }
        if ( preg_match('%^tr:(.*):(.*)$%u', $charExtraItem, $matches) ) { $word = strtr($word, array($matches[1]=>$matches[2])); }
    }
    if ($sl=='ar') { $word = strtr($word, array(json_decode('"\u0640"')=>'')); }  //Remove tatweel characters from Arabic words

    if (preg_match('|^\(\((.*)\)\)$|',$word,$matches)) {  //$word is really a dictionary code enclosed in double brackets ((..)), not a word
        $code    = $matches[1];
        $url     = str_replace('{code}',$code,$urlc);
        $pparams = '';
    } else {  // $word is a normal word
        $url     = str_replace('{word}',$word,$url);
        $pparams = str_replace('{word}',$word,$pparams);
    }

//Stub!! Needs a proper mechanism using table dictHl
$hl = 'ENG';
    $url     = str_replace('{hl}',$hl,$url);
    $pparams = str_replace('{hl}',$hl,$pparams);

    if (!empty($paramEncoding)) {
        $url     = iconv('UTF-8',$paramEncoding,$url);
        $pparams = iconv('UTF-8',$paramEncoding,$pparams);
    }


    if ($handling=='form') {
        $html = createForm($url,$pparams);
        goto echohtml;
    }
    
    if ($handling=='click') {
        $html = createClick($url,$message,$sid);
        goto echohtml;
    }
    
    if ($handling=='popup') {
        $html = createPopup($url,$message,$sid);
        goto echohtml;
    }
    
    $servername = $_SERVER['SERVER_NAME'];
    $scheme = ( empty($_SERVER['HTTPS']) ? 'http' : 'https' );
    $server = "$scheme://$servername";
    if (strpos($url,'dictpage')>0) {
        $url = "$server$url&inc=$inc";
    } else {
        if ($scheme=='http'  && substr($url,0,8)=='https://') { $url = 'http'.substr($url,4); }  //Change $url to use http
        if ($scheme=='https' && substr($url,0,7)=='http://' && $handling=='redirect' )  { $handling = ''; }  //redirect is not possible
    }

    if ($handling=='redirect') { header("Location:$url"); }

    $req = new HTTP_Request2();
    if (!empty($pparams)) { $req->setMethod('POST'); }
                    else  { $req->setMethod('GET');  }
//    $req->setConfig('proxy_host','wwwcache.uhi.ac.uk');
//    $req->setConfig('proxy_port',8080);
// Following line can seemingly be deleted since no longer required -- CPD, 2019-11-24
//if ($dict=='IATE') { $req->setConfig('protocol_version','1.0'); } //Hack for IATE because ->getBody() suddenly started giving a gzinflate() error for IATE, failing to decompress the body returned by IATE under HTTP 1.1 -- 2014-08-01
    $req->setConfig('timeout',20);
    $req->setHeader('Referer',"$server/multidict/");

    if (!empty($pparams)) {
        $pparams_arr = explode('&',$pparams);
        foreach ($pparams_arr as $pparam) {
            list($key,$value) = explode('=',$pparam,2); 
            $req->addPostParameter($key, $value);
        }
    }
    $netUrl = new Net_URL2($url);
    for ($nR=0; $nR<6; $nR++) {   //up to 6 redirections
        $req->setURL($url);
        $httpResponse = $req->send();
        $httpStatus = $httpResponse->getStatus();
      if ($httpStatus<>301 and $httpStatus<>302) { break; }
        $url = $netUrl->resolve($httpResponse->getHeader('Location'))->getURL();
    }
    if ($httpStatus>=300) {
        $httpReason = $httpResponse->getReasonPhrase();
        throw new SM_MDexception("No response received from:<br>$url<br><br>"
                               . "<span style=\"background-color:yellow\">HTTP error $httpStatus - $httpReason</span>");
    }
    $html = $httpResponse->getBody();

    $html = rtrim($html);

    // Convert all relative links to absolute links
    function resolveLink ($link) {
        global $netUrl;
        if (substr($link,0,7)=='http://' || substr($link,0,8)=='https://') { return $link; }
         else { return $netUrl->resolve($link)->getURL(); }
    };
$encoding = $paramEncoding;
$html = iconv($encoding,'UTF-8',$html);
    $html = preg_replace_callback('|href="(.*?)"|usi',   function ($match) { $newLink = resolveLink($match[1]); return "href=\"$newLink\""; }, $html);
    $html = preg_replace_callback("|href='(.*?)'|usi",   function ($match) { $newLink = resolveLink($match[1]); return "href=\"$newLink\""; }, $html);
    $html = preg_replace_callback('|src="(.*?)"|usi',    function ($match) { $newLink = resolveLink($match[1]); return "src=\"$newLink\"";  }, $html);
    $html = preg_replace_callback("|action='(.*?)'|usi", function ($match) { $newLink = resolveLink($match[1]); return "action=\"$newLink\"";  }, $html);
    $html = preg_replace_callback('|action="(.*?)"|usi', function ($match) { $newLink = resolveLink($match[1]); return "action=\"$newLink\"";  }, $html);
    $html = preg_replace_callback("|src='(.*?)'|usi",    function ($match) { $newLink = resolveLink($match[1]); return "src=\"$newLink\"";  }, $html);
    $html = preg_replace_callback('|url\("(.*?)"\)|usi', function ($match) { $newLink = resolveLink($match[1]); return "url(\"$newLink\")"; }, $html);
    $html = preg_replace_callback("|url\('(.*?)'\)|usi", function ($match) { $newLink = resolveLink($match[1]); return "url(\"$newLink\")"; }, $html);
    $html = preg_replace_callback('|url\((.*?)\)|usi',   function ($match) { $newLink = resolveLink($match[1]); return "url($newLink)";     }, $html);
    $html = preg_replace_callback('|@import "(.*?)"|usi',function ($match) { $newLink = resolveLink($match[1]); return "@import \"$newLink\""; },$html);
    $html = preg_replace_callback("|@import '(.*?)'|usi",function ($match) { $newLink = resolveLink($match[1]); return "@import \"$newLink\""; },$html);
$html = iconv('UTF-8',$encoding,$html);


    $html = preg_replace('/(.*)<head>(.*)/i',"$1<head><base href=\"$url\">\n<meta name=\"robots\" content=\"noindex,nofollow\">$2",$html);
    if     ($handling=='zapOnload')   { $html = preg_replace('/(.*)onload(.*)/i',"$1onload0$2",$html); }
    elseif ($handling=='zapLocation') { $html = preg_replace('/(.*)location(.*)/i',"$1location0$2",$html); }
    elseif ($handling=='zapIfParent') { $html = preg_replace('/(.*)if \(parent(.*)/i',"$1\\if (parent$2",$html); }
    elseif (substr($handling,0,6)=='onload') {
            $handling    = str_replace('{word}',$word,$handling);
            $html = preg_replace('/(.*)<body(.*)/i',"$1<body $handling$2",$html);
    }
if ($dict=='Gyldendal') { $html = preg_replace('/(.*)value="Søgning(.*)/i',"$1value0=\"Søgning$2",$html); }

    /* Try to work out the character encoding of the document and signal this in the HTTP header */
/*
Old code. Hopefully no longer needed, since $encoding now comes from the dictionaries database
Something still needing done: Replace $paramEncoding throughout this program with simply $encoding
    $encoding = '';
    $content_type = strtoupper($httpResponse->getHeader('content-type'));
    if (preg_match('|CHARSET\s*=\s*(\S*)|',$content_type,$matches)) { $encoding = $matches[1]; }
    if ($encoding=='' and preg_match('|Content-type.*?charset\w*=\s*(\S*?)\s*"|i',$html,$matches)==1) { $encoding = $matches[1]; }
    if ($encoding=='') { $encoding = mb_detect_encoding($html); }
    $encoding = strtoupper($encoding);
    if (strpos($encoding,'UTF-8')!==FALSE) { $encoding = 'UTF-8'; }
    if ($encoding=='') { $encoding = 'ISO-8859-1'; }  //Original WWW encoding (This line shouldn't be needed after mb_detect_encoding)
*/

    if ($encoding<>'UTF-8') { header("Content-Type: text/html; charset=$encoding"); }

echohtml:
    echo $html;

  } catch (Exception $e) {
      $message = $e->getMessage();
      if ($message=='{blankpage}') {
          $pageHtml = '<body>';
      } elseif (substr($message,0,11)=='No response' && ($dict=='glosbe' || $dict='OALD')) {
          $suggestionsHtml = '';
          $wfsArr = explode('|',$wlSession->wfs);
          if (count($wfsArr)>1) {
              $plural = ( count($wfsArr)>2 ? 's' : '' );
              $suggestionsHtml = "<p style='color:brown'>But you can reclick to try the alternative$plural above suggested by Multidict</p>"; 
          }
          $pageHtml = <<<END_NORESPONSE
<body style="background-color:#ffe;font-size:120%">
<p style="font-size:150%">The word &ldquo;<b>$word</b>&rdquo; was not found in this dictionary <img src="/icons-smo/bronach.gif"/></p>
$suggestionsHtml
END_NORESPONSE;
      } else {
          $pageHtml = <<<END_MESSAGE
<body style="background-color:#fdd;color:red;font-size:140%">
<h1>Fatal error <img src="/icons-smo/bronach.gif"/></h1>
<p>$message</p>
END_MESSAGE;
    }
    echo <<<END_ERRORPAGE
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Multidict: Exception handling</title>
</head>
<body>
$pageHtml
</body>
</html>
END_ERRORPAGE;
  }

?>
