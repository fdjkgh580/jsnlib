<?
namespace Jsnlib\Hash;

class Form 
{

	//<input> 名稱
	public $hashname 	= 	"thisisdefaultname"; 
	
	//<input> 值
	public $hashval;
	
	//hash 加密的形式
	public $hashtype 	= 	"sha512";
	
	//種子 可隨意自訂字串做為hash加密
	public $seed	 	= 	"grkiRETFREufeEFfeiwbhbgjrkegudkeso;lewrfuefn2349806uti4th884t63532ngrn";
	
	function __construct()
	{
		
		$type 			= 	$this->hashtype;
		
		$this->seed		=	hash($type, $this->seed . date("YmdHisss"));
		
		
		$this->hashname = 	hash($type, $this->hashname . $this->seed);
		
		//值 = loop (名稱 + 加密過的種子密碼)
		$this->hashval	=	$this->loophashval();
		
	}
	
	
	//利用loop加長
	function loophashval()
	{
		$innerval = hash($this->hashtype, $this->hashname . $this->seed); 
		$loopval = null;
		for ($a= 10; $a<=15; $a++) 
		{
			$loopval .= $innerval . hash($this->hashtype, $a);
		}
		return $loopval;
	}
	
	//註冊session
	function register_session()
	{
		return $_SESSION['jshform'][$this->hashname] = $this->hashval;
	}
	
	/*
	 * 因為<form>裡面的<input name>每秒受$this->seed影響，是變動的，所以把 hashname	名稱註冊到session
	*/
	function register_hashname()
	{
		return $_SESSION['jshform']['in_session_name'] = $this->hashname;
	}
		
	//放置input
	function put()
	{
		$this->register_session();
		$this->register_hashname();
		?><input name="<?=$_SESSION['jshform']['in_session_name']?>" class="" type="hidden" value="<?=$this->hashval?>" ><?
		return 1;
	}
		
	//驗證 - 使用重複循環
	function check()
	{
		$input_name = $_SESSION['jshform']['in_session_name'];

		if (($_POST[$input_name] != $_SESSION['jshform'][$input_name] ) or (empty($_POST[$input_name]) or empty($_SESSION['jshform'][$input_name]) ) ) 
		{
			?>
            <meta charset="utf-8">
			<div>程序已執行完畢，請返回前一頁重新操作!</div>
			<?
			die;
		}
		else 
		{
			//若不使用，可避免送出表單時重整無法繼續操作
			//unset($_SESSION['jshform']);
		return 1;
		}
	}
		
	// 驗證 - 不使用重複循環 (可用在只允許顯示一次的表單結果)
	function check_die():bool
	{
		$input_name = $_SESSION['jshform']['in_session_name'];
		
		if (($_POST[$input_name] != $_SESSION['jshform'][$input_name] ) or (empty($_POST[$input_name]) or empty($_SESSION['jshform'][$input_name]) ) ) 
		{
			return false;
		}
		else 
		{
			//若不使用，可避免送出表單時重整無法繼續操作
			unset($_SESSION['jshform']);
			return true;
		}
	}
	

		
		
}