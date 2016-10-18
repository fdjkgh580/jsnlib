<?
try
{
	include_once("jsnrequestlog.php");
	$jsnrequestlog 					=	new jsnrequestlog;

	$jsnrequestlog->filename 		=	"demo_log.txt"; //指定log記錄位置
	$jsnrequestlog->limit_num 		=	3; //最多的筆數
	$jsnrequestlog->reset_num 		=	1; //清空後要保留的最新筆數

	$jsnrequestlog->write("get"); //記錄post或get
	
	// $jsnrequestlog->read(1); //可輸出顯示(debug)
	$result 						=	$jsnrequestlog->read(0); 
	print_r($result);

}
catch (Exception $e)
{
	echo $e->getMessage();	
}