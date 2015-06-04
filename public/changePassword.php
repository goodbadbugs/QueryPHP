<?php
	ini_set('display_startup_errors',1);
	ini_set('display_errors',1);
	error_reporting(-1);

	session_start();
	include('assets/php/lib.php');
	global $user;
	global $password;
	global $db;
	
	$user = getUser();
	$password = getPassword();
	$db = getDB($user, $password);

	if (isAdmin($user, $db)) {
		$_SESSION['privilege'] = true;
	} else {
		$_SESSION['privilege'] = false;
	}

?>

<html>
<head>
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src='assets/js/effect.js' type='text/javascript'></script>
	<link rel='stylesheet' href='assets/css/styles.css' type='text/css' />
	<script type='text/javascript'>
	$('document').ready(function() {
	
		$('td').css('padding', '6px 10px');
		$('body').css('background-color', '#D0D0D0');	
	});
	</script>
</head>
<body>


<?php
	if ($_SESSION['privilege'] == false) {
		echo 'Access Denied for user ' . $user;
		exit();
	}	
?>

<?php

	// Governs when the user submits a ticket and refreshes the page; will
	// increment the ticket number count by one
	$password1 = $_POST['password1'];
	$password2 = $_POST['password2'];

	if ($password1 !== $password2) {
		echo 'The passwords did not match';
	}

	////	if (isset($_POST['submit']) && $password1 == $password2) {
	if (isset($_POST['submit'])) {
		$username = $_POST['username'];
		$newPassword = $password1;
	//	$password1 = null;
	//	$password2 = null;

		$query = "SET PASSWORD FOR '" . $username . "'@'localhost' = PASSWORD('" . $newPassword . "')";
		if (!$db->exec($query)) {
			print_r($db->errorInfo()); 
		} else {
			echo "Password changed for " . $username;
		}
	} else 
	if (isset($_POST['logout'])) {
		logout();
	} else
	if (isset($_POST['addUser'])) {
		header('Location: addUser.php');
	} else
	if (isset($_POST['home'])) {
		header('Location: index.php');
	} else
	if (isset($_POST['ticket'])) {
		header('Location: ticket.php');
	}

?>


<h1>Form</h1>

<form action="<?php echo $_SERVER['PHP_SELF'] ?>" name='addUserForm' id='addUserForm' method='post'>
	<nav>
		<input type='submit' class='button' name='home' id='home' value='Home' />
		<input type='submit' class='button' name='ticket' value='Create Work Order' />
		<input type='submit' class='button' name='addUser' value='Add Users' />
		<input type='submit' class='button' name='logout' value='Log Out' />
	</nav>

	<table border=1>
		<tr>
			<td>
			<input type='text' class='logon' name='username' placeholder='Username' />
			</td>
		</tr>
		<tr>
			<td>
			<input type='password' class='logon' name='password1' placeholder='Password' />
			</td>

		</tr>
		<tr>
			<td>
			<input type='password' class=logon' name='password2' placeholder='Confirm Password' />
			</td>
		</tr>
		<tr>
			<td>
			<input type='submit' class='button' name='submit' value='Change Password' />
			</td>
		</tr>
	</table>
</form>
</body>
</html>

