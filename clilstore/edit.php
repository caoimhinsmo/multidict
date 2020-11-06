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

  $T = new SM_T('clilstore/edit');

  $T_Creating_new_unit   = $T->h('Creating_new_unit');  
  $T_editorsMessage      = $T->h('editorsMessage');
  $T_Title               = $T->h('Title');
  $T_Title_info_4_120    = $T->h('Title_info_4_120');
  $T_Language            = $T->h('Language');
  $T_Embed_code_legend   = $T->h('Embed_code_legend');
  $T_Left                = $T->h('Left');
  $T_Right               = $T->h('Right');
  $T_Scroll_text         = $T->h('Scroll_text');
  $T_Clone_as_a_new_unit = $T->h('Clone_as_a_new_unit');
  $T_Text                = $T->h('Text');
  $T_Text_placeholder    = $T->h('Text_placeholder');
  $T_Text_Advice         = $T->h('Text_Advice');
  $T_Text_Advice_html    = $T->h('Text_Advice_html');
  $T_Text_Advice_new     = $T->h('Text_Advice_new');
  $T_Text_Advice_new_br  = $T->h('Text_Advice_new_br');
  $T_Link_buttons        = $T->h('Link_buttons');
  $T_Button_text         = $T->h('Button_text');
  $T_You_can_write_here  = $T->h('You_can_write_here');
  $T_Whether_to_WL_link  = $T->h('Whether_to_WL_link');
  $T_Whether_to_WL_info  = $T->h('Whether_to_WL_info');
  $T_Whether_to_new_tab  = $T->h('Whether_to_new_tab');
  $T_Learner_level       = $T->h('Learner_level');
  $T_CEFR                = $T->h('CEFR');
  $T_CEFR_longname       = $T->h('CEFR_longname');
  $T_Unspecified         = $T->h('Unspecified');
  $T_Choose_level_info   = $T->h('Choose_level_info');
  $T_CEFR_A1_name        = $T->h('CEFR_A1_name');
  $T_CEFR_A2_name        = $T->h('CEFR_A2_name');
  $T_CEFR_B1_name        = $T->h('CEFR_B1_name');
  $T_CEFR_B2_name        = $T->h('CEFR_B2_name');
  $T_CEFR_C1_name        = $T->h('CEFR_C1_name');
  $T_CEFR_C2_name        = $T->h('CEFR_C2_name');
  $T_easier_side         = $T->h('easier_side');
  $T_harder_side         = $T->h('harder_side');
  $T_CEFR_A1_description = $T->h('CEFR_A1_description');
  $T_CEFR_A2_description = $T->h('CEFR_A2_description');
  $T_CEFR_B1_description = $T->h('CEFR_B1_description');
  $T_CEFR_B2_description = $T->h('CEFR_B2_description');
  $T_CEFR_C1_description = $T->h('CEFR_C1_description');
  $T_CEFR_C2_description = $T->h('CEFR_C2_description');
  $T_Media_type          = $T->h('Media_type');
  $T_Media_length        = $T->h('Media_length');
  $T_Media_length_title  = $T->h('Media_length_title');
  $T_video               = $T->h('video');
  $T_sound_only          = $T->h('sound_only');
  $T_neither             = $T->h('neither');
  $T_eg                  = $T->h('eg');
  $T_Summary             = $T->h('Summary');
  $T_Language_notes      = $T->h('Language_notes');
  $T_1000_character_max  = $T->h('1000_character_max');
  $T_Tick_if_test_unit   = $T->h('Tick_if_test_unit');
  $T_Owner               = $T->h('csCol_owner');
  $T_I_am_the_author     = $T->h('I_am_the_author');
  $T_I_agree_to_copyleft = $T->h('I_agree_to_copyleft');
  $T_I_grant_use         = $T->h('I_grant_use');
  $T_Save_unit           = $T->h('Save_unit');
  $T_Publish             = $T->h('Publish');
  $T_Parameter_p_a_dhith = $T->h('Parameter_p_a_dhith');
  $T_Warning             = $T->h('Warning');
  $T_Unit_created        = $T->h('Unit_created');
  $T_Edit_complete       = $T->h('Edit_complete');
  $T_New                 = $T->h('New');
  $T_Link                = $T->h('Link');
  $T_Link_advice         = $T->h('Link_advice');

  $T_CC_BY_message       = $T->j('CC_BY_message');
  $T_CC_SA_message       = $T->j('CC_SA_message');
  $T_CC_ND_message       = $T->j('CC_ND_message');
  $T_CC_NC_message       = $T->j('CC_NC_message');
  $T_Error_in            = $T->j('Error_in');

  $T_Clone_this_unit_title     = $T->h('Clone_this_unit_title');
  $T_Creating_Clilstore_unit_d = $T->h('Creating_Clilstore_unit_d');
  $T_Editing_Clilstore_unit_d  = $T->h('Editing_Clilstore_unit_d');
  $T_not_saved_errormessage    = $T->h('not_saved_errormessage');
  $T_scriptingMessage          = $T->h('scriptingMessage');
  $T_Err_Must_tick_permission  = $T->h('Err_Must_tick_permission');
  $T_Err_No_language_code      = $T->h('Err_No_language_code');
  $T_Err_No_title              = $T->h('Err_No_title');
  $T_Err_Title_too_short       = $T->h('Err_Title_too_short');
  $T_Err_Title_too_long        = $T->h('Err_Title_too_long');
  $T_Err_Invalid_media_length  = $T->h('Err_Invalid_media_length');
  $T_Err_CEFR_level_missing    = $T->h('Err_CEFR_level_missing');
  $T_Err_Invalid_CC_licence    = $T->h('Err_Invalid_CC_licence');
  $T_Err_Invalid_level         = $T->h('Err_Invalid_level');
  $T_Warn_No_text              = $T->h('Warn_No_text');
  $T_Warn_Hardly_any_text      = $T->h('Warn_Hardly_any_text');
  $T_Warn_New_licence_restrict = $T->h('Warn_New_licence_restrict');
  $T_No_unit_found_for_id      = $T->h('No_unit_found_for_id');
  $T_May_only_edit_own_units   = $T->h('May_only_edit_own_units');
  $T_You_have_Javascript_off   = $T->h('You_have_Javascript_off');

  $hl0 = $T->hl0();
  $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

  $T_I_am_the_author     = strtr( $T_I_am_the_author,     [ '['=>'<i>', ']'=>'</i>' ] );
  $T_I_agree_to_copyleft = strtr( $T_I_agree_to_copyleft, [ '['=>'<i>', ']'=>'</i>', '{'=>'<a href=copyleftPolicy.php>', '}'=>'</a>' ] );
  $T_I_grant_use         = strtr( $T_I_grant_use,         [ '{'=>'<a href=https://creativecommons.org/licenses/>', '}'=>'</a>' ] );
  $T_Text_Advice_new     = strtr( $T_Text_Advice_new,     [ '['=>'<b>', ']'=>'</b>' ] );

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
    //Can also deal with url’s ending in .mp4, converting them to HTML5 <video> elements, although these may not work in all browsers and platforms
    //If $medembed is not recognised as any such url, then it is returned unchanged.

      $T = new SM_T('clilstore/edit');
      $T_Listen_to_audio     = $T->h('Listen_to_audio');
      $T_View_video          = $T->h('View_video');
      $T_May_not_work_in_all = $T->h('May_not_work_in_all');
      $T_Need_to_upd_browser = $T->h('Need_to_upd_browser');
      $T_No__element_support = $T->h('No__element_support');

      $T_No_audio_el_support = sprintf($T_No__element_support,'<audio>');
      $T_No_video_el_support = sprintf($T_No__element_support,'<video>');

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
                           => "<audio controls=controls style='width:100%;max-height:70px' title='$T_Listen_to_audio (mp3 - $T_May_not_work_in_all)'> <source src='$1://$2.mp3' type='audio/mpeg'/>  <span style='color:red;font-size:90%'>[$T_No_audio_el_support - $T_Need_to_upd_browser]</span> </audio>",
          '|(.*)://(.*)\.m4a$|u'
                           => "<audio controls=controls style='width:100%;max-height:70px' title='$T_Listen_to_audio (m4a - $T_May_not_work_in_all)'> <source src='$1://$2.m4a' type='audio/m4a'/>  <span style='color:red;font-size:90%'>[$T_No_audio_el_support - $T_Need_to_upd_browser]</span> </audio>",
          '|(.*)://(.*)\.ogg$|u'
                           => "<audio controls=controls style='width:100%;max-height:70px' title='$T_Listen_to_audio (.ogg - $T_May_not_work_in_all)'> <source src='$1://$2.ogg' type='audio/ogg'/>  <span style='color:red;font-size:90%'>[$T_No_audio_el_support - $T_Need_to_upd_browser]</span> </audio>",
          '|(.*)://(.*)\.mp4$|u'
                           => "<video controls=controls style='width:480px' title='$T_View_video (.mp4 - $T_May_not_work_in_all)'> <source src='$1://$2.mp4' type='video/mp4'/>  <span style='color:red;font-size:90%'>[$T_No_video_el_support - $T_Need_to_upd_browser]</span> </video>",
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
          $this->ord  = $ord;
          $this->but  = $but;
          $this->wl   = $wl;
          $this->new  = $new;
          $this->link = trim(strip_tags($link));
      }

  }


  try {
    $myCLIL->dearbhaich();
    $user = $myCLIL->id;
    if (!isset($_REQUEST['id'])) { throw new SM_MDexception(sprintf($T_Parameter_p_a_dhith,'id')); }
    $id = $_REQUEST['id'];
    $insistOnTitleJS = ( empty($id) ? 'insistOnTitle();' : '' );

    $servername = SM_myCLIL::servername();
    $serverhome = SM_myCLIL::serverhome();

    if (@$_REQUEST['editor']=='old') { $editor = 'old'; } else { $editor = 'new'; }
    if ($editor=='new') {
        $oldEditorLink = "edit.php?id=$id&amp;editor=old";
        if (isset($_GET['view'])) { $oldEditorLink .= '&amp;view'; }
        $editorsMessage = strtr($T_editorsMessage, [ '{' => "<a href='$oldEditorLink'>", '}' => '</a>' ] );
        $editorsMessage = "<p style='margin:1em 0;color:green;font-size:80%'>$editorsMessage</p>";
//      $tinymceCSS = '/clilstore/tinymce.css?bogus=' . time();  //Bogus parameter to thwart browser cache and ensure refresh while under development
        $tinymceCSS = '/clilstore/tinymce.css';

        $hlTiny = $hl0;
        $langdirTiny = $_SERVER['DOCUMENT_ROOT'] . '/tinymce/langs/';
        $hlTinyTra = ['af'=>'af_ZA', 'azj'=>'az', 'bg'=>'bg_BG', 'bn'=>'bn_BD', 'en'=>'en_GB', 'ekk'=>'et', 'pes'=>'fa',
                     'fr'=>'fr_FR', 'he'=>'he_IL', 'hu'=>'hu_HU', 'kab'=>'kab', 'ka'=>'ka_GE', 'km'=>'km_KH', 'ko'=>'ko_KR',
                     'lvs'=>'lv', 'nb'=>'nb_NO', 'pt'=>'pt_PT', 'sv'=>'sv_SE', 'th'=>'th_TH', 'zh-Hans'=>'zh_CN', 'zh-Hant'=>'zh_TW'];
        if ( !file_exists("{$langdirTiny}{$hlTiny}.js") ) { $hlTiny = $hlTinyTra[$hlTiny] ?? $hlTiny; }
        $tinymceScript = <<<EODtinyMCE
    <script src="/tinymce/tinymce.min.js"></script>
    <script>
    tinymce.init({
        selector: "textarea#text",
        language: "$hlTiny",
        entity_encoding: "raw",
        plugins: [
             "advlist autolink link image lists charmap preview hr anchor spellchecker",
             "searchreplace wordcount visualblocks visualchars code fullscreen media nonbreaking",
             "save table directionality emoticons template paste"
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
    $errorMessage = $warningMessage = $cruth = $cloneHtml = $legend = '';
    $refreshTime = 1;
    $formRequired = 1;

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
        $medfloat = ( isset($_POST['scrollText']) ? 'scroll' : 'none' );
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
        foreach ($linkArr as $i=>$link) { if (substr($link,0,3)=='cs/') { $linkArr[$i] = "/$link"; } }
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

        if       (empty($permis))         { $errorMessage = $T_Err_Must_tick_permission; }
          elseif (scriptscan($medembed))  { $errorMessage = sprintf($T_scriptingMessage,scriptscan($medembed), 'media embed code'); }
          elseif (scriptscan($text))      { $errorMessage = sprintf($T_scriptingMessage,scriptscan($text),     'text'            ); }
          elseif (scriptscan($summary))   { $errorMessage = sprintf($T_scriptingMessage,scriptscan($summary),  'summary'         ); }
          elseif (scriptscan($langnotes)) { $errorMessage = sprintf($T_scriptingMessage,scriptscan($langnotes),'language notes'  ); }
          elseif (empty($sl))             { $errorMessage = $T_No_language_code; }
          elseif (empty($title))          { $errorMessage = $T_No_title; }
          elseif ($level==-1 && $test==0) { $errorMessage = $T_Err_CEFR_level_missing; }
          elseif (iconv_strlen($title,'UTF-8')<4)       { $errorMessage = sprintf($T_Err_Title_too_short,4);  }
          elseif (iconv_strlen($title,'UTF-8')>120)     { $errorMessage = sprintf($T_Err_Title_too_long,120); }
          elseif (SM_csSess::minsecs2secs($medlen)==-1) { $errorMessage = sprintf($T_Err_Invalid_media_length,"&lsquo;$medlen&rsquo;"); }
          elseif (!in_array($licence,['BY','BY-SA','BY-ND','BY-NC','BY-NC-SA','BY-NC-ND'])) { $errorMessage = sprintf($T_Err_invalid_CC_licence, "&ldquo;$licence&rdquo;"); }
          else {

            if (empty($text) && $test==0)                          { $warningMessage = $T_Warn_No_text; }
              elseif (iconv_strlen($text,'UTF-8')<150 && $test==0) { $warningMessage = $T_Warn_Hardly_any_text; }
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
                if (!is_numeric($levelStr)) { throw new SM_MDexception("sgrios|bog|$T_Err_Invalid_level: $levelStr"); }
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
                $happyMessage = $T_Unit_created;
                $id = current($DbMultidict->query("SELECT MAX(id) FROM clilstore")->fetch()); //The id of the newly created unit
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
                    { $warningMessage = strtr($T_Warn_New_licence_restrict, ['{%s1}'=>$licence,'{%s2}'=>$prevLicence]); }
                $query = 'UPDATE clilstore'
                        . ' SET sl=?,level=?,title=?,text=?,medembed=?,medfloat=?,medtype=?,medlen=?,words=?,summary=?,langnotes=?,changed=?,licence=?,test=?'
                        . ' WHERE id=? AND owner LIKE ?';
                $stmt = $DbMultidict->prepare($query);
                $userRequired = ( $user=='admin' ? '%' : $user);
                $result = $stmt->execute(array(
                           $sl,$level,$title,$text,$medembed,$medfloat,$medtype,$medlen,$words,$summary,$langnotes,$changed,$licence,$test,$id,$userRequired));
                $DbMultidict->prepare('DELETE FROM csButtons WHERE id=:id')->execute(array('id'=>$id)); //Delete any previous buttons
                $happyMessage = $T_Edit_complete;
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
            if ( isset($_GET['view']) ) { $happyRefresh = "$serverhome/cs/$id";     }
                                   else { $happyRefresh = "$serverhome/clilstore/"; }
            if (!empty($warningMessage)) {
                $refreshTime = 5;
                $warningMessage = "<p style='color:#d70;font-weight:bold'>$T_Warning: $warningMessage</p>";
            }
            echo <<<EOD2
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="$refreshTime; url=$happyRefresh">
    <title>Clilstore: $happyMessage</title>
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
                $legend = $T_Creating_new_unit;
               //See if the user has a default language set for new units
                $stmt = $DbMultidict->prepare('SELECT unitLang FROM users WHERE user=:user');
                $stmt->execute(array('user'=>$user));
                if ($row = $stmt->fetch()) { $sl = $row['unitLang']; }
                $stmt = null;
            } else {
             //Editing an old unit, so first fetch the values from the database
                $stmt = $DbMultidict->prepare('SELECT owner,sl,level,title,text,medembed,medfloat,medtype,medlen,summary,langnotes,licence,test FROM clilstore WHERE id=:id');
                $stmt->execute(array('id'=>$id));
                if (!$row = $stmt->fetch(PDO::FETCH_ASSOC)) { throw new SM_MDexception("$T_No_unit_found_for_id=$id"); }
                extract($row);
                if ($user<>$owner && $user<>'admin') { throw new SM_MDexception($T_May_only_edit_own_units); }

                $stmt = $DbMultidict->prepare('SELECT ord,but,wl,new,link FROM csButtons WHERE id=:id');
                $stmt->execute(array('id'=>$id));
                while ($r = $stmt->fetch(PDO::FETCH_OBJ)) { $buttons[] = new button($r->ord,$r->but,$r->wl,$r->new,$r->link); }
                $text = str_replace('&','&amp;',$text);
                if ($test<>2) { $permis = 'checked'; }
                $legend = ( $test==2 ? $T_Creating_Clilstore_unit_d : $T_Editing_Clilstore_unit_d );
                $legend = sprintf($legend,$id);
                $cruth  = 'html'; 
            }
            if (count($buttons)<8) { $buttons[] = new button(count($buttons)); } // Always create at least one blank button, up to a maximum of 8 buttons
            for ($ord=count($buttons);$ord<4;$ord++) { $buttons[] = new button($ord); } //Create new blank buttons if necessary, to give a minimum of 4 buttons
        }
        $clonech = ( empty($clone) ? '' : ' checked' );
        $brch    = ( empty($br)    ? '' : ' checked' );
        $testch  = ( (isset($test)&&$test==1)   ? ' checked' : '' );
        $permisch= ( empty($permis)? '' : ' checked' );
        $medtype0sel  = ( $medtype==0 ? ' checked' : '' );
        $medtype1sel  = ( $medtype==1 ? ' checked' : '' );
        $medtype2sel  = ( $medtype==2 ? ' checked' : '' );
        $medlenHtml = SM_csSess::secs2minsecs($medlen);  if ($medlenHtml=='?:??') { $medlenHtml = ''; }
        $T_Text_Advice_new = strtr( $T_Text_Advice_new, [ '{%s}'=> "(<input type=checkbox name=br id=br $brch> <label for=br>$T_Text_Advice_new_br</label>).&nbsp;&nbsp;&nbsp;&nbsp;" ] );

        $medembedHtml = htmlspecialchars($medembed);
        if ($id>0) { $cloneHtml = <<<END_cloneHtml
<label class=toggle-switchy for=clone data-size=xs>
  <input type=checkbox id=clone name=clone $clonech><span class=toggle><span class=switch></span></span>
  <span class=label>$T_Clone_as_a_new_unit</span>
</label>
END_cloneHtml;
        }
        if ($editor=='new') {
            $textAdvice = $T_Text_Advice;
        } else {
            $textAdvice = ( $cruth=='html'
                          ? $T_Text_Advice_html
                          : $T_Text_Advice_new );
        }
        $scrollChecked = ( $medfloat=='scroll' ? 'checked' : '' );

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

        if (empty($id)) { $submitValue = $T_Publish;   }
          else          { $submitValue = $T_Save_unit; }
        if ($errorMessage) { $errorMessage = "<div class=errorMessage>$errorMessage<br><br>$T_not_saved_errormessage</div>"; }
        $buttonsHtml = '';
        foreach ($buttons as $b) {
            $ord   = $b->ord;
            $but   = $b->but;
            $wlch  = ( $b->wl  ? 'checked' : '');
            $newch = ( $b->new ? 'checked' : '');
            $link  = $b->link;
            $buttonsHtml  .= <<<EODbutHtml
<tr>
<td><input name="but[]"  value="$but" placeholder="$T_You_can_write_here"></td>
<td><input type="checkbox" name="wl[]" $wlch value="$ord" title="$T_Whether_to_WL_link\n$T_Whether_to_WL_info"></td>
<td><input type="checkbox"  name="new[]" $newch value="$ord" title="$T_Whether_to_new_tab"></td>
<td><input name="link[]" value="$link"></td>
</tr>
EODbutHtml;
        }

        $titleSC = htmlspecialchars($title);

        $chkLic = array();
        $chkLic['BY-SA']
      = $chkLic['BY']
      = $chkLic['BY-ND']
      = $chkLic['BY-NC-SA']
      = $chkLic['BY-NC']
      = $chkLic['BY-NC-ND'] = '';
        $chkLic[$licence] = 'checked';

// Create fileInfoForm
        $stmtFiles = $DbMultidict->prepare('SELECT fileid, filename, LENGTH(bloigh) AS filesize FROM csFiles WHERE id=:id ORDER BY filename');
        $stmtFiles->execute(array('id'=>$id));
        $fileInfoArr = $stmtFiles->fetchAll(PDO::FETCH_ASSOC);
        $filesHtml = '';
        foreach ($fileInfoArr as $fileInfo) {
            extract($fileInfo);
            if ($filesize<10000) { $filesize .= ' bytes'; } else { $filesize = round($filesize/1024) . 'KB'; }
            $filesHtml .= <<<EODeditFile
<tr id="filetr-$fileid">
<td><span id="filetick-$fileid" class=change>✔<span></td>
<td><input id="filename-$fileid" value="$filename" title="filesize $filesize" onchange="changeFilename('$fileid')"></td>
<td><img src="/icons-smo/curAs.png" title="Delete this file (immediately and permanently)" onclick="deleteFile('$fileid')" alt="Delete"></td>
<td><a id="fileLink-$fileid" href="/cs/$id/$filename"><img src="/icons-smo/td.gif" title="View this file" alt="View"></a></td>
<td style="font-size:80%" id="fileLinkName-$fileid">/cs/$id/$filename</td>
</tr>
EODeditFile;
        }
        if (empty($filesHtml)) { $nofilesDisplay = 'block'; $filesDisplay = 'none';  }
          else                 { $nofilesDisplay = 'none';  $filesDisplay = 'block'; }
        $fileInfoForm = <<<EODfileInfoForm
<div id="nofilesDisplay" style="display:$nofilesDisplay">
<p style="margin:0.2em;font-size:80%">The unit currently has no attached files</p>
<p class="info" style="margin:0">You can upload files which will be attached to this unit.  Make sure that you name them with the correct filename extension: <b>.html</b> or <b>.docx</b> or <b>.pdf</b> or whatever, as appropriate to their file type.</p>
</div>
<fieldset id="filesDisplay" style="display:$filesDisplay;margin:6px;border:1px solid grey;padding:10px;border-radius:5px;background-color:#ffd">
<legend class=boldleg>Files attached to the unit</legend>
<form name="fileInfoForm">
<table id="filesAtt">
<tbody id="filesAttBody">
<tr style="font-size:85%;background-color2:#ddf">
<td></td>
<td>File <span class="info" style="font-style:italic">(you can edit its name here to change it)</span></td>
<td>Delete</td>
<td>View</td>
<td>Link address <span style="font-size:80%;font-style:italic">(relative url for use in buttons or in the body of the unit)</span></td>
</tr>
$filesHtml
</tbody>
</table>
</form>
</fieldset>
EODfileInfoForm;

        echo <<<EOD1
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>$legend</title>
    <link rel="stylesheet" href="/css/smo.css?version=rubbishToForceReload">
    <link rel="stylesheet" href="style.css?version=rubbishToForceReload">
    <link rel="stylesheet" href="/css/toggle-switchy.css?version=rubbishToForceReload">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <meta name="robots" content="noindex">
    <style>
        table#editlinkbuts { margin-bottom:0.5em; }
        table#editlinkbuts td:nth-child(1) input { width:12em; text-align:center; background-color:#bfb; color:black; 
                                                   padding:2px 4px; border:1px solid green; border-radius:6px; }
        table#editlinkbuts td:nth-child(2)       { width:1.5em; text-align:center; }
        table#editlinkbuts td:nth-child(3)       { width:1.8em; text-align:center; }
        table#editlinkbuts td:nth-child(4) input { min-width:60em; }

        table#filesAtt { margin-bottom:0.2em; border-collapse:collapse; }
        table#filesAtt td { padding:2px 4px; } 
        table#filesAtt td:nth-child(1)       { padding:2px 0; }
        table#filesAtt td:nth-child(2) input { min-width:20em; background-color:#bfe; color:black; font-size:100%;
                                                   padding:3px 6px; border:1px solid green; border-radius:4px; }
        table#filesAtt td:nth-child(3)       { width:1.8em; text-align:center; }
        table#filesAtt td:nth-child(4)       { width:1.5em; text-align:center; }
        table#filesAtt td:nth-child(5)       { padding-left:1em; color:green; }
        table#filesAtt td:nth-child(2).newFile input { background-color:#9fb; }

        legend.boldleg { font-weight:bold; color:white; background-color:grey; border:1px solid grey; }
        label.rad { border:1px solid black; padding:1px 3px; border-radius:3px; background-color:#ffd; }
        label.highlighted    { background-color:#ff4; }
        label.bithighlighted { background-color:#ff9; }
        label.midrange       { background-color:#ed0; }
        div.box { border:1px solid black; padding:4px; border-radius:4px; background-color:#ffd; }
        div.errorMessage { margin:0.5em 0; color:red; font-weight:bold; }
        table#ownertab { margin:1.5em 0 0.3em 0; border-collapse:collapse; }
        table#ownertab tr { vertical-align:top; }
        table#licTable { margin:0.5em 0 1em 4em; }
        table#licTable td { border:2px solid white; border-radius:6px; }
        table#licTable td.chk { border-color:#bb3; background-color:#ffd; }
        table#licTable img { padding:1px 25px 4px 4px; }
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
             else if (level<10) { radValue =  5; message = '$T_CEFR_A1_name';}
             else if (level<20) { radValue = 15; message = '$T_CEFR_A2_name';}
             else if (level<30) { radValue = 25; message = '$T_CEFR_B1_name';}
             else if (level<40) { radValue = 35; message = '$T_CEFR_B2_name';}
             else if (level<50) { radValue = 45; message = '$T_CEFR_C1_name';}
             else if (level<60) { radValue = 55; message = '$T_CEFR_C2_name';}
             else               { radValue = -1; message = '';}
            if (level!=-1) {
                if       (level%10 < 3) { message += ' <span style="color:#777">($T_easier_side)</span>'; }
                 else if (level%10 < 5) { message += ' <span style="color:#aaa">($T_easier_side)</span>'; }
                if       (level%10 > 7) { message += ' <span style="color:#777">($T_harder_side)</span>'; }
                 else if (level%10 > 5) { message += ' <span style="color:#aaa">($T_harder_side)</span>'; }
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
            licNameLink = '<a href="//creativecommons.org/licenses/' + licence.toLowerCase() + '/4.0/">' + licName + '</a>';
            var message = { 'BY':'$T_CC_BY_message',
                            'SA':'$T_CC_SA_message',
                            'ND':'$T_CC_ND_message',
                            'NC':'$T_CC_NC_message' }; 
            if (licence=='BY-NC-SA') { message['SA'] = message['SA'].replace('BY-SA','BY-NC-SA'); }
            bits = licence.split('-');
            var lastbit = bits[bits.length-1];
            var re = /([^\{]*)\{([^\}]*)\}(.*)/;
            if (lastbit=='ND') { message['BY'] = message['BY'].replace( re, '$1$3'   ); }
              else             { message['BY'] = message['BY'].replace( re, '$1$2$3' ); }
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

        function insistOnTitle() {
            el = document.getElementById('title');
            el.onblur =
                function() {
                    var title = el.value;
                    if (title.length<4) {
                        setTimeout( function(){el.focus();} ,0);
                    } else {
                        var xmlhttp = new XMLHttpRequest();
                        xmlhttp.onload = function() {
                            var resp = this.responseText;
                            if (this.status!=200 || resp.substring(0,8)!='Created:') { 
                                alert('$T_Error_in createUnit:'+this.status+' '+this.responseText); return;
                            } else {
                                var newunit = resp.substring(8);
                                window.location.href = '$serverhome/clilstore/edit.php?id=' + newunit;
                            }
                        }
                        xmlhttp.open('GET', 'ajax/createUnit.php?title=' + title);
                        xmlhttp.send();
                    }
                }
        }

        function banChars(oldname) {
            //Returns a new filename, with all non-advisable characters in oldname converted to to _
            //after issuing an alert
            var newnameArr = oldname.split('');
            var bannedChars = '#%&{}\\\\<>*? $!\\'":@+`|='.split('');
            for (var i=0;i<newnameArr.length;i++) {
                for (var j=0;j<bannedChars.length;j++) {
                    if (newnameArr[i]==bannedChars[j]) { newnameArr[i] = '_'; } //Covert banned characters to _
                }
            }
            var newname = newnameArr.join('');
            if (newname!=oldname) {
                alert('Non-advisable characters in the filename have been replaced by _'
                    + '\\n\\n' + oldname + ' →\\n' + newname);
            }
            return newname;
        }

        function makeNiceFilename(value) {
            var nicename = value.split("\\\\").pop().split("/").pop();
            var el = document.getElementById('filenameUpload');
            el.value = banChars(nicename);
            document.getElementById('nicenameDiv').style.display = 'block';
        }

        function changeFilename(fileid) {
            var filenameEl = document.getElementById('filename-'+fileid);
            var newname = banChars(filenameEl.value);
            filenameEl.value = newname;
            var fileLinkName = '/cs/$id/'+newname;
            document.getElementById('fileLink-'+fileid).href = fileLinkName;
            document.getElementById('fileLinkName-'+fileid).innerHTML = fileLinkName;

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onload = function() {
                if (this.status!=200 || this.responseText!='OK') {
                    alert('$T_Error_in changeFilename:'+this.status+' '+this.responseText); return;
                } else {
                    var tickel = document.getElementById('filetick-'+fileid);
                    tickel.classList.remove('changed'); //Remove the class (if required) and add again after a tiny delay, to restart the animation
                    setTimeout(function(){tickel.classList.add('changed');},50);
                }
            }
            xmlhttp.open('GET', 'ajax/changeFile.php?fileid=' + fileid + '&filename=' + newname);
            xmlhttp.send();
        }

        function deleteFile(fileid) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onload = function() {
                if (this.status!=200 || this.responseText!='OK') { 
                    alert('$T_Error_in deleteFile:'+this.status+' '+this.responseText); return;
                } else {
                    var filetr = document.getElementById('filetr-'+fileid);
                    var parent = filetr.parentNode;
                    parent.removeChild(filetr);
                    if (parent.childElementCount==1) {
                        document.getElementById('nofilesDisplay').style.display = 'block';
                        document.getElementById('filesDisplay').style.display   = 'none';
                    }
                }
            }
            xmlhttp.open('GET', 'ajax/changeFile.php?fileid=' + fileid + '&delete=');
            xmlhttp.send();
        }

        function uploadOnsubmit(event) {
            event.preventDefault();
            var uploadForm = document.getElementById('uploadForm');
            var file = document.getElementById('bloigh').files[0]; //If multiple files selected, retain only the first
            if (typeof(file)=='undefined') { alert('You need to select a file first'); return; }
            if (file.size>3000000) { alert('File size exceeds Clilstore’s 3MB upload limit'); return; }
            if (file.size>1000000) { alert('This file is very big'); }
            uploadStatus.innerHTML = 'Uploading...';
            var formData = new FormData();
            filenameUpload = document.getElementById('filenameUpload').value; 
            formData.append('bloigh', file, filenameUpload);
            formData.append('id','$id');
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'ajax/uploadHandling.php');
            xhr.onload = function () {
                if (this.status!=200 || this.responseText.substring(0,3)!='OK-') { 
                    uploadStatus.innerHTML = 'Upload error. Try again.';
                    alert('$T_Error_in uploadOnsubmit:'+this.status+'\\n\\n'+this.responseText+'\\n\\n'); return;
                } else {
                    uploadStatus.innerHTML = '';
                    var fileid = this.responseText.substring(3);
                    document.getElementById('nofilesDisplay').style.display = 'none';
                    document.getElementById('filesDisplay').style.display   = 'block';
                    var trNew = document.createElement('tr');
                    trNew.id = 'filetr-' + fileid;
                    var inner =
'<td><span id="filetick-_fileid_" class=change>✔<span></td>' +
'<td class=newFile><input id="filename-_fileid_" value="_filename_" title="filesize '+file.size+'" onchange=changeFilename("_fileid_")></td>' +
'<td><img src="/icons-smo/curAs.png" title="Delete this file (immediately and permanently)" onclick=deleteFile("_fileid_") alt="Delete"></td>' +
'<td><a id="fileLink-_fileid_" href="/cs/$id/_filename_"><img src="/icons-smo/td.gif" title="View this file" alt="View"></a></td>' +
'<td style="font-size:80%" id="fileLinkName-_fileid_">/cs/$id/_filename_</td>';
                    inner = inner.replace(/_filename_/g,filenameUpload);
                    inner = inner.replace(/_fileid_/g,fileid);
                    var filesTableBody = document.getElementById('filesAttBody');
                    trNew.innerHTML = inner;
                    filesTableBody.appendChild(trNew);
                    uploadForm.reset();
                    document.getElementById('nicenameDiv').style.display = 'none';
                }
            };
            xhr.send(formData);
        }

/* example - Sgudal - delete
    // Check the file type
    if (!file.type.match('image.*')) {
        statusP.innerHTML = 'The file selected is not an image.';
        return;
    }
*/

    </script>

$tinymceScript

</head>
<body onload="setLevel($level); medlenDisp($medtype); licenceChange('$licence'); permisChange(); $insistOnTitleJS">

$mdNavbar
<div class="smo-body-indent">
$errorMessage
$editorsMessage

<form method="post" name="mainForm">

<fieldset style="background-color:#eef;border:8px solid #55a8eb;border-radius:10px">
<legend style="width:auto;margin-left:auto;margin-right:auto;background-color:#55a8eb;color:white;padding:1px 3em">$legend</legend>
<div style="float:right;padding:3px;font-size:75%;color#333" title="$T_Clone_this_unit_title">
$cloneHtml</div>
<div>$T_Title<br>
<input id=title name="title" value="$titleSC" autofocus "required pattern=".{4,120}" title="$T_Title_info_4_120" style="width:99%"></div>
<div style="margin-top:6px">$T_Embed_code_legend
<input name="medembed" value="$medembedHtml" style="width:99%"><br>
<label class=toggle-switchy for=scrollText data-size=xs>
  <input type=checkbox id=scrollText name=scrollText value=scroll $scrollChecked><span class=toggle><span class=switch></span></span>
  <span class=label>$T_Scroll_text</span>
</label>
</div>
<div style="margin-top:6px">
$T_Text <span class="info" style="padding-left:2em">$textAdvice</span><br>
<textarea name="text" id="text" placeholder="$T_Text_placeholder" style="width:100%;height:400px">$text</textarea></div>
<fieldset style="margin:6px 0 0 0;border:1px solid green;padding:5px;corner-radius:5px">
<legend>$T_Link_buttons</legend>
<table id="editlinkbuts">
<tr style="font-size:85%">
<td style="text-align:center">$T_Button_text</td>
<td title="$T_Whether_to_WL_link">WL</td>
<td title="$T_Whether_to_new_tab">$T_New</td>
<td>$T_Link <span class="info">($T_Link_advice)</span></td>
</tr>
$buttonsHtml
</table>
</fieldset>
</fieldset>

<div style="margin-top:5px">
$T_Language
<select name="sl" required>
$slOptionHtml
</select>
</div>

<div style="margin:12px 0;padding:4px;border:0">
$T_Learner_level
(<a href="//en.wikipedia.org/wiki/Common_European_Framework_of_Reference_for_Languages#Common_reference_levels" title="$T_CEFR_longname">$T_CEFR</a>)&nbsp;

<label for="un" class="rad">
 <input type="radio" name="cefr" value="-1" id="un" onclick="radClick(-1);"><span style="color:grey;font-size:75%;vertical-align:middle">$T_Unspecified</span></label>&nbsp;
<label for="a1" class="rad" title=" $T_CEFR_A1_description">
 <input type="radio" name="cefr" value="5"  id="a1" onclick="radClick(5); ">A1</label>
<label for="a2" class="rad" title=" $T_CEFR_A2_description">
 <input type="radio" name="cefr" value="15" id="a2" onclick="radClick(15);">A2</label>
<label for="b1" class="rad" title=" $T_CEFR_B1_description">
 <input type="radio" name="cefr" value="25" id="b1" onclick="radClick(25);">B1</label>
<label for="b2" class="rad" title=" $T_CEFR_B2_description">
 <input type="radio" name="cefr" value="35" id="b2" onclick="radClick(35);">B2</label>
<label for="c1" class="rad" title=" $T_CEFR_C1_description">
 <input type="radio" name="cefr" value="45" id="c1" onclick="radClick(45);">C1</label>
<label for="c2" class="rad" title=" $T_CEFR_C2_description">
 <input type="radio" name="cefr" value="55" id="c2" onclick="radClick(55);">C2</label>

<input name="level" id="level" type="range" min=-1 max=59 value=$level style="width:17em;color:#aaa" title="range 0-59" oninput="setLevel(value);" onchange="setLevel(value);">
<output id="levnum" style="font-size:80%;color:#ccc;display:inline-block;width:20px;text-align:right"></output>
<output id="cefrmessage"><span style="color:red;font-size:80%">$T_Warning: $T_You_have_Javascript_off</span></output><br>
<span class="info" style="padding-left:20em">$T_Choose_level_info</span>
</div>

<div style="margin-top:6px">
$T_Media_type:<br>
&nbsp;<input type="radio" name="medtype"$medtype2sel value="2" id="medtype2" onclick="medlenDisp(2)"><label for="medtype2">$T_video</label><br>
&nbsp;<input type="radio" name="medtype"$medtype1sel value="1" id="medtype1" onclick="medlenDisp(1)"><label for="medtype1">$T_sound_only</label><br>
&nbsp;<input type="radio" name="medtype"$medtype0sel value="0" id="medtype0" onclick="medlenDisp(0)"><label for="medtype0">$T_neither</label><br>
<span id="medlen">$T_Media_length: <input name="medlen" value="$medlenHtml" style="width:4em;text-align:right" placeholder="?:??" title="$T_Media_length_title">
<span class="info">$T_eg 80, 80s, 1:20</span></span>&nbsp;
</div>

<div style="margin-top:8px">
$T_Summary: <span class="info">($T_1000_character_max)</span>
<textarea name="summary" style="width:99%;height:2.5em;margin:2px;padding:0.4em;border:1px solid;border-radius:0.4em">$summary</textarea>
</div>

<div style="margin-top:6px">
$T_Language_notes: <span class="info">($T_1000_character_max)</span>
<textarea name="langnotes" style="width:99%;height:2.5em;margin:2px;padding:0.4em;border:1px solid;border-radius:0.4em">$langnotes</textarea>
</div>

<label class=toggle-switchy for=test data-size=xs>
  <input type=checkbox id=test name=test $testch><span class=toggle><span class=switch></span></span>
  <span class=label>$T_Tick_if_test_unit</span>
</label>

<!--
<table id=ownertab><tr>
<td>$T_Owner: <span style="font-size:120%;text-decoration:underline;padding-right:0.35em">$owner</span></td>
<td><input  type="checkbox" name="permis" id="permis" required $permisch onChange="permisChange();"></td>
<td><label for="permis">$T_I_am_the_author<br>$T_I_agree_to_copyleft</td>
</tr></table>
-->

<div style="margin:1em 0 0.4em 0">
$T_Owner: <span style="font-size:120%;text-decoration:underline;padding-right:0.35em">$owner</span>
<label class=toggle-switchy for=permis data-size=xs>
  <input type=checkbox id=permis name=permis $permisch required onChange="permisChange()"><span class=toggle><span class=switch></span></span>
  <span class=label>$T_I_am_the_author<br>$T_I_agree_to_copyleft</span>
</label>
</div>

<div style="margin-left2:5em" id="ccdiv">
$T_I_grant_use
<table id=licTable>
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

<div style="margin-top:8px;margin-bottom:2em">
<input type="submit" name="save" id="save" value="$submitValue">
</div>

<input type="hidden" name="owner" value="$owner">
</form>

<div style="margin-top:4em;border-top:0.25em solid #5ae;padding:1px;background-color2:#9cf">
$fileInfoForm

<fieldset style="margin:6px;border:1px solid grey;padding:10px;border-radius:5px;background-color:#ffd"> 
<legend class=boldleg>Upload a file</legend>
<form id="uploadForm" action="ajax/uploadHandling.php" method="post" onsubmit="uploadOnsubmit(event)">
<div>
    <label for="bloigh">Choose a file on your computer:</label>
    <input type="file" name="bloigh" id="bloigh" style="width:16em" onchange="makeNiceFilename(this.value)">
</div>
<div style="display:none" id="nicenameDiv">
    <label for="filenameUpload">and the name it will have in Clilstore</legend>
    <input id="filenameUpload" style="width:16em"> <span class="info">You can change this, but the name should be something sensible for a computer file<br>&nbsp;</span>
    <input type="submit" value="Upload">
    <span class="info">Remember that you must not upload copyrighted material</span>
</div>
<p id="uploadStatus"></p>
</form>
</fieldset>
</div>

</div>
$mdNavbar

</body>
</html>
EOD1;

    }

  } catch (Exception $e) { echo $e; }

?>
