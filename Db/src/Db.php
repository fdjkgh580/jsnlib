<?php 
namespace Jsnlib;

class Db 
{
    protected $connect; //連接資源
    protected $column;
    protected $sql;


    function __construct()
    {
        $this->column = new \Jsnlib\Db\Column();
        $this->column->clean();

        $this->sql = new \Jsnlib\Db\Sql();
        $this->sql->clean();
    }

    /**
     * 連線
     * @param   $database         資料庫類型如 mysql
     * @param   $hostname         主機位置
     * @param   $dbname           資料庫名稱
     * @param   $username         使用者名稱
     * @param   $password         使用者密碼
     * @return                    返回實體化的物件
     */
    public function connect($database, $hostname, $dbname, $username, $password)
    {
        try
        {
            $this->connect = new \PDO("{$database}:host={$hostname};dbname={$dbname}", $username, $password);
            $this->connect->query("SET NAMES 'UTF8'");

        }
        catch (\PDOException $e) 
        {
            echo $e->getMessage();
        }
    }


    public function col($key, $value, $isquote = false)
    {
        $this->column->set($key, $value, $isquote);
        return $this;
    }


    public function insert($table)
    {
        $keystring    = $this->column->get("key");
        $valstring    = $this->column->get("value");
        $column_match = $this->column->get("all");


        $stmt2 = $this->connect->prepare("INSERT INTO jsntable (title, content) VALUES ( :title , :content )");
        $stmt2->bindParam(":title", '123');
        $stmt2->bindParam(":content", $content);
        $title = 124;
        $content = 789;
        // $stmt2->bindParam(":content", "ang");
        $stmt2->execute();
        print_r($stmt2);
        die;

        $stmt      = $this->sql->set
        (
            $this->connect, 
            "INSERT INTO `{$table}` ( {$keystring} ) VALUES ( {$valstring} )",
            $column_match
        );

        $this->column->bind_param($stmt);

        $stmt->execute();

        // 提交表並清除欄位對應
        $this->column->clean();

        return $this;
    }



}