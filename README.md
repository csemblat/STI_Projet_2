# STI_Projet_2
Projet de sécurité web pour le cours de STI de l'HEIG-VD


# Vulnérabilités et corrections :

Le site était sensible aux exploit de type XSS qui permettaient d'injecter du code dans le sujet des message. la vulnérabilité XSS de type onError a été utilisé avec succès pour afficher le cookie d'un utilisateur dans un scénario d'attaque.
Sujet de message utilisé pour faire l'exploit XSS : " test_message <img src="./imagenotexist.png" onerror=alert(document.cookie);>test_img</img> "
Cette vulnérabilité a été corrigée en sanitisant les input d'utilisateur en enlevant les '<' et '>' des inputs d'utilisateur dans le code du fichier "NewMessage.php".
