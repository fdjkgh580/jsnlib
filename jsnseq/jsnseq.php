<?
/*
 * 使用jQuery ui 做排序
 *
 */
class Jsnseq
{
	
	private $param;
	
	public function put($param)
	{
		
		// 
		// @selector 指定要排序的父元素
		// @child_selector 要排序的子元素
		// @send_selector綁定的送出元素
		// @ajaxurl AJAX送出的網址
		// @seq_startval 排序的第一個起始值
		// AJAX 成功後會呼叫 success_sequence_table(data)
		
		$this->param = $param;
		?>
		<script>
		function sequence_table(selector, child_selector, send_selector, ajaxurl) {
			$(function (){
				$(selector).sortable();
				
				$(send_selector).on("click", function (){
					
					var urstring = "";
					var startval = parseInt($(selector).attr("data-seq-startval"), 10); //起始值
					
					$(child_selector).each(function(index, element) {
						 
						//唯一編號
						var id = $(this).attr("data-id");
						 
						//目前的排序
						if (index == 0) seq = startval + index;
						else seq = seq + 1;
						 
						urstring = urstring + "&" + id + "=" + seq;
						 
						});
					console.log(urstring);
					 
					$.post(ajaxurl, {
						'jsnseq_querystring' : urstring
						}, function (data) {
							success_sequence_table(data)
						})
								 
					});
				
				})
			}
		sequence_table("<?=$param->selector?>", "<?=$param->child_selector?>", "<?=$param->send_selector?>", "<?=$param->ajaxurl?>");		
        </script>
		<?
		return $this;
	}
	
	/**
	 * 選用sortable的handle
	 * @handle_selector 指定的元素 
	 *
	 */
	public function handle($handle_selector)
	{
		?>
		<script>
        $(function (){
			$("<?=$this->param->selector?>").sortable( "option", "handle", "<?=$handle_selector?>" );	
			})
        </script>
		<?
	}
	
	
	public function get()
	{
		//過濾前後符號
		$jsnseq_querystring = $_POST['jsnseq_querystring'];
		$jsnseq_querystring = trim($jsnseq_querystring, "? ");
		$jsnseq_querystring = trim($jsnseq_querystring, "& ");
		 
		parse_str($jsnseq_querystring, $data);
		return $data;
	}
	
}

?>