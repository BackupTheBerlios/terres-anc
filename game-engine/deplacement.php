<?php
/*
Engine of ``Terres-Anciennes", a web-based multiplayer RPG.
Copyright 2004, 2005 Nicolas Hess / Choplair-network / Nova Devs.
#$Id: deplacement.php,v 1.3 2005/02/09 20:38:04 pachilor Exp $

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

if(!$_SESSION['infos']['pseudo'])
{
	echo("Vous n'êtes pas identifié");
        exit;
}

if( $_SESSION['infos']['mouvement_reste'] <= 0)
{
        echo("<script>
        alert(\"Vous n'avez plus de déplacement possible\");
        document.location.href='general.php'
        </script>");
        exit;
}

/* CONNECTION A LA BASE DE DONNEE */

define('terres_anciennes',true);
include 'common-inc/config.inc.php';

$link = @mysql_connect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

/* LES DIFFERENTS CAS DE MOUVEMENT */

if( isset($_POST['where']) )
{
	switch($_POST['where'])
        {
        	case 1: $_SESSION['infos']['x'] -= 1; $_SESSION['infos']['y'] += 1; break;
                case 2: $_SESSION['infos']['y'] += 1; break;
                case 3: $_SESSION['infos']['x'] += 1; $_SESSION['infos']['y'] += 1; break;
                case 4: $_SESSION['infos']['x'] -= 1; break;
                case 6: $_SESSION['infos']['x'] += 1; break;
                case 7: $_SESSION['infos']['x'] -= 1; $_SESSION['infos']['y'] -= 1; break;
                case 8: $_SESSION['infos']['y'] -= 1; break;
                case 9: $_SESSION['infos']['x'] += 1; $_SESSION['infos']['y'] -= 1; break;
                default:
        }
        $_SESSION['infos']['mouvement_reste'] -= 1;
}
else
{ echo("<script>
alert(\"Il semble que vous n'avez sélectionné aucune direction...\");
document.location.href='general.php'
</script>");
exit; }

$sql = mysql_query("SELECT id,nom,x,y
FROM forteresse
WHERE x >= ".$_SESSION['infos']['x']."
AND x <= ".($_SESSION['infos']['x']+2)."
AND y <= ".$_SESSION['infos']['y']."
AND y >= ".($_SESSION['infos']['y']-2)."
LIMIT 1") or die("Erreur lors de la vérification des forteresses");

if(mysql_num_rows($sql) == 0)
{
	$filename = "./info/info/".md5($_SESSION['infos']['pseudo']).".inc";

  if( !file_exists($filename))
  { $new = fopen($filename,"w");
    fputs($new,"<?php
if ( !defined('terres_anciennes') )
{ die('Hacking attempt');
  exit; }
?>\n\n");
    fclose($new);
  }
  $myfile = @fopen($filename,"a");
  fputs($myfile,"".time()."/Il a bougé\n");
  fclose($myfile);
}
else
{
	$f = mysql_fetch_array($sql,MYSQL_ASSOC);
        $_SESSION['infos']['place'] = $f['id'];
        $_SESSION['infos']['x']     = $f['x']+1;
        $_SESSION['infos']['y']     = $f['y']-1;

        $filename = "./info/info/".md5($_SESSION['infos']['pseudo']).".inc";
        $myfile = @fopen($filename,"a");
        fputs($myfile,"".time()."/Il est entré dans: ".$f['nom']."\n");
        fclose($myfile);
}

$deplacement = "UPDATE joueur
SET x = '".$_SESSION['infos']['x']."',
    y = '".$_SESSION['infos']['y']."',
    mouvement_reste = '".$_SESSION['infos']['mouvement_reste']."',
    place = '".$_SESSION['infos']['place']."'
WHERE pseudo = '".$_SESSION['infos']['pseudo']."'";

if(mysql_query($deplacement))
{ Header("location: ./general.php"); }
else
{ echo("Echec de la requete"); exit; }
?>
