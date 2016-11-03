<?
namespace Jsnlib\Export\Type;

require_once 'Absexport.php';

// Excel
class Excel extends \Jsnlib\Abs\Export
{
    public $map = array();
    

    //Excel行數的ABC字串陣列
    protected function abc()
    {
        $abc    =   "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
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
     * @param $titline      必
     * @param $TitleInfo    必
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
     * @param  $TitleInfo    必 | 標題
     * @param  $ContentList  必 | 批次的內容陣列
     * @param  $return_map   必 | true返回陣列地圖 false直接匯出
     */
    public function quick_export($param)
    {
        
        $titline            =   0; // title 在map陣列中的起始key
        $conline            =   1; // content 在map陣列中的起始key
        
        //製作地圖。設定標題與內容
        $param2->titline     = $titline;
        $param2->TitleInfo   = $param->TitleInfo;
        $this->set_title($param2);
        $param2              =  NULL;
        
        $param2->conline     = $conline;
        $param2->ContentList = $param->ContentList;
        $this->set_content($param2);

        $map                 = $this->map();
        if ($param->return_map === true) return $map;

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

        $O                  =   $objPHPExcel->setActiveSheetIndex(0);

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