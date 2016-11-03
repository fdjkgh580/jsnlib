<?
namespace Jsnlib\Hash;
/*
 * 提供由AJAX送出表單時，需要驗證的hash。比對成功後，會自動拋回一組新的hash值，以增加安全性。
 */
class Ajax 
{
	
	//SESSION名稱
	public $sess_name;
	
	//hash種子
	public $seed;
	
	//預設種子
	function __construct() 
	{
		$this->seed = hash("sha512", date("HisYmddHH").microtime()); 
	} 
	
	/*
		製造hash
		$string 置入hash的字串
	*/
	private function create_hash($string)
	{
		$hash = null;
		for ($a = 0; $a < 10; $a++) $hash .= hash("sha512", $string);
		return $hash;
	}
	
	//放置
	public function put() 
	{
		$seed		= $this->seed;
		$newhash	= $this->create_hash($seed);
		return $_SESSION[$this->sess_name]['hash']	= $newhash;
	}
	
	/*
	 * 比對, 並回傳陣列(建議由外部使用json_encode()而不是此處，因為有可能外部還要夾帶參數)
	 * 若這裡直接return json_encode();就寫死了, 外部反而沒有彈性
	 * $POSTorGET_hashname : 經由AJAX傳過來的POST或GET名稱
	 */
	public function check($POSTorGET_hashname) 
	{
		$ary			= array();
		$from_hash 		= $POSTorGET_hashname;
		$here_hash		= $_SESSION[$this->sess_name]['hash'];


		//不同?
		if (($from_hash != $here_hash) or empty($from_hash) or empty($here_hash)) {
			$ary['status']		=		"error";
			$ary['from_hash']	=		$from_hash;
			$ary['here_hash']	=		$here_hash;
			}
		else {			
			//回傳一個新的hash
			$ary['status']		=		"success";
			$ary['newhash']		=		$this->put();
			}
		return $ary;
	}
	
}