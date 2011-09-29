<?php
/**
 * Fichier de mise � jour de la version 1.5.3 � la version 1.5.4
 * 
 * $Id: 153_to_1531.inc.php 7923 2011-08-23 12:36:29Z regis $
 *
 * Le code PHP pr�sent ici est ex�cut� tel quel.
 * Pensez � conserver le code parfaitement compatible pour une application
 * multiple des mises � jour. Toute modification ne doit �tre r�alis�e qu'apr�s
 * un test pour s'assurer qu'elle est n�cessaire.
 *
 * Le r�sultat de la mise � jour est du html pr�format�. Il doit �tre concat�n�
 * dans la variable $result, qui est d�j� initialis�.
 *
 * Exemple : $result .= msj_ok("Champ XXX ajout� avec succ�s");
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */

$result .= "<h3 class='titreMaJ'>Mise � jour vers la version 1.5.3.1" . $rc . $beta . " :</h3>";


$test = sql_query1("SHOW TABLES LIKE 'edt_semaines'");
if ($test == -1) {
	$result .= "<br />Cr�ation de la table 'edt_semaines'. ";
	$sql="CREATE TABLE edt_semaines (id_edt_semaine int(11) NOT NULL auto_increment,num_edt_semaine int(11) NOT NULL default '0',type_edt_semaine varchar(10) NOT NULL default '', num_semaines_etab int(11) NOT NULL default '0', PRIMARY KEY  (id_edt_semaine));";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la cr�ation de la table 'edt_semaines': ".$result_inter."<br />";
	}
}

// Modification Eric
// ============= Insertion d'un champ pour le module discipline

$sql = "SELECT commentaire FROM s_incidents LIMIT 1";
$req_rank = mysql_query($sql);
if (!$req_rank){
    $sql_request = "ALTER TABLE `s_incidents` ADD `commentaire` TEXT NOT NULL ";
    $req_add_rank = mysql_query($sql_request);
    if ($req_add_rank) {
        $result .= "<p style=\"color:green;\">Ajout du champ commentaire dans la table <strong>s_incidents</strong> : ok.</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Ajout du champ commentaire � la table <strong>s_incidents</strong> : Erreur.</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Ajout du champ commentaire � la table <strong>s_incidents</strong> : d�j� r�alis�.</p>";
}

//==========================================================
// Modification Delineau
$result .= "<br /><br /><strong>Ajout d'une table pour les \"super-gestionnaires\" d'AID :</strong><br />";
$result .= "<br />&nbsp;->Tentative de cr�ation de la table j_aidcateg_super_gestionnaires.<br />";
$test = sql_query1("SHOW TABLES LIKE 'j_aidcateg_super_gestionnaires'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS j_aidcateg_super_gestionnaires (indice_aid INT NOT NULL ,id_utilisateur VARCHAR( 50 ) NOT NULL);");
	if ($result_inter == '')
	$result .= msj_ok("La table j_aidcateg_super_gestionnaires a �t� cr��e !");
	else
	$result .= $result_inter."<br />";
} else {
		$result .= msj_present("La table j_aidcateg_super_gestionnaires existe d�j�.");
}

$champ_courant=array('nom1', 'prenom1', 'nom2', 'prenom2');
for($loop=0;$loop<count($champ_courant);$loop++) {
	$result .= "&nbsp;->Extension � 50 caract�res du champ '$champ_courant[$loop]' de la table 'responsables'<br />";
	$query = mysql_query("ALTER TABLE responsables CHANGE $champ_courant[$loop] $champ_courant[$loop] VARCHAR( 50 ) NOT NULL;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$champ_courant=array('nom', 'prenom');
for($loop=0;$loop<count($champ_courant);$loop++) {
	$result .= "&nbsp;->Extension � 50 caract�res du champ '$champ_courant[$loop]' de la table 'resp_pers'<br />";
	$query = mysql_query("ALTER TABLE resp_pers CHANGE $champ_courant[$loop] $champ_courant[$loop] VARCHAR( 50 ) NOT NULL;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

?>
