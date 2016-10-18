<?

class Jsnalbum {
	
	//記錄檔名的session名稱
	public $sessname = "jsnalbum_userupload_filename";
	
	
	function __construct(){
		if (!class_exists('jsnupload')) die("請先載入 jsnupload !");
		}
	
	
	
	/*
	 *	接收上傳的檔案
	 *	$param['php_path'] //上傳位置, 使用PHP絕對或相對路徑
	 */
	public function getfile($param) {
		unset($_SESSION[$this->sessname]);
		
		$U = new jsnupload;
		
		$inputname					=	"album_TinyMCE"; //設定
		$U->filename 				=	$inputname; //input name屬性的陣列名稱				
		$U->arraykey 				= 	0; //input name陣列鍵值(起始值)						
		$U->could_secondname		=	"jpg,png,tif,gif"; //允許副檔名	
		$U->pathaccess				=	"0777"; //路徑權限
		$U->size					=	30; //MB					
		$U->site					=	$param['php_path']; //上傳路徑		
		$U->resizeImageScriptPath	=	"plugin/ImageResize.php"; //套件ImageResize 路徑	(可相對於class jsnupload 的位置)
		$U->resize_width			=	400;
		$U->resize_height			=	400;
		$U->resize_quality			=	100;
		
		foreach ($_FILES[$inputname]["name"] as $val) {
	
			if ($U->isnextkey($val)) continue; //不限數量 (遇到未指定的就換下一個<input>)
			$add_arraykey	= $U->arraykey;
			
			
			
			//指定為一名稱
			$primary_filename = uniqid();
			
			//開始上傳
			//小
			$newname_s 		= $primary_filename.$U->arraykey."_s.".$U->scandN(1);	
			$U->resize_width			=	150;
			$U->resize_height			=	150;
			$U->fileupload_multi($newname_s, $add_arraykey, 1, "retain");
			
			//中
			$newname_m 		= $primary_filename.$U->arraykey."_m.".$U->scandN(1);	
			$U->resize_width			=	400;
			$U->resize_height			=	400;
			$U->fileupload_multi($newname_m, $add_arraykey, 1, "retain");
	
			//大
			$newname_b 		= $primary_filename.$U->arraykey."_b.".$U->scandN(1);	
			$U->resize_width			=	1280;
			$U->resize_height			=	1280;
			$U->fileupload_multi($newname_b, $add_arraykey, 1, "clean");
			
			$_SESSION[$this->sessname][] = $newname_b;
			}
			
		return "1";
		}
	
	
	
	
	
	/*
	 *	放置相簿連結
	 *	$param['html_href'] //AJAX取得相簿的位置, 使用html超連結表示法
	 *	$param['inner_a'] 	//<a>裡面要放置的html, 可以文字或圖片
	 */
	public function album_link($param){
		$classname = "janalbum_usefancybox_v206_" . uniqid();
		?>
        <script>$(function (){$(".<?=$classname ?>").fancybox();})</script>
		<a class="<?=$classname ?>" data-fancybox-type="ajax" href="<?=$param['html_href'] ?>"><?=$param['inner_a'] ?></a>
		<?
		}
	
	
	
	/*
	 *	刪除圖片
	 *	$param['php_path'] //PHP路徑寫法, 圖片存放的絕對或相對路徑
	 *	$param['filename'] //刪除的檔案名稱, 預設接收$_POST['filename']即可
	 */
	public function delimg($param){
		$param['php_path'] = rtrim($param['php_path'], "\ /") . "/";
		
		$after = strtok($param['filename'], "_");
		$secname = pathinfo($param['filename'], PATHINFO_EXTENSION); //附檔名
		
		
		unlink($param['php_path'] . $after . "_s." . $secname);
		unlink($param['php_path'] . $after . "_m." . $secname);
		unlink($param['php_path'] . $after . "_b." . $secname);
		
		return "1";
		}
	
	
	
