<?php
session_start();

/*************************************************************
**  ./jeu/messagerie/mouve_message.php                      **
**  COPYRIGHT TERRES-ANCIENNES :) lol                       **
*************************************************************/

/*/ VERIFIE SI LE JOUEUR EST CONNECTE /*/

if( !isset($_SESSION['infos']))
{ echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html><head><body>
<script>
alert(\"Vous ne vous êtes pas identifié...\");
document.location.href='../../index.php'
</script></body></head></html>");
  exit;
}

/*/ CONNECTION A LA BASE DE DONNEE /*/

define('terres_anciennes',true);
include '../common-inc/config.inc.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

/*/ RECUPERE LE(S) MESSAGE(S), LES SUPPRIME OU LES ARCHIVE /*/

if($HTTP_POST_VARS['submit'] == 'Suprimer')
{
	for($i = 0; $i < count($HTTP_POST_VARS['msg']); $i++)
        {
        	mysql_query("DELETE FROM messagerie WHERE id = '".$HTTP_POST_VARS['msg'][$i]."' AND destinataire = '".$_SESSION['infos']['pseudo']."' LIMIT 1") or die("Erreur $i"); 
        }
        header("Location: ./messagerie.php");
}
else if($HTTP_POST_VARS['submit'] == 'Archiver')
{
	for($i = 0; $i < count($HTTP_POST_VARS['msg']); $i++)
        {
        	$id = $HTTP_POST_VARS['msg'][$i];
        	$sql = @mysql_query("SELECT * FROM messagerie WHERE id = '$id' AND destinataire = '".$_SESSION['infos']['pseudo']."' LIMIT 1") or die("Erreur $i");
        	if( mysql_num_rows($sql) == 0)
        	{
        		echo("Vous tentez d'archivez des messages ne vous appatenant pas...");
        		exit;
        	}
                else
                {
                	$message = mysql_fetch_array($sql,MYSQL_ASSOC);
                        $insert = mysql_query("INSERT INTO archives (time,sujet,pseudo,texte,part) VALUES ('".$message['time']."','".$message['sujet']."','".$_SESSION['infos']['pseudo']."','".$message['message']."','".$message['expediteur']."')") or die("Erreur");
                        @mysql_query("DELETE FROM messagerie WHERE id = '".$id."' AND destinataire = '".$_SESSION['infos']['pseudo']."' LIMIT 1") or die("Erreur $i");
                }
        }
        header("Location: ./messagerie.php");
}
?>
