<?
namespace Jsnlib\Copy;

class Urlimg
{
	private	$url; //要抓取的圖片網址
	private $result; //供檢視活動完成的結果
	private $resizeary; // 重新縮放的陣列
	private $orgary; // 原圖複製陣列
	
	//設定抓取圖片的網址
	public function url($url)
	{
		$this->url					=	$url;
		return $this;
	}
	
	//最後就開始複製吧, 返回的是$this 供串接下去，若要減是成果請print_r public result()
	public function copy($mode = 0775)
	{
		try
		{
			if (empty($this->resizeary) and empty($this->orgary)) throw new \Exception("請先指定前置步驟，copy()是最後一個步驟");
		
			//若有使用多筆縮放
			if (is_array($this->resizeary))  foreach ($this->resizeary as $DataInfo)
			{	
				$elsename			=	$DataInfo[3] . DIRECTORY_SEPARATOR . $DataInfo[4];
				$res				=	@ImageResize($this->url, $elsename, $DataInfo[0], $DataInfo[1], 1, $DataInfo[2]);
				if (!empty($res))
					$this->result[]	=	$elsename;	
				else 					throw new \Exception("抓取圖片發生錯誤(縮放)");	

				chmod($elsename, $mode);
			}


			//若有使用多筆原圖複製, 不透過ImageResize()會較快些
			if (is_array($this->orgary)) foreach ($this->orgary as $DataInfo)
			{
				$allname			=	$DataInfo[0] . DIRECTORY_SEPARATOR . $DataInfo[1];

				$imgcontent			=	file_get_contents($this->url);
				$Handle				=	fopen($allname, "w+");
				$fr					=	fwrite($Handle, $imgcontent);  //傳回byte數
				fclose($Handle);
				chmod($allname, $mode);

				if (!empty($fr))
					$this->result[]	=	$allname;
				else 					throw new \Exception("抓取圖片發生錯誤(原始)");	
			}
			
			return $this;
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
		
	}
	
	//檢查，並返回可寫入的檔名
	public function check($save_dir, $newname)
	{
		try
		{
			if (empty($this->url))		throw new \Exception("請先用url()指定抓取圖片的網址");
			
			//過濾並檢查路徑可寫入嗎？
			$save_dir				=	rtrim($save_dir, "/\ ");
			$result					=	$this->is_write($save_dir);
			if (!$result)
			{
				$perms				=	$this->get_perms($save_dir);
				throw new \Exception("儲存的路徑{$save_dir}不可寫入，目前的權限值：{$perms}，請調整為可寫入的值，例如 0777");	
			}

			//若沒有自訂檔名
			if (empty($newname))
			{
				$newname			=	$this->get_save_name();
			}
			
			return $newname;
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
			
	}
	
	public function result()
	{
		return (!empty($this->result)) ? new \ArrayObject($this->result) : false;
	}
	
	
	
	//設定-複製原圖 org(路徑, 自訂檔名)
	public function org($save_dir, $newname = NULL)
	{
		try
		{
			$newname				=	$this->check($save_dir, $newname);
			$this->orgary[]			=	array($save_dir, $newname);
			return $this;
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	
	
	//設定-縮放的寬高品質 resize(寬, 高, 0-100的JPG壓縮品質, 路徑, 自訂檔名)
	public function resize($width, $height, $quantity, $save_dir, $newname = NULL)
	{
		try
		{
			if (!function_exists("ImageResize")) {
				require_once("plugin/ImageResize.php");
			}
			
			$newname							=	$this->check($save_dir, $newname);
			$this->resizeary[]					=	array($width, $height, $quantity, $save_dir, $newname);
			return $this;
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	
	
	//自動取得一個檔名
	public function get_save_name()
	{
		$secname	=	trim(strrchr($this->url, "."), " ");
		return uniqid() . $secname;
	}
	
	//路徑可寫入嗎
	public function is_write($save_dir)
	{
		return (!is_writable($save_dir)) ? "0" : "1";
	}
	
	//取得路徑權值
	public function get_perms($save_dir)
	{
		return substr(decoct(fileperms($save_dir)), -4);
	}
}
?>