<?php

namespace Jsnlib;

/**
 * 使用 PHPMailer 發送 mail 
 * 
 * @param  $path 					PHPMailer資料夾的整個路徑，不必填寫 script.php 檔
 * @param  $from     				從哪發送的 email
 * @param  $to 				array 	發送給誰的 email
 * @param  $subject 				標題
 * @param  $message 				內文訊息
 * @param  $attachment_key 			(選)輸入夾帶附件的鍵名，如 "upl" 代表 $_FILES[upl]，當有上傳文件才會觸發。
 * 
 * @return 					返回狀態物件
 */
function send_mail($param)
{
	try 
	{
		foreach ($param as $key => $val) $$key = $val;
		if (empty($path)) 		throw new Exception("須要指定PHPMailer的路徑");
		if (empty($from)) 		throw new Exception("須要指定發送來源的E-mail");
		if (empty($to)) 		throw new Exception("須要指定發送對象的E-mail");
		if (!is_array($to)) 	throw new Exception("發送對象的E-mail 須要是陣列");
		if (empty($subject)) 	throw new Exception("須要指定信件標題");
		if (empty($message)) 	throw new Exception("須要指定信件內容");

		$script 								=	$param->path . "/PHPMailerAutoload.php";

		if (!file_exists($script)) 	
		{
			throw new Exception("引用的核心檔案不存在：{$script}");
		}
		
		require_once($script);

		$mail           						= 	new PHPMailer;

		foreach ($to as $email)
		{
			// 添加收件人, 第二個參數姓名選用
			$mail->addAddress($email);     							
		}
		
		$mail->setLanguage('zh'); 								// 中文錯誤訊息語言
		$mail->setFrom($from, "系統管理員");
		$mail->WordWrap 						= 	50;           // Set word wrap to 50 characters
		$mail->isHTML(true);                                  
		$mail->CharSet  						= 	"utf-8";
		$mail->Subject  						= 	$subject;
		$mail->Body     						= 	$message;

		$file 									=	$_FILES[$attachment_key];
		if (is_array($file)) foreach ($file['name'] as $key => $info)
		{
			$file_tmp 							=	$file['tmp_name'][$key];
			$file_name 							=	$file['name'][$key];
			
			// 夾帶檔案。第二個參數檔名，選用
			$mail->addAttachment($file_tmp, $file_name);   
		}
		

		if(!$mail->send()) 
		{
			throw new Exception("信件未送出喔，有些錯誤需要處理：『" . $mail->ErrorInfo . "』");
		}

		else 
		{
		    return output_status_comb_jsonp($_GET['callback'], "success", "發送成功", null, $isjson_encode = true, $isreturn = true);
		}

	}
	catch(Exception $e)
	{
		return output_status_comb_jsonp($_GET['callback'], "error", $e->getMessage(), null, $isjson_encode = true, $isreturn = true);
	}
}


/**
 * 方便使用的curl函式
 * @param  $url           要連接的網址
 * @param  $postdataarray POST資料的陣列
 * @return 				  回傳取得的資料
 */
function curlfun($url, $postdataarray)
{
	/*
		CURLOPT_POSTFIELDS參數即為POST的內容，而 http_build_query() 效果是將array併成 a=123&b=321 型式的字串，
		POST內容會在header中標示以application/x-www-form-urlencoded型式傳送，如果不用字串而直接給array也可以，
		傳送方式則會變成multipart/form-data，但是封包會變大，且可能不被某些Server接受，通常是傳送檔案時才用。
		cURL有很多參數可以設置，詳細用法見PHP官網。
		
		CURLOPT_RETURNTRANSFER=true 會傳回網頁回應, false 時只回傳成功與否
		
    */
    $ch			= curl_init();
    $options    = array
    (
		CURLOPT_URL				=>    $url,
		CURLOPT_HEADER			=>    0,
		CURLOPT_VERBOSE			=>    0,
		CURLOPT_RETURNTRANSFER  =>    true,
		CURLOPT_USERAGENT		=>    "", //"Mozilla/4.0 (compatible;)
		CURLOPT_POST			=>    true, //啟用post
		CURLOPT_POSTFIELDS		=>    $postdataarray
	);
    
    curl_setopt_array($ch, $options) ; //把陣列放入設定
    $result = curl_exec($ch); //開始執行
    curl_close($ch);
    return $result;
}





