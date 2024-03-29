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

  $T = new SM_T('clilstore/unitinfo');

  $T_Language  = $T->h('Language');
  $T_Title     = $T->h('Title');
  $T_Owner     = $T->h('csCol_owner');
  $T_Short_url = $T->h('Short_url');
  $T_Summary   = $T->h('Summary');
  $T_Level     = $T->h('Level_title');
  $T_Media     = $T->h('csCol_medtype');
  $T_Created   = $T->h('csCol_created');
  $T_Changed   = $T->h('csCol_changed');
  $T_Licence   = $T->h('csCol_licence');
  $T_Views     = $T->h('csCol_views');
  $T_Likes     = $T->h('csCol_likes');
  $T_since_    = $T->h('since_'); 
  $T_Offer     = $T->h('Offer');
  $T_Language_notes  = $T->h('Language_notes');
  $T_Word_count      = $T->h('Word_count');
  $T_Clicks_on_words = $T->h('Clicks_on_words');
  $T_List_of_clicked_words = $T->h('List_of_clicked_words');
  $T_Parameter_p_a_dhith   = $T->h('Parameter_p_a_dhith');
  $T_Raw_unit_unwordlinked = $T->h('Raw_unit_unwordlinked');
  $T_Google_translated     = $T->h('Google_translated');
  $T_Ownership_transfer    = $T->h('Ownership_transfer');
  $T_Offer_to_transfer_    = $T->h('Offer_to_transfer_');
  $T_No_such_userid_as_    = $T->h('No_such_userid_as_');
  $T_The_unit_is_on_offer_ = $T->h('The_unit_is_on_offer_');
  $T_Withdraw_the_offer    = $T->h('Withdraw_the_offer');
  $T_User_can_accept_      = $T->h('User_can_accept_');
  $T_Offer_it_instead_to_  = $T->h('Offer_it_instead_to_');
  $T_Clilstore_user_id     = $T->h('Clilstore_user_id');
  $T_Details_for_unit_     = $T->h('Details_for_unit_');
  $T_The_unit_was_on_offer = $T->h('The_unit_was_on_offer');
  $T_Report_abuse          = $T->h('Report_abuse');
  $T_Report_abuse_label    = $T->h('Report_abuse_label');

  $id = $_REQUEST['id'] ?? NULL;
  $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan,$id);
  $T_Raw_unit_unwordlinked = strtr($T_Raw_unit_unwordlinked,[ '{'=>"<a href=/clilstore/page.php?id=$id>", '}'=>'</a>', '('=>'<i>(', ')'=>')</i>' ]);
  $T_Details_for_unit_ = strtr($T_Details_for_unit_, ['{unitNo}'=>$id]);

  try {
    if (is_null($id))     { throw new SM_MDexception(sprintf($T_Parameter_p_a_dhith,'id')); }
    if (!is_numeric($id)) { throw new SM_MDexception('id parameter is not numeric'); }

    $abuseParams = $ownerHtml = $offerMess = $errorMessage = '';

    $servername = SM_myCLIL::servername();
    $serverhome = SM_myCLIL::serverhome();
    $DbMultidict = SM_DbMultidictPDO::singleton('rw');

    if (isset($_REQUEST['offerSubmit'])) {
        $offer = $_REQUEST['offer'] ?? '';
        if (!empty($offer)) {
            $stmt = $DbMultidict->prepare('SELECT user FROM users WHERE user=:user');
            $stmt->execute(array(':user'=>$offer));
            if (!$stmt->fetch()) {
                $errorMessage = '<p style="color:red">' . strtr($T_No_such_userid_as_,['{userid}'=>$offer]) . '<p>';
            } elseif ($offer==$user) {
                $errorMessage = '<p style="color:red">You can’t transfer ownership from yourself to yourself</p>';
            } else {
                $offerTime = time();
                $stmt = $DbMultidict->prepare("UPDATE clilstore SET offer=:offer,offerTime=:offerTime where id=:id AND (owner=:user OR '$user'='admin')");
                $stmt->execute( array(':id'=>$id, ':user'=>$user, ':offer'=>$offer, ':offerTime'=>$offerTime) );
                SM_csSess::logWrite($user,'offerMake',"User $user offered unit $id to user $offer",$offer);
            }
        }
    }
    if (isset($_REQUEST['withdrawSubmit'])) {
        $stmt = $DbMultidict->prepare("UPDATE clilstore SET offer=NULL,offerTime=0 WHERE id=:id AND (owner=:user OR '$user'='admin')");
        $stmt->execute(array('id'=>$id,':user'=>$user));
        SM_csSess::logWrite($user,'offerWithdraw',"User $user withdrew the offer on unit $id");
    }

    $query = 'SELECT sl,level,owner,medtype,medlen,title,summary,langnotes,words,created,changed,licence,views,clicks,likes,offer,offerTime,fullname'
            .' FROM clilstore, users WHERE id=:id AND clilstore.owner=users.user';
    $stmt = $DbMultidict->prepare($query);
    $stmt->execute([':id'=>$id]);
    if (!($r = $stmt->fetch(PDO::FETCH_ASSOC))) { throw new SM_MDexception("No unit exists for id=$id"); }
    extract($r);
    $licenceLC = strtolower($licence);
    $tl = ( $sl=='en' ? 'es' : 'en'); //airson Google Translate
    $summary   = htmlspecialchars($summary);
    $langnotes = htmlspecialchars($langnotes);
    $words = ( isset($words) ? $words : '');
    $clicksMessage = ( $created>1395188407 ? '' : ' <span style="color:grey;font-size:80%">(' . sprintf($T_since_,'2014-03-18') . ')</span>' ) ;

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

    $stmtLikeUsers = $DbMultidict->prepare('SELECT user FROM user_unit WHERE unit=:id AND likes>0 ORDER BY user');
    $stmtLikeUsers->execute([':id'=>$id]);
    $likeUsersArr = $stmtLikeUsers->fetchAll(PDO::FETCH_COLUMN);
    $likeUsers = htmlspecialchars(implode(', ',$likeUsersArr));
    $likeUsersTitle = ( $likeUsers ? " title='$likeUsers'" : '' );

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
            $offerMess .= "$T_Offer_to_transfer_";
        } else {
            if (!empty($offerTime)) {
                $offerTimeObj = new DateTime("@$offerTime");
                $offerDateTime = date_format($offerTimeObj, 'Y-m-d H:i:s');
                $T_The_unit_is_on_offer_ = strtr($T_The_unit_is_on_offer_,
                  [ '{userid}' => "<a href='userinfo.php?user=$offer' style='font-weight:bold'>$offer</a>",
                    '{dateTime}' => $offerDateTime,
                    '(' => '<span style="font-size:80%;color:grey">', ')' => '</span>' ]);
                $T_User_can_accept_ = strtr($T_User_can_accept_, ['{userid}'=>$offer]);
                $offerMess .= <<<END_offerMess
<form method=post>
$T_The_unit_is_on_offer_
<input type=submit name="withdrawSubmit" value="$T_Withdraw_the_offer" style="margin-left:0.5em"><br>
</form>
<span class="info" style="padding-left:2em">$T_User_can_accept_</span><br><br>
END_offerMess;
            } else {
                $T_The_unit_was_on_offer = strtr($T_The_unit_was_on_offer, ['{userid}' => "<a href='/userinfo.php?user=$oxsffer'>$offer</a>"]);
                $offerMess .= "$T_The_unit_was_on_offer<br>\n";
            }
            $offerMess .= $T_Offer_it_instead_to_;
        }
        $offerMess .= "<form action='unitinfo.php?id=$id' method=post>\n"
                    . "<input name='offer' list='userList' placeholder='$T_Clilstore_user_id'> <input type=submit name='offerSubmit' value='$T_Offer'></form>\n";
        $ownerHtml = <<<EODowner
