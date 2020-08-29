<?php if (!include('autoload.inc.php'))
  header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header('Cache-Control: no-cache, no-store, must-revalidate');
  header("Cache-Control:max-age=0");

  try {
    $T = new SM_T('clilstore/index');

    $T_First_visit_to_CS      = $T->h('First_visit_to_CS');
    $T_CS_needs_cookies       = $T->h('CS_needs_cookies');
    $T_Got_it                 = $T->h('Got_it');
    $T_If_message_persists    = $T->h('If_message_persists');
    $T_privacy_policy         = $T->h('privacy_policy');
    $T_CS_is_well_behaved     = $T->h('CS_is_well_behaved');
    $T_setNewbieAlert         = $T->j('setNewbieAlert');

    $T_Teaching_units         = $T->h('Teaching_units');
    $T_for_CLIL               = $T->h('for_CLIL');
    $T_Help                   = $T->h('Cobhair');
    $T_About_Clilstore        = $T->h('mu_Clilstore');
    $T_Select_lang_level      = $T->h('Select_lang_level');
    $T_My_options             = $T->h('My_options');
    $T_My_units               = $T->h('My_units');
    $T_My_vocabulary          = $T->h('My_vocabulary');
    $T_Add_a_column_info      = $T->h('Add_a_column_info');
    $T_See_as_newbie          = $T->h('See_as_newbie');
    $T_Create_a_unit          = $T->h('Create_a_unit') . '…';
    $T_For_students           = $T->h('For_students');
    $T_For_teachers           = $T->h('For_teachers');
    $T_More_options           = $T->h('More_options');
    $T_more_options           = $T->h('more_options');
    $T_For_students_info      = $T->h('For_students_info');
    $T_For_students_more_info = $T->h('For_students_more_info');
    $T_For_teachers_info      = $T->h('For_teachers_info');
    $T_For_teachers_more_info = $T->h('For_teachers_more_info');
    $T_Include_test_units     = $T->h('Include_test_units');
    $T_Include_test_units_o   = $T->h('Include_test_units_o');
    $T_Login                  = $T->h('Log_air');
    $T_Logout                 = $T->h('Logout');
    $T_Logout_from_Clilstore  = $T->h('Logout_from_Clilstore');
    $T_Logged_in_as           = $T->h('Logged_in_as');
    $T_Disclaimer             = $T->h('Disclaimer');
    $T_Disclaimer_EuropeanCom = $T->h('Disclaimer_EuropeanCom');
    $T_or                     = $T->h('or');
    $T_register               = $T->h('register');
    $loginReasonStudent       = $T->h('loginReasonStudent');
    $loginReasonTeacher       = $T->h('loginReasonTeacher');
    $T_Lorg                   = $T->h('Lorg');

    $T_UnitID                 = $T->h('csCol_id');
    $T_Views                  = $T->h('csCol_views');
    $T_Clicks                 = $T->h('csCol_clicks');
    $T_Likes                  = $T->h('csCol_likes');
    $T_Created                = $T->h('csCol_created');
    $T_Changed                = $T->h('csCol_changed');
    $T_Licence                = $T->h('csCol_licence');
    $T_Owner                  = $T->h('csCol_owner');
    $T_Language               = $T->h('Language');
    $T_Level                  = $T->h('csCol_level');
    $T_Words                  = $T->h('csCol_words');
    $T_Media                  = $T->h('csCol_medtype');
    $T_MedLength              = $T->h('csCol_medlen');
    $T_Buttons                = $T->h('csCol_buttons');
    $T_Files                  = $T->h('csCol_files');
    $T_Title                  = $T->h('Title');
    $T_TextOrSummary          = $T->h('csCol_text');

    $T_UnitID_title           = $T->h('UnitID_title');
    $T_Views_title            = $T->h('Views_title');
    $T_Clicks_title           = $T->h('Clicks_title');
    $T_Created_title          = $T->h('Created_title');
    $T_Changed_title          = $T->h('Changed_title');
    $T_Licence_title          = $T->h('Licence_title');
    $T_Owner_title            = $T->h('Owner_title');
    $T_by_language_code       = $T->h('by_language_code');
    $T_Level_title            = $T->h('Level_title');
    $T_Words_title            = $T->h('Words_title');
    $T_Media_title            = $T->h('Media_title');
    $T_MedLength_title        = $T->h('MedLength_title');
    $T_Buttons_title          = $T->h('Buttons_title');
    $T_Files_title            = $T->h('Files_title');

    $T_minimum_views          = $T->h('minimum_views');
    $T_maximum_views          = $T->h('maximum_views');
    $T_minimum_clicks         = $T->h('minimum_clicks');
    $T_maximum_clicks         = $T->h('maximum_clicks');
    $T_minimum_likes          = $T->h('minimum_likes');
    $T_maximum_likes          = $T->h('maximum_likes');
    $T_start_date             = $T->h('start_date');
    $T_end_date               = $T->h('end_date');
    $T_minimum_CEFR_level     = $T->h('minimum_CEFR_level');
    $T_maximum_CEFR_level     = $T->h('maximum_CEFR_level');
    $T_minimum_words          = $T->h('minimum_words');
    $T_maximum_words          = $T->h('maximum_words');
    $T_media_field_title      = $T->h('media_field_title');
    $T_minimum_media_length   = $T->h('minimum_media_length');
    $T_maximum_media_length   = $T->h('maximum_media_length');
    $T_maximum_buttons        = $T->h('maximum_buttons');
    $T_minimum_buttons        = $T->h('minimum_buttons');
    $T_maximum_files          = $T->h('maximum_files');
    $T_minimum_files          = $T->h('minimum_files');
    $T_title_title            = $T->h('title_title');
    $T_text_title             = $T->h('text_title');

    $T_Click_to_sort          = $T->h('Click_to_sort');
    $T_Click_to_sort_hide     = $T->h('Click_to_sort_hide');
    $T_Sort_the_column        = $T->h('Sort_the_column');
    $T_Hide_the_column        = $T->h('Hide_the_column');
    $T_Restore                = $T->h('Restore');
    $T_Restore_title          = $T->h('Restore_title');
    $T_min                    = $T->h('min');
    $T_max                    = $T->h('max');
    $T_part_placeholder       = $T->h('part_placeholder');
    $T_or_wildcard_pattern    = $T->h('or_wildcard_pattern');
    $T_Clear_filter           = $T->h('Clear_filter');
    $T_Clear_filter_title     = $T->h('Clear_filter_title');
    $T_units_found            = $T->h('units_found');
    $T_Total                  = $T->h('Iomlan');
    $T_Average                = $T->h('Average');
    $T_First_choose_language  = $T->h('First_choose_language');
    $T_No_units_match_filter  = $T->h('No_units_match_filter');
    $T_inc_d_attached_files   = $T->h('inc_d_attached_files');
    $T_incUnitMessage         = $T->h('incUnitMessage');
    $T_hours                  = $T->h('hours');
    $T_minutes                = $T->h('minutes');
    $T_seconds                = $T->h('seconds');
    $T_Delete_this_unit       = $T->h('Delete_this_unit');
    $T_Edit_this_unit         = $T->h('Edit_this_unit');
    $T_Show_all               = $T->h('Seall_na_h_uile');
    $T_only_first_n_are_shown = $T->h('only_first_n_are_shown');

    $T_If_message_persists = sprintf($T_If_message_persists,$T_Got_it);
    $T_privacy_policy      = "<a href='privacyPolicy.php'>$T_privacy_policy</a>";
    $T_CS_is_well_behaved  = sprintf($T_CS_is_well_behaved,$T_privacy_policy);

    $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

    $dataLists = $tableHtml = $cookieMessage = $avgRow = $totRow = '';
    $limitUnits = $limitInfo = FALSE;

    if (!isset($_COOKIE['csSessionId'])) $cookieMessage = <<<EOD_cookieMessage
<div id=cookieMessage>
<p>$T_First_visit_to_CS</p>
<p>$T_CS_needs_cookies &nbsp;
<a id=gotitButton style='float:none' onclick=location.reload()>$T_Got_it</a></p>
<p style='font-size:80%;margin-top:2em'>$T_If_message_persists</p>
<p style='font-size:80%'>$T_CS_is_well_behaved</p>
</div>
EOD_cookieMessage;

    $myCLIL = SM_myCLIL::singleton();
    $user = ( isset($myCLIL->id) ? $myCLIL->id : '' );
    $csSess   = SM_csSess::singleton();
    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $servername = SM_myCLIL::servername();
    $serverhome = SM_myCLIL::serverhome();

    $EUlogo = '/EUlogos/' . SM_T::hl0() . '.jpg';
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $EUlogo)) { $EUlogo = '/EUlogos/en.jpg'; }

    if (isset($_GET['mode']))         { $csSess->setMode($_GET['mode']            ); }
    if (!empty($_GET['sortCol']))     { $csSess->sortCol($_GET['sortCol']         ); }
