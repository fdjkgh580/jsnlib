<?
include_once "jsnfakestr.php";
include_once "jsntaiwanarea.php";

// 自動
		echo jsnfakestr::string_create(50);
		echo "<hr>";
//中文
		echo jsnfakestr::lang("ch")->string_create(10, 50, "...");
		echo "<hr>";

//英文
		echo jsnfakestr::lang("en")->string_create(40);
		echo "<hr>";

//date  mode: 0= mm/dd/yyyy, 1=mm/dd/yy(民國), 2=yyyy/mm/dd, 3=yy/mm/dd 
		echo jsnfakestr::date_create('2014-07-07 10:10:10','2014-08-08 10:10:10','Y/M/D H:i:s'); 
		//echo jsnfakestr::date_create('1262055681','1262099999','Y/m/d H:i:s'); 
		echo "<hr>";

//亂數
		$min ='11.24';
		$max ='234.453';
		echo jsnfakestr::number($min,$max,0);  //int= 顯純數字; float= 顯小數 
		echo "<br>";
        echo jsnfakestr::number($min,$max,1);  //int= 顯純數字; float= 顯小數 
        echo "<hr>";

//電話	type: tel=0/mobile=1    mode: 市話:(xx)xxxx-xxxx/xx-xxxx-xxxx/xxxxxxxxxx; 手機: 09xx-xxx-xxx/09xxxxxxxx
		echo jsnfakestr::phone($type=0, $mode=1); 
        echo '<br>';
        echo jsnfakestr::phone($type=0, $mode=2); 
        echo '<br>';
        echo jsnfakestr::phone($type=0, $mode=3); 
        echo '<br>';
        echo jsnfakestr::phone($type=1, $mode=1);
        echo '<br>'; 
        echo jsnfakestr::phone($type=1, $mode=2); 
        echo '<br>';
        // $val='(001)'. 22;
        // echo $val;
		echo "<hr>";

// //地址 
		$taiwan= new jsntaiwanarea;   // Dependency Injection 
		echo jsnfakestr::address($taiwan);
		echo "<hr>";

// --------- 附加方法 --------- 


// //[截字串]

// 		echo Jsnfakestr::word_limit("繁體假文輸出", "3", ".......[更多]");

// 		//--------------------------------
// 		echo "<br><br>";
// 		//--------------------------------


// //[陣列洗牌]
// 		$array = array
// 		(
// 			"星期一", "星期二","星期三",
// 			"星期四","星期五","星期六",
// 			"星期日"
// 		);
// 		$new_array = Jsnfakestr::array_random($array, 2);
// 		var_dump($new_array);
