<?
session_start();
include_once("Form.php");
$form = new Jsnlib\Hash\Form;

if (isset($_POST['go']))
{	
	
	/* 1. 使用循環 */
	//echo $form->check();
	
	/* 2. 不使用循環 */
	echo $form->check_die();
	
	die;
}





?>
<form name="" method="post">
    
    <? $form->put(); ?>
    <input name="write" type="text" value="write something...">
    <input name="go" class="" type="submit" value="GOGOGO" >


</form>

