<?php
session_start();

/*********************************************************************************


                                 PARTIE DE CONNECTION


/********************************************************************************/

define('terres_anciennes',true);
include 'common-inc/config.inc.php';
include 'common-inc/fonctions.php';

$link = @mysql_pconnect($dbhost,$dbname,$dbpass);
@mysql_select_db($dbbase,$link);

if($_SESSION['oui'] != 'yes')
{
	die("Erreur n°1");
}
if( isset($HTTP_GET_VARS['p']))
{
        if( isset($HTTP_GET_VARS['p']))
	{ $p = $HTTP_GET_VARS['p']; }

        $back = base64_decode(base64_decode(strrev($p)));

        $hack = @explode('/',$back) or die('Erreur n°2: L\'url ne comporte pas de schlashs, veuillez vous reconnecter...');
        
        $get_id = $hack[1];
        $get_pa = $hack[0];

        $sql = @mysql_query("SELECT pseudo,id_account FROM joueur WHERE id = '$get_id' LIMIT 1");
        $t = @mysql_fetch_array($sql,MYSQL_ASSOC);
        $sql_1 = @mysql_query("SELECT passe FROM compte WHERE id_account = '".$t['id_account']."' LIMIT 1");
        $u = @mysql_fetch_array($sql_1,MYSQL_ASSOC);

        if( $u['passe'] == $get_pa)
        {
                $id = $get_id;
                $pseudo = $t['pseudo'];
                header("location: ./general.php");
        }
        else
        {
                die("Erreur n°3");
        }
}
else if( !isset($HTTP_GET_VARS['p']) && isset($_SESSION['infos']['pseudo']))
{
	$pseudo = $_SESSION['infos']['pseudo'];
        $id     = $_SESSION['infos']['id'];
}
else
{
	header("Erreur n°4");
}

/*********************************************************************************


                            RECUPERATION DES DONNEES


/********************************************************************************/

