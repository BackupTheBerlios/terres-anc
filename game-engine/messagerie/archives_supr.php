<?php
session_start();

/************************************************************
**  ./jeu/messagerie/archives.php                          **
**  - VERIFIE SI LE JOUEUR EST CONNECTE                    **
**  - CONNECTION A LA BASE DE DONNEE                       **
**  - RECUPERE TOUT LES MESSAGE CONCERNES ET LES ASSEMBLE  **
**  COPYRIGHT TERRES-ANCIENNES :) lol                      **
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
