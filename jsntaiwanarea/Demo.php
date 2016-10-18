<?
include_once("jsntaiwanarea.php");
$jsntaiwanarea = new Jsntaiwanarea;

//1.查詢城市編號, 得到台北市
echo "查詢1001<br>";
echo $jsntaiwanarea->city('1001') . "<hr>";

//2.查詢城市編號與區域編號, 得到台北市中正區
echo "查詢1001, 100<br>";
echo $jsntaiwanarea->area('1001', '100') . "<hr>";

//3.查詢城市編號底下所有區域, 得到高雄市底下的所有區域陣列
echo "取得1016所有的區域<br>";
$Kao = $jsntaiwanarea->all_area('1016');
print_r($Kao);
echo "<hr>";


//4.查詢城市名稱關鍵字"高", 得到城市陣列
echo "取得關鍵字 台 的所有城市<br>";
$ary = $jsntaiwanarea->search("city", "高");
print_r($ary);
echo "<hr>";

//5.查詢區域關鍵字"平", 得到所有區域陣列
echo "取得關鍵字 平 的所有區域<br>";
$ary = $jsntaiwanarea->search("area", "平");
print_r($ary);
echo "<hr>";

?>