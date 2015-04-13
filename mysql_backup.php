<?php 
header('Content-Type: text/html; charset=utf-8');
/**
* Author: @xhark
* Website: http://blogmotion.fr
* License : Creative Commons http://creativecommons.org/licenses/by-nd/4.0/deed.fr
* Inspired : http://www.ylegrand.com/blog/sauvegarder-ses-donnees-sur-un-hebergement-ovh-mutualise_16
* Date: 30/04/2014
* Update: 02/01/2015
* Version 1.1
* Need PHP functions: system()
* Need system functions: mysqldump, gzip
*
* But du script: fait un backup de la base de données MySQL, compressé en GZ
* Purpose: Create a backup of mysql tables, GZ compressed
*/

####################################
# VARIABLES
# Information de la base de donnees
$pdo_host = "";
$pdo_user = ""; 
$bdd_name = "";
$pdo_pwd = "";
# Repertoire de destination
$dstDir = './ZIP_archives/';
####################################

// NE RIEN MODIFIER APRES CETTE LIGNE
// debut du script
@ignore_user_abort(1);	// exécution du script en arrière plan, même si utilisateur arrête le chargement navigateur
@set_time_limit(0);		// exécution du script sans limite de temps

if(systemDisabled() === true) { 
	echo 'Erreur: votre hébergeur a désactivé la fonction PHP system(), rendant ce script est incompatible.';
	exit;
}
$startTime = time();
$dstFile = "backup-mysql_" . date('Y-m-d_H\hi\_s') . ".sql.gz";
$dst = $dstDir . $dstFile;

if(creeDirDst($dstDir) === false) { echo "Erreur: impossible de créer le répertoire de destination"; exit; }

# lancement du mysqldump
system("mysqldump".
	" -h".escapeshellarg($pdo_host).
	" -u".escapeshellarg($pdo_user).
	" -p".escapeshellarg($pdo_pwd).
	" ".escapeshellarg($bdd_name).
	" | gzip> ".$dst);
echo "✔ Backup gzip MySQL OK (en ".temps($startTime)." secondes pour un poids de ".round(filesize($dst)/1024/1024)." mo)";

####################################
# FONCTIONS
function temps($startTime) { $now = time();	return ($now-$startTime); }

# crée le répertoire de destination s'il n'existe pas
function creeDirDst($rep) {
	if (!is_dir($rep)) { if(mkdir($rep) !== true) return false;	}
	# generation htaccess du protection des archives (s'il n'existe pas)
	if(!is_file($rep.'/.htaccess')) @file_put_contents($rep.'/.htaccess', 'deny from all');
	return true;
}

# renvoi true si system() est désactivé
function systemDisabled() {
	$disabled = explode(',', ini_get('disable_functions'));
	array_walk($disabled, 'trima');
	return in_array('system', $disabled);
}

function trima(& $t) { $t = trim($t); }
