<?php
session_start();

/***************************************************************************
* *                              combatdis.php
* *                            -------------------
* *   begin     : Monday, January 31, 2005
* *   copyright : www.terres-anciennes.com
* *   email     : nicolas.hess@gmail.com, mortys_666@hotmail.com
* *               pachilor@hotmail.com  , stephmouton@hotmail.com
**
* *   Version: index.php v 0.0.1
* *
* *
* ***************************************************************************/

/***************************************************************************
* *
* *
* *
* *
* *
* *
* ***************************************************************************/


/* VERIFIE SI LE JOUEUR EST CONNECTE */

if( !isset($_SESSION['infos']))
{
	echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html><head><body>
<script>
alert(\"Vous ne vous êtes pas identifié...\");
document.location.href='../../index.php'
</script></body></head></html>");
        exit;
}

/* CONNECTION A LA BASE DE DONNEE */

define('terres_anciennes',true);
include 'common-inc/config.inc.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

/* FONCTIONS RELATIVES AU COMBAT */

function ajout_info($perso,$action)
{ $filename = "info/info/".md5($perso).".inc";
  if( !$myfile = @fopen($filename,"a") )
  { echo("Impossible d'écrire une nouvelle info: ".$action.".");
    exit;
  }
  fputs($myfile,"".time()."/".$action."\n");
  fclose($myfile);
}

/* INFORMATIONS ENNEMIES */

if($_SESSION['infos']['nombre_reste'] <= 0)
{
	echo("<center><font color='interdit'><b>Cela de sert à rien car vous n'avez plus d'attaques, sorry :(</b></font>");
        exit;
}

$perso = $HTTP_POST_VARS['attaquedistance'];

$sql = mysql_query("SELECT * FROM joueur WHERE id = $perso AND x BETWEEN ".($_SESSION['infos']['x']-$_SESSION['infos']['portee']-1)." AND ".($_SESSION['infos']['x']+$_SESSION['infos']['portee']+1)." AND y BETWEEN ".($_SESSION['infos']['y']-$_SESSION['infos']['portee']-1)." AND ".($_SESSION['infos']['y']+$_SESSION['infos']['portee']+1)." AND place = '0' LIMIT 1");
if( mysql_num_rows($sql) == 0)
{
	die("Erreur");
}
else
{
	$perso = mysql_fetch_array($sql,MYSQL_ASSOC);
}

/* VALEURS D'ATTAQUE */

$valeurattaque = rand($_SESSION['infos']['habilete_dis'],($_SESSION['infos']['habilete_dis']-20));
$valeuresquive = rand($perso['esquive'],($perso['esquive']-20));

echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
  <title>TERRES - Page de jeu</title>
  <link rel=\"stylesheet\" href=\"style_general.css\">
</head>

<body bgcolor=\"#000000\" text=\"#FFFFFF\" link=\"#FFFFFF\" alink=\"#FFFFFF\" vlink=\"#FFFFFF\">

<div align=\"center\">

Vous avez attaqué le personnage: <b>".$perso['pseudo']."</b>.<br>
Pour cette attaque, la valeur était de: <b>$valeurattaque</b><br>
Et votre adversaire avait une valeur d'esquive de: <b>$valeuresquive</b><br><br>Donc: <br><br>");

/* ATTAQUE RATEE */

if($valeurattaque < $valeuresquive)
{
	ajout_info($_SESSION['infos']['pseudo'],"".$_SESSION['infos']['pseudo']." a raté ".$perso['pseudo']."");
        ajout_info($perso['pseudo'],"Il a esquivé une attaque de la part de ".$_SESSION['infos']['pseudo']."");
        
        print("<b>Vous avez rat&eacute; votre attaque.</b><br>
Votre adversaire: ".$perso['pseudo']." a gagn&eacute; ".$_SESSION['infos']['niveau']." xp<br><br>");
  
        // UPDATE POUR LE JOUEUR ENNEMIE
        mysql_query("UPDATE joueur
               SET xp='".($perso['xp']+$_SESSION['infos']['niveau'])."', xp_reste='".($perso['xp_reste']+$_SESSION['infos']['niveau'])."'
               WHERE pseudo='".$perso['pseudo']."'") or die("ERREUR1");

        // UPDATE POUR LE JOUEUR
        mysql_query("UPDATE joueur
               SET nombre_reste='".($_SESSION['infos']['nombre_reste']-1)."'
               WHERE pseudo='".$_SESSION['infos']['pseudo']."'") or die("ERREUR2");
}

/* ATTAQUE REUSSIE */

if($valeurattaque >= $valeuresquive)
{
	$force = rand(($_SESSION['infos']['force_dis']-1),($_SESSION['infos']['force_dis']+1));
        $gagnexp = rand(4,5);
        $degat = $force-$perso['armure'];
        
        echo("<b>Attaque réussie.</b><br>");

        // SI MORT
        
        if(($perso['pv_reste']-$degat) <= 0)
        {
        	ajouter_mort($_SESSION['infos']['id'],$perso['id']);
                
                $gagne_xp = $_SESSION['infos']['xp']+($perso['xp']/10);
                
                echo("<font class='normal'>Vous avez TUE: ".$perso['pseudo']."<br><br>");
                
                $fort = mysql_query("SELECT * 
                        FROM forteresse
                        WHERE race='".$perso['race']."' AND capitale='Y' LIMIT 1");
                $for  = mysql_fetch_array($fort);

                // UPDATE POUR LE JOUEUR ENNEMIE
                mysql_query("UPDATE joueur
                        SET xp='".($perso['xp']-$_SESSION['infos']['niveau'])."',
                            xp_reste='".($perso['xp_reste']-$_SESSION['infos']['niveau'])."',
                            x='".($for['x']+1)."',
                            y='".($for['y']-1)."',
                            place='".$for['id']."'
                        WHERE pseudo='".$perso['pseudo']."'") or die("ERREUR1");

                // UPDATE POUR LE JOUEUR
                mysql_query("UPDATE joueur
                        SET nombre_reste='".($_SESSION['infos']['nombre_reste']-1)."',
                            xp='".$gagne_xp."',
                            xp_reste='".$gagne_xp."'
                        WHERE pseudo='".$_SESSION['infos']['pseudo']."'") or die("ERREUR2");
        }
        else
        {
        	ajout_info($_SESSION['infos']['pseudo'],"".$_SESSION['infos']['pseudo']." à touché ".$perso['pseudo']."");
                ajout_info($perso['pseudo'],"Il a été touché par ".$_SESSION['infos']['pseudo']."");
                
                echo("<font class='normal'>
  Vous avez gagné $gagnexp xp<br>
  Vous avez infligé: <b>$degat</b> de dégats à votre adversaire<br><br>");
  
                // UPDATE POUR LE JOUEUR ENNEMIE
                mysql_query("UPDATE joueur
                        SET   pv_reste='".($perso['pv_reste']-$degat)."'
                        WHERE pseudo='".$perso['pseudo']."'") or die("ERREUR1");

                // UPDATE POUR LE JOUEUR
                mysql_query("UPDATE joueur
                        SET nombre_reste='".($_SESSION['infos']['nombre_reste']-1)."',
                            xp='".($_SESSION['infos']['xp']+$gagnexp)."',
                            xp_reste='".($_SESSION['infos']['xp_reste']+$gagnexp)."'
                        WHERE pseudo='".$_SESSION['infos']['pseudo']."'") or die("ERREUR2");
        }
}

//------------------------------------------perte d'une attaque--------------------------------------//

echo("<a href='general.php'>Retour à la page principale</a>");
$_SESSION['infos']['nombre_reste'] = $_SESSION['infos']['nombre_reste']-1;
?>
