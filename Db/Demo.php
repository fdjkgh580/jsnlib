<?
require_once '../vendor/autoload.php';

$db = new \Jsnlib\Db;

// CONNECT
$db->connect("mysql", "localhost", "jsnlib_pdo", "root", "");

// INSERT
$db
    ->col("title", 'Sun')
    ->col("content", '星期日')
    ->insert('jsntable');
die;

$db
    ->col('title', 'Mon')
    ->col('content', '星期一', false)
    ->insert('jsntable');


$j->run();


$j->col('price', 'price + 10000', false)
    ->match(":id", 18)
    ->match(":key", 'HelloWorld')
    ->where("id = :id and key = :key")
    ->update('jsntable');

$j->run();