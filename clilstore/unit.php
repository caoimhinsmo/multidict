<?php
// Takes an id= parameter, looks up the language in the Clilstore database
// and displays the Wordlinked unit by redirecting to Wordlink with approriate parameters.
  if (!include('autoload.inc.php'))
    header("Location:https://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  try {
      $myCLIL = SM_myCLIL::singleton();
      $user = ( isset($myCLIL->id) ? $myCLIL->id : '' );
  } catch (Exception $e) {
      $myCLIL->toradh = $e->getMessage();
  }

  try {
    if (!isset($_GET['id'])) { throw new SM_MDexception('No id parameter'); }
    $id = $_GET['id'];
    if (!is_numeric($id)) { throw new SM_MDexception('id parameter is not numeric'); }

    $DbMultidict = SM_DbMultidictPDO::singleton('rw');
    $query = 'SELECT sl FROM clilstore WHERE id=:id';
    $stmt = $DbMultidict->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $stmt->bindColumn(1,$sl);
    if (!$stmt->fetch()) { throw new SM_MDexception("No unit exists for id=$id"); }
    $stmt = null;
    
    $servername = $_SERVER['SERVER_NAME'];
    $scheme = ( empty($_SERVER['HTTPS']) ? 'http' : 'https' );
    $server = "$scheme://$servername";
    $userparam = ( empty($user) ? '' : "{and}user=$user" ); //Send userparam to generate edit button (since Wordlink doesn't do cookies)
    header("Location:$server/wordlink/?navsize=1&sl=$sl&url=$server/clilstore/page.php?id=$id$userparam");

  } catch (Exception $e) { echo $e; }

?>
