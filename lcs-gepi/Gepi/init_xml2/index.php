<?php

/*
 * $Id: index.php 7842 2011-08-20 14:45:42Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class='bold'><a href="../gestion/index.php#init_xml2"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<?php if (getSettingValue('use_ent') == 'y') {
	echo '<p>Avant de commencer, vous devez r�cup�rer les logins de vos utilisateurs dans l\'ENT : <a href="../mod_ent/index.php">RECUPERER</a></p>';
}
?>

<p>Vous allez effectuer l'initialisation de l'ann�e scolaire qui vient de d�buter.</p>
<?php

	//if((getSettingValue('use_sso')=="lcs")||(getSettingValue('use_sso')=="ldap_scribe")) {
	if((getSettingValue('use_sso')=="lcs")||(getSettingValue('auth_sso')=="lcs")||(getSettingValue('use_sso')=="ldap_scribe")||(getSettingValue('auth_sso')=="ldap_scribe")) {
		echo "<p style='color:red;'><b>ATTENTION&nbsp;:</b> Vous utilisez un serveur LCS ou SCRIBE.<br />
		Il existe un mode d'initialisation de l'ann�e propre � <a href='../init_lcs/index.php'>LCS</a> d'une part et � SCRIBE d'autre part (<i><a href='../init_scribe/index.php'>Scribe</a> et <a href='../init_scribe_ng/index.php'>Scribe_ng</a></i>).<br />
		Si vous initialisez l'ann�e avec le mode XML, vous ne pourrez pas utiliser les comptes de votre serveur LCS/SCRIBE par la suite pour acc�der � GEPI.<br />R�fl�chissez-y � deux fois avant de poursuivre.</p>\n";
		echo "</p>\n";
	}

	echo "<p>Avez-vous pens� � effectuer les diff�rentes op�rations de fin d'ann�e et pr�paration de nouvelle ann�e � la page <a href='../gestion/changement_d_annee.php' style='font-weight:bold;'>Changement d'ann�e</a>&nbsp?</p>\n";

	/*
	$sql="SELECT 1=1 FROM matieres_notes LIMIT 1;";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0) {
		echo "<p>Avez-vous pens� � <a href='#' onmouseover=\"afficher_div('archivage','y',20,20);\" onclick=\"return false;\">archiver</a> l'ann�e qui se termine ?</p>\n";
		$texte="<p>L'archivage de l'ann�e en cours vous permettra, une fois pass� � l'ann�e suivante, de consulter les bulletins ant�rieurs de chacun de vos �l�ves, pour peu qu'ils aient �t� scolaris�s dans votre �tablissement.</p>";
		if (getSettingValue("active_annees_anterieures")=='y') {
			$texte.="<p>Proc�der � l'<a href='../mod_annees_anterieures/conservation_annee_anterieure.php'>archivage de l'ann�e</a>.</p>";
		}
		else {
			$texte.="<p>Cela n�cessite l'activation du <a href='../mod_annees_anterieures/admin.php?quitter_la_page=y' target='_blank'>module 'Ann�es ant�rieures'</a>.</p>";
		}
		$tabdiv_infobulle[]=creer_div_infobulle('archivage',"Archivage d'une ann�e","",$texte,"",30,0,'y','y','n','n');
	}

	// CDT
	$sql="SELECT 1=1 FROM ct_entry LIMIT 1;";
	$test1=mysql_query($sql);
	$sql="SELECT 1=1 FROM ct_devoirs_entry LIMIT 1;";
	$test2=mysql_query($sql);
	if((mysql_num_rows($test1)>0)||(mysql_num_rows($test2)>0)) {
		echo "<p>Les cahiers de textes ne sont pas vides.<br />\n";
		echo "Vous devriez&nbsp;:</p>\n";
		echo "<ol>\n";
		echo "<li><a href='../cahier_texte_2/archivage_cdt.php'>archiver les cahiers de textes de l'an dernier</a> si ce n'est pas encore fait,</li>\n";
		echo "<li>puis <a href='../cahier_texte_admin/admin_ct.php'>vider les cahiers de textes de l'an dernier</a> avant de proc�der � l'initialisation.</li>\n";
		echo "</ol>\n";
	}
	*/
