<?
include_once("jsnbgpopen.php");

$jsnbgpopen = new jsnbgpopen;
$jsnbgpopen->windows	= "C:\AppServ\php5\php.exe";
$jsnbgpopen->linux		= "php";
$ary[] = "script_1.php";
$ary[] = "script_2.php";

$jsnbgpopen->add_script($ary)->start();

echo "OK ";

?>