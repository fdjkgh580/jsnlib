<?
while($a < 3)
{
	mkdir("dir_from_script_1_" . uniqid());
	$a++;
	sleep(3);
}
?>