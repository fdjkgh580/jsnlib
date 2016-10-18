<?
class Jsnreplace
{
	private $box = array();
	public $exclude_token;

	
	//取得剩餘的
	public function remaining($str, $token, $endtoken)
	{
		return trim(strstr($str, $token), $token);
	}
	
	//取得區間內的字串
	public function rang($str, $token, $endtoken)
	{
		$after	= $this->remaining($str, $token, $endtoken);
		return strtok($after, $endtoken); 
	}
	
	
	//轉換前的字串
	private function before_string($token, $innerstr, $endtoken)
	{
		return $token . $innerstr . $endtoken;
	}
	
	//轉換後的字串
	private function after_string($replace_token, $innerstr, $replace_endtoken)
	{
		return $replace_token . $innerstr . $replace_endtoken;
	}
	
	//放到陣列裡，這樣才能供批次replace
	private function prepare_ary($before, $after)
	{
		$this->box[] = array($before => $after);
	}
	
	//剩下文字還有要替換嗎
	//轉換嗎
	function ischange($remaining, $token)
	{
		return substr_count($remaining, $token) > 0 ? "1" : "0";
	}
	
	
	
	//包圍替換的前後對應
	public function befaft($str, $token, $replace_token, $endtoken, $replace_endtoken)
	{
		$innerstr	= $this->rang($str, $token, $endtoken);		
		$remaining	= $this->remaining($str, $token, $endtoken);
		$before		= $this->before_string($token, $innerstr, $endtoken);
		$after		= $this->after_string($replace_token, $innerstr, $replace_endtoken);
		$box		= $this->prepare_ary($before, $after);
		
		if ($this->ischange($remaining, $token)) 
		{
			$this->befaft($remaining, $token, $replace_token, $endtoken, $replace_endtoken);
		}
		return $this->box;
	}
	
	//轉換包圍
	public function wrap($str, $token, $replace_token, $endtoken, $replace_endtoken)
	{
		unset($this->box);
		//取得要變換的前後對應陣列
		$box = $this->befaft($str, $token, $replace_token, $endtoken, $replace_endtoken);
		
		
		
		
		
		if (is_array($box)) foreach ($box as $DataInfo)
		{
			
			
			$before	= key($DataInfo);
			$after	= $DataInfo[$before];
			
			if (!empty($this->exclude_token) and substr_count($before, $this->exclude_token) > 0) continue;
			
			$str	= str_replace($before, $after, $str);
		}
		
		return $str;
	}
	
	


	//開始轉換包圍 + 取代
	public function wrap_replace($str, $token, $replace_token, $endtoken, $replace_endtoken, $need_chstr, $chstr)
	{
		//取得區間內的字串
		$rang = $this->rang($str, $token, $endtoken);
		
		$newstr = str_replace($need_chstr, $chstr, $str);
		
		return $this->wrap($newstr, $token, $replace_token, $endtoken, $replace_endtoken);
	}
	
	
	
	
}
?>