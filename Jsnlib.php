<?

class Jsnlib {
	
	
	
	private			$path;			//路徑位置 如/lib
	private			$file_ary;		//指定路徑底下的所有.php檔案陣列
	public			$islib;			//是否先載入libpath

	//指定路徑
	public function path($path) 
	{
		$path = rtrim($path, "/ ") . "/";
		is_dir($path) or die("指定的路徑{$path}不存在!");
		$ary = glob("{$path}*", GLOB_ONLYDIR); //只要目錄
		if (empty($ary[0])) die("{$path}沒有任何程式!");
		
		$this->path		= $path;
		$this->file_ary	= $ary;
		$this->islib	= "1";
		return $this;
	}
	
	//自動載入全部
	public function autoload() 
	{
		$this->chk_islib();
		$this->incl_file();
	}
	
	//檢查必須先設置路徑	
	private function chk_islib() 
	{
		if ($this->islib != "1") die("請先設定lib();的指定路徑");
		return "1";
	}
	
	/**
	 * 手動載入
	 * @param    $classname 要載入的類別名稱
	 * @param    $isobject  false: 返回jsnlib自己並可串接。 
	 *                      true:  返回該 $classname 的物件實體化，方便使用，但就不可以串接使用。
	 */
	public function load($classname, $isobject = false) 
	{
		
		$this->chk_islib();
		
		$dirname = $this->dname($classname);
		
		//放到全域變數
		$this->global_newclass($dirname);

		if ($isobject == true)
			return new $classname;
		else
			return $this;
	}

	
	
	private function dname($dirname) 
	{
		
		//文件檔存在?
		
		$site = "{$this->path}{$dirname}/{$dirname}.php";
		$result = file_exists($site) or die("{$site}不存在!");

		//引入文件
		include_once($site);
		return $dirname;
	}
	
	//載入物件的php檔
	private function incl_file()
	{
		
		$path 	= $this->path;
		$ary	= $this->file_ary;
		
		foreach ($ary as $dirpath) 
		{
			
			//取得資料夾名稱(也是class的名稱)
			$dirname = trim(strrchr($dirpath, "/"), "/ ");  // 斜線最後一次出現之後的字串
			$dirname = $this->dname($dirname);
			
			//放到全域變數
			$this->global_newclass($dirname);
		}
	}
		
		
	//放到全域變數
	private function global_newclass($classname)
	{
		$classname = $GLOBALS[$classname] = new $classname;
	}
		
		
}	
?>