$informations = @mysql_query("SELECT *
FROM joueur
WHERE id='$id' AND pseudo='$pseudo'
LIMIT 1") or die("Erreur pour selectionner votre personnage");
$_SESSION['infos'] = mysql_fetch_array($informations,MYSQL_ASSOC);

$infor_persos = mysql_query("SELECT *
FROM joueur
WHERE x BETWEEN ".($_SESSION['infos']['x']-$_SESSION['infos']['vision'])." AND ".($_SESSION['infos']['x']+$_SESSION['infos']['vision'])." 
AND y BETWEEN ".($_SESSION['infos']['y']-$_SESSION['infos']['vision'])." AND ".($_SESSION['infos']['y']+$_SESSION['infos']['vision'])."
AND place = '0'
ORDER BY x ASC, y DESC");
if( mysql_num_rows($infor_persos) > 0)
{ for($i = 0; $i < mysql_num_rows($infor_persos); $i++ )
  { $perso[] = mysql_fetch_array($infor_persos,MYSQL_ASSOC); } } else $perso = 'null';

$infor_forter = mysql_query("SELECT *
FROM forteresse
WHERE x BETWEEN ".($_SESSION['infos']['x']-$_SESSION['infos']['vision']-5)." AND ".($_SESSION['infos']['x']+$_SESSION['infos']['vision']+5)."
AND y BETWEEN ".($_SESSION['infos']['y']-$_SESSION['infos']['vision']-5)." AND ".($_SESSION['infos']['y']+$_SESSION['infos']['vision']+5)."
ORDER BY x ASC, y DESC");
if( mysql_num_rows($infor_forter) > 0)
{ for($i = 0; $i < mysql_num_rows($infor_forter); $i++ )
  { $forteresse[] = mysql_fetch_array($infor_forter,MYSQL_ASSOC); } } else $forteresse = 'null';

$query_n_mess = mysql_query("SELECT destinataire FROM messagerie WHERE lu='0' AND destinataire='".$_SESSION['infos']['pseudo']."'");
$nombre_nouveaux_messages = mysql_num_rows($query_n_mess);

/*********************************************************************************


                            FONCTION DE TOUR DE JEU


/********************************************************************************/

$time       = time();    /* CALCUL DU NOMBRE DE TOUR MANQUANTS */

if($time >= $_SESSION['infos']['date_tour'])
{
        $tours_manquants  = round(($time-$_SESSION['infos']['date_tour'])/$temps_tour)+1;
        $next_mise_a_jour = $_SESSION['infos']['date_tour']+($temps_tour*$tours_manquants);

        $affiche = "<font style=\"color:red;\"><b>Nouveau tour ($tours_manquants)</b></font>";

        // ARRET DE LA MAGIE
        
        
        // SI MALUS



        // NORMAL

        $mise_a_jour_mouvement_reste = $_SESSION['infos']['mouvement'];
        $mise_a_jour_nombre_reste    = $_SESSION['infos']['nombre'];
        $mise_a_jour_argent          = $_SESSION['infos']['argent']+10;
        $mise_a_jour_bois            = $_SESSION['infos']['bois']+5;
        $mise_a_jour_pv_reste        = (($_SESSION['infos']['pv_reste']+$_SESSION['infos']['pv_regen']) >= $_SESSION['infos']['pv']) ? $_SESSION['infos']['pv']: ($_SESSION['infos']['pv_reste']+$_SESSION['infos']['pv_regen']);
        $mise_a_jour_magie_reste     = (($_SESSION['infos']['magie_reste']+$_SESSION['infos']['magie_regen']) >= $_SESSION['infos']['magie']) ? $_SESSION['infos']['magie']: ($_SESSION['infos']['magie_reste']+$_SESSION['infos']['magie_regen']);

mysql_query("UPDATE joueur
SET date_tour       = '".$next_mise_a_jour."',
    mouvement_reste = '".$mise_a_jour_mouvement_reste."',
    nombre_reste    = '".$mise_a_jour_nombre_reste."',
    argent          = '".$mise_a_jour_argent."',
    bois            = '".$mise_a_jour_bois."',
    pv_reste        = '".$mise_a_jour_pv_reste."',
    magie_reste     = '".$mise_a_jour_magie_reste."'
WHERE pseudo = '".$_SESSION['infos']['pseudo']."' AND id= '".$_SESSION['infos']['id']."'")
or die("Erreur lors du changement de tour");

        $_SESSION['infos']['date_tour']       = $next_mise_a_jour;
        $_SESSION['infos']['mouvement_reste'] = $mise_a_jour_mouvement_reste;
        $_SESSION['infos']['nombre_reste']    = $mise_a_jour_nombre_reste;
        $_SESSION['infos']['argent']          = $mise_a_jour_argent;
        $_SESSION['infos']['bois']            = $mise_a_jour_bois;
        $_SESSION['infos']['pv_reste']        = $mise_a_jour_pv_reste;
        $_SESSION['infos']['magie_reste']        = $mise_a_jour_magie_reste;
}
else
{
        $nombre_de_tour_manquants=0;
        $affiche = "Date: ".date("d/m/Y")." &nbsp; et&nbsp; il est ".date("H:i:s")."";
}

/*********************************************************************************


                                    MAGIE


/********************************************************************************/

// niveau 1 fantassins


/*********************************************************************************


                               AUTRES FONCTIONS


/********************************************************************************/

function pv($pv_reste,$pv)
{
	$limite = (0.1*$pv);
        if($pv_reste < $limite)
        {
        	$return = '<font color=\"#FF0000\">'.$pv_reste.'</font> sur '.$pv.'';
        }
        else
        {
        	$return = ''.$pv_reste.' sur '.$pv.'';
        }
        return $return;
}

function barre_xp($xp,$niveau,$longeur_barre_xp=200)
{
	if($_SESSION['infos']['niveau'] == 1)
        { $next = 1000; $last = 1; }
        else if($_SESSION['infos']['niveau'] == 2)
        { $next = 2000; $last = 1000; }
        else if($_SESSION['infos']['niveau'] == 3)
        { $next = 3000; $last = 2000; }
        else if($_SESSION['infos']['niveau'] == 4)
        { $next = 5000; $last = 3000; }
        else if($_SESSION['infos']['niveau'] == 5)
        { $next = 7500; $last = 5000; }
        else if($_SESSION['infos']['niveau'] == 6)
        { $next = 10000; $last = 7500; }
        else if($_SESSION['infos']['niveau'] == 7)
        { $next = 20000; $last = 10000; }
        else if($_SESSION['infos']['niveau'] == 8)
        { $next = 10000; $last = 7500; }

        $longueur_xp = round(($xp-$last)*($longeur_barre_xp)/($next-$last));
        $longueur_reste = ($longeur_barre_xp)-$longueur_xp;

        $truc = "<b>Niveau: ".$niveau."</b>&nbsp; &nbsp; &nbsp;
        <img src=\"./images/icon_xp.jpg\" height=\"10\" width='".$longueur_xp."' border=\"0\" alt=\"complet\" /><img src='./images/icon_reste.jpg' height='10' width='".$longueur_reste."' border=\"0\" alt=\"vide\" />&nbsp;&nbsp;&nbsp;&nbsp;
        <b>".($niveau+1)."</b>";

  return $truc;
}

/*********************************************************************************


                                PARTIE D'AFFICHAGE    METTRE LE JAVA POUR PREVENIR DU SORT


/********************************************************************************/

$titre   = "TERRES - Page de jeu";

/* CARACTERISTIQUES DU PERSONNAGE */

echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
  <title> $titre </title>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">
  <meta http-equiv=\"Content-Language\" content=\"fr\">
  <meta name=\"Title\" lang=\"fr\" content=\"Terres anciennes\">
  <link rel=\"stylesheet\" href=\"style_general.css\">
  <script type=\"text/javascript\" language=\"javascript\">
  <!--
  function ChangeMessage(message,champ)
  {
  	  if(document.getElementById)
          document.getElementById(champ).innerHTML = message;
  }
  function changeCouleur(cellule,couleurFond,couleurPolice)
  {
  	  cellule.style.backgroundColor = couleurFond;
          cellule.style.color=couleurPolice;
  }
  //-->
  </script>
</head>

<body text=\"#FFFFFF\" link=\"#FFFFFF\" alink=\"#FFFFFF\" vlink=\"#FFFFFF\">");

echo("

<table border=\"0\" style=\"margin: 5px; padding: 3px;\" width=\"100%\">
<tr>
  <td valign=\"top\" width=\"30%\">

    <table border=\"1\" width=\"360\" align=\"left\" cellpadding=\"3\" bgcolor=\"#000000\">
    <tr align=\"center\">
      <td colspan=\"2\">
        Personnage: <b> ".$_SESSION['infos']['pseudo']."</b> &nbsp; (<b>".$_SESSION['infos']['sexe']."</b>)
      </td>
    </tr>
    <tr align=\"center\">
      <td width=150>Race: <b>".race($_SESSION['infos']['race'])."</b></td>
      <td width=150>Classe: <b>".classe($_SESSION['infos']['classe'],$_SESSION['infos']['niveau'])." </b></td>
    </tr>
    <tr align=\"center\">
      <td>Pi&egrave;ces d'or: <b>".$_SESSION['infos']['argent']."</b> &nbsp; &nbsp; &nbsp; <img src=\"./images/or.jpg\" border=\"0\" alt=\"or\" /></td>
      <td>Bois: <b>".$_SESSION['infos']['bois']."</b>  &nbsp; &nbsp;<img src=\"./images/bois.gif\" border=\"0\" alt=\"bois\" /></td>
    </tr>
    <tr align=\"center\">
      <td colspan=2>".$affiche."</td>
    </tr>
    <tr align=\"center\">
      <td colspan=2>Votre prochain tour de jeu: le <b>".date("d/m/Y à H:i:s",$_SESSION['infos']['date_tour'])."</b></td>
    </tr>
    <tr align=\"center\">
      <td colspan=2>
      ".barre_xp($_SESSION['infos']['xp'],$_SESSION['infos']['niveau'])."
      </td>
    </tr>
    <tr align=\"center\">
      <td width=150>PV: <b>".pv($_SESSION['infos']['pv_reste'],$_SESSION['infos']['pv'])."</b></td>
      <td width=150>Magie: <b>".$_SESSION['infos']['magie_reste']." sur ".$_SESSION['infos']['magie']."</b></td>
    </tr>
    <tr align=\"center\">
      <td width=150>Régén.PV: <b>".$_SESSION['infos']['pv_regen']."</b></td>
      <td width=150>Régén.Mana: <b>".$_SESSION['infos']['magie_regen']."</b></td>
    </tr>\n");

if(!($_SESSION['infos']['classe'] == 'A'))
{
	print("    <tr align=\"center\">
      <td width=150>Hablilté: <b>".$_SESSION['infos']['habilete_cac']."</b></td>
      <td width=150>Force:  <b>".$_SESSION['infos']['force_cac']."</b></td>
    </tr>\n");
}

if(!($_SESSION['infos']['classe'] == 'G' || $_SESSION['infos']['classe'] == 'B' || $_SESSION['infos']['classe'] == 'M'))
{
	print("    <tr align=\"center\">
      <td width=150>Précision: <b>".$_SESSION['infos']['habilete_dis']."</b></td>
      <td width=150>Force: <b>".$_SESSION['infos']['force_dis']."</b></td>
    </tr>
    <tr align=\"center\">
      <td colspan=2>Portée: <b>".$_SESSION['infos']['portee']."</b></td>
    </tr>\n");
}
print("    <tr align=\"center\">
      <td width=150>Esquive: <b>".$_SESSION['infos']['esquive']."</b></td>
      <td> &nbsp; </td>
    </tr>
    <tr align=\"center\">
      <td colspan=2>Mouvements: <b>".$_SESSION['infos']['mouvement_reste']."  / ".$_SESSION['infos']['mouvement']."</b></td>
    </tr>
    <tr align=\"center\">
      <td colspan=2>Nombres d'attaques: <b>".$_SESSION['infos']['nombre_reste']." /".$_SESSION['infos']['nombre']."</b></td>
    </tr>
    <tr align=\"center\">
      <td colspan=2>Statut: <b>".$_SESSION['infos']['statut']."</b></td>
    </tr>
    </table>
  
  </td>");
  
/*********************************************************************************


                                       LIENS


/********************************************************************************/

if($nombre_nouveaux_messages > 0)
{ $message = "<a href='./messagerie/messagerie.php'><font class='interdit'>MESSAGERIE ($nombre_nouveaux_messages New)</font></a>\n"; }
else
{ $message = "<a href='./messagerie/messagerie.php'><b>MESSAGERIE</b></a>\n"; }

  echo("
  <td width=\"70%\" align=\"center\">
    $message<br><br>

    <a href=\"./configuration/panneau_joueur.php\">Panneau personnel => gestion de votre personnage: ".$_SESSION['infos']['pseudo']."</a><br>
    <a href=\"#\" onClick=\"window.open('./carte/carte.php','_blank','toolbar=0,location=0,directories=0,status=0,scrollbars=0,resizable=0,copyhistory=0,menuBar=0,width=820,height=620')\">Afficher la carte du monde</a><br>
    <noscript>
    <a href=\"./carte/carte.php\">AFFICHER LA CARTE SANS JAVASCRIPT</a><br>
    </noscript>
    <a href=\"./objets/index_objets.php\">Votre inventaire</a><br>
    <a href='info/info.php?info=".$_SESSION['infos']['id']."' target='_blank'>Vos infos</a><br><br>

    <a href=\"../forum/\" target=\"_blank\"><b>FORUMS</b></a><br><br>

    <a href=\"./persos.php\"><b>Retour</b></a><br>
    <a href=\"./connexion/deconnexion.php\"><b>D&eacute;connection</b></a><br>
  </td>
</tr>
</table><p><br>\n\n");

/*********************************************************************************


                                     DEUXIEME RANGEE


/********************************************************************************/

$y = $_SESSION['infos']['y']+$_SESSION['infos']['vision']+1;

print("<table border=\"0\" style=\"margin: 5px; padding: 3px;\" width=\"100%\">
<tr>
  <td width=\"60%\" valign=\"top\">

    <table border=\"1\" align=\"center\" bgcolor=\"#806AF9\" valign=\"middle\">
    <tr align=\"center\" bgcolor=\"#EEEEEE\">
      <th height=\"15\" width=\"30\">&nbsp;</th>\n");


for($x = $_SESSION['infos']['x']-$_SESSION['infos']['vision']; $x <= $_SESSION['infos']['x']+$_SESSION['infos']['vision']; $x++)
{
	if($x == $_SESSION['infos']['x'])
        {
        	print("      <th height=\"5\" width=\"30\"><font class=\"interdit\">x:$x</font></th>\n");
        }
        else
        {
        	print("      <th height=\"15\" width=\"30\"><font color=\"black\">x:$x</font></th>\n");
        }
}
print("      <th height=\"15\" width=\"30\">&nbsp;</th>
    </tr>\n");

if($forteresse == 'null')
{ $max = count($perso)-1; }
if($perso == 'null')
{ $max = count($forteresse)-1; }
else
{ $max = max(count($perso)-1,count($forteresse)-1); }


/* CHAMP DE VISION */

for($lignes = $_SESSION['infos']['y']-$_SESSION['infos']['vision']; $lignes <= $_SESSION['infos']['y']+$_SESSION['infos']['vision']; $lignes++)
{
	
        $y -= 1;
        if($y == $_SESSION['infos']['y'])
        { print("  <tr align=\"center\"><th width=35 height=35 bgcolor=\"#EEEEEE\"><font class=\"interdit\">y:$y</font></th>\n"); }
        else
        { print("  <tr align=\"center\"><th width=35 height=35 bgcolor=\"#EEEEEE\"><font color=\"black\">y:$y</font></th>\n"); }

        
        for($x = $_SESSION['infos']['x']-$_SESSION['infos']['vision']; $x <= $_SESSION['infos']['x']+$_SESSION['infos']['vision']; $x++)
        {
        	
                if($y >= 75 || $y <= -75 || $x >= 100 || $x <= -100)
                { print("    <td width=35 height=35 bgcolor='#000000'><img src=\"./images/limittes.gif\" border=0 alt=\"Limittes\" /></td>\n"); break; }
                
                
                for($k = 0; $k <= $max; $k++)
                {
                        if($x >= $forteresse[$k]['x'] && $x <= $forteresse[$k]['x']+2 && $y <= $forteresse[$k]['y'] && $y >= $forteresse[$k]['y']-2 && ($x != 0 && $y != 0))
                        {
                        	switch($forteresse[$k]['capitale'])
                                {
                                	case 'Y': print("    <td width=35 height=35 bgcolor=\"#0000FF\" onMouseOver=\"ChangeMessage('<center><b>Nom:</b> ".addslashes($forteresse[$k]['nom'])."<br><b>CAPITALE</b>','ejs_texte');\" onMouseOut=\"ChangeMessage('','ejs_texte');\"><a href='info/info_forteresse.php?id=".$forteresse[$k]['id']."' target='_blank'>".trim(strtoupper(substr($forteresse[$k]['race'],0,1)))."</a></td>\n"); break;
                                        case 'N': print("    <td width=35 height=35 bgcolor=\"#0000FF\" onMouseOver=\"ChangeMessage('<center><b>Nom:</b> ".addslashes($forteresse[$k]['nom'])."','ejs_texte');\" onMouseOut=\"ChangeMessage('','ejs_texte');\"><a href='info/info_forteresse.php?id=".$forteresse[$k]['id']."' target='_blank'>".trim(strtolower(substr($forteresse[$k]['race'],0,1)))."</a></td>\n"); break;
                                }
                                break;
                        }
                        else if( isset($perso[$k]))
                        {
                        	if($perso[$k]['x'] == $x && $perso[$k]['y'] == $y)
                                {
                                	switch($perso[$k]['race'])
                                        {
                                        	case 'C': print("    <td width=\"35\" height=\"35\" bgcolor=\"#FF5E5E\"><a href='info/info.php?info=".$perso[$k]['id']."' onMouseOver=\"ChangeMessage('<center><b>Nom:</b> ".$perso[$k]['pseudo']."<br>&nbsp;&nbsp;&nbsp; <b>Message:</b> ".$perso[$k]['message_du_jour']."','ejs_texte');\" onMouseOut=\"ChangeMessage(' ','ejs_texte');\" target='_blank' title='".$perso[$k]['pseudo']."'><img src='./images/avatars/".$perso[$k]['avatar']."' border=0 alt=\"".$perso[$k]['pseudo']."\" /></a></td>\n");
                                                          break;
                                                case 'A': print("    <td width=\"35\" height=\"35\" bgcolor=\"#6464FF\"><a href='info/info.php?info=".$perso[$k]['id']."' onMouseOver=\"ChangeMessage('<center><b>Nom:</b> ".$perso[$k]['pseudo']."<br>&nbsp;&nbsp;&nbsp; <b>Message:</b> ".$perso[$k]['message_du_jour']."','ejs_texte');\" onMouseOut=\"ChangeMessage(' ','ejs_texte');\" target='_blank' title='".$perso[$k]['pseudo']."'><img src='./images/avatars/".$perso[$k]['avatar']."' border=0 alt=\"".$perso[$k]['pseudo']."\" /></a></td>\n");
                                                          break;
                                                case 'N': print("    <td width=\"35\" height=\"35\" bgcolor=\"#C0C0C0\"><a href='info/info.php?info=".$perso[$k]['id']."' onMouseOver=\"ChangeMessage('<center><b>Nom:</b> ".$perso[$k]['pseudo']."<br>&nbsp;&nbsp;&nbsp; <b>Message:</b> ".$perso[$k]['message_du_jour']."','ejs_texte');\" onMouseOut=\"ChangeMessage(' ','ejs_texte');\" target='_blank' title='".$perso[$k]['pseudo']."'><img src='./images/avatars/".$perso[$k]['avatar']."' border=0 alt=\"".$perso[$k]['pseudo']."\" /></a></td>\n");
                                                          break;
                                                case 'E': print("    <td width=\"35\" height=\"35\" bgcolor=\"#B3F96C\"><a href='info/info.php?info=".$perso[$k]['id']."' onMouseOver=\"ChangeMessage('<center><b>Nom:</b> ".$perso[$k]['pseudo']."<br>&nbsp;&nbsp;&nbsp; <b>Message:</b> ".$perso[$k]['message_du_jour']."','ejs_texte');\" onMouseOut=\"ChangeMessage(' ','ejs_texte');\" target='_blank' title='".$perso[$k]['pseudo']."'><img src='./images/avatars/".$perso[$k]['avatar']."' border=0 alt=\"".$perso[$k]['pseudo']."\" /></a></td>\n");
                                                          break;
                                                case 'H': print("    <td width=\"35\" height=\"35\" bgcolor=\"#D069FC\"><a href='info/info.php?info=".$perso[$k]['id']."' onMouseOver=\"ChangeMessage('<center><b>Nom:</b> ".$perso[$k]['pseudo']."<br>&nbsp;&nbsp;&nbsp; <b>Message:</b> ".$perso[$k]['message_du_jour']."','ejs_texte');\" onMouseOut=\"ChangeMessage(' ','ejs_texte');\" target='_blank' title='".$perso[$k]['pseudo']."'><img src='./images/avatars/".$perso[$k]['avatar']."' border=0 alt=\"".$perso[$k]['pseudo']."\" /></a></td>\n");
                                                          break;
                                                case 'M': print("    <td width=\"35\" height=\"35\" bgcolor=\"#D069FC\"><a href='info/info.php?info=".$perso[$k]['id']."' onMouseOver=\"ChangeMessage('<center><b>Nom:</b> ".$perso[$k]['pseudo']."<br>&nbsp;&nbsp;&nbsp; <b>Message:</b> ".$perso[$k]['message_du_jour']."','ejs_texte');\" onMouseOut=\"ChangeMessage(' ','ejs_texte');\" target='_blank' title='".$perso[$k]['pseudo']."'><img src='./images/avatars/".$perso[$k]['avatar']."' border=0 alt=\"".$perso[$k]['pseudo']."\" /></a></td>\n");
                                                          break;
                                        }
                                        break;
                                }
                        }
                        else if($x == 0 && $y == 0)
                        {
                        	print("    <td width=35 height=35 bgcolor='#000000'>-</td>\n");
                                break;
                        }
                        if($k == $max)
                        {
                        	print("    <td width=35 height=35 bgcolor='#000000'>-</td>\n");
                                break;
                        }
                }
        }
        echo("    <th width=35 height=35 bgcolor=\"#EEEEEE\"><font color=\"black\">y:$y</font></th>
  </tr>\n");
}

echo("  <tr align=\"center\" bgcolor=\"#EEEEEE\"><th height=15 width=30>&nbsp;</th>\n");

for($x = $_SESSION['infos']['x']-$_SESSION['infos']['vision']; $x <= $_SESSION['infos']['x']+$_SESSION['infos']['vision']; $x++)
{
	echo("    <th height=15 width=30><font color=\"black\">x:$x</font></th>\n");
}

echo("    <th height=15 width=30></th>
    </tr>
    </table>
  </td>
  <td width=\"390\" valign=\"top\" align=\"center\">\n");

/* TABLEAU ONMOUSE, AFFICHAGE DES PETITES CARAC DES PERSO */

echo("  <!-- ## TABLEAU ONMOUSE, AFFICHAGE DES PETITES CARAC DES PERSO ## -->

    <b>Informations:</b><br><br>
    <table width=\"310\" border=\"1\" align=\"center\" bgcolor=\"#000000\">
    <tr>
      <td width=\"100%\" height=\"80\">
        <div id=ejs_texte></div>
      </td>
    </tr>
    </table>
    <br>
    <b>Tableau d'attaque:</b><br><br>\n\n");

/* TABLEAU D'ATTAQUES */

if($_SESSION['infos']['nombre_reste'] <= 0)
{ print("  <table border=1 width=\"300\" align=\"center\" bgcolor=\"#000000\">
  <tr align=\"center\">
    <td height=\"90\">Vous ne pouvez plus <br> attaquer pendant ce tour.</td>
  </tr>
  </table>\n");
}
else
{ print("  <table border=1 width=\"300\" align=\"center\" bgcolor=\"#000000\">
  <tr>
    <td>
      <table border=0 align=\"center\">
      <tr align=\"center\">\n");

        if(!($_SESSION['infos']['classe'] == 'A'))
        {
        	print("        <td width=150>
          Corps à corps:<br><br>
          <form method=\"post\" action='./combat.php'>
            <select name=\"attaque\" size=1>\n");

                for($i=0;$i<count($perso);$i++)
                {
                        if($perso[$i]['x'] >= ($_SESSION['infos']['x']-1) && $perso[$i]['x'] <= ($_SESSION['infos']['x']+1) && $perso[$i]['y'] >= ($_SESSION['infos']['y']-1) && $perso[$i]['y'] <= ($_SESSION['infos']['y']+1))
                	{
                		if($perso[$i]['pseudo'] != $_SESSION['infos']['pseudo'])
                		{
                			print("<option value='".$perso[$i]['id']."'>".$perso[$i]['pseudo']."</option>\n"); $j++;
                                }
                        }
                }

                print("            </select>
            <input style='width: 90px; cursor: pointer;' value='Corps à corps' name='corps' type='submit' class='submit' />
          </form>
        </td>");
        }

        if(!($_SESSION['infos']['classe'] == 'G' || $_SESSION['infos']['classe'] == 'B' || $_SESSION['infos']['classe'] == 'M'))
        { print("        <td width=150>
          Distance:<br><br>
          <form method='post' action='./combatdis.php' height=20>
            <select name='attaquedistance' size=1>");

                for($i=0;$i<count($perso);$i++)
                {
                        if($perso[$i]['x'] >= ($_SESSION['infos']['x']-$_SESSION['infos']['portee']-1) && $perso[$i]['x'] <= ($_SESSION['infos']['x']+$_SESSION['infos']['portee']+1) && $perso[$i]['y'] >= ($_SESSION['infos']['y']-$_SESSION['infos']['portee']-1) && $perso[$i]['y'] <= ($_SESSION['infos']['y']+$_SESSION['infos']['portee']+1))
                	{
                		if($perso[$i]['pseudo'] != $_SESSION['infos']['pseudo'])
                		{
                			print("<option value='".$perso[$i]['id']."'>".$perso[$i]['pseudo']."</option>\n"); $j++;
                                }
                        }
                }
                
                print("            </select><br>
            <input style='width: 90px; cursor: pointer;' value='A distance' name='distance' type='submit' class='submit'>
          </form>
        </td>");
}
        print("
        <td width=150>
          <form method='post' action='./magie.php'>
            <select name='sortilege' size=1 style='width: 100px; color: black;'>\n");

        switch($_SESSION['infos']['classe'])
        {
        	case 'B':
                        switch($_SESSION['infos']['niveau'])
        	        {
                                case 4: echo "              <option style='color: black;' value='mort-heroique'>Mort Héroïque</option>\n";
                                case 3: echo "              <option style='color: black;' value='puissance'>Puissance</option>\n";
                                case 2: echo "              <option style='color: black;' value='rage'>Rage</option>\n";
        	     	        case 1: echo "              <option style='color: black;' value='puissancec'>Puissance</option>\n";
        	     	        break;
                        }
        	        break;
        	case 'M':
        	        switch($_SESSION['infos']['niveau'])
        	        {
        	     	        case 4: echo "              <option style='color: black;' value='poing'>Poing des Dieux</option>\n";
                                case 3: echo "              <option style='color: black;' value='ab'>Absordsion de la terre</option>\n";
                                        echo "              <option style='color: black;' value='sacrifice'>Sacrifice</option>\n";
        	     	        case 2: echo "              <option style='color: black;' value='foudreserie'>Foudre en série</option>\n";
                                        echo "              <option style='color: black;' value='armuredeglace'>Armure de glace</option>\n";
        	     	        case 1: echo "              <option style='color: black;' value='soin'>Soin</option>\n";
                                        echo "              <option style='color: black;' value='foudre'>Foudre</option>\n";
                                break;
                        }
        	        break;
        	case 'A':
        	        switch($_SESSION['infos']['niveau'])
        	        {
                                case 4: echo "              <option style='color: black;' value='toto'>Poing des Dieux</option>";
                                case 3: echo "              <option style='color: black;' value='toto'>Absordsion de la terre</option>";
        	     	        case 2: echo "              <option style='color: black;' value='armure-de-glace'>Archer Exp&eacute;riment&eacute;</option>";
                                case 1: echo "              <option style='color: black;' value='oeil'>Oeil de Faucon</option>";
                                break;
                        }
                        break;
        	case 'G':
        	        switch($_SESSION['infos']['niveau'])
        	        {
                                case 4: echo "              <option style='color: black;' value='frenesie'>Frénesie</option>\n";
                                case 3: echo "              <option style='color: black;' value='epee-enflammee'>Epée-enflammée</option>\n";
                                case 2: echo "              <option style='color: black;' value='defense'>Défense</option>\n";
        	     	        case 1: echo "              <option style='color: black;' value='fine-lame'>Fine-lame</option>\n";
        	     	        break;
                        }
                        break;
                default: echo "aucune";
        }
        
        print("            </select><br>
            Sur:<br>
            <select name='attaquesort' size=1>\n");

                if(count($perso) <= 0)
                { echo("<option>Personne</option>"); }
                else
                { $i=0;
                  while($perso[$i]['x'] >= $_SESSION['infos']['x']-$_SESSION['infos']['vision']-1 && $perso[$i]['x'] <= $_SESSION['infos']['x']+$_SESSION['infos']['vision']+1 && $perso[$i]['y'] >= $_SESSION['infos']['y']-$_SESSION['infos']['vision']-1 && $perso[$i]['y'] <= $_SESSION['infos']['y']+$_SESSION['infos']['vision']+1)
                  { print("<option value='".$perso[$i]['id']."'>".$perso[$i]['pseudo']."</option>\n"); $i++; }
                }

        print("            </select><br><br>
            <input style=\"height: 20px; width: 95px; cursor: pointer;\" class=\"submit\" value='Sortilège' type='submit'>
          </form>
        </td>
      </tr>
      </table>
    </td>
  </tr>
  </table>\n\n");
}

/* TABLEAU DE DEPLACEMENT DU PERSONNAGE */

print("<br>
  <b>Tableau de d&eacute;placement:</b><br><br>");
  
if($_SESSION['infos']['mouvement_reste'] > 0)
{ print("

    <!-- ## TABLEAU DE DEPLACEMENT DU PERSONNAGE ## -->

    <form method=\"post\" action=\"./deplacement.php\" name=\"deplacement\">
    <table border=1 width=\"300\" align=\"center\" bgcolor=\"#000000\">\n");

  if($_SESSION['infos']['place'] != 0)
  { print("    <tr align=\"center\">
      <td height=\"90\"><a href='sortir.php'><font color=\"white\">Sortir <br>de la citadelle</font></a></td>
    </tr>\n");
  }
  else
  { print("    <tr align=\"center\">
      <td width=100 height=\"30\"><input type='radio' name='where' value='1' /></td>
      <td width=100><input type='radio' name='where' value='2' /></td>
      <td width=100><input type='radio' name='where' value='3' /></td>
    </tr>
    <tr align=\"center\">
      <td width=100 height=\"30\"><input type='radio' name='where' value='4' /></td>
      <td width=100><input type='submit' name='Bouger' value='se deplacer' style=\"height: 20px; width: 95px; cursor: pointer;\" class=\"submit\"></td>
      <td width=100><input type='radio' name='where' value='6' /></td>
    </tr>
    <tr align=\"center\">
      <td width=100 height=\"30\"><input type='radio' name='where' value='7' /></td>
      <td width=100><input type='radio' name='where' value='8' /></td>
      <td width=100><input type='radio' name='where' value='9' /></td>
    </tr>\n");
  }
  print("    </table>
    </form>\n\n");
}
else
{ print("    <br>

    <!-- ## TABLEAU DE DEPLACEMENT DU PERSONNAGE ## -->

    <table align='center' border=1 width=300 height=90 bgcolor=\"#000000\">
    <tr align=\"center\">
      <td valign=\"middle\">Vous n'avez plus de déplacement<br>Pour ce tour.</td>
    </tr>
    </table>\n\n");
}
echo("
  </td>
</tr>
</tbody>
</table>

<br><br><br>

</body>
</html>");
?>
