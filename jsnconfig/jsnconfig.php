<?
//設定
class Jsnconfig{
	
	
	
	/* 
	 * 	[將POST或GET取出經由addslashes加入斜線]
	 *	當get_magic_quotes_gpc() != 1, 也就是php.ini的 magic_quotes_gpc = On時。
	 *	用法: 	$POST_GET可填"POST"; "GET"; "POST,GET"
	 *			$add_remove可填"add";"remove" 控制使用addslashes或stripslashes
	*/
	public function post_slash($POST_GET, $add_remove){
		if (empty($POST_GET)) 						die("Error: Config-> post_slash()  請填入對應取出的POST或GET!");
		$ary = explode(",", $POST_GET);
		
		//依照字串逐一比對
		foreach ($ary as $str) {
			if ($str == "POST" && !empty($_POST))  $this->loopary($_POST,"POST",$add_remove); 
			elseif	($str == "GET" && !empty($_GET))  $this->loopary($_GET,"GET",$add_remove); 
			else									 		break;
			}
		return 1;
		}
		
		
	/*
	 *	
	 * 	[自動深入陣列維度到底, 把全部的值都加入addslashes]
	 *	用法: $chkary 為array或字串 ; $type 為 POST 或 GET
	*/
	private function loopary($chkary, $type, $add_remove){
		foreach ($chkary as $key => $val){
			
			//呼叫自己直到取完陣列的維度
			if (is_array($val)) $this->loopary($val, $type, $add_remove);
			else {
				if ($type == "POST") {
					if ($add_remove == "add") $_POST[$key] = addslashes($val);
					else $_POST[$key] = stripslashes($val);
					}
				elseif ($type == "GET") {
					if ($add_remove == "add") $_GET[$key] = addslashes($val);
					else $_GET[$key] = stripslashes($val);
					}
				}
			}
		return 1;
		}	
		
	}

?>