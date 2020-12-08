<?php	
	class Database {
		
		private $db;
		
		public function __construct($db_info, $db_user, $db_pass) {
			$this->db = new PDO($db_info, $db_user, $db_pass);
		}
		
		public function getDatabase(){
			return $this->db;
		}
		
		public function setDatabase($val){
		    $this->db = $val;
		}
	}
?>