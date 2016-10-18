<?
/**
 * 提供驗證合法的路程，須要啟用session
 * 並可比對驗證值
 */
class Jsnlegalproccess
{
	

	// session名稱
	public $sess      = "jsnlib_jsnlegalproccess";


	public function start()
	{
		unset($_SESSION[$this->sess]);
		
		//雜湊值
		$_SESSION[$this->sess]['hash'] = array();
		
		//原始文字
		$_SESSION[$this->sess]['code'] = array();
		
		//當前文件位置
		$_SESSION[$this->sess]['file'] = array();

		return $this;
	}
	
	public function chk($check_ary)
	{
		try
		{
			if (!is_array($_SESSION[$this->sess])) throw new Exception("流程錯誤");
			
			//檢驗流程
			foreach ($_SESSION[$this->sess]['hash'] as $key => $session_value)
			{
				
				$file    = $_SESSION[$this->sess]['file'][$key];
				$code    = $_SESSION[$this->sess]['code'][$key];
				$thehash = $this->hash($code, $file);
				if ($thehash != $session_value) throw new Exception("流程與預期的不符合");
			}

			//檢驗碼
			foreach ($check_ary as $key => $check_value)
			{
				// 若沒指定就跳過不驗證
				if (empty($check_value)) continue;
				if (empty($_SESSION[$this->sess]['code'][$key])) throw new Exception("缺少 {$key} 的驗證程序！");
				if ($_SESSION[$this->sess]['code'][$key] != $check_value) throw new Exception("驗證錯誤！");
			}

			return $this->jsonval("success", "通過");
		}
		catch(Exception $e)
		{
			return $this->jsonval("error", $e->getMessage());
		}
	}


	// 加入流程驗證點
	public function set($key, $string)
	{
		$_SESSION[$this->sess]['hash'][$key] = $this->hash($string, $_SERVER['PHP_SELF']);
		$_SESSION[$this->sess]['code'][$key] = $string;
		$_SESSION[$this->sess]['file'][$key] = $_SERVER['PHP_SELF'];
		return $this;
	}

	//清空session
	public function unset_sess()
	{
		unset($_SESSION[$this->sess]);
	}


	//雜湊值
	protected function hash($value, $value2)
	{
		return hash("md5", $value . $value2);
	}

	//json格式
	protected function jsonval($status, $message)
	{
		$ary['status']  = $status;
		$ary['message'] = $message;
		return json_encode($ary);
	}

	
}

?>