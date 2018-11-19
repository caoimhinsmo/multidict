<?php
// Tiondachaidh seo GET query gu POST query
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {
    if (!isset($_GET['url'])) { throw new SM_MDexception("sgrios|Tha an duilleag seo feumach air parameter url="); }
    $url = $_GET['url'];

    $req = new HTTP_Request2();
    $req->setMethod('POST');
    $req->setConfig('proxy_host','wwwcache.uhi.ac.uk');
    $req->setConfig('proxy_port',8080);
    foreach ($_GET as $key=>$value) { 
        if ($key<>'url') { $req->addPostParameter($key, $value); }
    }
//if ($url=='http://www.lexicelt.org/geiriadur/geiriadur.aspx') { $req->addPostParameter('__VIEWSTATE','/wEPDwULLTEzMTE2MjMwODYPZBYCAgEPZBYKAgMPDxYCHghJbWFnZVVybAUraW1hZ2VzL2N5bmxsdW5fbGx5ZnJ5bjNfcjVfYzNfZ3d5ZGRlbGVnLmdpZmRkAgUPDxYCHwAFK2ltYWdlcy9jeW5sbHVuX2xseWZyeW4zX3I1X2M1X2d3eWRkZWxlZy5naWZkZAIHDw8WAh8ABStpbWFnZXMvY3lubGx1bl9sbHlmcnluM19yNV9jOF9nd3lkZGVsZWcuZ2lmZGQCCQ8PFgIfAAUsaW1hZ2VzL2N5bmxsdW5fbGx5ZnJ5bjNfcjVfYzEwX2d3eWRkZWxlZy5naWZkZAILD2QWCgIBDxAPFgIeBFRleHQFE0JyZWF0bmFpcyA+IEdhZWlsZ2VkZGRkAgMPEA8WAh8BBRNHYWVpbGdlID4gQnJlYXRuYWlzZGRkZAIFDw8WAh8BBQpDdWFyZGFpZ2g6ZGQCBw8PZBYCHglvbmtleWRvd24FK2ZuVHJhcEtEKFNlYXJjaEJveENvbnRyb2xfc2VhcmNoX2J0bixldmVudClkAgkPDxYCHwEFCUN1YXJkYWlnaGRkGAEFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYJBQ9JQnRuX2xhbmdTd2l0Y2gFDUlidG5feW1hZHJvZGQFDklCdG5fZ2VpcmlhZHVyBQ1JQnRuX2dyYW1hZGVnBQ9JQnRuX2d3eWJvZGFldGgFHFNlYXJjaEJveENvbnRyb2w6Y3kyZ2FfcmFkaW8FHFNlYXJjaEJveENvbnRyb2w6Z2EyY3lfcmFkaW8FHFNlYXJjaEJveENvbnRyb2w6Z2EyY3lfcmFkaW8FG1NlYXJjaEJveENvbnRyb2w6YnRuVW5pY29kZSxERmCBwM9/rmQin0cOQclgdS09'); }
    for ($nR=0; $nR<6; $nR++) {   //up to 6 redirections
        $req->setURL($url);
        $httpResponse = $req->send();
        $httpStatus = $httpResponse->getStatus();
      if ($httpStatus<>301 and $httpStatus<>302) { break; }
        $netUrl = new Net_URL2($url);
        $url = $netUrl->resolve($httpResponse->getHeader('Location'))->getURL();
    }
    if ($httpStatus>=300) { 
        $httpReason = $httpResponse->getReasonPhrase();
        throw new SM_MDexception("sgrios|Cha d'fhuaireadh freagairt bho $url<br/>"
                              ."<span style=\"background-color:yellow\">HTTP error $httpStatus - $httpReason</span>");
    }
    $html = $httpResponse->getBody();
    $html = preg_replace('/(.*)<head>(.*)/i',"$1<head><base href=\"$url\">$2",$html);
    $html = preg_replace('/(.*)action=""(.*)/i',"$1action=\"$url\"$2",$html);

    /* Try to work out the character encoding of the document and signal this in the HTTP header */
    $encoding = '';
    $content_type = strtolower($httpResponse->getHeader('content-type'));
    if (preg_match('|charset\s*=\s*(\S*)|',$content_type,$matches)) { $encoding = $matches[1]; }
    if ($encoding=='' and preg_match('|Content-type.*?charset\w*=\s*(\S*?)\s*"|i',$html,$matches)==1) { $encoding = $matches[1]; }
    if ($encoding=='') { $encoding = mb_detect_encoding($html); }
    $encoding = strtoupper($encoding);
    if (strpos($encoding,'UTF-8')!==FALSE) { $encoding = 'UTF-8'; }
    if ($encoding=='') { $encoding = 'ISO-8859-1'; }  //Original WWW encoding (This line shouldn't be needed after mb_detect_encoding)
    if ($encoding<>'UTF-8') { header("Content-Type: text/html; charset=$encoding"); }

    echo $html;
  } catch (Exception $e) { echo <<<EOD
<!DOCTYPE html>
<html>
<head>
   <title>Mearachd ann am postair.php</title>
</head>
<body>
$e
</body>
</html>
EOD;
  }

?>
