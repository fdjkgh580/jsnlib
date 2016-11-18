<?
require_once ('../Seq.php');
$seq = new Jsnlib\Seq;
$param = new stdClass;
?>
<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<script>
//3.必要的！提供 sequence_table() 的callback function 
function success_sequence_table(data) 
{
    alert(data)
}

</script>

<?
//1.設置
// 
// @selector 指定要排序的父元素
// @child_selector 要排序的子元素
// @send_selector綁定的送出元素
// @ajaxurl AJAX送出的網址
// @seq_startval 排序的第一個起始值
// AJAX 成功後會呼叫 success_sequence_table(data)

$param->selector                =       "ul";
$param->child_selector          =       "li";
$param->send_selector           =       "button";
$param->ajaxurl                 =       "ajax.php";
$seq->put($param);
?>
<!--
    2.
    data-seq-startval   為排序的起始值
    data-id             為該項目的唯一編號
-->
<ul data-seq-startval="10">
    <li data-id="1">1.美語</li>
    <li data-id="2">2.日文</li>
    <li data-id="3">3.俄文</li>
    <li data-id="4">4.德文</li>
    <li data-id="5">5.法文</li>
    <li data-id="6">6.奧文</li>
    <li data-id="7">7.義文</li>
    <li data-id="8">8.英文</li>
    <li data-id="9">9.中文</li>
</ul>
 
<button type="button">GO</button>
