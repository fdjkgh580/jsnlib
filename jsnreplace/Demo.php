<?
include_once('jsnreplace.php');
$jsnreplace = new Jsnreplace;

$DEMO = '<a href="../../img.jpg">連結</a><a href="../../ss.jpg">連結</a>';
$DEMO_PHP = '<a href="<?=site_url(HELLOW.jpg)?>">連結</a><a href="../../WORD.jpg">連結</a>';


echo "取得區間內的字串<br>";
echo $jsnreplace->rang($DEMO, 'href="', '"');
echo "<hr />";

echo "開始轉換包圍<br>";
echo $jsnreplace->wrap($DEMO, 'href="', 'href="<?=site_url(', '"', ');?>"');
echo "<hr />";


echo "開始轉換包圍 + 取代<br>";
echo $jsnreplace->wrap_replace($DEMO, 'href="', 'href="<?=site_url(', '"', ');?>"', '../', NULL);
echo "<hr />";


echo "開始轉換包圍 + 取代, 並排除指定的token<br>";
$jsnreplace->exclude_token = "=site_url(";
echo $jsnreplace->wrap_replace($DEMO_PHP, 'href="', 'href="<?=site_url(', '"', ');?>"', '../', NULL);
echo "<hr />";


?>
