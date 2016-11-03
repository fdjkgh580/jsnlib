<?
require_once '../../vendor/autoload.php';

$taiwanarea = new Jsnlib\Taiwan\Area;

//所有城市
echo "所有城市<br>";
$citylist = $taiwanarea->city()->get();
print_r($citylist);
echo "<hr>";


//查詢城市編號, 得到台北市
echo "查詢1001<br>";
echo $taiwanarea->city('1001')->get() . "<hr>";


//查詢城市編號與區域編號, 得到 中正區
echo "查詢1001, 100<br>";
echo $taiwanarea->city('1001')->area('100')->get() . "<hr>";

//查詢城市編號底下所有區域, 得到高雄市底下的所有區域陣列
echo "取得1016所有的區域<br>";
$arealist = $taiwanarea->city('1016')->area()->get();
print_r($arealist);
echo "<hr>";

//查詢城市名稱關鍵字"高", 得到城市陣列
echo "取得關鍵字 台 的所有城市<br>";
// $ary = $taiwanarea->search("city", "高");
// print_r($ary);
// echo "<hr>";
$list = $taiwanarea->city()->search("高")->get();
print_r($list);
echo "<hr>";


//查詢區域關鍵字"平", 得到所有區域陣列
echo "取得關鍵字 平 的所有區域<br>";
$ary = $taiwanarea->city(1003)->area()->search("平")->get();
print_r($ary);
echo "<hr>";

?>