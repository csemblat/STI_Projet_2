# STI_Projet_2
## Projet de sécurité web pour le cours de STI de l'HEIG-VD


## Vulnérabilités et corrections :

- Injection XSS :

Le site était sensible aux exploits de type XSS qui permettaient d'injecter du code dans le sujet des messages. La vulnérabilité XSS de type onError a été utilisé avec succès pour afficher le cookie d'un utilisateur dans un scénario d'attaque.
Sujet de message utilisé pour faire l'exploit XSS : `test_message <img> src="./imagenotexist.png" onerror=alert(document.cookie);>test_img </img>`
Cette vulnérabilité a été corrigée en sanitisant les inputs d'utilisateur en enlevant les `<` et `>` des inputs d'utilisateur dans le code du fichier "NewMessage.php".
Ce type de vulnérabilité a aussi été enlevé de AdministratorPage dans la partie création d'utilisateur pour éviter des attaques XSS venant d'un administrateur malveillant.

- Bruteforce :

La page de login ne vérifie pas si l'utilisateur envoie trop de tentatives de login d'affilée.
Correction : blocages par IP au bout de 30 tentatives ratées (implémentation de la persistance via création de fichiers dont le nom est la concaténation de l’IP et du numéro de la tentative)

- Injection SQL du second degré:

L'application n'est pas protégée contre les injections du second ordre dans la gestion des contenus postés par les utilisateurs (e.g. messages).
Correction : Utilisation d'une fonction de nettoyage de caractères spéciaux dans la création de messages et d'utilisateurs pour empêcher l'apparition d'attaques du second ordre

- accès non autorisé aux messages

Un utilisateur peut changer l'id des messages et peut donc accéder a tous les messages stockés dans la database

- Infrastructure :

Selon un scan Nessus l'application possède plusieurs vulnérabilités (e.g. DoS, input validation error, XSS) dû aux versions de PHP et JQuery qui ne sont pas à jour.
Correction : Il faudrait mettre à jour l'interpréteur PHP et JQuery.
