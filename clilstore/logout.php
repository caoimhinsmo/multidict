<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header("Cache-Control:max-age=0");

  try {
    $myCLIL = SM_myCLIL::singleton();
    $myCLIL::logout();
    $servername = SM_myCLIL::servername();
    $serverhome = SM_myCLIL::serverhome();

    $T = new SM_T('clilstore/logout');
    $T_You_have_been_logged_out = $T->h('You_have_been_logged_out');

    echo <<<EOD1
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logout from Clilstore</title>
    <meta http-equiv="refresh" content="2; url=$serverhome/clilstore/">
    <link rel="icon" type="image/png" href="/favicons/clilstore.png">
</head>
<body>

<p><img src="/icons-smo/wave.gif" alt=""> $T_You_have_been_logged_out</p>

</body>
</html>
EOD1;

  } catch (Exception $e) { echo $e; }

?>
