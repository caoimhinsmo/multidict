<?php if (!include('autoload.inc.php'))
  header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");

  header('Cache-Control: no-cache, no-store, must-revalidate');
  header("Cache-Control:max-age=0");

  $T = new SM_T('clilstore/serverhome');

  $T_Clilstore_studentWelcome = $T->h('Clilstore_studentWelcome');
  $T_Clilstore_teacherWelcome = $T->h('Clilstore_teacherWelcome');
  $T_Wordlink_welcome         = $T->h('Wordlink_welcome');
  $T_Multidict_welcome        = $T->h('Multidict_welcome');
  $T_Disclaimer               = $T->h('Disclaimer');
  $T_Disclaimer_EuropeanCom   = $T->h('Disclaimer_EuropeanCom');

  $EUlogo = '/EUlogos/' . SM_T::hl0() . '.jpg';
  if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $EUlogo)) { $EUlogo = '/EUlogos/en.jpg'; }

echo <<< END_html
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Multidict, Wordlink and Clilstore - Tools for CLIL</title>
<meta name="description" content="Three tools for language learners.  Multidict can link to online dictionaries in hundreds of languages.  Wordlink links (nearly) any webpage word by word to online dictionaries.  Clilstore is a store of audiovisual learning units, with every word linked automatically to dictionaries.">
<link href="lone.css" rel="stylesheet">
<style>
#apDiv1 {
	position: absolute;
	width: 39px;
	height: 21px;
	z-index: 1;
	left: 5px;
	top: 87px;
}
#apDiv2 {
	position: absolute;
	width: 39px;
	height: 21px;
	z-index: 1;
	left: 4px;
	top: 126px;
}
#apDiv3 {
	position: absolute;
	width: 39px;
	height: 21px;
	z-index: 1;
	left: 4px;
	top: 353px;
}
#apDiv4 {
	position: absolute;
	width: 39px;
	height: 21px;
	z-index: 1;
	left: -24px;
	top: -24px;
}
</style>
</head>

<body>

<div id="master">
<!--<span style="color:red;background-color:yellow">News: Service disruption likely on Friday evening, 4 March, 17:00-21:00 UT</span>-->
<div id="apDiv1"><a href="/clilstore/?mode=0"><img src="lonelogo/pil-blue.png" alt="" style="width:17px;height:17px;border:0"></a></div>
<div id="apDiv2"><a href="/clilstore/?mode=2"><img src="lonelogo/pil-blue.png" alt="" style="width:17px;height:17px;border:0"></a></div>
  <div id="clilstore"> <a href="/clilstore/?mode=0"><img src="lonelogo/clilstore-blue.png" style="width:217px;height:81px;border:0" alt="Clilstore"></a>
    <div id="cliltext">
      <p style='padding-left:5em;text-indent:-5em'>$T_Clilstore_studentWelcome
      <p><br>
      <p style='padding-left:5em;text-indent:-5em'>$T_Clilstore_teacherWelcome
    </div>
    <div class="rule1"></div>
  </div>
  <div id="wordlink"><a href="/wordlink/"><img src="lonelogo/wordlink-blue.png" style="width:252px;height:80px;border:0" alt="Wordlink"></a>
    <div id="wordtext">$T_Wordlink_welcome</div>
    <div id="rule1"></div>
  </div>
  <div id="apDiv3"><a href="/multidict/"><img src="lonelogo/pil-blue.png" alt="" style="width:17px;height:17px;border:0"></a></div>
  <div id="multidict"><a href="/multidict/"><img src="lonelogo/multidict-blue.png" style="width:250px;height:111px;border:0" alt="Multidict"></a>
  <div id="apDiv4"><a href="/wordlink/"><img src="lonelogo/pil-blue.png" alt="" style="width:17px;height:17px;border:0"></a></div>
    <div id="multitext">$T_Multidict_welcome</div>
  </div>
  <div id="disclaimer"><a href="http://eacea.ec.europa.eu/llp/index_en.php"><img src="$EUlogo" style="width:275px;height:60px;border:0" alt="logo"></a></div>
  <div id="distekst"><i>$T_Disclaimer:</i> $T_Disclaimer_EuropeanCom</div>
</div>
</body>
</html>
END_html;

?>
