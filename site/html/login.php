<?php
session_start();
// Here we grab the user and his password and we verify
// if they are the same

$message = "";

if 	($_SERVER['REQUEST_METHOD'] === 'POST'){
	try{
		//checking for special characters in username
		$validite = true;
		for($i=0; $i<strlen($_POST['usr']); $i++){
		if($_POST['usr'][$i] === "'" || $_POST['usr'][$i] === "-" || $_POST['usr'][$i] === '"'){
		$validite = false;
		}
		}
		if($validite === false){ //checking if special characters were found in username
			$message =  'Erreur de connection - Vérifiez vos informations';
		header("Location: ./login.php");
		die();
		}

		// Create PDO object
		$db = new PDO('sqlite:/usr/share/nginx/databases/database.sqlite');
		// Set errormode to exceptions
		$db->setAttribute(PDO::ATTR_ERRMODE, 
								PDO::ERRMODE_EXCEPTION);
		
		$statement = $db->query("SELECT * FROM Member WHERE user LIKE '{$_POST['usr']}';");
		$statement->execute();

		$resultat = $statement->fetch();
		
	}catch(PDOException $e){
		echo $e->getMessage();
	}

	// Now we check the password
	if (!$resultat){
		$message =  'Erreur de connection - Vérifiez vos informations';
	} else {
		if (md5($_POST['pass']) == $resultat['pass_md5'] && $resultat['valid'] == 1){
			$_SESSION["role"] = $resultat["role"];
			$_SESSION["id"] = $resultat["id"];
			$_SESSION["user"] = $resultat["user"];
			echo 'Connection !';
			if($_SESSION["role"] == "colab"){
			header("Location: ./Logged/ColaboratorPage.php");
			die();
			}
			if($_SESSION["role"] == "admin"){
			header("Location: ./Logged/AdministratorPage.php"); //après mise à jour de ColaboratorPage ce bloc est devenu redondant
			die();
			}
			
		} else {
			$message =  'Erreur de connection - Vérifiez vos informations';
		}
	}
}
?>

<html>
	<head>
		<title>Connection au salon de reception</title>
		<meta charset="utf-8">
		<meta name="author" lang="fr" content="CANIPEL Vincent">
		<meta name="author" lang="fr" content="SEMBLAT Clement"> 
		<meta name="Description" content="Site de discution avec de grands manques de sécurité"> 

		<link rel="stylesheet" href="LoginStyle.css" media="screen" type="text/css" />
	</head>

	<body>
		<div id="container">
			<form action ="login.php" method="POST">
				<h1>Entrez vos identifiants</h1>
				
				<label>Pseudonyme</label>
				<input type="text" placeholder="Veuillez saisir votre pseudonyme" name="usr" required><br>
				<label>Mot de passe</label>
				<input type="password" placeholder="Veuillez saisir votre mot de passe" name="pass" required><br>
				<input type="submit" id="submit" value="Connection">
				<p><?php echo $message; ?></p>	
			</form>	
		</div>
	</body>
</html>
