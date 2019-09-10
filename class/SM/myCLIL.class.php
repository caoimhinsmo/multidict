<?php
class SM_myCLIL {
// This class was produced from the SMO class moSMO, so there may be relics of Gaelic language in it,
// as well as stuff which is not at the moment relevant to clilstore

  public $toradh, $id, $expiry, $toiseach, $feart;

  private function __construct() {
      if (!isset($_COOKIE['myCLIL_authentication'])) { $this->toradh=''; return; }
      list($hash,$message) = explode('&',$_COOKIE['myCLIL_authentication'],2);
      $myCLIL_nonce = 'C2iI0l[*';
      if (md5($myCLIL_nonce.$message)!=$hash) { $this->toradh='sgrios|The cookie myCLIL_authentication is invalid'; return; }
      list($expiry,$id,$_feartan,$toiseach) = explode('&',$message);
      if ($expiry<time()) { $toradh=''; return; }
      $this->id       = $id;
      $this->expiry   = $expiry;
      $this->toiseach = $toiseach;
      foreach (explode(':',$_feartan) as $feart_str) {
         if (preg_match('/^(\w+?)(\d+?)$/',$feart_str,$matches)) { 
            $this->feart[$matches[1]] = $matches[2];
         }
      }
      $this->id3 = $id;
      if (!empty($_COOKIE['myCLIL_authentication2'])) {
          list($hash2,$message2) = explode('&',$_COOKIE['myCLIL_authentication2'],2);
          if (md5($myCLIL_nonce.$message2)!=$hash2) { $this->toradh='sgrios|The cookie myCLIL_authentication2 is invalid'; return; }
          list($expiry2,$id2,$_feartan2,$toiseach2) = explode('&',$message2);
          foreach (explode(':',$_feartan2) as $feart_str) {
             if (preg_match('/^(\w+?)(\d+?)$/',$feart_str,$matches)) { 
                $this->feart2[$matches[1]] = $matches[2];
             }
          }
          if (!empty($id2)) {
              $this->id2 = $id2;
              if (substr(!$this->id2,0,1)<>'{') { $this->id3 = $this->id2; }
          }
      }
  }

  private function __clone() {}

  private static $_instance;
  public static function singleton() {
      if (self::$_instance === null) { self::$_instance = new self; }
      return self::$_instance;
  }


  public function ceadid($ceadaichte,$id,$feart) {
    try {
      if ($id=='1991-cpd') { return 1; }
      $ceadaichte = trim($ceadaichte);
      if (substr($ceadaichte,0,1)=='!') { return !self::ceadid(substr($ceadaichte,1),$id); }
      if (substr($ceadaichte,0,1)=='(') {
          $nbrackets = 1;
          for ($i=1;$i<strlen($ceadaichte);$i++) {
              if      (substr($ceadaichte,$i,1)=='(') { $nbrackets++; }
               elseif (substr($ceadaichte,$i,1)==')') { $nbrackets--; }
              if ($nbrackets==0) { break; }
          }
          if ($nbrackets>0) { throw new SM_MDexception('Mearachd air an duilleig - a thaobh cheadan'); }
          $cli = substr($ceadaichte,1,$i-1);
          $deas = trim(substr($ceadaichte,$i+1));
          if (empty($deas)) { return self::ceadid($cli,$id); }
           elseif (substr($deas,0,1)=='|') { return self::ceadid($cli,$id)||self::ceadid(substr($deas,1),$id); }
           elseif (substr($deas,0,1)=='&') { return self::ceadid($cli,$id)&&self::ceadid(substr($deas,1),$id); }
           else                            { throw new SM_MDexception('Mearachd air an duilleig - a thaobh cheadan'); }
      }
      if (preg_match('/^(.*?)\|(.*)$/u',$ceadaichte,$matches)) { return self::ceadid($matches[1],$id,$feart) || self::ceadid($matches[2],$id,$feart); }
      if (preg_match('/^(.*?)\&(.*)$/u',$ceadaichte,$matches)) { return self::ceadid($matches[1],$id,$feart) && self::ceadid($matches[2],$id,$feart); }
      if ($ceadaichte=='{logged-in}') {
          if (!isset($id))       { return 0; }
           elseif ($id=='{www}') { return 0; }
           else                  { return 1; }
      } elseif ($ceadaichte==$id) {
          return 1;
      } elseif ($ceadaichte=='{oileanach}') {
          if (isset($feart['oil'])) { return 1; }
           else                     { return 0; }
      }
      return 0;
    } catch (Exception $e) { return 0; } 
  }

  public function cead($ceadaichte) {
      $result = self::ceadid($ceadaichte,$this->id,$this->feart);
      if (isset($this->id2)) { $result = $result && self::ceadid($ceadaichte,$this->id2,$this->feart2); }
      return $result;
  }

  public function diultadh($brath) {
      header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  //Àm a chaidh seachad = no caching
      $this->toradh = 'sgrios|diultadhCS|Sorry. You are not allowed to view this page';
      if ($this->id=='') {
          $this->toradh .= '<br>because you are not logged in to Clilstore';
      } else {
          $this->toradh .= '<br>(You are logged in to Clilstore as &ldquo;' . $this->id . '&rdquo;)';
          if (!empty($this->id2)) { $this->toradh
                        .= "<br>(And you are currently emulating user &ldquo;" . $this->id2 . '&rdquo; - Faic: <a href="roghainnean.php">roghainnean</a>)'; }
      }
      if ($brath>'') { $this->toradh .= "<br>$brath"; }
  }


  public function dearbhaich() {
      $mirean = explode('|',$this->toradh,2);
      if ($mirean[0]=='sgrios')  { throw new SM_MDexception($this->toradh); }
      $uine = $this->expiry - time();
      if ($uine<120 and isset($this->id)) {
          echo '<p style="border:solid 1px red;color:red;background-color:#fcc;padding:0.2em;text-align:center;">'
              ."You only have $uine seconds left on myCLIL.&nbsp;&nbsp;It would be wise to log in again</p>\n";
      }
  }

  public static function cuirCookie($cookie_name,$smid,$cookie_expire,$uine) {
      //Obraich a-mach feartan airson an cuir sa cookie
      $feartan = '';
      $myCLIL_nonce = 'C2iI0l[*';
      $message = (time()+$uine)."&".$smid."&".$feartan."&".time();
      $hash = md5($myCLIL_nonce.$message);
      setcookie($cookie_name,
                $hash."&".$message,
                $cookie_expire,
                '/'
               );
  }

  public static function logout() {
      setcookie('myCLIL_authentication','',1,'/');  //unset cookie
  }

  public static function servername() {
      return $_SERVER['SERVER_NAME'];
  }
  public static function serverhome() {
      return ( $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://' . $_SERVER['SERVER_NAME'];
  }

  public function fullname($user='') {
      if (empty($user)) { $user = $this->id; }
      $DbMultidict = SM_DbMultidictPDO::singleton('r');
      $stmt = $DbMultidict->prepare('SELECT fullname FROM users WHERE user=:user');
      $stmt->bindParam(':user',$user);
      $stmt->execute();
      $fullname = $stmt->fetchColumn();
      $stmt = null;
      return $fullname;
  }

  public function email($user='') {
      if (empty($user)) { $user = $this->id; }
      $DbMultidict = SM_DbMultidictPDO::singleton('r');
      $stmt = $DbMultidict->prepare('SELECT email FROM users WHERE user=:user');
      $stmt->bindParam(':user',$user);
      $stmt->execute();
      $email = $stmt->fetchColumn();
      $stmt = null;
      return $email;
  }

}
?>
