<?

include_once('../Seq.php');
$jsnseq = new Jsnlib\Seq;
$param = new stdClass;


// 排序類型 1:  [排序值 => 編號]
// $data = $jsnseq->get(); 
// print_r($data);


// 排序類型 2:  [編號 => 排序值]
$data = $jsnseq->get(false); 
print_r($data);



?>