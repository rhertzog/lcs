<?php
/*
 * $Id: import_note_app.php 3323 2009-08-05 10:06:18Z crob $
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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



$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
	$current_group = get_group($id_groupe);
} else {
	$current_group = false;
}

$periode_num = isset($_POST['periode_num']) ? $_POST['periode_num'] : (isset($_GET['periode_num']) ? $_GET['periode_num'] : NULL);
if (!is_numeric($periode_num)) $periode_num = 0;

if ($_SESSION['statut'] != "secours") {
    if (!(check_prof_groupe($_SESSION['login'],$current_group["id"]))) {
        $mess=rawurlencode("Vous n'�tes pas professeur de cet enseignement !");
        header("Location: index.php?msg=$mess");
        die();
    }
}

include "../lib/periodes.inc.php";

//**************** EN-TETE *****************
$titre_page = "Saisie des moyennes et appr�ciations | Importation";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// $long_max : doit �tre plus grand que la plus grande ligne trouv�e dans le fichier CSV
$long_max = 8000;

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil saisie</a>";
//====================================
if($_SESSION['statut']=='professeur'){
	//$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";


    $tab_groups = get_groups_for_prof($_SESSION["login"],"classe puis mati�re");
    //$tab_groups = get_groups_for_prof($_SESSION["login"]);

	if(!empty($tab_groups)) {
		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		//foreach($tab_groups as $tmp_group) {
		for($loop=0;$loop<count($tab_groups);$loop++) {
			if($tab_groups[$loop]['id']==$id_groupe){
				$temoin_tmp=1;
				if(isset($tab_groups[$loop+1])){
					$id_grp_suiv=$tab_groups[$loop+1]['id'];
				}
				else{
					$id_grp_suiv=0;
				}
			}
			if($temoin_tmp==0){
				$id_grp_prec=$tab_groups[$loop]['id'];
			}
		}
		// =================================

		if(isset($id_grp_prec)){
			if($id_grp_prec!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_num=$periode_num";
				echo "'>Enseignement pr�c�dent</a>";
			}
		}
		if(isset($id_grp_suiv)){
			if($id_grp_suiv!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_num=$periode_num";
				echo "'>Enseignement suivant</a>";
				}
		}
	}
	// =================================
}
//====================================
echo "</p>\n";

echo "<p><span class = 'grand'>Premi�re phase d'importation des moyennes et appr�ciations </span>";
//echo "<p class = 'bold'>Groupe : " . $current_group["description"] ." (" . $current_group["classlist_string"] . ")| Mati�re : " . $current_group["matiere"]["nom_complet"] . " | P�riode : $nom_periode[$periode_num]</p>";
echo "<p class = 'bold'>Groupe : " . htmlentities($current_group["description"]) ." (" . $current_group["classlist_string"] . ")| Mati�re : " . htmlentities($current_group["matiere"]["nom_complet"]) . " | P�riode : $nom_periode[$periode_num]";
echo "</p>\n";


if (!isset($is_posted)) {
    ?>
    <form enctype="multipart/form-data" action="import_note_app.php" method=post name=formulaire>
    <?php $csv_file=""; ?>
    <p>Fichier CSV � importer : <input type='file' name="csv_file" />    <input type='submit' value='Ouvrir' /></p>
    <p>Si le fichier � importer comporte une premi�re ligne d'en-t�te (non vide) � ignorer, <br />cocher la case ci-contre&nbsp;
    <input type='checkbox' name="en_tete" value="yes" checked /></p>
    <input type='hidden' name=is_posted value = 1 />
    <?php
    echo "<input type='hidden' name='id_groupe' value='" . $id_groupe . "' />\n";
    echo "<input type='hidden' name='periode_num' value='" . $periode_num . "' />\n";
    ?>
    </form>
    <?php
    echo "<p>Vous avez d�cid� d'importer directement un fichier de moyennes et/ou d'appr�ciations. Le fichier d'importation doit �tre au format csv (s�parateur : point-virgule) et doit contenir les trois champs suivants :<br />\n";
    echo "--> <B>IDENTIFIANT</B> : L'identifiant GEPI de l'�l�ve (<b>voir les explications plus bas</b>).<br />\n";
    echo "--> <B>NOTE</B> : note entre 0 et 20 avec le point ou la virgule comme symbole d�cimal.<br />Autres codes possibles (sans les guillemets) : \"<b>abs</b>\" pour \"absent\", \"<b>disp</b>\" pour \"dispens�\", \"<b>-</b>\" pour absence de note.<br />Si ce champ est vide, Il n'y aura pas modification de la note d�j� enregistr�e dans GEPI pour l'�l�ve en question.<br />\n";
    echo "--> <B>Appr�ciation</B> : le texte de l'appr�ciation de l'�l�ve.<br />Si ce champ est vide, Il n'y aura pas modification de l'appr�ciation enregistr�e dans GEPI pour l'�l�ve en question.</p>\n";
    echo "<p>Pour constituer le fichier d'importation vous avez besoin de conna�tre l'identifiant <b>GEPI</b> de chaque �l�ve. Vous pouvez t�l�charger:</p>\n";
    echo "<ul>\n";
    echo "<li>le fichier �l�ves (identifiant GEPI, sans nom et pr�nom) en <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;champs=3&amp;ligne_entete=y&amp;mode=Id_Note_App'><b>cliquant ici</b></a></li>\n";
    echo "<li>ou bien le fichier �l�ves (nom - pr�nom - identifiant GEPI) en <a href='import_class_csv.php?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;champs=5&amp;ligne_entete=y&amp;mode=Nom_Prenom_Id_Note_App'><b>cliquant ici</b></a><br />(<i>ce deuxi�me fichier n'est pas directement adapt� � l'import<br />(il faudra en supprimer les colonnes Nom et Pr�nom avant import)</i>)</li>\n";
    echo "</ul>\n";

    echo "<p>Une fois t�l�charg�, utilisez votre tableur habituel pour ouvrir ce fichier en pr�cisant que le type de fichier est csv avec point-virgule comme s�parateur.</p>\n";

}
if (isset($is_posted )) {
    $non_def = 'no';
    $csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
    echo "<form enctype='multipart/form-data' action='traitement_csv.php' method=post >";
    if($csv_file['tmp_name'] != "") {
        echo "<p><b>Attention</b>, les donn�es ne sont pas encore enregistr�es dans la base GEPI. Vous devez confirmer l'importation (bouton en bas de la page) !</p>";

        $fp = @fopen($csv_file['tmp_name'], "r");
        if(!$fp) {
            echo "Impossible d'ouvrir le fichier CSV";
        } else {
            $row = 0;
            echo "<table class='boireaus'>\n<tr>\n<th><p class='bold'>IDENTIFIANT</p></th>\n<th><p class='bold'>Nom</p></th>\n<th><p class='bold'>Pr�nom</p></th>\n<th><p class='bold'>Note</p></th>\n<th><p class='bold'>Appr�ciation</p></th>\n</tr>\n";
            $valid = 1;
			$alt=1;
            while(!feof($fp)) {
                if (isset($en_tete)) {
                    $data = fgetcsv ($fp, $long_max, ";");
                    unset($en_tete);
                }
                $data = fgetcsv ($fp, $long_max, ";");
                 $num = count ($data);
                // On commence par rep�rer les lignes qui comportent 2 ou 3 champs tous vides de fa�on � ne pas les retenir
                if (($num == 2) or ($num == 3)) {
                    $champs_vides = 'yes';
                    for ($c=0; $c<$num; $c++) {
                        if ($data[$c] != '') {
                            $champs_vides = 'no';
                        }
                    }
                }
                // On ne retient que les lignes qui comportent 2 ou 3 champs dont au moins un est non vide
                if ((($num == 3) or ($num == 2)) and ($champs_vides == 'no')) {
                    $alt=$alt*(-1);
					$row++;
                    echo "<tr class='lig$alt'>\n";
                    for ($c=0; $c<$num; $c++) {
                        $col3 = '';
                        $reg_app = '';
                        $data_app = '';
                        switch ($c) {
                        case 0:
                            //login
                            $reg_login = "reg_".$row."_login";
                            $reg_statut = "reg_".$row."_statut";
                            $call_login = mysql_query("SELECT * FROM eleves WHERE login='" . $data[$c] . "'");
                            $test = @mysql_num_rows($call_login);
                            if ($test != 0) {
                                $nom_eleve = @mysql_result($call_login, 0, "nom");
                                $prenom_eleve = @mysql_result($call_login, 0, "prenom");

                                //
                                // Si l'�l�ve ne suit pas la mati�re
                                //
                                if (in_array($data[$c], $current_group["eleves"][$periode_num]["list"]))  {
                                    echo "<td><p>$data[$c]</p></td>\n";
                                } else {
                                    echo "<td><p><font color = red>* $data[$c] ??? *</font></p></td>\n";
                                    $valid = 0;
                                }
                                echo "<td><p>$nom_eleve</p></td>\n";
                                //echo "<td><p>$prenom_eleve</p></td>";
                                echo "<td><p>$prenom_eleve</p>";
                                $data_login = urlencode($data[$c]);
                                echo "<input type='hidden' name='$reg_login' value=\"$data_login\" />";
                                echo "</td>\n";
                            } else {
                                echo "<td><font color = red>???</font></td>\n";
                                echo "<td><font color = red>???</font></td>\n";
                                echo "<td><font color = red>???</font></td>\n";
                                echo "<td><font color = red>???</font></td>\n";
                                $valid = 0;
                            }
                            break;
                        case 1:
                            // Note
                            if (ereg ("^[0-9\.\,]{1,}$", $data[$c])) {
                                $data[$c] = str_replace(",", ".", "$data[$c]");
                                $test_num = settype($data[$c],"double");
                                if ($test_num) {
                                    if (($data[$c] >= 0) and ($data[$c] <= 20)) {
                                        //echo "<td><p>$data[$c]</p></td>";
                                        echo "<td><p>$data[$c]</p>";
                                        $reg_note = "reg_".$row."_note";
                                        echo "<input type='hidden' name='$reg_note' value=\"$data[$c]\" />";
                                        echo "</td>\n";
                                    } else {
                                        echo "<td><font color = red>???</font></td>\n";
                                        $valid = 0;
                                    }
                                } else {
                                    echo "<td><font color = red>???</font></td>\n";
                                    $valid = 0;
                                }
                            } else {
                                $tempo = strtolower($data[$c]);
                                if (($tempo == "disp") or ($tempo == "abs") or ($tempo == "-")) {
                                    //echo "<td><p>$data[$c]</p></td>";
                                    echo "<td><p>$data[$c]</p>\n";
                                    $reg_note = "reg_".$row."_note";
                                    echo "<input type='hidden' name='$reg_note' value=\"$data[$c]\" />";
                                    echo "</td>\n";
                                } else if ($data[$c] == "") {
                                    //echo "<td><p><font color = green>ND</font></p></td>";
                                    echo "<td><p><font color = green>ND</font></p>";
                                    $reg_note = "reg_".$row."_note";
                                    echo "<input type='hidden' name='$reg_note' value='' />";
                                    echo "</td>\n";
                                    $non_def = 'yes';
                                } else {
                                    echo "<td><font color = red>???</font></td>\n";
                                    $valid = 0;
                                }
                            }
                            break;
                        case 2:
                            // Appr�ciation
							$non_def='';
                            if ($data[$c] == "") {
                                $col3 = "<font color = green>ND</font>";
                                $non_def = 'yes';
                                $data_app = '';
                            } else {
								// =====================================================
								// L'export CSV g�n�r� par le fichier ODS remplace les ; par des |POINT-VIRGULE|
								// pour ne pas provoquer de probl�me avec le s�parateur ; du CSV
								// AJOUT: boireaus
								//echo "<td>\$data[$c]=$data[$c]</td>";
								//$data[$c]=my_ereg_replace("|POINT-VIRGULE|",";",$data[$c]);
								//$data[$c]=my_ereg_replace("\|POINT-VIRGULE\|",";",$data[$c]);
								$data[$c]=str_replace("|POINT-VIRGULE|",";",$data[$c]);
								// =====================================================
                                $col3 = $data[$c];
                                $data_app = urlencode($data[$c]);
                            }
                            $reg_app = "reg_".$row."_app";
//                            echo "<INPUT TYPE=HIDDEN name='$reg_app' value = $data_app>";
								echo "<td><p>$col3</p>";
								if($non_def!='yes'){
									echo "<input type='hidden' name='$reg_app' value=\"$data_app\" />";
								}
								//echo "</td>\n</tr>\n";
								echo "</td>\n";
                            break;
                        }
                    }
                    //echo "<td><p>$col3</p>"</td></tr>";
					/*
                    echo "<td><p>$col3</p>";
                    echo "<INPUT TYPE=HIDDEN name='$reg_app' value = $data_app />";
                    echo "</td>\n</tr>\n";
					*/
                    echo "</tr>\n";
                // fin de la condition "if ($num == 3)"
                }

            // fin de la boucle "while(!feof($fp))"
            }
            fclose($fp);
            echo "</table>\n";
            echo "<p>Premi�re phase de l'importation : $row entr�es import�es !</p>\n";
            if ($row > 0) {
                if ($valid == '1') {
                    echo "<input type='hidden' name='nb_row' value=\"$row\" />\n";
                    echo "<input type='hidden' name='id_groupe' value=\"$id_groupe\" />\n";
                    echo "<input type='hidden' name='periode_num' value=\"$periode_num\" />\n";
                    echo "<input type='submit' value='Enregistrer les donn�es' />\n";
                    echo "</form>\n";
                    ?>
                    <script type="text/javascript" language="javascript">
                    <!--
                    alert("Attention, les donn�es ne sont pas encore enregistr�es dans la base GEPI. Vous devez confirmer l'importation (bouton en bas de la page) !");
                    //-->
                    </script>
                    <?php
                } else {
                    echo "<p class='bold'>AVERTISSEMENT : Les symboles <font color=red>???</font> signifient que le champ en question n'est pas valide. L'op�ration d'importation des donn�es ne peut continuer normalement. Veuillez corriger le fichier � importer <br /></p>\n";
                    echo "</form>\n";
                }
                if ($non_def == 'yes') {
                    echo "<p class='bold'>Les symboles <font color=green>ND</font> signifient que le champ en question sera ignor�. Il n'y aura donc pas modification de la donn�e existante dans la base de GEPI.<br /></p>\n";
                }
            } else {
                echo "<p>L'importation a �chou� !</p>\n";
            }
        }
    // suite de la condition "if($csv_file != "none")"
    } else {
        echo "<p>Aucun fichier n'a �t� s�lectionn� !</p>\n";
    // fin de la condition "if($csv_file != "none")"
    }
}
require("../lib/footer.inc.php");
?>