<fieldset id="transfer"><legend>$T_Ownership_transfer</legend>$offerMess</fieldset>
EODowner;
    }


    echo <<<EOD1
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>$T_Details_for_unit_</title>
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="StyleSheet" href="style.css?version=2014-04-15">
    <style>
        div.body-indent { clear:both; margin:0 0.25%; padding:0 0.25% 6px 0.25%; border-top:1px solid white; }
        table#priomh    { border-collapse:collapse; }
        table#priomh td { padding:7px 4px; vertical-align:top; }
        table#priomh td:first-child { white-space:nowrap; text-align:right; font-weight:bold; }
        form { display:inline; }
        fieldset#transfer { margin-top:1.5em; border:2px solid #61abec; border-radius:6px; padding:6px; margin-bottom:2em; }
        fieldset#transfer legend { border:2px solid #61abec; border-radius:4px; padding:1px 4px; background-color:#eef; color:#55a8eb; font-weight:bold; }
        a#abuse { padding:2px; border:1px solid red; border-radius:4px; font-weight:bold; color:red; background-color:#fdd; }
        a#abuse:hover { background-color:#f99; }
    </style>

</head>
<body>
$mdNavbar
<div class="body-indent">

$errorMessage

<h1 style="font-size:130%">$T_Details_for_unit_</h1>

