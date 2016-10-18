<script src="plugin/jquery-1.7.2.js"></script>
<? 
include_once('jsnkanban2.php');
$jsnkanban2 = new jsnkanban2;
$jsnkanban2->delay = 1000;
$jsnkanban2->display_num = 2;
$jsnkanban2->put();

//寬高由首元素決定
?>

<div class="jsnkanban2_view" data-isani="false">
	<div class="btnblock">
        <div class="next">NEXT</div>
        <div class="prev">PREV</div>
    </div>
	<div class="container">
    	<div class="kanban"><img src="plugin/img.jpg" width="200" height="200"><span>&emsp;</span></div>
    	<div class="kanban"><img src="plugin/img2.jpg" width="200" height="200"><span>&emsp;</span></div>
    	<div class="kanban"><img src="plugin/img3.jpg" width="200" height="200"><span>&emsp;</span></div>
    </div>
</div>