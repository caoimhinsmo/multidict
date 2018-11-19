<?php
// With paramater dict=, returns an icon for that dictionary.
// Alternatively, with parameter lang=, returns an icon for that language.
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  try {
    if (isset($_GET['dict'])) {
        $dict = $_GET['dict'];
        SM_WlSession::getDictIcon($dict,$icon,$mimetype);
    } elseif ( isset($_GET['lang'])) {
        $lang = $_GET['lang'];
        SM_WlSession::getLangIcon($lang,$icon,$mimetype);
    } else {
        return;
    }
    if (!empty($icon)) {
        header("Content-type: $mimetype");
        header("Cache-control: max-age=86400");
        echo $icon;
    }
  } catch (Exception $e) { echo $e; }
?>
