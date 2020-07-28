<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  try {
      $myCLIL = SM_myCLIL::singleton();
      $user = ( isset($myCLIL->id) ? $myCLIL->id : '' );
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }

  try {
    if (!isset($_REQUEST['id'])) { throw new SM_MDexception('No id parameter'); }
    $id = $_REQUEST['id'];
    if (!is_numeric($id)) { throw new SM_MDexception('id parameter is not numeric'); }

    $abuseParams = $ownerHtml = $offerMess = $errorMessage = '';

    $servername = SM_myCLIL::servername();
    $serverhome = SM_myCLIL::serverhome();
    $DbMultidict = SM_DbMultidictPDO::singleton('rw');

    if (isset($_REQUEST['offerSubmit'])) {
        $offerSubmit = $_REQUEST['offerSubmit'];
        if ($offerSubmit=='Withdraw the offer') {
            $stmt = $DbMultidict->prepare("UPDATE clilstore SET offer=NULL,offerTime=0 WHERE id=:id AND (owner=:user OR '$user'='admin')");
            $stmt->execute(array('id'=>$id,':user'=>$user));
            SM_csSess::logWrite($user,'offerWithdraw',"User $user withdrew the offer on unit $id");
        } else if ($offerSubmit=='Offer') {
            $offer = (isset($_REQUEST['offer']) ? $_REQUEST['offer'] : '');
            if (!empty($offer)) {
                $stmt = $DbMultidict->prepare('SELECT user FROM users WHERE user=:user');
                $stmt->execute(array(':user'=>$offer));
                if (!$stmt->fetch()) {
                    $errorMessage = '<p style="color:red">No such user as ' . htmlspecialchars($offer) .'<p>';
                } elseif ($offer==$user) {
                    $errorMessage = '<p style="color:red">You can’t transfer ownership from yourself to yourself</p>';
                } else {
                    $offerTime = time();
                    $stmt = $DbMultidict->prepare("UPDATE clilstore SET offer=:offer,offerTime=:offerTime where id=:id AND (owner=:user OR '$user'='admin')");
                    $stmt->execute( array(':id'=>$id, ':user'=>$user, ':offer'=>$offer, ':offerTime'=>$offerTime) );
                    SM_csSess::logWrite($user,'offerMake',"User $user offered unit $id to user $offer",$offer);
                }
            }
        } else { throw new SM_MDexception('Invalid offerSubmit parameter '.htmlspecialchars($offerSubmit)); }
     }

    $query = 'SELECT sl,level,owner,medtype,medlen,title,summary,langnotes,words,created,changed,licence,views,clicks,likes,offer,offerTime,fullname'
            .' FROM clilstore, users WHERE id=:id AND clilstore.owner=users.user';
    $stmt = $DbMultidict->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    if (!($r = $stmt->fetch(PDO::FETCH_ASSOC))) { throw new SM_MDexception("No unit exists for id=$id"); }
    extract($r);
    $tl = ( $sl=='en' ? 'es' : 'en'); //airson Google Translate

    $summary   = htmlspecialchars($summary);
    $langnotes = htmlspecialchars($langnotes);
    $words = ( isset($words) ? $words : '');
    $clicksMessage = ( $created>1395188407 ? '' : ' <span style="color:grey;font-size:80%">(since 2014-03-18)</span>' ) ;

    if      ($level<0)  { $cefr = '';   }
     elseif ($level<10) { $cefr = 'A1'; }
     elseif ($level<20) { $cefr = 'A2'; }
     elseif ($level<30) { $cefr = 'B1'; }
     elseif ($level<40) { $cefr = 'B2'; }
     elseif ($level<50) { $cefr = 'C1'; }
     elseif ($level<60) { $cefr = 'C2'; }

    $createdObj = new DateTime("@$created");
    $createdDateTime = date_format($createdObj, 'Y-m-d H:i:s');
    $changedObj = new DateTime("@$changed");
    $changedDateTime = date_format($changedObj, 'Y-m-d H:i:s');

    if (empty($medlen)) { $medlenStr = '?:??'; }
     else { $medlenStr = SM_csSess::secs2minsecs($medlen) . ($medlen<60 ? 's' : ''); }
    if      ($medtype==0) { $mediaHtml = 'none'; }
     elseif ($medtype==1) { $mediaHtml = "<img src=\"audio.png\" alt=\"audio\"> ($medlenStr)"; }
     elseif ($medtype==2) { $mediaHtml = "<img src=\"video.png\" alt=\"video\"> ($medlenStr)"; }

    $linkbuttons = <<<EOBUT
<ul class="linkbuts">
<li><a href="./" class="nowordlink" target="_top" title="Clilstore index">Clilstore</a></li>
<li><a href="/cs/$id" title="Back to Unit $id">Unit $id</a></li>
</ul>
EOBUT;

    $stmtLikeUsers = $DbMultidict->prepare('SELECT user FROM user_unit WHERE unit=:id AND likes>0 ORDER BY user');
    $stmtLikeUsers->execute([':id'=>$id]);
    $likeUsersArr = $stmtLikeUsers->fetchAll(PDO::FETCH_COLUMN);
    $likeUsers = htmlspecialchars(implode(', ',$likeUsersArr));

    $likeUsersTitle = ( $likeUsers
                      ? "TITLE='Liked by: $likeUsers'"
                      : ''
                      );

    if (!empty($user)) {
        $abuseParams =  '&amp;name=' . $myCLIL->fullname()
                      . '&amp;from=' . $myCLIL->email();
    }

    if ($user==$owner || $user=='admin') { //If authorized, create html for offering the unit to someone else
        $stmt = $DbMultidict->prepare('SELECT user FROM users ORDER BY user');
        $stmt->execute();
        $userList = $stmt->fetchAll(PDO::FETCH_COLUMN,0);
        foreach ($userList as $key=>&$usr) {
            if ($usr==$owner || $usr==$offer) { unset($userList[$key]); }
             else { $usr = "<option value=\"$usr\">"; }
        }
        $userListHtml = implode("\n",$userList);
        $stmt = null;
        $offerMess = "<datalist id=\"userList\">\n$userListHtml\n</datalist>\n";
        if (empty($offer)) {
            $offerMess .= "Offer to transfer ownership of this unit to user: ";
        } else {
            if (!empty($offerTime)) {
                $offerTimeObj = new DateTime("@$offerTime");
                $offerDateTime = date_format($offerTimeObj, 'Y-m-d H:i:s');
                $offerMess .= "<form method=\"POST\" action=\"unitinfo.php?id=$id\">\n"
                            . "The unit is on offer from you to <b><a href=\"userinfo.php?user=$offer\">$offer</a></b> <span style=\"font-size:80%;color:grey\">(since $offerDateTime)</span>\n"
                            . '<input type="submit" name="offerSubmit" value="Withdraw the offer" style="margin-left:0.5em"><br>'
                            . "</form>"
                            . "<span class=\"info\">&nbsp;&nbsp;&nbsp;User $offer can accept or reject the offer via his/her Clilstore Options</span><br><br>\n";
            } else {
                $offerMess .= "The unit was on offer to <a href=\"userinfo.php?user=$offer\">$offer</a> but has been rejected.<br>\n";
            }
            $offerMess .= 'Offer it instead to user: ';
        }
        $offerMess .= "<form action=\"unitinfo.php?id=$id\" method=\"post\">\n"
                    . '<input name="offer" list="userList" placeholder="Clilstore user id"> <input type="submit" name="offerSubmit" value="Offer"></form>'."\n";
        $ownerHtml = <<<EODowner
<fieldset id="transfer"><legend>Ownership transfer</legend>$offerMess</fieldset>
EODowner;
    }


    echo <<<EOD1
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Details for Clilstore unit $id</title>
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="StyleSheet" href="style.css?version=2014-04-15">
    <style>
        div.body-indent { clear:both; margin:0 0.25%; padding:0 0.25% 6px 0.25%; border-top:1px solid white; }
        table#priomh    { border-collapse:collapse; }
        table#priomh td { padding:7px 4px; }
        table#priomh td:first-child { white-space:nowrap; }
        table#priomh td:first-child { text-align:right; font-weight:bold; }
        form { display:inline; }
        fieldset#transfer { margin-top:1.5em; border:2px solid #61abec; border-radius:6px; padding:6px; margin-bottom:2em; }
        fieldset#transfer legend { border:2px solid #61abec; border-radius:4px; padding:1px 4px; background-color:#eef; color:#55a8eb; font-weight:bold; }
        a#abuse { padding:2px; border:1px solid red; border-radius:4px; font-weight:bold; color:red; background-color:#fdd; }
        a#abuse:hover { background-color:#f99; }
    </style>

</head>
<body>
$linkbuttons
<div class="body-indent">

$errorMessage

<h1 style="font-size:130%">Details for Clilstore unit $id</h1>

<table id="priomh">
<tr><td>Title:</td><td style="font-weight:bold;font-size:130%">$title</td></tr>
<tr><td>Owner:</td><td><a href="./userinfo.php?user=$owner" title="$fullname">$owner</a></td></tr>
<tr><td>Short&nbsp;URL:</td><td><a href="/cs/$id">$serverhome/cs/$id</a>
<tr><td style="vertical-align:top">Summary:</td><td>$summary</td></tr>
<tr><td style="vertical-align:top">Language notes:</td><td>$langnotes</td></tr>
<tr><td>Language:</td><td><a href="./?sl=$sl">$sl</a></td></tr>
<tr><td>Level:</td><td><a href="./?sl=$sl&amp;levelMin=$cefr&amp;levelMax=$cefr">$cefr</a> <span style="color:grey;font-size:70%;padding-left:1em">($level)</span></td></tr>
<tr><td>Word count:</td><td>$words</td></tr>
<tr><td>Media:</td><td>$mediaHtml</td></tr>
<tr><td>Created:</td><td>$createdDateTime UT</td></tr>
<tr><td>Changed:</td><td>$changedDateTime UT</td></tr>
<tr><td>Licence:</td><td>Creative Commons $licence</td></tr>
<tr><td>Views:</td><td>$views</td></tr>
<tr><td>Clicks on words:</td><td>$clicks$clicksMessage - <a href='unitwordclicks.php?id=$id'>List of clicked words</a></td></tr>
<tr><td>Likes:</td><td $likeUsersTitle>$likes</td</tr>
</table>

<p><a href="page.php?id=$id">Raw unit</a> <i>(unwordlinked)</i>
⇒ <a href="http://translate.google.com/translate?sl=$sl&amp;tl=$tl&amp;u=$serverhome/clilstore/page.php%3Fid=$id">Google translated</a>
</p>

$ownerHtml

<p style="margin-top:3em;border-top:1px solid red;padding-top:12px">
<a href="http://www.languages.dk/abuse.php?subject=$serverhome/cs/$id$abuseParams" id="abuse">Report abuse</a> Tell us if you think this unit contains copyright or inappropriate material and should be removed</p>

</div>
$linkbuttons
</body>
</html>
EOD1;

  } catch (Exception $e) { echo $e; }

?>
