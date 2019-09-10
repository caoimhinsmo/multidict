<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  try {
      $myCLIL = SM_myCLIL::singleton();
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }

  try {
    if (empty($_GET['user'])) { throw new SM_MDexception('No user parameter'); }
    $user = $_GET['user'];
    $giveFullInfo = in_array($myCLIL->id, array(/* $user,*/'admin','caoimhinsmo','Kent','fred','Claisneachd'));
    $timeFormat = ( $giveFullInfo ? 'Y-m-d H:i:s' : 'Y-m-d' );

    function dateTime($dateTimeObj,$giveFullInfo) {
        $dateTime = $dateTimeObj->format('Y-m-d');
        if (!$giveFullInfo) return $dateTime;
        $dateTime .= '<span class="time">' . $dateTimeObj->format(' H:i:s') .'</span>';
        return $dateTime;
    }

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $csSess   = SM_csSess::singleton();
    $mode    = $csSess->getCsSession()->mode;

    $stmt = $DbMultidict->prepare('SELECT user,fullname,email,joined,password FROM users WHERE user=:user');
    $stmt->execute(array(':user'=>$user));
    if (!($r = $stmt->fetch(PDO::FETCH_OBJ))) { throw new SM_MDexception("No such user as &ldquo;$userSC&rdquo;"); }
    $stmt = null;
    $user     = $r->user; //Replace the GET parameter copy, as this may possibly not have normalized diacritics and capitals
    $fullname = $r->fullname;
    $email    = $r->email;
    $joined   = $r->joined;
    $password = $r->password;

    $userHtml     = htmlspecialchars($user);
    $fullnameHtml = htmlspecialchars($fullname);
    $emailHtml    = htmlspecialchars($email);
    $joinedObj = new DateTime("@$joined");
    $joinedDateTime = dateTime($joinedObj,$giveFullInfo);

    $stmt = $DbMultidict->prepare('SELECT user,email FROM users WHERE fullname=:fullname AND user<>:user ORDER BY user');
    $stmt->execute(array(':fullname'=>$fullname,':user'=>$user));
    $rArr = $stmt->fetchAll(PDO::FETCH_OBJ);
    $stmt = null;
    if (!empty($rArr)) {
        $usersArr = array();
        foreach ($rArr as $r) {
            $u  = $r->user;
            $em = $r->email;
            $u = htmlspecialchars($u);
            $usersArr[] = "<a href=\"./userinfo.php?user=$u\" title=\"$em\">$u</a>";
        }
        $usersList = implode(', ',$usersArr);
        if (!empty($usersList)) { $fullnameHtml .= "<br><span style=\"font-size:85%;color:grey\">Other users with this name: $usersList</span>"; }
    }

    $stmt = $DbMultidict->prepare('SELECT user,fullname,email FROM users WHERE password=:password AND user<>:user ORDER BY user');
    $stmt->execute(array(':password'=>$password,':user'=>$user));
    $rArr = $stmt->fetchAll(PDO::FETCH_OBJ);
    $stmt = null;
    if (/* $myCLIL->id=='admin' || */ $myCLIL->id=='caoimhinsmo') {
        $usersArr = array();
        foreach ($rArr as $r) {
            $u  = $r->user;
            $fn = $r->fullname;
            $em = $r->email;
            $u = htmlspecialchars($u);
            $usersArr[] = "<a href=\"./userinfo.php?user=$u\" title=\"$fn - $em\">$u</a>";
        }
        $usersList = implode(', ',$usersArr);
        if (!empty($usersList)) { $fullnameHtml .= "<br><span style=\"font-size:85%;color:grey\">Other users with the same password: $usersList</span>"; }
    }

    $stmt = $DbMultidict->prepare("SELECT COUNT(1) AS nLogins, MIN(utime) AS firstLogin, max(utime) AS lastLogin FROM log WHERE user=:user AND type='login'");
    $stmt->execute(array(':user'=>$user));
    $r = $stmt->fetch(PDO::FETCH_OBJ);
    $stmt = null;
    $nLogins    = $r->nLogins;
    if ($nLogins==0) { $loginInfo = 'Never logged in'; } else {
        $firstLogin = $r->firstLogin;
        $lastLogin  = $r->lastLogin;
        $firstLoginObj = new DateTime("@$firstLogin");
        $lastLoginObj  = new DateTime("@$lastLogin");
        $firstLoginDateTime = dateTime($firstLoginObj,TRUE);
        $lastLoginDateTime  = dateTime($lastLoginObj ,TRUE);
        $loginInfo = "$nLogins logins between $firstLoginDateTime and $lastLoginDateTime";
    }

    $query = 'SELECT COUNT(1) AS cnt, MIN(created) AS minCreated, MAX(created) AS maxCreated, MAX(changed) AS maxChanged, SUM(views) AS totViews, SUM(clicks) AS totClicks'
            .' FROM clilstore WHERE owner=:user';
    $stmt = $DbMultidict->prepare($query.' AND test=0');
    $stmt->execute(array(':user'=>$user));
    $r = $stmt->fetch(PDO::FETCH_OBJ);
    $stmt = null;
    $nUnits = $r->cnt;
    $unitsInfo = "<b>$nUnits units</b>";
    if ($nUnits>0) {
        if ($mode>0) { $unitsInfo = "<a href=\"/clilstore?owner=$user\">$unitsInfo</a>"; }
        $minCreated = $r->minCreated;
        $maxCreated = $r->maxCreated;
        $maxChanged = $r->maxChanged;
        $minCreatedObj = new DateTime("@$minCreated");
        $maxCreatedObj = new DateTime("@$maxCreated");
        $maxChangedObj = new DateTime("@$maxChanged");
        $minCreatedDateTime = dateTime($minCreatedObj,$giveFullInfo);
        $maxCreatedDateTime = dateTime($maxCreatedObj,$giveFullInfo);
        $maxChangedDateTime = dateTime($maxChangedObj,$giveFullInfo);
        $unitsInfo .= "<br>Created between $minCreatedDateTime and $maxCreatedDateTime, last changed on $maxChangedDateTime";
        $totViews  = $r->totViews;
        $totClicks = $r->totClicks;
        $unitsInfo .= "<br>$totViews views; $totClicks clicks<span style=\"font-size:65%;color:grey\"> since 2013-04-18</span>";
    }
    $stmt = $DbMultidict->prepare($query. ' AND test<2');
    $stmt->execute(array(':user'=>$user));
    $r = $stmt->fetch(PDO::FETCH_OBJ);
    $stmt = null;
    $nUnitsAll = $r->cnt;
    $unitsInfoAll = "$nUnitsAll units";
    if ($nUnitsAll==$nUnits) {
        $unitsInfoAll = '<i>(No test units)</i>';
    } elseif ($nUnitsAll>0) {
        $minCreated = $r->minCreated;
        $maxCreated = $r->maxCreated;
        $maxChanged = $r->maxChanged;
        $minCreatedObj = new DateTime("@$minCreated");
        $maxCreatedObj = new DateTime("@$maxCreated");
        $maxChangedObj = new DateTime("@$maxChanged");
        $minCreatedDateTime = dateTime($minCreatedObj,$giveFullInfo);
        $maxCreatedDateTime = dateTime($maxCreatedObj,$giveFullInfo);
        $maxChangedDateTime = dateTime($maxChangedObj,$giveFullInfo);
        $unitsInfoAll = "<a href=\"/clilstore?owner=$user\">$unitsInfoAll</a><br>Created between $minCreatedDateTime and $maxCreatedDateTime. Last change on $maxChangedDateTime";
        $totViews  = $r->totViews;
        $totClicks = $r->totClicks;
        $unitsInfoAll .= "<br>$totViews views; $totClicks clicks<span style=\"font-size:65%;color:grey\">since 2013-04-18</span>";
    }

    $stmt = $DbMultidict->prepare('SELECT sl, COUNT(1) as cnt, endonym FROM clilstore,lang WHERE owner=:user AND test=0 AND sl=lang.id GROUP BY sl ORDER BY cnt DESC, sl');
    $stmt->execute(array(':user'=>$user));
    $rAll = $stmt->fetchAll(PDO::FETCH_OBJ);
    $stmt = null;
    if (!empty($rAll)) {
        foreach ($rAll as $r) {
            $sl      = $r->sl;
            $cnt     = $r->cnt;
            $endonym = $r->endonym;
            $langHtmlArr[] = "<a href=\"/clilstore/?owner=$user&amp;sl=$sl\" title=\"$endonym&lrm;: $cnt units\">$sl</a>";
        }
        $unitsInfo .= '<br>Languages: ' . implode(', ', $langHtmlArr);
    }

    if (!$giveFullInfo) { $displayInfo = <<<EODbriefInfo
<table id="priomh">
<tr><td>Full name:</td><td>$fullnameHtml</td></tr>
<tr><td>Published units:</td><td>$unitsInfo</td></tr>
</table>
EODbriefInfo;
    } else { $displayInfo = <<<EODfullInfo
<table id="priomh">
<tr><td>Full name:</td><td>$fullnameHtml</td></tr>
<tr><td>Email:</td><td><a href="mailto:$email">$email</a></td></tr>
<tr><td>Registered:</td><td>$joinedDateTime</td></tr>
<tr><td>Logins:</td><td>$loginInfo</td></tr>
<tr><td>Published units:</td><td>$unitsInfo</td></tr>
<tr><td>All units inc. test:</td><td>$unitsInfoAll</td></tr>
</table>
EODfullInfo;
    }

    echo <<<EOD
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Information on Clilstore user $userHtml</title>
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <link rel="StyleSheet" href="/css/smo.css" type="text/css">
    <link rel="StyleSheet" href="style.css?version=2014-04-15" type="text/css">
    <style type="text/css">
        div.body-indent { clear:both; margin:0 0.25%; padding:0 0.25% 6px 0.25%; border-top:1px solid white; }
        table#priomh    { border-collapse:collapse; }
        table#priomh td { padding:7px 4px; vertical-align:top; }
        table#priomh td:first-child { white-space:nowrap; }
        table#priomh td:first-child { text-align:right; font-weight:bold; }
        span.time { font-size:60%; color:grey;  }
    </style>
</head>
<body>
<ul class="linkbuts">
<li><a href="./" title="Clilstore index page">Clilstore</a>
</ul>
<div class="body-indent">

<h1 style="font-size:130%">Information on Clilstore user <span style="font-size:120%;text-decoration:underline">$userHtml</span></h1>

$displayInfo

</div>
<ul class="linkbuts">
<li><a href="./" title="Clilstore index page">Clilstore</a>
</ul>
</body>
</html>
EOD;

  } catch (Exception $e) { echo $e; }

?>
