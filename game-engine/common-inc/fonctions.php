<?php

/***************************************************************************
* *                               fonctions.php
* *                            -------------------
* *   begin     : Monday, January 31, 2005
* *   copyright : www.terres-anciennes.com
* *   email     : nicolas.hess@gmail.com, mortys_666@hotmail.com
* *               pachilor@hotmail.com  , stephmouton@hotmail.com
**
* *   Version: index.php v 0.0.1
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


/* FONCTION BBCODE */

function bbcode_first($text)
{
	$text = preg_replace("#\[img\]((ht|f)tp://)([^\r\n\t<\"]*?)\[/img\]#sie", "'<img src=\\1' . str_replace(' ', '%20', '\\3') . '>'", $text);
        $text = preg_replace("#\[url\]((ht|f)tp://)([^\r\n\t<\"]*?)\[/url\]#sie", "'<a href=\"\\1' . str_replace(' ', '%20', '\\3') . '\" target=blank>\\1\\3</a>'", $text);
        $text = preg_replace("/\[url=(.+?)\](.+?)\[\/url\]/", "<a href=$1 target=blank>$2</a>", $text);

        $text = preg_replace("/\[b\](.+?)\[\/b\]/", "<b>$1</b>", $text);
        $text = preg_replace("/\[i\](.+?)\[\/i\]/", "<i>$1</i>", $text);
        $text = preg_replace("/\[u\](.+?)\[\/u\]/", "<u>$1</u>", $text);
        $text = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/", "<font color=$1>$2</font>", $text);
        return $text;
}
function bbcode_second($text)
{
	$text = preg_replace("#\[img\]((ht|f)tp://)([^\r\n\t<\"]*?)\[/img\]#sie", "'<img src=\\1' . str_replace(' ', '%20', '\\3') . '>'", $text);
        $text = preg_replace("#\[url\]((ht|f)tp://)([^\r\n\t<\"]*?)\[/url\]#sie", "'<a href=\"\\1' . str_replace(' ', '%20', '\\3') . '\" target=blank>\\1\\3</a>'", $text);
        $text = preg_replace("/\[url=(.+?)\](.+?)\[\/url\]/", "<a href=$1 target=blank>$2</a>", $text);

        $text = preg_replace("/\[b\](.+?)\[\/b\]/", "<b>$1</b>", $text);
        $text = preg_replace("/\[i\](.+?)\[\/i\]/", "<i>$1</i>", $text);
        $text = preg_replace("/\[u\](.+?)\[\/u\]/", "<u>$1</u>", $text);
        $text = preg_replace("/\[code\](.+?)\[\/code\]/", "<table width=100%><tr><th align=left>Code :</th></tr><tr><td align=left><code>$1</code></td></tr></table>", $text);
        $text = preg_replace("/\[quote\](.+?)\[\/quote\]/", "<table width=100%><tr><th align=left>citation :</th></tr><tr><td align=left>$1</td></tr></table>", $text);
        $text = preg_replace("/\[quote=(.+?)\](.+?)\[\/quote\]/", "<table width=100%><tr><th align=left>$1 :</th></tr><tr><td align=left>$2</td></tr></table>", $text);
        $text = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/", "<font color=$1>$2</font>", $text);
        return $text;
}

/* PERMET D'AFFICHER $sexe: M ou F; $image: 0 (ecriture) 1 (image) avec la racine */

function sexe($sexe,$image=0,$root='./')
{
	switch($sexe)
	{
		case 'M': $retrun = ($image == 0) ? 'Masculin' : '<img src="'.$root.'images/male.gif" alt="masculin" />'; break;
		case 'F': $retrun = ($image == 0) ? 'F&eacute;minin' : '<img src="'.$root.'images/female.gif" alt="feminin" />'; break;
	}
	return $retrun;
}

/* POUR AFFICHER L'AVATAR A PARTIR DU FORUM PHPBB */

function avatar($pseudo_perso,$root='../../')
{
	global $db_forum,$link;
        mysql_select_db($db_forum,$link);
        $avatar = mysql_query("SELECT user_avatar,user_avatar_type FROM phpbb_users WHERE username='".$pseudo_perso."' LIMIT 1");
        $avatar = mysql_fetch_array($avatar);
        switch($avatar[1])
        {
        	case 1: $aff = "<img src='".$root."forum/images/avatars/".$avatar[0]."' alt=\"".$pseudo_perso."\" />\n"; break;
                case 2: $aff = "<img src='".$avatar[0]."' alt=\"".$pseudo_perso."\" />\n"; break;
                case 3: $aff = "<img src='".$root."forum/images/avatars/gallery/".$avatar[0]."' alt=\"".$pseudo_perso."\" />\n"; break;
                default:  $aff = "<img src='http://www.terres-anciennes.com/images/no_avatar.jpg' alt=\"no_avatar\" />\n";
        }
        return $aff;
}

