<?php
  if (!include('autoload.inc.php'))
    header("Location:https://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header('P3P: CP="CAO PSA OUR"');

  $T = new SM_T('wordlink/wordlink');
  $T_Help    = $T->h('Cobhair');
  $T_Process = $T->h('Process');
  $T_Wordlink_start_text1 = $T->h('Wordlink_start_text1');
  $T_Wordlink_start_text2 = $T->h('Wordlink_start_text2');
  $T_Wordlink_start_text3 = $T->h('Wordlink_start_text3');

  $T_Wordlink_start_text2 = strtr( $T_Wordlink_start_text2, [ '{{Process}}' => '‘' . $T_Process . '’' ] );
  $T_Wordlink_start_text3 = strtr( $T_Wordlink_start_text3, [ '{{Help}}'    => '‘' . $T_Help    . '’' ] );

  $document_root = $_SERVER['DOCUMENT_ROOT'];
  $servername = $_SERVER['SERVER_NAME'];
  $scheme = ( empty($_SERVER['HTTPS']) ? 'http' : 'https' );
  $server = "$scheme://$servername";
  $php_self = $_SERVER['PHP_SELF'];
  $wlhome  = "$server/" . substr($php_self,0,strrpos($php_self,'/'));  //This is normally just  http://multidict.net/wordlink  but the expression allows the Wordlink directory to be easily changed

  function addLink($word,$title='') { global $mode, $multidictLink, $multidictTarget;
   //Add a multidict link to an individual word
    $href = "$multidictLink$word";
    if ($mode=='pu') { $href    = "javascript:popup('$href')"; }
    $link = "<a href=\"$href\" class=\"wll\" $multidictTarget title=\"$title\" onclick=\"markCur(this);\">$word</a>";
    return $link;
  }

  function addLinks($text) { global $sl, $wordpreg;
   //Add multidict links to a string of text
    $result = '';
    $text = strtr($text,array('&lt;'=>'«⁰','&gt;'=>'⁰»')); //Preserve any &lt; and &gt; with something unlikely
    $text = html_entity_decode($text,ENT_QUOTES,'UTF-8');
    $text = strtr($text, array("\xC2\xAD" => ""));  //Remove soft hypens (a more sophisticated treatment would retain them in the text but omit them from wordlinks). Could maybe do this too for zero-width-space??
    $pattern = '%^(\p{^L}*)('.$wordpreg.')(.*)$%us';
    while (preg_match($pattern,$text,$matches)) {
        $whitespace = $matches[1];
        $word       = $matches[2];
        $text       = $matches[4];
        $result .= $whitespace;
        if ($sl=='ja') { //Japanese has no spaces between words so needs to be broken up with the mecab tokenizer
            $command = "echo '{$word}' | mecab";
            $response = shell_exec($command);
            $lines = explode("\n", $response);
            foreach ($lines as $line) {
                if ($line=='EOS') continue;
                if (empty($line)) continue;
                $bits = explode("\t", $line);
                $jaWord = $bits[0];
                $mecabInfo = ( empty($bits[1]) ? '' : $bits[1] );
                $moreBits = explode(',', $mecabInfo);
                $jaPronounce =( empty($moreBits[7]) ? '' : $moreBits[7] );
                if (empty($jaPronounce)) { $jaPronounce = $jaWord; }
                //Convert the pronunciation from katakana to hiragana using a translation table
                $hiragana = 'ぁあぃいぅうぇえぉおかがきぎくぐけげこごさざしじすずせぜそぞただちぢっつづてでとどなにぬねのはばぱひびぴふぶぷへべぺほぼぽまみむめもゃやゅゆょよらりるれろゎわゐゑをんゔ';
                $katakana = 'ァアィイゥウェエォオカガキギクグケゲコゴサザシジスズセゼソゾタダチヂッツヅテデトドナニヌネノハバパヒビピフブプヘベペホボポマミムメモャヤュユョヨラリルレロヮワヰヱヲンヴ';
                preg_match_all('/./u', $katakana, $keys);
                preg_match_all('/./u', $hiragana, $values);
                $kanaMapping = array_combine($keys[0], $values[0]);
                $jaPronounce = strtr($jaPronounce,$kanaMapping);
                if ($jaPronounce==$jaWord) { $jaPronounce = ''; }
                $result .= addLink($jaWord,$jaPronounce);
            }
/* //This special processing for Chinese using Urheen works, but is far far too slow, so commented out
        } elseif ($sl=='zh' || substr($sl,0,3)=='zh-') { //Chinese has no spaces between words so needs to be broken up with the urheen tokenizer
            $word = iconv('UTF-8','GBK',$word); //Convert to GBK character encoding, which urheen works in, unfortunately
            $command = "cd /usr/local/bin; echo '{$word}' | urheen_m64 -i /dev/stdin -o /dev/stdout -t segpos 2>/dev/null";
            $response = shell_exec($command);
            $response = trim(explode("\n",$response)[1]); //The second line of the response is what we want
            $response = iconv('GBK','UTF-8',$response); //Convert back to UTF-8
            $wordPOSs = explode(' ', $response);
            foreach ($wordPOSs as $wordPOS) {
                $bits = explode('/',$wordPOS);
                $zhWord = $bits[0];
                $POS    = $bits[1]; //Part of speech
                $result .= addLink($zhWord,$POS);
            }
*/
        } else { $result .= addLink($word); } // The usual case, for languages with spaces between words
    }
    $result .= $text; //Add any remaining text (trailing non-word text)
    $result = strtr($result,array('«⁰'=>'&lt;','⁰»'=>'&gt;')); //Restore &lt; and &gt; back again
    return $result;
  }

  function addWordlink($tag,$context='') { global $sid, $sl, $wlhome;
    if (preg_match('/nowordlink/iu',$tag)) { return $tag; }  //No wordlinking if 'nowordlink' found in the tag
    if (preg_match('/\bno\s*wordlink\b/iu',$tag)) { return $tag; }  //No wordlinking if 'no wordlink' or similar found in the tag
    $attribute = ( $context=='frame' ? 'src' : 'href' ); //The attribute to be wordlinked
    $pattern =  '/((.*?) '.$attribute.'=")(.*?)("(.*))/ius';
    if (!preg_match($pattern,$tag,$matches)) {                      // If no luck...
        $pattern =  '/((.*?) '.$attribute.'=\')(.*?)(\'(.*))/ius';  // then try again with single quotes instead of double just in case html uses them 
        if (!preg_match($pattern,$tag,$matches)) { return $tag; }  //Faulty html so return $tag unchanged
    }
    $tagStart = $matches[1];
    $oldLink  = $matches[3];
   if (substr($oldLink,0,1)=='#') { return $tag; } //Don’t wordlink internal fragment links
    $media = array('jpg','jpeg','bmp','png','gif','tif','tiff', 'flv','asf','qt','mov','mpg','mpeg','avi','wmv','mp4','m4v','3gp', 'mp3','wav','wma');
    $pattern = '/\.' . implode('$|\.',$media) . '$/ius';
   if (preg_match($pattern,$oldLink)) { return $tag; } //Don't wordlink links to media such as .jpg, .gif, .mov
    $tagEnd   = $matches[4];
    $newLink = strtr ( $oldLink, array('&amp;'=>'&') );
    $newLink = strtr ( $newLink, array('&'=>'{and}') );  //Protect ampersands 
    if ($context=='frame') {
        $newWordlink = "$wlhome/wordlink.php?sid=$sid&amp;sl=$sl&amp;url=$newLink";
        $newTag = $tagStart.$newWordlink.$tagEnd;
    } else {
        $newWordlink = "$wlhome/?sid=$sid&amp;sl=$sl&amp;url=$newLink";
        if (!preg_match('/target=(.*)_top/ius',$tagEnd)
         && !preg_match('/target=(.*)_blank/ius',$tagEnd)) {       //If $tagEnd does not contain a pre-existing target=_top or target=_blank
            $tagEnd = str_ireplace('target=','target0=',$tagEnd);  //then first zap any pre-existing target attribute
            $tagEnd = substr($tagEnd,0,-1) . ' target="_top">';    //and then add a target=_top
        }
        $newTag = $tagStart.$newWordlink.$tagEnd;
    }
    return $newTag;
  }

  try {

    $sid = ( !empty($_GET['sid']) ? $_GET['sid'] : null);
    $wlSession = new SM_WlSession($sid);
    $wlSession->storeVars();
    $sl      = $wlSession->sl;
    $url     = $wlSession->url;
    $rmLi    = $wlSession->rmLi;
    $mode    = $wlSession->mode;
    $wordpreg= $wlSession->wordpreg;

// Removed this check meantime, since GoogleSafeBrowsing had stopped working after new version of GSB.
// It ought to be put working again soon --CPD 2020-09-28
//    $sbCheckResult = SM_WlSession::checkSafeBrowsing($url);
//    if (!empty($sbCheckResult)) { header("Location:$wlhome/safeBrowsingError.php?result=$sbCheckResult&url=$url"); }

    $ftErrMess = '';  //Check $url does not end in .pdf or .docx or suchlike
    if     ( preg_match('|\.pdf$|i', $url) ) { $ftErrMess = "This is a pdf<br><br>$url<br><br>"; }
    elseif ( preg_match('|\.docx$|i',$url) || preg_match('|\.doc$|i', $url) )
                                             { $ftErrMess = "This is a Word document<br><br>$url<br><br>"; }
    if ($ftErrMess) { throw new Exception($ftErrMess . 'Wordlink cannot process pdf’s or Word documents, only html webpages'); }

    //Calculate a couple of constants to save time later
    $multidictLink = "$server/multidict/?sid=$sid&amp;sl=$sl&amp;word=";
    if       ($mode=='pu') { $multidictTarget = ''; }
      elseif ($mode=='st') { $multidictTarget = 'target="_top"'; }
      else                 { $multidictTarget = "target=\"MD$sid\""; }

    header("Cache-Control:max-age=0");

    if ($sl=='null') { header("Location:$url"); }
    if ($sl=='unselected') { throw new SM_MDexception("You forgot to specify the webpage language"); }

    if (empty($url))  { throw new Exception('{blankpage}'); }

    if ($url=='{compose}') {
        if ($_GET['composed']<>1) {
            header("Location:$wlhome/compose.php?sid=$sid");
        }
        $text  = $_POST['text'];
        $text = str_replace('&'   ,'&amp;',$text);
        $text = str_replace('<'   ,'&lt;' ,$text);
        $text = str_replace('>'   ,'&gt;' ,$text);
        $text = str_replace("\r\n","\n"   ,$text);
        $text = str_replace("\r"  ,"\n"   ,$text);
        $text = str_replace(" \n" ,"\n"   ,$text);
        $text = str_replace("\n\n","</p>\n\n<p>",$text);
        $text = "<p>$text</p>";
        $html = <<<END_COMPOSE
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<p>$text</p>
</body>
</html>
END_COMPOSE;
    } else {
        //The next two statements are just to change the likes of http://domain.com to http://domain.com/ (with trailing slash) - otherwise resolveLink doesn’t work
        $ubits = parse_url($url);  //split the url into components, before recombing it inserting any missing ‘/’
        $url =  $ubits['scheme'] . '://' . $ubits['host']
              . ( empty($ubits['path'])     ? '/' : $ubits['path']         )
              . ( empty($ubits['query'])    ? ''  : '?'.$ubits['query']    )
              . ( empty($ubits['fragment']) ? ''  : '#'.$ubits['fragment'] );
        $netUrl = new Net_URL2($url);
//        if ($netUrl->getScheme()<>'http') { header("Location:$url"); }  //Redirect any non http pages and hope for the best!

        for ($nRhtml=0; $nRhtml<6; $nRhtml++) {   //up to 6 html redirections 
           $req = new HTTP_Request2();
//            $req->setConfig('proxy_host','wwwcache.uhi.ac.uk');
//            $req->setconfig('proxy_port',8080);
            if (strpos($url,'foramnagaidhlig')!==FALSE) { $req->setHeader('User-Agent', null); }
            for ($nRhttp=0; $nRhttp<6; $nRhttp++) {   //up to 6 redirections
                $req->setURL($url);
                $httpResponse = $req->send();
                $httpStatus = $httpResponse->getStatus();
                if ($httpStatus==204) { //Mur am faighear freagairt, feuch a-rithist le User-agent null
                    $req->setHeader('User-Agent', null);
                    $httpResponse = $req->send();
                    $httpStatus = $httpResponse->getStatus();
                }
              if ($httpStatus<>301 and $httpStatus<>302) { break; }
                $url = $netUrl->resolve($httpResponse->getHeader('Location'))->getURL();
            }
            if ($httpStatus>=300) { 
                $httpReason = $httpResponse->getReasonPhrase();
                throw new Exception("HTTP error $httpStatus - $httpReason");
            }
            $html = $httpResponse->getBody();
          if (!preg_match('/<META\s*HTTP-EQUIV="REFRESH"(.*)URL=\s*(\S*)\s*"/iu',$html,$matches)) { break; }
            $url = $matches[2];
            $netUrl = new Net_URL2($url);
        }

        // Remove BOM and NULLs - Copied from someone else's program - Seems like a good idea
         $html = preg_replace('/^\xef\xbb\xbf/', '', $html);
         $html = str_replace("\x0", '', $html);

        // Try to work out the character encoding of the document and convert to UTF-8 if necessary
        $encoding = '';
        $content_type = strtoupper($httpResponse->getHeader('content-type'));
        if (preg_match('|CHARSET\s*=\s*(\S*)|i',$content_type,$matches)) { $encoding = $matches[1]; }
        if ($encoding=='' and preg_match('|Content-type.*?charset\w*=\s*(\S*?)\s*"|i',$html,$matches)==1) { $encoding = $matches[1]; }
        if ($encoding=='') { $encoding = mb_detect_encoding($html); }
        $encoding = strtoupper($encoding);
        if (strpos($encoding,'UTF-8')!==FALSE) { $encoding = 'UTF-8'; }
        if ($encoding=='') { $encoding = 'ISO-8859-1'; }  //Original WWW encoding (This line shouldn't be needed after mb_detect_encoding)
        if ($encoding=='ISO-8859-1') { $encoding = 'windows-1252'; } //Might as well be liberal for the sake of pages which wrongly specify ISO-8859-1
        if ($encoding<>'UTF-8') { $html = iconv ($encoding,'UTF-8',$html); }

       //Clean out any dross from $html as it can cause preg_match, etc to fail
       //The following code is copied from http://magp.ie/2011/01/06/remove-non-utf8-characters-from-string-with-php/
        //reject overly long 2 byte sequences, as well as characters above U+10000
         $html = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]|[\x00-\x7F][\x80-\xBF]+|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*'.
          '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S', '', $html );
        //reject overly long 3 byte sequences and UTF-16 surrogates
         $html = preg_replace('/\xE0[\x80-\x9F][\x80-\xBF]|\xED[\xA0-\xBF][\x80-\xBF]/S','', $html );

        //Add base reference to html head
        $html = preg_replace('|<head(.*?)>|i',"<head$1><base href=\"$url\"/>\n<meta name=\"robots\" content=\"noindex,nofollow\">",$html);

    }

    $html = rtrim($html);  //Without rtrim the program crashed on pages with trailing whitespace

    // Convert all relative links to absolute links
    function resolveLink ($link) {
        global $netUrl;
        if (substr($link,0,7)=='http://' || substr($link,0,8)=='https://') { return $link; }
         else { return $netUrl->resolve($link)->getURL(); }
    };
    if ($url<>'{compose}') {
        $html = preg_replace_callback('|href="(.*?)"|usi',   function ($match) { $newLink = resolveLink($match[1]); return "href=\"$newLink\""; }, $html);
        $html = preg_replace_callback("|href='(.*?)'|usi",   function ($match) { $newLink = resolveLink($match[1]); return "href=\"$newLink\""; }, $html);
        $html = preg_replace_callback('|src="(.*?)"|usi',    function ($match) { $newLink = resolveLink($match[1]); return "src=\"$newLink\"";  }, $html);
        $html = preg_replace_callback("|src='(.*?)'|usi",    function ($match) { $newLink = resolveLink($match[1]); return "src=\"$newLink\"";  }, $html);
        $html = preg_replace_callback("|action='(.*?)'|usi", function ($match) { $newLink = resolveLink($match[1]); return "action=\"$newLink\"";  }, $html);
        $html = preg_replace_callback('|action="(.*?)"|usi', function ($match) { $newLink = resolveLink($match[1]); return "action=\"$newLink\"";  }, $html);
        $html = preg_replace_callback('|url\("(.*?)"\)|usi', function ($match) { $newLink = resolveLink($match[1]); return "url(\"$newLink\")"; }, $html);
        $html = preg_replace_callback("|url\('(.*?)'\)|usi", function ($match) { $newLink = resolveLink($match[1]); return "url(\"$newLink\")"; }, $html);
        $html = preg_replace_callback('|url\((.*?)\)|usi',   function ($match) { $newLink = resolveLink($match[1]); return "url($newLink)";     }, $html);
        $html = preg_replace_callback('|@import "(.*?)"|usi',function ($match) { $newLink = resolveLink($match[1]); return "@import \"$newLink\""; },$html);
        $html = preg_replace_callback("|@import '(.*?)'|usi",function ($match) { $newLink = resolveLink($match[1]); return "@import \"$newLink\""; },$html);
    }

    if (preg_match('|^(.*?)<body(.*?>)(.*)$|usi',$html,$matches)) {
        $bodytag   = "<body id=\"wll\"" . $matches[2];
    } elseif (preg_match('|^(.*?)<frameset(.*?>)(.*)$|usi',$html,$matches)) {
        $bodytag   = "<frameset" . $matches[2];
    } else {
        throw new Exception('Page appears to have no &ldquo;&lt;body&gt;&rdquo; tag');
    }
    $head      = $matches[1];
    $remainder = $matches[3];  // Actually includes closing </body> and </html> as well as body itself

    if (empty($sl)) {
        $errormessage = "You forgot to specify the webpage language";
        $tld = new Text_LanguageDetect();
        $plaintext = preg_replace('|<.*?>|us',' ',$remainder);
        $language = $tld->detectSimple($plaintext);
        $knownLanguages = array ( //The following are known to Text_LanguageDetect, but code is null if not currently known to Multidict
'Albanian'=>'sq', 'Arabic'=>'ar', 'Azeri'=>'', 'Bengali'=>'', 'Bulgarian'=>'bg', 'Cebuano'=>'', 'Croatian'=>'hr', 'Czech'=>'cs', 'Danish'=>'da', 'Dutch'=>'nl', 'English'=>'en', 'Estonian'=>'et', 'Farsi'=>'', 'Finnish'=>'fi', 'French'=>'fr', 'German'=>'de', 'Hausa'=>'', 'Hawaiian'=>'', 'Hindi'=>'hi', 'Hungarian'=>'hu', 'Icelandic'=>'is', 'Indonesian'=>'id', 'Italian'=>'it', 'Kazakh'=>'', 'Kyrgyz'=>'', 'Latin'=>'la', 'Latvian'=>'lv', 'Lithuanian'=>'lt', 'Macedonian'=>'', 'Mongolian'=>'', 'Nepali'=>'', 'Norwegian'=>'no', 'Pashto'=>'', 'Pidgin'=>'', 'Polish'=>'pl', 'Portuguese'=>'pt', 'Romanian'=>'ro', 'Russian'=>'ru', 'Serbian'=>'sr', 'Slovak'=>'sk', 'Slovene'=>'sl', 'Somali'=>'', 'Spanish'=>'es', 'Swahili'=>'', 'Swedish'=>'sv', 'Tagalog'=>'tl', 'Turkish'=>'tk', 'Ukrainian'=>'uk', 'Urdu'=>'', 'Uzbek'=>'', 'Vietnamese'=>'vi', 'Welsh'=>'cy');
        $sl = $knownLanguages[$language];
        if (!empty($sl)) { $errormessage .= "<br/>Select this, or confirm that it is "
                                           ."<a href=\"$wlhome/?sid=$sid&amp;sl=$sl\" target=\"_top\">$language</a>"; }
        throw new Exception($errormessage);
    }

    $javascript = <<<END_JS
    <script>
      function markCur(elem) {
            aTags = document.getElementsByTagName('a');
            for (var i=0; i<aTags.length; i++) {
                aTag = aTags[i]; 
                if (aTag.className=='wllCur') { aTag.className = 'wllCurSean'; }
            }
            elem.className = 'wllCur';
      }
      function popup(url) {
          dictwin = window.open(url, "dictwin", "resizable=1,scrollbars=1,top=50,left=25,height=500,width=700");
          dictwin.focus();
//          dictwin.outerHeight = screen.availHeight - 250;
//          dictwin.outerWidth  = screen.availWidth  - 100;
      }
    </script>
