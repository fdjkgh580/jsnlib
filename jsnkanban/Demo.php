<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="plugin/jquery-1.7.2.js"></script>
</head>

<body>
<?
include_once("jsnkanban.php");
$jsnkanban = new Jsnkanban;

$jsnkanban->setting->width = "400px"; //可選擇設定
$jsnkanban->setting->height = "200px"; //可選擇設定

//範例一、使用模板，什麼都不必寫
/*
$ary[] = "plugin/img.jpg";
$ary[] = "plugin/img2.jpg";
$ary[] = "plugin/img3.jpg";
$jsnkanban->play_template($ary);
*/

//範例二、可能有時候要自由發揮，那就是形邊寫html，範例如下(此範例在play_free()有)：
$jsnkanban->play_free();
?>

<div class="jsn_kbview" style="">
    
    <a class="prev" style="">
        <img src="images/left.png" >
    </a>
    <a class="next" style="">
        <img src="images/right.png" >
    </a>
    
    <div class="box" style="">
        <div id="nb_<?=++$a?>" class="kanban" style="">
        	<img src="plugin/img.jpg" style="max-width:200px; max-height:200px">
            <div>文字1</div>
        </div>
        <div id="nb_<?=++$a?>" class="kanban" style="">
        	<img src="plugin/img2.jpg" style="max-width:200px; max-height:200px">
            <div>文字2</div>
        </div>
        <div id="nb_<?=++$a?>" class="kanban" style="">
        	<img src="plugin/img3.jpg" style="max-width:200px; max-height:200px">
            <div>文字3</div>
        </div>
    </div>
    
    
</div>
<?

?>

</body>
</html>