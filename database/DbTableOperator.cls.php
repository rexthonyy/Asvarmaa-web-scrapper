<?php		
	include_once "DbTable.cls.php";
	include_once "DbTableQuery.cls.php";
	include_once "DbPrepareResult.cls.php";
	
	class DbTableOperator {
		
		/*	properties : columns, tokens, values
		*
		*	INSERT INTO tableName
		*	(column1, column2)
		*	VALUES
		*	(?,?)
		*/
		public function insert($dbTable, $dbTableQuery){
			$sql = "INSERT INTO ".$dbTable->getName().
					$dbTableQuery->getProperty('columns').
					"VALUES".
					$dbTableQuery->getProperty('tokens');
					
			//echo $sql; exit;
			
			$pdoObjStmt = $dbTable->getDatabase()->getDatabase()->prepare($sql);
			$pdoObjStmt->execute($dbTableQuery->getProperty('values'));
			$pdoObjStmt->closeCursor();
			
			return $dbTable->getDatabase()->getDatabase()->lastInsertId();
		}
		
		/*properties : columns, condition
		*
		*	SELECT column1, column2
		*	FROM tableName
		*	WHERE condition = value
		*	ORDER BY RAND()
		*	LIMIT 0,1
		*/
		public function read($dbTable, $dbTableQuery, $dbPrepareResult){
			$sql = "SELECT ".$dbTableQuery->getProperty('columns').
					" FROM ".$dbTable->getName().
					" ".$dbTableQuery->getProperty('condition').
					" ".$dbTableQuery->getProperty('orderBy').
					" ".$dbTableQuery->getProperty('limit');
					
			//echo $sql; exit;

			$pdoObjStmt = $dbTable->getDatabase()->getDatabase()->query($sql);
			$dbPrepareResult->setResult($pdoObjStmt);
			$pdoObjStmt->closeCursor();

			return $dbPrepareResult->getResult();
		}
		
		/*properties : columns=tokens, condition, value
		*
		*	UPDATE tableName
		*	SET column1=?, column2=?
		*	WHERE condidtion = value
		*/
		public function update($dbTable, $dbTableQuery){
			$sql = "UPDATE ".$dbTable->getName().
					" SET ".$dbTableQuery->getProperty('equality').
					" ".$dbTableQuery->getProperty('condition');

			//echo $sql; exit;
			
			$pdoObjStmt = $dbTable->getDatabase()->getDatabase()->prepare($sql);
			$pdoObjStmt->execute($dbTableQuery->getProperty('values'));
			$pdoObjStmt->closeCursor();
			$dbTable->getDatabase()->setDatabase(null);
		}
		
		/*properties : condition
		*
		*	DELETE FROM tableName
		*	WHERE condition = value
		*/
		public function delete($dbTable, $dbTableQuery){
			$sql = "DELETE FROM ".$dbTable->getName().
					" ".$dbTableQuery->getProperty('condition');
					
			//echo $sql; exit;
			
			$pdoObjStmt = $dbTable->getDatabase()->getDatabase()->query($sql);
			$pdoObjStmt->closeCursor();
		}
		
		/*
		    Supply the query string
		*/
		public function readRawSQL($sql, $database, $dbPrepareResult) {
			
			//echo $sql; exit;
			
			$pdoObjStmt = $database->getDatabase()->query($sql);
			$dbPrepareResult->setResult($pdoObjStmt);
			$pdoObjStmt->closeCursor();
			
			return $dbPrepareResult->getResult();
		}
	}
?>