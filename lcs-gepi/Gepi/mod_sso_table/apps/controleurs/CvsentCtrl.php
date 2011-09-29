<?php
/*
* $Id: CvsentCtrl.php 7805 2011-08-17 13:43:12Z dblanqui $
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
// On emp�che l'acc�s direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};

require_once ("Controleur.php");
require_once("ImportModele.php");

/**
 * Contr�leur par d�faut: Index
 */
class CvsentCtrl extends Controleur {

    public $table;
    public $couleur;
    private $csv = null;
    private $ecriture = false;
    private $erreurs_lignes = Null;
    private $libel_eleve;
    private $libel_responsable;
    private $libel_enseignant;

    /**
     * Action par d�faut
     */
    function index() {
        $this->vue->LoadTemplate('cvsent.php');
        $this->vue->Show();
    }

    function result() {
        try {
            $this->tmp = $_FILES['fichier']['tmp_name'];
            if (is_uploaded_file($this->tmp)) {
                $this->copy_file($this->tmp);
            } else
                throw new Exception('Aucun fichier ne semble upload� ');
            $this->csv = '../temp/'.get_user_temp_directory().'/ENT-Identifiants.csv';
            if (file_exists($this->csv)) {
                $this->traite_file($this->csv);
                unlink($this->csv);
                if (file_exists($this->csv)){
                    throw new Exception('Impossible de supprimer le fichier csv dans votre repertoire temp. Il est conseill� de le faire manuellement.');
                }
                if (is_null($this->erreurs_lignes)) {
                    $this->vue->LoadTemplate('result.php');
                    if (!is_null($this->table)
                        )$this->vue->MergeBlock('b1', $this->table);
                    $this->vue->Show();
                } else {
                    foreach ($this->erreurs_lignes as $ligne) {
                        $this->table[] = Array("ligne" => $ligne);
                    }
                    $this->vue->LoadTemplate('erreurs_fichier.php');
                    $this->vue->MergeBlock('b1', $this->table);
                    $this->vue->Show();
                }
            } else
                throw new Exception('Le nom du fichier csv est incorrect');
        } catch (Exception $e) {
            $this->vue->LoadTemplate('exceptions.php');
            $this->mess[] = Array('mess' => $e->getMessage());
            $this->vue->MergeBlock('b1', $this->mess);
            $this->vue->Show();
        }
    }

    private function copy_file($file) {
        $extension = strrchr($_FILES['fichier']['name'], '.');
        if ($extension == '.csv') {
            $this->file = $_FILES['fichier']['name'];
            if ($this->file == 'ENT-Identifiants.csv') {
                $copie = move_uploaded_file($file,'../temp/'.get_user_temp_directory().'/'. $this->file);
            } else
                throw new Exception('Le nom du fichier est incorrect');
        } else
            throw new Exception('Le fichier n\'est pas un fichier csv ');
    }

