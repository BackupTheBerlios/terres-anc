<?
session_start();

/***********************************************************************
**  ./jeu/configuration/panneau_joueur.php                            **
**  - VERIFIE SI LE JOUEUR EST CONNECTE                               **
**  - CONNECTION A LA BASE DE DONNEE                                  **
**  - CHANGEMENT DE MESSAGE                                           **
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
include '../common-inc/config.inc.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

/* RECUPERATION */

$sql = mysql_query("SELECT a.*, b.passe,b.email,b.bg
FROM ( joueur a
LEFT JOIN compte b ON a.id_account = b.id_account )
WHERE a.pseudo = '".$_SESSION['infos']['pseudo']."'") or die("Erreur");
$perso = mysql_fetch_array($sql,MYSQL_ASSOC);

if( isset($HTTP_GET_VARS['action']))
{
	if($HTTP_GET_VARS['action'] == 'password')
	{
		define('php',true);
		include './password.html.php';
		exit;
	}
	if($HTTP_GET_VARS['action'] == 'email')
	{
		define('php',true);
		include './email.html.php';
		exit;
	}
}

/* CHAGEMENT DE L'EMAIL */

if( isset($HTTP_POST_VARS['email']))
{
        function verifemail($email)
        {
        	$pdr="^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}$";
                if(!(eregi($pdr,$email)))
                {
                	echo("<font class=\"interdit\"><b>Votre email n'est pas valide</b></font>
          <br><a href=\"javascript:history.back()\" >retour</a>");
                        exit;
                }
                return $email;
        }

        $email = verifemail($HTTP_POST_VARS['email']);
        
        mysql_query("UPDATE compte
SET email = '$email'
WHERE id_account = '".$_SESSION['infos']['id_account']."'") or die("Erreur");
        echo("<font style=\"color:red; font-weight:bold;\">Vous avez bien chang&eacute; votre email</font><br /><br />");
        $perso['email'] = $email;
}

/* CHANGEMENT DU MOT DE PASSE */

if( isset($HTTP_POST_VARS['ancien']) && isset($HTTP_POST_VARS['new']) && isset($HTTP_POST_VARS['confnew']))
{
        if($HTTP_POST_VARS['new'] != $HTTP_POST_VARS['confnew'])
        {
        	echo("<font style=\"color:red; font-weight:bold;\">Vous devez inscrire 2 identiques</font><br /><br />");
        }
        else if( trim($HTTP_POST_VARS['new']) == '')
        {
        	echo("<font style=\"color:red; font-weight:bold;\">Votre nouveau mot de passe ne doit pas etre vide</font><br /><br />");
        }
        else if($HTTP_POST_VARS['ancien'] != $perso['passe'])
        {
        	echo("<font style=\"color:red; font-weight:bold;\">Ce n'est pas votre ancien mot de passe</font><br /><br />");
        }
        else
        {
        	mysql_query("UPDATE compte
SET passe = '".$HTTP_POST_VARS['new']."'
WHERE id_account = '".$_SESSION['infos']['id_account']."'") or die("Erreur");
                echo("<font style=\"color:red; font-weight:bold;\">Vous avez bien chang&eacute; de mot de passe</font><br /><br />");
        }
}

/* CHANGEMENT DE MESSAGE */

if( isset($HTTP_POST_VARS['message']))
{
        $message = htmlentities(addslashes(trim($HTTP_POST_VARS['message'])));
        mysql_query("UPDATE joueur
SET message_du_jour = '".$message."'
WHERE pseudo = '".$_SESSION['infos']['pseudo']."'") or die("Erreur");
        $_SESSION['infos']['message_du_jour'] = $HTTP_POST_VARS['message'];
}

function aug($xp_aug,$xp,$carac)
{
	if( $xp_aug <= $xp)
        {
        	$return = '<a href="./augmenter.php?action='.$carac.'"><font style="color:green; font-weight: bold;">augmenter</font></a>';
        }
        else
        {
        	$return = '<font style="color:red; font-weight: bold;">non</font>';
        }
        return $return;
}

/* AFFICHAGE */

echo("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html>
<head>

<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\" />
<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />
<meta http-equiv=\"Content-Language\" content=\"fr\" />
	
<title>Terres Anciennes - Configuration</title>
<style type=\"text/css\">
<!--
body {
background-image: url(./images/paper_blue.jpg);
color: #000000;
font-family: \"Trebuchet MS\", Verdana, Helvetica, Arial, sans-serif;
font-size: 0.8em;
text-align: center;
}

@font-face: url('./images/trebuc.ttf');

a { text-decoration: none; color: #000000; }
a:link { text-decoration: none; }
a:visited { text-decoration: none; }
a:hover { text-decoration: underline; color: #A3A3A3; }

a.noir { color: #000000; }

.interdit {
font-size: 15px;
font-weight: bold;
color: #FF0000;
font-family:  Times New Roman;
}

table {
empty-cells: show;
border-color: #A3A3A3;
border-collapse: collapse;
border: 1px solid black;
}

td,th { border-color: #A3A3A3; text-align: center; height: 28px; }
td.left,th.left { border-color: #A3A3A3; text-align: left; padding-left: 20px; }

th { border-bottom: 1px solid black; }

.inputjouer {
font-size: 20px;
font-weight: bold;
border-color: black;
border: solid 2px;
background-color: #C0C0C0;
}

input {
color: #0F1113; 
font-family: Arial,Verdana,times;
background-color: #E1E1E1;
text-decoration: none;
font-size: 13px;
border: 1px solid black;
padding: 3px 3px 3px 3px;
height: 15px;
}
-->
</style>
</head>

<body>
<div align=\"center\">

<br /><img src=\"./images/top.jpg\" alt=\"top_logo\" /><br /><br />


<form method=\"post\" action=\"./panneau_joueur.php\">
<table border=\"1\" width=\"900\" align=\"center\" cellpadding=\"4\" bgcolor=\"#EEEEEE\">
<tr>
  <td width=\"200\" class=\"left\"><b>Personnage</b></td>
  <td width=\"200\"> ".$perso['pseudo']." </td>
  <td width=\"200\"> &nbsp; </td>
</tr>
<tr><td width=\"200\" class=\"left\"><b>Votre Mot de passe:</b></td>
  <td width=\"200\"> ****** </td>
  <td width=\"200\"><a href=\"./panneau_joueur.php?action=password\" class=\"noir\">Changer le mot de passe</a></td>
</tr>
<tr><td width=\"200\" class=\"left\"><b>Votre Email:</b></td>
  <td width=\"200\"> ".$perso['email']." </td>
  <td width=\"200\"><a href=\"./panneau_joueur.php?action=email\" class=\"noir\">Changer le mail</a></td>
</tr>
<tr>
  <td width=\"200\" class=\"left\">
    <b>Message du jour:</b>
  </td>
  <td width=\"500\">
    <input type=\"text\" class=\"text\" name=\"message\" value=\"".stripslashes($_SESSION['infos']['message_du_jour'])."\" size=\"80\" maxlength=\"150\" />
  </td>
  <td width=\"200\">
    <input type=\"submit\" value=\"Changer\" />
  </td>
</tr>
</table>
</form><br />

<table border=\"1\" cellpadding=\"5\" width=\"900\" align=\"center\">
<tr bgcolor=\"#DEDEBE\">
  <th width=\"200\">Xp restant: <font class='interdit'>".$_SESSION['infos']['xp_reste']."</font></th>
  <th width=\"200\">Votre valeur actuelle:</th>
  <th width=\"200\">Prochain palier:</th>
  <th width=\"200\">Xp necessaire:</th>
  <th width=\"100\">Possible</th>
</tr>
<tr bgcolor=\"#EEEEEE\">
  <td>Points de Vie</td>
  <td>".$_SESSION['infos']['pv']."</td>
  <td>".($_SESSION['infos']['pv']+1)."</td>
  <td>5</td>
  <td>".aug(5,$_SESSION['infos']['xp'],'pv')."</td>
</tr>
<tr bgcolor=\"#EEEEEE\">
  <td>R&eacute;g&eacute;n&eacute;ration PV</td>
  <td>".$_SESSION['infos']['pv_regen']."</td>
  <td></td>
  <td></td>
  <td>".aug(10000,$_SESSION['infos']['xp'],'pv_regen')."</td>
</tr>
<tr bgcolor=\"#EEEEEE\">
  <td>Points de Magie</td>
  <td>".$_SESSION['infos']['magie']."</td>
  <td>".($_SESSION['infos']['magie']+1)."</td>
  <td>8</td>
  <td>".aug(8,$_SESSION['infos']['xp'],'pm')."</td>
</tr>
<tr bgcolor=\"#EEEEEE\">
  <td>R&eacute;g&eacute;n&eacute;ration des PM</td>
  <td>".$_SESSION['infos']['magie_regen']."</td>
  <td></td>
  <td></td>
  <td>".aug(100000,$_SESSION['infos']['xp'],'pm_regen')."</td>
</tr>");
if( $_SESSION['infos']['classe'] != 'A')
{
	echo("
<tr bgcolor=\"#EEEEEE\">
  <td>Habilit&eacute; (cac)</td>
  <td>".$_SESSION['infos']['habilete_cac']."</td>
  <td></td>
  <td></td>
  <td>".aug(100000,$_SESSION['infos']['xp'],'h_cac')."</td>
</tr>
<tr bgcolor=\"#EEEEEE\">
  <td>Force (cac)</td>
  <td>".$_SESSION['infos']['force_cac']."</td>
  <td></td>
  <td></td>
  <td>".aug(100000,$_SESSION['infos']['xp'],'f_cac')."</td>
</tr>");
}
if( ($_SESSION['infos']['classe'] == 'A') || ($_SESSION['infos']['classe'] == 'F'))
{
	echo("
<tr bgcolor=\"#EEEEEE\">
  <td>Habilit&eacute; (dis)</td>
  <td>".$_SESSION['infos']['habilete_cac']."</td>
  <td></td>
  <td></td>
  <td>".aug(100000,$_SESSION['infos']['xp'],'h_dis')."</td>
</tr>
<tr bgcolor=\"#EEEEEE\">
  <td>Force (dis)</td>
  <td>".$_SESSION['infos']['force_cac']."</td>
  <td></td>
  <td></td>
  <td>".aug(100000,$_SESSION['infos']['xp'],'f_dis')."</td>
</tr>");
}















echo("</table>

<br /><a href=\"inscriptionforum.php\"><font style=\"color:white;\">Inscrition automatique au forum</font></a><br />
<a href='../general.php'><font style=\"color:white;\">Retour</font></a>

</div>
</body>
</html>");
?>
