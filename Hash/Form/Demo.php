<?
session_start();
require_once '../../vendor/autoload.php';

$form = new Jsnlib\Hash\Form;

if (!isset($_POST['go'])) $_POST['go'] = NULL;

if (isset($_POST['go']))
{	
	
	/* 1. 使用循環 */
	//echo $form->check();
	
	/* 2. 不使用循環 */
	if ($form->check_die() !== true) die("程序已執行完畢，請返回前一頁重新操作");
	
    echo "success";

	die;
}





?>
<form name="" method="post">
    
    <? $form->put(); ?>
    <input name="write" type="text" value="" placeholder="write something...">
    <input name="go" class="" type="submit" value="GOGOGO" >


</form>

