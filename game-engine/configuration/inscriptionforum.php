<?php
session_start();

/***************************************************************************
* *                           inscriptionforum.php
* *                            -------------------
* *   begin     : Monday, January 31, 2005
* *   copyright : www.terres-anciennes.com
* *   email     : nicolas.hess@gmail.com, mortys_666@hotmail.com
* *               pachilor@hotmail.com  , stephmouton@hotmail.com
* *
* *   Version: index.php v 0.0.1
* *
* ***************************************************************************/

/***************************************************************************
* *
* *
* *
* *
* *
* *
* ***************************************************************************/

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
