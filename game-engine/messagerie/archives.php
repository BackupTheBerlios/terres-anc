<?php
session_start();

/************************************************************
**  ./jeu/messagerie/archives.php                          **
**  COPYRIGHT TERRES-ANCIENNES :) lol                      **
************************************************************/

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
include '../common-inc/fonctions.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

/*/ RECUPERE TOUT LES MESSAGE CONCERNES ET LES ASSEMBLE /*/

$pseudo = $_SESSION['infos']['pseudo'];

$sql = @mysql_query("SELECT * FROM archives WHERE pseudo = '$pseudo' ORDER BY time DESC LIMIT 80");
$nb = @mysql_num_rows($sql);

for($i=0;$i<$nb;$i++)
{
	$message[$i] = mysql_fetch_array($sql,MYSQL_ASSOC);
}

if( $nb == 0)
{ echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
  <title>TERRES - Messagerie - Lecture d'un message</title>
  <link rel=\"stylesheet\" href=\"style.css\">
</head>

<body bgcolor=\"#000000\" text=\"#FFFFFF\" link=\"#FFFFFF\" alink=\"#FFFFFF\" vlink=\"#FFFFFF\">

<div align=\"center\">
<br>
<font class=\"interdit\">Vous n'avez aucun messages archivés.</font><br><br>

<a href=\"./messagerie.php\">Retour à votre boite de reception</a>
</body>
</head>
</html>");
  exit;
}

echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
  <title>TERRES - Messagerie - Lecture d'un message</title>
  <link rel=\"stylesheet\" href=\"style.css\">
</head>

<body bgcolor=\"#000000\" text=\"#FFFFFF\" link=\"#FFFFFF\" alink=\"#FFFFFF\" vlink=\"#FFFFFF\">

<div align=\"center\">
<br>");

for($i=0;$i<$nb;$i++)
{
        $message[$i]['texte'] = ereg_replace("\n","<br>",$message[$i]['texte']);
        $message[$i]['texte'] = bbcode_first($message[$i]['texte']);
        $message[$i]['texte'] = clickable($message[$i]['texte']);

        echo("<table border=1 align=\"center\" width=\"70%\" cellpadding=\"10\">
<tr>
  <td bgcolor=\"#DEDEBE\">
    <div style=\"font-family: Arial,Verdana,times; font-size: 9pt; color: #000000;\">
      <b>Sujet:</b> ".$message[$i]['sujet']." &nbsp; &nbsp; <b>envoy&eacute; par:</b> ".$message[$i]['part']." &nbsp; &nbsp; <b>le:</b> ".date("d/m/Y à H:i:s",$message[$i]['time'])."
      <a href=\"./archives_supr.php?id=".$message[$i]['id']."\"><img src=\"./images/topic_delete.gif\" border=\"0\" alt=\"SUPRIMER DEFINITIVEMENT\" /></a></div>
  </td>
</tr>
<tr>
  <td bgcolor=\"#EEEEEE\">
    <div style=\"font-family: Arial,Verdana,times; font-size: 9pt; color: #000000;\">
      ".stripslashes($message[$i]['texte'])."
    </div>
  </td>
</tr>
</table><br>\n\n");

}
?>

<a href="./messagerie.php">Retour à la messagerie</a>

</div>
</body>
</html>