    private function traite_file($file) {
        $data = new ImportModele();
        $this->verif_file($file);
        if (is_null($this->erreurs_lignes)) {
            // on cr�e la table des imports ENT
            $data->cree_table_import();

            $this->fic = fopen($file, 'r');
            $statut = 'eleve';
            while (($this->ligne = fgetcsv($this->fic, 1024, ";")) !== FALSE) {

                // On charge la table temporaire
                //$this->ligne[0] : rne
                //$this->ligne[1] : uid
                //$this->ligne[2] : classe
                //$this->ligne[3] : statut
                //$this->ligne[4] : pr�nom
                //$this->ligne[5] : nom
                //$this->ligne[6] : login
                //$this->ligne[7] : mot de passe
                //$this->ligne[8] : cle de jointure
                //$this->ligne[9] : uid pere
                //$this->ligne[10] : uid pere
                //$this->ligne[11] : uid tuteur1
                //$this->ligne[12] : uid tuteur2
                // si on a un �l�ve, il a un p�re ou une m�re ou un tuteur 1 ou un tuteur 2
                if ($this->ligne[9] != "" || $this->ligne[10] != "" || $this->ligne[11] != "" || $this->ligne[12] != "") {
                    $recherche = TRUE;
                } else {
                    $recherche = FALSE;
                }
                $this->res = $data->cherche_login($this->ligne, $statut, $recherche);
                if (mysql_num_rows($this->res) == 1) {
                    // on a un seul utilisateur dans G�pi
                    $row = mysql_fetch_row($this->res);
                    $login_gepi = $row[0];
                } else {
                    // Pour les autres cas, il faut attendre que la table soit remplie
                    $login_gepi = '';
                }
                $data->ligne_table_import($this->ligne, $login_gepi);
            }

            // regrouper dans un seul enregistrement les UID pr�sents plusieurs fois
            $data->cree_index_uid();

            // r�cup�rer les libell�s �l�ves, responsables, enseignants
            //$this->req= "SELECT DISTINCT `statut` FROM `utilisateurs` u, `plugin_sso_table_import` e  WHERE u.`statut` = 'professeur' AND e.login = u.login AND e.login != '' ";
            // Ne fonctionne pas, certains profs ont un statut 'personnel' dans l'ENT (rempla�ant pas encore remont�, prof des �cole UPI...)
            // supprimer les �l�ves sans classe
            $this->res = $data->trouve_statut_eleves();
            if (mysql_num_rows($this->res) == 1) {
                $row = mysql_fetch_row($this->res);
                $this->libel_eleve = $row[0];
            } else {
                echo "il y a " . mysql_num_rows($this->res) . " d�nominations pour le statut �l�ve ";
                die ();
            }
            $data->supprime_sans_classe($this->libel_eleve);

            // supprimer les responsables sans classe
            $this->res = $data->trouve_statut_responsables();
            if (mysql_num_rows($this->res) == 1) {
                $row = mysql_fetch_row($this->res);
                $this->libel_responsable = $row[0];
            } else {
                echo "il y a " . mysql_num_rows($this->res) . " d�nominations pour le statut responsable ";
                die ();
            }
            $data->supprime_sans_classe($this->libel_responsable);
            // supprimer les responsables sans �l�ve (erreurs dans l'ENT)
            /*
              // si on a un tuteur dans l'ENT qui n'est que tuteur1 ou tuteur2, on peut le supprimer
              $this->tuteur = $data->est_que_tuteur();
              if (mysql_num_rows($this->tuteur) != 0) {
              while ($this->row = mysql_fetch_array($this->tuteur)) {
              // on a bien un compte tuteur dans l'ENT, on peut le supprimer il n'a pas de compte dans g�pi
              $data->del_by_uid($this->row['uid']);
              }
              }
             * 
             */


            /* On traite les doublons */
            // On recherche les enregistrements sans login
            $this->res = $data->login_vide();
            while ($this->row = mysql_fetch_array($this->res)) {
                $login = '';
                // si on a un responsable, on le retrouve dans p�re ou m�re ou tuteur 1 ou tuteur 2
                $this->resp = $data->est_responsable($this->row);
                if (mysql_num_rows($this->resp) != 0) {
                    // on a bien un responsable
                    // on regarde d�j� si la recherche sur responsable ne r�gle pas le probl�me
                    $this->resp1 = $data->cherche_login($this->row, 'responsable');
                    if (mysql_num_rows($this->resp1) == 1) {
                        $row1 = mysql_fetch_assoc($this->resp1);
                        $login = $row1['login'];
                    } else if (mysql_num_rows($this->resp) != 0) {
                        // on recherche le responsable avec ce nom et pr�nom ayant cet �l�ve
                        $this->eleve = mysql_fetch_assoc($this->resp);
                        $this->resp2 = $data->cherche_responsable($this->eleve, $this->row);
                        if (mysql_num_rows($this->resp2) != 0) {
                            $row2 = mysql_fetch_row($this->resp2);
                            $login = $row2[0];
                        }
                    } else {
                        // on a pas trouver d'�l�ve, il va falloir traiter � la main
                        $login = '';
                    }
                } else {
                    // si on a un �l�ve, il a un p�re ou une m�re ou un tuteur 1 ou un tuteur 2
                    $this->reselv = $data->est_eleve($this->row);
                    if (mysql_num_rows($this->reselv) != 0) {
                        // on a bien un �l�ve, on recherche l'�l�ve ayant un de ces responsables
                        $this->responsable = mysql_fetch_assoc($this->reselv);
                        $this->reselv2 = $data->cherche_eleve($this->responsable, $this->row);
                        if (mysql_num_rows($this->reselv2) == 1) {
                            $rowelv2 = mysql_fetch_row($this->reselv2);
                            $login = $rowelv2[0];
                        }
                    }
                    // les autres sont ni �l�ve ni responsable
                    $this->resautre = $data->doublon_pro($this->row, $this->libel_eleve, $this->libel_responsable);
                    if (mysql_num_rows($this->resautre) == 1) {
                        $rowautre = mysql_fetch_row($this->resautre);
                        $login = $rowautre[0];
                    }
                }
                // on enregistre
                $data->met_a_jour_ent($login, $this->row['uid']);
            }

            fclose($this->fic);

            // il reste encore les erreurs : 2 comptes ENT -> 1 compte G�pi, on peut nettoyer quand les 2 comptes ne sont pas des comptes parents
            $this->res = $data->doublon_2ent_1gepi();
            if (mysql_num_rows($this->res) > 0) {
                while ($this->row2 = mysql_fetch_array($this->res)) {
                    $data->efface_2ent_1gepi($this->row2, $this->libel_responsable);
                }
            }
            $this->res = $data->doublon_2ent_1gepi();
            if (mysql_num_rows($this->res) > 0) {
                $class = "message_red";
                $message = "Ce compte pose probl�me";
                while ($this->row2 = mysql_fetch_array($this->res)) {
                    $this->nom = $this->row2['nom'] . " " . $this->row2['prenom'];
                    $this->table[] = array('login_gepi' => $this->nom, 'login_sso' => $this->ligne['uid'], 'couleur' => $class, 'message' => $message);
                }
            }
            $this->setVarGlobal('choix_info', 'affich_result');
            if (!isset($_POST["choix"]) || ($_POST["choix"] == "ecrit")) {
                $this->ecriture = TRUE;
            } else {
                $this->ecriture = FALSE;
            }
            // On r�cup�re tous les membres de l'ENT ayant un login G�pi
            $this->res = $data->get_gepi_ent();
            if (mysql_num_rows($this->res) != 0) {
                while ($this->ligne = mysql_fetch_array($this->res)) {
                    $this->messages = $this->get_message($data->get_error($this->ligne['login'], $this->ligne['uid'], $this->ecriture));
                    if ($_POST["choix"] == "erreur" && $this->messages[0] == "message_red") {
                        $this->table[] = array('login_gepi' => $this->ligne['login'], 'login_sso' => $this->ligne['uid'], 'couleur' => $this->messages[0], 'message' => $this->messages[1]);
                    } else if ($_POST["choix"] != "erreur") {
                        $this->table[] = array('login_gepi' => $this->ligne['login'], 'login_sso' => $this->ligne['uid'], 'couleur' => $this->messages[0], 'message' => $this->messages[1]);
                    }
                }
            }
            // On r�cup�re les membres de l'ENT sans login pr�sents dans G�pi
            $this->class = "message_red";
            $this->res = $data->get_sans_login();
            if (mysql_num_rows($this->res) != 0) {
                while ($this->ligne = mysql_fetch_array($this->res)) {
                    $this->res2 = $data->cherche_login($this->ligne);
                    if (mysql_num_rows($this->res2) > 0) {
                        $this->message = 'Il y a plusieurs personnes dans G�pi ayant les m�mes noms et pr�noms';
                        $nomPrenom = $this->ligne['nom'] . " " . $this->ligne['prenom'];
                        $this->table[] = array('login_gepi' => $nomPrenom, 'login_sso' => $this->ligne['uid'], 'couleur' => $this->class, 'message' => $this->message);
                    }
                }
            }
            // On r�cup�re les membres de l'ENT sans login absents de G�pi
            $this->res = $data->get_sans_login();
            if (mysql_num_rows($this->res) != 0) {
                while ($this->ligne = mysql_fetch_array($this->res)) {
                    $this->res2 = $data->cherche_login($this->ligne);
                    if (mysql_num_rows($this->res2) == 0) {
                        $possibles = Null;
                        $probable = Null;
                        $this->res3 = $data->get_homonymes_sans_correspondance($this->ligne['nom']);
                        while ($this->ligne2 = mysql_fetch_array($this->res3)) {
                            $possibles[] = $this->ligne2;
                        }
                        if (isset($possibles))
                            $probable = $this->get_probable($this->ligne, $possibles);
                        if ($probable) {
                            $this->message = "L'utilisateur est peut �tre " . $probable['nom'] . " " . $probable['prenom'] . "( " . $probable['statut'] . ")";
                            $this->class = "message_purple";
                            $nomPrenom = $this->ligne['nom'] . " " . $this->ligne['prenom'];
                            $this->table[] = array('login_gepi' => $nomPrenom, 'login_sso' => $this->ligne['uid'], 'couleur' => $this->class, 'message' => $this->message);
                        } else {
                            $this->message = "L'utilisateur n'existe probablement pas dans g�pi.";
                            $this->class = "message_red";
                            $nomPrenom = $this->ligne['nom'] . " " . $this->ligne['prenom'];
                            $this->table[] = array('login_gepi' => $nomPrenom, 'login_sso' => $this->ligne['uid'], 'couleur' => $this->class, 'message' => $this->message);
                        }
                    }
                }
            }

            // On s'assure que table a bien un enregistrement
            if (is_null($this->table)) {
                //$this->table[] = array('login_gepi' => ' ', 'login_sso' => ' ', 'couleur' => ' ', 'message' => ' ');
                if ($_POST["choix"] != "erreur")
                    $this->setVarGlobal('choix_info', 'no_data');
                if ($_POST["choix"] == "erreur")
                    $this->setVarGlobal('choix_info', 'no_error');
            }
        }
        //$data->supprime_table_import();
    }

