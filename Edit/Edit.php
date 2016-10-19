<?
namespace Jsnlib;

class Edit{
	
	public $class_jsnpdo; //以連接的jsnpdo物件
	
	public $URL				=	"index.php";
	public $type			= 	"textarea";
	public $width			=	"100%";		
	public $height			=	"40px";				
	
	
	public $listname		= 	"[data-jlist=singlelist]";
	public $listjid			=	"[data-jid=id]";
	public $columnname		=	"data-jcol";
	
	public $submit			=	"儲存";
	public $cancel 			=	"取消";
	public $indicator		=	"儲存中...";
	public $placeholder		=	"- 編輯文字 -";
	public $onblur			= 	"ignore";
	
	
	
	//執行
	public function update($tablename,$returnSQLstring=NULL){
		$j			= $this->class_jsnpdo;
		$key		= $_POST['column'];
		$whereid	= $_POST['id'];
		
		$ary[$key] 	= $j->quo($_POST['value']);
		$j->uary($tablename, $ary, "where id = '{$whereid}'", "POST", $returnSQLstring);
		$DataInfo = $j->selone("*", $tablename, "where id = '{$whereid}'");
		if ($DataInfo == 0) die("查無資料");
		return $DataInfo[$key] ;
		}
	
	
	
	//放置JQ
	public function put($selector, $act){
		?>
        <script>
		$(function (){
			//不必指定value 因為會自動添加
			$("<?=$selector?>").editable(
				'<?=$this->URL?>',{
				<? if (!empty($this->height)) {?>
					height			: '<?=$this->height?>',
				<? } ?>
				<? if (!empty($this->width)) {?>
					width			: '<?=$this->width?>',
				<? } ?>
				submit 			: '<?=$this->submit?>',
				type			: '<?=$this->type?>',
				cancel 			: '<?=$this->cancel?>',
				indicator		: '<?=$this->indicator?>',
				placeholder		: '<?=$this->placeholder ?>',
				onblur			: '<?=$this->onblur?>',
				
				submitdata		: function(value, settings) {
									return    { 
										act     : '<?=$act?>',
										id		: $(this).parents("<?=$this->listname?>").find("<?=$this->listjid?>").attr("id"),
										column  : $(this).attr("<?=$this->columnname?>") //要修的欄位名稱
										};
									},
				callback		: function(value, settings) {
					//如果需要callback就是用這個function
					if (typeof jeditable_callback == "function") {
						jeditable_callback();
						}
					}
				});
			})
			
		</script>
		<?
		}
	}
?>