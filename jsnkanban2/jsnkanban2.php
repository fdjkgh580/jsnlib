<?
class Jsnkanban2
{
	//寬高會依照首元素決定
	
	public $delay = 5000; //JS的延遲時間(毫秒)
	public $display_num	= 2; //顯示的數量
	
	
	public function put()
	{
		$this->css();
		$this->js();
		
	}
	
	private function js()
	{
		?>
		<script>
        $(function (){
        
			//自動執行
            function auto(x){
                return setInterval(function (){
                    next(x);
                    }, <?=$this->delay?>);
                }
            
			//上鎖
			function lock() {
				$(".jsnkanban2_view").attr("data-isani", "true")
				}
			
			//解鎖
			function unlock() {
				$(".jsnkanban2_view").attr("data-isani", "false")
				}
				
			//鎖定中?
			function islock() {
				if ($(".jsnkanban2_view").attr("data-isani") == "true") return true;
				return false;
				}
			
			//換下一個, x為基準值
            function next(x) {
                x = x - kanban_width;
        
				//鎖定中?
				if (islock()) return false;
				
				//進行鎖定
				lock();
		
		
                //首元素複製到最後
                $(".jsnkanban2_view .container").append( $(".jsnkanban2_view .kanban").eq(0).clone() )
                
                
                //開始移動
                $(".jsnkanban2_view .kanban").eq(0).animate({"margin-left":x + "px"}, "slow", null, function (){
                    
                    //首元素刪除
                    $(".jsnkanban2_view .kanban").eq(0).remove();
					
					//解除鎖定
					unlock();
                    });
                
                }
            
			
			
            //換上一個
			function prev() {
				
				//鎖定中?
				if (islock()) return false;
				
				//進行鎖定
				lock();
				
				//把最後一個元素放到第一個元素
				$(".jsnkanban2_view .container").prepend($(".jsnkanban2_view .kanban").eq(-1).clone());
				
				//暫時讓它隱藏在最前方
				$(".jsnkanban2_view .kanban").eq(0).css("margin-left", - kanban_width)
				
				
				//開始動畫往前一個
				$(".jsnkanban2_view .kanban").eq(0).animate({"margin-left":"0px"}, "slow", null, function (){
					
					//刪除最後一個
					$(".jsnkanban2_view .kanban").eq(-1).remove();
					
					//解鎖
					unlock();

					})
				
				
				
				}
			
			
			
            //首元素的寬
            kanban_width = $(".jsnkanban2_view .kanban").css("width");
            kanban_width = parseInt(kanban_width.replace("px",""), 10);
            
            //首元素的高
            kanban_height = $(".jsnkanban2_view .kanban").css("height");
            kanban_height = parseInt(kanban_height.replace("px",""), 10);
            
            //容納的寬高
            $(".jsnkanban2_view .container").css({
                "width":"192000px", 
                "height": kanban_height + "px"})
            
            
            //遮罩的寬高, 多餘的隱藏
            $(".jsnkanban2_view").css({
                "width":kanban_width * <?=$this->display_num?> + "px", 
                "height":kanban_height+ "px",
				"overflow":"hidden"})
            
			$(".jsnkanban2_view .btnblock").css("width", $(".jsnkanban2_view").css("width"))
			
			
            var TID = auto(0);
            
            //滑鼠移入移出啟用或暫停
            $(".jsnkanban2_view ").hover(function (){
                
                clearInterval(TID)
                }, function (){
                TID = auto(0);
                })
        
			$(".next").on("click", function (){
				next(0)
				});
			
			$(".prev").on("click", function (){
				prev(0);
				})
		
            });
        </script>
		<?
	}
	
	private function css()
	{
		?>
		<style>
        .jsnkanban2_view, .jsnkanban2_view div {
            float:left;
        }
		.jsnkanban2_view .btnblock {
			position:absolute;
			top:100px;
		}
		.jsnkanban2_view .next {
			
			float:right;
		}
		.jsnkanban2_view .prev {
			float:left;
		}
        </style>
		<?
	}
	
}

?>