<?
include_once("Jsnlog.php");
include_once("../jsnao/jsnao.php");
include_once("../jsnpdo/jsnpdo.php");


$init->jsnpdo       = new jsnpdo;
$init->sql_database = "mysql";
$init->hostname     = "localhost";
$init->dbname       = "ci_jsn";
$init->user         = "root";
$init->password     = "1234";

$jsnlog = new jsnlog();
$jsnlog->connect($init);

//初次自動建立新資料表
    // $jsnlog->create_table();

//物件
    $obj->first     = "第一順位";
    $obj->second    = "第二順位";
    $jsnlog->set($obj, "notice");
    echo $jsnlog->get("all");

//陣列
    $ary['first']   = "星期一";
    $ary['second']  = "星期二";
    $jsnlog->set($ary, "notice");
    echo $jsnlog->get("all");

//一般字串或數字
    $jsnlog->set("文字訊息");
    echo $jsnlog->get("all");