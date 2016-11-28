<?php 
namespace Jsnlib\Db;

class Sql 
{
    protected $ary;

    public function clean()
    {
        $this->ary = [];
    }

    public function set($db_connect, $sql_string, $column_match)
    {
        $this->ary[] = $sql_string;
        $stmt = $db_connect->prepare($sql_string);

        foreach ($column_match as $key => $match)
        {
            $stmt->bindParam($match['bindkey'], $match['value']);
        }
        return $stmt;
    }

    public function get()
    {
        return $this->ary;
    }
}