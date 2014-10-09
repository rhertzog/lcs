<?php
/**
 * Interface php pour l'applet Jmol.
 * jmol.php : action pour moteur wikini 0.4.1
 *
 * @filesource jmol.php
 * @copyright (C) Defolie Denis
 * @author	Defolie Denis (denis.defolie@free.fr)
 * @link mailto:denis.defolie@free.fr
  * @version 1.0
 * @date 12/10/2006
 *
 *	All rights reserved.
 *	Redistribution and use in source and binary forms, with or without
 *	modification, are permitted provided that the following conditions
 *	are met:
 *	1. Redistributions of source code must retain the above copyright
 *	notice, this list of conditions and the following disclaimer.
 *	2. Redistributions in binary form must reproduce the above copyright
 *	notice, this list of conditions and the following disclaimer in the
 *	documentation and/or other materials provided with the distribution.
 *	3. The name of the author may not be used to endorse or promote products
 *	derived from this software without specific prior written permission.
 *
 *	THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 *	IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 *	OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 *	IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 *	INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 *	NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 *	DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 *	THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 *	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 *	THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

	if ( ($jmol=$this->config['jmol_action']) != "") {
		/**
		 * PARAMETRES JMOL
		 */
		$jmol_base = $this->GetConfigValue('action_path') .'/'. $jmol;
		$jmol_css = "$jmol_base/jmol-class.css";

		define("PHPJMOL", "$jmol_base/");
		define ("JMOL_CODEBASE", "./$jmol" );

		/**
		 * INSERER SOURCE DE LA CLASSE phpJmol.class.php
		 */
		if (!class_exists('jmolWIKINI')){
			include("$jmol_base/jmolWIKINI.class.php");
			/**
			 * FEUILLE DE STYLE et JAVASCRIPT: LIAISON AVEC APPLET
			 *
			 * IL SERAIT BON DE TROUVER UN MOYEN D'ECRIRE DIRECTEMENT CES INFOS
			 * DANS LE HEADER DU FICHIER WIKI GENERE, L'ACTION HEADER DE WIKINI
			 * ETANT REALISEE AVANT L'APPEL DE CETTE ACTION.
			 *
			 */
			print "<link href=\"$jmol_css\" rel=\"stylesheet\" type=\"text/css\" />";
			print "<script type=\"text/javascript\">";
			include("$jmol_base/phpJmol.js");
			print "</script>";
		}

		/**
		 * Declarer l' applet et afficher
		 */
		$objet = new jmolWIKINI($this);
		$objet->display();
	} else {
		print "<span style=\"color:red\">La classe phpJmol n'est pas disponible sur ce wiki!</span>";
	}
?>