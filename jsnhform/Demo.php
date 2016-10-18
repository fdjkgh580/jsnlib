<?
session_start();
include_once("jsnhform.php");
$hform = new Jsnhform;

if (isset($_POST['go'])){	
	
	/* 1. 使用循環 */
	//echo $hform->check();
	
	/* 2. 不使用循環 */
	echo $hform->check_die();
	
	die;
	}





?>
<form name="" method="post">
    
    <? $hform->put(); ?>
    <input name="write" type="text" value="write something...">
    <input name="go" class="" type="submit" value="GOGOGO" >


</form>

