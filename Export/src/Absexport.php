<?
// 定義抽項類別與共用程序
namespace Jsnlib\Abs;

abstract class Export
{
    abstract public function set_title($param);         //設定標題
    abstract public function set_content($param);       //逐列設定內容
    abstract public function quick_export($param);      //快速匯出程序

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