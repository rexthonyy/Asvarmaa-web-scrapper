<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    require $_SERVER['DOCUMENT_ROOT'] . '/mail/Exception.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/mail/PHPMailer.php';
    require $_SERVER['DOCUMENT_ROOT'] . '/mail/SMTP.php';
        
    $mail = new PHPMailer;
    
    $mail->isSMTP(); 
    $mail->Host = "smtp.gmail.com"; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls'; // ssl is deprecated
    
    $mail->Username = 'rexthonyy@gmail.com'; // email
    $mail->Password = 'wprceeyanjzjnwdc'; // password
    $mail->Port = 587; // TLS only
    $mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
       
	include_once('funcs.php');
	
	$isEmailSent = false;
	
	$authorDataList = getAuthorDataList();
	
	foreach($authorDataList as $authorData){
		
		//get the html structure of the authors page
		$html = file_get_html($authorData["authorUrl"]);
		
		//get the post data from the html
		$postList = getPostsFromHTML($html);
	
		//determine whether we have a new post, then create an email and send it to the contacts
		$post = $postList[0];
		//foreach($postList as $post){
			//determine if the post has already been registered in the database
			$columns = Column::ID;
			$properties = array();
			$properties['columns'] = $columns;
			$properties['condition'] = "WHERE postUrl='".$post['link']."'";
			$properties['orderBy'] = "";
			$properties['limit'] = "";
			$database = new Database(DB::INFO, DB::USER, DB::PASS);
			$dbTable = new DbTable($database, Table::POST_TB); 
			$dbTableQuery = new DbTableQuery($properties);
			$dbTableOperator = new DbTableOperator();
			$isNewPosts = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());
			
			//if this is a new post
			if(!isset($isNewPosts)){
				
				//add new post to the database
				$columns = "(".Column::AUTHOR_ID.",".Column::TITLE.",".Column::CONTENT.",".Column::POST_URL.",".Column::POST_PIC.",".Column::CREATED.")";
				$tokens = "(?, ?, ?, ?, ?, ?)";
				$values = array();
				$values[] = $authorData["authorId"];
				$values[] = $post['title'];
				$values[] = $post['content'];
				$values[] = $post['link'];
				$values[] = $post['pic'];
				$values[] = $post['time'];

				$properties = array();
				$properties['columns'] = $columns;
				$properties['values'] = $values;
				$properties['tokens'] = $tokens;
				
				$database = new Database(DB::INFO, DB::USER, DB::PASS);
				$dbTable = new DbTable($database, Table::POST_TB); 
				$dbTableQuery = new DbTableQuery($properties);
				$dbTableOperator = new DbTableOperator();
				$dbTableOperator->insert($dbTable, $dbTableQuery);
				
				//get a list of emails registered to this author
				$columns = Column::EMAIL;
				$properties = array();
				$properties['columns'] = $columns;
				$properties['condition'] = "WHERE authorId=".$authorData["authorId"];
				$properties['orderBy'] = "";
				$properties['limit'] = "";
				$database = new Database(DB::INFO, DB::USER, DB::PASS);
				$dbTable = new DbTable($database, Table::EMAILLIST_TB); 
				$dbTableQuery = new DbTableQuery($properties);
				$dbTableOperator = new DbTableOperator();
				$emailDataList = $dbTableOperator->read($dbTable, $dbTableQuery, new DbPrepareResult());
				
				if(isset($emailDataList)){
					foreach($emailDataList as $emailData){
						//send the email
						$authorName = $authorData['authorName'];
						$email = $emailData[Column::EMAIL];
						$title = $post['title'];
					    $content = $post['content'];
					    $link = $post['link'];
					    $pic = $post['pic'];
					    $created = $post['time'];
				
						$subject = "$authorName published a new post";
					    	
						$message = "
						<div style='text-align:center; font-family: ariel; font-size: normal; padding: 20px;'>
							<div>
								<img src='$pic' width='128px' height='128px'/>
							</div>
							<div>
								<h4 style='color: gray; font-size: 0.8em;'>$authorName | $created</h4>
								<h3>$title</h3>
								<p>$content</p>
								<a href='$link'>
									<button 
									style='margin-top: 10px;
									margin-bottom: 30px;
									background-color: orange; 
									outline: none; 
									border: 0; 
									border-radius: 8px; 
									padding: 10px 50px; 
									font-size: 1.2em; 
									color: white;
									cursor: pointer;'>
									View post
									</button>
								</a>
							</div>
							<hr/>
							<div style='font-size: 0.8em; color: lightgray;'>
								<p>
									To help keep your email secure, please don't forward this email.
								</p>
								<p>
									This app was created with &hearts; by <a href='http://rexanthony.ga'>Rex Anthony</a>
								</p>
							</div>
						</div>
						";
				        
						if(sendEmail($mail, $email, $subject, $message)){
						    //echo "Email sent to $email <br><br>";
						    $isEmailSent = true;
						}else{
						    echo "Email not sent to $email <br><br>";
						}
						
						$mail->ClearAllRecipients();
					}
				}
			}
		//}
	}
	
	echo $isEmailSent ? "Email sent" : "No new posts";
?>

<?php
    function sendEmail($mail, $email, $subject, $message){
       
        $mail->setFrom('rexthonyy@gmail.com', 'Tipranks.com'); // From email and name
        $mail->addAddress($email, 'Asvarmaa'); // to email and name
        $mail->Subject = $subject;
        
        $mail->isHTML(true);
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message);
        
        // $mail->isSMTP(); 
        // $mail->Host = "smtp.gmail.com"; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
        // $mail->SMTPAuth = true;
        // $mail->SMTPSecure = 'tls'; // ssl is deprecated
        
        // $mail->Username = 'rexthonyy@gmail.com'; // email
        // $mail->Password = 'wprceeyanjzjnwdc'; // password
        // $mail->Port = 587; // TLS only
        // $mail->SMTPDebug = 0; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
       
 
        //$mail->msgHTML("test body"); //$mail->msgHTML(file_get_contents('contents.html'), __DIR__); //Read an HTML message body from an external file, convert referenced images to embedded,
        //$mail->AltBody = 'HTML messaging not supported'; // If html emails is not supported by the receiver, show this body
        // $mail->addAttachment('images/phpmailer_mini.png'); //Attach an image file
        /*$mail->SMTPOptions = array(
                            'ssl' => array(
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                                'allow_self_signed' => true
                            )
                        );*/
        if(!$mail->send()){
            echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        }else{
            return true;
        }
    }
?>