<?
require("../../jsnpdo/jsnpdo.php");
require("../Edit.php");

$j 			= new jsnpdo;
$jsnedit 	= new Jsnlib\Edit;
$j->connect("mysql", "localhost", "wood", "root", "1234");


//接收POST
if ($_POST['act'] == "query_update") {
	$jsnedit->class_jsnpdo = $j;
	echo $jsnedit->update("member");
	die;
	}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title></title>
<script src="plubin/jquery-1.7.2.js"></script>
<script src="plubin/jquery.jeditable.js"></script>
<?
$jsnedit = new jsnedit;
$jsnedit->put(".edit", "query_update");

?>
</head>
<body>
	<?
    $DataList = $j->select("*", "member", "");
    
	?>
    <!--
    	若使用validat的JQ外掛，請注意validat 綁定的 form class若與此處的form class取名相同, 
        可能能產生錯誤
        
        
        以下為固定屬性
        
        先設定AJAX的URL : $jsnedit->URL; (預設index.php)
        再設定認定編輯的class名稱與AJAX的屬性act : $jsnedit->put(".edit", "query_update");
        
        1.每列的屬性:
            <div data-jlist="singlelist">
        
        2.建立唯一ID:
            <div data-jid="id" id="你的ID">
            
        3.要修改的欄位:
            <div data-jcol="你的欄位名稱" class="edit">
    -->
    <form name="form1" class="form1" action="">
        <table border="1" width="600" style="table-layout:fixed">
                <?
                if ($DataList != 0) {
                    foreach ($DataList as $DataInfo) {?>
                        <tr data-jlist="singlelist">
                            <td data-jid="id" id="<?=$DataInfo['id']?>">
                                <input name="box" class="box" type="hidden" value="<?=$DataInfo['id']?>">
                                <?=$DataInfo['id']?>
                            </td>
                            <td data-jcol="account" class="edit"><?=$DataInfo['account']?></td>
                            <td data-jcol="email" class="edit"><?=$DataInfo['email']?></td>
                        </tr>
                        <?
                        }
                    }
                ?>
        </table>
    </form>
</body>
</html>