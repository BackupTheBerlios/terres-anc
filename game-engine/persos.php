<?php
session_start();

/* CONNECTION A LA BASE DE DONNEE */

define('terres_anciennes',true);
include '../include/config.inc.php';
include '../include/fonctions.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

/* VERIFICATION ET ACCEPTATION SI PSEUDO ET PASSWORD CORRECT */

if( isset($HTTP_POST_VARS["log"]))
{
	$post_ident = trim($HTTP_POST_VARS["log"]);
        $post_perso = trim($HTTP_POST_VARS["login"]);
        $post_passw = $HTTP_POST_VARS["pwd"];
        
        $a = mysql_query("SELECT * FROM compte WHERE id_account = '".$post_ident."' AND passe = '".$post_passw."' AND inscrit = 'Y' LIMIT 1");
        $b = mysql_query("SELECT * FROM compte WHERE pseudo = '".$post_perso."' AND passe = '".$post_passw."' AND inscrit = 'Y' LIMIT 1");
        
        if( mysql_num_rows($a) > 0)
        {
        	$compte_passew = $post_passw;
                $compte_identi = $post_ident;
                $compte_pseudo = mysql_fetch_row(mysql_query("SELECT pseudo FROM compte WHERE id_account='$compte_identi'"));
                $compte_pseudo = $compte_pseudo[0];
        }
        else if( mysql_num_rows($b) > 0)
        {
        	$compte_passew = $post_passw;
                $compte_pseudo = $post_perso;
                $compte_identi = mysql_fetch_row(mysql_query("SELECT id_account FROM compte WHERE pseudo='$compte_pseudo'"));
                $compte_identi = $compte_identi[0];
        }
        else
        {
        	echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html><head><body>
<script>
alert(\"Vous vous êtes mal identifié ou vous n'avez pas confirmé votre inscription...\");
document.location.href='../index.php'
</script></body></head></html>");
                
                exit;
        }
}
else if( isset($_SESSION['infos']))
{
        $compte_identi = $_SESSION['infos']['id_account'];
}
else
{
	echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html><head><body>
<script>
alert(\"Vous vous êtes mal identifié ou vous n'avez pas confirmé votre inscription...\");
document.location.href='../index.php'
</script></body></head></html>");

}

/* 
** RECUP ALL VARIABLES
** ACCEPT JUST TWO PLAYER BY COMPTE
*/

$sql = @mysql_query("SELECT a.*,b.id_account,b.passe
FROM ( joueur a
LEFT JOIN compte b ON a.id_account = b.id_account )
WHERE b.id_account='".$compte_identi."'
LIMIT 0,2");

if( mysql_num_rows($sql) > 0)
{
        for($i=0; $i<mysql_num_rows($sql); $i++)
        {
        	$perso[] = mysql_fetch_array($sql,MYSQL_ASSOC);
        }
        $compte_passew = $perso[0]['passe'];
}
else
{
	die("Vous n'avez aucun perso à jouer");
}
$nombre_perso = count($perso);

/*
** DISPLAY ON SCREEN AND CHOOSE
*/

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Language" content="fr" />

<title>Terres Anciennes - Connection</title>
<link rev="made" href="mailto:darkspace@club-internet.fr" />

<style type="text/css">
<!--
body {
        text-align: center;
        background-image: url(./images/paper017.jpg);
        margin: 0;
        font-family: "Trebuchet MS", Verdana, Helvetica, Arial, sans-serif;
        font-size: 0.8em;
        color: #000000;
}

a {
  	font-size: 15px;
        color: #000000;
}
a:hover {
        text-decoration: underline;
        color: #A3A3A3;
}

.lh2 {
     	text-align: center;
        margin-top: 20px;
        margin-bottom: 30px;
        font-size:28px;
        border: 0;
        color:black;
        text-indent: 0;
}

.bloc {
      	background: #eec;
        color: #000;
        float: left;
        width: 44%;
        border: 1px solid black;
        margin-left: 25px;
}

table.avat {
        width: 100%;
        height: 100%;
}

.img {
     	border: 0px;
}

div.heure {
        position: absolute;
        top: 30px;
        left:60px;
}

div.nom {
        text-align: center;
        font-size:20px;
        color:black;
        padding: 10px;
}

div.avatar {
        background: #EEEEEE;
        float: left;
        width: 195px;
        height: 250px;
        border: 1px solid black;
        left: 20px;
        position: relative;
}

div.infos {
        text-align:left;
        float: right;
        width: 45%;
        height: 250px;
}

div.clic {
        text-align:left;
        margin-left: 75px;
        width: 50%;
        float: left;
        margin-top: 15px;
        font-size: 0.7em;
}

div.decox {
        left: 200px;
        font-size: 0.8em;
        position: absolute;
        bottom: 50px;
}
-->
</style>
</head>
<body>

<div class="heure">heure: <?php echo date("H:i:s \l\e d/m/Y ",time()); ?></div>
<div class="lh2"> VOS PERSONNAGES! </div><p></p>
<?php

/* JUST ONE */

if( $nombre_perso == 1)
{
	$lien_1 = strrev(base64_encode(base64_encode(''.$compte_passew.'/'.$perso[0]['id'].'')));
	
	echo $lien;

        echo("<div class=\"bloc\">
<div class=\"nom\">".$perso[0]['pseudo']."</div>
<div class=\"avatar\"><table class=\"avat\"><tr><td valign=\"middle\"><a href=\"./general.php?p=".$lien_1."\">".avatar($perso[0]['pseudo'],'../../')."</a></td></tr></table></div>
<div class=\"infos\">
<br />
<ul>
<li>Race: ".race($perso[0]['race'])."</li>
<li>Classe: ".classe($perso[0]['classe'])."</li>
<li>x: ".$perso[0]['x']."</li>
<li>y: ".$perso[0]['y']."</li>
<li>pv: ".$perso[0]['pv_reste']." / ".$perso[0]['pv']."</li>");
    
        if( $perso[1]['classe'] != 'A')	echo("<li>pm: ".$perso[1]['magie_reste']." / ".$perso[1]['magie']."</li>");
  
        echo("<li>xp: ".$perso[0]['xp']."</li>
</ul>
</div>
<div class=\"clic\">(cliquez sur l'avatar pour acc&egrave;der au jeu)</div>
<p></p>
</div>");
}
else if( $nombre_perso == 2)
{
        $lien_1 = strrev(base64_encode(base64_encode(''.$compte_passew.'/'.$perso[0]['id'].'')));
        $lien_2 = strrev(base64_encode(base64_encode(''.$compte_passew.'/'.$perso[1]['id'].'')));

        echo("<div class=\"bloc\">
<div class=\"nom\">".$perso[0]['pseudo']."</div>
<div class=\"avatar\"><table class=\"avat\"><tr><td valign=\"middle\"><a href=\"./general.php?p=".$lien_1."\">".avatar($perso[0]['pseudo'],'../../')."</a></td></tr></table></div>
<div class=\"infos\">
<br />
<ul>
<li>Race: ".race($perso[0]['race'])."</li>
<li>Classe: ".classe($perso[0]['classe'])."</li>
<li>x: ".$perso[0]['x']."</li>
<li>y: ".$perso[0]['y']."</li>
<li>pv: ".$perso[0]['pv_reste']." / ".$perso[0]['pv']."</li>");

if( $perso[1]['classe'] != 'A')	echo("<li>pm: ".$perso[1]['magie_reste']." / ".$perso[1]['magie']."</li>");
  
echo("<li>xp: ".$perso[0]['xp']."</li>
</ul>
</div>
<div class=\"clic\">(cliquez sur l'avatar pour acc&egrave;der au jeu)</div>
<p></p>
</div>

<div class=\"bloc\">
<div class=\"nom\">".$perso[1]['pseudo']."</div>
<div class=\"avatar\"><table class=\"avat\"><tr><td valign=\"middle\"><a href=\"./general.php?p=".$lien_2."\">".avatar($perso[1]['pseudo'],'../../')."</a></td></tr></table></div>
<div class=\"infos\">
<br />
<ul>
<li>Race: ".race($perso[1]['race'])."</li>
<li>Classe: ".classe($perso[1]['classe'])."</li>
<li>x: ".$perso[1]['x']."</li>
<li>y: ".$perso[1]['y']."</li>
<li>pv: ".$perso[1]['pv_reste']." / ".$perso[1]['pv']."</li>");
    
if( $perso[1]['classe'] != 'A')	echo("<li>pm: ".$perso[1]['magie_reste']." / ".$perso[1]['magie']."</li>");
  
echo("<li>xp: ".$perso[1]['xp']."</li>
</ul>
</div>
<div class=\"clic\">(cliquez sur l'avatar pour acc&egrave;der au jeu)</div>
<p></p>
</div>");
}

$_SESSION['oui'] = 'yes';

?>
<p></p><p></p>
<div class="decox"><a href="./connexion/deconnexion.php">D&eacute;connection</a></div>

</body>
</html>
