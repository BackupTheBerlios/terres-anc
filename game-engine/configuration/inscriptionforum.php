<?php
/*
Engine of ``Terres-Anciennes", a web-based multiplayer RPG.
Copyright 2004, 2005 Nicolas Hess / Choplair-network / Nova Devs.
#$Id: inscriptionforum.php,v 1.3 2005/02/09 20:38:04 pachilor Exp $

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

/* VERIFIE SI LE JOUEUR EST CONNECTE */

if( !isset($_SESSION['infos']))
{ echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html><head><body>
<script>
alert(\"Vous ne vous êtes pas identifié...\");
document.location.href='../../index.php'
</script></body></head></html>");
  exit;
}

/* CONNECT TO DATABASE */

define('terres_anciennes',true);
include '../common-inc/config.inc.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

/* RECUPERE LES INFORMATIONS PERSONELLES */

$sql = mysql_query("SELECT *
FROM ( joueur a
RIGHT JOIN compte b ON a.id_account = b.id_account )
WHERE a.pseudo = '".$_SESSION['infos']['pseudo']."' LIMIT 1") or die("Erreur");
$perso = mysql_fetch_array($sql,MYSQL_ASSOC);

mysql_select_db($db_forum,$link) or die("Impossible de selectionner la base du forum");

/* IF PERSO EXISTS */

$sql = mysql_query("SELECT username FROM phpbb_users WHERE username = '".$_SESSION['infos']['pseudo']."'");

if( mysql_num_rows($sql) != 0)
{
	die("Vous etes deja inscrit: le mot de passe est celui de votre compte ;)");
        
}

/* ELSE */

switch($_SESSION['infos']['race'])
{
        case 'A':
	break;
	case 'C':
	break;
	case 'E':
	break;
	case 'H':
	break;
	case 'N':
	break;
}

echo "bon, je fais des test ;)";

exit;


if($_SESSION['infos']['race'] == 'A')
{
	$pass = md5($perso['passe']);
        mysql_query("INSERT INTO phpbb_users (username,user_regdate,user_password,user_rank,user_email)
                 VALUES('".$_SESSION['infos']['pseudo']."'," . time() . ",'$pass','3','".$perso['email']."')") or die('ERREUR');
        while($row = mysql_fetch_row(mysql_query("SELECT user_id FROM phpbb_users WHERE username='".$_SESSION['infos']['pseudo']."'")))
        {
        	mysql_query("INSERT INTO phpbb_user_group (group_id,user_id,user_pending) VALUES ('3','".$row[0]."','0')");
                break;
        }
}
if($_SESSION['infos']['race'] == 'C')
{
	$pass = md5($perso['passe']);
        mysql_query("INSERT INTO phpbb_users (username,user_regdate,user_password,user_rank,user_email)
                 VALUES('".$_SESSION['infos']['pseudo']."'," . time() . ",'$pass','4','".$_SESSION['infos']['email']."')") or die('ERREUR');
    while($row = mysql_fetch_row(mysql_query("SELECT user_id FROM phpbb_users WHERE username='".$_SESSION['infos']['pseudo']."'")))
    { mysql_query("INSERT INTO phpbb_user_group (group_id,user_id,user_pending)
                   VALUES ('4','".$row[0]."','0')");
      break; }
}
if($_SESSION['infos']['race'] == 'H')
{
	$pass = md5($perso['passe']);
        mysql_query("INSERT INTO phpbb_users (username,user_regdate,user_password,user_rank,user_email)
                 VALUES('".$_SESSION['infos']['pseudo']."'," . time() . ",'$pass','2','".$_SESSION['infos']['email']."')") or die('ERREUR');
    while($row = mysql_fetch_row(mysql_query("SELECT user_id FROM phpbb_users WHERE username='".$_SESSION['infos']['pseudo']."'")))
    { mysql_query("INSERT INTO phpbb_user_group (group_id,user_id,user_pending)
                   VALUES ('5','".$row[0]."','0')");
      break; }
}
if($_SESSION['infos']['race'] == 'N')
{
	$pass = md5($perso['passe']);
        mysql_query("INSERT INTO phpbb_users (username,user_regdate,user_password,user_rank,user_email)
                 VALUES('".$_SESSION['infos']['pseudo']."'," . time() . ",'$pass','5','".$_SESSION['infos']['email']."')") or die('ERREUR');
    while($row = mysql_fetch_row(mysql_query("SELECT user_id FROM phpbb_users WHERE username='".$_SESSION['infos']['pseudo']."'")))
    { mysql_query("INSERT INTO phpbb_user_group (group_id,user_id,user_pending)
                   VALUES ('7','".$row[0]."','0')");
      break; }
}
if($_SESSION['infos']['race'] == 'E')
{
	$pass = md5($perso['passe']);
        mysql_query("INSERT INTO phpbb_users (username,user_regdate,user_password,user_rank,user_email)
                 VALUES('".$_SESSION['infos']['pseudo']."'," . time() . ",'$pass','6','".$_SESSION['infos']['email']."')") or die('ERREUR');
    while($row = mysql_fetch_row(mysql_query("SELECT user_id FROM phpbb_users WHERE username='".$_SESSION['infos']['pseudo']."'")))
    { mysql_query("INSERT INTO phpbb_user_group (group_id,user_id,user_pending)
                   VALUES ('6','".$row[0]."','0')");
      break; }
}

echo("<html>
<head>
<title>Page pricipale du jeux fantastique</title>
<link rel='stylesheet' href='style.css'>
<body bgcolor='#000000' text='#FFFFFF' link='#FFFFFF' alink='#FFFFFF' vlink='#FFFFFF'>\n");
  echo("<center><b>Inscription réussie</b>
        <a href='./panneau_joueur.php'>Retour</a>");

?>
