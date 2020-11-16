<?php session_start();
if(!(isset($_SESSION['id'])) || $_SESSION['role'] != 'admin'){
	header("Location: ../login.php"); //a non-admin shouldn't ever reach an admin page
	die();
}

$message = "";
$messageRM = "";
$messageADD = "";
$messageMOD = "";
$MDPmessage = "";

if 	($_SERVER['REQUEST_METHOD'] === 'POST'){
    if ($_POST['changepass']) {
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
                $MDPmessage = "Mauvais mot de passe";
            }else{
                $mdc = md5($_POST['passChange']);
                if (md5($_POST['pass']) === $resultat['pass_md5']){
                    $statementE = $db->query("UPDATE Member SET pass_md5='{$mdc}' WHERE user LIKE '{$_SESSION['user']}';");
        
                    if (!$statementE){
                        $MDPmessage = 'Echec';
                    } else {
                        $MDPmessage = 'Reussite';
                    }
                } else {
                    $MDPmessage = "Mauvais mot de passe";
                }
            }
    
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    


    if ($_POST['modifuser']) {
        try{
            // Create PDO object
            $db = new PDO('sqlite:/usr/share/nginx/databases/database.sqlite');
            // Set errormode to exceptions
            $db->setAttribute(PDO::ATTR_ERRMODE, 
                                    PDO::ERRMODE_EXCEPTION);
            
            $statement = $db->query("SELECT * FROM Member WHERE user LIKE '{$_POST['usr_name']}';");
            $statement->execute();
    
            $resultat = $statement->fetch();

            if ($resultat != null){
                $md5 = "";
                $role = "";
                $valid = 1;
                
                if ($_POST['pass_val'] != ""){
                    $md5 = md5($_POST['pass_val']);
                } else {
                    $md5 = $resultat['pass_md5'];
                }

                if ($_POST['role_val'] != "no"){
                    $role = $_POST['role_val'];
                } else {
                    $role = $resultat['role'];
                }

                if (empty($_POST['validation_val'])){
                    $valid = 0;
                }
                
                $statementE = $db->query("UPDATE Member SET
                pass_md5 = '{$md5}', role='{$role}', valid={$valid}  
                WHERE user LIKE '{$_POST['usr_name']}';");
    
                if (!$statementE){
                    $messageMOD = 'Echec du changement';
                } else {
                    $messageMOD = 'Reussite du changement';
                }
            } else {
                $messageMOD = "Utilisateur non existant";
            }
    
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }


    
    if ($_POST['adduser']) {
        try{
            // Create PDO object
            $db = new PDO('sqlite:/usr/share/nginx/databases/database.sqlite');
            // Set errormode to exceptions
            $db->setAttribute(PDO::ATTR_ERRMODE, 
                                    PDO::ERRMODE_EXCEPTION);
            
            $statement = $db->query("SELECT * FROM Member WHERE user LIKE '{$_POST['usr_name']}';");
            $statement->execute();
    
            $resultat = $statement->fetch();
    
            if ($resultat == null){
                $md5 = md5($_POST['pass_val']);
                $valid = 1;
                if (empty($_POST['validation_val'])){
                    $valid = 0;
                }

                $statementE = $db->query("INSERT INTO
                Member(user, pass_md5, role, valid) 
                VALUES('{$_POST['usr_name']}', '{$md5}', '{$_POST['role_val']}', {$valid});");
    
                if (!$statementE){
                    $messageADD = 'Echec de l ajout';
                } else {
                    $messageADD = 'Reussite de l ajout';
                }
            } else {
                $messageADD = "Utilisateur deja existant";
            }
    
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    
    if ($_POST['rmuser']) {
        try{
            // Create PDO object
            $db = new PDO('sqlite:/usr/share/nginx/databases/database.sqlite');
            // Set errormode to exceptions
            $db->setAttribute(PDO::ATTR_ERRMODE, 
                                    PDO::ERRMODE_EXCEPTION);
            
            $statement = $db->query("SELECT * FROM Member WHERE user LIKE '{$_POST['usr_name']}';");
            $statement->execute();
    
            $resultat = $statement->fetch();
    
            if ($resultat != null){
                
                $statementE = $db->query("DELETE FROM Member WHERE user LIKE '{$_POST['usr_name']}';");
    
                if (!$statementE){
                    $messageRM = 'Echec de la suppression';
                } else {
                    $messageRM = 'Reussite de la suppression';
                }
            } else {
                $messageRM = "Utilisateur non existant";
            }
    
        }catch(PDOException $e){
            echo $e->getMessage();
        }
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
                <input type="button" class="browse" value="Profile" onClick="window.location = './AdministratorPage.php'">
                <input type="button" class="browse" value="Réception"onClick="window.location = './ReceptionPage.php'">
                <input type="button" class="browse" value="Ecrire message"onClick="window.location = './NewMessage.php'">
            </div>

            <div id="profile">
                <div id="profile-informations">
                    <p>Username: <?php echo $_SESSION["user"]; ?></p>
                </div>
            </div>

            <div id="management">
                <div id="management-informations">
                    <div id="add-user">
                        <h2>Ajout d'un utilisateur :</h2>
                        <div class="add-user">
                            <form method="POST">
                                <label for="usr_name">Nom d'utilisateur:</label><br>
                                <input type="text" id="usr_name" name="usr_name" placeholder="Rentrez un nom d'utilisateur" required></input><br>
                                <label for="pass_val">Mot de passe:</label><br>
                                <input type="password" id="pass_val" name="pass_val" placeholder="Rentrez un mot de passe" required></input><br>
                                <label for="role_val">Rôle:</label><br>
                                <select id="role" name="role_val" required>
                                    <option value="admin">administrateur</option>
                                    <option value="colab">colaborateur</option>
                                </select></br>
                                <label for="validation_val">Validation:</label>
                                <input type="checkbox" id="validation_val" name="validation_val" value="valid" checked>
                                <input type="submit" id="submit" name="adduser" value="Valider">  
                                <p id="passChangeL"> <?php echo $messageADD; ?></p>                        
                            </form>
                        </div>
                    </div>
                    <div id="change-user">
                        <h2>Modification d'un utilisateur :</h2>
                        <div class="change-user">
                            <form method="POST">
                                <label for="usr_name">Nom d'utilisateur:</label><br>
                                <input type="text" id="usr_name" name="usr_name" placeholder="Rentrez un nom d'utilisateur" required></input><br>
                                <label for="pass_val">Mot de passe:</label><br>
                                <input type="password" id="pass_val" name="pass_val" placeholder="Rentrez un mot de passe"></input><br>
                                <label for="role_val">Rôle:</label><br>
                                <select id="role" name="role_val">
                                    <option value="no">ne pas modifier</option>
                                    <option value="admin">administrateur</option>
                                    <option value="colab">colaborateur</option>
                                </select></br>
                                <label for="validation_val">Validation:</label>
                                <input type="checkbox" id="validation_val" name="validation_val" checked>
                                <input type="submit" id="submit" name="modifuser" value="Valider">  
                                <p id="passChangeL"> <?php echo $messageMOD; ?></p>                    
                            </form>
                        </div>
                    </div>
                    <div id="del-user">
                        <h2>Suppression d'un utilisateur :</h2>
                        <div class="del-user">
                            <form method="POST">
                                <label for="usr_name">Nom d'utilisateur:</label><br>
                                <input type="text" id="usr_name" name="usr_name" placeholder="Rentrez un nom d'utilisateur" required></input><br>
                                <input type="submit" id="submit" name="rmuser" value="Valider">  
                                <p id="passChangeL"> <?php echo $messageRM; ?></p>                    
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div id="change_pass">
                <div id="change_pass_info">
                    <form method="POST">
                        <label for='passChange'>Changer de mot de passe:</label></br></br>
                        <label id="passChangeL">Ancien mot de passe :</label><input type="password" id="passChange" name="pass" placeholder="Rentrez votre ancien mot de passe" required><br>
                        <label id="passChangeL">Nouveau mot de passe :</label><input type="password" id="passChange" name="passChange" placeholder="Rentrez votre nouveau mot de passe" required><br>
                        <input id="passChange" type="submit" name="changepass" value="Valider">
                        <p id="passChangeL"> <?php echo $MDPmessage; ?></p>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
