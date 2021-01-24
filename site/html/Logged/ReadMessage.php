<?php session_start();
if(isset($_SESSION['id']) === false){
echo "not logged in yet";
header("Location: ../login.php");
die();
}
function cleaner($string){
    $ennemis = array("'",'"',"<",">"); //ajout de < et > pour contrer les XSS
    return str_replace($ennemis, "", $string);
}

$db = new PDO('sqlite:/usr/share/nginx/databases/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$exp="";
$subject="";
$content="";
$id = cleaner($_GET['i']);

$statement = $db->query("SELECT exp, subject, content FROM Messages WHERE id = '{$id}';");
$statement->execute();
$resultat = $statement->fetch();
if($resultat){
    $exp = $resultat['exp'];
    $subject = $resultat['subject'];
    $content = $resultat['content'];
}
else{
    header("Location: /Logged/ReceptionPage.php");
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
                <label for="dest-value">Destinataire: <?php echo "{$exp}";?></label><br><br>
                <label for="subject-value">Sujet: <?php echo "{$subject}";?></label><br><br>
                <label id="msg_value" for="msg-value">Message<br></label><br>
                <textarea rows="34" cols="81" name="msg-value" readonly="readonly"><?php echo "{$content}";?></textarea>
            
                <form action='ReceptionPage.php' method='post'> 
                        <a href=<?php echo "NewMessage.php?dest={$exp}";?>><button type="button">Repondre</button></a>
                        <button type='submit' name='suppr' value=<?php echo "{$id}";?> class='btn-suppr'>Supprimer</button>
                </form>
            </div>

        </div>
    </body>
</html>;
