<?php
session_start();
// Here we grab the user and his password and we verify
// if they are the same

$message = "";
/*
fonctions anti-bruteforce (Projet 2)
Création de fichiers dans le dossier site/databases pour suivre le nombre de tentatives ratées 
*/
//added for registering failed login attempts
function attempt_register($string){
	$increment = 1;
	for($increment = 1; $increment <= 30; $increment++)
	{
	  if(file_exists("/usr/share/nginx/databases/{$string}_{$increment}.txt") === false){
		$myfile = fopen("/usr/share/nginx/databases/{$string}_{$increment}.txt", "w") or die("Unable to open file!");
		fwrite($myfile, "1", 4);
		fclose($myfile);
		break;
	  }
	}
}
//verifies how many times ip has attempted to log in
function login_attempts_num($string){
	$value_keep = 0;
	$increment = 1;
	for($increment = 1; $increment <= 31; $increment++)
	{
	  if(file_exists("/usr/share/nginx/databases/{$string}_{$increment}.txt") === false){
		$value_keep = $increment;
		break;
	  }
	}
	return $value_keep - 1;
}
//verifies if ip should be blocked
function verify_block($string){ //verifies if ip has logged in too many times
	$result = false;
	if(login_attempts_num($string) >= 30){
	$result = true;
	}
	return $result;
}
//cleans up ip to avoid troubles when creating files
function ip_cleaner($string){
    $ennemis = array(".","'",'"',"<",">"); //ajout de . pour empêcher des problèmes avec l'interpréteur. les autres caractères sont là au cas où.
    return str_replace($ennemis, "_", $string);
}
//ip du potentiel attaquant :
$Querier_ip = strval(ip_cleaner($_SERVER['REMOTE_ADDR'])); 
/*
fin des fonctions anti-bruteforce
*/


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
	if (!$resultat  || verify_block($Querier_ip)){
		if(verify_block($Querier_ip) === false && $_POST['usr'] != ""){ //tentative d'attaque seulement si un nom d'utilisateur a été mis
		attempt_register($Querier_ip);
		$message =  'Erreur de connection - Vérifiez vos informations, limite d essais : 30 avant blocage par IP';
		}
		else{
		$message = "IP BLOCKED";
		}
		

		
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
