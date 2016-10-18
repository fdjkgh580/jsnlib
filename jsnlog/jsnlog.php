<?
class Jsnlog
{
    // jsnpdo
    protected $j;

    /**
     * 與資料庫連線
     * @param  $param->jsnpdo       jsnpdo 物件
     * @param  $param->sql_database 資料庫類型
     * @param  $param->hostname     位置
     * @param  $param->dbname       資料庫名稱
     * @param  $param->user         帳號
     * @param  $param->password     密碼  
     */
    public function connect($param)
    {
        //與資料庫連線
        $this->j = $param->jsnpdo;
        $this->j->connect
        (
            $param->sql_database,
            $param->hostname, 
            $param->dbname, 
            $param->user, 
            $param->password
        );
    }



    /**
     * 自動建立新的資料表
     * @param   $table_name 資料表名稱
     */
    public function create_table($table_name = "log")
    {
        $sql = 
        "
            DROP TABLE IF EXISTS `{$table_name}`;
            CREATE TABLE IF NOT EXISTS `{$table_name}` (
              `id` int(10) NOT NULL auto_increment COMMENT '編號',
              `type` varchar(500) NOT NULL COMMENT '等級類型： notice 注意 | warning 警告 | error 錯誤 ',
              `content` longtext NOT NULL COMMENT '訊息內容',
              `file` longtext NOT NULL COMMENT '檔案位置',
              `createtime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '變更時間',
              PRIMARY KEY  (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='log記錄' AUTO_INCREMENT=1 ;
        ";

        return $this->j->query($sql, NULL);   
    }


    /**
     * 設定訊息
     * @param mix       $message 混合型態。可以是字串、數字、陣列、物件
     * @param string    $type    類型 notice | warning | error
     */
    public function set($message, $type = "notice")
    {
        if (is_object($message) or is_array($message))
        {
            $message        =       json_encode($message);
        }

        $ary['type']        =        $this->j->quo($type);
        $ary['content']     =        $this->j->quo($message);
        $ary['file']        =        $this->j->quo($_SERVER['SCRIPT_FILENAME']);
        return $this->j->iary("log", $ary, "POST");
    }

    /**
     * 取得
     * @param  $type  取得類型 notice | warning | error
     * @param  $num   取得的數量
     */
    public function get($type, $num = 100)
    {
        if ($type != "all")
        {
            $andelse        .=      " and `type` = :type ";
        }
        
        return $this->j->sel("*", "log", "where 1 = 1 {$andelse}");
    }

    

}
