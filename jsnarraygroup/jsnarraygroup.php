<?
// 將陣列分組
class Jsnarraygroup {

    /**
     * 指定每個盒子最多有多少個
     * 
     * 會自動分配盒子
     * @param   $ary          準備分裝的陣列
     * @param   $box_max      限制每個盒子可以放多少個元素
     */
    public static function length($ary, $box_max)
    {
        $box     = array();   // 盒子
        $box_key = 0;         // 盒子的鍵。用來計算當前是第幾個盒子 
        $input   = 1;         // 準備放入的序列號。計算該盒子裡面放了多少個。(這是實體的概念，所以是從第 1 個物件開始放，而不是程式邏輯的索引鍵。)

        foreach ($ary as $key => $element)
        {
            // 若準備放入的序列號 > 每個盒子的最多數量，那就放到新的盒子的第一個。
            if ($input > $box_max)
            {
                // 添加新的盒子
                $box_key++;    

                // 從盒子的第一個序列號開始
                $input   =  1;
            }

            // 下一個的序列號
            $input++;  


            $box[$box_key][] = $element;
        }

        return $box;
    }


    

    /**
     * 匹配放置
     * @param   $ary      準備分裝的陣列
     * @param   $max      最多可以接受的分組數量 
     */
    public static function each($ary, $max)
    {
        // 先產生需要的空容器
        $newbox = self::create_empty_ary($ary, $max);
        
        // 依序從 0 開始分配
        $newary = self::put($newbox, $ary, $max);

        return $newary;
    }

    // 依序從 0 開始分配
    private static function put($box, $source_ary, $max)
    {
        $key = 0;
        foreach ($source_ary as $ele)
        {
            // 若放到了最後一個陣列，就回到第1個開始放置
            if ($key >= $max)
            {
                $key = 0;
            }
            $box[$key][] = $ele;

            // 下一個位置
            $key += 1;
        }
        return $box;
    }

    // 產生空容器陣列
    private static function create_empty_ary($ary, $num)
    {
        // 目前輸入的陣列數量
        $arynum = count($ary);

        // 若要求的的數量 > 輸入的陣列數量，就使用輸入的陣列數量
        if ($num > $arynum)
        {
            $cond = $arynum;
        }
        else
        {
            $cond = $num;
        }

        for($key = 0; $key < $cond; $key++) $box[] = null;
            
        return $box;
    }
    
}