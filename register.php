<?php
	session_start();
	//jesli jestesmy zalogowani to -> gra
	if(isset($_SESSION['isloggedin']) && $_SESSION['isloggedin'] == true)
	{
		header('Location: game.php');
		exit();
	}
	if(isset($_POST['email']))
	{
		$validation = true;
		// sprawdzienie poprawnosci loginu
		$login = $_POST['login'];
		if(strlen($login)<3 || strlen($login)>20)
		{
			$validation = false;
			$_SESSION['error_login']="Nick must be between 3 and 20 characters long!";
		}
		if(ctype_alnum($login) == false)
		{
			$validation=false;
			$_SESSION['error_login'] = "The nickname can only consist of letters and numbers (no Polish characters)";
		}
		//sprawdzenie poprawnosci emailu
		$email = $_POST['email'];
		$safe_email = filter_var($email, FILTER_SANITIZE_EMAIL);
		if(filter_var($safe_email, FILTER_VALIDATE_EMAIL) == false || $safe_email != $email)
		{
			$validation = false;
			$_SESSION['error_email'] = "Incorrect e-mail";
		}
		//sprawdzenie poprawnosci haseł
		$password = $_POST['password'];
		$password2 = $_POST['password2'];
		if(strlen($password)<8 || strlen($password) > 20)
		{
			$validation = false;
			$_SESSION['error_pass_len'] = "Password should contain from 8 to 20 characters!";
		}
		if($password!=$password2)
		{
			$validation = false;
			$_SESSION['error_pass_notequal'] = "Passwords not equal!";
		}
		//hashowanie hasla
		$password_hash = password_hash($password, PASSWORD_DEFAULT);

		//sprawdzenie checkboxa
		if(!isset($_POST['regulamin']))
		{
			$validation = false;
			$_SESSION['error_checkbox'] = "Accept the rules!";
		}

		//sprawdzenie captchy
		$secret_key = "6LeqYQwgAAAAAI43XFFNrCwO1Fm7FdY1UNBo70kx";
		$sprawdz=file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']);
		$odpowiedz = json_decode($sprawdz);
		if($odpowiedz->success == false)
		{
			$validation = false;
			$_SESSION['error_captcha'] = "Are you a bot or not?";
		}
		//zabezpieczenie przed powtórzeniem emailu
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
				$result = $connection->query("Select ID FROM users WHERE email ='$email'");
				if(!$result) throw new Exception($connection->error);
				$mail_num = $result->num_rows;
				if($mail_num > 0)
				{
					$validation = false;
					$_SESSION['error_email'] = "This e-mail is already in use";
				}
				$connection->close();
			}
		}
		catch(Exception $e)
		{
			echo'<span style="color:red;">Server Error, please try again in a moment</span>';
			echo '<br>informacja o bledzie: '.$e;
		}
		//sprawdzenie wybrania profesji
		if(!isset($_POST['profession']) || $_POST['profession'] == "...")
		{
			$validation = false;
			$_SESSION['error_profession'] = "Choose a profession of your character!";
		}
		else
		{
			$profession = $_POST['profession'];
		}

		//zabezpieczenie przed powtórzeniem loginu
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
				$result = $connection->query("Select ID FROM users WHERE login ='$login'");
				if(!$result) throw new Exception($connection->error);
				$login_num = $result->num_rows;
				if($login_num > 0)
				{
					$validation = false;
					$_SESSION['error_login'] = "Ten login jest już zajęty";
				}
				//jeśli wszystko przeszło kontrolę
				if($validation==true)
				{
					if($connection->query("INSERT INTO users VALUES(NULL,'$login','$password_hash','$email',1,'$profession',0)"))
					{
						$_SESSION['registered'] = true;
						header('Location: welcome.php');
					}
					else
					{
						throw new Exception($connection->error);
					}
				}
				$connection->close();
			}
		}
		catch(Exception $e)
		{
			echo'<span style="color:red;">Błąd serwera! Przepraszamy i prosimy spróbować za chwilę</span>';
			echo '<br>informacja o bledzie: '.$e;
		}
	}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
	<meta charset="utf-8">
	<meta content="IE = edge,chrome = 1">
	<title>TheGame - create account</title>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<style>
		.error
		{
			color:red;
			margin-top:10px;
			margin-bottom:10px;
		}
	</style>
</head>

<body>
	<b>Registration form for THEGAME</b><br><br>
	<form method="post">
		Login:* <br><input type="text" name="login">
		<?php
			if(isset($_SESSION['error_login']))
			{
				echo '<div class="error">'.$_SESSION['error_login'].'</div>';
				unset($_SESSION['error_login']);
			}
		?><br>
		Password:** <br><input type="password" name="password">
		<?php
			if(isset($_SESSION['error_pass_len']))
			{
				echo '<div class="error">'.$_SESSION['error_pass_len'].'</div>';
				unset($_SESSION['error_pass_len']);
			}
		?><br>
		Repeat password:** <br><input type="password" name="password2">
		<?php
			if(isset($_SESSION['error_pass_notequal']))
			{
				echo '<div class="error">'.$_SESSION['error_pass_notequal'].'</div>';
				unset($_SESSION['error_pass_notequal']);
			}
		?><br>
		E-mail: <br><input type="text" name="email">
		<?php
			if(isset($_SESSION['error_email']))
			{
				echo '<div class="error">'.$_SESSION['error_email'].'</div>';
				unset($_SESSION['error_email']);
			}
		?><br>
		<label for "profession">Choose profession:<br>
		<select id = "profession" name ="profession">
			<option selected>...</option>
			<option value="Warrior">Warrior</option>
			<option value="Paladin">Paladin</option>
			<option value="Mage">Mage</option>
			<option value="Blade dancer">Blade Dancer</option>
			<option value="Tracker">Tracker</option>
			<option value="Hunter">Hunter</option>
		</select>
		<?php
			if(isset($_SESSION['error_profession']))
			{
				echo '<div class="error">'.$_SESSION['error_profession'].'</div>';
				unset($_SESSION['error_profession']);
			}
		?></label>
		<label><br><br>
			<input type="checkbox" name="regulamin"> I accept the terms and conditions.
		</label><br><br>
		<?php
			if(isset($_SESSION['error_checkbox']))
			{
				echo '<div class="error">'.$_SESSION['error_checkbox'].'</div>';
				unset($_SESSION['error_checkbox']);
			}
		?>
		<div class="g-recaptcha" data-sitekey="6LeqYQwgAAAAAHN39qEdNi1FcBrrlKoEScPWSput"></div>
		<?php
			if(isset($_SESSION['error_captcha']))
			{
				echo '<div class="error">'.$_SESSION['error_captcha'].'</div>';
				unset($_SESSION['error_captcha']);
			}
		?><br>
		<input type="submit" name="register" value="Register"><br>
	</form><br><br>
	<font size="-1">
		<b>Rules:</b><br>
		*Login should contain only upper and lower case letters and 3 to 20 characters.<br>
		**The password should be at least 8 to 20 characters long.<br><br>
	</font>
	<a href="index.php">[Back to the main page]</a>
</body>
</html>