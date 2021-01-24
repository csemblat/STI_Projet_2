<?php session_start();
if(!(isset($_SESSION['id']))){
	header("Location: ../login.php"); //a non-admin shouldn't ever reach an admin page
	die();
}
if($_SESSION['role'] === 'admin'){
       header("Location: ./AdministratorPage.php"); //a non-admin shouldn't ever reach an admin page
       die();
}

$message = "";
if 	($_SERVER['REQUEST_METHOD'] === 'POST'){
    try{
        // Create PDO object
        $db = new PDO('sqlite:/usr/share/nginx/databases/database.sqlite');
        // Set errormode to exceptions
        $db->setAttribute(PDO::ATTR_ERRMODE, 
                                PDO::ERRMODE_EXCEPTION);
        
        $statement = $db->query("SELECT pass_md5 FROM Member WHERE user LIKE '{$_SESSION['user']}';");
        $statement->execute();

        $resultat = $statement->fetch();

        if(!$resultat){
            $message = "Mauvais mot de passe";
        }else{
            $mdc = md5($_POST['passChange']); //pas besoin de nettoyer car jamais reflété vers l'utilisateur ou envoyé intact au datastore
            if (md5($_POST['pass']) === $resultat['pass_md5']){ 
                $statementE = $db->query("UPDATE Member SET pass_md5='{$mdc}' WHERE user LIKE '{$_SESSION['user']}';");
    
                if (!$statementE){
                    $message = 'Echec';
                } else {
                    $message = 'Reussite';
                }
            } else {
                $message = "Mauvais mot de passe";
            }
        }

    }catch(PDOException $e){
        echo $e->getMessage();
    }
}
?>
<html>
    <head>
		<title>Profile</title>
		<meta charset="utf-8">
		<meta name="author" lang="fr" content="CANIPEL Vincent">
		<meta name="author" lang="fr" content="SEMBLAT Clement"> 
		<meta name="Description" content="Site de discution avec de grands manques de sécurité"> 
	
		<link rel="stylesheet" href="style.css" media="screen" type="text/css" />
	</head>
	
    <body>
        <div id="container">
            <div id="browsing">
                <input type="button" class="browse" value="Déconnection" onClick="window.location = '../logout.php'">
                <input type="button" class="browse" value="Profile" onClick="window.location = './ColaboratorPage.php'">
                <input type="button" class="browse" value="Réception"onClick="window.location = './ReceptionPage.php'">
                <input type="button" class="browse" value="Ecrire message"onClick="window.location = './NewMessage.php'">
            </div>

            <div id="profile">
                <div id="profile-informations">
                    <p>Username: <?php echo $_SESSION["user"]; ?></p>
                </div>
            </div>

            <div id="change_pass">
                <div id="change_pass_info">
                    <form method="POST">
                        <label for='passChange'>Changer de mot de passe:</label></br></br>
                        <label id="passChangeL">Ancien mot de passe :</label><input type="password" id="passChange" name="pass" placeholder="Rentrez votre ancien mot de passe" required><br>
                        <label id="passChangeL">Nouveau mot de passe :</label><input type="password" id="passChange" name="passChange" placeholder="Rentrez votre nouveau mot de passe" required><br>
                        <input id="passChange" type="submit" value="Valider">
                        <p id="passChangeL"> <?php echo $message; ?></p>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