/* INSERT MORTS DANS LA BASE DE DONNEE */

function ajouter_mort($id_perso_tueur,$id_perso_mort)
{
	$sql = "INSERT INTO joueur_mort (acteur_id,time,perso_mort_id) VALUES ('$id_perso_tueur','".time()."','$id_perso_mort');";
	if( !mysql_query($sql))
        {
        	echo("Erreur lors de la mort");
        }
}

/* RETOURNER LA RACE QUI CORRESPOND A L'INITIALE */

function race($race)
{
	switch($race)
        {
        	case 'A': $race_perso = 'Ame-Perdue'; break;
                case 'C': $race_perso = 'Chaos'; break;
                case 'E': $race_perso = 'Elfe'; break;
                case 'H': $race_perso = 'Humain'; break;
                case 'N': $race_perso = 'Nain'; break;
                case 'M': $race_perso = 'Monstre'; break;
        }
        return $race_perso;
}

/* RETOURNER LA CLASSE QUI CORRESPOND A L'INITIALE ET AU NIVEAU */

function classe($classe,$niveau=1)
{
	if($niveau == 1)
        {
        	 switch($classe)
                 {
                 	case 'A': $c = 'Archer'; break;
                        case 'B': $c = 'Berserker'; break;
                        case 'F': $c = 'Fantassin'; break;
                        case 'G': $c = 'Guerrier'; break;
                        case 'M': $c = 'Mage'; break;
                }
        }
        if($niveau == 2)
        {
        	switch($classe)
                {
                	case 'A': $c = 'Tirailleur'; break;
                        case 'B': $c = 'Fanatique'; break;
                        case 'F': $c = 'Tirailleur'; break;
                        case 'G': $c = 'Fine Lame'; break;
                        case 'M': $c = 'Mage Blanc'; break;
                }
        }
        if($niveau == 3)
        {
        	switch($classe)
                {
                	case 'A': $c = 'Grand Archer'; break;
                        case 'B': $c = 'Fou Furieux'; break;
                        case 'F': $c = 'V&eacute;t&eacute;ran'; break;
                        case 'G': $c = 'Rodeur'; break;
                        case 'M': $c = 'Mage Rouge'; break;
                }
        }
        if($niveau == 4)
        {
        	switch($classe)
                {
                	case 'A': $c = 'Archer Composite'; break;
                        case 'B': $c = 'Fr&eacute;n&eacute;tique'; break;
                        case 'F': $c = 'Lieutenant'; break;
                        case 'G': $c = 'Bretteur'; break;
                        case 'M': $c = 'Mage Noir'; break;
                }
        }
        if($niveau >= 5)
        {
        	switch($classe)
                {
                	case 'A': $c = "Archer d'&eaucte,lite"; break;
                        case 'B': $c = 'Foudre de Guerre'; break;
                        case 'F': $c = "Archer d'élite"; break;
                        case 'G': $c = "Maitre d'arme"; break;
                        case 'M': $c = 'Archimage'; break;
                }
        }
        return $c;
}

/* FONCTION PHPBB bof bof */

function smilies_pass_nicolas($message)
{
	static $orig, $repl;

	if (!isset($orig))
	{
		global $db, $board_config;
		$orig = $repl = array();

		$sql = 'SELECT * FROM ' . SMILIES_TABLE;
		if( !$result = $db->sql_query($sql) )
		{
			message_die(GENERAL_ERROR, "Couldn't obtain smilies data", "", __LINE__, __FILE__, $sql);
		}
		$smilies = $db->sql_fetchrowset($result);

		if (count($smilies))
		{
			usort($smilies, 'smiley_sort');
		}

		for ($i = 0; $i < count($smilies); $i++)
		{
			$orig[] = "/(?<=.\W|\W.|^\W)" . phpbb_preg_quote($smilies[$i]['code'], "/") . "(?=.\W|\W.|\W$)/";
			$repl[] = '<img src="http://www.terres-anciennes.com/forum/'. $board_config['smilies_path'] . '/' . $smilies[$i]['smile_url'] . '" alt="' . $smilies[$i]['emoticon'] . '" border="0" />';
		}
	}

	if (count($orig))
	{
		$message = preg_replace($orig, $repl, ' ' . $message . ' ');
		$message = substr($message, 1, -1);
	}

	return $message;
}
function clickable($text)
{
	$ret = ' ' . $text;
	$ret = preg_replace("#(^|[\n ])([\w]+?://[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
	$ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
	$ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
	$ret = substr($ret, 1);
        return($ret);
}


?>
