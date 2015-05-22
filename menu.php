<?php
	session_start();
	include('lib.php');
	global $user;
	global $password;
	global $numberOfRecords;
	global $db;	
?>

<html>
<head>
	<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js'></script>
	<link rel='stylesheet' href='assets/styles.css' type='text/css' />
	<script type='text/javascript'>
	$('document').ready(function() {
	
		$('td').css('padding', '6px 10px');
		$('body').css('background-color', '#D0D0D0');	
	});
	</script>
</head>
<body>

<?php

	// If Create Work Order button is clicked, navigate to work order creation form
	if (isset($_SESSION['createWorkorder'])) {
		header('Location: database.php');
	}

	// Check user session; if the session is new, use post data from logon form; otherwise renew user credentials
	// with session variables
	if (!isset($_SESSION['user'])) {
		$_SESSION['user'] = $_POST['user'];
		$_SESSION['password'] = $_POST['password'];
		$user = $_SESSION['user'];
		$password = $_SESSION['password'];

	}
	$user = $_SESSION['user'];
	$password = $_SESSION['password'];
	$hostname = "localhost";

	try {
		$db = getDB($user, $password);
	} catch (PDOException $e) {
		echo 'ERROR:' . $e->getMessage();
		session_unset();
		exit();
	}

?>


<h1>Form</h1>

<div class='formContainer'>
	<form action="$_SERVER['PHP_SELF']" name='menu' id='menu' method='post'>
	<div class='btnHeader'>
		<input type='submit' name='saveNew' value='Save and New' />
		<input type='button' name='clear' value='Clear' onclick="document.getElementById('ticket').reset()" />
		<input type='submit' name='logout' value='Log Out' />
	</div>

	<table border=1>
		<tr>
			<th><h2>Ticket Number</h2></th>
			<th><h2>Date Created</h2></th>
			<th><h2>Problem Code</h2></th>
		</tr>
		<tr>
			<td>
			<?php
		
			try {
				$db = getDB($user, $password);
			} catch (PDOException $e) {
				echo 'ERROR: ' . $e->getMessage();
				session_unset();
			}
			$stmt = $db->query("SELECT MAX(ticketNumber) from tickets");
			$newID = $stmt->fetch(PDO::FETCH_NUM);
			$newID = $newID[0]+1;
			if ($newID < 1000) {
				$newID = 1000;
			}
			echo "<input type='text' readonly='true' name='ticketNumber' value=$newID />"
			?>
			</td>
			<td>
			<?php
				$date = date("Y-m-d");
				$date = date("Y-m-d", strtotime(str_replace('-', '/', $date)));
				echo "<input type='text' name='dateCreated' value=$date readonly='true' />"
			?>
			<td>
			<select name='problemCode'>
			<?php
				try {

				$db = getDB($user, $password);
				$table = "tickets";
				$col = "problemCode";
				$sql = 'SHOW COLUMNS FROM '.$table.' WHERE field="'.$col.'"';
				    $row = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
				    foreach(explode("','",substr($row['Type'],6,-2)) as $option) {
				    echo("<option>$option</option>");
				}
				} catch (PDOException $e) {
					echo "ERROR: " . $e->getMessage();
				}	
			?>
			</select>
			</td>
		</tr>
		<tr>
			<th colspan='3'><h2>Problem Details</h2></th>
		</tr>
		<tr>
			<td colspan='3'><textarea rows='5' style='width: 100%;'  name='problemDescription'></textarea></td>
		</tr>
	</table>
	<br>
	<table border=1>
		<tr>
			<th><h2>Assigned To</h2></th>
			<th><h2>Status</h2></th>
			<th><h2>Date Closed</h2></th>
		</tr>
		<tr>
			<td>
			<select name='assignedTo'>
			<?php
				$db = getDB($user, $password);
				$table = "tickets";
				$col = 'assignedTo';
				$sql = 'SHOW COLUMNS FROM '.$table.' WHERE field="'.$col.'"';
				$row = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
				foreach(explode("','", substr($row['Type'],6,-2)) as $option) {
					echo ("<option>$option</option>");
				}
			?>
			</select>
			</td>
			<td>
			<select name='status' onchange='var today = new Date(); var dd = today.getDate(); var mm = today.getMonth()+1; mm = (mm < 10 ? "0" : "") + mm; var yyyy = today.getFullYear(); document.getElementById("dateClosed").value = yyyy + "-" + mm + "-" + dd'>
			<?php
				$db = getDB($user, $password);
				$table = "tickets";
				$col = 'status';
				$sql = 'SHOW COLUMNS FROM '.$table.' WHERE field="'.$col.'"';
				$row = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
				foreach(explode("','", substr($row['Type'],6,-2)) as $option) {
					echo ("<option>$option</option>");
				}
			?>
			</select>
			</td>
			<td>
				<input type='text' name='dateClosed' id='dateClosed' />
			</td>
		</tr>
	</table>
	</form>
</div>
</body>
</html>

