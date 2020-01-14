<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");
  header("Cache-Control:max-age=0");

  $T = new SM_T('multidict/languages');

  $T_Error_in   = $T->h('Error_in');
  $T_AltSl_info = $T->h('AltSl_info');
  $T_AltTl_info = $T->h('AltTl_info');
  $T_Languages_handled_by_Multidict = $T->h('Languages_handled_by_Multidict');

  $T_AltSl_info = strtr ( $T_AltSl_info, [ 'AltSl' => '<b>AltSl</b>' ] );
  $T_AltTl_info = strtr ( $T_AltTl_info, [ 'AltTl' => '<b>AltTl</b>' ] );

  $mdNavbar = SM_mdNavbar::mdNavbar($T->domhan);

  function getName ($id) {
      $DbMultidict = SM_DbMultidictPDO::singleton('rw');
      $query = "SELECT priName FROM mt WHERE id=?";
      $stmt = $DbMultidict->prepare($query);
      $stmt->bindParam(1,$id);
      $stmt->execute();
      $stmt->bindColumn(1,$name);
      $stmt->fetch();
      $stmt = null;
      return $name;
  }

  try {
    $DbMultidict = SM_DbMultidictPDO::singleton('rw');

    $HTML = <<<EOD1
<table id=mainTable>
<tr><td>id</td><td>Endonym</td><td>English name</td><td>AltSl</td><td>AltTl</td><td></td><td>Parentage</td></tr>
EOD1;

    $query = "SELECT id, endonym, name_en, parentage FROM langV WHERE id<>'¤' AND id<>'x' ORDER BY parentage_ord";
    $stmt = $DbMultidict->prepare($query);
    $stmt->execute();
    $langinfoAll = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $parentNodes = array('');
    foreach ($langinfoAll as $langInfo) {
        extract($langInfo);
        $stmtSl = $DbMultidict->prepare("SELECT alt FROM langAltSl WHERE id=:id ORDER BY ord");
        $stmtSl->execute([':id'=>$id]);
        $altSl = implode(' ',$stmtSl->fetchAll(PDO::FETCH_COLUMN, 0));

        $stmtTl = $DbMultidict->prepare("SELECT alt FROM langAltTl WHERE id=:id ORDER BY ord");
        $stmtTl->execute([':id'=>$id]);
        $altTl = implode(' ',$stmtTl->fetchAll(PDO::FETCH_COLUMN, 0));

        $prevParentNodes = $parentNodes;
        $parentNodes = explode(':',$parentage);
        if (@$parentNodes[1]<>@$prevParentNodes[1]) {
            $familyName = getName($parentNodes[1]);
            $HTML .= "<tr class=family><td colspan=7>$familyName</td></tr>\n";
        }
        if (@$parentNodes[3]=='lieu' && @$parentNodes[4]<>@$prevParentNodes[4]) {
            $subgroupName = getName($parentNodes[4]);
            $HTML .= "<tr class=subgroup><td colspan=7 style='padding-left:0.7em'>$subgroupName</td></tr>\n";
        }
        $parentNodesHtml = $parentNodes;
        $marked = 0;
        for ($i=0; $i<count($parentNodes); $i++) {
            $node = $nodeHtml = $parentNodes[$i];
            if ($marked==0 && isset($prevParentNodes[$i]) && $node<>$prevParentNodes[$i]) {
                $nodeHtml = "<span class=mark>$node</span>";
                $marked = 1;
            }
            $parentNodesHtml[$i] = "<a href=\"http://multitree.org/codes/$node\">$nodeHtml</a>";
        }
        $parentageHtml = implode (':',$parentNodesHtml);
        $HTML .= <<<END_ROW
<tr>
<td><a href="/multidict/?sl=$id">$id</a></td>
<td>$endonym</td>
<td>$name_en</td>
<td><input type=text title="alternate source languages" value="$altSl" onchange=updateAlt('$id','sl',this)></td>
<td><input type=text title="alternate target languages" value="$altTl" onchange=updateAlt('$id','tl',this)></td>
<td><span id="$id-changed" class=change>✔<span></td>
<td>$parentageHtml</td>
</tr>
END_ROW;
    }
    $HTML .= "</table>\n";

  } catch (Exception $e) { $HTML = $e->getMessage(); }


  echo <<<END_DOC
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>$T_Languages_handled_by_Multidict</title>
    <link rel="StyleSheet" href="/css/smo.css">
    <style>
        div.headings div { font-weight:bold; text-decoration:underline; }
        a { text-decoration:none; }
        a:hover { color:white; background-color:blue; }
        span.mark { background-color:yellow; }
        input[type=text] { background-color:#ffe; width:97%; margin:0; padding:0}
        span.change { opacity:0; color:white; }
        span.change.changed { color:green; animation:appearFade 5s; }
        @keyframes appearFade { from { opacity:1; background-color:yellow; } 20% { opacity:0.8; background-color:transparent; } to { opacity:0; } }
        table#mainTable { border-collapse:collapse; font-size:90%; margin-bottom:1em; }
        table#mainTable tr:first-child { font-weight:bold; text-decoration:underline; }
        table#mainTable tr.family   { border-top:1em solid white;   background-color:#bbf; font-size:150%; font-weight: bold;color:red; }
        table#mainTable tr.subgroup { border-top:0.5em solid white; background-color:#ddf; font-size:130%; font-weight: bold;color:brown; }
        table#mainTable tr:hover { background-color:pink; }
        table#mainTable tr td:nth-child(1) { width:4.7em; font-weight:bold; }
        table#mainTable tr td:nth-child(2) { width:14em;font-weight:bold; }
        table#mainTable tr td:nth-child(3) { width:11em; }
        table#mainTable tr td:nth-child(4) { width:8em; }
        table#mainTable tr td:nth-child(5) { width:8em; }
        table#mainTable tr td:nth-child(6) { width:1.5em; }
        table#mainTable tr td:nth-child(7) { font-size:85%; }
    </style>
    <script>
        function updateAlt(id,sltl,el) {
            var altList = el.value.trim().replace(/  /g,' ').replace(/ /g,'|');
            var xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
                if (this.status!=200) { alert('$T_Error_in updateAlt:'+this.status); return; }
                var resp = this.responseText;
                if (resp!='OK' && resp!='') { alert(resp); return; }
                var tickel = document.getElementById(id+'-changed');
                tickel.classList.remove('changed'); //Remove the class (if required) and add again after a tiny delay, to restart the animation
                setTimeout(function(){tickel.classList.add('changed');},50);
            }
            var url = window.location.origin + '/multidict/ajax/updateAlt.php';
            var params = 'id=' + id + '&sltl=' + sltl + '&altList=' + altList;
            xhttp.open('POST',url,true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(params);
        }
    </script>
</head>
<body style="background-color:white">
$mdNavbar
<div class='smo-body-indent'>

<h1>Languages handled by Multidict</h1>

<div style="margin-bottom:3em;padding:0 0.6em;border:1px solid green;border-radius:0.5em;background-color:#dfd;max-width:75em">
<p style='margin-top:0.5em'>$T_AltSl_info</p>
<p style='margin-bottom:0.5em'>$T_AltTl_info</p>
</div>

$HTML

</div>
$mdNavbar
</body>
</html>
END_DOC;

?>
