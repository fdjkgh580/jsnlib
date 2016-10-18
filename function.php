<?php


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
		    return output_status_comb_jsonp($_GET['callback'], "success", "發送成功", NULL, $isjson_encode = 1, $isreturn = 1);
		}

	}
	catch(Exception $e)
	{
		return output_status_comb_jsonp($_GET['callback'], "error", $e->getMessage(), NULL, $isjson_encode = 1, $isreturn = 1);
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
 * @param  $status        "success" 或 "error"
 * @param  $message       顯示的提示訊息
 * @param  $data          物件或陣列的回傳資料
 * @param  $isjson_encode 預設返回json格式
 * @return 返回組合的物件或是json格式
 */
function status_comb($status, $message, $data = NULL, $isjson_encode = 1)
{
	$obj->status  = $status;
	$obj->message = $message;
	$obj->data    = $data;
	return $isjson_encode == 1 ? json_encode($obj) : $obj;
}

/**
 * 自動判斷輸出 status_comb() 的執行結果
 * @param  $status        "success" 或 "error"
 * @param  $message       顯示的提示訊息
 * @param  $data          物件或陣列的回傳資料
 * @param  $isjson_encode 預設返回json格式
 * @return 返回組合的物件或是json格式
 */
function output_status_comb($status, $message, $data = NULL, $isjson_encode = 1)
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
 * @param  [type]  $callback_name 當需要 jsonp 回應的 callback 名稱
 * @param  $status        "success" 或 "error"
 * @param  $message       顯示的提示訊息
 * @param  $data          物件或陣列的回傳資料
 * @param  $isjson_encode 預設返回json格式
 * @param  $isreturn      預設輸出，設為1可在最後返回。
 * 
 */
function output_status_comb_jsonp($callback_name = NULL, $status, $message, $data = NULL, $isjson_encode = 1, $isreturn = 0)
{
	$result = status_comb($status, $message, $data, $isjson_encode);
	if (is_object($result)) 
	{
		if ($isreturn == 0) print_r($result);
		else return $result;
	}
	else
	{
		if (empty($callback_name)) 
		{

			if ($isreturn == 0) echo $result; //json 格式
			else return $result;
		}
		else 
		{
			if ($isreturn == 0) echo "{$callback_name}($result)"; //jsonp 格式
			else return "{$callback_name}($result)"; //jsonp 格式
		}
	}
}


//將陣列組合成 SQL 的 where in 值 
function wherein($DataInfo)
{
	if (!is_array($DataInfo)) die("參數請指定為陣列");
	$mix = implode(",", $DataInfo);
	return "({$mix})";
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





/* 
   	FB分享, 使用PHP取得分享數，並配合JS的 Feed Dialog
   	$script 預設不加載，避免與讚衝突
 	$param['appId']
 	$param['secret']
 	$param['url']
 	$param['rediredt_uri']
 	$param['picture']
 	$param['name']
 	$param['caption']
 	$param['description']
	
 */
function FB_feed_share ($param, $script = NULL) {
	
	if (!is_array($param)) die("FB_feed_share() 請指定參數型態為陣列");
	//print_r($param);die;
	
	
	//取得指定網址的分享總數
	$config['appId']	= $param['appId'];
	$config['secret']   = $param['secret'];
	$Facebook			= new Facebook($config);
	$fql = "SELECT  total_count 
			FROM link_stat 
			WHERE url = '{$param[url]}' "; //你要查詢的網址
	$Fary['method']	    = 'fql.query';
	$Fary['query']		= $fql;
	$LinkInfo = $Facebook->api($Fary);     
	
	//再用Javascript SDK 來設定分享的feed dialog
	?>
	<div id='fb-root'></div>
	<? if (!empty($script)) { ?>
		<script src='http://connect.facebook.net/zh_TW//all.js'></script>
	<? } ?>
	<script> 
		
	  FB.init({appId: "<?=$param['appId'] ?>", status: true, cookie: true});
		
	  function postToFeed() {
		  
		var obj = {
		  method		: 'feed',
		  redirect_uri	: '<?=$param['redirect_uri'] ?>',
		  link			: '<?=$param['url'] ?>',
		  picture		: '<?=$param['picture'] ?>',
		  name			: '<?=$param['name'] ?>',
		  caption		: '<?=$param['caption'] ?>',
		  description	: '<?=$param['description'] ?>'
		};
		
		function callback(response) {
		 //console.log("Post ID: " + response['post_id']);
		}

		FB.ui(obj, callback);
	  }
	
	</script>

	<style>
		.fb_custom_feedialog_sharebt .bubble{
			position:relative;
			margin-left:5px;
			float:left;
			border:1px solid #000;
			background:white;
			border-radius:3px;
			
			width:50px; 
			height:20px; 
			text-align:center;
			padding-top:5px;
			font-size:9px;
			color:#666;
			}
		
		.fb_custom_feedialog_sharebt .bubble:after {
			content:"";
			width:0;
			height:0;
			display:block;
			border-style: solid;
			border-width: 5px;
			border-color: transparent  #000 transparent transparent;
			
			position:absolute;
			z-index:1;
			top:8px;
			left:-10px; 
			}
			
		.fb_custom_feedialog_sharebt .bubble2{
			position:relative;
			margin-left:-50px;
			float:left;
			border-top:#000 solid 1px;
			background:white;
			}
			
		.fb_custom_feedialog_sharebt .bubble2:after {
			content:"";
			width:0;
			height:0;
			display:block;
			border-style:solid;
			border-width:5px;
			border-color: transparent  white transparent transparent;
			
			
			position:absolute;
			z-index:1;
			top:8px;
			left:-10px; 
			}
			
		.fb_custom_feedialog_sharebt .btbutton:active{
			position:relative;
			top:1px;
			left:1px;
			}
	</style>
	
	<a class="fb_custom_feedialog_sharebt" onclick='postToFeed(); return false;' href="" >
		<img class="btbutton" src="<?=_URL ?>images/FB_feed_dialog_sharebt.png" style="max-height:30px; float:left">
		<span class="bubble">
			<?=$LinkInfo[0]['total_count'] ?>
		</span>
		<span class="bubble2">&nbsp;</span>
	</a>
	
	<?		
	}




/*
 * 隨著當前網頁而醒目圖片
 * 
 * [範例]
 * 若網址為 index.php?p=1
 * current_item("p", "1", "images/index/menu01-1.png", "images/index/menu01-2.png")
 * 
 * $get_key: 
 *		為$_GET[]的鍵, 如填p
 * $equal_value:
 *		當$_GET[p]的值等於多少時做為判斷
 * $img01:
 *		原本的圖片URL
 * $img02:
 *		替換後的圖片URL
 * $isdefault:
 *		是否做為預選。也就是當網址為index.php時是否做為預選
 */
function current_item($get_key, $equal_value, $img01, $img02, $isdefault = "0") {
	if (empty($_GET[$get_key]) and $isdefault != "0") return $img02;
	if ($_GET[$get_key] == $equal_value) return $img02;
	return $img01;
	}



//切換語言
function switch_lang($zh, $en, $jp) {
	switch (sess_lang_get()) {
		case "zh": return $zh;	break;
		case "en": return $en;	break;
		case "jp": return $jp;	break;
		default: die();			break;
		}
	}

//把語言註冊到session
function sess_lang_set($language) {
	$_SESSION['user_language'] = $language;
	return $language;
	}

//回傳目前語言, 若未註冊則回傳中文zh
function sess_lang_get() {
	return empty($_SESSION['user_language']) ? "zh" : $_SESSION['user_language'];
	}

//設定語言連結的網址
function href_use_lang($use_lang){
	return $href = _URL . "?lang={$use_lang}&returnurl=" . urlencode(_MY);
	}



/*
 * 可用在內頁的橫向選單 例「項目1 項目2 項目3」
 * 
 * $param: 
 *		陣列型態
 *		
 * $POSorTGET: 	
 *		指定數據傳送的方式為 $_POST(填POST) 或 $_GET(填GET), 
 *		如網址『index.php?p=1&q=2』擇填 GET
 *
 * $POSorTGET_key:	
 *		識別的$_POST或$_GET鍵, 
 * 		如網址『index.php?p=1&q=2』擇填 q
 *
 * $default_value:
 *		當初次進入沒有任何$POSorTGET_key時，將使用指定的鍵
 *		如網址『index.php?p=1』因沒有&q=任何數字, 所以這時若指定$default_value = 3,
 *		就等同網址『index.php?p=1&q=3』
 *
 *  範例：
	title_bar(array(
		1	=>	array('title' => '項目1', 'href' => 'index.php?p=1&q=1'),
		2	=>	array('title' => '項目2', 'href' => 'index.php?p=1&q=2'),
		3	=>	array('title' => '項目3', 'href' => 'index.php?p=1&q=3')
		), 
		"GET", 
		"q",
		"3"
		);

 */
 
function title_bar($param, $POSorTGET, $POSorTGET_key, $default_value) {
	
	if (!is_array($param)) die("title_bar() 參數$param 型態為陣列");
	
	if ($POSorTGET == "POST") 		$POSorTGET = $_POST;
	elseif ($POSorTGET == "GET") 	$POSorTGET = $_GET;
	else 							die("title_bar() 參數$POSorTGET 請指定POST或GET");

	if (empty($POSorTGET[$POSorTGET_key])) $POSorTGET[$POSorTGET_key] = $default_value;
	?>
    <style>
	.FrontEnd_BackEnd_tititem { 
		float:left;
		padding:5px;
		margin:15px 0px;
		border-radius:5px;
		box-shadow:0px 0px 15px 1px rgba(0,0,0,0.2)
		}
		
	.FrontEnd_BackEnd_tititem .it {
		padding:0px 9px;
		}    
	.FrontEnd_BackEnd_tititem .is { 
		color:red
		}
    </style>
    <div style="clear:both;"></div>
	<div class="FrontEnd_BackEnd_tititem">
    	<? foreach ($param as $key => $DataInfo) {?>
            <a	href="<?=$DataInfo['href']?>"
            	class="it <?=$POSorTGET[$POSorTGET_key] == $key ? "is" : NULL; ?>" 
            >
				<?=$DataInfo['title'] ?>
            </a>
    	<? } ?>
    </div>
    <div style="clear:both;"></div>
	<?
	}



//Facebook dialog 分享
function fb_dialog_share ($include_SDK, $param = NULL) {
	if ($include_SDK == "use") {
		?>
        <script src='http://connect.facebook.net/zh_TW/all.js'></script>
		<?
		}
	?>
    <div id='fb-root'></div>
    <script> 
      FB.init({appId: "553541354665282", status: true, cookie: true});

      function postToFeed() {

        // calling the API ...
        var obj = {
          method		: 'feed',
		  /*
          redirect_uri: 'http://www.wondershow.tw',
          link: 'http://www.wondershow.tw/',
          picture: 'http://www.wondershow.tw/upload/File_2013011814374454.JPG',
          name: 'Facebook Dialogs',
          caption: 'Reference Documentation',
          description: 'Using Dialogs to interact with users.'
		  */
          redirect_uri	: '<?=$param['redirect_uri'] ?>',
          link			: '<?=$param['link'] ?>',
          picture		: '<?=$param['picture'] ?>',
          name			: '<?=$param['name'] ?>',
          caption		: '<?=$param['caption'] ?>',
          description	: '<?=$param['description'] ?>'
        };

        function callback(response) {
          document.getElementById('msg').innerHTML = "Post ID: " + response['post_id'];
        }

        FB.ui(obj, callback);
      }
    
    </script>
	<?
	}


//Facebook 讚
function fb_like_button ($include_SDK) {
	if ($include_SDK == "use") {
		?>
		<script>(function(d, s, id) {
          var js, fjs = d.getElementsByTagName(s)[0];
          if (d.getElementById(id)) return;
          js = d.createElement(s); js.id = id;
          js.src = "//connect.facebook.net/zh_TW/all.js#xfbml=1";
          fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>	
		<?
		}
	?>
    <div id="fb-root"></div>
	<div class="fb-like" data-send="true" data-width="350" data-show-faces="true"></div>	
	<?
	}


//判斷是否勾選刪除的項目
function ischk(){
	if (empty($_POST['box'])) msgdie("請勾選項目", _BACK);
	}



//須登入
function needlogin(){
	if (empty($_SESSION['member'])) {
		urlto("login.php", 0);
		die;
		}
	if ($_SESSION['member']['authority'] == 1000) {
		unset($_SESSION['member']);
		urlto("login.php", 0);
		die;
		}
	}

//Facebook share botton 
function FB_share($appid, $href, $include_script = "NO") {
	if ($include_script == "YES") {
		?><script src='http://static.ak.fbcdn.net/connect.php/js/FB.Share' type='text/javascript'/></script><?
		}
	?>
	<script>
		FB.init({appId: "<?=$appid?>", status: true, cookie: true});
	</script>
	<div style="width:65px;  float:left">
		<div id="facebook-share-button" style=" float:left;margin-left:5px">
			<fb:share-button href="<?=$href?>" type="button_count" data-width="60" style="width:60px"></fb:share-button>
		</div>
		<iframe src='' frameborder='0' scrolling='no' style=' display:none; border:none; overflow:hidden; width:530px; height:55px' allowTransparency='true' id="facebook_iframe"></iframe>
	</div>
	<?
	}



//自動判斷瀏覽器語言,  回傳陣列的語言名稱，找不到回傳"0"
function get_language(){
	
	$match_lang = array(
		'zh','en','jp'
		);
	
	//取得語言資訊陣列
	$ary = explode(",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	
	//轉小寫
	$lang = strtolower($ary[0]);
	
	
	foreach ($match_lang as $val) {
		if (substr_count($lang, $val) > 0) return $val;
		}
	return "0";
	}



//一維陣列命名 例如自動把 $ary['first'] 轉換成全域變數 $first
function name_array($ary){
	foreach ($ary as $key => $val) {
		$GLOBALS[$key] = $val;
		}
	}




//判別作業系統所採用的斷行符號 osnl : OS newline
function osnl(){
	if (strtoupper(substr(PHP_OS,0,3)=='WIN')) { 
		return "\r\n"; 
		}
	elseif (strtoupper(substr(PHP_OS,0,3)=='MAC')) { 
		return "\r"; 
		}
	else { 
		return "s\n";  //UNIX
		}
	}


//使用進階編輯器ckeditor與ckfinder
//$nameandID為textarea的name與id  $PHPp 為php路徑的前置位置 $JSp 為JS路徑
function CkeAndCkf($nameandID,$PHPp=NULL,$JSp=NULL)
	{
	//使用編輯器套件
	include_once $PHPp."ckeditor/ckeditor.php";
	$CKEditor = new CKEditor();
	$CKEditor->basePath = $JSp.'ckeditor/';
	$CKEditor->replace("$nameandID");
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
function tinyMCE4($id, $JSp=NULL, $height="400px", $weight="100%", $param = NULL)
{
	
	$document_base_url 		= (!isset($param->document_base_url)) ? "" : $param->document_base_url; //網站基本位置
	$my_albumupload_insert	= (!isset($param->my_albumupload_insert) or $param->my_albumupload_insert == 1) ? "my_albumupload_insert" : NULL;
	$my_video_insert		= (!isset($param->my_video_insert) or $param->my_video_insert == 1) ? "my_video_insert" : NULL;
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





//檢查是否登入
function chkacc()
	{
	if(empty($_SESSION['account']))
		{die("error");}
	}

//取得domain
function domain($url)
{
	strtok($url, "//");
	return strtok("/");
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
			

//檢查是否非法連結
function IllegalLink($LegalURL)
	{
	$lasturl=$_SERVER['HTTP_REFERER'];//上一頁的網址
	$val=0;
	$val=strpos($lasturl,$LegalURL);//strpos("使用者前頁網址","允許前一頁連結到此的網址")
	if($val==0)//代表沒有搜尋到字元，表示非從允許頁面連結到此
		{
		die("error!");
		}
	
	}
	
	

//使用者IP
function IP(){
	if (!empty($_SERVER['HTTP_CLIENT_IP']))
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	else
		$ip=$_SERVER['REMOTE_ADDR'];
	return $ip;
	}

	
//寫入紀錄 $content 事件內容;  $isdie_return_string=1 可回傳顯示事件內容
function wlog($content,$isdie_return_string = "0")
	{
	global $j;
	
	//$userip = $_SERVER['REMOTE_ADDR'];
	$userip 		= IP();
	$account		= $_SESSION['member']['account'];
	$page   		= "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
	$data_post 		= mysql_real_escape_string(json_encode($_POST));
	$data_get 		= mysql_real_escape_string(json_encode($_GET));
	$data_session	= mysql_real_escape_string(json_encode($_SESSION));
	$SQL = "insert into `log` (ip, account, content, page ) 
			values('{$userip}', '{$account}', '{$content}', '{$page}')";
	$j->query($SQL, $isdie_return_string) or die("log error: $SQL");
	
	if($isdie_return_string == "1") 
		die($content); //可使用終止條件
	}

//目前網頁名稱 $secondname=0預設不傳回副檔名
function pagename($secondname=0){
	$page_name = ltrim(strrchr($_SERVER['REQUEST_URI'],"/"),"/");
	
	if($secondname == 0) {
		return $page_name = strtok($page_name,".");
		}
	return $page_name;
	
	}


//判斷瀏覽器的主機裝置
function browser_type(){
	$agent = strtolower($_SERVER['HTTP_USER_AGENT']); 
	$is_pc = (strpos($agent, 'windows nt')) ? true : false; 
	$is_iphone = (strpos($agent, 'iphone')) ? true : false; 
	$is_ipad = (strpos($agent, 'ipad')) ? true : false;
	
	if($is_pc)			return "PC"; 
	if($is_iphone) 		return "iPhone";  
	if($is_ipad) 		return "iPad";  
	else 				return "else";
	}


//提示訊息1
	function msg($string, $returnURL=NULL, $delay=1.5, $replace=NULL){
		//參數:$replace可使用js的 replace, 不讓記錄存在的換頁
		if ($returnURL && empty($replace)) {
			?><meta content="<?=$delay?>;url=<?=$returnURL?>" http-equiv="refresh"><?
			}
		if ($returnURL && $replace=="replace") {
			?>
			<script>
				setInterval(function(){
					location.replace('<?=$returnURL?>');
					},<?=$delay?>*1000);
            </script>
			<?
			}

		//wlog($string);
		BlockUI($string, $delay, $returnURL);
		/*
		?>
		<div style="width:100%;">
			<div style="text-align:center; 
						background-color:#222; 
						color:white;
						font-size:20px;
						font-weight:600;
						margin:120px auto; 
						padding:20px 80px;
						width:500px; 
						border-radius:10px;
						-moz-border-radius:10px;
						-webkit-border-radius:10px;
						word-break:break-all">
				<?=$string?>
			</div>
		</div>
		<?
		*/
		}
//提示訊息2
	function msgdie($string, $returnURL=NULL, $delay=1.5, $replace=NULL){

		if ($returnURL && empty($replace)) {
			?><meta content="<?=$delay?>;url=<?=$returnURL?>" http-equiv="refresh"><?
			}

		if ($returnURL && $replace=="replace") {
			?>
			<script>
				setInterval(function(){
					location.replace('<?=$returnURL?>');
					},<?=$delay?>*1000);
            </script>
			<?
			}
		if ($replace=="jsback") {
			?>
			<script>
				setInterval(function(){
					history.back()
					},<?=$delay?>*1000);
            </script>
			<?
			}
			
		//wlog($string);
		BlockUI($string, $delay, $returnURL);
		die;
		}


//jQ blockUI
	function BlockUI($string, $delay, $gotourl = NULL) {
		?>
        <script>
			$(function(){
				$.getScript("<?=_JS_BASEURL?>jquery.blockUI-2.4.2.js",function(){
					
					$.blockUI({
						message		:  	'<?=$string?>', 
						css			:	{
										border: 'none', 
										padding: '15px', 
										'text-align':'center',
										backgroundColor: '#000', 
										'-webkit-border-radius': '10px', 
										'-moz-border-radius': '10px', 
										opacity: 0.95, 
										color: '#FFF'
										},
						overlayCSS:  	{ 
										backgroundColor: '#FFF', 
										opacity : 1
										}, 	
						fadeIn		: 0,
						baseZ		: 0						
						});
					//setTimeout($.unblockUI, <?=$delay?>*1000); //自動關閉
					
					//增加點擊跳轉
					<? if (!empty($gotourl)) {?>
						$(".blockPage").wrap('<a href="<?=$gotourl?>"></a>').css("cursor","pointer");
					<? } ?>
					})
				})
				
        </script>
		<?
		}

//換網頁去哪裡	
	function urlto($URL,$second="0"){
		?><meta content="<?=$second?>;url=<?=$URL?>" http-equiv="refresh"><?
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
?>