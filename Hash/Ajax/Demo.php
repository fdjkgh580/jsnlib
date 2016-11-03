<?
session_start();
require_once '../../vendor/autoload.php';


$Ajax = new Jsnlib\Hash\Ajax;

if (empty($_POST['act'])) $_POST['act'] = null;

if ($_POST['act'] == "test") {
	
	//比對
	$thehash = $_POST['thehash'];
	$array = $Ajax->check($thehash);
	
	//接收使用者輸入的資料, 請避免覆蓋到class Ajax回傳的key
	$array['user_write'] = $_POST['content'];
	
	//使用json回傳
	echo json_encode($array);
	die;
	}


/*

[使用說明]

1.設定一個<input name="隨便取名稱A" type="hidden" value="<?=$Ajax->put()?>">

2.建立一個$.post的json格式，如範例

3.其中jQuery務必夾帶參數如
	{'thehash' : 『隨便取名稱A』的值}

4.進行PHP比對時$Ajax->check(這裡務必填寫如『thehash』);

5.比對成功後會得到data.status == "success"，
再將步驟1的『隨便取名稱A』，指定PHP回傳的新hash值(由data.newhash取得)

*/



?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="plubin/jquery-1.7.2.js"></script>
<script>
$(function (){
	$(".send").on("click", function (){
		
		$.post("Demo.php", {
			'act'		:	'test',
			'content'	:	$(".text").val(),
			'thehash'	:	$(".hash").val()
			}, function (data){
				
				//錯誤
				if (data.status == "error") {
					$(".result").html("比對錯誤!<br>");
					$(".result").append("傳輸的hash : " + data.from_hash + "<br>");
					$(".result").append("伺服器的hash: " + data.here_hash + "<br>");
					}
					
				//成功
				else if (data.status == "success") {
					$(".hash").val(data.newhash);
					$(".result").html("成功!您輸入的是:" + data.user_write);
					}
					
				//例外
				else {
					$(".result").html("未預期的錯誤！");
					}
					
			},"json");
		})
	})
</script>
</head>

<body>

<textarea class="text"></textarea><br>
<input name="send" class="send" type="button" value="AJAX送出"><br>
<div class="result"></div>


<br>
這裡是要隱藏的hash以供變換，為了查看替換的動作，所以在此顯示，實際上務必使用type="hidden"：<br>
<input name="hash" type="text" class="hash" value="<?=$Ajax->put()?>">
</body>
</html>