<?php
	include_once "database/DB.const.php";
	include_once "database/Table.const.php";
	include_once "database/Column.const.php";
	include_once "database/Database.cls.php";
	include_once "database/DbTable.cls.php";
	include_once "database/DbTableQuery.cls.php";
	include_once "database/DbTableOperator.cls.php";
		
	include_once('helpers/simple_html_dom.php');
	
	function getAuthorDataList(){
		$authorDataList = array();
		
		//$authorUrls[] = "https://www.tipranks.com/news/author/mayas";
		//$authorUrls[] = "structure.html";
	
		$columns = Column::ID.",".Column::AUTHOR_URL.",".Column::NAME.",".Column::IMAGE_PIC;
	
		$properties['columns'] = $columns;
		$properties['condition'] = "";
		$properties['orderBy'] = "";
		$properties['limit'] = "";
		$database = new Database(DB::INFO, DB::USER, DB::PASS);
		$dbTable = new DbTable($database, Table::AUTHOR_TB); 
		$dbTableQuery = new DbTableQuery($properties);
		$dbTableOperator = new DbTableOperator();
		$urls = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());
		
		if(isset($urls)){
			foreach($urls as $url){
				$authorData = array();
				$authorData["authorId"] = $url[Column::ID];
				$authorData["authorUrl"] = $url[Column::AUTHOR_URL];
				$authorData["authorName"] = $url[Column::NAME];
				$authorData["authorPic"] = $url[Column::IMAGE_PIC];
				$authorDataList[] = $authorData;
			}
		}
		
		return $authorDataList;
	}
	
	function getAuthorNameFromHTML($html){
		return $html->getElementByTagName('h1')->innertext;
	}
	
	function getAuthorPicFromHTML($html){
		return $html->getElementsByTagName('img', 1)->src;
	}
	
	function getPostsFromHTML($html){
		$postList = array();
		
		for($i = 0; ; $i++){
			$title = $html->getElementsByTagName('h3', $i);
			$time = $html->getElementsByTagName('time', $i);
			if(isset($title)){
				$content = $title->nextSibling();
				$link = $title->parentNode();
				$img = $link->parentNode()->parentNode()->first_child()->first_child();
				
				$post = array();
				$post['title'] = $title->innertext;
				$post['content'] = $content->innertext;
				$post['link'] = "https://www.tipranks.com".$link->href;
				$post['pic'] = $img->src;
				$post['time'] = $time->innertext;
				
				$postList[] = $post;
			}else{
				break;
			}
		}
		
		return $postList;
	}
?>