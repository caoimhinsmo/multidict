<?php
class SM_csNavbar {

  public static function csNavbar($domhan='',$duilleagAghaidh=0) {
      $servername =  $_SERVER['SERVER_NAME'];
      $serverhome = ( empty($_SERVER['HTTPS']) ? 'http' : 'https' ) . '://' . $_SERVER['SERVER_NAME'];
      $hl0 = SM_T::hl0();

      $T = new SM_T('clilstore/navbar');
      $T_homeTitle            = $T->h('homeTitle');
      $T_canan_eadarAghaidh   = $T->h('canan_eadarAghaidh');
      $T_Log_air              = $T->h('Log_air');
      $T_Log_air_fios         = $T->h('Log_air_fios');
      $T_Logout               = $T->h('Logout');
      $T_Log_dheth_fios       = $T->h('Log_dheth_fios');
      $T_tr_fios              = $T->h('tr_fios');
      $T_Clilstore_index_page = $T->h('Clilstore_index_page');

      if ($duilleagAghaidh) { $SmotrCeangal = "<li><a href='/' title='$T_homeTitle'>$servername</a>"; }
        else                { $SmotrCeangal = "<li><a href='/clilstore/' title='$T_Clilstore_index_page'>Clilstore</a>"; }
      $myCLIL = SM_myCLIL::singleton();
      if ($myCLIL->cead(SM_myCLIL::LUCHD_EADARTHEANGACHAIDH))
        { $trPutan = "\n<li class=deas><a href='//www3.smo.uhi.ac.uk/teanga/smotr/tr.php?domhan=$domhan' target='tr' title='$T_tr_fios'>tr</a>"; } else { $trPutan = ''; }
      $ceangalRiMoSMO = ( isset($myCLIL->id)
                        ? "<li class='deas'><a href='/clilstore/logout.php' title='$T_Log_dheth_fios'>$T_Logout</a></li>"
                        : "<li class='deas'><a href='/clilstore/login.php?till_gu=/' title='$T_Log_air_fios'>$T_Log_air</a></li>"
                        );
      $hlArr = array(
          'da'=>'Dansk',
          'en'=>'English',
          'gd'=>'Gàidhlig',
          'it'=>'Italiano',
          'lt'=>'Lietuvių',
            '----1'=>'',  //Partial translations
          'pt'=>'Português',
            '----2'=>'',  //Very partial translations
          'br'=>'Brezhoneg',
          'cy'=>'Cymraeg',
          'de'=>'Deutsch',
          'es'=>'Español',
          'fr'=>'Français',
          'ga'=>'Gaeilge',
          'is'=>'Íslenska',
          'bg'=>'Български');
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
          //Rud lag lag airson seann Internet Explorer, nach eil eòlach air URLSearchParams. Sguab ás nuair a bhios IE marbh.
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
      $csNavbar = <<<EOD_NAVBAR
<ul class="smo-navlist">
$SmotrCeangal
$ceangalRiMoSMO
<li style="float:right" title="$T_canan_eadarAghaidh">$selCanan$trPutan
</ul>
EOD_NAVBAR;
      return $csNavbar;
  }

}
?>
