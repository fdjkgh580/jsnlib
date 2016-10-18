<?
while($a < 3)
{
	mkdir("dir_from_script_2_" . uniqid());
	$a++;
	sleep(5);
}
?>