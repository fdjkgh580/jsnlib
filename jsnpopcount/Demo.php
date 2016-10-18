<?
session_start();
include_once("jsnpopcount.php");
$jsnpopcount = new Jsnpopcount;
//unset($_SESSION[$jsnpopcount->sess_name]);


$jsnpopcount->reset_seconds	= 6;
$ID 						= "100";
$count						= 67;
echo "[ 指定重算的時間:{$jsnpopcount->reset_seconds}秒 ]<br>";
echo "目前參觀的是編號{$ID}，參觀人氣{$count}:<br>";
echo "要執行+1嗎？ " . $jsnpopcount->add("experience", $ID);
echo "<br>";


$jsnpopcount->reset_seconds = 2;
$ID 	= "101";
$count	= 67;
echo "[ 指定重算的時間:{$jsnpopcount->reset_seconds}秒 ]<br>";
echo "目前參觀的是編號{$ID}，參觀人氣{$count}:<br>";
echo "要執行+1嗎？ " . $jsnpopcount->add("experience", $ID); 
echo "<br>";




echo "<hr>";
print_r($_SESSION[$jsnpopcount->sess_name]);
echo "<hr>";