<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Arabic root</title>
</head>
<body>

<div style="text-align:center;color:#aaa;font-size:150%">
<?php
  $document_root = $_SERVER['DOCUMENT_ROOT'];
  $word = $_GET['word'];
  if (empty($word)) {
    echo "<p>This page requires a  ?word=...  paramter</p>";
  } else {
    echo "<p>Arabic wordform<br><span style=\"color:black;font-size:170%\">$word</span></p>\n";
    $root = shell_exec("python3 $document_root/multidict/arroot.py $word");
    $rootLetters = preg_split("//u", $root, -1, PREG_SPLIT_NO_EMPTY);
    $rootLettersStr = implode(' ',$rootLetters);
    echo "<p>has root<br><span style=\"color:black;font-size:400%\">$root<br>$rootLettersStr</span></p>";
    echo "<p style=\"margin-top:3em;font-size:70%\">(as evaluated by the module <a href=\"http://www.nltk.org/_modules/nltk/stem/isri.html\" style=\"color:#888\">nltk.stem.isri</a>)</p>";
  }
?>
</div>

</body>
</html>