?>
<ul>
	<li>
		<p>Au cours de la proc�dure, le cas �ch�ant, certaines donn�es de l'ann�e pass�e seront d�finitivement effac�es de la base GEPI (<i>�l�ves, notes, appr�ciations, ...</i>).<br />
		Seules seront conserv�es les donn�es suivantes :<br /></p>
		<ul>
			<li><p>les donn�es relatives aux �tablissements,</p></li>
			<li><p>les donn�es relatives aux classes : intitul�s courts, intitul�s longs, nombre de p�riodes et noms des p�riodes,</p></li>
			<li><p>les donn�es relatives aux mati�res : identifiants et intitul�s complets,</p></li>
			<li><p>les donn�es relatives aux utilisateurs (<i>professeurs, administrateurs, ...</i>). Concernant les professeurs, les mati�res enseign�es par les professeurs sont conserv�es,</p></li>
			<li><p>Les donn�es relatives aux diff�rents types d'AID.</p></li>
		</ul>
	</li>
	<li>
		<p>Pour proc�der aux importations, quatre fichiers sont requis:</p>
		<p>Les trois premiers, 'ElevesAvecAdresses.xml', 'Nomenclature.xml', 'ResponsablesAvecAdresses.xml', doivent �tre r�cup�r�s depuis l'application web Sconet.<br />
		Demandez gentiment � votre secr�taire de se rendre dans 'Sconet/Acc�s Base �l�ves mode normal/Exploitation/Exports standard/Exports XML g�n�riques' pour r�cup�rer les fichiers 'ElevesAvecAdresses.xml', 'Nomenclature.xml' et 'ResponsablesAvecAdresses.xml'.</p>
		<p>Le dernier, 'sts_emp_RNE_ANNEE.xml', doit �tre r�cup�r� depuis l'application STS/web.<br />
		Demandez gentiment � votre secr�taire d'acc�der � STS-web et d'effectuer le parcours suivant: 'Mise � jour/Exports/Emplois du temps'</p>

		<?php
		//==================================
		// RNE de l'�tablissement pour comparer avec le RNE de l'�tablissement de l'ann�e pr�c�dente
		$gepiSchoolRne=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
		//==================================
		if($gepiSchoolRne=="") {
			echo "<p><b style='color:red;'>Attention</b>: Le RNE de l'�tablissement n'est pas renseign� dans 'Gestion g�n�rale/<a href='../gestion/param_gen.php' target='_blank'>Configuration g�n�rale</a>'<br />Cela peut perturber l'import de l'�tablissement d'origine des �l�ves.<br />Vous devriez corriger avant de poursuivre.</p>\n";
		}
		?>

		<ul>
			<li>
				<p><a href='step1.php'>Proc�der � la premi�re phase</a> d'importation des �l�ves, de constitution des classes et d'affectation des �l�ves dans les classes : le fichier <b>ElevesAvecAdresses.xml</b> (<i>ou ElevesSansAdresses.xml</i>) et le fichier <b>Nomenclature.xml</b> sont requis.<br />
				Le deuxi�me fichier sert � identifier les noms courts des options des �l�ves (<i>le premier fichier ne contient que les codes num�riques de ces options</i>).</p>
			</li>
			<li>
				<p><a href='responsables.php'>Proc�der � la deuxi�me phase</a> d'importation des responsables des �l�ves : le fichier <b>ResponsablesAvecAdresses.xml</b> est requis.</p>
			</li>
			<li>
				<p><a href='matieres.php'>Proc�der � la troisi�me phase</a> d'importation des mati�res : le fichier <b>sts_emp_RNE_ANNEE.xml</b> est requis.</p>
			</li>
			<li>
				<p><a href='professeurs.php'>Proc�der � la quatri�me phase</a> d'importation des professeurs.<br />
				Le fichier <b>sts_emp_RNE_ANNEE.xml</b> doit avoir �t� fourni � l'�tape pr�c�dente pour pouvoir �tre � nouveau lu lors de cette �tape.</p>
			</li>
			<li>
				<p><a href='prof_disc_classe_csv.php?a=a<?php echo add_token_in_url();?>'>Proc�der � la cinqui�me phase</a> d'affectation des mati�res � chaque professeur, d'affectation des professeurs dans chaque classe  et de d�finition des options suivies par les �l�ves.<br />
				Le fichier <b>sts_emp_RNE_ANNEE.xml</b> doit avoir �t� fourni deux �tapes auparavant pour pouvoir �tre � nouveau lu lors de cette �tape.</p>
			</li>
			<li>
				<p><a href='init_pp.php'>Proc�der � la sixi�me phase</a>: Initialisation des professeurs principaux.</p>
			</li>
			<li>
				<p><a href='clean_tables.php?a=a<?php echo add_token_in_url();?>'>Proc�der � la septi�me phase</a> de nettoyage des donn�es : les donn�es inutiles import�es � partir des fichiers XML lors des diff�rentes phases d'initialisation seront effac�es !</p>
			</li>
			<li>
				<p><a href='clean_temp.php?a=a<?php echo add_token_in_url();?>'>Proc�der � la phase de nettoyage des fichiers</a>: Supprimer les fichiers XML et CSV qui n'auraient pas �t� supprim�s auparavant.</p>
			</li>
		</ul>
	</li>
	<li>
		<p>Une fois toute la proc�dure d'initialisation des donn�es termin�e, il vous sera possible d'effectuer toutes les modifications n�cessaires au cas par cas par le biais des outils de gestion inclus dans <b>GEPI</b>.</p>
	</li>
</ul>
<?php require("../lib/footer.inc.php");?>