<?
//自動讀取lib底下的class
include_once("jsnlib.php");
$jsn	= new jsnlib;

//範例一:自動載入
//$jsn->path("../jsnclass")->autoload();
	
//範例二:不自動載入，手動指定，可以做連續串接載入
$jsn->path("../jsnclass")->load('jsnpdo');

//範例三:載入並返回實體化物件。不可連續串接載入
$jsn->path("../jsnclass")->load('jsnpdo', true);



unset($jsn);

//直接使用資料庫吧
$jsnpdo->connect("mysql", "localhost", "ishowmap", "root", "1234");

$DataList = $jsnpdo->select("*", "member", "");

if ($DataList != 0) print_r($DataList);
?>