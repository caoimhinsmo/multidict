<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  try {
      $myCLIL = SM_myCLIL::singleton();
      if (!$myCLIL->cead('{logged-in}')) { $myCLIL->diultadh(''); }
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }

  function scriptscan($text) {
      if (preg_match('|<\?php|iu', $text)) { return 'PHP'; }
      if (preg_match('|<\?|iu',    $text)) { return 'scripting'; }
      if (preg_match('|<script|iu',$text)) { return 'scripting'; }
      return '';
  }

  function htmlify(&$text,$br) { //If text does not look like html, then put <p>..</p> tags round paragraphs
      if (empty($text)) return;
      if (preg_match('|<.{1,20}>|iu', $text)) { return; } //Looks like html so just return
      $text = htmlspecialchars($text,ENT_NOQUOTES);
      $text = preg_replace ( '|\r\n|u',  "\n",   $text );   //Convert all CR-LF to LF to standardise
      $text = preg_replace ( '|\r|u',    "\n",   $text );   //Convert all CR    to LF to standardise
      $text = preg_replace ( '| *\n|u',  "\n",   $text );   //Delete whitespace before LFs
      $text = preg_replace ( '|\n{2,}|u',"\n\n", $text );   //No more than two consecutive LFs allowed
      $text = preg_replace ( '|^\n*|u',  "<p>",  $text,1 ); //Insert <p> at start of text
      $text = preg_replace ( '|\s*$|u',  "</p>", $text,1 ); //Insert </p> at end of text
      $text = preg_replace ( '|\n\n|u',  "</p>\n\n<p>", $text );  //Insert paragraph marks within text
      if ($br) {
          $text = preg_replace ( '|\n|u',       "<br>\n", $text );  //Add <br> at the end of every line
          $text = preg_replace ( '|</p><br>|u', '</p>',   $text );  //But we don't want them at the ends of paragraphs
          $text = preg_replace ( '|\n<br>\n|u', "\n\n",   $text );  //Nor on blank lines
      }
      $text = preg_replace ( '|\n|u',   "\r\n", $text);  //Convert all LF back to CR-LR
  }

  function getEmbed($medembed) {
    //If $medembed does not in fact contain embed code, but instead is recognised as a url for a Youtube or TED video or some such, then replace this with the correct (hopefully!) embed code.
    //Can also deal with url’s ending in .mp3, .m4a and .ogg, converting them to HTML5 <audio> elements, although these may not work in all browsers and platforms
    //If $medembed is not recognised as any such url, then it is returned unchanged.
      $embedcodeArr = array(
          '|.*<iframe.*|u' => '$0', //Leave unchanged if it already contains embed code
          '|.*<object.*|u' => '$0',
          '|.*<embed.*|u'  => '$0',
          '|.*youtube\.com/watch.*[?&]v=([^?&]*.*?$)|u'
                           => '<iframe width="420" height="315" src="//www.youtube.com/embed/$1?rel=0" frameborder="0" allowfullscreen></iframe>',
          '|.*youtu\.be/(.*)|u'
                           => '<iframe width="420" height="315" src="//www.youtube.com/embed/$1?rel=0" frameborder="0" allowfullscreen></iframe>',
          '|.*ted\.com/talks/(.*)\.html$|u'
                           => '<iframe src="http://embed.ted.com/talks/$1.html" width="560" height="315" frameborder="0" scrolling="no" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>',
          '|.*vimeo\.com/([0-9]*).*?$|u'
                           => '<iframe src="//player.vimeo.com/video/$1" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>',
          '|.*dailymotion\.com/video/(.*)|u'
                           => '<iframe frameborder="0" width="480" height="270" src="http://www.dailymotion.com/embed/video/x1f90ru" allowfullscreen></iframe>',
          '|.*teachertube\.com/viewVideo.php.*[?&]video_id=([0-9]*).*?$|u'
                           => '<iframe width="560" height="315" src="http://www.teachertube.com/embed.php?pg=video_$1" frameborder="0" allowfullscreen/></iframe>',
          '|.*schooltube\.com/video/([^/]*)/.*|u'
                           => '<iframe width="500" height="375" src="http://www.schooltube.com/embed/$1" frameborder="0" allowfullscreen"></iframe>',
          '|.*kiwi6\.com/file/(.*)|u'
                           => '<iframe width="100%" scrolling="no" height="96" frameborder="no" src="http://kiwi6.com/tracks/widget/$1"></iframe>',
          '|(.*)://(.*)\.mp3$|u'
                           => '<audio controls="controls" style="width:100%;max-height:70px" title="Listen to audio (MP3 - may not work in all browsers and platforms)"> <source src="$1://$2.mp3" type="audio/mpeg"/>  <span style="color:red;font-size:90%">[No <audio> element support - You need to update your browser]</span> </audio>',
          '|(.*)://(.*)\.m4a$|u'
                           => '<audio controls="controls" style="width:100%;max-height:70px" title="Listen to audio (MP4 - may not work in all browsers and platforms)"> <source src="$1://$2.m4a" type="audio/mp4"/>  <span style="color:red;font-size:90%">[No <audio> element support - You need to update your browser]</span> </audio>',
          '|(.*)://(.*)\.ogg$|u'
                           => '<audio controls="controls" style="width:100%;max-height:70px" title="Listen to audio (.ogg - may not work in all browsers and platforms)"> <source src="$1://$2.ogg" type="audio/ogg"/>  <span style="color:red;font-size:90%">[No <audio> element support - You need to update your browser]</span> </audio>',
          '|(.*)://(.*)\.jpg$|u'  => '<img src="$1://$2.jpg" style="width:500px" alt="">',
          '|(.*)://(.*)\.jpeg$|u' => '<img src="$1://$2.jpeg" style="width:500px" alt="">',
          '|(.*)://(.*)\.png$|u'  => '<img src="$1://$2.png" style="width:500px" alt="">',
          '|(.*)://(.*)\.gif$|u'  => '<img src="$1://$2.gif" style="width:500px" alt="">'
      );
      foreach ($embedcodeArr as $pattern=>$embedcode) {
          $medembed = preg_replace($pattern,$embedcode,$medembed,-1,$count);
          if ($count>0) return $medembed;
      }
      return $medembed;
  }

  class button {
      public $ord, $but, $wl, $new, $link;

      public function __construct ($ord, $but='', $wl=0, $new=0, $link='') {
          $but  = trim(strip_tags($but));
          $link = trim(strip_tags($link));
          if (   !empty($link)
              && strpos($link,'://')==0
              && substr($link,0,7)<>'mailto:'
              && substr($link,0,5)<>'file:'
              && !is_numeric($link)
             ) { $link = "http://$link"; }  //Add http:// on the assumption that it is missing
          $this->ord  = $ord;
          $this->but  = $but;
          $this->wl   = $wl;
          $this->new  = $new;
          $this->link = trim(strip_tags($link));
      }

      public function formHtml () {
          $ord   = $this->ord;
          $but   = $this->but;
          $wlch  = ( $this->wl  ? 'checked' : '');
          $newch = ( $this->new ? 'checked' : '');
          $link  = $this->link;
          $html  = <<<EODbutHtml
<tr>
<td><input name="but[]"  value="$but"></td>
<td><input type="checkbox" name="wl[]" $wlch value="$ord" title="Whether to wordlink this link?  If you use this, remember to test whether it works, and switch WL back off if it doesn’t"></td>
<td><input type="checkbox"  name="new[]" $newch value="$ord" title="Whether to open this link in a new tab/window?"></td>
<td><input name="link[]" value="$link"></td>
</tr>
EODbutHtml;
          return $html;
      }
  }

