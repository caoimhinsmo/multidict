<?php
class SM_mdNavbar {

  public static function hlArr() {
      $hlArr = array(
          'br'=>'Brezhoneg',
          'da'=>'Dansk',
          'en'=>'English',
          'es'=>'Español',
          'fr'=>'Français',
          'ga'=>'Gaeilge',
          'gd'=>'Gàidhlig',
          'it'=>'Italiano',
          'lt'=>'Lietuvių',
          'pt'=>'Português',
          'bg'=>'Български',
//            '----1'=>'',  //Partial translations
            '----2'=>'',  //Very partial translations
          'cy'=>'Cymraeg',
          'de'=>'Deutsch',
          'is'=>'Íslenska');
      return $hlArr;
  }


  public static function mdNavbar($domhan='',$unit=NULL) {
      $servername =  $_SERVER['SERVER_NAME'];
      $serverhome = ( empty($_SERVER['HTTPS']) ? 'http' : 'https' ) . '://' . $_SERVER['SERVER_NAME'];
      $hl0 = SM_T::hl0();

      $T = new SM_T('clilstore/mdNavbar');
      $T_homeTitle            = $T->h('homeTitle');
      $T_canan_eadarAghaidh   = $T->h('canan_eadarAghaidh');
      $T_Log_air              = $T->h('Log_air');
      $T_Log_air_fios         = $T->h('Log_air_fios');
      $T_Logout               = $T->h('Logout');
      $T_Log_dheth_fios       = $T->h('Log_dheth_fios');
      $T_tr_fios              = $T->h('tr_fios');
      $T_Clilstore_index_page = $T->h('Clilstore_index_page');
      $T_Wordlink_index_page  = $T->h('Wordlink_index_page');
      $T_Multidict_index_page = $T->h('Multidict_index_page');
      $T_Unit                 = $T->h('Unit');  

      $php_self = $_SERVER['PHP_SELF'];
      $php_self1 = explode('/',$php_self)[1] ?? '';
      if ($php_self=='/clilstore/index.php') {
          $homeLink = "<li><a href='/' title='$T_homeTitle'>$servername</a>";
      } elseif ($php_self1=='clilstore') {
          $homeLink = "<li><a href='/clilstore/' title='$T_Clilstore_index_page'>Clilstore</a>";
      } elseif ($php_self1=='wordlink') {
          $homeLink = "<li><a href='/wordlink/' title='$T_Wordlink_index_page'>Wordlink</a>";
      } elseif ($php_self1=='multidict') {
          $homeLink = "<li><a href='/multidict/' title='$T_Multidict_index_page'>Multidict</a>";
      } else {
          $homeLink = '';
      }
      $unitLink = ( isset($unit)
                  ? "<li><a href='/cs/$unit'>$T_Unit $unit</a></li>"
                  : ''
                  );
      $myCLIL = SM_myCLIL::singleton();
      if ($myCLIL->cead(SM_myCLIL::LUCHD_EADARTHEANGACHAIDH))
        { $trPutan = "\n<li class=deas><a href='//www3.smo.uhi.ac.uk/teanga/smotr/tr.php?domhan=$domhan' target='tr' title='$T_tr_fios'>tr</a>"; } else { $trPutan = ''; }
      $ceangalRiMoSMO = ( isset($myCLIL->id)
                        ? "<li class='deas'><a href='/clilstore/logout.php' title='$T_Log_dheth_fios'>$T_Logout</a></li>"
                        : "<li class='deas'><a href='/clilstore/login.php?till_gu=/' title='$T_Log_air_fios'>$T_Log_air</a></li>"
                        );
      $hlArr = self::hlArr();
      $options = '';
      foreach ($hlArr as $hl=>$hlAinm) {
          if (substr($hl,0,4)=='----') { $options .= "<option value='' disabled>&nbsp;_{$hlAinm}_</option>/n"; }  //Divider in the list of select options
            else                       { $options .= "<option value='$hl|en'" . ( $hl==$hl0 ? ' selected' : '' ) . ">$hlAinm</option>\n"; }
      }
      $selCanan = <<< END_selCanan
<script>
    function atharraichCanan(hl) {
        document.cookie = 'Thl=' + hl + '; path=/; max-age=15000000';  //Valid for six months
        var paramstr = location.search;
        if (/Trident/.test(navigator.userAgent) || /MSIE/.test(navigator.userAgent)) {
          //Something really weak for Internet Explorer, which doesn’t understand URLSearchParams. Delete when IE is finally dead.
            if (paramstr.length==6 && paramstr.substring(0,4)=='?hl=') { paramstr = ''; }
            paramstr = paramstr;
        } else {
            const params = new URLSearchParams(paramstr)
            params.delete('hl');
            paramstr = params.toString();
            if (paramstr!='') { paramstr = '?'+paramstr; }
        }
        loc = window.location;
        location = loc.protocol + '//' + loc.hostname + loc.pathname + paramstr;
    }
</script>
<form>
<select name="hl" style="display:inline-block;background-color:#eef;margin:0 4px" onchange="atharraichCanan(this.options[this.selectedIndex].value)">
$options</select>
</form>
END_selCanan;
      $mdNavbar = <<<EOD_NAVBAR
<ul class="smo-navlist">
$homeLink
$unitLink
$ceangalRiMoSMO
<li style="float:right" title="$T_canan_eadarAghaidh">$selCanan$trPutan
</ul>
EOD_NAVBAR;
      return $mdNavbar;
  }

  public static function hlSelect() {
      $hl0 = SM_T::hl0();

      $T = new SM_T('clilstore/mdNavbar');
      $T_interface_language  = $T->h('canan_eadarAghaidh');

      $hlArr = self::hlArr();
      $options = '';
      foreach ($hlArr as $hl=>$hlAinm) {
          if (substr($hl,0,4)=='----') { $options .= "<option value='' disabled>&nbsp;_{$hlAinm}_</option>/n"; }  //Divider in the list of select options
            else                       { $options .= "<option value='$hl|en' title='$hlAinm'" . ( $hl==$hl0 ? ' selected' : '' ) . ">$hl</option>\n"; }
      }
      $hrSelect = <<< END_hrSelect
<script>
    function atharraichCanan(hl) {
        document.cookie = 'Thl=' + hl + '; path=/; max-age=15000000';  //Valid for six months
        var paramstr = location.search;
        if (/Trident/.test(navigator.userAgent) || /MSIE/.test(navigator.userAgent)) {
          //Something really weak for Internet Explorer, which doesn’t understand URLSearchParams. Delete when IE is finally dead.
            if (paramstr.length==6 && paramstr.substring(0,4)=='?hl=') { paramstr = ''; }
            paramstr = paramstr;
        } else {
            const params = new URLSearchParams(paramstr)
            params.delete('hl');
            paramstr = params.toString();
            if (paramstr!='') { paramstr = '?'+paramstr; }
        }
        loc = window.location;
        location = loc.protocol + '//' + loc.hostname + loc.pathname + paramstr;
    }
</script>
<select class=hlSelect name="hl" title="$T_interface_language" onchange="atharraichCanan(this.options[this.selectedIndex].value)">
$options</select>
END_hrSelect;
      return $hrSelect;
  }

}
?>
