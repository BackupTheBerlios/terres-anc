<?php
/*
Engine of ``Terres-Anciennes", a web-based multiplayer RPG.
Copyright 2004, 2005 Nicolas Hess / Choplair-network / Nova Devs.
#$Id: email.html.php,v 1.2 2005/02/09 20:38:04 pachilor Exp $

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

if( !defined('php'))
{ exit; }

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Language" content="fr" />
	
<title>Terres Anciennes - Changement email</title>
<style type="text/css">
<!--
body {
background-image: url(./images/paper_blue.jpg);
color: #FFFFFF;
font-family: "Trebuchet MS", Verdana, Helvetica, Arial, sans-serif;
font-size: 0.8em;
text-align: center;
}

a { text-decoration: none; color:#FFFFFF; }
a:link { text-decoration: none; }
a:visited { text-decoration: none; }
a:hover { text-decoration: underline; color: #A3A3A3; }

.text {
font-size: 16px;
font-weight: bold;
border: 1px solid black;
padding: 2px;
}

input {
color: #0F1113; 
font-family: Arial,Verdana,times;
background-color: #E1E1E1;
text-decoration: none;
font-size: 9pt;
border: 1px solid black;
padding: 3px;
}
-->
</style>
</head>

<body>

<div align="center">

<br /><img src="./images/top.jpg" alt="top_logo" /><br /><br />

<form method="post" action="./panneau_joueur.php">
<table border="0">
<tr>
  <td>
    <font style="color:white;">Nouvel email: </font> &nbsp;
  </td>
  <td>
    <input type="text" name="email" size="40" maxlength="62" class="text" /><br />
  </td>
</tr>
<tr>
  <td align="center" colspan="2">
    <br /><input type="submit" value="Enregistrer" />
  </td>
</tr>
</table>
</form>

<a href="./panneau_joueur.php">Retour</a>

</div>
</body>
</html>
