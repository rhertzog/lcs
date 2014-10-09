/**
 *
 * DETECTION NAVIGATEUR &  FONCTIONS DHTML
 * FONCTIONS JAVASCRIPT POUR PHPJMOL
 *
 *		IMPORTANT: TOUS LES SELECTEURS CONTIENNENT UNE id="XXX"
 *
 * @filesource phpJmol.js
 * @editor Eclipse & phpEclipse plugin
 * @copyright (C) Defolie Denis
 * @author	Defolie Denis (denis.defolie@free.fr)
 * @package  modules/lib/jmol
 * @version 1.0
 * @date 16/02/2006
 *
 * @license	http://www.gnu.org/copyleft/gpl.html GNU General Public License
 ***   This program is free software.
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
 */

 /**
  * DETECTION NAVIGATEUR
  */
	DOM = (document.getElementById) ? 1 : 0;
	NS4 = (document.layers) ? 1 : 0;
	Opera = (navigator.userAgent.indexOf('Opera') > -1) ? 1 : 0;
	IE = (navigator.userAgent.indexOf('MSIE') > -1) ? 1 : 0;
	IE = IE && !Opera;
	IE5 = IE && DOM;
	IE4 = (document.all) ? 1 : 0;
	IE4 = IE4 && IE && !DOM;
/**
 * FONCTIONS DHTML
 */
	function getObject(id)	{ return DOM ? document.getElementById(id) : NS4 ? document.layers[id] : document.all[id]; }
	function css(id)		{ return NS4 ? getObject(id) : getObject(id).style; }

	function getValue(id) 		{ return getObject(id).value;		}
	function setValue(id, value){ getObject(id).value = value;		}
	function setVisible(id)		{ css(id).visibility = 'visible';	}
	function setHidden(id)		{ css(id).visibility = 'hidden';	}
	function show(id)			{ css(id).display = 'block';		}
	function hide(id)			{ css(id).display = 'none';			}

	function isChecked(id)		{ return getObject(id).checked;		}

/*
 *  FONCTIONS JAVASCRIPT POUR PHPJMOL
 */
	/**
	* Appel de la methode script dans l'applet
	*
	* @access private
	* @params string	appletId	nom id applet
	* @params string	script		script ï¿½ ï¿½xï¿½cuter dans JmolApplet
	*/
	function jmolScript(appletId, script) {
		var applet = getObject(appletId);
	    if (applet)
      		applet.script(script);
	}
	/**
	* Actions sur objets html Checkbox
	*
	* @access public
	* @params string	appletId			nom id applet
	* @params string	boxId				nom id checkbox
	* @params string	scriptWhenChecked	script ï¿½ ï¿½xï¿½cuter quant case cochï¿½e
	* @params string	scriptWhenUnchecked	script ï¿½ ï¿½xï¿½cuter quant case dï¿½cochï¿½e
	*/
	function jmolCheckbox(appletId, boxId, scriptWhenChecked, scriptWhenUnchecked) {
		isChecked(boxId) ?
			jmolScript(appletId, scriptWhenChecked) :
			jmolScript(appletId, scriptWhenUnchecked);
	}
	/**
	* Actions sur objet html link
	*
	* @access public
	* @params string	appletId	nom id applet
	* @params string	script		script à exécuter dans JmolApplet
	*/
	function jmolLink(appletId, script) {
		jmolScript(appletId, script);
	}
	/**
	* Actions sur objet html Button
	*
	* @access public
	* @params string	appletId	nom id applet
	* @params string	script		script à exécuter dans JmolApplet
	*/
	function jmolButton(appletId, script) {
		jmolScript(appletId, script);
	}
	/**
	* Actions sur objet html Radio group
	*
	* @access public
	* @params string	appletId	nom id applet
	* @params string	script		script à exécuter dans JmolApplet
	*/
	function jmolRadio(appletId, script) {
		jmolScript(appletId, script);
	}
	/**
	* Actions sur objet html Select
	* les scripts se trouvent dans l'option
	*
	* @access public
	* @params string	appletId	nom id applet
	* @params string	selectId	nom id objet select
	*/
	function jmolSelect( appletId, selectId ) {
		jmolScript(appletId, getObject(selectId).value );
	}
	/**
	* Actions sur objet html textarea
	* le script se trouvent dans le textarea
	*
	* @access public
	* @params string	appletId	nom id applet
	* @params string	textId		nom id objet select
	*/
	function jmolTextArea( appletId, textId ) {
		jmolScript(appletId, getObject(textId).value );
	}