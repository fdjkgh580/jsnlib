<?
/**
 * 匯出如表格般的匯出檔
 */

// 定義抽項類別與共用程序
abstract class Abstract_jsnexport
{
	abstract public function set_title($param); 		//設定標題
	abstract public function set_content($param); 		//逐列設定內容
	abstract public function quick_export($param); 		//快速匯出程序

	//可用來重設陣列的鍵為數字
	public function reset_arykey($ary)
	{
		$i = 0;
		foreach ($ary as $val) $newary[$i++] = $val;
		return $newary;
	}

	//取得地圖陣列
	public function map()
	{
		return $this->map;
	}

}




// --------------------------------------以下為實做--------------------------------------

// csv
class Jsnexport_csv extends Abstract_jsnexport
{
	public $map = array();

	//CSV一行的結構, 拼湊如 "hello","world","string"
	protected function format_csv_line($ContentInfo)
	{
		foreach ($ContentInfo as $cell)
		{
			$newstr 		=	'"' . $cell . '"';
			$format_ary[] 	=	$newstr;
		}
		return implode(",", $format_ary);
	}

	//標題
	public function set_title($param)
	{
		$param->TitleInfo 		= 	$this->reset_arykey($param->TitleInfo);
		$this->map[]			=	$this->format_csv_line($param->TitleInfo);
		return $this;
	}

	//內容
	public function set_content($param)
	{
		$param->ContentList 	= 	$this->reset_arykey($param->ContentList);
		
		//逐行
		foreach ($param->ContentList as $key => $ContentInfo)
		{
			//逐格
			$this->map[]		=	$this->format_csv_line($ContentInfo);
		}
	}

	/**
	 * 快速匯出
	 * @param  $TitleInfo	 必 | 標題
	 * @param  $ContentList	 必 | 批次的內容陣列
	 * @param  $return_map   必 | true返回陣列地圖 false直接匯出
	 * @param  $iconv_from 	 選 | 編碼從哪 	(如 utf-8)
	 * @param  $iconv_to 	 選 | 編碼轉到哪 (如 big5)
	 * @return 				 返回CSV文字格式 
	 */
	
	public function quick_export($param)
	{
		// 若要轉編碼
		if ( isset($param->iconv_from) and isset($param->iconv_to) ) 
		{
			//先轉換標題
			foreach ($param->TitleInfo as $key => $TitleCel)
			{
				$param->TitleInfo[$key] = iconv($param->iconv_from, $param->iconv_to, $TitleCel);
			}

			//再轉換多筆的資料列內容
			foreach ($param->ContentList as $key => $ContentInfo)
			{
				//取出該列的儲存格
				foreach ($ContentInfo as $ckey => $ContentCel)
				{
					$ContentInfo[$ckey] = iconv($param->iconv_from, $param->iconv_to, $ContentCel);
				}
				
				//放回該列
				$param->ContentList[$key] = $ContentInfo;
			}
		}

		//標題
		$this->set_title($param);

		//內容
		$this->set_content($param);

		$mapary = $this->map();

		return implode("\r\n", $mapary);	
	}
}

// Excel
class Jsnexport_excel extends Abstract_jsnexport
{
	public $map = array();
	

	//Excel行數的ABC字串陣列
	protected function abc()
	{
		$abc 	=	"A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
		return explode(",", $abc);
	}

	//儲存格賦值
	protected function cell($conline, $ContentInfo, $excel_line)
	{
		$abc = $this->abc();
		$ContentInfo = $this->reset_arykey($ContentInfo);
		foreach ($ContentInfo as $infokey => $val)
		{
			$this->map[$conline][ $abc[$infokey] . $excel_line ] = $val;
		}
	}

	/**
	 * 設定標題
	 * @param $titline		必
	 * @param $TitleInfo	必
	 */
	public function set_title($param)
	{
		$this->cell($param->titline, $param->TitleInfo, $param->titline + 1);
		return $this;
	}

	
	/**
	 * 設定內容
	 * @param $conline     必
	 * @param $ContentInfo 必
	 */
	public function set_content($param)
	{
		foreach ($param->ContentList as $key => $ContentInfo)
		{
			$this->cell($param->conline, $ContentInfo, $param->conline + 1);
			$param->conline++;
		}
		return $this;
	}


	
	/**
	 * 快速匯出
	 * @param  PHPExcel_path 必 | PHPExcel套件的引用路徑如 PHPExcel_1.8.0_doc/Classes/PHPExcel.php
	 * @param  $TitleInfo	 必 | 標題
	 * @param  $ContentList	 必 | 批次的內容陣列
	 * @param  $return_map   必 | true返回陣列地圖 false直接匯出
	 */
	public function quick_export($param)
	{
		
		$titline            =	0; // title 在map陣列中的起始key
		$conline            =	1; // content 在map陣列中的起始key
		
		//製作地圖。設定標題與內容
		$param2->titline     = $titline;
		$param2->TitleInfo   = $param->TitleInfo;
		$this->set_title($param2);
		$param2              =	NULL;
		
		$param2->conline     = $conline;
		$param2->ContentList = $param->ContentList;
		$this->set_content($param2);

		$map 				 = $this->map();
		if ($param->return_map) return $map;

		//
		include_once($param->PHPExcel_path);
		if (!class_exists(PHPExcel)) die("請使用 PHPExcel 套件，並指定參數PHPExcel_path的路徑");
		$objPHPExcel = new PHPExcel();
		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
									 ->setLastModifiedBy("Maarten Balliauw")
									 ->setTitle("Office 2007 XLSX Test Document")
									 ->setSubject("Office 2007 XLSX Test Document")
									 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
									 ->setKeywords("office 2007 openxml php")
									 ->setCategory("Test result file");

		$O 					=	$objPHPExcel->setActiveSheetIndex(0);

		//逐列
		foreach ($map as $DataInfo)
		{
			// 該列逐行
			foreach ($DataInfo as $excel_key => $cell)
			{
				$O->setCellValue($excel_key, $cell);
			}
		}

		
		// Rename worksheet
		// $objPHPExcel->getActiveSheet()->setTitle('Simple');


		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);


		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="01simple.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		


	}
	

}


// --------------------------------------實體使用--------------------------------------
//為了簡潔呼叫，此處僅做呼叫類別不實作
class Jsnexport
{
	//excel格式
	static public function excel($param)
	{
		$jsnexport_excel = new Jsnexport_excel;
		return $jsnexport_excel->quick_export($param);
	}

	//csv格式
	static public function csv($param)
	{
		$Jsnexport_csv = new Jsnexport_csv;
		return $Jsnexport_csv->quick_export($param);
	}
}

?>