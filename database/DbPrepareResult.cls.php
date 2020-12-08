<?php
	class DbPrepareResult {
		private $result;
		
		public function getResult(){
			return $this->result;
		}
		
		public function setResult($dbResult){
			while($row = $dbResult->fetch()) {
				$this->result[] = $row;
			}
		}
	}
?>