/**
 * 組合的執行結果
 * @param  $status        "success" 或 "error" 或其他語意狀態
 * @param  $message       顯示的提示訊息
 * @param  $data          物件或陣列的回傳資料
 * @param  $isjson_encode 預設返回json格式
 * @return                返回組合的物件或是json格式
 */
function status_comb($status, $message, $data = null, $isjson_encode = true)
{
	$obj          = new \stdClass;
	$obj->status  = $status;
	$obj->message = $message;
	$obj->data    = $data;
	return $isjson_encode == true ? json_encode($obj) : $obj;
}

/**
 * 自動判斷輸出 status_comb() 的執行結果
 * @param  $status        "success" 或 "error" 或其他語意狀態
 * @param  $message       顯示的提示訊息
 * @param  $data          物件或陣列的回傳資料
 * @param  $isjson_encode 預設返回json格式
 * @return                返回組合的物件或是json格式
 */
function output_status_comb($status, $message, $data = null, $isjson_encode = true)
{
	$result = status_comb($status, $message, $data, $isjson_encode);
	if (is_object($result)) print_r($result);
	else echo $result;
}

/**
 * 如果對方需要 JSON 格式(同網域才可以)。就僅回覆 JSON 格式，否則回覆JSONP格式。
 * 主要用在API設計回覆請求。若配合
 * 可以需求，是否在檔頭添加
 * header("Content-type: application/json; charset=utf-8");
 * 該函數可以取代 output_status_comb() 了，因為功能進階。只是寫法不同。
 * 
 * @param  $callback_name    string         當需要 jsonp 回應的 callback 名稱
 * @param  $status           string         "success" 或 "error"
 * @param  $message          string         顯示的提示訊息
 * @param  $data             obj | array    物件或陣列的回傳資料
 * @param  $isjson_encode    bool           預設返回json格式
 * @param  $isreturn         bool           預設 true 可在最後返回。
 * 
 */
function output_status_comb_jsonp($callback_name = null, $status, $message, $data = null, $isjson_encode = true, $isreturn = true)
{
	$result = status_comb($status, $message, $data, $isjson_encode);
	if (is_object($result)) 
	{
		if ($isreturn == false) print_r($result);
		else return $result;
	}
	else
	{
		if (empty($callback_name)) 
		{

			if ($isreturn == false) echo $result; //json 格式
			else return $result;
		}
		else 
		{
			if ($isreturn == false) echo "{$callback_name}($result)"; //jsonp 格式
			else return "{$callback_name}($result)"; //jsonp 格式
		}
	}
}


/**
 * js的輔助語言
 * $type 
 * $param array 夾帶參數
 
 */
function js($type, $param)
{
	if (!is_array($param)) die("請使用陣列夾帶自訂參數");
	
	//自訂參數 url
	if ($type == "location")
	{
		return "location.href='{$param['url']}'";
	}

	//自訂參數 url
	elseif ($type == "form_action")
	{
		return 	"jQuery(this).parents('form').attr('action', '{$param['url']}')";
	}
}

function js_location($url)
{
	return js("location", array("url" => $url));
}

function js_form_action($url)
{
	return js("form_action", array("url" => $url));
}


