<?php			
	class DbTable {
		private $db;
		private $name;
		
		public function __construct($db, $name){
			$this->db = $db;
			$this->name = $name;
		}
		
		public function getDatabase(){
			return $this->db;
		}
		
		public function getName(){
			return $this->name;
		}
	}
?>