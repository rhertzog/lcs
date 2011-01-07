<?php
/*
 * $Id: eleves.php 6074 2010-12-08 15:43:17Z crob $
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

$liste_tables_del = array(
"absences",
"absences_gep",
"aid",
"aid_appreciations",
//"aid_config",
"avis_conseil_classe",
//"classes",
//"droits",
"eleves",
//"groupes",
"responsables",
"responsables2",
"resp_pers",
"resp_adr",
//"etablissements",
"j_aid_eleves",
"j_aid_utilisateurs",
"j_aid_eleves_resp",
"j_eleves_classes",
//==========================
// On ne vide plus la table chaque ann�e
// Probl�me avec Sconet qui r�cup�re seulement l'�tablissement de l'ann�e pr�c�dente qui peut �tre l'�tablissement courant
//"j_eleves_etablissements",
//==========================
"j_eleves_professeurs",
"j_eleves_regime",
"j_eleves_groupes",
//"j_groupes_professeurs",
//"j_groupes_classes",
//"j_groupes_matieres",
//"j_professeurs_matieres",
//"log",
//"matieres",
"matieres_appreciations",
"matieres_notes",
"matieres_appreciations_grp",
"matieres_appreciations_tempo",
//==========================
// Tables notanet
'notanet',
'notanet_avis',
'notanet_app',
'notanet_verrou',
'notanet_socles',
'notanet_ele_type',
//==========================
//"periodes",
"tempo2",
//"temp_gep_import",
"tempo",
//"utilisateurs",
"cn_cahier_notes",
"cn_conteneurs",
"cn_devoirs",
"cn_notes_conteneurs",
"cn_notes_devoirs",
//"setting"
);

//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'ann�e : Importation des �l�ves";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

?>
<script type="text/javascript">
<!--
function CocheCase(boul){
  len = document.formulaire.elements.length;
  for( i=0; i<len; i++) {
    if (document.formulaire.elements[i].type=='checkbox') {
      document.formulaire.elements[i].checked = boul ;
    }
  }
 }

function InverseSel(){
  len = document.formulaire.elements.length;
  for( i=0; i<len; i++) {
    if (document.formulaire.elements[i].type=='checkbox') {
      a=!document.formulaire.elements[i].checked  ;
      document.formulaire.elements[i].checked = a
    }
   }
}

function MetVal(cible){
len = document.formulaire.elements.length;
if ( cible== 'nom' ) {
  a=2;
  b=document.formulaire.nom.value;
  } else {
  a=3;
  b=document.formulaire.pour.value;
  }
for( i=0; i<len; i++) {
if ((document.formulaire.elements[i].type=='checkbox')
     &&
    (document.formulaire.elements[i].checked)
    ) {
document.formulaire.elements[i+a].value = b ;
}}}
 // -->
</script>

<?php



echo "<p class='bold'><a href='../init_scribe/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

if (isset($_POST['step'])) {
	check_token(false);

    // L'admin a valid� la proc�dure, on proc�de donc...
    include "../lib/eole_sync_functions.inc.php";

    // On se connecte au LDAP
    $ldap_server = new LDAPServer;


    //----***** STEP 1 *****-----//

    if ($_POST['step'] == "1") {
        // La premi�re �tape consiste � importer les classes

        if ($_POST['record'] == "yes") {
            // Les donn�es ont �t� post�es, on les traite donc imm�diatement

            $j=0;
            while ($j < count($liste_tables_del)) {
                if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
                    $del = @mysql_query("DELETE FROM $liste_tables_del[$j]");
                }
                $j++;
            }

                // On va enregistrer la liste des classes, ainsi que les p�riodes qui leur seront attribu�es
            $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(description=Classe*)");
            $data = ldap_get_entries($ldap_server->ds,$sr);

            for ($i=0;$i<$data["count"];$i++) {

                $classe = $data[$i]["cn"][0];

                // On enregistre la classe
                // On teste d'abord :
                $test = mysql_result(mysql_query("SELECT count(*) FROM classes WHERE (classe='$classe')"),0);

                if ($test == "0") {
                    //$reg_classe = mysql_query("INSERT INTO classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($_POST['reg_nom_complet'][$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($_POST['reg_suivi'][$classe]))."',formule='".traitement_magic_quotes(corriger_caracteres($_POST['reg_formule'][$classe]))."', format_nom='np'");
                    $reg_classe = mysql_query("INSERT INTO classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($_POST['reg_nom_complet'][$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($_POST['reg_suivi'][$classe]))."',formule='".html_entity_decode(traitement_magic_quotes(corriger_caracteres($_POST['reg_formule'][$classe])))."', format_nom='np'");
                } else {
                    //$reg_classe = mysql_query("UPDATE classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($_POST['reg_nom_complet'][$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($_POST['reg_suivi'][$classe]))."',formule='".traitement_magic_quotes(corriger_caracteres($_POST['reg_formule'][$classe]))."', format_nom='np' WHERE classe='$classe'");
                    $reg_classe = mysql_query("UPDATE classes SET classe='".traitement_magic_quotes(corriger_caracteres($classe))."',nom_complet='".traitement_magic_quotes(corriger_caracteres($_POST['reg_nom_complet'][$classe]))."',suivi_par='".traitement_magic_quotes(corriger_caracteres($_POST['reg_suivi'][$classe]))."',formule='".html_entity_decode(traitement_magic_quotes(corriger_caracteres($_POST['reg_formule'][$classe])))."', format_nom='np' WHERE classe='$classe'");
                }
                if (!$reg_classe) echo "<p>Erreur lors de l'enregistrement de la classe $classe.";

                // On enregistre les p�riodes pour cette classe
                // On teste d'abord :
                $id_classe = mysql_result(mysql_query("select id from classes where classe='$classe'"),0,'id');
                $test = mysql_result(mysql_query("SELECT count(*) FROM periodes WHERE (id_classe='$id_classe')"),0);
                if ($test == "0") {
                    $j = '0';
                    while ($j < $_POST['reg_periodes_num'][$classe]) {
                        $num = $j+1;
                        $nom_per = "P�riode ".$num;
                        if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                        $register = mysql_query("INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                        if (!$register) echo "<p>Erreur lors de l'enregistrement d'une p�riode pour la classe $classe";
                        $j++;
                    }
                } else {
                    // on "d�marque" les p�riodes des classes qui ne sont pas � supprimer
                    $sql = mysql_query("UPDATE periodes SET verouiller='N' where (id_classe='$id_classe' and num_periode='1')");
                    $sql = mysql_query("UPDATE periodes SET verouiller='O' where (id_classe='$id_classe' and num_periode!='1')");
                    //
                    $nb_per = mysql_num_rows(mysql_query("select num_periode from periodes where id_classe='$id_classe'"));
                    if ($nb_per > $_POST['reg_periodes_num'][$classe]) {
                        // Le nombre de p�riodes de la classe est inf�rieur au nombre enregistr�
                        // On efface les p�riodes en trop
                        $k = 0;
                        for ($k=$_POST['reg_periodes_num'][$classe]+1; $k<$nb_per+1; $k++) {
                            $del = mysql_query("delete from periodes where (id_classe='$id_classe' and num_periode='$k')");
                        }
                    }
                    if ($nb_per < $_POST['reg_periodes_num'][$classe]) {

                        // Le nombre de p�riodes de la classe est sup�rieur au nombre enregistr�
                        // On enregistre les p�riodes
                        $k = 0;
                        $num = $nb_per;
                        for ($k=$nb_per+1 ; $k < $_POST['reg_periodes_num'][$classe]+1; $k++) {
                            $num++;
                            $nom_per = "P�riode ".$num;
                            if ($num == "1") { $ver = "N"; } else { $ver = 'O'; }
                            $register = mysql_query("INSERT INTO periodes SET num_periode='$num',nom_periode='$nom_per',verouiller='$ver',id_classe='$id_classe'");
                            if (!$register) echo "<p>Erreur lors de l'enregistrement d'une p�riode pour la classe $classe";
                        }
                    }
                }
            }

            // On efface les classes qui ne sont pas r�utilis�es cette ann�e  ainsi que les entr�es correspondantes dans les groupes
            $sql = mysql_query("select distinct id_classe from periodes where verouiller='T'");
            $k = 0;
            while ($k < mysql_num_rows($sql)) {
               $id_classe = mysql_result($sql, $k);
               $res1 = mysql_query("delete from classes where id='".$id_classe."'");
               // On supprime les groupes qui �taient li�es � la classe
               $get_groupes = mysql_query("SELECT id_groupe FROM j_groupes_classes WHERE id_classe = '" . $id_classe . "'");
               for ($l=0;$l<$nb_groupes;$l++) {
                    $id_groupe = mysql_result($get_groupes, $l, "id_groupe");
                    $delete2 = mysql_query("delete from j_groupes_classes WHERE id_groupe = '" . $id_groupe . "'");
                    // On regarde si le groupe est toujours li� � une autre classe ou pas
                    $check = mysql_result(mysql_query("SELECT count(*) FROM j_groupes_classes WHERE id_groupe = '" . $id_groupe . "'"), 0);
                    if ($check == "0") {
                        $delete1 = mysql_query("delete from groupes WHERE id = '" . $id_groupe . "'");
                        $delete2 = mysql_query("delete from j_groupes_matieres WHERE id_groupe = '" . $id_groupe . "'");
                        $delete2 = mysql_query("delete from j_groupes_professeurs WHERE id_groupe = '" . $id_groupe . "'");
                    }
               }
               $k++;
            }
            $res = mysql_query("delete from periodes where verouiller='T'");
            echo "<p>Vous venez d'effectuer l'enregistrement des donn�es concernant les classes. S'il n'y a pas eu d'erreurs, vous pouvez aller � l'�tape suivante pour enregistrer les donn�es concernant les �l�ves.";
            echo "<center>";
            echo "<form enctype='multipart/form-data' action='eleves.php' method=post name='formulaire'>";
			echo add_token_field();
            echo "<input type=hidden name='record' value='no'>";
            echo "<input type=hidden name='step' value='2'>";
            echo "<input type='submit' value=\"Acc�der � l'�tape 2\">";
            echo "</form>";
            echo "</center>";

			// On sauvegarde le t�moin du fait qu'il va falloir
			// convertir pour g�n�rer l'ELE_ID et remplir ensuite les nouvelles tables responsables:
			saveSetting("conv_new_resp_table", 0);

        } else {
            // Les donn�es n'ont pas encore �t� post�es, on affiche donc le tableau des classes

            // On commence par "marquer" les classes existantes dans la base
            $sql = mysql_query("UPDATE periodes SET verouiller='T'");

            $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(description=Classe*)");
            $data = ldap_get_entries($ldap_server->ds,$sr);

            echo "<form enctype='multipart/form-data' action='eleves.php' method=post name='formulaire'>";
			echo add_token_field();
            echo "<input type=hidden name='record' value='yes'>";
            echo "<input type=hidden name='step' value='1'>";

            echo "<p>Les classes en vert indiquent des classes d�j� existantes dans la base GEPI.<br />Les classes en rouge indiquent des classes nouvelles et qui vont �tre ajout�es � la base GEPI.<br /></p>";
            echo "<p>Pour les nouvelles classes, des noms standards sont utilis�s pour les p�riodes (p�riode 1, p�riode 2...), et seule la premi�re p�riode n'est pas verrouill�e. Vous pourrez modifier ces param�tres ult�rieurement</p>";
            echo "<p>Attention !!! Il n'y a pas de tests sur les champs entr�s. Soyez vigilant � ne pas mettre des caract�res sp�ciaux dans les champs ...</p>";
            echo "<p>Essayez de remplir tous les champs, cela �vitera d'avoir � le faire ult�rieurement.</p>";
            echo "<p>N'oubliez pas <b>d'enregistrer les donn�es</b> en cliquant sur le bouton en bas de la page<br /><br />";

            ?>
            <fieldset style="padding-top: 8px; padding-bottom: 8px;  margin-left: 8px; margin-right: 100px;">
            <legend style="font-variant: small-caps;"> Aide au remplissage </legend>
            <table border="0">
            <tr>
              <td width="2%">&nbsp;</td>
              <td width="2%">&nbsp;</td>
              <td width="2%">&nbsp;</td>
              <td width="2%">&nbsp;</td>
              <td width="25%">&nbsp;</td>
              <td width="53%">&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="5">Vous pouvez remplir les cases <font color="red">
            une � une</font> et/ou <font color="red">globalement</font> gr�ce aux
            fonctionnalit�s offertes ci-dessous :</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">1) D'abord, cochez les lignes une � une</td>
            </tr>
              <tr>
              <td colspan="3">&nbsp;</td>
              <td colspan="3">Vous pouvez aussi &nbsp;
              <a href="javascript:CocheCase(true)">
              COCHER</a> ou
              <a href="javascript:CocheCase(false)">
              DECOCHER</a> toutes les lignes , ou
              <a href="javascript:InverseSel()">
              INVERSER </a>la s�lection</td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">2) Puis, pour les lignes coch�es :</td>
            </tr>
             <tr>
              <td colspan="4">&nbsp;</td>
              <td align="right">le nom au bas du bulletin sera &nbsp;:&nbsp;</td>
              <td><input type="text" name="nom" maxlength="80" size="40">
              <input type ="button" name="but_nom" value="Recopier"
            onclick="javascript:MetVal('nom')"></td>
             </td>
            </tr>
             <tr>
              <td colspan="4">&nbsp;</td>
              <td align="right">la formule au bas du bulletin sera
            &nbsp;:&nbsp;</td>
              <td><input type="text" name="pour" maxlength="80" size="40">
              <input type ="button" name="but_pour" value="Recopier"
            onclick="javascript:MetVal('pour')"></td>
             </td>
            </tr>
            <tr>
              <td colspan="2">&nbsp;</td>
              <td colspan="4">3) Cliquez sur les boutons "Recopier" pour remplir les champs selectionn�s.</td>
            </tr>

            </table>
            </fieldset>
            <br />
            <?php
            echo "<table border=1 cellpadding=2 cellspacing=2>";
            echo "<tr><td><p class=\"small\"><center>Aide<br />Remplissage</center></p></td><td><p class=\"small\">Identifiant de la classe</p></td><td><p class=\"small\">Nom complet</p></td><td><p class=\"small\">Nom apparaissant au bas du bulletin</p></td><td><p class=\"small\">formule au bas du bulletin</p></td><td><p class=\"small\">Nombres de p�riodes</p></td></tr>";
            for ($i=0;$i<$data["count"];$i++) {
                $classe_id = $data[$i]["cn"][0];
                $test_classe_exist = mysql_query("SELECT * FROM classes WHERE classe='$classe_id'");
                $nb_test_classe_exist = mysql_num_rows($test_classe_exist);

                if ($nb_test_classe_exist==0) {
                    $nom_complet = $classe_id;
                    $nom_court = "<font color=red>".$classe_id."</font>";
                    $suivi_par = getSettingValue("gepiAdminPrenom")." ".getSettingValue("gepiAdminNom").", ".getSettingValue("gepiAdminFonction");
                    $formule = "";
                    $nb_per = '3';
                } else {
                    $id_classe = mysql_result($test_classe_exist, 0, 'id');
                    $nb_per = mysql_num_rows(mysql_query("select num_periode from periodes where id_classe='$id_classe'"));
                    $nom_court = "<font color=green>".$classe_id."</font>";
                    $nom_complet = mysql_result($test_classe_exist, 0, 'nom_complet');
                    $suivi_par = mysql_result($test_classe_exist, 0, 'suivi_par');
                    $formule = mysql_result($test_classe_exist, 0, 'formule');
                }
                echo "<tr>";
                echo "<td><center><input type=\"checkbox\"></center></td>\n";
                echo "<td>";
                echo "<p><b><center>$nom_court</center></b></p>";
                echo "";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_nom_complet[$classe_id]' value=\"".$nom_complet."\">\n";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_suivi[$classe_id]' value=\"".$suivi_par."\">\n";
                echo "</td>";
                echo "<td>";
                echo "<input type=text name='reg_formule[$classe_id]' value=\"".$formule."\">\n";
                echo "</td>";
                echo "<td>";
                echo "<select size=1 name='reg_periodes_num[$classe_id]'>\n";
                for ($k=1;$k<7;$k++) {
                    echo "<option value='$k'";
                    if ($nb_per == "$k") echo " SELECTED";
                    echo ">$k";
                }
                echo "</select>";
                echo "</td></tr>";
            }
            echo "</table>\n";
            echo "<input type=hidden name='step2' value='y'>\n";
            echo "<center><input type='submit' value='Enregistrer les donn�es'></center>\n";
            echo "</form>\n";

        }



    //----***** STEP 2 *****-----//

    } elseif ($_POST['step'] == "2") {
        // La deuxi�me �tape consiste � importer les �l�ves et � les affecter dans les classes

        // On cr�� un tableau avec tous les professeurs principaux de chaque classe

        $classes = mysql_query("SELECT id, classe FROM classes");
        $nb_classes = mysql_num_rows($classes);
        $pp = array();
        for ($i=0;$i<$nb_classes;$i++) {
            $current_classe = mysql_result($classes, $i, "classe");
            $current_classe_id = mysql_result($classes, $i, "id");
            $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(&(objectClass=administrateur)(divcod=" . $current_classe ."))");
            $prof = ldap_get_entries($ldap_server->ds,$sr);
            if (array_key_exists(0, $prof)) {
                $pp[$current_classe_id] = $prof[0]["uid"][0];
            }
        }

        // Debug profs principaux
        //echo "<pre>";
        //print_r($pp);
        //echo "</pre>";

        $sr = ldap_search($ldap_server->ds,$ldap_server->base_dn,"(&(uid=*)(objectClass=Eleves))");
        $info = ldap_get_entries($ldap_server->ds,$sr);

        for($i=0;$i<$info["count"];$i++) {

            // On ajoute l'utilisateur. La fonction s'occupe toute seule de v�rifier que
            // le login n'existe pas d�j� dans la base. S'il existe, on met simplement � jour
            // les informations

            // function add_eleve($_login, $_nom, $_prenom, $_sexe, $_naissance, $_elenoet) {

            $date_naissance = substr($info[$i]["datenaissance"][0], 0, 4) . "-" .
                                substr($info[$i]["datenaissance"][0], 4, 2) . "-" .
                                substr($info[$i]["datenaissance"][0], 6, 2);

            // -----
            // DEPRECIATION : les lignes ci-dessous ne sont plus n�cessaire, Gepi a �t� mis � jour
            //
            // Pour des raisons de compatibilit� avec le code existant de Gepi, il n'est pas possible d'avoir
            // un point dans le login... (le point est transform� bizarrement en "_" dans les $_POST)...

            //$info[$i]["uid"][0] = preg_replace("/\./", "_", $info[$i]["uid"][0]);
			// -----

            // En th�orie ici chaque login est de toute fa�on unique.
            $add = add_eleve($info[$i]["uid"][0],
                            $info[$i]["sn"][0],
                            $info[$i]["givenname"][0],
                            $info[$i]["codecivilite"][0],
                            $date_naissance);
                            //$info[$i]["employeenumber"]);

            $id_classe = mysql_result(mysql_query("SELECT id FROM classes WHERE classe = '" . $info[$i]["divcod"][0] . "'"), 0);

            $check = mysql_result(mysql_query("SELECT count(*) FROM j_eleves_professeurs WHERE (login = '" . $info[$i]["uid"][0] . "')"), 0);
            if ($check > 0) {
                $del = mysql_query("DELETE from j_eleves_professeurs WHERE login = '" . $info[$i]["uid"][0] . "'");
            }
            if (array_key_exists($id_classe, $pp)) {
                //echo "Debug : $pp[$id_classe]<br/>";
                $res = mysql_query("INSERT INTO j_eleves_professeurs SET login = '" . $info[$i]["uid"][0] . "', id_classe = '" . $id_classe . "', professeur = '" . $pp[$id_classe] . "'");
            }

            $get_periode_num = mysql_result(mysql_query("SELECT count(*) FROM periodes WHERE (id_classe = '" . $id_classe . "')"), 0);

            $check = mysql_result(mysql_query("SELECT count(*) FROM j_eleves_classes WHERE (login = '" . $info[$i]["uid"][0] . "')"), 0);
            if ($check > 0) {
                $del = mysql_query("DELETE from j_eleves_classes WHERE login = '" . $info[$i]["uid"][0] . "'");
            }

            for ($k=1;$k<$get_periode_num+1;$k++) {
                $res = mysql_query("INSERT into j_eleves_classes SET login = '" . $info[$i]["uid"][0] . "', id_classe = '" . $id_classe . "', periode = '" . $k . "'");
            }

            echo "<br/>Login �l�ve : " . $info[$i]["uid"][0] . "  ---  " . $date_naissance . " --- Classe " . $info[$i]["divcod"][0];
        }

        echo "<p>Op�ration effectu�e.</p>";
        echo "<p>Vous pouvez v�rifier l'importation en allant sur la page de <a href='../eleves/index.php'>gestion des eleves</a>.</p>";
        echo "<br />";
        echo "<p><center><a href='professeurs.php'>Phase suivante : importation des professeurs</a></center></p>";
    }

} else {

    echo "<p>L'op�ration d'importation des �l�ves depuis le LDAP de Scribe va effectuer les op�rations suivantes :</p>";
    echo "<ul>";
    echo "<li>Importation des classes</li>";
    echo "<li>Tentative d'ajout de chaque �l�ves pr�sent dans le LDAP</li>";
    echo "<li>Si l'utilisateur n'existe pas, il est cr�� et est directement utilisable</li>";
    echo "<li>Si l'utilisateur existe d�j�, ses informations de base sont mises � jour et il passe en �tat 'actif', devenant directement utilisable</li>";
    echo "<li>Affectation des �l�ves aux classes</li>";
    echo "</ul>";
    echo "<form enctype='multipart/form-data' action='eleves.php' method=post>";
	echo add_token_field();
    echo "<input type=hidden name='step' value='1'>";
    echo "<input type=hidden name='record' value='no'>";
    $j=0;
    $flag=0;
    while (($j < count($liste_tables_del)) and ($flag==0)) {
        if (mysql_result(mysql_query("SELECT count(*) FROM $liste_tables_del[$j]"),0)!=0) {
            $flag=1;
        }
        $j++;
    }
    if ($flag != 0){
        echo "<p><b>ATTENTION ...</b><br />";
        echo "Des donn�es concernant la constitution des classes et l'affectation des �l�ves dans les classes sont pr�sentes dans la base GEPI ! Si vous poursuivez la proc�dure, ces donn�es seront d�finitivement effac�es !</p>";
    }

    echo "<p>Etes-vous s�r de vouloir importer tous les �l�ves depuis l'annuaire du serveur Scribe vers Gepi ?</p>";
    echo "<br/>";
    echo "<input type='submit' value='Je suis s�r'>";
    echo "</form>";
}
require("../lib/footer.inc.php");
?>