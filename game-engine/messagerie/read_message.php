<?php
session_start();

/***********************************************************************
**  ./jeu/messagerie/read_message.php                                 **
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

$titre = "TERRES - Messagerie - Lecture d'un message";

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title><? echo $titre; ?></title>
  <meta http-equiv="Content-Language" content="fr">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
  <style type="text/css">
html
{ font-family: Times New Roman,Arial,Sans-Serif; }

body { 
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

td {
border-bottom:1px solid black;
border-right:1px solid black;
border-top:0px;
border-left:0px;
border-collapse: collapse;
}
  </style>
</head>

<body text="#FFFFFF" link="#FFFFFF" alink="#FFFFFF" vlink="#FFFFFF">

<?php

/*/ VERIFICATION DE L'APPARTENANCE DU MESSAGE /*/

$sql = mysql_query("SELECT *
FROM messagerie
WHERE id='".$HTTP_GET_VARS['num']."' AND destinataire = '".$_SESSION['infos']['pseudo']."'
LIMIT 1");

if(! ($m = mysql_fetch_array($sql,MYSQL_ASSOC)))
{
	echo("<br><br><div style=\"margin-left: 350px;\" align=\"left\">

<b>Probl&egrave;mes lors de la lecture du message id = ".$HTTP_GET_VARS['num'].", surement d&ucirc; &agrave;:<br>
- Vous lisez un message qui ne vous appartient pas...<br>
- ou erreur dans la base de donnée, informez vous sur le forum.</b></div>

<br><br>

<a href=\"./messagerie.php\">Retour &agrave; la messagerie</a>

</body>
</html>");
        exit;
}

/*/ MET LE MESSAGE EN LU /*/

if($message['lu'] == 0)
{
	mysql_query("UPDATE messagerie SET lu='1' WHERE id = '".$HTTP_GET_VARS['num']."'") or die("Erreur");
}


/*/ TRANSFORME LE MESSAGE /*/

define('IN_PHPBB', true);
$phpbb_root_path = '../../forum/';

include('../../forum/extension.inc');
include('../../forum/common.'.$phpEx);
include('../../forum/includes/bbcode.'.$phpEx);

@mysql_select_db($dbform);

$m['message'] = ereg_replace("\n","<br>",$m['message']);
$m['message'] = bbcode_first($m['message']);
$m['message'] = smilies_pass_nicolas($m['message']);
$m['message'] = clickable($m['message']);

?><div align="center">

<br>
Envoyé par: <b><? echo $m['expediteur']; ?></b><br>
Recu par: <b><? echo $m['destinataire']; ?></b><br>
Le: <b><? echo date("d/m/Y à H:i:s",$m['time']); ?></b><p>

<table style="border: 1px solid black; border-collapse: collapse;" align="center" width="760" cellpadding="10">
<tr>
  <td rowspan="2" width="150" bgcolor="#EEEEEE" align="center">
    <b><u><? echo $m['expediteur']; ?></u></b><br><br>
    <? echo avatar($m['expediteur']); ?>
  </td>
  <td bgcolor="#DEDEBE" width="600" height="50">
    <div style="font-family: Arial,Verdana,times; font-size: 9pt; color: #000000;">
      <b><u>Sujet:</u></b> <? echo $m['sujet']; ?></div>
  </td>
</tr>
<tr>
  <td bgcolor="#EEEEEE" valign="top">
    <div style="font-family: Arial,Verdana,times; font-size: 9pt; color: #000000;">
      <br><? echo stripslashes($m['message']); ?>
    </div>
  </td>
</tr>
</table>
<p>
<? echo("<a href=\"./send_message.php?reply=".$m['id']."\">Renvoyer un message &agrave; ".$m['expediteur']."</a>"); ?>
<p>
<a href="./messagerie.php">Retour à la messagerie</a>
</div>
</body>
</html>
