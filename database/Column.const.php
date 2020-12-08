<?php	
	abstract class Column {
		//all
		const ID = "id";
		
		//author_tb
		const NAME = "name";
		const IMAGE_PIC = "imagePic";
		const AUTHOR_URL = "authorUrl";
		
		//post_tb
		const AUTHOR_ID = "authorId";
		const TITLE = "title";
		const CONTENT = "content";
		const POST_URL = "postUrl";
		const POST_PIC = "postPic";
		const CREATED = "created";

		//emaillist_tb
		//const AUTHOR_ID = "authorId";
		const EMAIL = "email";
	}
?>