//    if (!empty($_GET['deleteCol']))   { $csSess->deleteCol($_GET['deleteCol']     ); }
    if (!empty($_GET['addCol']))      { $csSess->addCol($_GET['addCol']           ); }
    if (!empty($_GET['restoreCols'])) { $csSess->restoreCols($_GET['restoreCols'] ); }

    $mode    = $csSess->getCsSession()->mode;
$clearFilter = $_REQUEST['clearFilter'] ?? 'notset';
error_log("\$clearFilter=$clearFilter");
    $filterForm = ( isset($_REQUEST['filterForm']) ? 1 : 0 );
    if ($filterForm && $mode>1) {
       if (isset($_REQUEST['incTest'])) { $csSess->setIncTest(1); } else { $csSess->setIncTest(0); }
       if (isset($_REQUEST['wide']))    { $csSess->setMode(3);    } else { $csSess->setMode(2);    }
    }

    $mode    = $csSess->getCsSession()->mode;
    
    $incTest = $csSess->getCsSession()->incTest;
    $mode0selected = ( $mode==0 ? 'selected=selected' : '');
    $mode1selected = ( $mode==1 ? 'selected=selected' : '');
    $mode2selected = ( $mode==2 ? 'selected=selected' : '');
    $mode3selected = ( $mode==3 ? 'selected=selected' : '');
    $addColHtml = $csSess->addColHtml();

    $modecol = "m$mode";
    $pri = $symbol = array();
    foreach ($csSess->csFilter as $r) {
        $fd  = $r['fd'];
        $pr  = $r['sortpri'];
        if ($r[$modecol]<>0) {
        if ($pr<>0) { $pri[$fd] = $pr; }
            if ($r['sortord']==1) { $symbol[$fd] = '▵'; }
              else                { $symbol[$fd] = '▿'; }
        }
    }
    $ranks = array_flip($pri);
    ksort($ranks);
    $prifd  = array_shift($ranks);  //The sort field
    $prifd2  = array_shift($ranks); //The secondary sort field
    if (!empty($prifd))  { if ($symbol[$prifd] =='▵') {$symbol[$prifd]  = '▲';} else {$symbol[$prifd]  = '▼';}  }
    if (!empty($prifd2)) { if ($symbol[$prifd2]=='▵') {$symbol[$prifd2] = '▴';} else {$symbol[$prifd2] = '▾';}  }
    $hrefSelf = $_SERVER['PHP_SELF'];
    if (substr($hrefSelf,-9)=='index.php') { $hrefSelf = substr($hrefSelf,0,strlen($hrefSelf)-9); }
    foreach ($symbol as $fd=>$sym) {
        if ($fd<>'title') {
            $pad = ( $fd=='id' ? '' : '&nbsp;' );
            $deleteHtml = "<a href='$hrefSelf?deleteCol=$fd' style='color:red;font-size:80%' title='$T_Hide_the_column'>×</a>";
            $symbolHtml[$fd] = "<td><a href='$hrefSelf?sortCol=$fd' title='$T_Sort_the_column'>$sym$pad</a> $deleteHtml</td>\n";
        } else {
            $symbolinfo = '<span style="font-size:65%">⇐ '
                         . sprintf($T_Click_to_sort_hide, '<span style="color:#55a8eb;font-size:140%">▵</span>', '<span style="color:red;font-size:140%">×</span>')
                         . " [<a href='$hrefSelf?restoreCols=restore' title='$T_Restore_title' style='font-size:110%'>$T_Restore</a>]</span>";
            $symbolHtml['title'] = "<td colspan=2 style='vertical-align:bottom'><div style='float:right'>$symbolinfo</div><a href='$hrefSelf?sortCol=title' title='$T_Sort_the_column'>$sym</a></td>\n";
        }
    }

    $tabletopChoices = '';
    $photo = ( $mode<2 ? 'StudentsBoard.png' : 'TeacherBoard.png' );
    $newMode = ($mode+2)%4;
    $photo = "<a href='/clilstore/?mode=$newMode'><img src='/clilstore/$photo' style='float:left;padding-left:20px' alt=''></a>";

    if ($mode<=1) { $checkboxesHtml = "<span style='color:green;font-size:80%'>$T_Add_a_column_info</span>"; } else {
        $incTestLabel = ( empty($user)
                        ? "$T_Include_test_units"
                        : "$T_Include_test_units_o" );
        $incTestChecked = ( $incTest ? 'checked' : '' );
        $wideChecked    = ( $mode==3 ? 'checked' : '' );
        $checkboxesHtml = <<<CHECKBOXES
<label class=toggle-switchy for=incTest data-size=xs style="padding-right:2em" onclick="submitFForm()">
  <input type=checkbox id=incTest name=incTest form="filterForm" $incTestChecked>
  <span class=toggle><span class=switch></span></span>
  <span class=label>$incTestLabel</span>
</label>
<label class=toggle-switchy for=wide data-size=xs title="include more columns" onclick="submitFForm()">
  <input type=checkbox name=wide id=wide form=filterForm $wideChecked>
  <span class=toggle><span class=switch></span></span>
  <span class=label>$T_More_options</span>
</label>
CHECKBOXES;
    }
    $checkboxesHtml = <<<CHECKBOXES2
<div style="color:grey;font-size:90%;margin-left:0.6em">
$addColHtml &nbsp
$checkboxesHtml
</div>
CHECKBOXES2;

    if (empty($user)) {
        if ($mode<=1) { $loginReason = $loginReasonStudent; }
          else        { $loginReason = $loginReasonTeacher; }
        $userHtml = <<<END_USER1
<p style="clear:both;padding:1em 0"><a href="login.php" class=mybutton>$T_Login</a> $T_or <a href="register.php">$T_register</a> $loginReason.</p>
END_USER1;
    } else {
        $stmtIncUnit = $DbMultidict->prepare('SELECT id AS incUnit, created AS incCreated FROM clilstore WHERE test=2 and owner=:user'); //Check whether the user has any incomplete units
        $stmtIncUnit->execute([':user'=>$user]);
        $row = $stmtIncUnit->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            extract($row);
            $secondsToLive = $incCreated + 86400 - time(); //Delete automatically after 1 day
            if ($secondsToLive<=0) {
                header("Location:$serverhome/clilstore/delete.php?id=$incUnit&delete");
            } elseif ($secondsToLive<100) {
                $deleteTimeMess = "$secondsToLive $T_seconds";
            } else {
                $minutesToLive = round($secondsToLive/60);
                if ($minutesToLive<100) {
                    $deleteTimeMess = "$minutesToLive $T_minutes";
                } else {
                    $hoursToLive = round($minutesToLive/60);
                    $deleteTimeMess = "$hoursToLive $T_hours";
                }
            }
            $stmtIncUnitFcount = $DbMultidict->prepare('SELECT COUNT(1) As cnt FROM csFiles WHERE id=:id');
            $stmtIncUnitFcount->execute([':id'=>$incUnit]);
            $incUnitFcount = $stmtIncUnitFcount->fetchColumn();
            $fcountMessage = ( $incUnitFcount ? ' <i>('.sprintf($T_inc_d_attached_files,$incUnitFcount).')</i>' : '' );
            $incUnitMessage = sprintf($T_incUnitMessage,$fcountMessage,$deleteTimeMess);
            $incUnitMessage = strtr($incUnitMessage,
                                     [ '{' => "<a href='edit.php?id=$incUnit' class=mybutton>",
                                       '}' => '</a>',
                                       '[' => "<a href='delete.php?id=$incUnit' class=mybutton>",
                                       ']' => '</a>',
                                       '&lt;br&gt;' => '<br>'
                                     ]);
            $incUnitMessage = "<p style='margin:0;padding:0.5em;background-color:red;color:white'>$incUnitMessage</p>";
                                    
            $createButton = '';
        } else {
            $incUnitMessage = '';
            $createButton = "<a href='edit.php?id=0' class=mybutton style='margin-right:2px'>$T_Create_a_unit</a>";
        }
        if ($mode<=1) { $mybuttons = <<<END_MYBUTTONSstud
<a href="voc.php?user=$user" class="mybutton" style="margin-left:0;margin-right:1px">$T_My_vocabulary</a>
END_MYBUTTONSstud;
        } else { $mybuttons = <<<END_MYBUTTONSteach
<a href="./?owner=$user" class="mybutton" style="margin-left:0;margin-right:1px">$T_My_units</a>
$createButton
END_MYBUTTONSteach;
        }
        $userHtml = <<<END_USER2
