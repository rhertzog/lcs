<?php
/*
 $Id: footer.inc.php 4372 2010-04-26 17:48:43Z crob $
*/
// SI on ne souhaite pas utiliser les js de base, on enl�ve tout ce qui suit :
if (isset($utilisation_jsbase) AND $utilisation_jsbase == "non") {
	echo "<!-- Pas de js en pied -->\n";
} else {
	echo "<!-- D�but du pied -->\n";
	// ========================================
	// Astuce http://www.ehow.com/how_2000413_convert-em-px-sizes.html
	// pour calculer le rapport em/px et corriger le positionnement des infobulles (taille fix�e en 'em')
	echo "<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
		//alert('1em == '+em2px+'px');
	</script>\n";
	// ========================================



	// On a stock� les DIV dans un tableau et on parcourt le tableau PHP en fin de page pour afficher les infobulles en dehors du coeur de la page.
	// Les infobulles apparaissent ainsi un peu comme des notes de bas de page.
	// On pourrait cependant ins�rer l'infobulle au milieu du texte avec:
	//       echo creer_div("div1","1er DIV","Test de petit texte",12,"y","y","n");
	// au risque de perturber l'affichage de la page si Javascript est d�sactiv�.

	if(isset($tabdiv_infobulle)){
//echo "1";
		// Pour �viter des cas de doublons...
		$temoin_infobulle=array();

		//echo count($tabdiv_infobulle);
		if(count($tabdiv_infobulle)>0){
//echo "2";
			for($i=0;$i<count($tabdiv_infobulle);$i++){
				//if(!in_array($tabid_infobulle[$i],$temoin_infobulle)) {
				if((isset($tabid_infobulle[$i]))&&(!in_array($tabid_infobulle[$i],$temoin_infobulle))) {
					echo $tabdiv_infobulle[$i]."\n";
					$temoin_infobulle[]=$tabid_infobulle[$i];
				}
/*
				if(isset($tabid_infobulle[$i])) {
					if(!in_array($tabid_infobulle[$i],$temoin_infobulle)) {
						//echo "$i ";
						echo $tabdiv_infobulle[$i]."\n";
						$temoin_infobulle[]=$tabid_infobulle[$i];
					}
				}
				elseif(isset($tabdiv_infobulle[$i])) {
					echo $tabdiv_infobulle[$i]."\n";
				}
*/
			}
		}
	}

	// T�moin destin� � tester la fin de chargement de la page pour �viter des erreurs JavaScript avant la fin de chargement de tous les �l�ments.
	// Par exemple: la fonction cacher_div() utilis�e plus bas teste cette variable pour ne tenter les op�rations que si la variable est � 'ok'
	echo "<script type='text/javascript'>
	temporisation_chargement='ok';
	//desactivation_infobulle='n';
</script>\n";

	if(isset($tabid_infobulle)){
		if(count($tabid_infobulle)>0){
			// On cache les DIV en fin de chargement de la page (il faut qu'ils existent pour qu'il soit possible de les cacher).
			// Il me semble qu'il n'est pas possible d'initialiser le 'display' � 'none' et de modifier ce display ensuite via JavaScript.
			echo "<script type='text/javascript'>\n";
			for($i=0;$i<count($tabid_infobulle);$i++){
				echo "cacher_div('".$tabid_infobulle[$i]."');\n";
			}
			echo "</script>\n";

			/*
			// Remarques:
			echo "<p><i>Remarque:</i></p><blockquote><p>Pour tester l'effet infobulle-&gt;note de bas de page, d�sactivez JavaScript et rechargez la page.<br />Par exemple, avec l'extension WebDevelopper de Firefox, cliquez sur la barre: Disable/Disable javascript/All javascripts et rechargez la page.</p></blockquote>\n";
			*/

			/*
			// Pour afficher le code source de la page:
			echo "<div style='width: 800px; border: 1px solid black;'>\n";
			show_source($_SERVER['SCRIPT_FILENAME']);
			echo "</div>\n";
			*/
		}
	}
} // fin du if (isset($utilisation_jsbase) AND $utilisation_jsbase == "non")

	if(getSettingValue("gepi_pmv")!="n"){
		if (file_exists($gepiPath."/pmv.php")) require ($gepiPath."/pmv.php");
	}
	// Affichage de la dur�e de chargement de la page

	if (!isset($niveau_arbo)) $niveau_arbo = 1;

	if ($niveau_arbo == "0") {
		require ("./lib/microtime.php");
	} elseif ($niveau_arbo == "1") {
		require ("../lib/microtime.php");
	} elseif ($niveau_arbo == "2") {
		require ("../../lib/microtime.php");
	} elseif ($niveau_arbo == "3") {
		require ("../../../lib/microtime.php");
	}
?>
</div>

<?php
	// Pour permettre l'affichage du nombre de champs dans les formulaires, ins�rer:
	// insert into setting set name='affich_debug_info_form', value='y';
	// insert into setting set name='login_debug_info_form', value='LOGIN1|LOGIN2|LOGIN3|...';
	// insert into setting set name='statut_debug_info_form', value='professeur|administrateur|...';

	if(getSettingValue('affich_debug_info_form')=='y') {
		$affiche_lien_info_forms="n";

		$liste_login_debug_info_form=getSettingValue('login_debug_info_form');
		if($liste_login_debug_info_form!='') {
			$tab_tmp=explode('|',$liste_login_debug_info_form);
			if(in_array($_SESSION['login'],$tab_tmp)) {
				$affiche_lien_info_forms="y";
			}
		}

		$liste_statut_debug_info_form=getSettingValue('statut_debug_info_form');
		if($liste_statut_debug_info_form!='') {
			$tab_tmp=explode('|',$liste_statut_debug_info_form);
			if(in_array('all',$tab_tmp)) {
				$affiche_lien_info_forms="y";
			}
			elseif(in_array($_SESSION['statut'],$tab_tmp)) {
				$affiche_lien_info_forms="y";
			}
		}

		if($affiche_lien_info_forms=="y") {
			echo "<a name='div_info_formulaires'></a>\n";
			echo "<a href='#div_info_formulaires' onclick=\"info_form('div_info_formulaires'); afficher_div('div_info_formulaires','y',10,10); return false;\">\n";
			if ($niveau_arbo == "0") {
				$chemin_img_info_forms="images/ico_question_petit.png";
			} elseif ($niveau_arbo == "1") {
				$chemin_img_info_forms="../images/ico_question_petit.png";
			} elseif ($niveau_arbo == "2") {
				$chemin_img_info_forms="../../images/ico_question_petit.png";
			} elseif ($niveau_arbo == "3") {
				$chemin_img_info_forms="../../../images/ico_question_petit.png";
			}
			echo "<img src='$chemin_img_info_forms' width='15' height='15' title='Image info forms' />";

			echo "</a>\n";
			echo "<div id='div_info_formulaires' style='color:red; border:1px solid black; background-color: white; width: 40em; display:none; margin: 1em;'></div>\n";
		}
	}
?>

</body>
</html>
