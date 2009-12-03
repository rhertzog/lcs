<?php
/*
 * Last modification  : 14/03/2005
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//**************** EN-TETE *****************
$titre_page = "Outil de gestion | Importation";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

// $long_max : doit �tre plus grand que la plus grande ligne trouv�e dans le fichier CSV
$long_max = 8000;
if (!isset($is_posted) or (isset($is_posted) and ($is_posted == 'R')) ) {
    ?><p class=bold><a href="index.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>| <a href='javascript:centrerpopup("help_import.php",600,480,"scrollbars=yes,statusbar=no,resizable=yes")'>Aide</a></p>
    <p><b>Remarque importante</b> : vous allez importer dans la base GEPI des donn�es "�l�ve" � partir d'un fichier au format csv (s�parateur point-virgule).<br />
    Il peut s'agir de nouveaux �l�ves ou bien d'�l�ves d�j� pr�sents dans la base. Dans ce dernier cas,  les donn�es existantes seront �cras�es par les donn�es pr�sentes dans le fichier � importer.
    <br /><b>Attention </b> : certaines modifications en cours d'ann�e sur des �l�ves d�j� pr�sents dans la base peuvent entra�ner des incoh�rences dans les bases et par suite un mauvais fonctionnement de l'application.

    </p>

    <form enctype="multipart/form-data" action="import_csv.php" method=post name=formulaire>
    <?php $csv_file=""; ?>
    <p>Fichier CSV � importer : <INPUT TYPE=FILE NAME="csv_file"></p>
    <INPUT TYPE=HIDDEN name=is_posted value = 1>
    <p>Le fichier � importer comporte une premi�re ligne d'en-t�te, � ignorer&nbsp;
    <INPUT TYPE=CHECKBOX NAME="en_tete" VALUE="yes" CHECKED></p>
    <INPUT TYPE=SUBMIT value = Valider><br />
    </FORM>
    <?php
    echo "<p>Le fichier d'importation doit �tre au format csv (s�parateur : point-virgule)<br />";
    echo "Le fichier doit contenir les diff�rents champs suivants, tous obligatoires :<br />";
    echo "--> <B>IDENTIFIANT</B> : l'identifiant de l'�l�ve (".$longmax_login." caract�res maximum)<br />";
    echo "--> <B>Nom</B><br />";
    echo "--> <B>Pr�nom</B><br />";
    echo "--> <B>Sexe</B>  : F ou M<br />";
    echo "--> <B>Date de naissance</B> : jj/mm/aaaa<br />";
    echo "--> <B>Classe (fac.)</B> : le nom court d'une classe d�j� d�finie dans la base GEPI ou bien le caract�re - si l'�l�ve n'est pas affect� � une classe.<br />";
    echo "--> <B>R�gime</B> : d/p (demi-pensionnaire) ext. (externe) int. (interne) ou i-e (interne extern�(e))<br />";
    echo "--> <B>Doublant</B> : R (pour un doublant)  - (pour un non-doublant)<br />";
    echo "--> <B>".ucfirst(getSettingValue("gepi_prof_suivi"))."</B> : l'identifiant d'un ".getSettingValue("gepi_prof_suivi")." d�j� d�fini dans la base GEPI ou bien le caract�re - si l'�l�ve n'a pas de ".getSettingValue("gepi_prof_suivi").".<br />";
    echo "--> <B>Identifiant de l'�tablissement d'origine </B> : le code RNE identifiant chaque �tablissement scolaire et d�j� d�fini dans la base GEPI, ou bien le caract�re - si l'�tablissement n'est pas connu.<br /></p>";
} else {
    ?><p class=bold><a href="import_csv.php?is_posted=R"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>| <a href='javascript:centrerpopup("help_import.php",600,480,"scrollbars=yes,statusbar=no,resizable=yes")'>Aide</a></p>
    <?php

    $csv_file = isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;
    echo "<form enctype='multipart/form-data' action='traitement_csv.php' method=post >";
    if($csv_file['tmp_name'] != "") {
        $fp = @fopen($csv_file['tmp_name'], "r");
        if(!$fp) {
            echo "Impossible d'ouvrir le fichier CSV.";
        } else {
            $row = 0;
            echo "<table border=1><tr><td><p>Identifiant</p></td><td><p>Nom</p></td><td><p>Pr�nom</p></td><td><p>Sexe</p></td><td><p>Date de naissance</p></td><td><p>Classe</p></td><td><p>R�gime</p></td><td><p>Doublant</p></td><td><p>".ucfirst(getSettingValue("gepi_prof_suivi"))."</p></td><td><p>Id. �tab.</p></td></tr>";
            $valid = 1;
            while(!feof($fp)) {
                if (isset($en_tete) and ($en_tete=='yes')) {
                    $data = fgetcsv ($fp, $long_max, ";");
                    $en_tete = 'no';
                }
                $data = fgetcsv ($fp, $long_max, ";");
                $num = count ($data);
                if ($num == 10) {
                $row++;
                echo "<tr>";
                $test_login_existant = '';
                $login_exist = '';
                $login_valeur = '';
                for ($c=0; $c<$num; $c++) {
                    switch ($c) {
                    case 0:
                        //login
                        if (ereg ("^[a-zA-Z0-9_]{1,".$longmax_login."}$", $data[$c])) {
                            $reg_login = "reg_".$row."_login";
                            $reg_statut = "reg_".$row."_statut";
                            $data[$c] =    strtoupper($data[$c]);
                            $call_login = mysql_query("SELECT login FROM eleves WHERE login='$data[$c]'");
                            $test = mysql_num_rows($call_login);
                            if ($test != 0) {
                                echo "<td><p><font color = red>$data[$c]</font></p></td>";
                                echo "<INPUT TYPE=HIDDEN name='$reg_statut' value=existant>";
                                $test_login_existant = "oui";
                                $login_exist = "oui";
                                $login_valeur = $data[$c];
                            } else {
                                echo "<td><p>$data[$c]</p></td>";
                                echo "<INPUT TYPE=HIDDEN name='$reg_statut' value=nouveau>";
                                $login_exist = "non";
                           }
                            $data_login = urlencode($data[$c]);
                            echo "<INPUT TYPE=HIDDEN name='$reg_login' value = $data_login>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    case 1:
                        //Nom
                        $test_nom_prenom_existant = 'no';
                        if (ereg ("^.{1,30}$", $data[$c])) {
                            $temp = $c+1;
                            $call_nom = mysql_query("SELECT nom FROM eleves WHERE (nom='$data[$c]' and prenom = '$data[$temp]')");
                            $test = @mysql_num_rows($call_nom);
                            if ($test != 0) {
                                $test_nom_prenom_existant = 'yes';
                                echo "<td><p><font color = blue>$data[$c]</font></p></td>";
                            } else {
                                echo "<td><p>$data[$c]</p></td>";
                            }
                            $reg_nom = "reg_".$row."_nom";
                            $data_nom = urlencode($data[$c]);
                            echo "<INPUT TYPE=HIDDEN name='$reg_nom' value = $data_nom>";
                        } else {
                        echo "<td><font color = red>???</font></td>";
                        }
                        break;
                    case 2:
                        //Prenom
                        if (ereg ("^.{1,30}$", $data[$c])) {
                            if ($test_nom_prenom_existant == 'yes') {
                                echo "<td><p><font color = blue>$data[$c]</font></p></td>";
                            } else {
                                echo "<td><p>$data[$c]</p></td>";
                            }
                            $reg_prenom = "reg_".$row."_prenom";
                            $data_prenom = urlencode($data[$c]);
                            echo "<INPUT TYPE=HIDDEN name='$reg_prenom' value = $data_prenom>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    case 3:
                        // Sexe
                        $data[$c] =    strtoupper($data[$c]);
                        if (ereg ("^[MF]$", $data[$c])) {
                            echo "<td><p>$data[$c]</p></td>";
                            $reg_sexe = "reg_".$row."_sexe";
                            echo "<INPUT TYPE=HIDDEN name='$reg_sexe' value = $data[$c]>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    case 4:
                        // Date de naissance
                        if (ereg ("^[0-3]{1}[0-9]{1}[/]{1}[0-1]{1}[0-9]{1}[/]{1}[0-9]{4}$", $data[$c])) {
                            echo "<td><p>$data[$c]</p></td>";
                            $reg_naissance = "reg_".$row."_naissance";
                            echo "<INPUT TYPE=HIDDEN name='$reg_naissance' value = $data[$c]>";

                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    case 5:
                        //Classe
                        if ($data[$c] == '-') {
                            if ($login_exist == "non") {
                                $valeur_classe='-';
                            } else {
                                $test_classe = mysql_query("SELECT * FROM j_eleves_classes WHERE login='$login_valeur'");
                                $result_test = mysql_num_rows($test_classe);
                                if ($result_test == 0) {
                                    $valeur_classe='-';
                                } else {
                                    $valeur_classe='????';
                                }
                            }
                        } else {
                            $call_classe = mysql_query("SELECT id FROM classes WHERE classe='$data[$c]'");
                            $test = mysql_num_rows($call_classe);
                            if ($test == 0) {
                                $valeur_classe='????';
                            } else {
                                $id_classe=@mysql_result($call_classe,0,id);
                                if ($login_exist == "non") {
                                    $valeur_classe = $data[$c];
                                } else {
                                    $test_classe = mysql_query("SELECT * FROM j_eleves_classes WHERE (login='$login_valeur' and id_classe='$id_classe')");
                                    $result_test = mysql_num_rows($test_classe);
                                    if ($result_test == 0) {
                                        $valeur_classe='????';
                                    } else {
                                        $valeur_classe = $data[$c];
                                    }
                                }
                            }
                        }
                        if ($valeur_classe != '????') {
                            echo "<td><p>$valeur_classe</p></td>";
                            $reg_classe = "reg_".$row."_classe";
                            echo "<INPUT TYPE=HIDDEN name='$reg_classe' value = $valeur_classe>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    case 6:
                        //R�gime
                        $data[$c] =    strtolower($data[$c]);
                        if (ereg ("^(d/p|ext.|int.|i-e)$", $data[$c])) {
                            echo "<td><p>$data[$c]</p></td>";
                            $reg_regime = "reg_".$row."_regime";
                            echo "<INPUT TYPE=HIDDEN name='$reg_regime' value = $data[$c]>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;

                   case 7:
                        // Doublant
                        $data[$c] =    strtoupper($data[$c]);
                        if (ereg ("^[R\-]{1}$", $data[$c])) {
                            echo "<td><p>$data[$c]</p></td>";
                            $reg_doublant = "reg_".$row."_doublant";
                            echo "<INPUT TYPE=HIDDEN name='$reg_doublant' value = $data[$c]>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    case 8:
                        //Prof de suivi
                        if (($valeur_classe == '????') or ($valeur_classe == '-')) {
                            // si la classe n'est pas d�finie, le professeur de suivi ne peut pas l'�tre non plus !
                            if ($data[$c] != '-') {
                                $valeur_prof = '????';
                            } else {
                                $valeur_prof = '-';
                            }
                        } else {
                            $call_prof = mysql_query("SELECT * FROM utilisateurs u, j_groupes_professeurs jgp, j_groupes_classes jgc WHERE (" .
                            		"u.login = '$data[$c]' AND " .
                            		"u.login = jgp.id_professeur and " .
                            		"jgp.id_groupe = jgc.id_groupe and " .
                            		"jgc.id_classe = '$id_classe' )");
                            $test = mysql_num_rows($call_prof);
                            if (($test != 0)  or ($data[$c] == '-')) {
                                $valeur_prof = $data[$c];
                            } else {
                                $valeur_prof = '????';
                            }
                        }
                        if ($valeur_prof != '????') {
                            echo "<td><p>$valeur_prof</p></td>";
                            $reg_prof_suivi = "reg_".$row."_prof_suivi";
                            $valeur_prof = urlencode($valeur_prof);
                            echo "<INPUT TYPE=HIDDEN name='$reg_prof_suivi' value = $valeur_prof>";
                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                        case 9:
                        //�tablissement d'origine
                        $call_etab = mysql_query("SELECT * FROM etablissements WHERE id = '$data[$c]'");
                        $test = mysql_num_rows($call_etab);
                        if (($test != 0) or ($data[$c] == '-')) {
                            echo "<td><p>$data[$c]</p></td>";
                            $reg_etab = "reg_".$row."_etab";
                            $data_etab = urlencode($data[$c]);
                            echo "<INPUT TYPE=HIDDEN name='$reg_etab' value = $data_etab>";

                        } else {
                            echo "<td><font color = red>???</font></td>";
                            $valid = 0;
                        }
                        break;
                    }
                }
                echo "</tr>";
                }
            }
            fclose($fp);
            echo "</table>";
            echo "<p>Premi�re phase de l'importation : $row entr�es import�es !</p>";
            if ($row > 0) {
                if ($test_login_existant == "oui") {
                    echo "<p>--> Les identifiants qui apparaissent en rouge correspondent � des identifiants d�j� existant dans la base GEPI. Les donn�es existantes seront donc �cras�es par les donn�es pr�sentes dans le fichier � importer !</p>";
                }
                if ($test_nom_prenom_existant == 'yes') {
                    echo "<p>--> Les noms et pr�noms qui apparaissent en bleu correspondent � des �l�ves d�j� pr�sents dans la base GEPI et portant les m�mes noms et pr�noms.
                    <br />Si le nouvel identifiant est diff�rent, un nouvel �l�ve sera cr�e. Sinon, les donn�es de GEPI seront modifi�es. </p>";
                }
                if ($valid == '1') {
                    echo "<input type=submit value='Enregistrer les donn�es'>";
                    echo "<INPUT TYPE=HIDDEN name=nb_row value = $row>";
                    echo "</FORM>";
                } else {
                    echo "<p>AVERTISSEMENT : Les symboles ??? signifient que le champ en question n'est pas valide. L'op�ration d'importation des donn�es ne peut continuer normalement. Veuillez corriger le fichier � importer ou bien effectuer les op�rations n�cessaires dans la base GEPI !<br /></p>";
                    echo "</FORM>";
                }
            } else {
                echo "<p>L'importation a �chou� !</p>";
            }
        }
    } else {
        echo "<p>Aucun fichier n'a �t� s�lectionn� !</p>";
    }
}

require("../lib/footer.inc.php");
?>