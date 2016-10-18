<?
/*
// 會員系統 
//
*/

class jsnuser {
	
	//連接jsnpdo物件
	public $class_jsnpdo ;
	
	//資料表名稱
	public $table_name = "member";
	
	//註冊session 的陣列名稱
	public $sess_name = "member";

	//建立資料庫
	public function create_table(){
		$sql = "CREATE TABLE IF NOT EXISTS `{$this->table_name}` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `account` varchar(50) NOT NULL COMMENT '帳號',
			  `password` varchar(500) NOT NULL,
			  `name` varchar(500) NOT NULL,
			  `email` varchar(500) NOT NULL,
			  `authority` int(10) NOT NULL,
			  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='會員' AUTO_INCREMENT=14 ";
		
		$j = $this->class_jsnpdo;
		$j->quo($sql) or die("error 建立資料表[{$this->table}]發生錯誤");
		}
		

	//判斷主鍵帳號是否存在資料庫？	
	protected function in_database ($pri_col_name, $value, $return = "0") {
		$j			= $this->class_jsnpdo;
		$table		= $this->table_name;
		$DataInfo	= $j->selone("*", $table, "where $pri_col_name = $value ", $return);
		return $DataInfo == 0 ? "0" : "1";
		}


	//註冊 $pri_col_name:指定主鍵欄位(通常是帳號); $param 註冊屬性值
	public function insert($pri_col_name, $param, $return = "0") {
		$j			= $this->class_jsnpdo;
		$table		= $this->table_name;
		$DataInfo	= $this->in_database($pri_col_name, $param[$pri_col_name]);
		if ($DataInfo != 0) return "have";
		$j->iary($table, $param, "POST", $return);
		return "1";
		}


	//登入
	public function login($pri_col_name, $param, $return = "0") {
		$j		= $this->class_jsnpdo;
		$table	= $this->table_name;
		$SN		= $this->sess_name;
		
		if (empty($param[$pri_col_name])) die("請指定主鍵");
		
		foreach ($param as $key => $val) $andwhere .= " and $key = $val ";
		$DataInfo = $j->selone("*", $table, "where 1 = 1 $andwhere", $return);
		if ($DataInfo == 0) return "0";
		return $DataInfo;//回傳使用者資料
		}
	
	//註冊SESSION
	public function reg_sess($ary) {
		$SN = $this->sess_name;
		foreach ($ary as $key => $val) $_SESSION[$SN][$key] = $val;
		return "1";
		}
	
	
	//修改
	public function update($pri_col_name, $param, $return = "0") {
		$j			= $this->class_jsnpdo;
		$table		= $this->table_name;
		$SN			= $this->sess_name;
		$DataInfo	= $this->in_database($pri_col_name, $param['account']);
		if ($DataInfo == 0) return "0";
		$j->uary($table, $param, "where {$pri_col_name} = {$param[$pri_col_name]}", "POST", $return);
		return "1";
		}	
		
	
	//登出
	public function logout() {
		unset($_SESSION[$this->sess_name]);
		return "1";
		}
	
	//刪除
	public function delete($pri_col_name, $who, $return = "0") {
		$j			= $this->class_jsnpdo;
		$table		= $this->table_name;
		$DataInfo	= $this->in_database($pri_col_name, $who);
		if ($DataInfo == 0) return "0";
		$j->delete($table, "{$pri_col_name} = $who", $return);		
		return "1";
		}


	}


?>