<table id="priomh">
<tr><td>$T_Title:</td><td style="font-weight:bold;font-size:130%">$title</td></tr>
<tr><td>$T_Owner:</td><td><a href="./userinfo.php?user=$owner" title="$fullname">$owner</a></td></tr>
<tr><td>$T_Short_url:</td><td><a href="/cs/$id">$serverhome/cs/$id</a>
<tr><td style="vertical-align:top">$T_Summary:</td><td>$summary</td></tr>
<tr><td style="vertical-align:top">$T_Language_notes:</td><td>$langnotes</td></tr>
<tr><td>$T_Language:</td><td><a href="./?sl=$sl">$sl</a></td></tr>
<tr><td>$T_Level:</td><td><a href="./?sl=$sl&amp;levelMin=$cefr&amp;levelMax=$cefr">$cefr</a> <span style="color:grey;font-size:70%;padding-left:1em">($level)</span></td></tr>
<tr><td>$T_Word_count:</td><td>$words</td></tr>
<tr><td>$T_Media:</td><td>$mediaHtml</td></tr>
<tr><td>$T_Created:</td><td>$createdDateTime UT</td></tr>
<tr><td>$T_Changed:</td><td>$changedDateTime UT</td></tr>
<tr><td style="vertical-align:bottom">$T_Licence:</td><td><a href="https://creativecommons.org/licenses/$licenceLC/4.0/"><img src="/icons-smo/CC-$licence.png" alt=""> Creative Commons $licence</a></td></tr>
<tr><td>$T_Views:</td><td>$views</td></tr>
<tr><td>$T_Clicks_on_words:</td><td>$clicks$clicksMessage - <a href='unitwordclicks.php?id=$id'>$T_List_of_clicked_words</a></td></tr>
<tr><td>$T_Likes:</td><td$likeUsersTitle>$likes</td</tr>
</table>

<p>$T_Raw_unit_unwordlinked
⇒ <a href="http://translate.google.com/translate?sl=$sl&amp;tl=$tl&amp;u=$serverhome/clilstore/page.php%3Fid=$id">$T_Google_translated</a>
</p>

$ownerHtml

<p style="margin-top:3em;border-top:1px solid red;padding-top:12px">
<a href="http://www.languages.dk/abuse.php?subject=$serverhome/cs/$id$abuseParams" id="abuse">$T_Report_abuse</a><br>$T_Report_abuse_label</p>

</div>
$mdNavbar
</body>
</html>
EOD1;

  } catch (Exception $e) { echo $e; }

?>
