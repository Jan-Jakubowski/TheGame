<?php
	session_start();
	if(!isset($_POST['login']) || !isset($_POST['haslo']))
	{
		header('Location: index.php');
		exit();
	}
	//obsluga bledu
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
			$login=$_POST['login'];
			$haslo=$_POST['haslo'];

			$login=htmlentities($login, ENT_QUOTES, "UTF-8"); // encje w loginie

			if($result = @$connection->query(
			sprintf("SELECT * FROM users WHERE login='%s'",
			mysqli_real_escape_string($connection, $login))))
			{
				$usersnumber = $result->num_rows;
				if($usersnumber > 0)
				{
					$wiersz = $result->fetch_assoc();

					if(password_verify($haslo, $wiersz['password']))
					{
						$_SESSION['isloggedin'] = true;
						$_SESSION['ID'] = $wiersz['ID'];
						$_SESSION['login'] = $wiersz['login'];
						$_SESSION['email'] = $wiersz['email'];
						$_SESSION['level'] = $wiersz['level'];
						$_SESSION['profession'] = $wiersz['profession'];
						$_SESSION['gold'] = $wiersz['gold'];

						unset($_SESSION['blad']);

						$result->free_result();
						header('Location: game.php');
					}
					else
					{
						$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło</span>';
						header('Location: index.php');
					}
				}
				else
				{
					$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło</span>';
					header('Location: index.php');
				}
			}
			else
			{
				throw new Exception($connection->error);
			}
			$connection->close();
		}
	}
	catch (Exception $e)
	{
		echo'<span style="color:red;">Błąd serwera! Przepraszamy i prosimy spróbować za chwilę</span>';
		echo '<br>informacja o bledzie: '.$e;
	}
?>