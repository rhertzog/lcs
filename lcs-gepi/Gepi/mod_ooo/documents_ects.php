<?php
/*
 * $Id: saisie_avis.php 2147 2008-07-23 09:01:04Z tbelliard $
 *
 * Copyright 2001, 2009 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

// Remplacement des anciennes versions vers la nouvelle lib TinyDoc
//include_once('./lib/lib_mod_ooo.php');
//include_once('./lib/tbs_class.php');
//include_once('./lib/tbsooo_class.php');

include_once('./lib/tinyButStrong.class.php');
include_once('./lib/tinyDoc.class.php');



define( 'PCLZIP_TEMPORARY_DIR', '../mod_ooo/tmp/' );
include_once('../lib/pclzip.lib.php');


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// On teste si un professeur principal peut effectuer l'�dition
if (($_SESSION['statut'] == 'professeur') and $gepiSettings["GepiAccesEditionDocsEctsPP"] !='yes') {
   die("Droits insuffisants pour effectuer cette op�ration");
}

// On teste si le service scolarit� peut effectuer la saisie
if (($_SESSION['statut'] == 'scolarite') and $gepiSettings["GepiAccesEditionDocsEctsScolarite"] !='yes') {
   die("Droits insuffisants pour effectuer cette op�ration");
}

$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : false;
$choix_edit = isset($_POST["choix_edit"]) ? $_POST["choix_edit"] : false;
$login_eleve = isset($_POST["login_eleve"]) ? $_POST["login_eleve"] : false;
$page_garde = isset($_POST['page_garde']) ? true : false;
$releve = isset($_POST['releve']) ? true : false;
$attestation = isset($_POST['attestation']) ? true : false;
$description = isset($_POST['description']) ? true : false;
$date_edition = isset($_POST['date_edition']) ? $_POST['date_edition'] : false;
$lieu_edition =isset($_POST['lieu_edition']) ? $_POST['lieu_edition'] : false;

// On va g�n�rer un gros tableau avec toutes les donn�es.

// Tableau global
$eleves = array();

if ($id_classe == 'all') {
    // On doit r�cup�rer la totalit� des �l�ves, pour les classes de l'utilisateur
    if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours')) {
        // On ne s�lectionne que les classes qui ont au moins un enseignement ouvrant � cr�dits ECTS
        if($_SESSION['statut']=='scolarite'){
            $call_classes = mysql_query("SELECT DISTINCT c.id
                                        FROM classes c, periodes p, j_scol_classes jsc, j_groupes_classes jgc
                                        WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' AND c.id=jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
        } else {
            $call_classes = mysql_query("SELECT DISTINCT c.id FROM classes c, periodes p, j_groupes_classes jgc WHERE p.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE ORDER BY classe");
        }
    } else {
        $call_classes = mysql_query("SELECT DISTINCT c.id FROM classes c, j_eleves_professeurs s, j_eleves_classes cc, j_groupes_classes jgc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id AND c.id = jgc.id_classe AND jgc.saisie_ects = TRUE)");
    }
    $nb_classes = mysql_num_rows($call_classes);
    $Eleves = array();
    for($i=0;$i<$nb_classes;$i++) {
        $Classe = ClassePeer::retrieveByPK(mysql_result($call_classes, $i, 'id'));
        if ($_SESSION['statut'] == 'scolarite' OR $_SESSION['statut'] == 'secours') {
            $Eleves = array_merge($Eleves,$Classe->getEleves('1'));
        } else {
            $Eleves = array_merge($Eleves,$Classe->getElevesByProfesseurPrincipal($_SESSION['login']));
        }
    }
} else {
    if ($choix_edit && $choix_edit == '2') {
        $Eleves = array();
        $Eleves[] = ElevePeer::retrieveByLOGIN($login_eleve);
    } else {
        $Classe = ClassePeer::retrieveByPK($id_classe);
        if ($_SESSION['statut'] == 'scolarite' OR $_SESSION['statut'] == 'secours') {
            $Eleves = $Classe->getEleves('1');
        } else {
            $Eleves = $Classe->getElevesByProfesseurPrincipal($_SESSION['login']);
        }
    }
}

$i = 0;
$mentions = array('A' => 'Tr�s bien', 'B' => 'Bien', 'C' => 'Assez Bien', 'D' => 'Convenable', 'E' => 'Passable', 'F' => 'Insuffisant');
$resultats = array();
//$recap_annees = array();
foreach($Eleves as $Eleve) {
    // On est dans la boucle principale. Le premier tableau contient les informations relatives � l'�l�ve.
    // C'est le premier bloc.
    $classes = $Eleve->getClasses('1');
    $Classe = $classes[0];
    if ($Eleve->getSexe() == 'F') {
        $naissance = "n�e le ".$Eleve->getNaissance();
    } else {
        $naissance = "n� le ".$Eleve->getNaissance();
    }

    $adresse_etablissement = $gepiSettings['gepiSchoolName'];
    if ($gepiSettings['gepiSchoolAdress1'] != '') $adresse_etablissement.= ', '.$gepiSettings['gepiSchoolAdress1'];
    if ($gepiSettings['gepiSchoolAdress2'] != '') $adresse_etablissement.= ', '.$gepiSettings['gepiSchoolAdress2'];
    $adresse_etablissement.= ', '.$gepiSettings['gepiSchoolZipCode'].' '.$gepiSettings['gepiSchoolCity'];
    if ($gepiSettings['gepiSchoolPays'] != '') $adresse_etablissement.= ', '.$gepiSettings['gepiSchoolPays'];
    $credit_global = $Eleve->getCreditEctsGlobal();
    if ($credit_global == null) {
        $mention_globale = 'A';
    } else {
        $mention_globale = $credit_global->getMention();
    }


    // Gestion du bloc adresse.
    // Le code ci-dessous est repris directement des bulletins... pas le temps
    // de faire un truc g�n�rique propre :-(

    $sql="SELECT rp.nom, rp.prenom, rp.civilite, ra.* FROM responsables2 r, resp_pers rp, resp_adr ra
					WHERE r.ele_id='".$Eleve->getEleId()."' AND
						rp.adr_id=ra.adr_id AND
						r.pers_id=rp.pers_id AND
						(r.resp_legal='1' OR r.resp_legal='2')
					ORDER BY r.resp_legal";

    $call_resp=@mysql_query($sql);

    $nom_resp=array();
    $prenom_resp=array();
    $civilite_resp=array();
    $adr1_resp=array();
    $adr2_resp=array();
    $adr3_resp=array();
    $adr4_resp=array();
    $cp_resp=array();
    $commune_resp=array();
    $pays_resp=array();
    $cpt=1;
    while($lig_resp=mysql_fetch_object($call_resp)){
            $nom_resp[$cpt]=$lig_resp->nom;
            $prenom_resp[$cpt]=$lig_resp->prenom;
            $civilite_resp[$cpt]=$lig_resp->civilite;
            $adr1_resp[$cpt]=$lig_resp->adr1;
            $adr2_resp[$cpt]=$lig_resp->adr2;
            $adr3_resp[$cpt]=$lig_resp->adr3;
            $adr4_resp[$cpt]=$lig_resp->adr4;
            $cp_resp[$cpt]=$lig_resp->cp;
            $commune_resp[$cpt]=$lig_resp->commune;
            $pays_resp[$cpt]=$lig_resp->pays;
            $cpt++;
    }

    // On a d�sormais toutes les infos concernant le ou les responsables
    // l�gaux de l'�l�ve.

    if ($nom_resp[1]=='') {
            // Si on n'a pas de nom pour le responsable 1, on n'affiche rien.
            $ligne1="";
            $ligne2="";
            $ligne3="";
    } else {
            if((isset($adr1_resp[2]))&&(isset($adr2_resp[2]))&&(isset($adr3_resp[2]))&&(isset($cp_resp[2]))&&(isset($commune_resp[2]))) {
                    if((
                    (substr($adr1_resp[1],0,strlen($adr1_resp[1])-1)==substr($adr1_resp[2],0,strlen($adr1_resp[2])-1))
                    and (substr($adr2_resp[1],0,strlen($adr2_resp[1])-1)==substr($adr2_resp[2],0,strlen($adr2_resp[2])-1))
                    and (substr($adr3_resp[1],0,strlen($adr3_resp[1])-1)==substr($adr3_resp[2],0,strlen($adr3_resp[2])-1))
                    and (substr($adr4_resp[1],0,strlen($adr4_resp[1])-1)==substr($adr4_resp[2],0,strlen($adr4_resp[2])-1))
                    and ($cp_resp[1]==$cp_resp[2])
                    and ($commune_resp[1]==$commune_resp[2])
                    and ($pays_resp[1]==$pays_resp[2])
                    )
                    and ($adr1_resp[2]!='')) {
                    // Les deux responsables l�gaux ont la m�me adresse

                            if(($nom_resp[1]!=$nom_resp[2])&&($nom_resp[2]!="")) {
                                // Les deux responsables l�gaux n'ont pas le m�me nom
                                    $ligne1=$civilite_resp[1]." ".$nom_resp[1]." ".$prenom_resp[1];
                                    $ligne1.="<br />\n";
                                    $ligne1.="et ";
                                    $ligne1.=$civilite_resp[2]." ".$nom_resp[2]." ".$prenom_resp[2];
                            } else {
                                // Ils ont le m�me nom
                                    if(($civilite_resp[1]!="")&&($civilite_resp[2]!="")) {
                                            $ligne1=$civilite_resp[1]." et ".$civilite_resp[2]." ".$nom_resp[1]." ".$prenom_resp[1];
                                    } else {
                                            $ligne1="M. et Mme ".$nom_resp[1]." ".$prenom_resp[1];
                                    }
                            }
                    } elseif ($civilite_resp[1]!="") {
                            $ligne1=$civilite_resp[1]." ".$nom_resp[1]." ".$prenom_resp[1];
                    } else {
                            $ligne1=$nom_resp[1]." ".$prenom_resp[1];
                    }
            } else {
                    if($civilite_resp[1]!=""){
                            $ligne1=$civilite_resp[1]." ".$nom_resp[1]." ".$prenom_resp[1];
                    } else {
                            $ligne1=$nom_resp[1]." ".$prenom_resp[1];
                    }
            }
            $ligne2=$adr1_resp[1];
            if($adr2_resp[1]!=""){
                    $ligne2.="<br />\n".$adr2_resp[1];
            }
            if($adr3_resp[1]!=""){
                    $ligne2.="<br />\n".$adr3_resp[1];
            }
            if($adr4_resp[1]!=""){
                    $ligne2.="<br />\n".$adr4_resp[1];
            }
            $ligne3=$cp_resp[1]." ".$commune_resp[1];
            if(($pays_resp[1]!="")&&(strtolower($pays_resp[1])!=strtolower($gepiSettings['gepiSchoolPays']))) {
                    if($ligne3!=" "){
                            $ligne3.="<br />";
                    }
                    $ligne3.="$pays_resp[1]";
            }
    }

    switch($gepiSettings['gepiSchoolStatut']){
        case 'public':
            $statut_etab = 'public';
            break;
        case 'prive_sous_contrat':
            $statut_etab = 'priv� sous contrat';
            break;
        case 'prive_hors_contrat':
            $statut_etab = 'priv� hors contrat';
            break;
        default:
            $statut_etab = 'public';
    }

    $eleves[$i] = array(
                        'nom' => $Eleve->getNom(),
                        'prenom' => $Eleve->getPrenom(),
                        'ine' => $Eleve->getNoGep(),
                        'date_naissance' => $naissance,
                        'mention_globale' => $mentions[$mention_globale],
                        'mention_globale_lettre' => $mention_globale,
                        'parcours' => $Classe->getEctsParcours(),
                        'code_parcours' => $Classe->getEctsCodeParcours(),
                        'type_formation' => $Classe->getEctsTypeFormation(),
                        'domaines_etude' => $Classe->getEctsDomainesEtude(),
                        'fonction_signataire' => $Classe->getEctsFonctionSignataireAttestation(),
                        'nom_signataire' => $Classe->getSuiviPar(),
                        'date' => $date_edition,
                        'academie' => $gepiSettings['gepiSchoolAcademie'],
                        'etablissement' => $gepiSettings['gepiSchoolName'],
                        'ville_etab' => $gepiSettings['gepiSchoolCity'],
                        'statut_etab' => $statut_etab,
                        'rne_etab' => $gepiSettings['gepiSchoolRne'],
                        'code_postal_etab' => $gepiSettings['gepiSchoolZipCode'],
                        'lieu_edition' => $lieu_edition,
                        'adresse_etab' => $adresse_etablissement,
                        'resp_ligne1' => $ligne1,
                        'resp_ligne2' => $ligne2,
                        'resp_ligne3' => $ligne3
                );

    // Tableau qui contient le total g�n�ral des cr�dits de l'�tudiant
    $total_credits = 0;

    // On commence par les ann�es archiv�es
    // On r�cup�re la liste des ann�es archiv�es pour l'�l�ve
    $annees = mysql_query("SELECT DISTINCT(a.annee) FROM archivage_ects a WHERE a.ine = '".$Eleve->getNoGep()."'");
    $annees_archivees = array();
    $nb_annees = mysql_num_rows($annees);
    for ($a=0;$a<$nb_annees;$a++) {
        $annees_archivees[] = mysql_result($annees, $a);
    }

    // Tableau qui contient le total des cr�dits par ann�e
    $total_credits_annees = array();
    foreach($annees_archivees as $annee_archive) {
        $total_credits_annees[$annee_archive] = 0;
    }
    $total_credits_annees[$gepiSettings['gepiYear']] = 0;

    // Boucle de traitement des archives
    $periode_courante = 1;
    foreach($annees_archivees as $annee_archive) {
        //TODO: Pour l'instant on laisse en dur un nombre de p�riodes maxi de 5
        // Il faudrait sans doute am�liorer le syst�me.
        for($p=1;$p<=5;$p++) {
            $semestres[$periode_courante] = array();
            $flag = false;
            foreach($Eleve->getArchivedEctsCredits($annee_archive, $p) as $Credit) {
                $valeur = $Credit ? $Credit->getValeur() : 'Non saisie';
                $mention = $Credit ? $Credit->getMention() : 'Non saisie';
                $semestres[$periode_courante][] = array(
                                    'discipline' => $Credit->getMatiere(),
                                    'ects_credit' => $valeur,
                                    'ects_mention' => $mention);
                $total_credits = $total_credits + $valeur;
                $total_credits_annees[$annee_archive] = $total_credits_annees[$annee_archive] + $valeur;
                $flag = true;
            }
            // On incr�mente le semestre si des valeurs avaient �t� trouv�es
            // pour la p�riode consid�r�e. Ce fonctionnement permet de jouer
            // sur des p�riodes sp�cifiques pour les ECTS, et d'avoir des relev�s
            // lin�aires en apparence.
            if ($flag) $periode_courante++;
        }
    }

    //TODO: On consid�re en dur seulement cinq p�riodes pour l'ann�e en cours.
    // A reprendre peut-�tre...
    for ($p=1;$p<=5;$p++) {
        $semestres[$periode_courante] = array();
        $flag = false;
        foreach($Eleve->getEctsGroupes($p) as $Group) {
            $Credit = $Eleve->getEctsCredit($p,$Group->getId());
            if ($Credit) {
                $valeur = $Credit ? $Credit->getValeur() : 'Non saisie';
                $mention = $Credit ? $Credit->getMention() : 'Non saisie';

                $semestres[$periode_courante][] = array(
                                    'discipline' => $Group->getDescription(),
                                    'ects_credit' => $valeur,
                                    'ects_mention' => $mention);
                $total_credits = $total_credits + $valeur;
                $total_credits_annees[$gepiSettings['gepiYear']] = $total_credits_annees[$gepiSettings['gepiYear']] + $valeur;
                $flag = true;
            }
        }
        // On incr�mente le semestre si on avait des donn�es.
        if ($flag) $periode_courante++;
    }

    $eleves[$i]['total_credits'] = $total_credits;

    // On filtre maintenant les p�riodes : on ne devra afficher que les p�riodes
    // pour lesquelles il y a des r�sultats saisis.
    $denomination_periodes = array(1 => 'Premier semestre', 2 => 'Deuxi�me semestre', 3 => 'Troisi�me semestre', 4 => 'Quatri�me semestre', 5 => 'Cinqui�me semestre', 6 => 'Sixi�me semestre');
    $resultats[$i] = array();

    foreach($semestres as $num_semestre => $semestre) {
        foreach($semestre as &$tab) {
            $tab['periode'] = $denomination_periodes[$num_semestre];
            array_push($resultats[$i],$tab);
        }
    }

    $recap_annees[$i] = array();
    foreach($total_credits_annees as $annee_courante => $credits_courants) {
        $recap_annees[$i][] = array('texte' => 'Ann�e acad�mique '.$annee_courante." : ".$credits_courants." ECTS");
    }
    // Fin de la boucle �l�ve
    $i++;
}

//echo "<pre>";
//print_r($resultats);
//echo "</pre>";

// Et maintenant on s'occupe du fichier proprement dit

//
//Les variables � modifier pour le traitement  du mod�le ooo
//
//Le chemin et le nom du fichier ooo � traiter (le mod�le de document)
$nom_fichier_modele_ooo ='documents_ects.odt';
// Par defaut tmp
$nom_dossier_temporaire ='tmp';
//par defaut content.xml
$nom_fichier_xml_a_traiter ='content.xml';


//Proc�dure du traitement � effectuer
//les chemins contenant les donn�es
include_once ("./lib/chemin.inc.php");


// instantiate a TBS OOo class
$OOo = new tinyDoc();
$OOo->setZipMethod('shell');
$OOo->setZipBinary('zip');
$OOo->setUnzipBinary('unzip');

// setting the object
$OOo->SetProcessDir($nom_dossier_temporaire ); //dossier o� se fait le traitement (d�compression / traitement / compression)
// create a new openoffice document from the template with an unique id
$OOo->createFrom($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo); // le chemin du fichier est indiqu� � partir de l'emplacement de ce fichier
// merge data with openoffice file named 'content.xml'
$OOo->loadXml($nom_fichier_xml_a_traiter); //Le fichier qui contient les variables et doit �tre pars� (il sera extrait)



// Traitement des tableaux
// On ins�re ici les lignes concernant la gestion des tableaux
if (!$page_garde) {
    $OOo->mergeXml(
        array(
            'name' => 'page_garde',
            'type' => 'clear'));
} else {
    $OOo->mergeXmlBlock('page_garde',array('fake')); // Juste pour que le bloc s'initialise correctement
}
if (!$releve) {
    $OOo->mergeXml(
        array(
            'name' => 'releve',
            'type' => 'clear'));
} else {
    $OOo->mergeXmlBlock('releve',array('fake')); // Juste pour que le bloc s'initialise correctement
}
if (!$attestation) {
    $OOo->mergeXml(
        array(
            'name' => 'attestation',
            'type' => 'clear'));
} else {
    $OOo->mergeXmlBlock('attestation',array('fake')); // Juste pour que le bloc s'initialise correctement
}
if (!$description) {
    $OOo->mergeXml(
        array(
            'name' => 'description',
            'type' => 'clear'));
} else {
    $OOo->mergeXmlBlock('description',array('fake')); // Juste pour que le bloc s'initialise correctement
}

$OOo->mergeXml(
    array(
      'name'      => 'eleves',
      'type'      => 'block',
      'data_type' => 'array',
      'charset'   => 'ISO 8859-15'
    ),$eleves);


// On ins�re les r�sultats
$OOo->mergeXml(
    array(
      'name'      => 'resultats',
      'type'      => 'block',
      'data_type' => 'array',
      'charset'   => 'ISO 8859-15'
    ),'resultats[%p1%]');

// On ins�re le r�capitulatif des ann�es
$OOo->mergeXml(
    array(
      'name'      => 'recap_annees',
      'type'      => 'block',
      'data_type' => 'array',
      'charset'   => 'ISO 8859-15'
    ),'recap_annees[%p1%]');

$nom_fic_logo = getSettingValue("logo_etab");
$nom_fic_logo_c = "../images/".$nom_fic_logo;
if (($nom_fic_logo != '') and (file_exists($nom_fic_logo_c))) {
    $OOo->mergeXmlField('logo',$nom_fic_logo_c);
} else {
    $OOo->mergeXmlField('logo','../images/blank.gif'); 
}


// Fin de traitement des tableaux


$OOo->saveXml(); //traitement du fichier extrait
$OOo->close();

//G�n�ration du nom du fichier
$now = gmdate('d_M_Y_H:i:s');
$nom_fichier_modele = explode('.',$nom_fichier_modele_ooo);
$nom_fic = $nom_fichier_modele[0]."_g�n�r�_le_".$now.".".$nom_fichier_modele[1];
header('Expires: ' . $now);
if (my_ereg('MSIE', $_SERVER['HTTP_USER_AGENT'])) {
    header('Content-Disposition: inline; filename="' . $nom_fic . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
} else {
    header('Content-Disposition: attachment; filename="' . $nom_fic . '"');
    header('Pragma: no-cache');
}

// display
header('Content-type: '.$OOo->getMimetype());
header('Content-Length: '.filesize($OOo->getPathname()));



// send and remove the document
$OOo->sendResponse();
$OOo->remove();
// Fin de traitement des tableaux
?>