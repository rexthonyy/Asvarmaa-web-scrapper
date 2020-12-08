<?php		
	class DbTableQuery {
		private $properties;
		
		public function __construct ($properties){
			$this->properties = $properties;			
		}
		
		public function getProperty($propertyName){
			return $this->properties[$propertyName];
		}
		
		public function getProperties(){
			return $this->properties;
		}
	}
?>