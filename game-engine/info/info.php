<?php
/*
Engine of ``Terres-Anciennes", a web-based multiplayer RPG.
Copyright 2004, 2005 Nicolas Hess / Choplair-network / Nova Devs.
#$Id: info.php,v 1.3 2005/02/09 20:38:04 pachilor Exp $

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
**  ./jeu/info/info.php                                               **
**  - VERIFIE SI LE JOUEUR EST CONNECTE                               **
**  - CONNECTION A LA BASE DE DONNEE                                  **
**  - RECUPERATION DES VARIABLES DU JOUEUR (VERIF SI VISIBLE)         **
**  - VERIFICATION DE SES FICHIER D'INFORMATION (INFOS,MORTS,MEUTRES) **
**  - LECTURE DE CES FICHIERS D'INFORMATIONS => TABLEAUX              **
**  - FONCTIONS                                                       **
**  - AFFICHAGE DES INFORMATIONS                                      **
***********************************************************************/

/* VERIFIE SI LE JOUEUR EST CONNECTE */

if( !isset($_SESSION['infos']['pseudo']))
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

/* RECUPERATION DES VARIABLES DU JOUEUR (VERIF SI VISIBLE) */

$sql_0 = mysql_query("SELECT *
FROM joueur
WHERE id='".$HTTP_GET_VARS['info']."'");

if( mysql_num_rows($sql_0) == 0)
{
	die("Soit ce perso n'existe pas soit il n'est pas visible...");
}

$perso = mysql_fetch_array($sql_0,MYSQL_ASSOC);

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

/* LECTURE DE CES FICHIERS D'INFORMATIONS => TABLEAUX */


$p=0;
if( $myfile = fopen($file_infos,"r"))
{ $line_infos = file($file_infos,40);
  if(count($line_infos) >= 7)
  { for($i = 0; $i < count($line_infos); $i++)
    { if($i > 6)
      { $infos[$p] = explode("/",$line_infos[$i]); $p++; }
    }
    if( is_array($infos))
    { $infos = array_reverse($infos); }
    fclose($myfile);
  }
  else
  { $infos = 'null'; }
}

/* AFFICHAGE DES INFORMATIONS */

$titre = "TERRES - Informations sur: ".stripslashes($perso['pseudo'])."";

echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
  <title>$titre</title>
  <link rel=\"stylesheet\" href=\"./style.css\">
</head>

<body text=\"#000000\" link=\"#000000\" alink=\"#000000\" vlink=\"#000000\">

<div align=\"center\">
<table style=\"border:solid 1px black;\" cellpadding=\"6\" bgcolor=\"#EEEEEE\">
<tr>
  <td>
    <font style=\"font-size:28px; color:black;\"> &nbsp; &nbsp; ".stripslashes($perso['pseudo'])." &nbsp; &nbsp; </font>
  </td>
</tr>
</table>

<br /><br />
<p>
<font style=\"color:white;\"><b>Race:</b> ".race($perso['race'])."<br>
<b>Classe:</b> ".classe($perso['classe'])."<br>
<b>Sexe:</b> ".sexe($perso['sexe'])."<br><p>

<table border=1 cellpadding=5 width=\"900\">
<tr bgcolor=\"#DEDEBE\" height=\"30\" align=\"center\">
  <th>Message du jour:</th>
  <th>Victimes:</th>
  <th>Morts:</th>
  <th>Avatar:</th>
</tr>
<tr bgcolor=\"#EEEEEE\" height=\"150\" align=\"center\">
  <td width=300> <b>".stripslashes($perso['message_du_jour'])."</b>  </td>
  <td width=200>
    <div id=\"info_cadre\">\n");

$sql_1 = mysql_query("SELECT a.*,b.id,b.pseudo
FROM ( joueur_mort a
LEFT JOIN joueur b ON a.perso_mort_id = b.id )
WHERE a.acteur_id = '".$perso['id']."'
ORDER BY a.time DESC");

while($a = mysql_fetch_array($sql_1,MYSQL_ASSOC))
{
        echo("      <br><font style=\"color:white;\">Le ".date("d M",$a['time']).": ".$a['pseudo']."</font>\n");
}

echo("      </div>
  </td>
  <td width=200>
    <div id=\"info_cadre\">");

$sql_2 = mysql_query("SELECT a.*,b.id,b.pseudo
FROM ( joueur_mort a
LEFT JOIN joueur b ON a.acteur_id = b.id )
WHERE a.perso_mort_id = '".$perso['id']."'
ORDER BY a.time DESC");

while($b = mysql_fetch_array($sql_2,MYSQL_ASSOC))
{
        echo("      <br><font style=\"color:white;\">Le ".date("d M",$b['time'])." par ".$b['pseudo']."</font>\n");
}

echo("      </div>
  </td>
  <td witdh=\"150\">
    ".avatar($perso['pseudo'])."
  </td>
</tr>
</table>
<br>

<table border=1 cellpadding=3 width=\"900\" cellpadding=5 align='center'>
<tr bgcolor=\"#DEDEBE\" height=\"30\" align=\"center\">
  <th width=300>
    Date de l'action:
  </th>
  <th width=550>
    Nature de l'action:
  </th>
</tr>\n");

$min = min(count($infos),30);

if($infos != 'null')
{ for($k = 0; $k < $min; $k++)
  { echo("<tr bgcolor=\"#EEEEEE\" height=\"25\" align=\"center\">
  <td>
    <span class=\"normal\" style=\"margin: 10px 10px 10px 10px\">".date("\L\e d/m/Y à H:i:s",$infos[$k][0])."</span>
  </td>
  <td>
    <span class=\"normal\" style=\"margin-left: 50px\">".$infos[$k][1]."</span>
  </td>
</tr>\n");
  }
}
?>
</table>
<br>

<a href="" onClick="self.close()"><font style="color:white;">Fermer</a><br><br>

</div>
</head>
</body>
</html>
