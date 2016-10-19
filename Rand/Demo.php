<?
header("Content-type: text/html; charset=utf-8");
include_once("Rand.php");

$Rand = new Jsnlib\Rand;

//單一亂數字串
echo $Rand->get(15, "1,2,3");

//多筆亂數字串
$ary = $Rand->get_np(10, 15,"1,2");
foreach ($ary as $val) {
	echo $val."<br>";
	}

?>