//CEFR level descriptions
    $a1Desc =
"Can understand and use familiar everyday expressions and very basic phrases aimed at the satisfaction of needs of a concrete type. Can introduce him/herself and others and can ask and answer questions about personal details such as where he/she lives, people he/she knows and things he/she has. Can interact in a simple way provided the other person talks slowly and clearly and is prepared to help.";
    $a2Desc =
"Can understand sentences and frequently used expressions related to areas of most immediate relevance (e.g. very basic personal and family information, shopping, local geography, employment). Can communicate in simple and routine tasks requiring a simple and direct exchange of information on familiar and routine matters. Can describe in simple terms aspects of his/her background, immediate environment and matters in areas of immediate need.";
    $b1Desc =
"Can understand the main points of clear standard input on familiar matters regularly encountered in work, school, leisure, etc. Can deal with most situations likely to arise whilst travelling in an area where the language is spoken. Can produce simple connected text on topics which are familiar or of personal interest. Can describe experiences and events, dreams, hopes & ambitions and briefly give reasons and explanations for opinions and plans.";
    $b2Desc =
"Can understand the main ideas of complex text on both concrete and abstract topics, including technical discussions in his/her field of specialisation. Can interact with a degree of fluency and spontaneity that makes regular interaction with native speakers quite possible without strain for either party. Can produce clear, detailed text on a wide range of subjects and explain a viewpoint on a topical issue giving the advantages and disadvantages of various options.";
    $c1Desc =
"Can understand a wide range of demanding, longer texts, and recognise implicit meaning. Can express him/herself fluently and spontaneously without much obvious searching for expressions. Can use language flexibly and effectively for social, academic and professional purposes. Can produce clear, well-structured, detailed text on complex subjects, showing controlled use of organisational patterns, connectors and cohesive devices.";
    $c2Desc =