<div style="clear:both;float:left;margin:0.5em 0 1.8em 0;padding:2px 4px;background-color:#def">
<p style="margin:2px 3px">$T_Logged_in_as <b>$user</b>
<a href="logout.php" title="$T_Logout_from_Clilstore" class="mybutton" style="margin-right:5px">$T_Logout <img src="/icons-smo/logout.png" alt=""></a>
<a href="options.php?user=$user" title="Change your Clilstore options or password" class="mybutton" style="margin-right:2.5em">$T_My_options</a>
$mybuttons
</p>
$incUnitMessage
</div><br style="clear:both">
END_USER2;
    }
    if ($mode<=1) { $userHtml .= "<p style='clear:both'>$T_Select_lang_level</p>"; }

    function wildChars (&$s,&$sVis,$con) {
     //Standardises wildcard characters to SQL format (% and _), removing duplicates
     //Adds a wildcard at the end if $con='starts', and at the start if $con='contains'
     //Sets the visible parameter $sVis appropriately (using * and ? instead of % and _)
        if (empty($s)) { $sVis = ''; return; }
        if ($con=='starts')   { $s =       $s . '%'; }
        if ($con=='contains') { $s = '%' . $s . '%'; }
        $s    = strtr( $s, array('*'=>'%','?'=>'_') );
        $s    = strtr( $s, array('%_'=>'_%')        );
        $s    = strtr( $s, array('%%'=>'%')         );
        $s    = strtr( $s, array('%%'=>'%')         );
        $sVis = strtr( $s, array('%'=>'*','_'=>'?') );
        if ($con=='starts'
         || $con=='contains') { $sVis = substr($sVis,0,-1); }
        if ($con=='contains') { $sVis = substr($sVis,1);    }
    }

    $stmt = $DbMultidict->prepare('SELECT DISTINCT owner FROM clilstore ORDER BY owner');
    $stmt->execute();
    $ownerList = $stmt->fetchAll(PDO::FETCH_COLUMN,0);
    foreach ($ownerList as &$owner) { $owner = "<option value=\"$owner\">"; }
    $ownerListHtml = implode("\n",$ownerList);

    $f['idFil']      =
    $f['viewsMin']   =
    $f['viewsMax']   =
    $f['clicksMin']  =
    $f['clicksMax']  =
    $f['likesMin']   =
    $f['likesMax']   =
    $f['createdMin'] =
    $f['createdMax'] =
    $f['changedMin'] =
    $f['changedMax'] =
    $f['licenceFil'] =
    $f['ownerFil']   =
    $f['slFil']      =
    $f['levelMin']   =
    $f['levelMax']   =
    $f['wordsMin']   =
    $f['wordsMax']   =
    $f['medtypeFil'] =
    $f['medlenMin']  =
    $f['medlenMax']  =
    $f['buttonsMin']  =
    $f['buttonsMax']  =
    $f['filesMin']  =
    $f['filesMax']  =
    $f['titleFil']   =
    $f['textFil']    = '';

    if (empty($_GET)) { $csSess->getFilter($f); }  //if we have no GET parameters, restore stored filter values
    elseif ( count($_GET)==1                       //and do the same if we have just a single GET parameter consisting of one of the commands mode..levelBut
          && in_array( array_keys($_GET)[0], array('mode','sortCol','deleteCol','addCol','restoreCols','levelBut'))
           ) { $csSess->getFilter($f); }

    if (isset($_REQUEST['id']))         { $f['idFil']      = $_REQUEST['id'];         }
    if (isset($_REQUEST['viewsMin']))   { $f['viewsMin']   = $_REQUEST['viewsMin'];   }
    if (isset($_REQUEST['viewsMax']))   { $f['viewsMax']   = $_REQUEST['viewsMax'];   }
    if (isset($_REQUEST['clicksMin']))  { $f['clicksMin']  = $_REQUEST['clicksMin'];  }
    if (isset($_REQUEST['clicksMax']))  { $f['clicksMax']  = $_REQUEST['clicksMax'];  }
    if (isset($_REQUEST['likesMin']))   { $f['likesMin']   = $_REQUEST['likesMin'];  }
    if (isset($_REQUEST['likesMax']))   { $f['likesMax']   = $_REQUEST['likesMax'];  }
    if (isset($_REQUEST['createdMin'])) { $f['createdMin'] = $_REQUEST['createdMin']; }
    if (isset($_REQUEST['createdMax'])) { $f['createdMax'] = $_REQUEST['createdMax']; }
    if (isset($_REQUEST['changedMin'])) { $f['changedMin'] = $_REQUEST['changedMin']; }
    if (isset($_REQUEST['changedMax'])) { $f['changedMax'] = $_REQUEST['changedMax']; }
    if (isset($_REQUEST['licence']))    { $f['licenceFil'] = $_REQUEST['licence'];    }
    if (isset($_REQUEST['owner']))      { $f['ownerFil']   = $_REQUEST['owner'];      }
    if (isset($_REQUEST['sl']))         { $f['slFil']      = $_REQUEST['sl'];         }
    if (isset($_REQUEST['levelMin']))   { $f['levelMin']   = $_REQUEST['levelMin'];   }
    if (isset($_REQUEST['levelMax']))   { $f['levelMax']   = $_REQUEST['levelMax'];   }
    if (isset($_REQUEST['wordsMin']))   { $f['wordsMin']   = $_REQUEST['wordsMin'];   }
    if (isset($_REQUEST['wordsMax']))   { $f['wordsMax']   = $_REQUEST['wordsMax'];   }
    if (isset($_REQUEST['medtype']))    { $f['medtypeFil'] = $_REQUEST['medtype'];    }
    if (isset($_REQUEST['medlenMin']))  { $f['medlenMin']  = $_REQUEST['medlenMin'];  }
    if (isset($_REQUEST['medlenMax']))  { $f['medlenMax']  = $_REQUEST['medlenMax'];  }
    if (isset($_REQUEST['buttonsMin'])) { $f['buttonsMin'] = $_REQUEST['buttonsMin']; }
    if (isset($_REQUEST['buttonsMax'])) { $f['buttonsMax'] = $_REQUEST['buttonsMax']; }
    if (isset($_REQUEST['filesMin']))   { $f['filesMin']   = $_REQUEST['filesMin'];   }
    if (isset($_REQUEST['filesMax']))   { $f['filesMax']   = $_REQUEST['filesMax'];   }
    if (isset($_REQUEST['title']))      { $f['titleFil']   = $_REQUEST['title'];      }
    if (isset($_REQUEST['text']))       { $f['textFil']    = $_REQUEST['text'];       }

    if ($mode==0) {  // Keep things really simple for mode 0, basic student mode
        $f['idFil']      =
        $f['viewsMin']   =
        $f['viewsMax']   =
        $f['clicksMin']  =
        $f['clicksMax']  =
        $f['likesMin']   =
        $f['likesMax']   =
        $f['createdMin'] =
        $f['createdMax'] =
        $f['changedMin'] =
        $f['changedMax'] =
        $f['licenceFil'] =
        $f['ownerFil']   =
        $f['wordsMin']   =
        $f['wordsMax']   =
        $f['medtypeFil'] =
        $f['medlenMin']  =
        $f['medlenMax']  =
        $f['buttonsMin'] =
        $f['buttonsMax'] =
        $f['filesMin']   =
        $f['filesMax']   =
//        $f['titleFil']   =
//        $f['textFil']    =
        '';
        $csSess->csFilter['sl']['m0'] = 0;  // No need to display Language because it is always filtered for in mode 0
       // Set up checked values for level radio buttons
        $level = $csSess->csFilter['level']['val1'];
        if ($level==='') {
            $levelAnychecked = 'checked';
        } else {
            $levelAnychecked = '';
            $levelA1checked = ( $level== 0 ? 'checked' : '');
            $levelA2checked = ( $level==10 ? 'checked' : '');
            $levelB1checked = ( $level==20 ? 'checked' : '');
            $levelB2checked = ( $level==30 ? 'checked' : '');
            $levelC1checked = ( $level==40 ? 'checked' : '');
            $levelC2checked = ( $level==50 ? 'checked' : '');
        }
    }

    $f['slFil']    = SM_WlSession::langName2Code($f['slFil']);  //Accept language names as well as codes
    $f['levelMin'] = SM_csSess::levelVis2Num($f['levelMin'],'min');
    $f['levelMax'] = SM_csSess::levelVis2Num($f['levelMax'],'max');

    $whereClauses['BASE'] = 'clilstore.sl=lang.id AND clilstore.owner=users.user';
    if ($f['idFil']<>'')      { $whereClauses['id']         = 'clilstore.id=?';     }
    if ($f['viewsMin']<>'')   { $whereClauses['viewsMin']   = 'views>=?';           }
    if ($f['viewsMax']<>'')   { $whereClauses['viewsMax']   = 'views<=?';           }
    if ($f['clicksMin']<>'')  { $whereClauses['clicksMin']  = 'clicks>=?';          }
    if ($f['clicksMax']<>'')  { $whereClauses['clicksMax']  = 'clicks<=?';          }
    if ($f['likesMin']<>'')   { $whereClauses['likesMin']   = 'likes>=?';           }
    if ($f['likesMax']<>'')   { $whereClauses['likesMax']   = 'likes<=?';           }
    if ($f['createdMin']<>'') { $whereClauses['createdMin'] = 'created>=?';         }
    if ($f['createdMax']<>'') { $whereClauses['createdMax'] = 'created<=?';         }
    if ($f['changedMin']<>'') { $whereClauses['changedMin'] = 'changed>=?';         }
    if ($f['changedMax']<>'') { $whereClauses['changedMax'] = 'changed<=?';         }
    if ($f['licenceFil']<>'') { $whereClauses['licence']    = 'licence LIKE ?';     }
    if ($f['ownerFil']<>'')   { $whereClauses['owner']      = 'owner LIKE ?';       }
    if ($f['slFil']<>'')      { $whereClauses['sl']         = 'sl=?';               }
    if ($f['levelMin']!=='')  { $whereClauses['levelMin']   = 'level>=?';           }
    if ($f['levelMax']!=='')  { $whereClauses['levelMax']   = 'level<=?';           }
    if ($f['wordsMin']<>'')   { $whereClauses['wordsMin']   = 'words>=?';           }
    if ($f['wordsMax']<>'')   { $whereClauses['wordsMax']   = 'words<=?';           }
    if ($f['medtypeFil']<>'') { $whereClauses['medtype']    = 'clilstore.medtype=?';}
    if ($f['medlenMin']<>'')  { $whereClauses['medlenMin']  = 'medlen>=?';          }
    if ($f['medlenMax']<>'')  { $whereClauses['medlenMax']  = 'medlen<=?';          }
    if ($f['buttonsMin']<>'') { $whereClauses['buttonsMin'] = 'buttons>=?';         }
    if ($f['buttonsMax']<>'') { $whereClauses['buttonsMax'] = 'buttons<=?';         }
    if ($f['filesMin']<>'')   { $whereClauses['filesMin']   = 'files>=?';           }
    if ($f['filesMax']<>'')   { $whereClauses['filesMax']   = 'files<=?';           }
    if ($f['titleFil']<>'')   { $whereClauses['title']      = 'title LIKE ?';       }
    if ($f['textFil']<>'')    { $whereClauses['text']       = '(text LIKE ? OR summary LIKE ?)';  }
    if ($incTest==0)          { $whereClauses['test']       = ( empty($user)
                                                              ? 'test=0'
                                                              : "(test=0 OR (test=1 AND owner='$user'))" ); }
    if ($mode==0 && $f['slFil']=='') { $whereClauses['zap'] = '0'; } //Zap everything and select no units if no language selected in mode 0

    $whereClause = implode(' AND ',$whereClauses);

    wildChars($f['licenceFil'],$licenceVis,'=');
    wildChars($f['ownerFil'],  $ownerVis,  '=');
    wildChars($f['titleFil'],  $titleVis,  'contains');
    wildChars($f['textFil'],   $textVis,   'contains');

    $csSess->setFilter($f);

    $idFil      = $f['idFil'];
    $viewsMin   = $f['viewsMin'];
    $viewsMax   = $f['viewsMax'];
    $clicksMin  = $f['clicksMin'];
    $clicksMax  = $f['clicksMax'];
    $likesMin   = $f['likesMin'];
    $likesMax   = $f['likesMax'];
    $createdMin = $f['createdMin'];
    $createdMax = $f['createdMax'];
    $changedMin = $f['changedMin'];
    $changedMax = $f['changedMax'];
    $licenceFil = $f['licenceFil'];
    $ownerFil   = $f['ownerFil'];
    $slFil      = $f['slFil'];
    $levelMin   = $f['levelMin'];
    $levelMax   = $f['levelMax'];
    $wordsMin   = $f['wordsMin'];
    $wordsMax   = $f['wordsMax'];
    $medtypeFil = $f['medtypeFil'];
    $medlenMin  = $f['medlenMin'];
    $medlenMax  = $f['medlenMax'];
    $buttonsMin = $f['buttonsMin'];
    $buttonsMax = $f['buttonsMax'];
    $filesMin   = $f['filesMin'];
    $filesMax   = $f['filesMax'];
    $titleFil   = $f['titleFil'];
    $textFil    = $f['textFil'];

    date_default_timezone_set('UTC');
    if ($createdMin<>'') { $createdMinQ = strtotime($f['createdMin'].'T00:00:00'); }
    if (!empty($createdMax)) { $createdMaxQ = strtotime($f['createdMax'].'T23:59:59'); }
    if (!empty($changedMin)) { $changedMinQ = strtotime($f['changedMin'].'T00:00:00'); }
    if (!empty($changedMax)) { $changedMaxQ = strtotime($f['changedMax'].'T23:59:59'); }
    $levelMin    = SM_csSess::levelVis2Num($levelMin,'min');
    $levelMax    = SM_csSess::levelVis2Num($levelMax,'max');
    $levelMinVis = SM_csSess::levelNum2Vis($levelMin,'min');
    $levelMaxVis = SM_csSess::levelNum2Vis($levelMax,'max');

    $idVal         =
    $viewsMinVal   =
    $viewsMaxVal   =
    $clicksMinVal  =
    $clicksMaxVal  =
    $likesMinVal   =
    $likesMaxVal   =
    $createdMinVal =
    $createdMaxVal =
    $changedMinVal =
    $changedMaxVal =
    $licenceVal    =
    $ownerVal      =
    $slVal         =
    $levelMinVal   =
    $levelMaxVal   =
    $wordsMinVal   =
    $wordsMaxVal   =
    $medtypeVal    =
    $medlenMinVal  =
    $medlenMaxVal  =
    $buttonsMinVal =
    $buttonsMaxVal =
    $filesMinVal   =
    $filesMaxVal   =
    $titleVal      =
    $textVal       = '';

    if (!empty($idFil))      { $idVal         = "value='$idFil'";      }
    if (!empty($viewsMin))   { $viewsMinVal   = "value='$viewsMin'";   }
    if (!empty($viewsMax))   { $viewsMaxVal   = "value='$viewsMax'";   }
    if (!empty($clicksMin))  { $clicksMinVal  = "value='$clicksMin'";  }
    if (!empty($clicksMax))  { $clicksMaxVal  = "value='$clicksMax'";  }
    if (!empty($likesMin))   { $likesMinVal   = "value='$likesMin'";   }
    if (!empty($likesMax))   { $likesMaxVal   = "value='$likesMax'";   }
    if (!empty($createdMin)) { $createdMinVal = "value='$createdMin'"; }
    if (!empty($createdMax)) { $createdMaxVal = "value='$createdMax'"; }
    if (!empty($changedMin)) { $changedMinVal = "value='$changedMin'"; }
    if (!empty($changedMax)) { $changedMaxVal = "value='$changedMax'"; }
    if (!empty($licenceVis)) { $licenceVal    = "value='$licenceVis'"; }
    if (!empty($ownerVis))   { $ownerVal      = "value='$ownerVis'";   }
    if (!($levelMin===''))   { $levelMinVal   = "value='$levelMinVis'";}
    if (!($levelMax===''))   { $levelMaxVal   = "value='$levelMaxVis'";}
    if (!empty($wordsMin))   { $wordsMinVal   = "value='$wordsMin'";   }
    if (!empty($wordsMax))   { $wordsMaxVal   = "value='$wordsMax'";   }
    if ($medtypeFil<>'')     { $medtypeVal    = "value='$medtypeFil'"; }
    if (!empty($medlenMin))  { $medlenMinVal  = "value='$medlenMin'";  }
    if (!empty($medlenMax))  { $medlenMaxVal  = "value='$medlenMax'";  }
    if (!empty($buttonsMin)) { $buttonsMinVal = "value='$buttonsMin'"; }
    if (!empty($buttonsMax)) { $buttonsMaxVal = "value='$buttonsMax'"; }
    if (!empty($filesMin))   { $filesMinVal   = "value='$filesMin'";   }
    if (!empty($filesMax))   { $filesMaxVal   = "value='$filesMax'";   }
    if (!empty($titleVis))   { $titleVal      = "value='$titleVis'";   }
    if (!empty($textVis))    { $textVal       = "value='$textVis'";    }


    $query = 'SELECT DISTINCT endonym,sl FROM clilstore,lang WHERE clilstore.sl=lang.id ORDER BY endonym';
    $stmt = $DbMultidict->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();
    $slOptions = array();
    $slOptions[] = '<option value="" style="background-color:white">'
                  .'</option><option value="ar">Arabic (ar)</option>'; //"Arabic" added in English as a special case
    foreach ($result as $lang) {
        $endonym = $lang['endonym'];
        $sl      = $lang['sl'];
        $selected = ( $sl==$slFil ? ' selected' : '');;
        $codeInfo = " ($sl)";  //Decided to always include the language code
        $slOptions[] = "<option value='$sl'$selected>$endonym$codeInfo</option>";
    }
    $slOptionsHtml = implode("\n",$slOptions);
    $slSelectColor = ( $slFil=='' ? 'white' : 'yellow' );

    if ($mode==0) {
        $levelButHtml = $csSess->levelButHtml();
        $tabletopChoices = <<<ENDtabletopChoices
<form id="selectForm" method="post" style="margin:1em 0 0.5em 0">
$T_Language <select name="sl" style="background-color:$slSelectColor" onchange="document.getElementById('selectForm').submit();">
$slOptionsHtml
</select>
</form>
$levelButHtml
ENDtabletopChoices;
    }

    $hoverColorOdd  = '#eef';
    $hoverColorEven = '#fff';
    if (empty($user)) { $highlightRow = 0; }
    else {
        $stmt = $DbMultidict->prepare('SELECT highlightRow FROM users WHERE user=:user');
        $stmt->execute(array(':user'=>$user));
        $highlightRow = $stmt->fetchObject()->highlightRow;
    }
    if ($highlightRow==1 || ($highlightRow==0 && $mode==3)) {
        $hoverColorOdd  = '#fe6';
        $hoverColorEven = '#fe6';
    }

    if ($mode==0 && $f['slFil']=='') { $noTable = true; } else { $noTable = false; }

    $columns = $csSess->columns();
    if (!empty($user)) { array_splice($columns,-2,0,array('edit')); }

    $tableStylesArr = [
        'id'      => 'text-align:right; font-size:75%; padding-top:4px;',
        'views'   => 'text-align:right; font-size:75%; padding-top:4px;',
        'clicks'  => 'text-align:right; font-size:75%; padding-top:4px;',
        'likes'   => 'text-align:right; font-size:75%; padding-top:4px;',
        'created' => 'font-size:75%; padding-top:4px; color:grey;',
        'changed' => 'font-size:75%; padding-top:4px; color:grey;',
        'licence' => 'font-size:75%; padding-top:5px;',
        'owner'   => '',
        'sl'      => '',
        'level'   => 'text-align:center;',
        'words'   => 'text-align:right; font-size:75%; padding-top:4px; text-align:right;',
        'medtype' => 'text-align:center;',
        'medlen'  => 'text-align:right; font-size:75%; padding-top:4px; text-align:right;',
        'buttons' => 'text-align:right;',
        'files'   => 'text-align:right;',
        'edit'    => '',
        'title'   => 'white-space:normal; padding-left:12px; text-indent:-10px;',
        'text'    => '' ];
    $tableStyles = '';
    foreach ($columns as $icol=>$fd) {
        $icol1 = $icol+1;
        $tableStyle = $tableStylesArr[$fd];
        $tableStyles .= "        table#main tr:not(.row4) td:nth-child($icol1) { $tableStyle }\n";
    }

    $row1 = $row2 = $row3 = $row4 = '';
    foreach ($columns as $fd) {
        if ($fd=='id') {
            $row1 .= "<td><a href='./?sortCol=id' title='$T_UnitID_title\n$T_Click_to_sort'>$T_UnitID</a></td>";
            $row2 .= "<td><input name=id $idVal pattern='[0-9]{1,5}' tabindex=10 autofocus style='width:2.5em;text-align:right'></td>";
            $row3 .= "<td></td>";
            $row4 .= $symbolHtml['id'];
        } elseif ($fd=='views') {
            $row1 .= "<td><a href='./?sortCol=views' title='$T_Views_title\n$T_Click_to_sort'>$T_Views</a></td>";
            $row2 .= "<td><input name=viewsMin pattern='[0-9]{1,}' $viewsMinVal placeholder='$T_min' tabindex=14 title='$T_minimum_views' style='width:3.5em;text-align:right'></td>";
            $row3 .= "<td><input name=viewsMax pattern='[0-9]{1,}' $viewsMaxVal placeholder='$T_max' tabindex=15 style='width:3.5em;text-align:right' title='$T_maximum_views'></td>";
            $row4 .= $symbolHtml['views'];
        } elseif ($fd=='clicks') {
            $row1 .= "<td><a href='./?sortCol=clicks' title='$T_Clicks_title\n$T_Click_to_sort'>$T_Clicks</a></td>";
            $row2 .= "<td><input name=clicksMin pattern='[0-9]{1,}' $clicksMinVal placeholder='$T_min' tabindex=16 title='$T_minimum_clicks' style='width:3.5em;text-align:right'></td>";
            $row3 .= "<td><input name=clicksMax pattern='[0-9]{1,}' $clicksMaxVal placeholder='$T_max' tabindex=17 style='width:3.5em;text-align:right' title='$T_maximum_clicks'></td>";
            $row4 .= $symbolHtml['clicks'];
        } elseif ($fd=='likes') {
            $row1 .= "<td><a href='./?sortCol=likes' title='$T_Click_to_sort'>$T_Likes</a></td>";
            $row2 .= "<td><input name=likesMin pattern='[0-9]{1,}' $likesMinVal placeholder='$T_min' tabindex=18 title='$T_minimum_likes' style='width:3.5em;text-align:right'></td>";
            $row3 .= "<td><input name=likesMax pattern='[0-9]{1,}' $likesMaxVal placeholder='$T_max' tabindex=19 style='width:3.5em;text-align:right' title='$T_maximum_likes'></td>";
            $row4 .= $symbolHtml['likes'];
        } elseif ($fd=='created') {
            $row1 .= "<td><a href='./?sortCol=created' title='$T_Created_title\n$T_Click_to_sort'>$T_Created</a></td>";
            $row2 .= "<td><input name=createdMin type=date $createdMinVal tabindex=20 title='$T_start_date' style='max-width:10em'></td>";
            $row3 .= "<td><input name=createdMax type=date $createdMaxVal tabindex=21 title='$T_end_date' style='max-width:10em'></td>";
            $row4 .= $symbolHtml['created'];
        } elseif ($fd=='changed') {
            $row1 .= "<td><a href='./?sortCol=changed' title='$T_Changed_title\n$T_Click_to_sort'>$T_Changed</a></td>";
            $row2 .= "<td><input name=changedMin type=date $changedMinVal tabindex=30 title='$T_start_date' style='max-width:10em'></td>";
            $row3 .= "<td><input name=changedMax type=date $changedMaxVal tabindex=31 title='$T_end_date' style='max-width:10em'></td>";
            $row4 .= $symbolHtml['changed'];
        } elseif ($fd=='licence') {
            $row1 .= "<td><a href='./?sortCol=licence' title='$T_Licence_title\n$T_Click_to_sort'>$T_Licence</a></td>";
            $row2 .= "<td><input name=licence $licenceVal tabindex=40 list='licenceList' style='width:4.7em'></td>";
            $row3 .= "<td></td>";
            $row4 .= $symbolHtml['licence'];
        } elseif ($fd=='owner') {
            $row1 .= "<td><a href='./?sortCol=owner' title='$T_Owner_title\n$T_Click_to_sort'>$T_Owner</a></td>";
            $row2 .= "<td><input name=owner $ownerVal tabindex=44 list='ownerList' style='width:10em'></td>";
            $row3 .= "<td></td>";
            $row4 .= $symbolHtml['owner'];
        } elseif ($fd=='sl') {
            $row1 .= "<td><a href='./?sortCol=sl' title='$T_Click_to_sort ($T_by_language_code)'>$T_Language</a></td>";
            $row2 .= "<td><select name=sl style='background-color:$slSelectColor' tabindex=50>$slOptionsHtml</select></td>";
            $row3 .= "<td></td>";
            $row4 .= $symbolHtml['sl'];
        } elseif ($fd=='level') {
            $row1 .=  "<td><a href='./?sortCol=level' title='$T_Level_title\n$T_Click_to_sort'>$T_Level</a></td>";
            $row2 .= ( $mode==0 ? '<td></td>'
                     : "<td><input name=levelMin $levelMinVal placeholder='$T_min' list=levelList title='$T_minimum_CEFR_level' tabindex=60 style='width:2.8em;text-align:center'></td>" );
            $row3 .= ( $mode==0 ? '<td></td>'
                     : "<td><input name='levelMax' $levelMaxVal placeholder='$T_max' list=levelList tabindex=61 title='$T_maximum_CEFR_level' style='width:2.8em;text-align:center'></td>" );
            $row4 .= $symbolHtml['level'];
        } elseif ($fd=='words') {
            $row1 .= "<td><a href='./?sortCol=words' title='$T_Words_title\n$T_Click_to_sort'>$T_Words</a></td>";
            $row2 .= "<td><input name=wordsMin pattern='[0-9]{1,}' $wordsMinVal placeholder='$T_min' title='$T_minimum_words' tabindex=62 style='width:3.5em;text-align:right'></td>";
            $row3 .= "<td><input name='wordsMax' pattern='[0-9]{1,}' $wordsMaxVal placeholder='$T_max' tabindex=63 style='width:3.5em;text-align:right' title='$T_maximum_words'></td>";
            $row4 .= $symbolHtml['words'];
        } elseif ($fd=='medtype') {
            $row1 .= "<td><a href='./?sortCol=medtype' title='$T_Media_title\n$T_Click_to_sort'>$T_Media</a></td>";
            $row2 .= ( $mode==0 ? '<td></td>'
                     : "<td><input name=medtype $medtypeVal pattern='[0-2]' placeholder='0,1,2' title='$T_media_field_title' tabindex=64 style='width:2.1em;text-align:center'></td>" );
            $row3 .= "<td></td>";
            $row4 .= $symbolHtml['medtype'];
        } elseif ($fd=='medlen') {
            $row1 .= "<td><a href='./?sortCol=medlen' title='$T_MedLength_title\n$T_Click_to_sort'>$T_MedLength</a></td>";
            $row2 .= "<td><input name=medlenMin pattern='[0-9]{1,}' $medlenMinVal placeholder='$T_min' title='$T_minimum_media_length' tabindex=66 style='width:3.3em;text-align:right'></td>";
            $row3 .= "<td><input name='medlenMax' pattern='[0-9]{1,}' $medlenMaxVal placeholder='$T_max' tabindex=67 style='width:3.3em;text-align:right' title='$T_maximum_media_length'></td>";
            $row4 .= $symbolHtml['medlen'];
        } elseif ($fd=='buttons') {
            $row1 .= "<td><a href='./?sortCol=buttons' title='$T_Buttons_title\n$T_Click_to_sort'>$T_Buttons</a></td>";
            $row2 .= "<td><input name=buttonsMin pattern='[0-9]{1,}' $buttonsMinVal placeholder='$T_min' title='$T_minimum_buttons' tabindex=68 style='width:3.3em;text-align:right'></td>";
            $row3 .= "<td><input name=buttonsMax pattern='[0-9]{1,}' $buttonsMaxVal placeholder='$T_max' tabindex=69 style='width:3.3em;text-align:right' title='$T_maximum_buttons'></td>";
            $row4 .= $symbolHtml['buttons'];
        } elseif ($fd=='files') {
            $row1 .= "<td><a href='./?sortCol=files' title='$T_Files_title\n$T_Click_to_sort'>$T_Files</a></td>";
            $row2 .= "<td><input name=filesMin pattern='[0-9]{1,}' $filesMinVal placeholder='$T_min' title='$T_minimum_files' tabindex=70 style='width:3.3em;text-align:right'></td>";
            $row3 .= "<td><input name=filesMax pattern='[0-9]{1,}' $filesMaxVal placeholder='$T_max' tabindex=71 style='width:3.3em;text-align:right' title='$T_maximum_files'></td>";
            $row4 .= $symbolHtml['files'];
        } elseif ($fd=='edit') {
            $row1 .= "<td>&nbsp;</td>";
            $row2 .= "<td></td>";
            $row3 .= "<td></td>";
            $row4 .= "<td></td>";
        } elseif ($fd=='title') {
            $row1 .= "<td><a href='./?sortCol=title' title='$T_Click_to_sort'>$T_Title</a></td>";
            $row2 .= "<td><input name=title $titleVal placeholder='$T_part_placeholder ($T_or_wildcard_pattern) 🔍' title='$T_title_title ($T_or_wildcard_pattern)' tabindex=72 style='width:17em'></td>";
            $row3 .= <<<END_row3titlecell
<td class="title" colspan=2>
 <div id="findDiv">
     <input type="submit" name=filter value="$T_Lorg" tabindex=80>&nbsp;&nbsp;
     <input type="submit" name=clearFilter value="$T_Clear_filter" title="$T_Clear_filter_title" tabindex=90 onclick="clearFields()">
 </div>
</td>
END_row3titlecell;
            $row4 .= $symbolHtml['title'];
        } elseif ($fd=='text') {
            $row1 .= "<td>$T_TextOrSummary</td>";
            $row2 .= "<td><input name=text $textVal placeholder='$T_part_placeholder ($T_or_wildcard_pattern) 🔍' title='$T_text_title ($T_or_wildcard_pattern)' tabindex=74 style='min-width:10em;width:95%'></td>";
        }
    }

    if (!$noTable) {
        $dataLists = <<<END_dataLists
<datalist id="levelList">
<option value="A1">
<option value="A2">
<option value="B1">
<option value="B2">
<option value="C1">
<option value="C2">
</datalist>

<datalist id="licenceList">
<option value="BY-SA">
<option value="BY">
<option value="BY-ND">
<option value="BY-NC-SA">
<option value="BY-NC">
<option value="BY-NC-ND">
</datalist>

<datalist id="ownerList">
$ownerListHtml
</datalist>

$checkboxesHtml
END_dataLists;

        $tableHtml = <<<END_tableHtmlBarr
<table id=main>
<tr class=row1>$row1</tr>
<tr class=row2 onchange="submitFForm()">$row2</tr>
<tr class=row3 onchange="submitFForm()">$row3</tr>
<tr class=row4>$row4</tr>
END_tableHtmlBarr;


        $orderClause = $csSess->orderClause();
        $query = 'SELECT clilstore.id,owner,fullname,sl,endonym,level,words,medtype,medlen,buttons,files,title,summary,created,changed,licence,test,views,clicks,likes'
                .' FROM clilstore,users,lang'
                ." WHERE $whereClause ORDER BY $orderClause"
                .' LIMIT 0,4000';
        $stmt = $DbMultidict->prepare($query);
        $i = 1;
        if (!empty($whereClauses['id']))         { $stmt->bindParam($i++,$idFil);       }
        if (!empty($whereClauses['viewsMin']))   { $stmt->bindParam($i++,$viewsMin);    }
        if (!empty($whereClauses['viewsMax']))   { $stmt->bindParam($i++,$viewsMax);    }
        if (!empty($whereClauses['clicksMin']))  { $stmt->bindParam($i++,$clicksMin);   }
        if (!empty($whereClauses['clicksMax']))  { $stmt->bindParam($i++,$clicksMax);   }
        if (!empty($whereClauses['likesMin']))   { $stmt->bindParam($i++,$likesMin);    }
        if (!empty($whereClauses['likesMax']))   { $stmt->bindParam($i++,$likesMax);    }
        if (!empty($whereClauses['createdMin'])) { $stmt->bindParam($i++,$createdMinQ); }
        if (!empty($whereClauses['createdMax'])) { $stmt->bindParam($i++,$createdMaxQ); }
        if (!empty($whereClauses['changedMin'])) { $stmt->bindParam($i++,$changedMinQ); }
        if (!empty($whereClauses['changedMax'])) { $stmt->bindParam($i++,$changedMaxQ); }
        if (!empty($whereClauses['licence']))    { $stmt->bindParam($i++,$licenceFil);  }
        if (!empty($whereClauses['owner']))      { $stmt->bindParam($i++,$ownerFil);    }
        if (!empty($whereClauses['sl']))         { $stmt->bindParam($i++,$slFil);       }
        if (!empty($whereClauses['levelMin']))   { $stmt->bindParam($i++,$levelMin);    }
        if (!empty($whereClauses['levelMax']))   { $stmt->bindParam($i++,$levelMax);    }
        if (!empty($whereClauses['wordsMin']))   { $stmt->bindParam($i++,$wordsMin);    }
        if (!empty($whereClauses['wordsMax']))   { $stmt->bindParam($i++,$wordsMax);    }
        if (!empty($whereClauses['medtype']))    { $stmt->bindParam($i++,$medtypeFil);  }
        if (!empty($whereClauses['medlenMin']))  { $stmt->bindParam($i++,$medlenMin);   }
        if (!empty($whereClauses['medlenMax']))  { $stmt->bindParam($i++,$medlenMax);   }
        if (!empty($whereClauses['buttonsMin'])) { $stmt->bindParam($i++,$buttonsMin);  }
        if (!empty($whereClauses['buttonsMax'])) { $stmt->bindParam($i++,$buttonsMax);  }
        if (!empty($whereClauses['filesMin']))   { $stmt->bindParam($i++,$filesMin);    }
        if (!empty($whereClauses['filesMax']))   { $stmt->bindParam($i++,$filesMax);    }
        if (!empty($whereClauses['title']))      { $stmt->bindParam($i++,$titleFil);    }
        if (!empty($whereClauses['text']))       { $stmt->bindParam($i++,$textFil);
                                                   $stmt->bindParam($i++,$textFil);     }
        $stmt->execute();
       //Initialise statistics
        $nunits = 0;
        $cnt['level'] = $cnt['medlen'] = 0;
        $tot['views'] = $tot['clicks'] = $tot['likes'] = $tot['created'] = $tot['created2'] = $tot['changed'] = $tot['level'] = $tot['words'] = $tot['medlen'] = $tot['buttons'] = $tot['files'] = 0;
       //
        $units = $stmt->fetchAll(PDO::FETCH_ASSOC);~~
        $nunits = count($units);
        $maxunits = 100;  //The limit unless showAll is set
        $showAll = $_REQUEST['showAll'] ?? '';
        if ($nunits > $maxunits) {
            if ($showAll) { $limitInfo = TRUE; } else { $limitUnits = TRUE; }
        }
        foreach ($units as $iunit=>$unit) {
            extract($unit);
           //Increment statistics
            $tot['views']   += $views;
            $tot['clicks']  += $clicks;
            $tot['likes']   += $likes;
            $tot['created'] += $created;
            $tot['created2']+= max($created,1395144000); //Adjusted for click count time, which only started on 2014-03-18
            $tot['changed'] += $changed;
            $tot['level']   += $level;
            $tot['words']   += $words;
            $tot['medlen']  += $medlen;
            $tot['buttons'] += $buttons;
            $tot['files']   += $files;
            $cnt['level']   += ($level>-1);
            $cnt['medlen']  += ($medlen>0);
            if ($limitUnits && $iunit>=$maxunits) {
                if ($iunit==$maxunits) { $tableHtml .= "<tr><td>[...]</td></tr>\n"; }
                continue;
            }
            $summary = ( $iunit <=100 ? htmlspecialchars($summary) : '');  //Maximum of 100 summaries shown, to save memory
            $createdObj = new DateTime("@$created");
            $createdDate     = date_format($createdObj, 'Y-m-d');
            $createdDateTime = date_format($createdObj, 'Y-m-d H:i:s');
            $changedObj = new DateTime("@$changed");
            $changedDate     = date_format($changedObj, 'Y-m-d');
            $changedDateTime = date_format($changedObj, 'Y-m-d H:i:s');
            if ($changed==$created) { $changedDate = $changedDateTime = ''; }
            $ownerHtml    = htmlspecialchars($owner);
            $fullnameHtml = htmlspecialchars($fullname);
            $ownerHtml = "<a href='userinfo.php?user=$ownerHtml' title='$fullnameHtml'>$ownerHtml</a>";
            $editHtml = '';
            $cefrHtml = SM_csSess::cefrHtml($level);
            $testHtml  = ( empty($test) ? '' : '<img src="/icons-smo/undConst.gif" alt="" class=undConst> ' );
            $titleClass = ( empty($test) ? 'title' : 'title italic' );
            if ($sl=='ar') { $titleClass .= ' arabicfont'; }
            $medlenHtml = ( ( empty($medlen) && ($medtype==0 || $owner<>$user  ) )
                          ? ''
                          : SM_csSess::secs2minsecs($medlen)
                          );
            if      ($medtype==1) { $medtypeHtml = "<img src='audio.png' alt='snd' title='$medlenHtml'>"; }
             elseif ($medtype==2) { $medtypeHtml = "<img src='video.png' alt='vid' title='$medlenHtml'>"; }
             else                 { $medtypeHtml = ''; }
            $buttonsHtml = ( empty($buttons) ? '' : $buttons );
            $filesHtml   = ( empty($files)   ? '' : $files   );
            if ($user==$owner || $user=='admin')  {
                $editHtml = "<a href='delete.php?id=$id'><img src='/icons-smo/curAs.png' alt='Delete' title ='$T_Delete_this_unit' class='favicon'></a>"
                          . "<a href='edit.php?id=$id'><img src='/icons-smo/peann.png' alt='Edit' title ='$T_Edit_this_unit' class='favicon'></a>";
            }
            $titleHtml = htmlspecialchars($title);
            $titleCool = strtr($titleHtml,[ "[COOL]" => "<img src='Cool.png' alt='[COOL]' style='padding-right:0.3em'>" ]);
            $titleHtml  = ( empty($test) ? $titleCool : "<img src='/icons-smo/undConst.gif' alt='' class=undConst> <i>$titleCool</i>" );
            $row = '<tr>';
            foreach ($columns as $fd) {
                if ($fd=='id')            { $row .= "<td><a href='/cs/$id' title='$views views'>$id</a></td>"; }
                  elseif ($fd=='views')   { $row .= "<td>$views</td>"; }
                  elseif ($fd=='clicks')  { $row .= "<td>$clicks</td>"; }
                  elseif ($fd=='likes')   { $row .= "<td>$likes</td>"; }
                  elseif ($fd=='created') { $row .= "<td title='$createdDateTime UT'>$createdDate</td>"; }
                  elseif ($fd=='changed') { $row .= "<td title='$changedDateTime UT'>$changedDate</td>"; }
                  elseif ($fd=='licence') { $row .= "<td>$licence</td>"; }
                  elseif ($fd=='owner')   { $row .= "<td>$ownerHtml</td>"; }
                  elseif ($fd=='sl')      { $row .= "<td title='language code: $sl'>$endonym</td>"; }
                  elseif ($fd=='level')   { $row .= "<td>$cefrHtml</td>"; }
                  elseif ($fd=='words')   { $row .= "<td>$words</td>"; }
                  elseif ($fd=='medtype') { $row .= "<td>$medtypeHtml</td>"; }
                  elseif ($fd=='medlen')  { $row .= "<td>$medlenHtml</td>"; }
                  elseif ($fd=='buttons') { $row .= "<td>$buttonsHtml</td>"; }
                  elseif ($fd=='files')   { $row .= "<td>$filesHtml</td>"; }
                  elseif ($fd=='edit')    { $row .= "<td>$editHtml</td>"; }
                  elseif ($fd=='title')   { $row .= "<td colspan=2><a href='/cs/$id' title='$summary'>$titleHtml</a></td>"; }
            }
            $row .= "</tr>\n";
            $tableHtml .= $row;
        }
        $stmt = null;
        $DbMultidict = null;
        if ($nunits==0) {
            $unitsFoundMessage = ( $mode==0 && $f['slFil']==''
                                 ? "$T_First_choose_language"
                                 : "$T_No_units_match_filter" );
            $unitsFoundMessage = "<p><span style='color:red'>$unitsFoundMessage</span></p>\n";
        } elseif ($nunits<2) {
            $unitsFoundMessage = '';
        } else { //Calculate and display statistics
             $unitsFoundMessage  = "<span style='color:grey'>$nunits $T_units_found</span>";
             if ($limitUnits) { $unitsFoundMessage .= ", <span style='color:red'>" . sprintf($T_only_first_n_are_shown,$maxunits) . "</span><br>"
                                                    . "<button name=showAll value=showAll type=submit title='Show information on all units (slightly condensed)'>$T_Show_all</button></p>"; }
             $unitsFoundMessage = "<p style='margin-top:0;font-size:70%'>$unitsFoundMessage</p>";
             if ($mode>1) {
                 $avgLevel  = ( $cnt['level']==0  ? '' : sprintf('%.1f',$tot['level']/$cnt['level']) );
                 $avgLevelHtml = SM_csSess::cefrHtml($avgLevel);
                 $avgMedlen = ( $cnt['medlen']==0 ? '' : SM_csSess::secs2minsecs(round($tot['medlen']/$cnt['medlen'])) );
                 $avgCreated = round($tot['created']/$nunits);
                 $avgCreatedObj = new DateTime("@$avgCreated");
                 $avgCreatedDate     = date_format($avgCreatedObj, 'Y-m-d');
                 $avgCreatedDateTime = date_format($avgCreatedObj, 'Y-m-d H:i:s');
                 $avgChanged = round($tot['changed']/$nunits);
                 $avgChangedObj = new DateTime("@$avgChanged");
                 $avgChangedDate     = date_format($avgChangedObj, 'Y-m-d');
                 $avgChangedDateTime = date_format($avgChangedObj, 'Y-m-d H:i:s');
                 $timeNow = time();
                 $totViewTime  = $nunits*$timeNow - $tot['created'];
                 $totClickTime = $nunits*$timeNow - $tot['created2'];
                 $viewRate  = $tot['views']/$totViewTime;
                 $clickRate = $tot['clicks']/$totClickTime;
                 function rateMessage($rate) { return sprintf('%.4g/day, %.4g/month, %.4g/year', $rate*86400, $rate*2630000, $rate*31557600); }
                 $viewRateMessage  = rateMessage($viewRate);
                 $clickRateMessage = rateMessage($clickRate);
                 $totViewRateMessage  = rateMessage($viewRate *$nunits);
                 $totClickRateMessage = rateMessage($clickRate*$nunits);
                 $totRow = '<tr style="font-size:70%;color:grey;background-color:#ddd">';
                 $avgRow = '<tr style="font-size:70%;color:grey;background-color:#ddd;border-top:solid 1px #888">';
                 foreach ($columns as $fd) {
                     if ($fd=='id') {
                         $totRow .= "<td>$T_Total</td>";
                         $avgRow .= "<td>$T_Average</td>";
                     } elseif ($fd=='views') {
                         $totRow .= "<td title='$totViewRateMessage'>" . $tot['views']  . '</td>';
                         $avgRow .= "<td title='$viewRateMessage'>"    . sprintf('%.1f',$tot['views']/$nunits) . '</td>';
                     } elseif ($fd=='clicks') {
                         $totRow .= "<td title='$totClickRateMessage'>" . $tot['clicks'] . '</td>';
                         $avgRow .= "<td title='$clickRateMessage'>"    . sprintf('%.1f',$tot['clicks']/$nunits)   . '</td>';
                     } elseif ($fd=='likes') {
                         $totRow .= "<td>" . $tot['likes'] . '</td>';
                         $avgRow .= "<td>" . sprintf('%.1f',$tot['likes']/$nunits)   . '</td>';
                     } elseif ($fd=='created') {
                         $totRow .= '<td></td>';
                         $avgRow .= "<td title='$avgCreatedDateTime UT'>$avgCreatedDate</td>";
                     } elseif ($fd=='changed') {
                         $totRow .= '<td></td>';
                         $avgRow .= "<td title='$avgChangedDateTime UT'>$avgChangedDate</td>";
                     } elseif ($fd=='licence') {
                         $totRow .= '<td></td>';
                         $avgRow .= '<td></td>';
                     } elseif ($fd=='owner') {
                         $totRow .= '<td></td>';
                         $avgRow .= '<td></td>';
                     } elseif ($fd=='sl') {
                         $totRow .= '<td></td>';
                         $avgRow .= '<td></td>';
                     } elseif ($fd=='level') {
                         $totRow .= '<td></td>';
                         $avgRow .= "<td>$avgLevelHtml</td>";
                     } elseif ($fd=='words') {
                         $totRow .= '<td>'  . $tot['words']  . '</td>';
                         $avgRow .= '<td>'  . sprintf('%.1f',$tot['words']/$nunits)  . '</td>';
                     } elseif ($fd=='medtype') {
                         $totRow .= '<td></td>';
                         $avgRow .= '<td></td>';
                     } elseif ($fd=='medlen') {
                         $totRow .= '<td>' . SM_csSess::secs2minsecs($tot['medlen']) . '</td>';
                         $avgRow .= "<td>$avgMedlen</td>";
                     } elseif ($fd=='buttons') {
                         $totRow .= '<td>'. $tot['buttons']. '</td>';
                         $avgRow .= '<td>'. sprintf('%.1f',$tot['buttons']/$nunits) . '</td>';
                     } elseif ($fd=='files') {
                         $totRow .= '<td>'. $tot['files']. '</td>';
                         $avgRow .= '<td>'. sprintf('%.1f',$tot['files']/$nunits) . '</td>';
                     }
                 }
                 $totRow .= '<td colspan=3></td></tr>';
                 $avgRow .= '<td colspan=3></td></tr>';
             }
        }
 
        $tableHtml .= <<<END_tableHtmlBun
$avgRow
$totRow
</table>
$unitsFoundMessage
END_tableHtmlBun;
    }

    echo <<<EOD1
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clilstore - $T_Teaching_units $T_for_CLIL</title>
    <link rel="StyleSheet" href="/css/smo.css">
    <link rel="StyleSheet" href="/css/toggle-switchy.css">
    <link rel="StyleSheet" href="style.css">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
    <style>
        table#main { border:1px solid #888; border-collapse:collapse; white-space:nowrap; color:#111; }
        table#main tr td { padding:0 3px; }

        table#main tr.row1 td { background-color:#888; color:white; padding-top:0; border-bottom:2px solid #888; }
        table#main tr.row1 td a { color:white; }
        table#main tr.row2 td { background-color:#e2e2e2; }
        table#main tr.row3 td { background-color:#e2e2e2; padding-top:0; }
        table#main tr.row4 td { background-color:#e2e2e2; padding-top:0; padding-bottom:1px; border-bottom:1px solid #888; font-size2:120%; text-align:center; }
        table#main tr.row4 td a:visited { color:#61abac; }
