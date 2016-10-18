<?
session_start();
header("Content-type: text/html; charset=utf-8");
include_once("../jsnpdo/jsnpdo.php");
include_once("jsnuser.php");


$j			= new jsnpdo;
$jsnuser	= new jsnuser;
$j->connect("mysql", "localhost", "jsntest", "root", "1234");


//建立資料表
$jsnuser->class_jsnpdo = $j; //指定class jsnpdo 
$jsnuser->create_table();

//新增
$ary['account']		= $j->PDO->quote("fdjkgh580");
$ary['password'] 	= $j->PDO->quote("fdjkgh580");
$ary['name'] 		= $j->PDO->quote("張先生");
$result 			= $jsnuser->insert("account", $ary);
if ($result == "have") echo"會員已存在，現在進行登入<br>";
else echo "新增使用者成功<br>";

//登入前先驗證
unset($ary, $regary);
$ary['account']		= $j->PDO->quote('fdjkgh580');
$ary['password']	= $j->PDO->quote('fdjkgh580');
$DataInfo	 		= $jsnuser->login("account", $ary);
if ($DataInfo == 0) die("登入失敗, 帳號密碼輸入錯誤");

//登入SESSION
unset($ary);
$ary['account']		= $DataInfo['account'];
$ary['name'] 		= $DataInfo['name'];
$ary['authority']	= 1000;
$jsnuser->reg_sess($ary);
echo "登入成功，歡迎{$_SESSION[member][account]}, {$_SESSION[member][name]}, {$_SESSION[member][authority]} <br>";


//修改資料
unset($ary);
$ary['account']		= $j->PDO->quote('fdjkgh580');
$ary['name']		= $j->PDO->quote('李先生');
$result 			= $jsnuser->update("account", $ary);
if ($result == 1) echo "修改成功<br>";
else die("修改失敗<br>");

//登出
$result = $jsnuser->logout();
if ($result == "1") echo "登出成功<br>";


//刪除
unset($ary);
$who				= $j->PDO->quote('fdjkgh580');
$result = $jsnuser->delete("account", $who);
if ($result == "1") echo "刪除成功<br>";
else die("該用戶不存在");

echo "Demo 結束!";
?>




