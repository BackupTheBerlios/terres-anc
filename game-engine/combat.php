<?php
/*
Engine of ``Terres-Anciennes", a web-based multiplayer RPG.
Copyright 2004, 2005 Nicolas Hess / Choplair-network / Nova Devs.
#$Id: combat.php,v 1.3 2005/02/09 20:38:04 pachilor Exp $

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

session_start();

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
include 'common-inc/fonctions.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

/* AFFICHAGE */

echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
  <title>TERRES - Page de jeu</title>
<style>
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
a { font-size: 15px; font-family: Times New Roman; text-decoration: none; }
a:link { font-size: 15px; font-family: Times New Roman; text-decoration: none; }
a:visited { font-size: 15px; font-family: Times New Roman; text-decoration: none; }
a:hover { font-size: 15px; font-family: Times New Roman; text-decoration: underline; color: #A3A3A3; }
</style>
</head>

<body>\n");

/* FONCTIONS RELATIVES AU COMBAT - INFORMATIONS ENNEMIES */

function ajout_info($perso,$action)
{ $filename = "info/info/".md5($perso).".inc";
  if( !$myfile = @fopen($filename,"a") )
  { echo("Impossible d'écrire une nouvelle info: ".$action.".");
    exit;
  }
  fputs($myfile,"".time()."/".$action."\n");
  fclose($myfile);
}

if($_SESSION['infos']['nombre_reste'] <= 0)
{
	echo("<div align=\"center\">
        
<b>Vous n'avez plus d'attaques, vous devez attendre <br>
votre prochain tour pour pourvoir attaquer de nouveau...</b><br><br>

<a href=\"./general.php\">Retour &agrave; la page principale</a>

</div>
</body>
</html>");
        exit;
}

$perso = $HTTP_POST_VARS['attaque'];

$sql = mysql_query("SELECT * 
FROM joueur 
WHERE id = $perso
AND x BETWEEN ".($_SESSION['infos']['x']-1)." AND ".($_SESSION['infos']['x']+1)." 
AND y BETWEEN ".($_SESSION['infos']['y']-1)." AND ".($_SESSION['infos']['y']+1)." 
AND id != '".($_SESSION['infos']['id'])."'
AND place = '0'
LIMIT 1");

if( mysql_num_rows($sql) == 0)
{
	die("<span style=\"margin-left: 200px;\">

<b>Probl&egrave;mes lors de l'attaque, surement du &agrave;:<br>
- Vous vous attaquez vous-m&ecirc;me ou quelqu'un qui ne se situe pas &agrave; cot&ecirc; de vous...<br>
- se situe dans une forteresse<br>
- ou erreur dans la base de donnée, informez vous sur le forum</b><br><br>

<a href=\"./general.php\">Retour &agrave; la page principale</a>

</div>
</body>
</html>");
}

$perso = mysql_fetch_array($sql,MYSQL_ASSOC);

/* VALEURS D'ATTAQUE */

$valeurattaque = rand($_SESSION['infos']['habilete_cac'],($_SESSION['infos']['habilete_cac']-20));
$valeuresquive = rand($perso['esquive'],($perso['esquive']-20));

echo("<div align=\"center\">

Vous avez attaqu&eacute; le personnage: <b>".$perso['pseudo']."</b>.<br>
Pour cette attaque, la valeur &eacute;tait de: <b>$valeurattaque</b><br>
Et votre adversaire avait une valeur d'esquive de: <b>$valeuresquive</b><br><br>

Donc: <br><br>\n\n");

/* ATTAQUE RATEE */

if($valeurattaque < $valeuresquive)
{
	ajout_info($_SESSION['infos']['pseudo'],"".$_SESSION['infos']['pseudo']." a raté ".$perso['pseudo']."");
        ajout_info($perso['pseudo'],"Il a esquivé une attaque de la part de ".$_SESSION['infos']['pseudo']."");

        print("<b>Vous avez rat&eacute; votre attaque.</b><br>
Votre adversaire: ".$perso['pseudo']." a gagn&eacute; ".$_SESSION['infos']['niveau']." xp<br><br>");

        // UPDATE POUR LE JOUEUR ENNEMIE
        mysql_query("UPDATE joueur
               SET xp='".($perso['xp']+$_SESSION['infos']['niveau'])."',
                   xp_reste='".($perso['xp_reste']+$_SESSION['infos']['niveau'])."'
               WHERE pseudo='".$perso['pseudo']."'") or die("ERREUR1");

        // UPDATE POUR LE JOUEUR
        mysql_query("UPDATE joueur
               SET nombre_reste='".($_SESSION['infos']['nombre_reste']-1)."'
               WHERE pseudo='".$_SESSION['infos']['pseudo']."'") or die("ERREUR2");
}

/* ATTAQUE REUSSIE */

if($valeurattaque >= $valeuresquive)
{
	$force = rand(($_SESSION['infos']['force_cac']-1),($_SESSION['infos']['force_cac']+1));
        $gagnexp = rand(4,5);
        $degat = $force-$perso['armure'];

        echo("<b>Attaque réussie.</b><br>\n");

        // SI MORT

        if(($perso['pv_reste']-$degat) <= 0)
        {
        	ajouter_mort($_SESSION['infos']['id'],$perso['id']);
                ajout_info($_SESSION['infos']['pseudo'],"Il a tué ".$perso['pseudo']."");
                ajout_info($perso['pseudo'],"Il a été tué par ".$_SESSION['infos']['pseudo']."");

                $gagne_xp = $_SESSION['infos']['xp']+($perso['xp']/10);

                echo("<b>Vous avez TUE: ".$perso['pseudo']."</b><br><br>\n\n");

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
                            xp='".($_SESSION['infos']['xp']+$gagnexp+1)."',
                            xp_reste='".($_SESSION['infos']['xp_reste']+$gagnexp+1)."'
                        WHERE pseudo='".$_SESSION['infos']['pseudo']."'") or die("ERREUR2");
        }
        else
        {
        	ajout_info($_SESSION['infos']['pseudo'],"".$_SESSION['infos']['pseudo']." à touché ".$perso['pseudo']."");
                ajout_info($perso['pseudo'],"Il a été touché par ".$_SESSION['infos']['pseudo']."");

                echo("<font class='normal'>
  Vous avez gagn&eacute; <b>$gagnexp xp</b><br>
  Vous avez inflig&eacute;: <b>$degat</b> de d&eacute;gats &agrave; votre adversaire<br><br>\n\n");

                // UPDATE POUR LE JOUEUR ENNEMIE
                mysql_query("UPDATE joueur
                        SET pv_reste='".($perso['pv_reste']-$degat)."'
                        WHERE pseudo='".$perso['pseudo']."'") or die("ERREUR1");

                // UPDATE POUR LE JOUEUR
                mysql_query("UPDATE joueur
                        SET nombre_reste='".($_SESSION['infos']['nombre_reste']-1)."',
                            xp='".($_SESSION['infos']['xp']+$gagnexp)."',
                            xp_reste='".($_SESSION['infos']['xp_reste']+$gagnexp)."'
                        WHERE pseudo='".$_SESSION['infos']['pseudo']."'") or die("ERREUR2");
        }
}

$_SESSION['infos']['nombre_reste'] = $_SESSION['infos']['nombre_reste']-1;

echo("<a href=\"general.php\">Retour à la page principale</a>

</div>
</body>
</html>");
?>
