<?
session_start();
include_once("../jsnfilelink.php");
$jsnfilelink = new Jsnfilelink;

//文件一
$ary['src'] 		= "mini.jpg"; //檔案實際路徑
$ary['hash'] 		= md5(microtime()); //驗證碼
$img				= $jsnfilelink->get_param($ary);
?><img src="fileimg.php<?=$img?>" style="width:60px;"><?

//文件一
$ary['src'] 		= "Lighthouse.jpg"; //檔案實際路徑
$ary['hash'] 		= md5(microtime()); //驗證碼
$img				= $jsnfilelink->get_param($ary);
?><img src="fileimg.php<?=$img?>" style="width:60px;"><?


echo "<br><br><hr>";
print_r($_SESSION);

