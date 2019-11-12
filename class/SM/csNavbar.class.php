<?php
class SM_csNavbar {

  public static function csNavbar($domhan='',$duilleagAghaidh=0) {
      $servername =  $_SERVER['SERVER_NAME'];
      $serverhome = ( $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://' . $_SERVER['SERVER_NAME'];
      $smohl = SM_T::hl0();
      $T = new SM_T('clilstore/navbar');
      $T_homeTitle          = $T->_('homeTitle');
      $T_canan_eadarAghaidh = $T->_('canan_eadarAghaidh','hsc');
      $T_Log_air            = $T->_('Log_air','hsc');
      $T_Log_air_fios       = $T->_('Log_air_fios','hsc');
      $T_Log_dheth_fios     = $T->_('Log_dheth_fios','hsc');
      $T_tr_fios            = $T->_('tr_fios','hsc');
      if ($duilleagAghaidh) { $SmotrCeangal = "<li><a href='/toisich/' title='Sabhal Mór Ostaig - prìomh dhuilleag (le dà briog)'>SMO</a>"; }
        else                { $SmotrCeangal = "<li><a href='$serverhome' title='$T_homeTitle'>$servername</a>"; }
      $myCLIL = SM_myCLIL::singleton();
      if ($myCLIL->cead(SM_myCLIL::LUCHD_EADARTHEANGACHAIDH))
        { $trPutan = "\n<li class=deas><a href='//www3.smo.uhi.ac.uk/teanga/smotr/tr.php?domhan=$domhan' target='tr' title='$T_tr_fios'>tr</a>"; } else { $trPutan = ''; }
      $ceangalRiMoSMO = ( isset($myCLIL->id)
                        ? "<li class='deas'><a href='/clilstore/logout.php' title='$T_Log_dheth_fios'>Logout</a></li>"
                        : "<li class='deas'><a href='/clilstore/login.php?till_gu=/' title='$T_Log_air_fios'>$T_Log_air</a></li>"
                        );
      $hlArr = array(
          'br'=>'Brezhoneg',
          'de'=>'Deutsch',
          'en'=>'English',
          'fr'=>'Français',
          'ga'=>'Gaeilge',
          'gd'=>'Gàidhlig',
          'cy'=>'Cymraeg (anorffenedig)',
          'da'=>'Dansk (ufuldstændig)',
          'es'=>'Español (incompleto)',
          'it'=>'Italiano (incompleto)',
          'bg'=>'Български (непълен)');
      $options = '';
      foreach ($hlArr as $hl=>$hlAinm) { $options .= "<option value='$hl|en'" . ( $hl==$smohl ? ' selected' : '' ) . ">$hlAinm</option>\n"; }
      $selCanan = <<< END_selCanan
<script>
    function atharraichCanan(hl) {
        document.cookie='smohl='+hl;
        const params = new URLSearchParams(location.search)
        params.delete('hl');
        var paramstr = params.toString();
        if (paramstr!='') { paramstr = '?'+paramstr; }
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
