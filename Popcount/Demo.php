<?
session_start();
require_once '../vendor/autoload.php';

$popcount = new Jsnlib\Popcount;

// $popcount->clean();

$popcount->reset_seconds	= 6;
$ID 						= "100";
$count						= 67;
echo "[ 指定重算的時間:{$popcount->reset_seconds}秒 ]<br>";
echo "目前參觀的是編號{$ID}，參觀人氣{$count}:<br>";
echo "要執行+1嗎？ " . $popcount->add("experience", $ID);
echo "<br><br><br>";


$popcount->reset_seconds = 2;
$ID 	= "101";
$count	= 67;
echo "[ 指定重算的時間:{$popcount->reset_seconds}秒 ]<br>";
echo "目前參觀的是編號{$ID}，參觀人氣{$count}:<br>";
echo "要執行+1嗎？ " . $popcount->add("experience", $ID); 
echo "<br>";




// echo "<hr>";
// print_r($_SESSION[$popcount->sess_name]);
// echo "<hr>";