$tableStyles
        table#main tr.row1 td:nth-child(n) { font-size:100%; padding-top:0; }

        table#main input { border:1px solid #e2e2e2; background-color:white; border-top:1px; border-bottom:1px; padding:0 2px; }
        table#main input:focus { border-top:1px solid #222; border-left:1px solid #222; border-bottom:1px solid #aaa; border-right:1px solid #aaa; }
        table#main input[value] { background-color:yellow; }

        table#main { border:1px solid #888; border-collapse2:collapse; white-space:nowrap; color:#111; }
        table#main tr td { padding:0 3px; vertical-align:top; }

        table#main tr:nth-child(odd)  { background-color:#eef; }
        table#main tr:nth-child(even) { background-color:#fff; }
        table#main tr:nth-child(odd):hover  { background-color:$hoverColorOdd; }
        table#main tr:nth-child(even):hover { background-color:$hoverColorEven; }

        div.find { float:right; margin:4px 0 0 0; padding:2px 22px 2px 1px; }
        div.find input { font-size:112%; background-color:#888; color:white; font-weight:bold; padding:3px 10px; border:0; border-radius:8px; }
        div.find input:hover { background-color:blue; }

        table#main div#findDiv { float:right; margin:4px 0 0 0; padding:0 22px 0 1px; }
        table#main div#findDiv input { font-size:100%; background-color:#888; color:white; font-weight:bold; padding:0px 8px; border:0; border-radius:8px; }
        table#main div#findDiv input:hover { background-color:blue; }

        img.favicon { width:16px; height:16px; border:0; margin:2px; }
        img.undConst { padding-left:16px; }
        a.button { display:block; float:left; margin:1px 7px; background-color:#55a8eb; color:white; font-weight:bold; padding:3px 10px; border:0; border-radius:8px; }
        a.button:hover { background-color:blue; }
        a.mybutton, button { background-color:#55a8eb; color:white; padding:1px 8px; border-radius:8px; white-space:nowrap; }
        a.mybutton:hover, button:hover { background-color:blue; }
        a.levelbutton { margin:1px 7px; background-color:#55a8eb; color:white; font-weight:bold; padding:2px 8px; border:1px solid white; border-radius:8px; }
        a.levelbutton.selected { border-color:#55a8eb; background-color:yellow; color:#55a8eb; }
        a.levelbutton.grey { background-color:#ccc; }
        a.levelbutton.live:hover { background-color:blue; }
        div#cookieMessage { clear:both; margin-bottom:0.7em; border:1px solid brown; background-color:#fdd; padding:0.4em }
        div#cookieMessage p { margin:0.5em 0; }
        a#gotitButton { background-color:#55a8eb; color:white; font-weight:bold; padding:3px 10px; border:0; border-radius:8px; }
        a.gotitButtonn:hover { background-color:blue; }

        select.con { border:0; text-align:center; }
        input[type=text][value] { background-color:yellow; }
        input[type=date][value] { background-color:yellow; }
        .italic { font-style:italic; }
        .arabicfont { font-family:"Times Roman","Times New Roman"; font-size:130%; }
        p.noUnits { color:red; background-color:yellow; }
        span.fann { color:grey; font-size:65%; }
    </style>
    <script>
        function clearFields () {
            var el,elType;
            form = document.getElementById('filterForm');
            for(i=0; i<form.elements.length; i++) {
                el = form.elements[i];
                if (el.name=='incTest') continue;
                if (el.name=='wide')    continue;
                elType = el.type.toLowerCase();
                if (elType=='text' || elType=='date') {
                    el.value = '';
                    delete el.value; //All that is needed for Opera; the rest is for other browsers
                } else if (elType=='checkbox') {
                    el.checked = '';
                } else if (elType=='select-one') {
                    el.selectedIndex = 0;
                }
            }
            form.submit();
        }

        function submitFForm () {
            document.getElementById('filterForm').submit();
        }

        function addColChange(fd) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onload = function() {
                if (this.status!=200) { alert('Error in addColChange:'+this.status); return; }
                window.location.href = window.location.href;
            }
            xmlhttp.open('GET', 'ajax/addCol.php?fd=' + fd);
            xmlhttp.send();
        }

        function setNewbie() {
            alert('$T_setNewbieAlert');
            document.cookie = "myCLIL_authentication=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "csSessionId=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/clilstore/;";
            document.cookie = "wlUser=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "Thl=; expires=Thu, 01 Jan 2019 00:00:00 UTC; path=/;";
            window.location = window.location.href;
        }
</script>
</head>
<body onload="history.pushState('','',location.pathname);">

$mdNavbar
$cookieMessage
<div class="smo-body-indent">
<a><img src=/favicons/restart.png style="float:right" alt="Restart" title="$T_See_as_newbie" onclick="setNewbie();"></a>

<h1 style="float:left;margin:10px 12px 0 0"><img src="/icons-smo/clilstore-blue45.png" alt="Clilstore" style="width:184px;height:45px"></h1>
<p style="margin:22px 0 0 0;font-size:90%;float:left">$T_Teaching_units<br>$T_for_CLIL</p>
<a href="help.php" class="button">$T_Help</a>
<a href="about.php" class="button">$T_About_Clilstore</a>

<div style="width:100%;min-height:1px;clear:both">

<form id="modeForm" method="get" style="float:left;padding:10px 0">
<select name="mode" style="background-color:white" onchange="document.getElementById('modeForm').submit();">
<option $mode0selected value="0" title="$T_For_students_info">$T_For_students</option>
<option $mode1selected value="1" title="$T_For_students_more_info">$T_For_students - $T_more_options</option>
<option $mode2selected value="2" title="$T_For_teachers_info">$T_For_teachers</option>
<option $mode3selected value="3" title="$T_For_teachers_more_info">$T_For_teachers - $T_more_options</option>
</select>
</form>
$photo

$userHtml

$tabletopChoices
$dataLists
<form id="filterForm" method="post" onreset="clearFields();">
<input type="hidden" name="filterForm" value="1">
$tableHtml
</form>

<div style="min-height:65px;max-width:840px;border:2px solid #47d;margin:8em 0 0.5em 0;border-radius:4px;color:#47d;font-size:95%">
<div style="float:left;margin-right:1.5em">
<a href="https://eacea.ec.europa.eu/erasmus-plus_en"><img src="$EUlogo" alt="" style="margin:3px"></a>
</div>
<div style="min-height:59px">
<p style="margin:0.3em 0;color:#1e4d9f;font-size:75%"><i>$T_Disclaimer:</i> $T_Disclaimer_EuropeanCom</p>
</div>
</div>
</div>

</div>
$mdNavbar

</body>
</html>
EOD1;

  } catch (Exception $e) { echo $e; }

?>
