<?
/*
Engine of ``Terres-Anciennes", a web-based multiplayer RPG.
Copyright 2004, 2005 Nicolas Hess / Choplair-network / Nova Devs.
#$Id: sortir.php,v 1.3 2005/02/09 20:38:04 pachilor Exp $

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

session_start();

/***********************************************************************
**  ./jeu/sortir.php                                                  **
**  - VERIFIE SI LE JOUEUR EST CONNECTE                               **
**  - CONNECTION A LA BASE DE DONNEE                                  **
**  - CALCUL DE LA NOUVELLE POSITION                                  **
**  - INSCRIPTION DANS LA BASE DE DONNEE ET DANS LE FICHIER INFO      **
**  - REDIRECTION                                                     **
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
include 'common-inc/config.inc.php';

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