//擷取字串
function WordLimit ($String , $Num = 100 , $OverString = "...", $HTML_LIMIT = true , $Code = "UTF-8") {
		
		if (is_string($HTML_LIMIT)) {
			$String = str_replace(array("  ","&nbsp;","\t","\r"),array(" ",""),strip_tags($String,$HTML_LIMIT)) ;
		} else if ($HTML_LIMIT == false) {
			//不動
		} else if ($HTML_LIMIT == true) {
			//替換特殊字或多空格者
			$String = str_replace(array("  ","&nbsp;","\t","\r"),array(" ",""),strip_tags($String,"")) ;
		}
		
		$ASCII= array(1		=> 0.0, 2		=> 0.0, 3		=> 0.0, 4		=> 0.0, 5		=> 0.0, 6		=> 0.0, 7		=> 0.0, 8		=> 0.0,	 9	=> 0.0, 10	=> 0.0, 
								11	=> 0.0, 12	=> 0.0, 13	=> 0.0, 14	=> 0.0, 15	=> 0.0, 16	=> 0.0, 17	=> 0.0, 18	=> 0.0, 19	=> 0.0, 20	=> 0.0, 
								21	=> 0.0, 22	=> 0.0, 23	=> 0.0, 24	=> 0.0, 25	=> 0.0, 26	=> 0.0, 27	=> 0.0, 28	=> 0.0, 29	=> 0.0, 30	=> 0.0, 
								31	=> 0.0, 32	=> 0.0, 33	=> 0.3, 34	=> 0.3, 35	=> 0.6, 36	=> 0.5, 37	=> 0.9, 38	=> 0.7, 39	=> 0.2, 40	=> 0.3, 
								41	=> 0.3, 42	=> 0.3, 43	=> 0.3, 44	=> 0.3, 45	=> 0.3, 46	=> 0.3, 47	=> 0.3, 48	=> 0.55, 49	=> 0.55, 50	=> 0.55, 
								51	=> 0.55, 52	=> 0.55, 53	=> 0.55, 54	=> 0.55, 55	=> 0.55, 56	=> 0.55, 57	=> 0.55, 58	=> 0.6, 59	=> 0.6, 60	=> 0.6, 
								61	=> 0.6, 62	=> 0.6, 63	=> 0.6, 64	=> 0.6, 65	=> 0.65, 66	=> 0.65, 67	=> 0.65, 68	=> 0.65, 69	=> 0.65, 70	=> 0.65, 
								71	=> 0.65, 72	=> 0.65, 73	=> 0.3, 74	=> 0.3, 75	=> 0.65, 76	=> 0.65, 77	=> 0.65, 78	=> 0.65, 79	=> 0.65, 80	=> 0.65, 
								81	=> 0.65, 82	=> 0.65, 83	=> 0.65, 84	=> 0.65, 85	=> 0.65, 86	=> 0.65, 87	=> 0.65, 88	=> 0.65, 89	=> 0.65, 90	=> 0.65, 
								91	=> 0.6, 92	=> 0.6, 93	=> 0.6, 94	=> 0.6, 95	=> 0.6, 96	=> 0.6, 97	=> 0.6, 98	=> 0.6, 99	=> 0.6, 100	=> 0.6, 
								101	=> 0.6, 102	=> 0.6, 103	=> 0.6, 104	=> 0.6, 105	=> 0.6, 106	=> 0.6, 107	=> 0.6, 108	=> 0.6, 109	=> 0.6, 110	=> 0.6, 
								111	=> 0.6, 112	=> 0.6, 113	=> 0.6, 114	=> 0.6, 115	=> 0.6, 116	=> 0.6, 117	=> 0.6, 118	=> 0.6, 119	=> 0.6, 120	=> 0.6, 
								121	=> 0.6, 122	=> 0.6, 123	=> 0.6, 124	=> 0.6, 125	=> 0.6, 126	=> 0.6, 127	=> 0.6) ;
		
		$WordCounter = 0 ;
		$ReturnStr = "" ;
		$STR_LENGTH = mb_strlen($String,$Code) ;
		for ($CurrentPosition = 0 ; $CurrentPosition < $STR_LENGTH ; $CurrentPosition++) {
			$Word = mb_substr($String,$CurrentPosition,1,$Code) ;
			
			$WordOrd = ord($Word) ;
			
			if ($WordOrd < 32) continue ;
			$ReturnStr .= $Word ;
			
			if ($WordOrd < 128) {
				$WordCounter += $ASCII[$WordOrd] ;
			} else {
				$WordCounter++ ;
			}
			
			if ($WordCounter >= $Num) break ;
		}
		
		return $CurrentPosition < $STR_LENGTH ? $ReturnStr . $OverString : $String ;
	}