END_JS;

    $linkstyle =  <<<END_LS
    <style>
        body#wll a.wll        { background-color:inherit; color:inherit; text-decoration:inherit; }
        body#wll a.wll:hover  { background-color:#002;    color:#fee;    text-decoration:none;    }
        body#wll a.wllCur     { background-color:#533;    color:#dff;    text-decoration:none;    }
        body#wll a.wllCurSean { background-color:rgba(85,51,51,0.6);    color:#eff;    text-decoration:none;    }
        a.opaque { opacity:0; -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)"; filter: alpha(opacity=0); }
        wordlink[noshow] { display:none; }
    </style>
END_LS;
    $head = preg_replace('|</head>|i',"$linkstyle$javascript\n</head>",$head);
    $head = preg_replace('|(<meta.*".*charset\s*=\s*).*(".*>)|i',"$1UTF-8$2",$head);
    echo $head;
    echo $bodytag;

//  First mop up anything before the first tag
    preg_match('|^(.*?)(<.*>)$|us',$remainder,$matches);
    echo addLinks($matches[1]);
    $remainder  = $matches[2];

//  Then process tag,text repeatedly til the end of the source
    $linkTag = 0;
    while (preg_match('|^(.*?>)(.*?)(<.*)$|us',$remainder,$matches)) {
        $tag       = $matches[1];
        $text      = $matches[2];
        $remainder = $matches[3];
        preg_match('|^<\s*([\/\w]*)|ius',$tag,$matches);
        $tagType = strtolower($matches[1]);
        if ( ($tagType=='a') && (   preg_match('|\"/wordlink.*url|'       ,$tag)
                                 || preg_match('|clilstore.eu/wordlink|'  ,$tag)
                                 || preg_match('|multidict.info/wordlink|',$tag)
                                 || preg_match('|multidict.net/wordlink|' ,$tag) ) ) {  //If this happens to be a link to Wordlink itself then
            $tag = '<a class="opaque">';                                                //zap it and make it invisible to avoid to avoid recursion
        } elseif ($tagType=='input') {
            $tag = str_ireplace('<input','<input readonly placeholder="Disabled in Wordlink"',$tag);
        } elseif ($rmLi) {
            if ($tagType=='a' or $tagType=='/a') { $tag = ''; }
            $text = addLinks($text);
        } else {
            if ($tagType=='a')  { $linkTag = 1; }
            if ($tagType=='/a') { $linkTag = 0; }
            if (!$linkTag) { $text = addLinks($text); }
            if ($tagType=='a')     { $tag  = addWordlink($tag); }
            if ($tagType=='frame') { $tag  = addWordlink($tag,'frame'); }
        }
        echo $tag,$text;
    }

  } catch (Exception $e) {
      $message = $e->getMessage();

      if ($message=='{blankpage}') { echo <<<END_BLANKPAGE
<!DOCTYPE html>
<html>
<body>
<div style="margin:4em 0.5em 0 0.5em;border: 1px solid green;border-radius:0.5em;padding:0 0.5em;
            background-color:#efe;color:green">
<p>$T_Wordlink_start_text1</p>
<p>$T_Wordlink_start_text2</p>
<p>$T_Wordlink_start_text3</p>
</div>
</body>
</html>
END_BLANKPAGE;
    } else { echo <<<END_MESSAGE
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Wordlink: Fatal error</title>
</head>
<body style="background-color:#fbb;color:red;font-size:140%">
<h1>Fatal error <img src="$server/icons-smo/bronach.gif"/></h1>
<p>$message</p>
</body>
</html>
END_MESSAGE;
    }

  }

?>
