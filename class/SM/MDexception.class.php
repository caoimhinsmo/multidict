<?php
class SM_MDexception extends Exception {

  public function __toString() {
    $sm_toradh = $this->message;
    @list($inbhe,$brath) = explode('|',$sm_toradh,2);
    if (!($brath>'')) { $brath=$inbhe; $inbhe='sgrios'; } //Cha robh còir seo a thachairt ma bhios am prògramadh ceart
    if ($inbhe=='sgrios') {
       $html = "<br style=\"clear:both\"/>\n";
       if (strpos($brath,'|')) { list($fath,$brath2) = explode('|',$brath,2); }
        else                   { $fath = ''; }
       if ($fath=="oidhirp") {
          $html .= "<div style=\"border:solid 2px green;margin:0.5em;padding:0.5em;color:green;font-weight:bold\">\n";
          $html .= '<p style="border:solid 1px red;padding:0.5em;color:red;background-color:#fcc">';
          $html .= "Development work is in progress.<br/>\n";
          $html .= "It may be best to leave things and try again after a short while.<br/>\n";
          $html .= "However, if this situation persists, please report it.</p>\n";
          $html .= "<p style=\"font-size:120%\">Oidhirp: $brath2</p>\n";
       } elseif ($fath=="diultadh") {
          $html .= "<div style=\"border:solid 1px red;margin:0.5em;padding:0.4em;color:red\">\n";
          $html .= "<p style=\"font-size:110%;margin:0\">Refused: $brath2</p>\n";
          $login_uri = "https://login.smo.uhi.ac.uk/?till_gu=https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
          $html .= "<p style=\"float:left;padding:0.3em;border:solid 2px green;background-color:#cff;font-size:160%\"><a href=\"$login_uri\">Log a-steach</a></p>\n";
       } elseif ($fath=="diultadhCS") {
          $html .= "<div style=\"border:solid 1px red;margin:0.5em;padding:0.4em;color:red\">\n";
          $html .= "<p style=\"font-size:110%;margin:0\">Refused: $brath2</p>\n";
          $login_uri = "https://www2.smo.uhi.ac.uk/clilstore/login.php?till_gu=https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
          $html .= "<p style=\"float:left;padding:0.3em;border:solid 2px green;background-color:#cff;font-size:160%\"><a href=\"$login_uri\">Login to Clilstore</a></p>\n";
       } elseif ($fath=="bog") {
          $html .= "<div style=\"border:solid 1px #f20;margin:0.5em;padding:0.5em;background-color:#fdd;color:#f20;font-weight:bold\">\n";
          $html .= "<p style=\"font-size:120%\"><img src=\"/icons-smo/bronach.gif\" alt=\"\"/> $brath2</p>\n";
       } else {
          $html .= "<div style=\"border:solid 4px red;margin:0.5em;padding:0.5em;background-color:#faa;color:red;font-weight:bold\">\n";
          $html .= "<p style=\"font-size:120%\">Fatal error: $brath</p>\n";
          $html .= "<p>Please report this fault if you can</p>\n";
       }
    } elseif ($inbhe=='rabhadh') {
       $html .= "<div style=\"clear:both;border:solid 1px #c60;margin:0.2em;padding:0.2em;background-color:#fda;color:#c60\">\n";
       $html .= "<p style=\"margin:0.2em\"><b>Rabhadh:</b> $brath</p>\n";
    }
    $html .= "</div>\n";
    return $html;
  }

}
?>
