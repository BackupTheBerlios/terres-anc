<?
/*
Engine of ``Terres-Anciennes", a web-based multiplayer RPG.
Copyright 2004, 2005 Nicolas Hess / Choplair-network / Nova Devs.
#$Id: augmenter.php,v 1.3 2005/02/09 20:38:04 pachilor Exp $

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
**  ./jeu/configuration/panneau_joueur.php                            **
**  - VERIFIE SI LE JOUEUR EST CONNECTE                               **
**  - CONNECTION A LA BASE DE DONNEE                                  **
**  - CHANGEMENT DE MESSAGE                                           **
***********************************************************************/

/* VERIFIE SI LE JOUEUR EST CONNECTE */

if( !isset($_SESSION['infos']))
{ echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html><head><body>
<script>
alert(\"Vous ne vous �tes pas identifi�...\");
document.location.href='../../index.php'
</script></body></head></html>");
  exit;
}

/* CONNECTION A LA BASE DE DONNEE */

define('terres_anciennes',true);
include '../common-inc/config.inc.php';

$link = @mysql_pconnect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

if( isset($HTTP_GET_VARS['action']))
{
	$action = $HTTP_GET_VARS['action'];
}
else
{
	exit;
}

echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html>
<head>

<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\" />
<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />
<meta http-equiv=\"Content-Language\" content=\"fr\" />
	
<title>Terres Anciennes - Augmentation</title>
<style type=\"text/css\">
<!--
body {
background-image: url(./images/paper_blue.jpg);
color: #FFFFFF;
font-family: \"Trebuchet MS\", Verdana, Helvetica, Arial, sans-serif;
font-size: 0.8em;
text-align: center;
}

a { text-decoration: none; color: #FFFFFF; }
a:link { text-decoration: none; }
a:visited { text-decoration: none; }
a:hover { text-decoration: underline; color: #A3A3A3; }

-->
</style>
</head>

<body>

<div align=\"center\">
<br />");

switch($action)
{
        case 'pv':
                if( $_SESSION['infos']['xp_reste'] >= 5)
                {
                	mysql_query("UPDATE joueur SET pv = pv+1, xp_reste = xp_reste-5 WHERE pseudo = '".$_SESSION['infos']['pseudo']."' LIMIT 1") or die("Erreur");
                        $_SESSION['infos']['xp_reste'] -= 5; $_SESSION['infos']['pv'] += 1;
                        echo("Vos points de vie sont maintenant &agrave; ".$_SESSION['infos']['pv']." pv. Vous avez perdu 5 xp.");
                }
                else
                {
                	echo("Vous n'avez pas assez d'xp");
                }
        break;
        case 'pv_regen':
        break;
        case 'pm':
                if( $_SESSION['infos']['xp_reste'] >= 8)
                {
                	mysql_query("UPDATE joueur SET magie = magie+1, xp_reste = xp_reste-8 WHERE pseudo = '".$_SESSION['infos']['pseudo']."' LIMIT 1") or die("Erreur");
                        $_SESSION['infos']['xp_reste'] -= 8; $_SESSION['infos']['magie'] += 1;
                        echo("Vos points de magie sont maintenant &agrave; ".$_SESSION['infos']['magie']." pm. Vous avez perdu 8 xp.");
                }
                else
                {
                	echo("Vous n'avez pas assez d'xp");
                }
        break;
        case 'pm_regen':
        break;
        case 'pv':
        break;
        case 'pv':
        break;
        case 'pv':
        break;
}

echo("<br /><br /><a href=\"./panneau_joueur.php\">Retour au panneau joueur</a>");
?>
</div>
</body>
</html>
