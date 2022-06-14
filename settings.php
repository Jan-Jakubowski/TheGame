<?php
	session_start();
	if(!isset($_SESSION['isloggedin']))
	{
		header('Location: index.php');
		exit();
	}
	$login = $_SESSION['login'];
	if(isset($_POST['profession']))
	{
		$profession = $_POST['profession'];
		require_once 'connect.php';
		mysqli_report(MYSQLI_REPORT_STRICT);
		try
		{
			$connection = new mysqli($hostname, $username, $password, $database);
			if($connection->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				$sql = "UPDATE users SET profession='$profession' WHERE login = '$login'";
				if ($connection->query($sql) === TRUE) 
				{
					$_SESSION['prof_update'] = "Record updated successfully";
  					$_SESSION['profession'] = $profession;
  					unset($profession);
				} 
				else 
				{
					throw new Exception($connection->error);
				}
			}
			$connection->close();
		}
		catch(Exception $e)
		{
			echo'<span style="color:red;">Server Error, please try again in a moment</span>';
			echo '<br>informacja o bledzie: '.$e;
		}
	}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
	<meta charset="utf-8">
	<meta content="IE = edge,chrome = 1">
	<title>TheGame - settings</title>
</head>
<body>
On this page you are able to change your character profession.<br><br>
<?php
	echo "<p>Logged in as: ".$_SESSION['login']."!<br><br>";
	if(isset($_SESSION['prof_update']))	
	{
		echo $_SESSION['prof_update']."<br>";
		unset($_SESSION['prof_update']);
	}
	echo "<b> | Actual profession: </b>".$_SESSION['profession']." |</p>";
?>

<form method="post">
	<label for "profession">Choose profession:<br>
	<select id = "profession" name ="profession">
		<option disabled selected value>-- select an option --</option>
		<option value="Warrior">Warrior</option>
		<option value="Paladin">Paladin</option>
		<option value="Mage">Mage</option>
		<option value="Blade Dancer">Blade Dancer</option>
		<option value="Tracker">Tracker</option>
		<option value="Hunter">Hunter</option>
	</select><br><br>
	<input type="submit" name = "Change_profession" value = "Change profession"><br><br>
</form>



<?php
	echo '[ <a href="game.php"> Back to game! ]</a></p>';
	echo '[ <a href="logout.php"> Log out! ]</a></p>';
?>

</body>
</html>