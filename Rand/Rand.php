<?
namespace Jsnlib;

class Rand 
{
	
	//不重覆時比對用的陣列
	public $notrepeat = array();
	
	
	
	//取得使用者指定的字串型態
	public function get_type($type)
	{
		$ary = explode(",", $type);
		$typeary = array();
		foreach ($ary as $val) 
		{
			if ($val> 3) die("指定得亂數型態錯誤");
			array_push($typeary, $val);
		}
		return $typeary;
	}
	
	//依需求給亂數字
	public function sh($char)
	{
		//英文或數字
		switch($char) 
		{
			//數字
			case 1:
				return chr(rand(48, 57));	
				break;
				
			//英文大寫	
			case 2:
				return  chr(rand(65, 90));	
				break;
			
			//英文小寫
			case 3:
				return chr(rand(97, 122));	
				break;
		}
	}
	
	
	//$type 指定要取得亂數的形態
	public function rand_char($type)
	{
		
		//使用者指定的可能值陣列
		$array = $this->get_type($type);
		
		$allarray = count($array) - 1; //因為用在陣列的關係需要-1
		
		$key = rand(0,$allarray);
		
		return $this->sh($array[$key]);
	}

	
	//取單一字串
	function get($length, $type)
	{
		$a = 0;
		$str = null;

		//字串左->右
		while($a < $length) 
		{
			
			//取得亂數單一字元
			$str .= $this->rand_char($type);
			$a += 1;
		}
		return $str;
	}
	
	
	//取多個不重複字串
	function get_np($length, $quality, $type)
	{
		for ($i = 0; $i < $quality; $i++) 
		{
			$time = $i + 1; //給人看的次數
			$the_string = $this->get($length, $type);
			
			
			//檢查陣列中是否重複
			$in = in_array($the_string, $this->notrepeat);
			if (!$in) array_push($this->notrepeat, $the_string);
			else 
			{
				
				/*
				if ($reget < 1000) {
					//sleep(2);
					$i -= 1;
					$reget += 1;
					continue;
					}
				*/
				
				echo "<div> error : class jsnrand get_np <br> 在第 $time 次產生重複 </div>";
				echo "<div>假如繼續重取" . $this->get($length, $type) . "</div>";
				echo "<div>假如繼續重取" . $this->get($length, $type) . "</div>";
				echo "<div>假如繼續重取" . $this->get($length, $type) . "</div>";
				echo "<div>假如繼續重取" . $this->get($length, $type) . "</div>";
				echo "<div>假如繼續重取" . $this->get($length, $type) . "</div>";
				print_r($this->notrepeat) ;
				die;
			}
		}
	return $this->notrepeat;
	}
}