"Can understand with ease virtually everything heard or read. Can summarise information from different spoken and written sources, reconstructing arguments and accounts in a coherent presentation. Can express him/herself spontaneously, very fluently and precisely, differentiating finer shades of meaning even in the most complex situations.";


  try {
    $myCLIL->dearbhaich();
    $user = $myCLIL->id;
    if (!isset($_REQUEST['id'])) { throw new SM_MDexception('No id parameter'); }
    $id = $_REQUEST['id'];

    if (@$_REQUEST['editor']=='old') { $editor = 'old'; } else { $editor = 'new'; }
    if ($editor=='new') {
        $oldEditorLink = "edit.php?id=$id&amp;editor=old";
        if (isset($_GET['view'])) { $oldEditorLink .= '&amp;view'; }
        $editorsMessage = '<p style="margin:1em 0;color:green;font-size:80%">This is the Clilstore wysiwyg editor. '
                        . 'You can still <a href="'.$oldEditorLink.'">edit your unit using the old html editor</a> '
                        . 'if you feel safer with this, or want to be in full control of your html, or encounter problems copy-and-pasting text into the new editor.</p>';
        $tinymceCSS = '/clilstore/tinymce.css?bogus=' . time();  //Bogus parameter to thwart browser cache and ensure refresh while under development
        $tinymceScript = <<<EODtinyMCE
    <script type="text/javascript" src="/tinymce/tinymce.min.js"></script>
    <script>
    tinymce.init({
        selector: "textarea#text",
        entity_encoding: "raw",
        plugins: [
             "advlist autolink link image lists charmap preview hr anchor spellchecker",
             "searchreplace wordcount visualblocks visualchars code fullscreen media nonbreaking",
             "save table contextmenu directionality emoticons template paste textcolor"
       ],
       content_css: "$tinymceCSS",
       toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor", 
       style_formats: [
            {title: 'Bold text', inline: 'b'},
            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
            {title: 'Example 1', inline: 'span', classes: 'example1'},
            {title: 'Example 2', inline: 'span', classes: 'example2'},
            {title: 'Table styles'},
            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
        ]
     }); 
    </script>
EODtinyMCE;

    } else {
        $editorsMessage = '';
        $tinymceScript  = '';
    }

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    //Initialisations
    $errorMessage = $warningMessage = $cruth = $cloneHtml = '';
    $refreshTime = 1;
    $formRequired = 1;
    $scriptingMessage = 'Scripting (PHP, Javascript, etc) is not allowed. You appear to have %s in the %s.';

    if (!empty($_REQUEST['save'])) {
     // A form has been submitted, so deal with it

        $clone     =@$_POST['clone'];     if (isset($clone)) { $id = 0; } // Set id to 0 to create a new unit
        $owner     = $_POST['owner'];
        $sl        = $_POST['sl'];        $sl = trim(strip_tags($sl));  $sl = SM_WlSession::langName2Code($sl); //Accept names+codes
        $level     = $_POST['level'];
        $cefr      = $_POST['cefr'];
        $title     = $_POST['title'];     $title = trim(strip_tags($title));
        $br        =@$_POST['br'];        $br = ( empty($br) ? 0 : 1 );
        $text      = $_POST['text'];
        $medembed  = $_POST['medembed'];
        $medfloat  = $_POST['medfloat'];  $medfload = trim(strip_tags($medfloat));
        $medtype   = $_POST['medtype'];
        $medlen    =@$_POST['medlen'];    $medlen= ( isset($medlen) ? $medlen : 0 );
        $summary   = $_POST['summary'];
        $langnotes = $_POST['langnotes'];
        $licence   = $_POST['licence'];
        $test      =@$_POST['test'];      $test  = ( isset($test)   ? 1 : 0 );
        $permis    =@$_POST['permis'];    $permis= ( isset($permis) ? 1 : 0 );
        $created = $changed  = time();
        $butArr  = $_POST['but'];
        $linkArr = $_POST['link'];
        $buttons = array();  foreach ($butArr as $i=>$but) { $buttons[$i] = new button( $i, $butArr[$i], 0, 0, $linkArr[$i] ); }
        if (isset($_POST['wl'])) {
            foreach ($_POST['wl'] as $ord) {
                $buttons[$ord]->wl  = 1;
               //But check the link extension and do not wordlink pdfs, doc files and other unsuitable mime types
                $link = $linkArr[$ord];
                if (  preg_match('|^file:.+\.([A-Za-z0-9]+)$|', $link,$matches)
                   || preg_match('|^http[s]?://.+/.+\.([A-Za-z0-9]+)$|',$link,$matches) ) {
                    $ext = $matches[1];
                    if (in_array($ext,['pdf','jpg','jpeg','gif','png','doc','docx','xls','xlsx','ppt','pptx','odt','ods'])) { //Check for the most common culprits
                        $buttons[$ord]->wl = 0;
                    } else { //Check for other image, audio and video mimetypes
                        $DbMultidict = SM_DbMultidictPDO::singleton('rw');
                        $stmtSELext = $DbMultidict->prepare('SELECT mime FROM mimetypes WHERE ext=:ext');
                        $stmtSELext->execute(array(':ext'=>$ext));
                        $r = $stmtSELext->fetch(PDO::FETCH_ASSOC);
                        if ($r) {
                            extract($r);
                            $mime0 = explode('/',$mime)[0];
                            if (in_array($mime0,['image','audio','video'])) { $buttons[$ord]->wl = 0; }
                        }
                    }
                }
            }
        }
        if (isset($_POST['new'])) {
            foreach ($_POST['new'] as $ord) { $buttons[$ord]->new = 1; }
        }

        if (empty($permis)) {
            $errorMessage = 'You must tick the box to confirm that you are the author or have the right to use the material';
        } elseif (scriptscan($medembed)) {
            $errorMessage = sprintf($scriptingMessage,scriptscan($medembed),'media embed code');
        } elseif (scriptscan($text)) {
            $errorMessage = sprintf($scriptingMessage,scriptscan($text),'text');
        } elseif (scriptscan($summary)) {
            $errorMessage = sprintf($scriptingMessage,scriptscan($summary),'summary');
        } elseif (scriptscan($langnotes)) {
            $errorMessage = sprintf($scriptingMessage,scriptscan($langnotes),'language notes');
        } elseif (empty($sl)) {
            $errorMessage = 'No language code specified';
        } elseif (empty($title)) {
            $errorMessage = 'No title specified';
        } elseif (iconv_strlen($title,'UTF-8')<4) {
            $errorMessage = 'The title is too short - The minimum is 4 characters';
        } elseif (iconv_strlen($title,'UTF-8')>120) {
            $errorMessage = 'The title is too long - The maximum is 120 characters';
        } elseif (SM_csSess::minsecs2secs($medlen)==-1) {
            $errorMessage = "Invalid media length specified, &lsquo;$medlen&rsquo; - unrecognised format";
        } elseif ($level==-1 && $test==0) {
            $errorMessage = "The learner level (CEFR level) must be specified, or else you must mark this as a test unit";
        } elseif (!in_array($licence,array('BY','BY-SA','BY-ND','BY-NC','BY-NC-SA','BY-NC-ND'))) {
            $errorMessage = "&ldquo;$licence&rdquo; is not an accepted Creative Commons licence";
        } else {

            if (empty($text) && $test==0) {
                $warningMessage = 'This unit has no text at all. Should it be marked as a test unit?';
            } elseif (iconv_strlen($text,'UTF-8')<150 && $test==0) {
                $warningMessage = 'This unit has hardly any text. Should it be marked as a test unit?';
            }
            if ($medtype==0) { $medlen = 0; } else { $medlen = SM_csSess::minsecs2secs($medlen); }
            htmlify($text,$br);
            //Clean up the summary.  No line breaks or multiple spaces allowed.
            $summary = strtr($summary,"\r",' ');
            $summary = strtr($summary,"\n",' ');
            $summary = preg_replace('/\s+/', ' ', $summary);
            $summary = trim($summary);
            $words =  count(preg_split('~[^\p{L}\p{N}\']+~u',strip_tags($text)));
            //Clean up the langnotes.  No line breaks or multiple spaces allowed.
            $langnotes = strtr($langnotes,"\r",' ');
            $langnotes = strtr($langnotes,"\n",' ');
            $langnotes = preg_replace('/\s+/', ' ', $langnotes);
            $langnotes = trim($langnotes);
            //Get embed code for well known sites such as Youtube and TED
            $medembed = getEmbed($medembed);

            function getLevel($levelStr) {
                if ($levelStr=='') { return -1; }
                if (!is_numeric($levelStr)) { throw new SM_MDexception("sgrios|bog|Invalid level '$levelStr'"); }
                return intval($levelStr);
            }
            $level = getLevel($level);
            if ($level==-1) { $level = getLevel($cefr); }

            if (empty($id)) {
                $query = 'INSERT INTO clilstore('
                        . 'owner,sl,level,title,text,medembed,medfloat,medtype,medlen,words,created,changed,summary,langnotes,licence,test'
                        . ') VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                $stmt = $DbMultidict->prepare($query);
                $stmt->execute(array($user,$sl,$level,$title,$text,$medembed,$medfloat,$medtype,$medlen,$words,$created,$changed,$summary,$langnotes,$licence,$test));
                $stmt = null;
                $happyMessage = 'Unit created';
                $happyTitle   = 'Create a new Clilstore unit: Unit created';
                $id = current($DbMultidict->query("select max(id) from clilstore")->fetch()); //The id of the newly created unit
            } else {
                $stmt = $DbMultidict->prepare('SELECT changed,licence FROM clilstore WHERE id=?');
                $stmt->execute(array($id));
                $row = $stmt->fetch();
                $stmt = null;
                $prevLicence = $row['licence'];
                $bits     = explode('-',$licence);
                $prevBits = explode('-',$prevLicence);
                $newBits  = array_diff_assoc($bits,$prevBits);
                if (  ( in_array('NC',$newBits) || in_array('ND',$newBits) || (in_array('SA',$newBits)&&!in_array('ND',$prevBits)) ) //New licence contains new restrictions
                   && time()-$row['changed'] > 20 ) //Old licence has been in force for some time
                    { $warningMessage = "The new licence $licence includes restrictions not present in the old licence $prevLicence.  It may may not be possible to enforce these if people have already made copies under the old terms."; }
                $query = 'UPDATE clilstore'
                        . ' SET sl=?,level=?,title=?,text=?,medembed=?,medfloat=?,medtype=?,medlen=?,words=?,summary=?,langnotes=?,changed=?,licence=?,test=?'
                        . ' WHERE id=? AND owner LIKE ?';
                $stmt = $DbMultidict->prepare($query);
                $userRequired = ( $user=='admin' ? '%' : $user);
                $result = $stmt->execute(array(
                           $sl,$level,$title,$text,$medembed,$medfloat,$medtype,$medlen,$words,$summary,$langnotes,$changed,$licence,$test,$id,$userRequired));
                $DbMultidict->prepare('DELETE FROM csButtons WHERE id=:id')->execute(array('id'=>$id)); //Delete any previous buttons
                $happyMessage = "Edit complete";
                $happyTitle   = 'Edit a Clilstore unit: Edit complete';
            }
            $stmt = $DbMultidict->prepare('INSERT INTO csButtons(id,ord,but,wl,new,link) VALUES(?,?,?,?,?,?)');
            $nbuttons = 0;
            foreach ($buttons as $ord=>$b) {
                if (!empty($b->but) && !empty($b->link)) { $stmt->execute(array($id,$nbuttons++,$b->but,$b->wl,$b->new,$b->link)); } //Store nonempty buttons, ignoring $ord and storing new sequence numbers
            }
            $stmt = null;
            $stmt = $DbMultidict->prepare('UPDATE clilstore SET buttons=:buttons WHERE id=:id');
            $stmt->execute(array(':buttons'=>$nbuttons,':id'=>$id)); //Update in clilstore the count of the number of buttons which the unit has
            $stmt = null;
            $servername= $_SERVER['SERVER_NAME'];
            if ( isset($_GET['view']) ) { $happyRefresh = "http://$servername/cs/$id";     }
                                   else { $happyRefresh = "http://$servername/clilstore/"; }
            if (!empty($warningMessage)) {
                $refreshTime = 4;
                $warningMessage = "<p style=\"color:#d70;font-weight:bold\">Warning: $warningMessage</p>";
            }
            echo <<<EOD2
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="$refreshTime; url=$happyRefresh">
    <title>$happyTitle</title>
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
</head>
<body>

<p style="color:green;font-weight:bold"><span style="font-size:200%">✔</span>$happyMessage</p>
$warningMessage

</body>
</html>
EOD2;
            $formRequired = 0;
        }
    }


    if ($formRequired) {

        if (empty($_REQUEST['save'])) {
         // No form has been submitted; so first obtain the initial values, then create one 
            $permis = $medtype = $medlen = 0;
            $buttons = array();
            if (empty($id)) {
             //Creating a new unit
                $sl = $title = $text = $medembed = $summary = $langnotes = '';
                $owner = $user;
                $level = -1;
                $medfloat = 'scroll';
                $licence = 'BY-SA';
                $legend = 'Creating a new Clilstore unit';
               //See if the user has a default language set for new units
                $stmt = $DbMultidict->prepare('SELECT unitLang FROM users WHERE user=:user');
                $stmt->execute(array('user'=>$user));
                if ($row = $stmt->fetch()) { $sl = $row['unitLang']; }
                $stmt = null;
            } else {
             //Editing an old unit, so first fetch the values from the database
                $stmt = $DbMultidict->prepare('SELECT owner,sl,level,title,text,medembed,medfloat,medtype,medlen,summary,langnotes,licence,test FROM clilstore WHERE id=:id');
                $stmt->execute(array('id'=>$id));
                if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) { throw new SM_MDexception("No unit found for id=$id"); }
                extract($row);
                if ($user<>$owner && $user<>'admin') { throw new SM_MDexception('You may only edit your own units'); }

                $stmt = $DbMultidict->prepare('SELECT ord,but,wl,new,link FROM csButtons WHERE id=:id');
                $stmt->execute(array('id'=>$id));
                while ($r = $stmt->fetch(PDO::FETCH_OBJ)) { $buttons[] = new button($r->ord,$r->but,$r->wl,$r->new,$r->link); }
                $text = str_replace('&','&amp;',$text);
                $permis = 'checked';
                $legend = "Editing Clilstore unit $id";
                $cruth  = 'html'; 
            }
            if (count($buttons)<8) { $buttons[] = new button(count($buttons)); } // Always create at least one blank button, up to a maximum of 8 buttons
            for ($ord=count($buttons);$ord<4;$ord++) { $buttons[] = new button($ord); } //Create new blank buttons if necessary, to give a minimum of 4 buttons
        }
        $clonech = ( empty($clone) ? '' : ' checked' );
        $brch    = ( empty($br)    ? '' : ' checked' );
        $testch  = ( empty($test)  ? '' : ' checked' );
        $permisch= ( empty($permis)? '' : ' checked' );
        $medtype0sel  = ( $medtype==0 ? ' checked' : '' );
        $medtype1sel  = ( $medtype==1 ? ' checked' : '' );
        $medtype2sel  = ( $medtype==2 ? ' checked' : '' );
        $medlenHtml = SM_csSess::secs2minsecs($medlen);  if ($medlenHtml=='?:??') { $medlenHtml = ''; }

        $medembedHtml = htmlspecialchars($medembed);
        if ($id>0) { $cloneHtml = "<input type=checkbox name=clone id=clone $clonech> <label for=clone>Clone as a new unit</label>"; }
        if ($editor=='new') {
            $textAdvice = '(If you find your browser refuses to paste text into here, try drag-and-drop instead, or use the old html editor, or try another browser)';
        } else {
            $textAdvice = ( $cruth=='html'
                          ? 'This is html - Remember to put &lt;p&gt;...&lt;/p&gt; round any new paragraphs you insert'
                          : "<b>Either</b> plaintext with blank lines between paragraphs (<input type=checkbox name=br id=br $brch> <label for=br>tick to preserve line breaks at ends of lines</label>)."
                           .' &nbsp;&nbsp;&nbsp;&nbsp; <b>Or else</b> entirely in html.' );
        }
        $floatnone = $floatleft = $floatright = $floatscroll = '';
        if      ($medfloat=='none')  { $floatnone  = ' selected'; }
         elseif ($medfloat=='left')  { $floatleft  = ' selected'; }
         elseif ($medfloat=='right') { $floatright = ' selected'; }
         elseif ($medfloat=='scroll'){ $floatscroll= ' selected'; }
         else                        { $floatscroll= ' selected'; }

        $slArr = SM_WlSession::slArr();
        foreach ($slArr as $lang=>$langInfo) { $slArray[$lang] = $langInfo['endonym']; }
        setlocale(LC_COLLATE,'en_GB.UTF-8');
        uasort($slArray,'strcoll');
        $slArray = array_merge(array(''=>'-Choose-'),$slArray);
        $slOptionHtml = '';
        foreach ($slArray as $code=>$name) {
            $selectHtml = ( $sl==$code ? ' selected="selected"' : '');
            $slOptionHtml .= "  <option value=\"$code\"$selectHtml>$name</option>\n";
        }

        if (empty($id)) { //Creating a new unit
            $submitValue = 'Publish';
            $filesButton = '<span class="info">You can upload and attach attach files to the unit for use on buttons, but to do this you must save the unit first then edit it again.</span>';
        } else {
            $submitValue = 'Save unit';
            $filesButton = <<<EODfilesButton
<a href="manageFiles.php?id=$id" class="button" title="Upload and manage files attached to this unit" target="_blank">Files…</a>
<span class="info">Upload and manage files attached to this unit...</span>
EODfilesButton;
        }
        $errorMessage = ( empty($errorMessage) ? '' :  '<div class="errorMessage">'.$errorMessage.'<br><br>The '
                                                     . ($id==0 ? 'new unit was' : 'changes were')
                                                     . ' not saved. Correct any errors or omissions and resubmit.</div>' );
//Fossil - delete
//        for ($i=0;$i<=3;$i++) {
//            ${'wl' .$i.'ch'} = ( ${'wl' .$i} ? 'checked' : '' );
//            ${'new'.$i.'ch'} = ( ${'new'.$i} ? 'checked' : '' );
//        }
        $buttonsHtml = '';
        foreach ($buttons as $b) { $buttonsHtml .= $b->formHtml(); }

        $titleSC = htmlspecialchars($title);

        $chkLic = array();
        $chkLic['BY-SA']
      = $chkLic['BY']
      = $chkLic['BY-ND']
      = $chkLic['BY-NC-SA']
      = $chkLic['BY-NC']
      = $chkLic['BY-NC-ND'] = '';
        $chkLic[$licence] = 'checked';

        echo <<<EOD1
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>$legend</title>
    <link rel="stylesheet" href="/css/smo.css" type="text/css">
    <link rel="stylesheet" href="style.css?version=2014-05-15">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style type="text/css">
        table#editlinkbuts { margin-bottom:0.5em; }
        table#editlinkbuts td:nth-child(1) input { width:12em; text-align:center; background-color:#bfb; color:black; 
                                                   padding:2px 4px; border:1px solid green; border-radius:6px; }
        table#editlinkbuts td:nth-child(2)       { width:1.5em; text-align:center; }
        table#editlinkbuts td:nth-child(3)       { width:1.8em; text-align:center; }
        table#editlinkbuts td:nth-child(4) input { min-width:60em; }
        label.rad { border:1px solid black; padding:1px 3px; border-radius:3px; background-color:#ffd; }
        label.highlighted    { background-color:#ff4; }
        label.bithighlighted { background-color:#ff9; }
        label.midrange       { background-color:#ed0; }
        div.box { border:1px solid black; padding:4px; border-radius:4px; background-color:#ffd; }
        div.errorMessage { margin:0.5em 0; color:red; font-weight:bold; }
        table.licence { margin:3px 0 0 6em; border-spacing:0; }
        table.licence td { border:2px solid white; border-radius:6px; }
        table.licence td.chk { border-color:#bb3; background-color:#ffd; }
        table.licence img { padding:1px 25px 4px 4px; }
        table.licence input { margin:0 0 0 4px; padding:0; }
        div#licenceInfo { margin:4px 0 0 6px; border:2px solid #bb3; border-radius:6px; background-color:#ffd; padding:2px; }
        div.fann { opacity:0.25; }
    </style>
    <script>

        function setLevel(level) {
        //Lots of things to do when the level changes
            document.getElementById('levnum').innerHTML=parseFloat(level);
           //Set and display the message
            var message;
            if      (level==-1) { radValue = -1; message = ''; }
             else if (level<10) { radValue =  5; message = 'Breakthrough';}
             else if (level<20) { radValue = 15; message = 'Waystage';}
             else if (level<30) { radValue = 25; message = 'Threshold';}
             else if (level<40) { radValue = 35; message = 'Vantage';}
             else if (level<50) { radValue = 45; message = 'Effective operational efficiency';}
             else if (level<60) { radValue = 55; message = 'Mastery';}
             else               { radValue = -1; message = '';}
            if (level!=-1) {
                if       (level%10 < 3) { message += ' <span style="color:#777">(easier side)</span>'; }
                 else if (level%10 < 5) { message += ' <span style="color:#aaa">(easier side)</span>'; }
                if       (level%10 > 7) { message += ' <span style="color:#777">(harder side)</span>'; }
                 else if (level%10 > 5) { message += ' <span style="color:#aaa">(harder side)</span>'; }
            }
            document.getElementById('cefrmessage').innerHTML = message;
           //Select and highlight the correct cefr radio button
            var radioObj = document.getElementsByName('cefr');
            for (var i=0; i< radioObj.length; i++) {
                var newCheck = false;
                var newClass = 'rad';
                if (radioObj[i].value == radValue.toString()) {
                    newCheck = true;
                    if      (radValue == -1)  { newClass += ' bithighlighted'; }
                    else if (level%10 == 5)   { newClass += ' midrange';       }
                    else                      { newClass += ' highlighted';    }
                }
                radioObj[i].checked = newCheck;
                radioObj[i].parentNode.className = newClass;
            }
        }

        function radClick(level) {
            document.getElementById('level').value = level;
            setLevel(level);
        }

        function medlenDisp(medtype) {
            var disp = 'inline';
            if (medtype==0) { disp = 'none'; }
            document.getElementById('medlen').style.display = disp;
        }

        function testClick(cb) {
            var testLabel = 'Create test unit';
            var nontestLabel = 'Publish';
            var testvalue = cb.checked;
            saveEl = document.getElementById('save');
            if (testvalue) {
                if (saveEl.value==nontestLabel) { saveEl.value = testLabel; }
            } else {
                if (saveEl.value==testLabel) { saveEl.value = nontestLabel; }
            }
        }

        function licenceChange(licence) {
            document.getElementById('licenceInfo').innerHTML = '&nbsp;';
            var licName, licNameLink, i, bits, info = '';
           //Highlight the selection
            document.getElementById('td-BY-SA').className = '';
            document.getElementById('td-BY').className = '';
            document.getElementById('td-BY-ND').className = '';
            document.getElementById('td-BY-NC-SA').className = '';
            document.getElementById('td-BY-NC').className = '';
            document.getElementById('td-BY-NC-ND').className = '';
            document.getElementById('td-'+licence).className = 'chk';
           //Construct licence info
            switch(licence) {
                case 'BY-SA'   : licName = 'Attribution-ShareAlike';               break;
                case 'BY'      : licName = 'Attribution';                          break;
                case 'BY-ND'   : licName = 'Attribution-NoDerivs';                 break;
                case 'BY-NC-SA': licName = 'Attribution-NonCommercial-ShareAlike'; break;
                case 'BY-NC'   : licName = 'Attribution-NonCommercial';            break;
                case 'BY-NC-ND': licName = 'Attribution-NonCommercial-NoDerivs';   break;
            }
            licNameLink = '<a href="http://creativecommons.org/licenses/' + licence.toLowerCase() + '/4.0/">' + licName + '</a>';
            var message = { 'BY':'Anyone sharing a copy or derivative of this unit must acknowledge the original author. (A link to the original unit will do)',
                            'SA':'Any new products based on this unit may only be shared under this same ' + licence  + ' licence.',
                            'ND':'Identical copies of this unit may be shared, but products derived from it may not.',
                            'NC':'Commercial use of this unit is not allowed.' }; 
            bits = licence.split('-');
            if (bits[bits.length-1]=='ND') { message['BY'] = message['BY'].replace('or derivative ',''); }
            info = '&nbsp;&nbsp;' + licNameLink + '<ul class="info" style="margin-top:2px">';
            for ( i=0; i<bits.length; i++ ) { info += '<li>' + message[bits[i]]; }
            info += '</ul>';
            document.getElementById('licenceInfo').innerHTML = info;
        }

        function permisChange() {
            permisvalue = document.getElementById('permis').checked;
            ccdiv = document.getElementById('ccdiv');
            if (permisvalue) { ccdiv.className = ''; }
             else            { ccdiv.className = 'fann'; }
        }
    </script>

$tinymceScript

</head>
<body onload="setLevel($level); medlenDisp($medtype); licenceChange('$licence'); permisChange();">

<ul class="linkbuts">
<li><a href="./" title="Clilstore index page">Clilstore</a>
</ul>
<div class="smo-body-indent">
$errorMessage
$editorsMessage

<form method="post" name="mainForm">

<fieldset style="background-color:#eef;border:8px solid #55a8eb;border-radius:10px">
<legend style="width:auto;margin-left:auto;margin-right:auto;background-color:#55a8eb;color:white;padding:1px 3em">$legend</legend>
<div style="float:right;padding:3px;font-size:75%;color#333" title="Copy this unit and create a new unit with a new unit number - not something you would do very often">
$cloneHtml</div>
<div>Title<br>
<input name="title" value="$titleSC" required pattern=".{4,120}" title="Title (between 4 and 120 characters long)" style="width:99%"></div>
<div style="margin-top:6px">Embed code for media or picture (if any) <span style="font-size:80%;padding-left:3em">Float or scroll
<select name="medfloat" style="width:7em" title="Choose the placement on the page">
  <option value="none"$floatnone> </option>
  <option value="left"$floatleft>left</option>
  <option value="right"$floatright>right</option>
  <option value="scroll"$floatscroll>scroll text</option>
</select>
</span>
<input name="medembed" value="$medembedHtml" style="width:99%"></div>
<div style="margin-top:6px">
Text <span class="info" style="padding-left:2em">$textAdvice</span><br>
<textarea name="text" id="text" placeholder="The text for the students to read (minimum length 100 characters)" style="width:100%;height:400px">$text</textarea></div>
<fieldset style="margin:6px 0 0 0;border:1px solid green;padding:5px;corner-radius:5px">
<legend>Link buttons</legend>
<table id="editlinkbuts">
<tr style="font-size:85%">
<td style="text-align:center">Button text</td>
<td title="Whether to wordlink this link?">WL</td>
<td title="Whether to open this link in a new tab/window?">New</td>
<td>Link <span class="info">(url or Clilstore unit number)</span></td>
</tr>
$buttonsHtml
</table>
$filesButton
</fieldset>
</fieldset>

<div style="margin-top:5px">
Language
<select name="sl" required>
$slOptionHtml
</select>
</div>

<div style="margin:12px 0;padding:4px;border:0">
Learner level
(<a href="http://en.wikipedia.org/wiki/Common_European_Framework_of_Reference_for_Languages#Common_reference_levels" title="Common European Framework of Reference for Languages">CEFR</a>)&nbsp;

<label for="un" class="rad">
 <input type="radio" name="cefr" value="-1" id="un" onclick="radClick(-1);"><span style="color:grey;font-size:75%;vertical-align:middle">Unspecified</span></label>&nbsp;
<label for="a1" class="rad" title="$a1Desc">
 <input type="radio" name="cefr" value="5"  id="a1" onclick="radClick(5); ">A1</label>
<label for="a2" class="rad" title="$a2Desc">
 <input type="radio" name="cefr" value="15" id="a2" onclick="radClick(15);">A2</label>
<label for="b1" class="rad" title="$b1Desc">
 <input type="radio" name="cefr" value="25" id="b1" onclick="radClick(25);">B1</label>
<label for="b2" class="rad" title="$b2Desc">
 <input type="radio" name="cefr" value="35" id="b2" onclick="radClick(35);">B2</label>
<label for="c1" class="rad" title="$c1Desc">
 <input type="radio" name="cefr" value="45" id="c1" onclick="radClick(45);">C1</label>
<label for="c2" class="rad" title="$c2Desc">
 <input type="radio" name="cefr" value="55" id="c2" onclick="radClick(55);">C2</label>

<input name="level" id="level" type="range" min=-1 max=59 value=$level style="width:17em;color:#aaa" title="range 0-59" oninput="setLevel(value);" onchange="setLevel(value);">
<output id="levnum" style="font-size:80%;color:#ccc;display:inline-block;width:20px;text-align:right"></output>
<output id="cefrmessage"><span style="color:red;font-size:80%">Warning: you have Javascript switched off</span></output><br>
<span class="info" style="padding-left:15em">The learner level must be specified, or else the unit must be marked as a test unit)</span>
</div>

<div style="margin-top:6px">
Media type:<br>
&nbsp;<input type="radio" name="medtype"$medtype2sel value="2" id="medtype2" onclick="medlenDisp(2)"><label for="medtype2">video</label><br>
&nbsp;<input type="radio" name="medtype"$medtype1sel value="1" id="medtype1" onclick="medlenDisp(1)"><label for="medtype1">sound only</label><br>
&nbsp;<input type="radio" name="medtype"$medtype0sel value="0" id="medtype0" onclick="medlenDisp(0)"><label for="medtype0">neither</label><br>
<span id="medlen">Media length: <input name="medlen" value="$medlenHtml" style="width:4em;text-align:right" placeholder="?:??" title="media length in seconds, or in minutes and seconds">
<span class="info">e.g. 80, 80s, 1:20</span></span>&nbsp;
</div>

<div style="margin-top:8px">
Summary: <span class="info">(1000 character maximum)</span>
<textarea name="summary" style="width:99%;height:2.5em;margin:2px;padding:0.4em;border:1px solid;border-radius:0.4em">$summary</textarea>
</div>

<div style="margin-top:6px">
Language notes: <span class="info">(1000 character maximum)</span>
<textarea name="langnotes" style="width:99%;height:2.5em;margin:2px;padding:0.4em;border:1px solid;border-radius:0.4em">$langnotes</textarea>
</div>

<div style="margin-top:7px">
<input type="checkbox" name="test" id="test" $testch title="Tick if this is still just a test unit" onClick="testClick(this)">
<label for="test">Tick if this is still just a test unit, not a production unit</label>
</div>

<div style="margin-top:8px">
Owner: <span style="padding:1px 3px;border:1px solid">$owner</span>
&nbsp;<input  type="checkbox" name="permis" id="permis" required $permisch onChange="permisChange();">
<label for="permis">I am the author of the text and material <i>or</i> I have permission to use the text and material. <i>And</i> I agree to the Clilstore</label> <a href="copyleftPolicy.html">copyleft policy</a>.
</div>

<div style="margin-left2:5em" id="ccdiv">
I grant use of this unit under the following <a href="http://creativecommons.org/licenses/">Creative Commons</a> licence
<table id="licTable" class="licence">
<tr>
   <td id="td-BY-SA">
       <input type="radio" name="licence" value="BY-SA" id="rad-BY-SA" {$chkLic['BY-SA']} onclick="licenceChange('BY-SA');">
       <label for="rad-BY-SA">BY-SA<br>
       <img src="/icons-smo/CC-BY-SA.png" alt="CC-BY-SA" title="Attribution-ShareAlike"></label>
   </td>
   <td id="td-BY">
       <input type="radio" name="licence" value="BY" id="rad-BY" {$chkLic['BY']} onclick="licenceChange('BY');">
       <label for="rad-BY">BY<br>
       <img src="/icons-smo/CC-BY.png" alt="CC-BY" title="Attribution"></label>
   </td>
   <td id="td-BY-ND">
       <input type="radio" name="licence" value="BY-ND" id="rad-BY-ND" {$chkLic['BY-ND']} onclick="licenceChange('BY-ND');">
       <label for="rad-BY-ND">BY-ND<br>
       <img src="/icons-smo/CC-BY-ND.png" alt="CC-BY-ND" title="Attribution-NoDerivs"></label>
   </td>
   <td rowspan="2" style="vertical-align:top">
       <div id="licenceInfo">&nbsp;</div>
   </td>
</tr>
<tr>
   <td id="td-BY-NC-SA">
       <input type="radio" name="licence" value="BY-NC-SA" id="rad-BY-NC-SA" {$chkLic['BY-NC-SA']} onclick="licenceChange('BY-NC-SA');">
       <label for="rad-BY-NC-SA">BY-NC-SA<br>
       <img src="/icons-smo/CC-BY-NC-SA.png" alt="CC-BY-NC-SA" title="Attribution-NonCommercial-ShareAlike"></label>
   </td>
   <td id="td-BY-NC">
       <input type="radio" name="licence" value="BY-NC" id="rad-BY-NC" {$chkLic['BY-NC']} onclick="licenceChange('BY-NC');">
       <label for="rad-BY-NC">BY-NC<br>
       <img src="/icons-smo/CC-BY-NC.png" alt="CC-NC-BY" title="Attribution-NonCommercial"></label>
   </td>
   <td id="td-BY-NC-ND">
       <input type="radio" name="licence" value="BY-NC-ND" id="rad-BY-NC-ND" {$chkLic['BY-NC-ND']} onclick="licenceChange('BY-NC-ND');">
       <label for="rad-BY-NC-ND">BY-NC-ND<br>
       <img src="/icons-smo/CC-BY-NC-ND.png" alt="CC-NC-BY-ND" title="Attribution-NonCommercial-NoDerivs"></label>
   </td>
</tr>
</table>
</div>

<div style="margin-top:8px">
<input type="submit" name="save" id="save" value="$submitValue">
</div>

<input type="hidden" name="owner" value="$owner">
</form>

</div>
<ul class="linkbuts" style="margin-top:1.5em">
<li><a href="./" title="Clilstore index page">Clilstore</a></li>
</ul>

</body>
</html>
EOD1;

    }

  } catch (Exception $e) { echo $e; }

?>
