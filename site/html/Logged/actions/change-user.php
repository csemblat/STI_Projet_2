<?php
session_start();
if(!(isset($_SESSION['id'])) || $_SESSION['role'] != 'admin'){
	header("Location: /login.php"); //a non-admin shouldn't ever reach an admin page
	die();
}
// Here we change the password of an user in the db
try{
    // Create PDO object
    $db = new PDO('sqlite:/usr/share/nginx/databases/database.sqlite');
    // Set errormode to exceptions
    $db->setAttribute(PDO::ATTR_ERRMODE, 
                            PDO::ERRMODE_EXCEPTION);
    
    $pass = md5($_POST['pass_val']);    
    $statement = $db->query("UPDATE Member SET pass_md5 = {$pass}, role = {$_POST[role_val]}, valid = {$_POST[validation_val]} WHERE user = {$_POST[usr_name]};");
    
    $statement->execute();

    $resultat = $statement->fetch();

}catch(PDOException $e){
    echo $e->getMessage();
}

// Now we check the password
if (!$resultat){
    echo 'Erreur de changement';
} else {
    echo 'Changement de l utilisateur';
}
?>
