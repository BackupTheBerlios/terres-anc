<?php
/*
Engine of ``Terres-Anciennes", a web-based multiplayer RPG.
Copyright 2004, 2005 Nicolas Hess / Choplair-network / Nova Devs.
#$Id: archives_supr.php,v 1.3 2005/02/09 20:38:04 pachilor Exp $

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

/************************************************************
**  ./jeu/messagerie/archives.php                          **
**  - VERIFIE SI LE JOUEUR EST CONNECTE                    **
**  - CONNECTION A LA BASE DE DONNEE                       **
**  - RECUPERE TOUT LES MESSAGE CONCERNES ET LES ASSEMBLE  **
************************************************************/

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

/* CONNECTION A LA BASE DE DONNEE */

define('terres_anciennes',true);
include '../common-inc/config.inc.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

/* RECUPERE TOUT LES MESSAGE CONCERNES ET LES ASSEMBLE */

if( !isset($HTTP_GET_VARS['id']))
{
	exit;
}
else
{
	$id = $HTTP_GET_VARS['id'];
        
        $sql = mysql_query("SELECT * FROM archives WHERE id = '$id' AND pseudo = '".$_SESSION['infos']['pseudo']."' LIMIT 1") or die("Erreur $i");
        if( mysql_num_rows($sql) == 0)
        {
        	echo("Vous tentez de suprimer des messages ne vous appatenant pas...");
        	exit;
        }
        
        mysql_query("DELETE FROM archives WHERE id = '$id' AND pseudo = '".$_SESSION['infos']['pseudo']."' LIMIT 1") or die("Erreur $i");

        header("location: ./messagerie.php");
}
?>
