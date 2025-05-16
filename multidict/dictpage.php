<?php
// Takes parameters dictindex= and word= (and exceptionally inst=) and returns a page from a page-image dictionary.
// Can also take a parameter inc=, indicating to move forwards (or backwards if negative) inc pages in the index.
//
  if (!include('autoload.inc.php'))
    header("Location:https://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {
    $dictindex = ( !empty($_GET['dictindex']) ? $_GET['dictindex'] : $_GET['dict'] ); //For backwards campatibility with Dwelly accept dict=
    $word      = $_GET['word'];
    if (empty($dictindex)) { throw new SM_MDexception("Error: No 'dictindex=' parameter specified in URL"); }
    if (empty($word))      { throw new SM_MDexception("Error: No 'word=' parameter specified in URL"); }
    $inst = $_GET['inst'] ?? 1;
    $inc  = $_GET['inc']  ?? 0;
    $inc = intval($inc);
$logging = 0;
if ( $dictindex=='GMFFbr' or $dictindex=='Dwelly' ) { $logging = 1; }
if ($logging) { error_log("dictindex.php: \$word=$word"); }

    $DbMultidict = SM_DbMultidictPDO::singleton();

    $query0 = 'SELECT url,pparams,firstlast FROM dictPageURL'
             .' WHERE dictindex=:dictindex AND inst=:inst AND firstword<=:word'
             .' ORDER BY firstword DESC LIMIT 1';
    $stmt0 = $DbMultidict->prepare($query0);
    $stmt0->execute( array(':dictindex'=>$dictindex, ':inst'=>$inst, ':word'=>$word) );
    if ($r = $stmt0->fetch(PDO::FETCH_OBJ)) {
        $url       = $r->url;
        $pparams   = $r->pparams;
        $firstlast = $r->firstlast;
if ($logging) { error_log("dictindex.php: \$url=$url"); }
    } else { throw new SM_MDexception("No information found for dictionary index:'$dictindex'"); }
    $stmt0 = null;

    $stmt = $DbMultidict->prepare("SELECT page FROM dictPage WHERE dictindex=:dictindex AND word COLLATE utf8_bin = :word");  //First try to find an exact case insensitive match
    $stmt->execute( array(':dictindex'=>$dictindex, ':word'=>$word) );
    if (!($r = $stmt->fetch(PDO::FETCH_OBJ))) { //If the case insensitive match finds nothing, do the usual and find the nearest page
        if ($firstlast=='first') {
            $query = "SELECT page FROM dictPage WHERE dictindex=:dictindex AND word<=:word ORDER BY word DESC LIMIT 1";
        } elseif ($firstlast=='last') {
            $query = "SELECT page FROM dictPage WHERE dictindex=:dictindex AND word>=:word ORDER BY word LIMIT 1";
        } else throw new SM_MDexception("Invalid firstlast parameter &ldquo;$firstlast&rdquo; in dictionary index");
        $stmt = $DbMultidict->prepare($query);
        $stmt->execute( array(':dictindex'=>$dictindex, ':word'=>$word) );
        if (!($r = $stmt->fetch(PDO::FETCH_OBJ))) { throw new SM_MDexception("No index found for dictionary '$dictindex'"); }
    }
    $page = $r->page;

    if ($inc) {
        if ($inc>0) {
            $queryInc = "SELECT page FROM dictPage WHERE dictindex=:dictindex AND word>:word AND page<>:page ORDER BY word ASC LIMIT $inc";
        } else {
            $inc = -$inc;
            $queryInc = "SELECT page FROM dictPage WHERE dictindex=:dictindex AND word<:word AND page<>:page ORDER BY word DESC LIMIT $inc";
        }
        $stmtInc = $DbMultidict->prepare($queryInc);
        $stmtInc->execute( array(':dictindex'=>$dictindex, ':word'=>$word, ':page'=>$page) );
        while ($r = $stmtInc->fetch(PDO::FETCH_OBJ)) { $page = $r->page; }
    }

    $url = str_replace('{page}',$page,$url);

 // If no POST parameters (as is usually the case) simply redirect and we are done
    if (empty($pparams)) { header("Location:$url"); }

 // But if there are post parameters we are forced to do a lot more work
    $pparams = str_replace('{page}',$page,$pparams);
    $req = new HTTP_Request2();
    $req->setMethod('POST');
    $req->setConfig('proxy_host','wwwcache.uhi.ac.uk');
    $req->setConfig('proxy_port',8080);
    $req->setConfig('timeout',12);
    if (!empty($pparams)) {
        $pparams_arr = explode('&',$pparams);
        foreach ($pparams_arr as $pparam) {
            list($key,$value) = explode('=',$pparam); 
            $req->addPostParameter($key, $value);
        }
    }
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
        throw new SM_MDexception("sgrios|No response from $url<br/>"
                              ."<span style=\"background-color:yellow\">HTTP error $httpStatus - $httpReason</span>");
    }
    $html = $httpResponse->getBody();
    $html = preg_replace('/(.*)<head>(.*)/i',"$1<head><base href=\"$url\">$2",$html);
    $html = preg_replace('/(.*)action=""(.*)/i',"$1action=\"$url\"$2",$html);
    header("Cache-Control:max-age=0");
    header("Content-Type: text/html; charset=UTF-8");  // Assuming all pageimage dictionaries are modern and UTF-8
    echo $html;

  } catch (exception $e) { echo $e; }
?>
