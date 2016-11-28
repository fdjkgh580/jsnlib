<?php
require_once '../vendor/autoload.php';

try 
{


    // 若有多筆資料庫切換使用
        $j  = new Jsnlib\Pdo;
        // $DB = $j->connect("mysql", "localhost", "Jsnlib_Pdo", "root", "");
        // $DB2= $j->connect("mysql", "localhost", "test", "root", "");

        // 那麼要改成這般操作
            // $DB->_id(3);
            // $DB->sel("*", "jsntable", "where id > :id");

            // $DB2->_id(3);
            // $DB2->sel("*", "type_base", "where id > :id limit 1000");

    // 連接資料庫
        $j->connect("mysql", "localhost", "Jsnlib_Pdo", "root", "");

    //建立資料庫
        $sql = "CREATE TABLE IF NOT EXISTS `jsntable` (
                      `id` int(10) NOT NULL auto_increment,
                      `title` varchar(500) NOT NULL,
                      `content` varchar(500) NOT NULL,
                      PRIMARY KEY  (`id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $resul = $j->query($sql, NULL);

    //insert 或 iary
        unset($ary);
        $ary['title']            =        $j->quo("傳統寫法 iary 1");
        $result                  =        $j->iary("jsntable", $ary, NULL);
        if ($result > 0) echo "新增成功 <br>";
        else throw new Exception("新增發生錯誤");
        

        $_POST['title']          =        $j->quo("經由POST iary 2");
        $ary['title']            =        NULL;
        $result                  =        $j->iary("jsntable", $ary, "POST");
        // 若要 debug 的參數, 可在第四個參數指定 1 str chk
            // $result                  =        $j->iary("jsntable", $ary, "POST", "chk");
        if ($result > 0) echo "新增成功 <br>";
        else throw new Exception("新增發生錯誤");


    //select 或 sel 多種用法 
        $j->_id(10);
        $DataList = $j->sel("*", "jsntable", "where id < :id ");

        // in ... or not in ....
        $place_holder            = $j->in("id", array(1, 3));
        $DataList   = $j->sel("*", "jsntable", "where id in ({$place_holder})");
        if ($DataList != false) echo "查詢 in 成功<br>";
        else throw new Exception("查詢 in 發生錯誤");

        // like
        $j->_id("%1%");
        $DataList   = $j->sel("*", "jsntable", "where id like :id");
        if ($DataList !== false) echo "查詢 like 成功<br>";
        else throw new Exception("查詢 like 發生錯誤");
        
        // beteween
        $j->_start(0);
        $j->_end(3);
        $DataList   = $j->sel("*", "jsntable", "where id between :start and :end ");
        if ($DataList !== false) echo "查詢 beteween 成功<br>";
        else throw new Exception("查詢 beteween 發生錯誤");


    //select_one 或 selone
        $j->_id(1);
        $DataInfo = $j->selone("*", "jsntable", "where id = :id");
        if ($DataInfo !== false) echo "查詢單筆成功<br>";
        else throw new Exception("查詢單筆發生錯誤");

    //update 或 uary
        unset($ary);
        $_POST = [];

        $_POST['title']             =    $j->quo("經由 POST 修改" . time());
        $j->_id(1);
        $ary['title']               =    NULL;
        $ary['content']             =    $j->quo("內容修改" . time());
        $result                     =    $j->uary("jsntable", $ary, "where id = :id", "POST");
        if ($result > 0)                 echo "修改成功 <br>";
        else throw new Exception("修改發生錯誤");

    //delete
        unset($ary);
        $_POST = [];

        $j->_id(2);
        $result                     =    $j->delete("jsntable", "id = :id");
        if ($result > 0)                 echo "刪除成功 <br>";
        else throw new Exception("刪除發生錯誤");

    //多筆增加
        $j::$get_string             =    true;
        $i=0; while($i++ < 5)
        {
            $ary['title']           =    $j->quo("使用多筆新增 {$i}");
            $ary['content']         =    $j->quo("使用多筆新增 {$i}");
            $with[]                 =    $j->iary("jsntable", $ary, "POST");
        }
        $j::$get_string             =    false;


        //若要 debug 可以添加第二個參數 Jsnpdo::with($with, 1);
        $result                     =    $j->with($with);
        // if ($result > 0)                 echo "一次多筆新增成功<br>";
        // else throw new Exception("一次多筆新增發生錯誤");


    //truncate
        $result                     =    $j->truncate("jsntable");
        if (!empty($result))             echo "清空成功<br>";
        else throw new Exception("清空發生錯誤");

    // 刪除資料表
        $sql                        =     "DROP TABLE `jsntable`";
        $result                     =     $j->query($sql, NULL);
        if ($result->queryString == $sql)
            echo "刪除資料表成功";
        else
            throw new Exception("刪除資料表錯誤");

    //end
        echo "<h1>測試成功</h1>";

}
catch(Exception $e)
{
    echo "<h2>獲取異常！</h2>";
    echo $e->getMessage() . "<br>";
    echo $e->getFile() . "<br>";
    echo $e->getLine() . "行<br>";
}