    private function verif_file($file) {
        $this->fic = fopen($file, 'r');
        $ligne_erreur = 1;
        while (($this->ligne = fgetcsv($this->fic, 1000, ";")) !== FALSE) {
            if (sizeof($this->ligne) != 13) {
                $this->erreurs_lignes[] = $ligne_erreur;
            }
            $ligne_erreur++;
        }
        fclose($this->fic);
        return($this->erreurs_lignes);
    }

    private function get_message($code) {
        //$NomBloc   : nom du bloc qui appel la fonction (lecture seule)
        //$CurrRec   : tableau contenant les champs de l'enregistrement en cours (lecture/�criture)
        //$RecNum    : num�ro de l'enregsitrement en cours (lecture seule)
        switch ($code) {
            case 0:
                $query = "SELECT `login_sso` FROM `sso_table_correspondance` WHERE `login_gepi`='" . $this->ligne['login'] . "'";
                $result = mysql_query($query);
                // V�rification du r�sultat
                if (!$result) {
                    $message = 'Requ�te invalide : ' . mysql_error() . "\n";
                    $message .= 'Requ�te compl�te : ' . $query;
                    die($message);
                }
                $row = mysql_fetch_row($result);
                if ($row[0] == $this->ligne['uid']) {
                    $this->class = "message_blue";
                    $this->message = 'Une entr�e identique existe d�j� dans la table pour ce login g�pi';
                } else {
                    $this->class = "message_red";
                    $this->message = 'Une entr�e diff�rente existe d�j� dans la table pour ce login g�pi';
                }
                break;
            case 1:
                $this->class = "message_red";
                $this->message = 'Une entr�e existe d�j� dans la table pour ce login sso';
                break;
            case 2:
                $this->class = "message_red";
                $this->message = 'L\'utilisateur n\'existe pas dans g�pi.';
                break;
            case 3:
                $this->class = "message_orange";
                $this->message = 'L\'utilisateur existe mais son compte n\'est pas param�tr� pour le sso. Il faut corriger absolument pour que la correspondance fonctionne.';
                break;
            case 5:
                $this->class = "message_red";
                $this->message = 'Aucune des deux valeurs ne peut �tre vide. Il faut rectifier cela.';
                break;
            default:
                $this->class = "message_green";
                $this->message = 'La correspondance est mise en place.';
        }
        return array($this->class, $this->message);
    }

    private function get_probable($personne, $possibles) {
        foreach ($possibles as $possible) {
            $longueur_min = min(strlen($personne['prenom']), strlen($possible['prenom']));
            if (soundex(substr($personne['prenom'], 0, $longueur_min)) == soundex(substr($possible['prenom'], 0, $longueur_min))) {
                $probables[] = $possible;
            }
        }
        if (isset($probables)) {
            $diff = 255;
            foreach ($probables as $probable) {
                if (levenshtein($probable['prenom'], $personne['prenom']) <= $diff) {
                    $diff = levenshtein($probable['prenom'], $personne['prenom']);
                    $plus_probables[] = $probable;
                }
            }
            if (count($plus_probables) != 1) {
                return false;
            } else {
                return($plus_probables[0]);
            }
        } else {
            return false;
        }
    }

}

?>
