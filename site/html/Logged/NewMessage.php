<?php
session_start();
if(isset($_SESSION['id']) === false){
echo "not logged in yet";
header("Location: ../login.php");
die();
}

function cleaner($string){
    $ennemis = array("'",'"',"<",">"); //ajout de < et > pour contrer les XSS
    return str_replace($ennemis, "", $string);
}

// Send a message
$message="";
if 	($_SERVER['REQUEST_METHOD'] === 'POST'){
    try{
        // Create PDO object
        $db = new PDO('sqlite:/usr/share/nginx/databases/database.sqlite');
        // Set errormode to exceptions
        $db->setAttribute(PDO::ATTR_ERRMODE, 
                                PDO::ERRMODE_EXCEPTION);
        
		$statement = $db->query("SELECT * FROM Member WHERE user LIKE '{$_POST['dest']}';");
		$statement->execute();

		$resultat = $statement->fetch();

        if ($resultat != null){
            date_default_timezone_set("Europe/Paris");
            $date = date('H:i d/m/Y');
            $Ssubject = cleaner($_POST['subject']);
            $Scontent = cleaner($_POST['msg']);
            $Sdest = cleaner($_POST['dest']); //ajouté pour empêcher de potentielles injections

            $message = $Scontent;

            $statementE = $db->query("INSERT INTO
            Messages(exp, dest, subject, content, date) 
            VALUES('{$_SESSION['user']}', '{$Sdest}', '{$Ssubject}', '{$Scontent}', '{$date}');");

            if (!$statementE){
                $message = 'Echec de l envoie';
            } else {
                $message = 'Reussite de l envoie';
            }
        } else {
            $message = "Destinataire introuvable";
        }

    }catch(PDOException $e){
        echo $e->getMessage();
    }
}

$dest = "";
if ($_GET["dest"]){
    $dest = $_GET["dest"];
}


?>

<html>
    <head>
		<title>Nouveau message</title>
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

            <div id="writing">
                <form method="POST">
                    <label for="dest">Destinataire</label>
                    <input type="text" id="dest-value" name="dest" required placeholder="Saisir le nom d'utilisateur du destinataire" value="<?php echo $dest?>"><br>
                    <label for="subject">Sujet</label>
                    <input type="text" id="subject-value" name="subject" required placeholder="Saisir le sujet" ><br>
                    <label id="msg_value" for="msg">Message</label><br>
                    <textarea rows="34" cols="81" name="msg"></textarea><br>
                    <input class="submit_msg" type="submit" value="Envoyer">

                    <p><?php echo $message; echo md5(mama);?></p>
                </form>
            </div>
        </div>
    </body>
</html>
