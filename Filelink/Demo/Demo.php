<?
session_start();
include_once("../Filelink.php");
$Filelink = new Jsnlib\Filelink;
$Filelink->url("fileimg.php");

//文件一
$url				= $Filelink->save("mini.jpg");
?><img src="<?=$url?>" style="width:60px;"><?

//文件二
$url				= $Filelink->save("Lighthouse.jpg");
?><img src="<?=$url?>" style="width:60px;"><?
