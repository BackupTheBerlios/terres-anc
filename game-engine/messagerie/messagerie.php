<?php
session_start();

/***********************************************************************
**  ./jeu/messagerie/messagerie.php                                   **
**  COPYRIGHT TERRES-ANCIENNES :) lol                                 **
***********************************************************************/

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
include '../../include/config.inc.php';
include '../../include/fonctions.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

/*/ RECUPERE TOUT LES MESSAGE CONCERNES ET LES ASSEMBLE /*/

$texte_accueil = "Bienvenue dans votre messagerie, ".$_SESSION['infos']['pseudo']."";

echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
  <title>TERRES - Messagerie</title>
  <meta http-equiv=\"Content-Language\" content=\"fr\">
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">
  <style type=\"text/css\">
html
{ font-family: Times New Roman,Arial,Sans-Serif; }

body { 
background: #E8DCA8;
background-image: url(./images/paper017.jpg);
color: #000000;
font-family: Times New Roman,Arial,Sans-Serif;
font-size: 11px;
text-align: center;
}

* { font-size: 15px; font-family: Times New Roman; color: #000000;}
a { font-size: 15px; font-family: Times New Roman; text-decoration: none;}
a:link { font-size: 15px; font-family: Times New Roman; text-decoration: none; }
a:visited { font-size: 15px; font-family: Times New Roman; text-decoration: none; }
a:hover { font-size: 15px; font-family: Times New Roman; text-decoration: underline; color: #A3A3A3; }

.interdit {
font-size: 15px;
font-weight: bold;
color: #FF0000;
font-family:  Times New Roman;
}

td {
border-bottom:0px;
border-right:0px;
border-top:0px;
border-left:0px;
border-collapse: collapse;
}

.normal_black {
font-size:    15px;
font-family:  Times New Roman;
color: #000000;
}

input {
color: #0F1113;
font-family: Arial,Verdana,times;
background-color: #E1E1E1;
text-decoration: none;
font-size: 9pt;
border: 1px solid black;
padding: 3px;
margin: 2px 10px 2px 2px;
height: 22px;
}
  </style>
  <script language=\"javascript\" type=\"text/javascript\">
  function select_switch(status)
  {
	  for (i = 0; i < document.cocher.length; i++)
          {
          	  document.cocher[i].checked = status;
          }
  }
  </script>
</head>
<body text=\"#FFFFFF\" link=\"#FFFFFF\" alink=\"#FFFFFF\" vlink=\"#FFFFFF\">

<div align=\"center\">

<br><table style=\"border:solid 1px grey;\" cellpadding=\"6\"><tr><td><font style=\"font-size:28px; color:black;\"> $texte_accueil </font></td></tr></table><p>
<br><a href=\"options_messagerie.php\">Option de votre messagerie</a> &nbsp; -  &nbsp; <a href=\"./archives.php\">Consulter vos archives</a><p>

<form method=\"post\" action=\"move_message.php\" name=\"cocher\">

<table style=\"border:solid 1px black; border-collapse:collapse;\" width=\"950\" cellpadding=\"9\">
<tr bgcolor=\"#EEEEEE\">
  <th width=140 height=\"40\">> Exp&eacute;diteur</th>
  <th width=140 height=\"40\">> Date de l'envoie</th>
  <th width=365 height=\"40\">> Sujet</th>
  <th width=150 height=\"40\">> R&eacute;pondre</th>
  <th width=155 height=\"40\">> Cocher</th>
</tr>\n");

$recupmessage = mysql_query("SELECT id,time,sujet,expediteur,lu
FROM messagerie
WHERE destinataire = '".$_SESSION['infos']['pseudo']."'
ORDER BY lu ASC, time DESC
LIMIT 30") or die("Impossible de recuperer les informations de votre messagerie interne...");

if( mysql_num_rows($recupmessage) == 0)
{
	echo("<tr bgcolor=\"#C0C0C0\" align=\"center\" height=\"43\">
  <td colspan=\"5\">Vous n'avez aucun message</td>
</tr>\n");
}
else
{
	while($m = mysql_fetch_array($recupmessage,MYSQL_ASSOC))
        {
        	switch($m['lu'])
                {
                	case 0: echo("<tr bgcolor='#C0C0C0' align=\"center\">
  <td height=\"43\"><font style=\"color: white;\">".$m['expediteur']."</font></td>
  <td height=\"43\"><font style=\"color: white;\">".date("d/m/Y à H:i:s",$m['time'])."</font></td>
  <td height=\"43\"><a href='read_message.php?num=".$m['id']."'><font style=\"color: white;\"><b>".$m['sujet']."</b></font></a></td>
  <td height=\"43\"><a href='send_message.php?reply=".$m['id']."'><font style=\"color: white;\">R&eacute;pondre</font></a></td>
  <td height=\"43\"><input type='checkbox' name='msg[]' value='".$m['id']."'></td>
</tr>\n");
                        break;
                        case 1: echo("<tr bgcolor='#000000' align=\"center\">
  <td height=\"43\"><font style=\"color: white;\">".$m['expediteur']."</font></td>
  <td height=\"43\"><font style=\"color: white;\">".date("d/m/Y à H:i:s",$m['time'])."</font></td>
  <td height=\"43\"><a href='read_message.php?num=".$m['id']."'><font style=\"color: white;\">".$m['sujet']."</font></a></td>
  <td height=\"43\"><a href='send_message.php?reply=".$m['id']."'><font style=\"color: white;\">R&eacute;pondre</font></a></td>
  <td height=\"43\"><input type='checkbox' name='msg[]' value='".$m['id']."'></td>
</tr>\n");
                        break;
                        case 3: echo("<tr bgcolor='#A3A3A3' align=\"center\">
  <td height=\"43\"><font style=\"color: white;\">".$m['expediteur']."</font></td>
  <td height=\"43\"><font style=\"color: white;\">".date("d/m/Y à H:i:s",$m['time'])."</font></td>
  <td height=\"43\"><font style=\"color: white;\"><a href='read_message.php?num=".$m['id']."'><b><font class='normal'>".$m['sujet']."</b></a></td>
  <td colspan=2 height=\"43\"><font class=\"interdit\">Vous ne pouvez rien faire sur ce message</font></td>
</tr>\n");
                        break;
                }
        }
}
?>
<tr bgcolor="#EEEEEE" align="center">
  <td colspan="2" style="border-top: 1px solid black;" height="43">
    Le nombre de message est limit&eacute; &agrave; <b>30</b>
  </td>
  <td style="border-top: 1px solid black;" height="43">
    <font color="black"><b>Pour la sélection:</b></font>
  </td>
  <td colspan=3 style="border-top: 1px solid black;" height="43">
    <input type="submit" name="submit" value="Suprimer" onclick="if (window.confirm('Etes-vous sûr de vouloir suprimer ces messages ?')) {location.href='move_message.php'; return true;} else {return false;}" />&nbsp;&nbsp;&nbsp;
    <input type="submit" name="submit" value="Archiver" onclick="if (window.confirm('Etes-vous sûr de vouloir archiver ces messages ?')) {location.href='move_message.php'; return true;} else {return false;}" />&nbsp;&nbsp;&nbsp;
    <a href="javascript:select_switch(true);"><font color="black">Tout</font></a>&nbsp;&nbsp;&nbsp;
    <a href="javascript:select_switch(false);"><font color="black">Retirer</font></a>
  </td>
</tr>
</table>
</form>

<p><p>
<a href="send_message.php">Pour envoyer un message...</a><p><p>
<a href="../general.php">Retour &agrave; la page principale</a>

<!-- 1 requete sql dans cette page au maximum -->

</div>
</body>
</html>
