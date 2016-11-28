<?php 
namespace Jsnlib\Db;

class Column 
{
    protected $ary;

    public function clean()
    {
        $this->ary   = [];
    }

    public function set($column, $value, $isquote = true)
    {
        $match = 
        [
            "column" => $column, 
            "bindkey" => ":" . uniqid("{$column}_"), 
            "value" => "'$value'"
        ];
        \array_push($this->ary, $match);
    }

    public function get($type = false)
    {
        $back = [];

        if ($type == "key") 
        {
            foreach ($this->ary as $key => $match)
            {
                $back[] = "`{$match['column']}`";
            }
        }

        elseif ($type == "value")
        {
            foreach ($this->ary as $key => $match)
            {
                $back[] = "`{$match['bindkey']}`";
            }
        }
        elseif ($type == "all")
        {
            return $this->ary;
        }

        return implode(" , ", $back);
    }

    public function bind_param($stmt)
    {
        foreach ($this->ary as $key => $match)
        {
            $stmt->bindParam($match['bindkey'], $match['value']);
        }
    }
}