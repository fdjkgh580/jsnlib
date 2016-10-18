<?

session_start();


include_once("Jsnlegalproccess.php");
$jsnlegalproccess = new Jsnlegalproccess;

//2.插入條件
$jsnlegalproccess->set("B", $_GET['usertext']);



?>
<form action="Demo_3.php" method="get">
	您填的是<input type="text" name="usertext" value="<?=$_GET['usertext']?>">
	<input type="submit" value="前往下一個程序驗證">
</form>
<?
echo "<pre>";
print_r($_SESSION[$jsnlegalproccess->sess]);
echo "</pre>";
?>