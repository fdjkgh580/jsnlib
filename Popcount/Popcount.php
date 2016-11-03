<?
/*
 * 計數器，可依照不同群組底下的ID進行辨識是否要執行+1的動作
 * SESSION格式 [群組][唯一鍵]
 * 如要判斷 	[展覽][編號A001]是否+1?
 *			[展覽][編號A007]是否+1?
 *			[文章][編號A001]是否+1?
*/
namespace Jsnlib;

class Popcount 
{
	
	//SESSION 名稱
	public $sess_name;
	
	//每隔多少秒當作新進入
	public $reset_seconds = 1;
	
	//比對的識別ID
	private $primary_id;
	
	//識別ID的所屬群組
	private $group;
	
	//短期間拜訪過?
	private function could_reset ()
	{
		$sess 			= $this->sess_name;
		$id				= $this->primary_id;
		$group			= $this->group;
		$short			= $this->reset_seconds;
		$my_last_time 	= $_SESSION[$sess][$group][$id]['time'];		
		
		
		//相隔時間 = 現在時間 - 離開的最後時間 > 指定重算的時間
		$T				= time() - $my_last_time;
		return ($T >= $short) ? "1" : "0";
	}
	
	//曾經拜訪過?
	private function is_visit()
	{
		$sess 	= $this->sess_name;
		$id		= $this->primary_id;
		$group	= $this->group;
		
		//初次進入
		if (empty($_SESSION[$sess][$group][$id])) 
		{
			return "0";
		}
	
		$short = $this->could_reset();
		if ($short == "1") 
		{
			return "0";
		}
		return "1";
	}
	
	//現在來了，所以+1
	private function update_session()
	{
		$sess 	= $this->sess_name;
		$id		= $this->primary_id;
		$group	= $this->group;
		$_SESSION[$sess][$group][$id]['time'] 	= time(); //紀錄近來的時間
		return "1";
	}
	
	/*
	 * 是否要增加呢?  
	 * $group 當對象很多時，依照分類使用的群組，已不至於容易重複使用到同樣的primary_id
	 * $primary_id識別的編號
	 */
	public function add($group, $primary_id) 
	{
		$sess 				= $this->sess_name;
		$this->primary_id 	= $primary_id;
		$this->group		= $group;
		
		//曾來過？
		$iscome = $this->is_visit();			
		if ($iscome == "0") return $this->update_session();
		else return "0";	
	}

	// 淨空 SESSION
	public function clean()
	{
		unset($_SESSION[$this->sess_name]);
	}
	
}