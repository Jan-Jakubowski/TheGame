<?php
	session_start();
	if(!isset($_SESSION['registered']))
	{
		header('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['registered']);
	}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
	<meta charset="utf-8">
	<meta content="IE = edge,chrome = 1">
	<title>TheGame - welcome</title>
</head>

<body>
	<h1>Thanks for creating account, since now you can log in!</h1><br><br>
	<a href="index.php">[Log in!]</a>

</body>
</html>