//一維陣列命名 例如自動把 $ary['first'] 轉換成全域變數 $first
function name_array($ary)
{
	foreach ($ary as $key => $val) 
	{
		$GLOBALS[$key] = $val;
	}
}



//進階編輯器tinyMCE, $id為元素的ID,多項元素使用可用逗號分開; $JSp為JS路徑的前置位置; $param 其他的tinyMCE參數
/**
 * [tinyMCE4 description]
 * @param   string     $id          jQuery 的選擇器
 * @param   string     $JSp         JS路徑的前置位置
 * @param   string     $height      高度
 * @param   string     $weight      寬度
 * @param   object     $param       (選用)
 *                                  document_base_url        網站基本位置，讓檔案可以正確顯示在編輯器中。
 *                                  my_albumupload_insert    1: (預設)使用上傳圖片插入 plugin
 *                                  my_video_insert          1: (預設)使用影片插入 plugin
 * 
 */
function tinyMCE4($id, $JSp=null, $height="400px", $weight="100%", $param = null)
{
	
	$document_base_url 		= (!isset($param->document_base_url)) ? "" : $param->document_base_url; //網站基本位置
	$my_albumupload_insert	= (!isset($param->my_albumupload_insert) or $param->my_albumupload_insert == 1) ? "my_albumupload_insert" : null;
	$my_video_insert		= (!isset($param->my_video_insert) or $param->my_video_insert == 1) ? "my_video_insert" : null;
	?>
	<script type="text/javascript" src="<?=$JSp?>tinymce-4.2.5/tinymce.min.js"></script>
    <script type="text/javascript">
		tinymce.init({
            language : "zh_TW",
			selector: "<?=$id?>",
			width : "<?=$weight?>",
			height: "<?=$height?>",
			convert_urls: false, //不使用轉換
			document_base_url: "<?=$document_base_url?>",
			

			plugins: [
			                "importcss advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
			                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			                "table contextmenu directionality emoticons template textcolor paste fullpage textcolor colorpicker textpattern",
			                "<?=$my_albumupload_insert?> <?=$my_video_insert?>"
			        ],
			
			toolbar: "fullscreen code preview | styleselect fontsizeselect formatselect  fontselect | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignjustify | bullist numlist outdent indent | link image media  | <?=$my_albumupload_insert?> <?=$my_video_insert?>",
			
			indentation: "2em", //+縮排
			outdentation: "2em", //-縮排

			nonbreaking_force_tab: true,

			plugin_preview_width : 1280,
			plugin_preview_height : 1000,

			content_css: "<?=$document_base_url?>/css/theme/BackEnd/purple/stylesheets/admin/tinyMCE/content_css.css",

			importcss_append: true,
			importcss_file_filter: "content_css.css",

			resize: "both",


			textpattern_patterns: 	[
								         {start: '*', end: '*', format: 'italic'},
								         {start: '**', end: '**', format: 'bold'},
								         {start: '#', format: 'h1'},
								         {start: '##', format: 'h2'},
								         {start: '###', format: 'h3'},
								         {start: '####', format: 'h4'},
								         {start: '#####', format: 'h5'},
								         {start: '######', format: 'h6'},
								         {start: '1. ', cmd: 'InsertOrderedList'},
								         {start: '* ', cmd: 'InsertUnorderedList'},
								         {start: '- ', cmd: 'InsertUnorderedList'}
								    ],

			// fullpage_default_encoding: "UTF-8",

			templates: 	[ 
			        		{title: 'Markdown', description: '使用Markdown快速編輯文本', content: '*斜體*<br>**粗體**<br>#標題1<br>##標題2<br>###標題3<br>####標題4<br>#####標題5<br>######標題6<br> '}, 
			    		],

			fontsize_formats: "<? for($a=8; $a<=100; $a++){ ?><?=$a?>pt <? } ?>",

			font_formats: 	"微軟正黑體='微軟正黑體';"+
							"新細明體='新細明體';"+
							"標楷體='標楷體';"+
							"Consolas=consolas;"+
			        		"Arial=arial,helvetica,sans-serif;"+
			        		"Arial Black=arial black,avant garde;"+
			        		"Book Antiqua=book antiqua,palatino;"+
			        		"Comic Sans MS=comic sans ms,sans-serif;"+
			        		"Courier New=courier new,courier;"+
			        		"Georgia=georgia,palatino;"+
			        		"Helvetica=helvetica;"+
			        		"Impact=impact,chicago;"+
			        		"Symbol=symbol;"+
			        		"Tahoma=tahoma,arial,helvetica,sans-serif;"+
			        		"Terminal=terminal,monaco;"+
			        		"Times New Roman=times new roman,times;"+
			        		"Trebuchet MS=trebuchet ms,geneva;"+
			        		"Verdana=verdana,geneva;"+
			        		"Webdings=webdings;"+
			        		"Wingdings=wingdings,zapf dingbats", 
			// style_formats: [
			//         {title: 'Bold text', inline: 'b'},
			//         {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
			//         {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
			//         {title: 'Example 1', inline: 'span', classes: 'example1'},
			//         {title: 'Example 2', inline: 'span', classes: 'example2'},
			//         {title: 'Table styles'},
			//         {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
			//     ]
			style_formats: 	[
								{
									title 	: 	'重要', 
									inline	: 	'span', 
									styles 	: 	{
													'color' 	: 	'red',
												}
								},

							],

		});
    </script>
    <?
}




