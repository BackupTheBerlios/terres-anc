<?php
session_start();

/***************************************************************************
* *                                magie.php
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
include 'common-inc/config.inc.php';
include 'common-inc/fonctions.php';

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

$perso = $HTTP_POST_VARS['attaquesort'];
$sort  = $HTTP_POST_VARS['sortilege'];

$sql = mysql_query("SELECT *
                    FROM joueur
                    WHERE id = $perso
                    AND x BETWEEN ".($_SESSION['infos']['x']-$_SESSION['infos']['vision']-1)." AND ".($_SESSION['infos']['x']+$_SESSION['infos']['vision']+1)."
                    AND y BETWEEN ".($_SESSION['infos']['y']-$_SESSION['infos']['vision']-1)." AND ".($_SESSION['infos']['y']+$_SESSION['infos']['vision']+1)."
                    AND place = '0' LIMIT 1");

if( mysql_num_rows($sql) == 0)
{ die("Erreur"); }
else
{ $perso = mysql_fetch_array($sql,MYSQL_ASSOC); }

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

* { font-size: 15px; font-family: Times New Roman; }
a { font-size: 15px; font-family: Times New Roman; text-decoration: none; }
a:link { font-size: 15px; font-family: Times New Roman; text-decoration: none; }
a:visited { font-size: 15px; font-family: Times New Roman; text-decoration: none; }
a:hover { font-size: 15px; font-family: Times New Roman; text-decoration: underline; color: #A3A3A3; }
</style>
</head>

<body bgcolor=\"#DEDEDE\" text=\"#000000\" link=\"#000000\" alink=\"#000000\" vlink=\"#000000\">

<div align=\"center\">

Vous avez attaqué le personnage: <b>".$perso['pseudo']."</b>.<br>");

/* VERIFICATION SI CE PERSO PEUT LANCER LE SORT */

