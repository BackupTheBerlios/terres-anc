<?
session_start();

/***********************************************************************
**  ./jeu/sortir.php                                                  **
**  - VERIFIE SI LE JOUEUR EST CONNECTE                               **
**  - CONNECTION A LA BASE DE DONNEE                                  **
**  - CALCUL DE LA NOUVELLE POSITION                                  **
**  - INSCRIPTION DANS LA BASE DE DONNEE ET DANS LE FICHIER INFO      **
**  - REDIRECTION                                                     **
**  COPYRIGHT TERRES-ANCIENNES :) lol                                 **
***********************************************************************/

/* VERIFIE SI LE JOUEUR EST CONNECTE */

if( !is_array($_SESSION['infos']))
{ echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html><head><body>
<script>
alert(\"Vous ne vous êtes pas identifié...\");
document.location.href='../../index.php'
</script></body></head></html>");
  exit;
}

/* CONNECTION A LA BASE DE DONNEE */

define('terres_anciennes',true);
include '../include/config.inc.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

/* CALCUL DE LA NOUVELLE POSITION */

mt_srand(time());

/* VERIFICATION DE SES FICHIER D'INFORMATION */

$file_infos = './info/'.md5($perso['pseudo']).'.inc';

if( !file_exists($file_infos))
{ $new = fopen($file_infos,"w");
  fputs($new,"<?php
if ( !defined('terres_anciennes') )
{ die('Hacking attempt');
  exit; }
?>\n\n");
  fclose($new);
}

/* CALCUL DE LA NOUVELLE POSITION */

$x_arrivee = array(($_SESSION['infos']['x']+2),($_SESSION['infos']['x']+3),($_SESSION['infos']['x']+4),($_SESSION['infos']['x']-2),($_SESSION['infos']['x']-3),($_SESSION['infos']['x']-4));
$y_arrivee = array(($_SESSION['infos']['y']+2),($_SESSION['infos']['y']+3),($_SESSION['infos']['y']+4),($_SESSION['infos']['y']-2),($_SESSION['infos']['y']-3),($_SESSION['infos']['y']-4));

$random_x = mt_rand(0,count($x_arrivee));
$random_y = mt_rand(0,count($y_arrivee));

$_SESSION['infos']['x']               = $x_arrivee[$random_x];
$_SESSION['infos']['y']               = $y_arrivee[$random_y];
$_SESSION['infos']['mouvement_reste'] -= 1;
$_SESSION['infos']['place']           = 0;

/* INSCRIPTION DANS LA BASE DE DONNEE ET DANS LE FICHIER INFO */

$forteresse = mysql_query("SELECT nom
FROM forteresse
WHERE id = '".$_SESSION['infos']['place']."'
LIMIT 1");

$forteresse = mysql_fetch_array($forteresse,MYSQL_ASSOC);

$filename = "./info/info/".md5($_SESSION['infos']['pseudo']).".inc";
$myfile = @fopen($filename,"a");
fputs($myfile,"".time()."/Il est est sorti de: ".$forteresse['nom']."\n");
fclose($myfile);
        
@mysql_query("UPDATE joueur
SET x = '".$_SESSION['infos']['x']."',
    y = '".$_SESSION['infos']['y']."',
    mouvement_reste = '".$_SESSION['infos']['mouvement_reste']."',
    place = '0'
WHERE pseudo = '".$_SESSION['infos']['pseudo']."'") or die("Erreur lors de la sortie du personnage".$_SESSION['infos']['pseudo']."");

header("Location: ./general.php");
?>
