<?
session_start();

/***********************************************************************
**  options_messagerie.php                                            **
**  - VERIFIE SI LE JOUEUR EST CONNECTE                               **
**  COPYRIGHT TERRES-ANCIENNES :) lol                                 **
***********************************************************************/

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
include '../../include/config.inc.php';
include '../../include/fonctions.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

$sql = mysql_query("SELECT * FROM joueur_options WHERE id_joueur = '".$_SESSION['infos']['id']."' LIMIT 1");
if( mysql_num_rows($sql) == 0)
{
	mysql_query("INSERT INTO joueur_options (id_joueur) VALUES ('".$_SESSION['infos']['id']."')");
}

if( isset($HTTP_POST_VARS['submit']))
{
	if( isset($_POST['accept_signature']))
	{ $accept_signature = 'Y'; }
	else
	{ $accept_signature = 'N'; }
        if( isset($_POST['accept_smilies']))
	{ $accept_smilies = 'Y'; }
	else
	{ $accept_smilies = 'N'; }

	$signature = trim(addslashes(htmlentities($_POST['signature'])));

        mysql_query("
UPDATE joueur_options
SET signature = '$signature',accept_smilies='$accept_smilies',accept_signature='$accept_signature'
WHERE id_joueur = '".$_SESSION['infos']['id']."'") or die("Erreur lors de l'enregistrement des options");

        echo("<table style=\"border:solid 1px grey;\" cellpadding=\"6\"><tr><td>Vos options sont enregistrées</td></tr></table><br><br>");
}

$sql = mysql_query("SELECT * FROM joueur_options WHERE id_joueur = '".$_SESSION['infos']['id']."' LIMIT 1");
$options = mysql_fetch_array($sql,MYSQL_ASSOC);

/* PARTIE DU DEBUT HTML */

$titre = "TERRES - Messagerie - Options";

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title><?php echo $titre; ?></title>
  <link rel="stylesheet" href="style.css">
</head>

<body bgcolor="#000000" text="#FFFFFF" link="#FFFFFF" alink="#FFFFFF" vlink="#FFFFFF">

<div align="center">

<br><table style="border:solid 1px grey;" cellpadding="6"><tr><td><font style="font-size:24px; color:black;"><?php echo(" Options de la Messagerie "); ?></font></td></tr></table>
<br>
</div>
<div align="left">

<br><form action="./options_messagerie.php" method="POST">
<?php

if( $options['accept_signature'] == 'Y')
{ echo("<input type=\"checkbox\" id=\"1\" value=\"accept_signature\" name=\"accept_signature\" checked><label for=\"1\">Inserer sa signatuture pour tout les messages</label><br>\n"); }
else
{ echo("<input type=\"checkbox\" id=\"1\" value=\"accept_signature\" name=\"accept_signature\"><label for=\"1\">Inserer sa signatuture pour tout les messages</label><br>\n");}

if( $options['accept_smilies'] == 'Y')
{ echo("<input type=\"checkbox\" id=\"2\" value=\"accept_smilies\" name=\"accept_smilies\" checked><label for=\"2\">Accepter les smilies</label><br>\n"); }
else
{ echo("<input type=\"checkbox\" id=\"2\" value=\"accept_smilies\" name=\"accept_smilies\"><label for=\"2\">Accepter les smilies</label><br>\n");}
?>

<br><br>
<font valign="top">Signature: &nbsp; </font><textarea cols="75" rows="7" maxleght="200" name="signature">
<?php echo stripslashes($options['signature']); ?></textarea><br><br>

<input type="submit" value="Valider" name="submit">

</form>

<p><a href="./messagerie.php">Retour à la messagerie</a>

</div>
</body>
</html>
