<?
/**
 * 匯出如表格般的匯出檔
 */
namespace Jsnlib;

require_once 'CSV.php';
require_once 'Excel.php';

//為了簡潔呼叫，此處僅做呼叫類別不實作
class Export
{
    //excel格式
    static public function excel($param)
    {
        $Excel = new \Jsnlib\Export\Type\Excel;
        return $Excel->quick_export($param);
    }

    //csv格式
    static public function csv($param)
    {
        $CSV = new \Jsnlib\Export\Type\CSV;
        return $CSV->quick_export($param);
    }
}
