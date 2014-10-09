<?php
/**
 *	jmolWIKINI.class.php est la classe action pour commander phpJmol (php Jmol Interface) a travers wikini
 *  format: {{jmol xml="mamolecule" }} ou {{jmol title="Benzene" size="300x350" script="script"}}
 *  L'applet et tous ses controles (actions sur l'applet elle meme) sont decrits dans une page XML
 *  et en simplfie sa mise en oeuvre.
 *  On peut en plus utiliser les pages Wiki pour stocker les descriptifs des atomes et les scripts.
 *
 *Action decrite dans un format Xml (avec controles de l'applet)
 *--------------------------------------------------------------
 *	Action: jmol
 *	Attribut xml: nom de la page contenant la description XML
 *	Attribut control: ajout ou non d'actions sur l'applet. Attribut absent signifie aussi aucun controle
 *		Valeurs :
 *			-absent aucun controle, applet seule
 *			-"no" aucun controle, applet seule
 *   		-"all" ajouter tous les contoles prevus dans description XML ou
 *   		-"nomDuControle"  ajouter le contole dont l'attribut name="nomDuControle"  dans description XML ou
 *   		-"nom1,nom2,nom3" ajouter les controles nom1, nom2 et nom3
 *
 * 	Description xml voir jmolXML.class.php
 *
 *Action applet seule (sans controle)
 *------------------------------------
 *Action: jmol
 *	Attribut title: titre de l'applet
 *	Attribut size: dimension de l'applet. Ex: size="300x400", size="400"
 *	Attribut au choix:
 *		a)script:  script Jmol (Documentations voir la documentation Jmol)
 *		b)model: modele d'atome dont la description est contenue dans une page Wiki
 *		c)load: script a charger dont la description est contenue dans une page Wiki
 *
 * ***   This program is free software.
 ***   You can redistribute it and/or modify it under the terms of the GNU General
 ***   Public License as published by the Free Software Foundation; either version 2
 ***   of the License, or (at your option) any later version.
 ***
 ***   This program is distributed in the hope that it will be useful, but WITHOUT ANY
 ***   WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 ***   PARTICULAR PURPOSE.
 ***   See the GNU General Public License for more details.
 ***
 ***   You should have received a copy of the GNU General Public License along with
 ***   this program; if not, write to the Free Software Foundation, Inc., 59 Temple
 ***   Place - Suite 330, Boston, MA  02111-1307, USA.
 ***   A copy of the GPL can be found at http://www.gnu.org/copyleft/gpl.html or in
 ***   the file GNU_license.txt.
 ***
 *
 * @copyright (C) Defolie Denis
 * @author	Defolie Denis (denis.defolie@free.fr)
 * @version 1.0
 * @date 12/10/2006
 */

require_once( PHPJMOL . "jmolXML.class.php");

class jmolWIKINI extends jmolXML {
	/**
	 * Constructeur
	 * @param	object	& de la classe wikini
	 */
	function jmolWIKINI(&$wiki) {
		$this->wiki = $wiki;
		$this->base = $this->wiki->GetConfigValue('action_path') .'/'. $this->wiki->config['jmol_action'];
		//-- cas xml sequence --
		if ( ($xmlPage=$this->wiki->GetParameter('xml')) ) {
			parent::jmolXML(
				$this->loadExternalScript($xmlPage),
				($ctrl=$this->wiki->GetParameter('control')) ? $ctrl : 'all'
			);
		}
		//-- cas Jmol sequence --
		else {
			$JMOL = array(
				'TITLE'	=> $this->wiki->GetParameter('title'),
				'SIZE'	=> $this->wiki->GetParameter('size'),
				'LOAD'	=> $this->wiki->GetParameter('load'),
				'MODEL'	=> $this->wiki->GetParameter('model'),
				'SCRIPT'=> $this->wiki->GetParameter('script')
			);
			parent::jmolXML('', '', $JMOL);
		}
		//-- Init applet --
		parent::jmolXMLApplet();
	}
	/**
	 * Appel d'un script ou modele externe
	 * Implementation de la methode
	 *
	 * @param string $file	fichier ou autre
	 * @return string		contenu du fichier ou autre
	 */
	function loadExternalScript($file) {
		$content = $this->wiki->LoadPage($file);
		return trim($content['body'], "% \"");
	}
	/**
	 * Affiche l'applet avec les objets s'ils existent
	 *
	 */
	function display() {
		$buffer = "<fieldset class='jmol-frame'>\n" . $this->applet();
		switch ( ($ctrl=$this->controlMode) ) {
			case 'all':
				foreach($this->control as $k=>$control) {
					$buffer.= "<p>$control</p>";
				}
			case 'no':
				break;
			default:
				foreach(explode(',', $ctrl) as $control) {
					$buffer.= '<p>' . $this->control[trim($control, "\t\n\r ")] .'</p>';
				}
		}
		print $buffer . "</fieldset>\n";
	}
}
?>
