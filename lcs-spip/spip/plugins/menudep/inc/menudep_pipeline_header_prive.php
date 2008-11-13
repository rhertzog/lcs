<?php 

	// exec/menudep_pipeline_header_prive.php
	
	// $LastChangedRevision: 17623 $
	// $LastChangedBy: paladin@quesaco.org $
	// $LastChangedDate: 2007-12-22 19:37:31 +0100 (sam., 22 déc. 2007) $

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Menudep.
	
	Menudep is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Menudep is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Menudep; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Menudep. 
	
	Menudep est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publi�e par 
	la Free Software Foundation (version 2 ou bien toute autre version ult�rieure 
	choisie par vous).
	
	Menudep est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU 
	pour plus de d�tails. 
	
	Vous devez avoir re�u une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez � la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// pipeline (plugin.xml)
// Ajoute l'appel aux feuilles de style dans le header priv�
function menudep_header_prive ($flux) {

	global $connect_statut
		, $connect_toutes_rubriques
		;

	$exec = _request('exec');
	
	if(
		($exec == 'menudep_configuration')
		&& ($connect_statut == '0minirezo')
		&& $connect_toutes_rubriques
		) {

		include_spip('inc/plugin_globales_lib');
	
		$config = __plugin_lire_key_in_serialized_meta('config', _MENUDEP_META_PREFERENCES);
		
		$menudep_values_array = unserialize(_MENUDEP_DEFAULT_VALUES_ARRAY);
		
		$js_var_menudep = "";
		$oui_non = array('oui', 'non');
		
		foreach($menudep_values_array as $key => $value) {
			if(!isset($config[$key]) || !$config[$key] || empty($config[$key])) $config[$key] = $value;
			$js_var_menudep .= ""
				. "$('#" . $key . "')"
				.	(
					in_array($value, $oui_non)
					? ".mcheck(" . (($value == 'oui') ? 'true' : 'false') . ")"
					: ".val('" . $value . "')"
					)
				. ";"
				;
		}

		//		
		$flux .= ""
			. "

<style type='text/css'>
<!--
#btn_valider_reinitialiser { background-color: red; }
fieldset { border:1px solid gray; margin-top:0.5em; }
.description { font-style: italic; }
.description em { font-style: normal; }
ul.meta-info-liste p { display:inline; } /* supprimes les <p> ajout�s aux infos par 193 */
}
-->
</style>

<!-- MenuDep JS -->
<script language='JavaScript'>
<!--
	
	$(document).ready(function(){
		
		jQuery.fn.extend({
			mcheck: function(i) {
			  return ($(this).attr('checked',i));
			}
		 });
	 
 		$('#btn_valider_reinitialiser').click(function(){
		"
		. $js_var_menudep
		. "
			return(false);
		});
		
		$('#menudep_replier').click(function(){
			if(this.checked) $('#menudep_reavant_id').show();
			else $('#menudep_reavant_id').hide();
		});

		$('#menudep_absolute').click(function(){
			if(this.checked) $('#menudep_absolute_pref_id').show();
			else $('#menudep_absolute_pref_id').hide();
		});

		$('#menudep_heriter').click(function(){
			if(this.checked) $('#menudep_heriter_pref_id').show();
			else $('#menudep_heriter_pref_id').hide();
		});
		
	});
	
//-->
</script>		
<!-- end MenuDep JS -->

"
		;
	}
	
	return ($flux);
}

?>