<?
	/*
	此類別用於輔助登入facebook的api程序
	
	一般facebook登入程序 : 
	
	在自己網站頁面A -> 
	登入facebook頁面 -> 
	登入成功 -> 
	返回自己網站的驗證資料庫會員 ->
	當驗證通過 -> 
	返回登入facebook前的頁面A
	
	*/

	class Jsnfbiden {
		
		function sess_set(){
			/*
			設定伺服器辨識是否以登入facebook的session
			原因是：Facebook登出時，用getUser許多時候還能抓到使用者編號，
			代表實際並未登出。所以在伺服器建立此session可以提供我們判斷，
			而不是藉由facebook的getUser方法
			來判斷是否登入會員。須連同此session一同判別。
			例如
			if (!empty($uid) and !empty($_SESSION['identify']['login'])) {
				//這樣就代表登入
				}
			*/
			$_SESSION['identify']['login'] = "1";
			return $this->islogin();
			}
		
		function sess_reset(){
			/*
			釋放紀錄的session。
			*/
			unset($_SESSION['identify']);
			return $this->islogin();
			}
					
		function islogin(){
			/*
			判別session是否記錄著已登入
			*/
			return $_SESSION['identify']['login'] == "1" ? "1" : "0";
			}	
			
		function login_page($url){
			/*
			要導向的登入頁面
			*/
			?><meta http-equiv="refresh" content="0;url=<?=$url?>"><?
			}
			
		function sess_remember_id($id){
			/*
			暫存facebook取得的使用者id, 可以供後續使用。
			當需要用到FB的使用者ID, 不需要再API去向FB要資料，會拖累速度。
			*/
			return $_SESSION['identify']['id'] = $id;
			}
		
		
		function sess_remember_id_get() {
			/*
			取得暫存的facebook使用者id
			*/
			return $_SESSION['identify']['id'];
			}
		
			
		function return_uri_set($uri) {
			/*
			設定要返回登入前的頁面網址，注意不是自己網站驗證資料庫的頁面
			*/
			$_SESSION['identify']['returnurl'] = $uri;
			return $_SESSION['identify']['returnurl'] == $uri ? "1" : "0";
			}
			
		function return_uri_get() {
			/*
			取得返回登入前的頁面網址
			*/
			$a = $_SESSION['identify']['returnurl'];
			return empty($a) ? "0" : $a;
			}
		}

?>