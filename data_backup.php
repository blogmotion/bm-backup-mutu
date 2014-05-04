<?php
header('Content-Type: text/html; charset=utf-8');
/**
* Author: @xhark
* Website: http://blogmotion.fr
* License : Creative Commons http://creativecommons.org/licenses/by-nd/4.0/deed.fr
* Inspired : http://www.daniweb.com/web-development/php/threads/467398/backup-server-script
* Date: 30/04/2014
* Version 1.0
* PHP >= 5.2
*
* But du script : crée un backup d'un dossier au format ZIP, récursivement.
* Ce script crée un nouveau répertoire de destination "ZIP_ARCHIVES" (modifiable)
* Le nom de ce répertoire doit être unique car il sera exclu du processus de sauvegarde
* Renommez le script de façon exotique (data-backup123.php)
* Evitez un nom comme "sauvegarde.php" ou "backup.php"
* Ce script peut être stocké à la racine du site mais il est préférable de le placer dans un
* répertoire dédié à la racine de votre hébergement, protégé par htaccess
* ex: /MON_BACKUP/data-backup123.php

* Purpose: ZIP Everything into one zip file, recursively
* Create a new directory called "ZIP_ARCHIVES" (editable)
* It's important to use that name because you don't want the backup script to backup previous backups.
* It will ignore the directory where your backup zipped files are located.
* Please rename the script with unusual name like "data-backup123.php"
* Make this script name something that nobody can easily figure out. Don't call it "backup.php"
* You can put it into your main website directory, but it will be more secure
* if you put in a dedicated new directory to the root of your website and htaccess protected
* ex: /MY_BACKUP/data-backup123.php
*/

####################################
# VARIABLES
# nom du repertoire de destination
$dstDir = 'ZIP_archives';
# chemin du repertoire de destination
$dstPath = './'.$dstDir.'/';
# repertoire à zipper. 2 possibilités "./" répertoire courant, ou "../" pour le répertoire parent
$sourceDir = "../";
####################################

// NE RIEN MODIFIER APRES CETTE LIGNE
// debut du script
# check version php
if (strnatcmp(phpversion(),'5.2.0') < 0) exit("Vous devez utiliser <strong>PHP5 ou supérieur</strong>. Vous utilisez <strong>".phpversion()."</strong>");
$startTime = time();
$dstFile = "backup-data_" . date('Y-m-d_H\hi\_s') . ".zip";
$dst = $dstPath . $dstFile;

if(creeDirDst($dstDir) === false) { echo "Erreur: impossible de créer le répertoire de destination"; exit; }

if (Zip($sourceDir, $dst) === TRUE) { 
	echo "✔ Backup ZIP data OK (en ".temps($startTime)." secondes pour un poids de ".round(filesize($dst)/1024/1024)." mo)";
} 
else { echo "Erreur: impossible de produire le fichier zip"; }

####################################
# FONCTIONS
function Zip($source, $destination) {
	global $dstDir;
    if (!extension_loaded('zip') || !file_exists($source)) { return false; }
    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) { return false; }
    $source = str_replace('\\', '/', realpath($source));
    if (is_dir($source) === true) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            $file = str_replace('\\', '/', $file);
            // On ignore : les dossiers "." and "..", le dossier des archives et le cache smarty
            if (in_array(substr($file, strrpos($file, '/') + 1), array(
                '.',
                '..'))
				//OR preg_match('@(cache/smarty|'.preg_quote($dstDir).')@', $file)
				OR (!strpos($file,$dstDir) === false) 
				# une ligne par fichier ou dossier à exclure, le chemin doit être le plus precis possible
				OR (!strpos($file,'cache/smarty') === false) 
			)
				continue; # passe a l'iteration suivante
            $file = realpath($file);
            if (is_dir($file) === true) 	 { $zip->addEmptyDir(str_replace($source . '/', '', $file . '/')); }
			elseif (is_file($file) === true) { $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file)); }
        } // foreach
    } // if
	elseif (is_file($source) === true) { $zip->addFromString(basename($source), file_get_contents($source)); }
    return $zip->close();
} // function Zip

function temps($startTime) { $now = time();	return ($now-$startTime); }

# crée le répertoire de destination s'il n'existe pas
function creeDirDst($rep) {
	if (!is_dir($rep)) { if(mkdir($rep) !== true) return false;	}
	# generation htaccess du protection des archives (s'il n'existe pas)
	if(!is_file($rep.'/.htaccess')) @file_put_contents($rep.'/.htaccess', 'deny from all');
	return true;
}
?> 