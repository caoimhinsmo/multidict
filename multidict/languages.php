<?php
  if (!include('autoload.inc.php'))
    header("Location:http://claran.smo.uhi.ac.uk/mearachd/include_a_dhith/?faidhle=autoload.inc.php");
  header("Cache-Control:max-age=0");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Languages handled by Multidict</title>
    <style type="text/css">
        div.headings div { font-weight:bold; text-decoration:underline; }
        p.family   { margin:2em 0 0 0; background-color:#bbf; font-size:150%; font-weight: bold;color:red; }
        p.subgroup { margin:1.5em 0 0 0; background-color:#ddf; font-size:130%; font-weight: bold;color:brown; padding-left:0.3em;   }
        a { text-decoration:none; }
        a:hover { color:white; background-color:blue; }
        span.mark { background-color:yellow; }
        input[type=text] { background-color:#ffe; width:97%; margin:0; padding:0}
        input[type=submit]:hover { background-color:blue; color:white; }
    </style>
</head>
<body>

<h1>Languages handled by Multidict</h1>

<div style="margin:0 2px 3em 2px;border:1px solid green;background-color:#dfd;padding:3px">
<p>“<b>AltSl</b>” indicates those languages which are similar enough, both in meaning and in spelling (e.g. Danish compared to Norwegian Bokmål),
to be used as “alternate source languages”.  That is to say, you could try looking up a word in their dictionaries and have a reasonable chance
of success.</p>

<p>“<b>AltTl</b>” indicates those languages which are similar enough in word meanings (though not necessarily in spelling) to be used
as “alternate target languages”.  They could be understood and provide meaning or inspiration to a speaker of the language in question.
For example, a speaker of Serbian who was trying to read the Italian Wikipedia might find an Italian to Croatian dictionary useful.
Or a Scottish Gaelic speaker trying to think of a suitable Gaelic word might find an English to Manx dictionary useful as inspiration.</p>

<p>You can help by filling in any suitable AltSl and AltTl for languages you are familiar with.  List the language codes (from column 1)
in order of increasing distance and click “Update” (one row at a time).  Or if you think you know better than the languages already listed,
feel free to alter them.  But remember that this will change Multidict for all users, so be very careful.</p>
</div>

<?php
  function checkValid($id) {
      if (!SM_WlSession::langValid($id))
        { throw new SM_MDexception("rabhadh|Update ignored because Multidict did not recognise language code &ldquo;$id&rdquo;"); }
  }

  function getAlts($param) {
      $param = strtr($param,array('  '=>' '));
      if (empty($param) || $param==' ') { return array(); }
      $arr = explode(' ',trim($param));
      return $arr;
  }

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

    if (!empty($_GET['id'])) {
        try {
            $id    = $_GET['id'];
            $altSlArr = getAlts($_GET['altsl']);
            $altTlArr = getAlts($_GET['alttl']);
            checkValid($id);
            foreach ($altSlArr as $alt) { checkValid($alt); }
            foreach ($altTlArr as $alt) { checkValid($alt); }
            $stmt = $DbMultidict->prepare("DELETE FROM langAltSl WHERE id=?");
            $stmt->execute(array($id));
            $stmt = $DbMultidict->prepare("DELETE FROM langAltTl WHERE id=?");
            $stmt->execute(array($id));
            $stmt = $DbMultidict->prepare("INSERT INTO langAltSl(id,alt,ord) VALUES(?,?,?)");
            for ($i=0;$i<count($altSlArr);$i++) { $stmt->execute(array($id,$altSlArr[$i],$i)); }
            $stmt = $DbMultidict->prepare("INSERT INTO langAltTl(id,alt,ord) VALUES(?,?,?)");
            for ($i=0;$i<count($altTlArr);$i++) { $stmt->execute(array($id,$altTlArr[$i],$i)); }
            $stmt = null;
        } catch (Exception $e) { echo $e; }
    }

    echo <<<EOD1
<div class="headings" style="padding-bottom:1px;font-size:85%">
  <div style="float:left;width:5.5em">Id</div>
  <div style="float:left;width:14.5em">Endonym</div>
  <div style="float:left;width:11.5em">English name</div>
  <div style="float:left;width:110px">AltSl</div>
  <div style="float:left;width:140px">AltTl</div>
  <div style="float:left;width:5em;margin:0 3px 0 0">&nbsp</div>
  <div>Parentage</div>
</div>
EOD1;

    $query = "SELECT id, endonym, name_en, parentage FROM langV ORDER BY parentage_ord";
    $stmt = $DbMultidict->prepare($query);
    $stmt->execute();
    $stmt->bindColumn(1,$id);
    $stmt->bindColumn(2,$endonym);
    $stmt->bindColumn(3,$name_en);
    $stmt->bindColumn(4,$parentage);
    $parentNodes = array('');
    while ($stmt->fetch()) {
        if ($id=='¤' || $id=='x') { continue; }
        $stmtSl = $DbMultidict->prepare("SELECT alt FROM langAltSl WHERE id=:id ORDER BY ord");
        $stmtSl->bindParam(':id',$id);
        $stmtSl->execute();
        $altSl = implode(' ',$stmtSl->fetchAll(PDO::FETCH_COLUMN, 0));

        if ($id=='¤' || $id=='x') { continue; }
        $stmtTl = $DbMultidict->prepare("SELECT alt FROM langAltTl WHERE id=:id ORDER BY ord");
        $stmtTl->bindParam(':id',$id);
        $stmtTl->execute();
        $altTl = implode(' ',$stmtTl->fetchAll(PDO::FETCH_COLUMN, 0));

        $prevParentNodes = $parentNodes;
        $parentNodes = explode(':',$parentage);
        if (@$parentNodes[1]<>@$prevParentNodes[1]) {
            echo '<p class="family">' . getName($parentNodes[1]) ."</p>\n";
        }
        if (@$parentNodes[3]=='lieu' && @$parentNodes[4]<>@$prevParentNodes[4]) {
            echo '<p class="subgroup">' . getName($parentNodes[4]) ."</p>\n";
        }
        $parentNodesHtml = $parentNodes;
        $marked = 0;
        for ($i=0; $i<count($parentNodes); $i++) {
            $node = $nodeHtml = $parentNodes[$i];
            if ($marked==0 && isset($prevParentNodes[$i]) && $node<>$prevParentNodes[$i]) {
                $nodeHtml = "<span class=\"mark\">$node</span>";
                $marked = 1;
            }
            $parentNodesHtml[$i] = "<a href=\"http://multitree.org/codes/$node\">$nodeHtml</a>";
        }
        $parentageHtml = implode (':',$parentNodesHtml);
        echo <<<EOD2
<form action="" method="get" style="margin:0;padding:0 2px 1px 2px;border-bottom:1px solid grey;background-color:#ffd;clear:both;font-size:85%">
  <div style="float:left;width:5.5em;font-weight:bold"><a href="/multidict/?sl=$id" target="_top">$id</a></div>
  <div style="float:left;width:14.5em;font-weight:bold">$endonym</div>
  <div style="float:left;width:11.5em">$name_en</div>
  <div><input type="hidden" name="id" value="$id"/></div>
  <div style="float:left;width:110px"><input type="text" name="altsl" title="alternate source languages" value="$altSl"/></div>
  <div style="float:left;width:140px"><input type="text" name="alttl" title="alternate target languages" value="$altTl"/></div>
  <div><input type="submit" value="Update" style="float:left;width:5em;margin:0 3px 0 0"></div>
  <div><span style="font-size:80%">$parentageHtml</span></div>
</form>
EOD2;
    }
    $stmt = null;

  } catch (Exception $e) { echo $e; }
?>

</body>
</html>
