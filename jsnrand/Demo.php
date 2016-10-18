<?
header("Content-type: text/html; charset=utf-8");
include_once("jsnrand.php");

$rand = new Jsnrand;

//單一亂數字串
echo $rand->get(15, "1,2,3");

//多筆亂數字串
$ary = $rand->get_np(10, 15,"1,2");
foreach ($ary as $val) {
	echo $val."<br>";
	}

?>