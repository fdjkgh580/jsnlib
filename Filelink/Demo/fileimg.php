<?
session_start();
include_once("../Filelink.php");
$Filelink = new Jsnlib\Filelink;

//驗證
$param['name']		= $_GET['name'];
$param['hash'] 		= $_GET['hash'];
$realfile = $Filelink->chk($param);
if (empty($realfile)) die;

header("Content-Type: application/octec-stream");
header("Content-Disposition: attachment; filename=".$realfile);
readfile($realfile);
?>