function verif_magie($cout,$sort,$fois)
{
	if(  $_SESSION['infos']['magie_reste'] < $cout)
	{
		die("Vous n'avez pas assez de mana pour lancer ce sort");
        }

        $time = time();
        $sql = mysql_query("SELECT *
FROM magie
WHERE id_perso = '".$_SESSION['infos']['id']."'
      AND magie = '".$sort."'
      AND fin > '".$_SESSION['infos']['date_tour']."'");
$lance = mysql_num_rows($sql);
       
        if( $lance >= $fois)
        {
        	die("<br>Vous ne pouvez plus lancer ce sort, il ne peux être lancé que <b>$fois</b> fois...<br><br>
<a href=\"./general.php\">Retour</a>

</body>
</html>");
        }
        
        mysql_query("UPDATE joueur
SET magie_reste = '".($_SESSION['infos']['magie_reste']-$cout)."'
WHERE pseudo = '".$_SESSION['infos']['pseudo']."'") or die("Erreur...");

        $_SESSION['infos']['magie_reste'] = $_SESSION['infos']['magie_reste']-$cout;
}

/* SWITCH */

                	        /*if($sort == 'puissancec')
                                {
                                	verif_magie(10,'puissancec',1);
                                        if($perso['pseudo'] != $_SESSION['infos']['pseudo'])
                                        {
                                        	die("Le sort puissance cach&eacute;e ne peux &ecirc;tre lanc&eacute; que sur soi-meme...");
                        	        }
                        	        else
                        	        {
                                                mysql_query("UPDATE joueur SET pv_reste = '".($perso['pv_reste']-10)."', force_cac = '".($perso['force_cac']+5)."' WHERE pseudo = '".$perso['pseudo']."'") or die("Erreur1");
                                                mysql_query("INSERT INTO magie (id_perso,debut,magie,fin) VALUES ('".$_SESSION['infos']['id']."','".$_SESSION['infos']['date_tour']."','puissancec','".($_SESSION['infos']['date_tour']+$temps_tour)."')") or die("Erreur2");
                                                $resultat = "Vous avez perdu 10 points de vie et 10 de mana<br>
                                                mais vous avez gagn&eacute;  5 de force au corps a corps.<br>
                                                PS: ce sort ne peut &ecirc;tre utilis&eacute; qu'une fois dans un tour de jeu...";
                                                ajout_info($_SESSION['infos']['pseudo'],"il s'est lancé le sort puissance cachée sur lui meme");
                                        }
                                }*/

switch($_SESSION['infos']['niveau'])
{
	case 1:
        	switch($_SESSION['infos']['classe'])
                {
                	case 'A':
                        break;
                	case 'B':
                        break;
                	case 'F':
                        break;
                	case 'G':
                        break;
                	case 'M':
                        	if($sort == 'soin')
                                {
                                	verif_magie(5,'soin',4);
                                        if($perso['pseudo'] == $_SESSION['infos']['pseudo'])
                                        {
                                        	die("<br>Le sort soin ne peut pas être lancer sur soi-meme...");
                        	        }
                        	        else
                        	        {
                        	        	$perso['pv_reste'] = ($perso['pv_reste'] >= ($perso['pv']-5)) ? $perso['pv'] : $perso['pv_reste']+5;
                                                mysql_query("UPDATE joueur SET pv_reste = '".$perso['pv_reste']."' WHERE pseudo = '".$perso['pseudo']."'") or die("Erreur1");
                                                mysql_query("INSERT INTO magie (id_perso,debut,magie,fin) VALUES ('".$_SESSION['infos']['id']."','".$_SESSION['infos']['date_tour']."','soin','".($_SESSION['infos']['date_tour']+$temps_tour)."')") or die("Erreur2");
                                                $resultat = "Vous avez soign&eacute; ".$perso['pseudo']." de 5 points de vie.<br>
                                                PS: ce sort ne peut &ecirc;tre utilis&eacute; que quatre fois maximum dans un tour de jeu...";
                                                ajout_info($_SESSION['infos']['pseudo'],"il a lance le sort soin sur ".$perso['pseudo']."");
                                                ajout_info($perso['pseudo'],"".$perso['pseudo']." a ete soigne par ".$_SESSION['infos']['pseudo']."");
                                        }
                                }
                                if($sort == 'foudre')
                                {
                                	verif_magie(10,'foudre',1);
                                        if($perso['pseudo'] == $_SESSION['infos']['pseudo'])
                                        {
                                        	die("<br>Le sort soin ne peut pas être lancé sur soi-meme...");
                        	        }
                        	        else
                        	        {
                        	        	$perso['pv_reste'] = (($perso['pv_reste']-10) >= 0) ? ($perso['pv_reste']-10) : 0;
                                                mysql_query("UPDATE joueur SET pv_reste = '".$perso['pv_reste']."' WHERE pseudo = '".$perso['pseudo']."'") or die("Erreur1");
                                                mysql_query("UPDATE joueur SET nombre_reste = '".($_SESSION['infos']['nombre_reste']-1)."' WHERE pseudo = '".$_SESSION['infos']['pseudo']."'") or die("Erreur2");
                                                mysql_query("INSERT INTO magie (id_perso,debut,magie,fin) VALUES ('".$_SESSION['infos']['id']."','".$_SESSION['infos']['date_tour']."','foudre','".($_SESSION['infos']['date_tour']+$temps_tour)."')") or die("Erreur3");
                                                $resultat = "".$perso['pseudo']." a perdu 10 points de vie<br>
                                                PS: ce sort utilise une attaque";
                                                ajout_info($_SESSION['infos']['pseudo'],"il a lance le sort foudre sur ".$perso['pseudo']."");
                                                ajout_info($perso['pseudo'],"a ete touche par le sort foudre de ".$_SESSION['infos']['pseudo']."");

                                        }
                                }
                        break;
                }
        break;
        case 2:
        	switch($_SESSION['infos']['classe'])
                {
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                }
        break;
        case 3:
        	switch($_SESSION['infos']['classe'])
                {
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                }
        break;
        case 4:
        	switch($_SESSION['infos']['classe'])
                {
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                }
        break;
        case 5:
        	switch($_SESSION['infos']['classe'])
                {
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                }
        break;
        case 6:
        	switch($_SESSION['infos']['classe'])
                {
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                	case 'M': break;
                }
        break;
}


if($perso['pv_reste'] == 0)
{
        ajouter_mort($_SESSION['infos']['id'],$perso['id']);
        ajout_info($_SESSION['infos']['pseudo'],"Il a tué ".$perso['pseudo']."");
        ajout_info($perso['pseudo'],"Il a été tué par ".$_SESSION['infos']['pseudo']."");

        $gagne_xp = $_SESSION['infos']['xp']+($perso['xp']/10);

        echo("<br><br><b>Vous avez TUE: ".$perso['pseudo']."</b><br><br>");

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
echo("<br>
".$resultat."<br><br><a href='general.php'>Retour à la page principale</a>");
?>
