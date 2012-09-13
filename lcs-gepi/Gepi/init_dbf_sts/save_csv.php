<?php
/*
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session

$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Page bourrinée... la gestion du token n'est pas faite... et ne sera faite que si quelqu'un utilise encore ce mode d'initialisation et le manifeste sur la liste de diffusion gepi-users
check_token();

// Initialisation du répertoire actuel de sauvegarde
$dirname = getSettingValue("backup_directory");


switch($_GET['fileid']){
	case 0:
		$filename="f_wind.csv";
		$filepath="../backup/".$dirname."/csv/".$filename;
		break;
	case 1:
		$filename="f_men.csv";
		$filepath="../backup/".$dirname."/csv/".$filename;
		break;
	case 2:
		$filename="f_gpd.csv";
		$filepath="../backup/".$dirname."/csv/".$filename;
		break;
	case 3:
		$filename="f_tmt.csv";
		$filepath="../backup/".$dirname."/csv/".$filename;
		break;
	case 4:
		$filename="f_div.csv";
		$filepath="../backup/".$dirname."/csv/".$filename;
		break;
	default:
	    header("Location: ../logout.php?auto=1");
	    die();
}

//header('Content-Encoding: utf-8');
header('Content-Type: text/x-csv');
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
include($filepath);
?>
