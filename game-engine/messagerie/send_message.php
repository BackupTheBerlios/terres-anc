<?
session_start();

/***************************************************************
**  ./jeu/messagerie/send_message.php                         **
**  COPYRIGHT TERRES-ANCIENNES :) lol                         **
***************************************************************/

/*/ VERIFIE SI LE JOUEUR EST CONNECTE /*/

if( !is_array($_SESSION['infos']))
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

/* VISION TOUS */

$infor_persos = mysql_query("SELECT pseudo
FROM joueur
WHERE x BETWEEN ".($_SESSION['infos']['x']-$_SESSION['infos']['vision'])." AND ".($_SESSION['infos']['x']+$_SESSION['infos']['vision'])." 
AND y BETWEEN ".($_SESSION['infos']['y']-$_SESSION['infos']['vision'])." AND ".($_SESSION['infos']['y']+$_SESSION['infos']['vision'])."
AND place = '0'
AND pseudo != '".$_SESSION['infos']['pseudo']."'
ORDER BY x ASC, y DESC");

if( mysql_num_rows($infor_persos) > 0)
{ for($i = 0; $i < mysql_num_rows($infor_persos); $i++ )
  { $persos[$i] = mysql_fetch_array($infor_persos,MYSQL_ASSOC);
    $perso[$i]  = $persos[$i]['pseudo'];
  }
}
else $perso = array('','');
$vision = implode($perso,'/');

/* VISION JUSTE RACE */

$infor_persos = mysql_query("SELECT pseudo
FROM joueur
WHERE x BETWEEN ".($_SESSION['infos']['x']-$_SESSION['infos']['vision'])." AND ".($_SESSION['infos']['x']+$_SESSION['infos']['vision'])."
AND y BETWEEN ".($_SESSION['infos']['y']-$_SESSION['infos']['vision'])." AND ".($_SESSION['infos']['y']+$_SESSION['infos']['vision'])."
AND race = '".$_SESSION['infos']['race']."'
AND place = '0'
AND pseudo != '".$_SESSION['infos']['pseudo']."'
ORDER BY x ASC, y DESC");

if( mysql_num_rows($infor_persos) > 0)
{ for($i = 0; $i < mysql_num_rows($infor_persos); $i++ )
  { $persos[$i] = mysql_fetch_array($infor_persos,MYSQL_ASSOC);
    $perso[$i]  = $persos[$i]['pseudo'];
  }
}
else $perso = array('','');
$vision_1 = implode($perso,'/');

/*/ AJOUT DES INFORMATIONS PERSONNELLES /*/

$sql = mysql_query("SELECT *
FROM joueur_options
WHERE id_joueur = '".$_SESSION['infos']['id']."' LIMIT 1");

if( mysql_num_rows($sql) == 0)
{
	mysql_query("INSERT INTO joueur_options (id_joueur) VALUES ('".$_SESSION['infos']['id']."')");
	header("Location: ./send_message.php");

}
else
{
	$r = mysql_fetch_array($sql,MYSQL_ASSOC);
}

/*/ PARTIE DU DEBUT HTML /*/

$titre = "TERRES - Messagerie - Envoie d'un message";

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title><?php echo $titre; ?></title>
  <link rel="stylesheet" href="style.css">

<body bgcolor="#000000" text="#FFFFFF" link="#FFFFFF" alink="#FFFFFF" vlink="#FFFFFF">

<script language="JavaScript" type="text/javascript">
function emoticon(text)
{
	var txtarea = document.post.message;
	text = ' ' + text + ' ';
	if (txtarea.createTextRange && txtarea.caretPos)
        {
		var caretPos = txtarea.caretPos;
		caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
		txtarea.focus();
	}
        else
        {
		txtarea.value  += text;
		txtarea.focus();
	}
}
</script>
</head>
<div align="center">

<br>
<table style="border:solid 1px grey;" cellpadding="6">
<tr>
  <td>
    <font style="font-size:24px; color:black;"><?php echo(" Envoie d'un message "); ?></font>
  </td>
</tr>
</table>

<br>

<?php

/*/ PREPARATION DES MESSAGES /*/

$message_envoie_bdd = ( isset($HTTP_POST_VARS['message'])) ? bbcode_first(ereg_replace("\n","<br>",$HTTP_POST_VARS['message'])) : '';


if( $r['accept_smilies'] == 'Y')
{
        $signature = '
---------
'.$r['signature'].'';
}

/*/ ENVOIE DU MESSAGE /*/

if( isset($HTTP_POST_VARS['send']))
{
	if($HTTP_POST_VARS['send'] == 'envoyer' )
        {
        	$boum = explode("/",$_POST['destinataires']);
        	if( isset($HTTP_POST_VARS['copie']))
                {
                	array_push($boum,$_SESSION['infos']['pseudo']);
                }
                $message = bbcode_first(ereg_replace("\n","<br>",htmlentities(addslashes($_POST['message']))));
                if( $r['accept_signature'] == 'Y')
                {
                	$message = $message.'<br><br>---------<br>'.$r['signature'].'';
                }
                $expediteur = $_SESSION['infos']['pseudo'];
                $sujet = $_POST['sujet'];
                
                if( trim($sujet) == '' )
                { $sujet = 'Aucun...'; }
                
                echo("<div align='center'>
<h3>Message bien envoyé à:</h3><p>\n");
                
                for($i = 0; $i < count($boum);$i++)
                {
                	$truc = mysql_query("SELECT pseudo FROM joueur WHERE pseudo='$boum[$i]'");
                        
                        if(mysql_num_rows($truc) == 0)
                        { echo("<font class='interdit'>$boum[$i] -> Introuvable</font><br>\n"); }
                        else
                        {
                        	echo("$boum[$i] -> OK<br>\n");
                                $save = mysql_query("INSERT INTO messagerie (time,sujet,expediteur,destinataire,message) VALUES ('".time()."','$sujet','$expediteur','$boum[$i]','$message')") or die("Erreur");
                        }
                }
                echo("<p><a href='messagerie.php'><font class='normal'>Retour à la messagerie</font></a>");
                exit;
        }
}

/* TABLEAU DES SMILES HTML */

?>
<form method="post" action="./send_message.php" name="post">
<table border="0" cellpadding="6" align="center">
<tr>
  <td align="center" valign="bottom" width="10%">
    <table width="100" border="0" cellspacing="0" cellpadding="5" style="border:solid 1px grey;" align="center">
    <tr align="center" valign="middle">
      <td><a href="javascript:emoticon(':D')"><img src="./images/smileys/smile.gif" border="0" alt="Very Happy" title="Very Happy" /></a></td>
      <td><a href="javascript:emoticon(':)')"><img src="./images/smileys/happy.gif" border="0" alt="Smile" title="Smile" /></a></td>
      <td><a href="javascript:emoticon(':(')"><img src="./images/smileys/sad.gif" border="0" alt="Sad" title="Sad" /></a></td>
      <td><a href="javascript:emoticon(':o')"><img src="./images/smileys/huh.gif" border="0" alt="Surprised" title="Surprised" /></a></td>
    </tr>
    <tr align="center" valign="middle">
      <td><a href="javascript:emoticon(':x')"><img src="./images/smileys/mad.gif" border="0" alt="Mad" title="Mad" /></a></td>
      <td><a href="javascript:emoticon(':P')"><img src="./images/smileys/tongue.gif" border="0" alt="Razz" title="Razz" /></a></td>
      <td><a href="javascript:emoticon(':oops:')"><img src="./images/smileys/blush.gif" border="0" alt="Embarassed" title="Embarassed" /></a></td>
      <td><a href="javascript:emoticon(':love:')"><img src="./images/smileys/icon_amour.gif" border="0" alt="Amour" title="Amour" /></a></td>
    </tr>
    <tr align="center" valign="middle">
      <td><a href="javascript:emoticon(':|')"><img src="./images/smileys/mellow.gif" border="0" alt="Neutral" title="Neutral" /></a></td>
      <td><a href="javascript:emoticon(':mrgreen:')"><img src="./images/smileys/icon_mrgreen.gif" border="0" alt="Mr. Green" title="Mr. Green" /></a></td>
      <td><a href="javascript:emoticon(':sleep:')"><img src="./images/smileys/sleep.gif" border="0" alt="Sleep" title="Sleep" /></a></td>
      <td><a href="javascript:emoticon(':crazy:')"><img src="./images/smileys/wacko.gif" border="0" alt="Crazy" title="Crazy" /></a></td>
    </tr>
    <tr align="center" valign="middle">
      <td><a href="javascript:emoticon(':?')"><img src="./images/smileys/unsure.gif" border="0" alt="Confused" title="Confused" /></a></td>
      <td><a href="javascript:emoticon('8)')"><img src="./images/smileys/cool.gif" border="0" alt="Cool" title="Cool" /></a></td>
      <td><a href="javascript:emoticon(':lol:')"><img src="./images/smileys/laugh.gif" border="0" alt="Laughing" title="Laughing" /></a></td>
      <td><a href="javascript:emoticon(':roll:')"><img src="./images/smileys/rolleyes.gif" border="0" alt="Rolling Eyes" title="Rolling Eyes" /></a></td>
    </tr>
    <tr align="center" valign="middle">
      <td><a href="javascript:emoticon(':!:')"><img src="./images/smileys/excl.gif" border="0" alt="Exclamation" title="Exclamation" /></a></td>
      <td><a href="javascript:emoticon(':ninja:')"><img src="./images/smileys/ninja.gif" border="0" alt="Ninja" title="Ninja" /></a></td>
      <td><a href="javascript:emoticon(':hehe:')"><img src="./images/smileys/mistake.gif" border="0" alt="Mistake" title="Mistake" /></a></td>
      <td><a href="javascript:emoticon(':king:')"><img src="./images/smileys/king.gif" border="0" alt="King" title="King" /></a></td>
    </tr>
    <tr align="center" valign="middle">
      <td><a href="javascript:emoticon(':nrv:')"><img src="./images/smileys/icon_nrv.gif" border="0" alt="Enerve" title="Enerve" /></a></td>
      <td><a href="javascript:emoticon(':twisted:')"><img src="./images/smileys/icon_twisted.gif" border="0" alt="Twisted Evil" title="Twisted Evil" /></a></td>
      <td><a href="javascript:emoticon(':shock:')"><img src="./images/smileys/blink.gif" border="0" alt="Shocked" title="Shocked" /></a></td>
      <td><a href="javascript:emoticon(':wink:')"><img src="./images/smileys/wink.gif" border="0" alt="Wink" title="Wink" /></a></td>
    </tr>
    </table>
    <br><br><br><br>
  </td>
  <td width="90%" align="center">

<?php


/* PREVISUALISATION DU MESSAGE */

if( isset($HTTP_POST_VARS['send']))
{
	if($HTTP_POST_VARS['send'] == 'previsualisation')
        {
                define('IN_PHPBB', true);
                $phpbb_root_path = '../../forum/';
                include('../../forum/extension.inc');
                include('../../forum/common.'.$phpEx);
                include('../../forum/includes/bbcode.'.$phpEx);
                @mysql_select_db($dbform);

                $message_trans = ereg_replace("\n","<br>",$HTTP_POST_VARS['message']);

                $message_trans = smilies_pass_nicolas($message_trans);

                echo("    <table border=1 align='center' width=\"700\" cellpadding=10>
    <tr>
      <td bgcolor=\"#EEEEEE\">
        <div style=\"font-family: Arial,Verdana,times; font-size: 9pt;\" class='normal_black'>".stripslashes($message_trans)."
<br>".stripslashes(ereg_replace("\n","<br>",$signature))."</font>
      </td>
    </tr>
    </table><p>\n\n");

                $value_sujet = $HTTP_POST_VARS['sujet'];
                $value_desti = $HTTP_POST_VARS['destinataires'];
                $message = $HTTP_POST_VARS['message'];
        }
}

/* SI IL S'AGIT DE REPONDRE A QUELQU'UN */

if( isset($HTTP_GET_VARS['reply']))
{
	$sql = mysql_query("SELECT * FROM messagerie WHERE id='".$HTTP_GET_VARS['reply']."'");
        $recup = mysql_fetch_array($sql,MYSQL_ASSOC);
        
        if( $recup['destinataire'] != $_SESSION['infos']['pseudo'] )
        { echo("<font class='interdit'>Vous répondez à un message qui ne vous été pas destiné...</font>"); }

        $value_sujet = 'Re: '.$recup['sujet'].'';
        $value_desti = ''.$recup['expediteur'].'';
        $message = '';
}
else if( !isset($HTTP_GET_VARS['reply']) && !isset($HTTP_POST_VARS['send']))
{
	$value_sujet = '';
        $value_desti = '';
        $message = '';
}

/* FIN HTML */

?>  <table border="0" cellpadding="5" align="center">
    <tr>
      <td width="20%">
        <center><font valign="top">Destinataires:
      </td>
      <td width="80%">
        <input type="text" size="75" maxlength="150" name="destinataires" class="text" <?php echo("value='".$value_desti."'"); ?>> &nbsp; (séparé par des /)
      </td>
    </tr>
    <tr>
      <td>
        <center><font valign="top">Sujet:
      </td>
      <td>
        <input type="text" size="35" maxlength="255" name="sujet" class="text" <?php echo("value='".$value_sujet."'"); ?>> <input type="checkbox" id="radio" name="copie" nochecked><label for="radio"><?php echo("M'en envoyer une copie"); ?></label>
      </td>
    </tr>
    <tr>
      <td>
      </td>
      <td>
        <? echo("<input type=\"button\" value=\"Destinataires dans votre vision\" onClick=\"document.post.destinataires.value='".$vision."'\"> &nbsp; &nbsp; &nbsp; <input type=\"button\" value=\"Meme race dans la vision\" onClick=\"document.post.destinataires.value='".$vision_1."'\">"); ?>
      </td>
    </tr>
    <tr>
      <td>
        <center><font valign="top">Message:</div>
      </td>
      <td>
        <textarea cols="100" rows="15" name="message"><?php echo(stripslashes($message).""); ?></textarea>
      </td>
    </tr>
    <tr>
      <td>
      </td>
      <td>
        <center><input type="submit" name="send" value="previsualisation"> &nbsp; &nbsp; &nbsp; <input type="submit" name="send" value="envoyer">
      </td>
    </tr>
    </table>
  </td>
</tr>
</table>


<p><a href="./messagerie.php">Retour à la messagerie</a>

</div>
</body>
</html>
