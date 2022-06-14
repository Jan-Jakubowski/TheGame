<?php
	session_start();
	if(isset($_SESSION['isloggedin']) && $_SESSION['isloggedin'] == true) //sprawdzenie statusu zalogowania
	{
		header('Location: game.php');
		exit();
	}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
	<meta charset="utf-8">
	<meta content="IE = edge,chrome = 1">
	<title>TheGame - strona głowna</title>
</head>

<body>
	<b>*Dynamic*</b> computer game, with 6 professions available.<br><br>
	<form action="log_in.php" method="post">
		Login:<br>
		<input type="text" name="login"><br>
		Password:<br>
		<input type="password" name="haslo"><br><br>
		<input type="submit" name="przycisk" value="Log me in!"><br>
	</form><br>
<?php
	if(isset($_SESSION['blad']))
		{
			echo $_SESSION['blad']."<br><br>";
		}
?>
<a href="register.php">[Register right now!]</a>

</body>
</html>