bm-backup (blogmotion backup)
===

###Description###
Script de sauvegarde pour hébergement mutualisé (OVH par exemple). Les fichiers sont indépendants :

- data_backup.php (permet la sauvegarde des données)
- mysql_backup.php (permet la sauvegarde d'une base mysql)

##Configuration minimale###
La sauvegarde des données (data_backup.php) **fonctionne uniquement avec PHP >=5.2**
Dans le cas contraire vous aurez un message d'erreur *"unexpected..."*

###Utilisation###
Il est recommandé de créer un répertoire portant le nom de votre choix à la racine de votre hébergement et d'y placer les fichiers PHP. Pensez à protéger l'accès à ce dossier avec un fichier *.htaccess* ou à minima lui donner un nom exotique.

###English version ###

###[EN] Description ###
Backup script for shared hosting (like OVH). Files are independent :

- data_backup.php (do a data backup)
- mysql_backup.php (do a mysql database backup)

###[EN] Requirements ###
The data backup script (data_backup.php) **only works with PHP >=5.2**
Otherwise you will receive an error message *"unexpected..."*.

###[EN] Usage ###
It is recommended to put PHP files in a dedicated new directory to the root of your website. Please protect it with a *.htaccess* file or rename something that nobody can easily figure out.
