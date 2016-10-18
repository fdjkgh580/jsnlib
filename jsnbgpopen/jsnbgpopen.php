<?
class Jsnbgpopen
{
	//php的shell執行檔 ex. C:\AppServ\php5\php.exe
	public $windows;
	
	//php的shell執行檔 ex. php
	public $linux;
	
	//要放到背景處理的程式檔, 建議使用相對位置，避免在不同伺服器下的路徑問題
	protected $scriptary = array();
	
	//可以debug查看指令
	private $debug = 0;
	
	//要放到背景的程式位置
	public function add_script($ary)
	{
		foreach($ary as $script) $this->scriptary[] = $script;
		return $this;
	}
	
	//debug字串樣式
	private function debugstyle($string, $mod)
	{
		return "『{$string}』 『{$mod}』 <br>";
	}
	
	//執行windows
	private function run_windows($script)
	{
		$str = "start /b {$this->windows} {$script}";
		$mod = "w";
		if ($this->debug == 1) echo $this->debugstyle($str, $mod);
		else pclose(popen($str, $mod));
	}
	
	//執行linux
	private function run_linux($script)
	{
		$str = "nohup {$this->linux} {$script} > /dev/null &";
		$mod = "w";
		if ($this->debug == 1) echo $this->debugstyle($str, $mod);
		else pclose(popen($str,'w'));
	}
	
	//是Windows系統?
	private function iswindows()
	{
		$bool = substr(php_uname(), 0, 7) == "Windows" ? "1" : "0";
		if ($bool and empty($this->windows))	throw new Exception("請先指定windows"); 
		if (!$bool and empty($this->linux))	throw new Exception("請先指定linux"); 
		return $bool;
	}
	
	//檢測錯誤
	public function debug()
	{
		$this->debug = 1;
		return $this;
	}
	
	
	//開始執行
	public function start()
	{
		try
		{
			if (!is_array($this->scriptary)) throw new Exception("請先指定script為陣列");
			foreach ($this->scriptary as $script)
			{
				//依照不同平台處裡shell
				if ($this->iswindows()) $this->run_windows($script);
				else $this->run_linux($script);
			}
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}
}
?>