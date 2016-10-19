<?
/*
 * 這支程式主要是阻擋使用者直接讀取檔案。
 * 將檔案的實際路徑紀錄在session，而不顯是在夾帶的參數；
 * 等PHP驗證後成功，再從session中尋找對應的陣列，取出他的實際路徑
 *
*/
namespace Jsnlib;

class Filelink {
	
	//此處使用的session名稱
	private $sess = "jsnfilelink_session_name";
	
	
	//是否開起session函數
	private function isopen_session() {
		$UID = session_id();
		if (empty($UID)) die("請使用session_start();程式才可正常執行。");
		return $this;
		}
	
	//註冊session供比對
	private function set_session($param) {
		$key = $param['name'];
		$_SESSION[$this->sess][$key]['src']		= $param['src'];
		$_SESSION[$this->sess][$key]['hash']	= $param['hash'];
		return "1";
		}
	
	
	//檢查是否包含必要的參數
	private function agree_param($param) {
		if (!is_array($param))				die("參數請指定陣列形態");
		elseif (empty($param['src']))		die("請指定必要參數 src");
		elseif (empty($param['hash']))		die("請指定必要參數 hash");
		return $this;
		}	
		
		
		
	//放置夾帶參數
	public function get_param($param) {
		
		//事前準備
		$this->isopen_session()->agree_param($param);
		
		
		//系統自行指派，避免使用者定義的麻煩
		$param['name'] = uniqid();
		
		//註冊SESSION
		$this->set_session($param);
		
		//回傳需要的參數
		$returnary['name'] 	= $param['name'];
		$returnary['hash'] 	= $param['hash'];
		return "?" . http_build_query($returnary);
		}
	
	public function chk ($param) {
		
		$this->isopen_session();
		
		//判斷name是否存在、驗證碼是否相同
		foreach ($_SESSION[$this->sess] as $key => $val) {
			if ($key != $param['name'] or $_SESSION[$this->sess][$key]['hash'] != $param['hash']) continue;
			$exist = "1";
			break;
			}
			
		//不存在或驗證比對失敗
		if ($exist != "1") return "0";
		
		//取得put()時所記錄在SESSION的實際路徑
		$key = $param['name'];
		$filepath = $_SESSION[$this->sess][$key]['src'];
		
		//釋放該筆SESSION，避除了免累績，重要的是避免貼網址直接查看
		unset($_SESSION[$this->sess][$key]);
		
		return $filepath;
		}
	
	
	}

?>