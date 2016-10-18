<?

session_start();


include_once("Jsnlegalproccess.php");
$jsnlegalproccess = new Jsnlegalproccess;


//2.驗證流程與字串的方法
// 不設定值代表不執行比對
$result = $jsnlegalproccess->chk(array(
	'A' => NULL, 
	'B' => '1234'
	));
$obj = json_decode($result);
echo $obj->message;

?>

<?
echo "<pre>";
print_r($_SESSION[$jsnlegalproccess->sess]);
echo "</pre>";
?>