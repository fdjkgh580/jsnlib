<?
namespace Jsnlib\Export\Type;

require_once 'Absexport.php';

// csv
class CSV extends \Jsnlib\Abs\Export
{
    public $map = array();

    //CSV一行的結構, 拼湊如 "hello","world","string"
    protected function format_csv_line($ContentInfo)
    {
        foreach ($ContentInfo as $cell)
        {
            $newstr         =   '"' . $cell . '"';
            $format_ary[]   =   $newstr;
        }
        return implode(",", $format_ary);
    }

    //標題
    public function set_title($param)
    {
        $param->TitleInfo       =   $this->reset_arykey($param->TitleInfo);
        $this->map[]            =   $this->format_csv_line($param->TitleInfo);
        return $this;
    }

    //內容
    public function set_content($param)
    {
        $param->ContentList     =   $this->reset_arykey($param->ContentList);
        
        //逐行
        foreach ($param->ContentList as $key => $ContentInfo)
        {
            //逐格
            $this->map[]        =   $this->format_csv_line($ContentInfo);
        }
    }

    /**
     * 輸出下載資料
     */
    protected function download($filename, $data)
    {
        header("Content-type:application");
        header("Content-Disposition: attachment; filename={$filename}");
        echo $data;
    }

    // 轉換編碼
    protected function iconv($param)
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

        return $param;
    }


    /**
     * 快速匯出
     * @param  $TitleInfo    object     必 | 標題
     * @param  $ContentList  object     必 | 批次的內容陣列
     * @param  $return_map   bool       必 | true返回陣列地圖 false直接匯出
     * @param  $iconv_from   string     選 | 編碼從哪   (如 utf-8)
     * @param  $iconv_to     string     選 | 編碼轉到哪 (如 big5)
     * @return                          返回CSV文字格式 
     */
    public function quick_export($param)
    {
        try
        {
            if (!isset($param->TitleInfo)) throw new \Exception("Undefined TitleInfo.");
            if (!isset($param->ContentList)) throw new \Exception("Undefined ContentList.");
            if (!isset($param->return_map)) throw new \Exception("Undefined return_map.");
            
            $this->iconv($param);

            //標題
            $this->set_title($param);

            //內容
            $this->set_content($param);

            $mapary = $this->map();
            $map    = implode("\r\n", $mapary);
            
            if ($param->return_map === true) return $map;

            $this->download(date("YmdHis").".csv", $map);
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
        }
    }
}