	/*
	 *	相簿
	 *	$param['php_path'] 					//放置相片的路徑(資料夾), php開啟，請使用php絕對或相對 
	 *	$param['html_src'] 					//<img>讀取圖片的html連結 
	 *	$param['delimg']['js_ajaxfile'] 			//AJAX刪除的PHP檔案路徑, js路徑寫法, 檔名後方可不須夾帶辨識參數
	 *	$param['delimg']['act'] 			//AJAX刪除傳遞json辨識的act參數值
	 *	$param['delimg']['name_success'] 	//AJAX刪除後傳送的javascript callback function name
	 *
	 */
	public function album_view ($param){
		$param['php_path'] = rtrim($param['php_path'], "\ /");
		$param['html_src'] = rtrim($param['html_src'], "\ /");
		//if (!file_exists($param['php_path'])) die("相簿路徑指定不存在");
		
		//取得所有文件
		$DataList = scandir($param['php_path']);
		if (!is_array($DataList)) return false;
		
		
		
		
		$newary = array();
		foreach ($DataList as $name) {
			if ($name == "." or $name == "..") continue; 
			
			//剔除小、中張的同名檔案
			if (substr_count($name, "_s") > 0 or substr_count($name, "_m") > 0) continue; 
			
			//取得最後修改時間
			$finfo = stat($param['php_path'] . "/" . $name);
			$newary[] = array (
				'time' => $finfo['mtime'], 
				'name' => $name
				);
			}
			
		rsort($newary); //依值做時間排序大到小	新->舊
		
		//讓陣列指存放檔名, 去掉原來存的時間
		foreach ($newary as $key => $val) {
			$newary[$key] = $val['name'];
			}
		
		$DataList = $newary;
		
		
		?>
        <script>
        $(function (){
			
			$(".del_jsnalbum_img").on("click", function () {
				if (!confirm("確定要刪除？")) return false;
				var my 			= $(this);
				var filename 	= $(this).parents(".jsnalbum_size_s").find("img").data("filename");
				//var src			= " <?=$param['php_path'] ?> ";
				$.post("<?=$param['delimg']['js_ajaxfile']?>", {
					'act'		:	'<?=$param['delimg']['act'] ?>',
					'filename'	:	filename
					},
					function (data){
						
						<?=$param['delimg']['name_success'] . " (data);"?>
						//動畫刪除
						my.parents(".jsnalbum_size_s").fadeOut(65, function (){
							my.parents(".jsnalbum_size_s").remove();
							});
						})
				});
			})
        </script>
		<style>
		.janalbum_container {
			width:800px;
			height:500px;
			}
        .jsnalbum_size_s {
			float:left;
			
			padding:5px;
			margin:20px;
			box-shadow:0px 0px 20px 5px rgba(95,95,95,0.4);
			max-width:150px;
			max-height:150px;
			min-width:150px;
			min-height:150px;
			}
		.jsnalbum_size_s img {
			max-width:100px;
			max-height:100px;
			cursor:pointer;
			}
        </style>
        <div class="janalbum_container">
        	<?
			foreach ($DataList as $name) {
				
				//取副檔名
				$secn = pathinfo($name, PATHINFO_EXTENSION);
				
				//取得檔明如 51468fd5aa7a70_b.jpg底線前方的字串
				$befn = strtok($name,"_");
				
				
				$size_s = $befn . "_s." . $secn;
				$size_m = $befn . "_m." . $secn;
				$size_b = $befn . "_b." . $secn;
				
				
				
				
				/*
				if ($name == "." or $name == "..") continue; 
				
				//取得檔明如 51468fd5aa7a70_b.jpg底線後方的字串，如_b.jpg
				$after = strstr($name, "_");
				
				//依尺寸不同指定到變數
				if (substr_count($after, "_s") > 0) $size_s = $name;
				elseif (substr_count($after, "_m") > 0) $size_m = $name;
				elseif (substr_count($after, "_b") > 0) $size_b = $name;
				
				//大中小圖檔，只顯示小圖檔即可, 但插入到tinyMCE為大圖
				if (substr_count($after, "_s") == 0) continue;
				*/
				?>
				<div class="jsnalbum_size_s">
                    <div>
                        <div class="del_jsnalbum_img" style="text-align:right">del</div>
                    </div>
					<div onClick="tinyMCE.execCommand('mceInsertContent', true,'<img src=\'<?=$param['html_src'] ."/". $size_b?>\'>');$.fancybox.close();">
                    	<img data-filename="<?=$name ?>" src="<?=$param['html_src'] ."/". $size_s?>">
                    </div>
				</div>
				<?
				
				}
			?>
        </div>
		<?
		}
	
	
	
	
	/*
	 *  放置上傳按鈕
	 *	$param['js_ajaxfile']		//jq接收的PHP檔, 使用JS路徑寫法
	 *	$param['html_src']			//(選用)當即時插入時, html的上傳路徑
     *  $param['data_json']			//傳遞的json參數
     *  $param['ajax_uplhtml']		//當AJAX上傳圖片時，想顯示的HTML, 可以是文字或動畫
	 *	$param['ajax_succ_uplhtml'] //(選用)當AJAX上傳圖片完畢，想顯示的HTML。
     *  $param['name_success']	//成功呼叫的javascript callbacl function name
     *  $param['name_error']	//失敗呼叫javascript callbacl function name
	 *
	 */
	public function button($param) {
		if (!empty($param['html_src'])) {
			$param['html_src'] = rtrim($param['html_src'], "\ /");
			}
		if (empty($param['ajax_succ_uplhtml'])) {
			$param['ajax_succ_uplhtml'] = "<strong style='color:red'>上傳成功</strong>";
			}
		?>
		<script>
		$(function (){
			
			function upstr(){
                $(".album_TinyMCE_btsite").css("display", "none");
				$(".album_TinyMCE_startstring").css("display","block");
				}
			function upstop(){
				$(".album_TinyMCE_btsite").css("display", "block");
				$(".album_TinyMCE_startstring").css("display","none");
				$(".album_TinyMCE_succstring").fadeIn(100).delay(400).fadeOut(800);
				}
			
			//AJAX取得session 上傳時記錄的位置後插入文章
			function getsess_insert(){
				$.post("<?=$param['js_ajaxfile'] ?>", {
					'act'	:	'jsnalbum_getuploadimg_json'
					}, 
					function (data) {
						//console.log(data);
						$.each(data,function (index, value){
							value = "<?=$param['html_src'] ?>/" + value;
							tinyMCE.execCommand('mceInsertContent', true, "<img src='" + value + "'>");
							});
						
					}, "json");
				}
			
				
			$("body").on("change", ".album_TinyMCE", function (){
				
				
				upstr();
				$.ajaxFileUpload({
					'url'			:    "<?=$param['js_ajaxfile'] ?>",
					secureuri		:    false,
					fileElementId   :    'album_TinyMCE',
					dataType	 	:	'text', //非內鍵的參數
					data			:    <?=$param['data_json'] ?>,
					success			:     function (data, status) {
											<?=$param['name_success'] . "(data, status);"?>
											upstop();
											
											//啟用即時插入
											if ($(".jsnalbum_insertbox").attr("checked")) {
												getsess_insert();
												}
											
											},
					error			:    function (data, status, e) {
											upstop();
											<?=$param['name_error'] . "(data, status, e);"?>
											}
					})    				
				})
			
			
			
			
				
			
			})
		</script>
        
		<div class="album_TinyMCE_btsite" style="float:left;">
			<input name="album_TinyMCE[]" class="album_TinyMCE" id="album_TinyMCE" type="file" multiple>
		</div>
        <? // 上傳時顯示的HTML ?>
		<div class="album_TinyMCE_startstring" style="display:none">
            <?=$param['ajax_uplhtml']?>
        </div>
        <? // 上傳完成顯示的HTML ?>
        <div class="album_TinyMCE_succstring" style="display:none">
        	<?=$param['ajax_succ_uplhtml'] ?>
        </div>
		<?
		}
		
		
	/*	
	 *	放置動態插入的選擇框供使用者
	 *	$ischecked //預設0 不勾選
	 */
	public function insertbox ($ischecked = 0) {
		$ischecked = empty($ischecked) ? NULL : "checked";
		?>
		<input name="jsnalbum_insertbox" class="jsnalbum_insertbox" type="checkbox" <?=$ischecked ?>>
		<?
		}	
		
		
		
	}


?>