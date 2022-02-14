<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods:GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
header("Content-Type: application/json; charset=utf-8");
header("Content-Type: multipart/form-data");

$conn = mysqli_connect('localhost','username','password','databasename');

$data = json_decode(file_get_contents('php://input'), true);

if($data['action'] == 'signup') {
	$pass = hash('sha256', $data['pass']);
	$tel = $data['tel']['internationalNumber'];
	$name = $data['name'];
	$email = $data['email'];
	
	$checkuser1 = mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_email='$data[email]'");
	$checkuser2 = mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_tel='$tel'");
	
	if(mysqli_num_rows($checkuser1) > 0) {
		$result = json_encode(array('success' => false, 'msg' => 'Email is already registered.'));
	} else if (mysqli_num_rows($checkuser2) > 0) {
		$result = json_encode(array('success' => false, 'msg' => 'Phone Number is already registered.'));
	} else  {
		$insertuser = mysqli_query($conn, "INSERT INTO buku555_user
		(user_id, user_name, user_username,
		user_email, user_tel, user_pass, user_confirm) VALUES
		(UUID(),'$data[name]', '$data[username]', '$data[email]',
		'$tel','$pass', 'NO')");
		
		if($insertuser) {
			$getuser = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_email='$data[email]'"));
			$to = $email;
			$confirmlink = "https://www.sukaa.my/buku555/api/api.php?emailconfirmation=".$getuser['user_id'];
			$subject = "Registration Buku 555 App: Email Confirmation";
			$message = "<b>Registration</b>";
			$message .= "<h1>Buku 555 Registration</h1>";
			$message .= "<p>Hi ".$name."!</p><br/>";
			$message .= "<p>Thank you for signing up with us. Click this link ".$confirmlink." to verify your email. Don't forget to rate our app in Google Play.</p><br/>";
			$message .= "<p>Stay in touch!</p><br/>";
			$message .= "<p>Kindly Regards,</p>";
			$message .= "<p>Suhaimi Masri</p>";
			$message .= "<p>Owner of Buku 555</p>";
			
			$header = "From:noreply@sukaa.my \r\n";
			$header .= "MIME-Version: 1.0\r\n";
			$header .= "Content-type: text/html\r\n";
			
			mail($to, $subject, $message, $header);
			
			$result = json_encode(array('success' => true, 'msg' => 'Register successfully'));
		} else {
			$result = json_encode(array('success' => false, 'msg' => 'Register error'));
		}
	}
	echo $result;
}


if(isset($_GET['emailconfirmation'])) {
	$id = $_GET['emailconfirmation'];
	
	$setemail = mysqli_query($conn, "UPDATE buku555_user SET user_confirm='YES' WHERE user_id='$id'");
	
	if($setemail) {
		$getuser = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_id='$id'"));
		$to = $getuser['user_email'];
		$subject = "Registration Buku 555 App: Email Verified";
		$message = "<b>Registration</b>";
		$message .= "<h1>Buku 555 Registration</h1>";
		$message .= "<p>Hi ".$getuser['user_name']."!</p><br/>";
		$message .= "<p>Your email is verified. Thank you for signing with us. Don't forget to rate our app in Google Play.</p><br/>";
		$message .= "<p>Stay in touch!</p><br/>";
		$message .= "<p>Kindly Regards,</p>";
		$message .= "<p>Suhaimi Masri</p>";
		$message .= "<p>Owner of Buku 555</p>";
			
		$header = "From:noreply@sukaa.my \r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/html\r\n";
			
		mail($to, $subject, $message, $header);
	}
}

if($data['action'] == 'signin') {
	$pass = hash('sha256', $data['pass']);
	$storage = array();
	$fri = array();
	$req = array();
	$test = true;
	$checkuser = mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_email='$data[email]' AND user_pass='$pass'");
	
	if(mysqli_num_rows($checkuser) > 0) {
		$user = mysqli_fetch_array($checkuser);
		
		$confirm = $user['user_confirm'];
		
		if ($confirm == 'YES') {
			$storage[] = array (
				'user_id'	=> $user['user_id'],
				'user_name'	=>	$user['user_name'],
				'user_username'	=>	$user['user_username'],
				'user_email'	=>	$user['user_email'],
				'user_tel'	=>	$user['user_tel']
			);
			
			$checkfriend1 = mysqli_query($conn, "SELECT * FROM buku555_friend WHERE friend_firstuser='$user[user_id]' AND friend_approval='FRIEND'");
			$checkfriend2 = mysqli_query($conn, "SELECT * FROM buku555_friend WHERE friend_seconduser='$user[user_id]' AND friend_approval='FRIEND'");
			$checkreq = mysqli_query($conn, "SELECT * FROM buku555_friend WHERE friend_seconduser='$user[user_id]' AND friend_approval='PENDING'");
			
			if(mysqli_num_rows($checkfriend1) > 0){
				while($fri1 = mysqli_fetch_array($checkfriend1)) {
					$getuser1 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_id='$fri1[friend_seconduser]'"));
					
					$fri[] = array (
						'friend_id'	=>	$getuser1['user_id'],
						'friend_name'	=>	$getuser1['user_name'],
						'friend_username'	=>	$getuser1['user_email'],
						'friend_email'	=>	$getuser1['user_email'],
						'friend_tel'	=>	$getuser1['user_tel']
					);
				}
			}
			
			if(mysqli_num_rows($checkfriend2) > 0) {
				while($fri2 = mysqli_fetch_array($checkfriend2)) {
					$getuser2 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_id='$fri2[friend_firstuser]'"));
					
					$fri[] = array (
						'friend_id'	=>	$getuser2['user_id'],
						'friend_name'	=>	$getuser2['user_name'],
						'friend_username'	=>	$getuser2['user_email'],
						'friend_email'	=>	$getuser2['user_email'],
						'friend_tel'	=>	$getuser2['user_tel']
					);
				}
			}
			
			if(mysqli_num_rows($checkreq) > 0) {
				while($reqs = mysqli_fetch_array($checkreq)) {
					$getuser3 = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_id='$reqs[friend_firstuser]'"));
					
					$req[] = array (
						'friend_id'	=>	$getuser3['user_id'],
						'friend_name'	=>	$getuser3['user_name'],
						'friend_username'	=>	$getuser3['user_email'],
						'friend_email'	=>	$getuser3['user_email'],
						'friend_tel'	=>	$getuser3['user_tel']
					);
				}
			}
			
			$result = json_encode(array('success' => true, 'confirm' => 'YES', 'result' => $storage, 'friend' => $fri, 'friendrequest' => $req));
		} else if ($confirm == 'NO') {
			$result = json_encode(array('success' => true, 'confirm' => 'NO'));
		}
	} else  {
		$result = json_encode(array('success' => false));
	}
	
	echo $result;
}

if($data['action'] == 'loaduser') {
	$checkuser = mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_username='$data[username]' AND NOT user_id='$data[id]'");
	$searchs = array();
	$search = array();
	if(mysqli_num_rows($checkuser) > 0) {
		$checkfriend = mysqli_fetch_assoc($checkuser);
		$friendid = $checkfriend['user_id'];
		
		$id = $data['id'];
		$checkdb = mysqli_query($conn, "SELECT * FROM buku555_friend WHERE friend_firstuser='$id' AND friend_seconduser='$friendid'");
		
		if(mysqli_num_rows($checkdb) > 0) {
			$status = mysqli_fetch_assoc($checkdb);
			$approval = $status['friend_approval'];
			
			if($approval == 'FRIEND') {
				$result = json_encode(array('success' => false));
			} else if ($approval == 'PENDING') {
				$checkuser2 = mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_username='$data[username]'");
			while($user = mysqli_fetch_array($checkuser2)) {
				$searchs[] = array (
					'user_id'	=> $user['user_id'],
					'user_name'	=>	$user['user_name'],
					'user_username'	=> $user['user_username'],
					'user_email'	=>	$user['user_email'],
					'user_tel'	=>	$user['user_tel']
				);
				}
		
				$result = json_encode(array('success' => true, 'result' => $searchs));
		}
			$result = json_encode(array('success' => false));
		} else {
			$checkuser1 = mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_username='$data[username]' AND NOT user_id='$data[id]'");
			while($user = mysqli_fetch_array($checkuser1)) {
			$search[] = array (
				'user_id'	=> $user['user_id'],
				'user_name'	=>	$user['user_name'],
				'user_username'	=> $user['user_username'],
				'user_email'	=>	$user['user_email'],
				'user_tel'	=>	$user['user_tel']
			);
		}
		
		$result = json_encode(array('success' => true, 'result' => $search));
		}
		
	} else {
		$result = json_encode(array('success' => false));
	}
	
	echo $result;
}

if($data['action'] == 'requestfriend') {
	
	$checkuser = mysqli_query($conn, "SELECT * FROM buku555_friend WHERE friend_firstuser='$data[id]' AND friend_seconduser='$data[friendid]'");
	
	if(mysqli_num_rows($checkuser) > 0) {
		$getstatus = mysqli_fetch_assoc($checkuser);
		$status = $getstatus['friend_approval'];
		
		if($status == 'PENDING') {
			$result = json_encode(array('success' => false, 'msg' => 'You already sent a request.'));
		}
	} else {
		$sendapprove = mysqli_query($conn, "INSERT INTO buku555_friend 
	(friend_id, friend_firstuser, friend_seconduser, 
	friend_approval) VALUES 
	(UUID(), '$data[id]', '$data[friendid]', 'PENDING')");
	
	if($sendapprove) {
		$result = json_encode(array('success' => true, 'msg' => 'Friend request has been sent.'));
	} else {
		$result = json_encode(array('success' => false, 'msg' => 'Error'));
	}
	}
	
	echo $result;
}

if($data['action'] == 'checkfriendrequest') {
	$getdata = mysqli_query($conn, "SELECT * FROM buku555_friend WHERE friend_seconduser='$data[id]' AND friend_approval='PENDING'");
	$a = array();
	if(mysqli_num_rows($getdata) > 0) {
		while($request = mysqli_fetch_array($getdata)) {
			$uuu = $request['friend_firstuser'];
			$getfriend = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_id='$uuu'"));
			$a[] = array (
				'friend_id'	=>	$getfriend['user_id'],
				'friend_name'	=>	$getfriend['user_name'],
				'friend_username'	=>	$getfriend['user_username'],
				'friend_email'	=>	$getfriend['user_email'],
				'friend_tel'	=>	$getfriend['user_tel']
			);
			
			
		}
		$result = json_encode(array('success' => true, 'list' => $a));
	} else {
		$result = json_encode(array('success' => false, 'list' => 'Error:'.$data['id']));
	}
	
	echo $result;
}

if($data['action'] == 'sendfriendrequest') {
	$getdata = mysqli_query($conn, "SELECT * FROM buku555_friend WHERE friend_seconduser='$data[id]' AND friend_approval='PENDING' AND friend_notify IS NULL");
	$a = array();
	if(mysqli_num_rows($getdata) > 0) {
		while($request = mysqli_fetch_array($getdata)) {
			$uuu = $request['friend_firstuser'];
			$getfriend = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_id='$uuu'"));
			$a[] = array (
				'friend_id'	=>	$getfriend['user_id'],
				'friend_name'	=>	$getfriend['user_name'],
				'friend_username'	=>	$getfriend['user_username'],
				'friend_email'	=>	$getfriend['user_email'],
				'friend_tel'	=>	$getfriend['user_tel']
			);
			
			mysqli_query($conn, "UPDATE buku555_friend SET friend_notify='1' WHERE friend_id='$request[friend_id]'");
		}
		$result = json_encode(array('success' => true, 'list' => $a));
	} else {
		$result = json_encode(array('success' => false, 'list' => 'Error:'.$data['id']));
	}
	
	echo $result;
}

if($data['action'] == 'checkfriend') {
	$getdata1 = mysqli_query($conn, "SELECT * FROM buku555_friend WHERE friend_firstuser='$data[id]' AND friend_approval='FRIEND'");
	$getdata2 = mysqli_query($conn, "SELECT * FROM buku555_friend WHERE friend_seconduser='$data[id]' AND friend_approval='FRIEND'");
	$a = array();
	if(mysqli_num_rows($getdata1) > 0) {
		while($request = mysqli_fetch_array($getdata1)) {
			$uuu = $request['friend_seconduser'];
			$getfriend = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_id='$uuu'"));
			$a[] = array (
				'friend_id'	=>	$getfriend['user_id'],
				'friend_name'	=>	$getfriend['user_name'],
				'friend_username'	=>	$getfriend['user_username'],
				'friend_email'	=>	$getfriend['user_email'],
				'friend_tel'	=>	$getfriend['user_tel'],
				'security'	=>	$request['friend_securitycode']
			);
			
			
		}
		
	} 
	
	if(mysqli_num_rows($getdata2) > 0) {
		while($request1 = mysqli_fetch_array($getdata2)) {
			$uuu = $request1['friend_firstuser'];
			$getfriend = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_id='$uuu'"));
			$a[] = array (
				'friend_id'	=>	$getfriend['user_id'],
				'friend_name'	=>	$getfriend['user_name'],
				'friend_username'	=>	$getfriend['user_username'],
				'friend_email'	=>	$getfriend['user_email'],
				'friend_tel'	=>	$getfriend['user_tel'],
				'security'	=>	$request1['friend_securitycode']
			);
		}
	}
	if ($a != null) {
		$result = json_encode(array('success' => true, 'list' => $a));
	} else {
		$result = json_encode(array('success' => false));
	}
	
	echo $result;
}

if($data['action'] == 'removereq') {
	$removerq = mysqli_query($conn, "DELETE FROM buku555_friend WHERE friend_firstuser='$data[friendid]' AND friend_seconduser='$data[id]'");
	
	if($removerq) {
		$result = json_encode(array('success' => true, 'msg' => 'Remove successfully'));
	} else {
		$result = json_encode(array('success' => false, 'msg' => 'Remove denied'));
	}
	echo $result;
}

if($data['action'] == 'acceptreq') {
	$newid = $data['id'].$data['friendid'];
	$enc = hash('sha256', $newid);
	$acceptrq = mysqli_query($conn, "UPDATE buku555_friend SET friend_approval='FRIEND', friend_securitycode='$enc' WHERE friend_firstuser='$data[friendid]' AND friend_seconduser='$data[id]'");
	
	if($acceptrq) {
		$result = json_encode(array('success' => true, 'msg' => 'Add Successfully'));
	} else {
		$result = json_encode(array('success' => false, 'msg' => 'Add denied'));
	}
	echo $result;
}

if($data['action'] == 'addpersonal') {
	
	$addpersonal = mysqli_query($conn, "INSERT INTO buku555_debt (
	debt_id, 
	debt_name,
	debt_desc,
	debt_type,
	debt_category,
	debt_amount,
	debt_createdate,
	debt_duedate,
	user_id
	) VALUES (
	UUID(),
	'$data[name]',
	'$data[desc]',
	'$data[type]',
	'PERSONAL',
	'$data[amount]',
	NOW(),
	'$data[duedate]',
	'$data[id]'
	)");
	
	if($addpersonal) {
		$result = json_encode(array('success' => true, 'msg' => 'Success'));
	} else {
		$result = json_encode(array('success' => false, 'msg' => 'Failed'));
	}
	
	echo $result;
}

if($data['action'] == 'adddebtfriend') {
	
	$addpersonal = mysqli_query($conn, "INSERT INTO buku555_debt (
	debt_id, 
	debt_name,
	debt_desc,
	debt_type,
	debt_category,
	debt_amount,
	debt_createdate,
	debt_duedate,
	debt_friendkey,
	debt_secondid,
	user_id
	) VALUES (
	UUID(),
	'$data[name]',
	'$data[desc]',
	'$data[type]',
	'FRIEND',
	'$data[amount]',
	NOW(),
	'$data[duedate]',
	'$data[contact]',
	'$data[secondid]',
	'$data[id]'
	)");
	
	if($addpersonal) {
		$result = json_encode(array('success' => true, 'msg' => 'Success'));
	} else {
		$result = json_encode(array('success' => false, 'msg' => 'Failed'));
	}
	
	echo $result;
}

if($data['action'] == 'loadlend') {
	
	$lend = array();
	$checkq = true;
	$checkqq = true;
	
	$getdata = mysqli_query($conn, "SELECT * FROM buku555_debt WHERE user_id='$data[id]' AND debt_type='Lend' AND debt_category='PERSONAL'");
	$getdata2 = mysqli_query($conn, "SELECT * FROM buku555_debt WHERE user_id='$data[id]' AND debt_type='Lend' AND debt_category='FRIEND'");
	
	if(mysqli_num_rows($getdata) > 0) {
		
		$checkq = true;
		
		while($x = mysqli_fetch_array($getdata)) {
			
			$lend[] = array (
				'debt_id'	=>	$x['debt_id'],
				'debt_name'	=>	$x['debt_name'],
				'debt_desc'	=>	$x['debt_desc'],
				'debt_type'	=>	$x['debt_type'],
				'debt_category'	=> 	$x['debt_category'],
				'debt_amount'	=> 	$x['debt_amount'],
				'debt_paid'		=> 	$x['debt_paid'],
				'debt_createdate'	=>	$x['debt_createdate'],
				'debt_duedate'	=>	$x['debt_duedate'],
				'user_id'	=>	$x['user_id']
			);
		}
	} else {
		$checkq = false;
	}
	
	if(mysqli_num_rows($getdata2) > 0) {
		
		$checkqq = true;
		
		while($y = mysqli_fetch_array($getdata2)) {
			$h = $y['debt_secondid'];
			$getdetail = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_id='$h'"));
			
			$lend[] = array (
				'debt_id'	=>	$y['debt_id'],
				'debt_name'	=>	$y['debt_name'],
				'debt_desc'	=>	$y['debt_desc'],
				'debt_type'	=>	$y['debt_type'],
				'debt_category'	=>	$y['debt_category'],
				'debt_amount'	=>	$y['debt_amount'],
				'debt_paid'	=>	$y['debt_paid'],
				'debt_createdate'	=>	$y['debt_createdate'],
				'debt_duedate'	=>	$y['debt_duedate'],
				'user_id'	=>	$y['user_id'],
			 	'user_secondid'	=>	$y['debt_secondid'],
				'debtname'	=>	$getdetail['user_name'],
				'debtusername'	=>	$getdetail['user_username'] 
			);
		}		
	} else  {
		$checkqq = false;
	}
	
	if($checkq == true || $checkqq == true){
		$result = json_encode(array('success'	=> true, 'result'	=>	$lend));
	} else if ($checkq == false && $checkqq == false) {
		$result = json_encode(array('success'	=>	false));
	}
	
	echo $result;
	
}

if($data['action'] == 'loadborrow') {
	
	$lend = array();
	$checkq = true;
	$checkqq = true;
	
	$getdata = mysqli_query($conn, "SELECT * FROM buku555_debt WHERE debt_secondid='$data[id]' AND debt_category='PERSONAL'");
	$getdata2 = mysqli_query($conn, "SELECT * FROM buku555_debt WHERE debt_secondid='$data[id]' AND debt_category='FRIEND'");
	
	if(mysqli_num_rows($getdata) > 0) {
		
		$checkq = true;
		
		while($x = mysqli_fetch_array($getdata)) {
			
			$lend[] = array (
				'debt_id'	=>	$x['debt_id'],
				'debt_name'	=>	$x['debt_name'],
				'debt_desc'	=>	$x['debt_desc'],
				'debt_type'	=>	$x['debt_type'],
				'debt_category'	=> 	$x['debt_category'],
				'debt_amount'	=> 	$x['debt_amount'],
				'debt_paid'		=> 	$x['debt_paid'],
				'debt_createdate'	=>	$x['debt_createdate'],
				'debt_duedate'	=>	$x['debt_duedate'],
				'user_id'	=>	$x['user_id']
			);
		}
	} else {
		$checkq = false;
	}
	
	if(mysqli_num_rows($getdata2) > 0) {
		
		$checkqq = true;
		
		while($y = mysqli_fetch_array($getdata2)) {
			$h = $y['debt_secondid'];
			$getdetail = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku555_user WHERE user_id='$h'"));
			
			$lend[] = array (
				'debt_id'	=>	$y['debt_id'],
				'debt_name'	=>	$y['debt_name'],
				'debt_desc'	=>	$y['debt_desc'],
				'debt_type'	=>	$y['debt_type'],
				'debt_category'	=>	$y['debt_category'],
				'debt_amount'	=>	$y['debt_amount'],
				'debt_paid'	=>	$y['debt_paid'],
				'debt_createdate'	=>	$y['debt_createdate'],
				'debt_duedate'	=>	$y['debt_duedate'],
				'user_id'	=>	$y['user_id'],
			 	'user_secondid'	=>	$y['debt_secondid'],
				'debtname'	=>	$getdetail['user_name'],
				'debtusername'	=>	$getdetail['user_username'] 
			);
		}		
	} else  {
		$checkqq = false;
	}
	
	if($checkq == true || $checkqq == true){
		$result = json_encode(array('success'	=> true, 'result'	=>	$lend));
	} else if ($checkq == false && $checkqq == false) {
		$result = json_encode(array('success'	=>	false));
	}
	
	echo $result;
	
}
?>