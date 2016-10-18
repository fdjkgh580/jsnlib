<?php 
/**
 * 指定要把request的資料寫入到log，可供備查
 */
class Jsnrequestlog
{

	
	public $filename; //要寫入的檔案

	public $limit_num = 20;  //最多的筆數。當超過這個數字，會觸發清空方法

	//當觸發清空方法時，資料中要保留的最新筆數。
	//與 $limit_num 相隔越大，觸發清空方法次數的機會就越少，系統負荷會較小
	public $reset_num = 5;  


	public function __construct()
	{
        
	}

	//實做讀取log內容
	private function content_red()
	{
		//可自動建立檔案
		if (!file_exists($this->filename)) fopen($this->filename, "w+");

		return file_get_contents($this->filename);
	}

	/**
	 * 寫入的格式
	 * @param   $type 		    post | get
	 * @return             		array()
	 */
	private function write_format($type)
	{
		$ary['createtime']  =	date("Y-m-d H:i:s");
		$ary['type']        =	$type;
		$ary['current_url'] =	$_SERVER['HTTPS'] ? 'https' : 'http' . '://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$ary['from_url']    =	$_SERVER['HTTP_REFERER'];
		$ary['data']        = 	($type == "post") ? $_POST : $_GET;
		return $ary;
	}

	/**
	 * 實做寫入log
	 * @param   $type 		    post | get
	 * @param   $def_data 		可放置檔案裡面原有的資料
	 * @param   $iscover  		是否使用覆蓋(不在前方追加)
	 * @return             		file_put_contents()
	 */
	private function content_write($type, $def_data, $iscover = 0)
	{
		if ($iscover == 0)
		{
			$def_data 						=	json_decode($def_data, true);
			
			//初次寫入新資料
			if (empty($def_data))
			{
				$data[0]					=	$this->write_format($type);
			}

			//在檔案前方追加post或get資料
			else
			{
				$newdata 					=	$this->write_format($type);

				//放到陣列之首
				array_unshift($def_data, $newdata);
				$data 						=	$def_data;
			}
		}
		else
		{
			$data 							=	$def_data;
		}	
		

		$data 				 				=	json_encode($data);
		return file_put_contents($this->filename, $data);
	}


	//若超過指定數量，只保留前多少筆
	private function clean_oldata()
	{
		//取得當前的資料與數量
		$DataList 				=	json_decode($this->content_red());
		$current_num 			=	count($DataList);
		
		//若當前數量未超過指定數量，那離開
		if ($current_num <= $this->limit_num) return true; 


		//刪除末端陣列的筆數，直到指定的最低數量
		$popnum 				=	$current_num - $this->reset_num;
		while (++$a <= $popnum) array_pop($DataList);


		$this->content_write($type, $DataList, 1);
	}


	public function write($type)
	{
		if (empty($type)) throw new Exception("請先指定type 為 POST或是GET");

		//取得內容
		$getdata = $this->content_red();


		//寫入
		$this->content_write($type, $getdata);


		//清除過多的資料
		$this->clean_oldata();
		
	}

	/**
	 * 取得列表
	 * @param  $isoutpot 是否添加直接顯示輸出
	 */
	public function read($isoutpot = 0)
	{
		$str 		=	json_decode($this->content_red());
		
		if ($isoutpot == 0) return $str;

		echo "<pre>";
		print_r($str);
		echo "</pre>";
		return $str;
	}



}