//提取副檔名
function secondname($name)
{
	$name=strrchr($name,".");//取得路徑最後一次出現「.」以後的字串
	$name=ltrim($name,".");//把字串前的特定字串「.」去除。若沒指定字串，則代表去除空白
	return $name;
}

/**
 * 去除附檔名
 * 
 * $val 指定的檔名如 Desert.jpg
 * $return_type = 0 回傳檔名如 Desert
 * $return_type = 1  回傳陣列如 Array ( [0] => Desert [1] => .jpg )
 * $return_type = 2  回傳陣列如 Array ( [0] => Desert [1] => jpg )
 */

function rm_secname($val, $return_type = 0)
{
	$secname		=	pathinfo($val, PATHINFO_EXTENSION);
	$secname_point	=	"." . $secname;
	$firstname		=	basename($val, $secname_point);
	
	//pathinfo 處理中文會空白, 所以添加亂數字串
	if ($firstname == $secname_point) $firstname = "system_replace";
	
	
	if ($return_type == 0)
		return $firstname;
	if ($return_type == 1)
		return array($firstname, $secname_point);
	if ($return_type == 2)
		return array($firstname, $secname);
}
			

//目前網頁名稱 $secondname=0預設不傳回副檔名
function pagename($secondname=0)
{
	$page_name = ltrim(strrchr($_SERVER['REQUEST_URI'],"/"),"/");
	
	if($secondname == 0) 
	{
		return $page_name = strtok($page_name,".");
	}

	return $page_name;
}


/**
 * 裁切圖片
 * @fromimgname 檔案名稱
 * @fromimg_startx 來源檔案的右上角X座標
 * @fromimg_starty 來源檔案的右上角Y座標
 * @newimg_width 裁切後的新圖檔寬
 * @newimg_height 裁切後的新圖檔高
 *
 */
function cutimg($fromimgname, $fromimg_startx, $fromimg_starty, $newimg_width, $newimg_height)
{
	//取得目標檔案的長寬
	$fromimg = imagecreatefromjpeg($fromimgname);
	$fromimg_info = getimagesize($fromimgname);
	$fromimg_width = $fromimg_info[0];
	$fromimg_height = $fromimg_info[1];

	//新檔案的寬高
	$newimg = imagecreatetruecolor($newimg_width, $newimg_height); // 超過256色改用這個

	//進行裁切
	imagecopy($newimg, $fromimg, 0,0,$fromimg_startx,$fromimg_starty,$newimg_width,$newimg_height);

	return $newimg;
}	
