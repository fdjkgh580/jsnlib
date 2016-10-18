<?
/*
 *
 * 如 2=1&1=2&3=3 
 * 代表	編號2的排序是1
 *      編號1的排序是2
 *      編號3的排序是3
 *
 */
include_once('jsnseq.php');
$jsnseq = new Jsnseq;
$param = new stdClass;


$data = $jsnseq->get(); 
print_r($data);
 
?>