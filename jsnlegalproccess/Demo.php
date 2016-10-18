<?
//0. 使用session
session_start();


include_once("Jsnlegalproccess.php");
$jsnlegalproccess = new Jsnlegalproccess;

//1. 插入條件
$jsnlegalproccess->start()->set("A", time());

?>
<form action="Demo_2.php" method="get">
	<div>填寫 1234 是正確的</div>
	<input type="text" name="usertext" value="" placeholder="原始值">
	<input type="submit" value="前往下一個程序驗證">
</form>
<?
echo "<pre>";
print_r($_SESSION[$jsnlegalproccess->sess]);
echo "</pre>"
?>