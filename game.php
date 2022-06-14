<?php
	session_start();

	if(!isset($_SESSION['isloggedin']))
	{
		header('Location: index.php');
		exit();
	}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
	<meta charset="utf-8">
	<meta content="IE = edge,chrome = 1">
	<title>TheGame - game</title>
</head>
<body>

<?php
	echo "<p>Welcome ".$_SESSION['login']."!";
	echo "<p><b>Gold: </b>".$_SESSION['gold'];
	echo "<b> | Level: </b>".$_SESSION['level'];
	echo "<b> | Profession: </b>".$_SESSION['profession']."</p><br><br>";
	echo "<p><b>E-mail: </b>".$_SESSION['email']."</p>";
	echo '<p>[ <a href="settings.php"> Settings ]</a></p>';
	echo '<p>[ <a href="logout.php"> Log out! ]</a></p>';
?>

<p><b>Actual data and time:</b> (requires refresh)<br>
<?php echo(strftime("%d.%m.%Y %H:%M")); ?></p>
</body>
</html>