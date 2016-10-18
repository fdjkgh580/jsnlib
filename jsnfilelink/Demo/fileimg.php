<?
session_start();
include_once("../jsnfilelink.php");
$jsnfilelink = new Jsnfilelink;

//驗證
$param['name']		= $_GET['name'];
$param['hash'] 		= $_GET['hash'];
$realfile = $jsnfilelink->chk($param);
if (empty($realfile)) die;

header("Content-Type: application/octec-stream");
header("Content-Disposition: attachment; filename=".$realfile);
readfile($realfile);
?>