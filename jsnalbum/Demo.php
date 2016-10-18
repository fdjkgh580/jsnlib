<?
//步驟0: MVC架構要記得注意使用session_start
session_start();

//步驟0: 因為配合tinyMCE所以要引用
include_once '../function.php';
include_once '../jsnupload/jsnupload.php';
include_once 'jsnalbum.php';

$jsnupload	= new jsnupload;
$jsnalbum	= new Jsnalbum;


//步驟2. 接收上傳檔案
if ($_POST['act'] == "upload_jsnalbumimg") {
	$param['php_path'] = "testupload";//上傳位置URL
	$jsnalbum->getfile($param);
	die;
	}

//步驟5. AJAX相簿讀取的PHP
if ($_GET['act'] == "viewalbum") {
	
	$param['php_path'] = "testupload";
	$param['html_src'] = "testupload";
	$param['delimg']['js_ajaxfile'] 	= "Demo.php";
	$param['delimg']['act'] 			= "delinner_img_jsnalbum"; //json辨識的act參數值
	$param['delimg']['name_success']	= "jsnalbum_delimg"; //AJAX刪除後傳送的callback function name
	$jsnalbum->album_view($param);
	die;
	}
	
//步驟6. 放置刪除照片的動作, act 須對應步驟4的參數
if ($_POST['act'] == "delinner_img_jsnalbum") {
	
	$param['php_path'] = "testupload";
	$param['filename'] = $_POST['filename'];
	
	echo $jsnalbum->delimg($param);
	die;
	}
	
	
//(步驟9. 選用)即時插入時, JSON 回傳 session 紀錄上傳的檔名
if ($_POST['act'] == "jsnalbum_getuploadimg_json") {
	echo json_encode($_SESSION[$jsnalbum->sessname]);
	die;
	}	
	

//步驟0: 檢查引用的JS系列	
?>
<script src="plugin/jquery-1.7.2.js"></script>

<script src="plugin/ajaxfileupload.js"></script>

<script src="plugin/fancybox-v2.0.6/jquery.mousewheel-3.0.6.pack.js"></script>
<script src="plugin/fancybox-v2.0.6/jquery.fancybox.pack.js"></script>
<script src="plugin/fancybox-v2.0.6/jquery.fancybox.js"></script>
<link rel="stylesheet" type="text/css" href="plugin/fancybox-v2.0.6/jquery.fancybox.css">



<script>
	
	//步驟3. 放置上傳成功與失敗的 callback funciton, 須對應 步驟1.的名稱設定
	function jsnalbum_success(data, status){
		console.log("成功: " + data);
		alert("上傳成功");
		}
	function jsnalbum_error(data, status, e){
		console.log("失敗: " + data);
		alert("上傳失敗");
		}
	
	
	//步驟7. 放置刪除成功後的 callback funciton, 須對應步驟5.的名稱設定
	function jsnalbum_delimg(data){
		console.log(data);
		if (data != "1") alert("刪除失敗");
		}
	
		
</script>
<form method="post" enctype="multipart/form-data" action="">
	<div>
		<? 
        //步驟1.放置上傳按鈕
        unset($param);
		$param['js_ajaxfile']	= 'Demo.php'; //接收的PHP路徑
		$param['html_src']		= 'testupload'; //(步驟9. 選用)需要即時插入時, 要指定html的上傳路徑
        $param['data_json']		= "{'act':'upload_jsnalbumimg'}"; //傳遞的json參數
        $param['ajax_uplhtml']	= "[上傳中]"; //傳遞的json參數
        $param['name_success']	= "jsnalbum_success"; //成功呼叫的javascript function名稱
        $param['name_error']	= "jsnalbum_error"; //失敗呼叫的javascript function名稱
        $jsnalbum->button($param); 
        ?>
    </div>
    <div style="clear:both"></div>
    <label>
    	<? 
		//(步驟8. 選用)即時插入的勾選功能, 一上傳馬上插入tinyMCE
		$jsnalbum->insertbox(); 
		?>
        啟用即時插入
    </label>
    <div>
        <?
		//步驟4. 放置AJAX即時的相簿<a></a>
		unset($param);
		$param['html_href'] = "Demo.php?act=viewalbum"; //AJAX取得相簿的位置
		$param['inner_a'] = "使用fancybox 2.0.6 AJAX插入圖片"; //<a>裡面要放置的html
		$jsnalbum->album_link($param);
		?>
    </div>
    <div>
        <textarea id="Tiny"></textarea>
        <?
        tinyMCE("", "plugin/");
        ?>
    </div>
	<?
    tinyMCE("Tiny", "plugin/");
    ?>
</form>