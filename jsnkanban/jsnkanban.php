<?
//請使用jsnkanban而非jsnkanban_setting
class Jsnkanban{
	
	public $setting; //設定
	
	function __construct() {
		//取得設定寫入 $setting 供後續使用
		$this->setting = new jsnkanban_setting;
		}
		
	private function css() {
		$setting 	= $this->setting;
		$width 		= $setting->width;
		$height		= $setting->height;
		?>
		<style>
            .jsn_kbview { float:left; width:<?=$width?>; height:<?=$height ?>; overflow:hidden; position: relative;}
            .jsn_kbview .prev { float:left; bottom:100px;left:0; position:absolute; cursor:pointer;}
            .jsn_kbview .next { float:right; bottom:100px;right:0; position:absolute; cursor:pointer;}
            .jsn_kbview .box { float:left; width:90000px; height:<?=$height ?>;}
            .jsn_kbview .kanban {width:<?=$width?>; height:<?=$height?>; float:left; }
        </style>
		<?
		return $this;
		}

	private function jquery(){
		$setting 	= $this->setting;
		$width 		= $setting->width;
		$height		= $setting->height;
		$delay		= $setting->delay;
		?>
		<script>
            jQuery(function (){
                
                //自動替換
                var allnum 		= jQuery(".kanban").length;
                var key 		= 0;
                var delay 		= <?=$delay?>;
                var timeoutID 	= autorun(delay, allnum); //取得timeoutID
                
				//只有一張照片就隱藏按鈕
				if (allnum <= 1) {
					jQuery(".prev, .next").css("display", "none");
					}
                
                //滑鼠移進移出啟用
                jQuery(".jsn_kbview").hover(
                    function() {
                        clearTimeout(timeoutID);
                    }, 
                    function() {
                        timeoutID = autorun(delay, allnum);
                    });
                
                //向前一個循環
                jQuery(".prev").on("click",function(){
					if (allnum <= 1) return false;
                    slideimg_prev();
                    })

                //向下一個循環
                jQuery(".next").on("click",function(){
					if (allnum <= 1) return false;
                    slideimg_next();
                    })
                
                
                function autorun(delay, allnum) {
                    timeoutID = setInterval(function (){
						if (allnum <= 1) return false;
						slideimg_next();
						}, 
						delay);
                    return timeoutID;
                    }
                
				//至上一個 (先移動元素, 再動畫)
				function slideimg_prev(){
                    var firstid = jQuery(".kanban").eq(0).attr("id"); //第一個元素ID
                    var lastid = jQuery(".kanban").eq(-1).attr("id"); //最後一個元素ID
					
					//把最後一個元素放到第一個元素, 暫時讓它隱藏在最前方
					jQuery("#" + firstid).before(jQuery("#" + lastid).css("margin-left", "-<?=$width?>")); 
                    
					//讓隱藏的第一個元素回到起點
					jQuery(".kanban").eq(0).animate({
						"margin-left" : "0"
						}, null);
					}
				
				
                //至下一個 (先動畫, 再移動元素)
				//先複製後再移動, 最後刪除
                function slideimg_next(){
                    var firstid = jQuery(".kanban").eq(0).attr("id"); //第一個元素ID
                    var lastid = jQuery(".kanban").eq(-1).attr("id"); //最後一個元素ID
					
                    jQuery(".kanban").eq(0).animate({
                        "margin-left" : "-<?=$width?>"
                        },
                        null,
                        function (){
                            //移動後...讓margin歸零，緊貼最後一個元素的右側
                            jQuery("#" + lastid).after(jQuery("#"+firstid).css("margin-left","0")); //把第一個元素(已經看不到了)放到最後一個元素
                        });
                    }
                    
                })
        </script>
		<?
		return $this;
		}
	
	private function block($array){
		?>
        <div class="jsn_kbview" style="">
            <a class="prev" style="">
                <img src="img/left.png" >
            </a>
            <a class="next" style="">
                <img src="img/right.png" >
            </a>
            <div class="box" style="">
            	<? foreach ($array as $url) {?>
                    <div id="nb_<?=++$a?>" class="kanban" style="background-image:url('<?=$url?>');"></div>
				<? } ?>
            </div>
        </div>
		<?
		}
	
	//使用不可變動的模板
	public function play_template($url_array){
		$this->css()->jquery();
		if (is_array($url_array)) {
			$this->block($url_array);
			return 1;
			}
		else die("請指定陣列參數為圖片路徑");
		}
		
	//使用者自行寫html
	public function play_free() {
		/*
		自行編輯的html範例：
		?>
        <div class="jsn_kbview" style="">
            
            <a class="next" style="">
                <img src="images/right.png" >
            </a>
            
            <div class="box" style="">
                <div id="nb_<?=++$a?>" class="kanban" style="background-image:url(web6_Product_2/a2.jpg);"></div>
                <div id="nb_<?=++$a?>" class="kanban" style="background-image:url(web6_Product_2/07.jpg);"></div>
            </div>
        </div>
		<?
		*/
		$this->css()->jquery();
		return 1;
		}
		
	}
	
class jsnkanban_setting {
	public $width		=	"800px";	//寬度
	public $height		=	"289px";	//高度
	public $delay		=	"2000";		//javascript延遲時間
	}

?>