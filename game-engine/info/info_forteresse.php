<?
/*
Engine of ``Terres-Anciennes", a web-based multiplayer RPG.
Copyright 2004, 2005 Nicolas Hess / Choplair-network / Nova Devs.
#$Id: info_forteresse.php,v 1.3 2005/02/09 20:38:04 pachilor Exp $

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
**  COMMENCEMENT DE LA PARTIE PHP SI PSEUDO CORRECT                   **
**  - VERIFIE SI LE JOUEUR EST CONNECTE                               **
**  - CONNECTION A LA BASE DE DONNEE                                  **
**  - RECUPERE LES VARIBLE DE LA FORTERESSE ET CALCULE SON ETAT       **
**  - RECHERCHE LES PERSOS A L'INTERIEUR                              **
**  - AFFICHAGE DES INFORMATIONS                                      **
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
include '../common-inc/config.inc.php';
include '../common-inc/fonctions.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

/* RECUPERE LES VARIBLE DE LA FORTERESSE ET CALCULE SON ETAT */

$sql = mysql_query("SELECT a.*, b.pseudo,b.id,b.race AS perso_race,b.classe
FROM ( forteresse a
LEFT JOIN joueur b ON a.id = b.place )
WHERE a.id = '".$HTTP_GET_VARS['id']."'");

if( mysql_num_rows($sql) > 0)
{ for($i = 0; $i < mysql_num_rows($sql); $i++ )
  { $f[] = mysql_fetch_array($sql,MYSQL_ASSOC); } } else $f = 'null';

if($f[0]['pv_reste'] >= ($f[0]['pv']*(0.8)))
{ $etat = 'En bon &eacute;tat'; }
else if($f[0]['pv_reste'] >= ($f[0]['pv']*(0.5)))
{ $etat = 'Enceinte fissur&eacute;e'; }
else if($f[0]['pv_reste'] >= ($f[0]['pv']*(0.3)))
{ $etat = 'Breches dans le mur'; }
else if($f[0]['pv_reste'] >= ($f[0]['pv']*(0.1)))
{ $etat = 'Trous b&eacute;ants dans les défenses'; }
else
{ $etat = 'En ruine'; }

/* RECHERCHE LES PERSOS A L'INTERIEUR */

$titre = "Terres - Information sur: ".$f[0]['nom']."";

echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
  <title>$titre</title>
  <link rel=\"stylesheet\" href=\"./style.css\">
</head>

<body text=\"#FFFFFF\" link=\"#FFFFFF\" alink=\"#FFFFFF\" vlink=\"#FFFFFF\">

<div align=\"center\">
<table style=\"border:solid 1px black;\" cellpadding=\"6\" bgcolor=\"#EEEEEE\">
<tr>
  <td>
    <font style=\"font-size:28px; color:black;\"> &nbsp; &nbsp; ".stripslashes($f[0]['nom'])." &nbsp; &nbsp; </font>
  </td>
</tr>
</table>

<p><br><img src=\"./images/".$f[0]['image']."\" width=\"300\" height=\"200\" border=\"0\" align=\"center\"><p><br>
<font style=\"color: white;\">Etat de la forteresse: <b>".$etat."</b></font><br><br>\n");

for($i=0;$i<count($f);$i++)
{
	if($f[$i]['perso_race'] == $f[$i]['race'])
        {
        	echo("<a href='info.php?info=".$f[$i]['id']."'>- ".$f[$i]['pseudo']."<br>\n");
        }
        else
        {
        	echo("<a href='info.php?info=".$f[$i]['id']."'>- ".$f[$i]['pseudo']." (<font class='interdit'>".race($f[$i]['perso_race'])."</font>)<br>\n");
        }
}
?>
<p><a href="" onClick="window.close();">Fermer</a>
</div>
</head>
</body>
</html>
