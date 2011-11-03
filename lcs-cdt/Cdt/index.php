<?php

/* ==================================================
  Projet LCS : Linux Communication Server
  Plugin "cahier de textes"
  VERSION 2.3 du 06/01/2011
  par philippe LECLERC
  philippe.leclerc1@ac-caen.fr
  - script de redirection -
  _-=-_
  =================================================== */
session_name("Cdt_Lcs");
@session_start();
if (isset($_SESSION['saclasse']))
    unset($_SESSION['saclasse']);
// Inclusion des fonctions de l'API-LCS
include ("/var/www/lcs/includes/user_lcs.inc.php");
include ("/var/www/lcs/includes/functions.inc.php");
include ("./Includes/functions2.inc.php");

//Inclusion de la liste des classes
include ('./Includes/data.inc.php');

//version de PHP >= 4.3.2 ?

if (version_compare(phpversion(), "4.3.2", ">="))
    $_SESSION['version'] = ">=432";
else
    $_SESSION['version'] = "<432";

// recuperation des donnees de l'utilisateur

$login = auth_lcs();

// Si $login, on recupere les datas de l'utilisateur
if ($login) {
    list($user, $groups) = people_get_variables($login, true);
    $_SESSION['login'] = $login;
    $_SESSION['name'] = $user["nom"];
    $_SESSION['nomcomplet'] = $user["fullname"];
    $_SESSION['RT'] = rand();
    if (is_prof($login)) {
        $_SESSION['cequi'] = "prof";
    } elseif (is_eleve($login)) {
        $_SESSION['cequi'] = "eleve";
        if (count($groups)) {
            for ($loop = 0; $loop < count($groups); $loop++) {
                if (mb_ereg("^Classe", $groups[$loop]["cn"]))
                //recherche d'une occurence dans le fichier des classes
                    for ($n = 0; $n < count($classe); $n++) {
                        if ((mb_ereg("(_$classe[$n])$", $groups[$loop]["cn"])) || ($classe[$n] == $groups[$loop]["cn"])) {
                            $_SESSION['saclasse'][1] = $classe[$n];
                             $_SESSION['safullclasse'] =$groups[$loop]["cn"];
                            break;
                        }
                        else
                            $_SESSION['saclasse'][1] = "";
                    }
            }
        }
    }
    elseif (is_administratif($login)) {
        $_SESSION['cequi'] = "administratif";
    }


    //redirection d'acces

    if ($_SESSION['login'] == "admin") {
        header("location: ./scripts/fichier_classes.php");
        exit;
    } elseif ($_SESSION['cequi'] == "administratif" && (ldap_get_right("Cdt_can_sign", $_SESSION['login']) == "Y")) {
        header("location: ./scripts/cahier_direction.php");
        exit;
    } elseif (($_SESSION['cequi'] == "eleve" && $_SESSION['saclasse'][1] != "") || $_SESSION['cequi'] == "administratif") {
        header("location: ./scripts/cahier_text_eleve.php");
        exit;
    } elseif ($_SESSION['cequi'] == "prof") {
        header("location: ./scripts/cahier_texte_prof.php");
        exit;
    }
    else
        echo 'Acc&#232;s non autoris&#233; ! '; exit;
}
//si pas de login

elseif (isset($_GET['cl1'])) {
    $toto = array();
    for ($x = 1; $x <= 5; $x++) {
        if (isset($_GET['cl' . $x])) {
            //parent
            if (isset($_GET['ef' . $x])) {
            $toto = decripte_uid($_GET['ef' . $x], decripte_classe($_GET['cl' . $x]));
            if ((decripte_classe($_GET['cl' . $x]) != "") && ($toto[0] != "")) {
                $_SESSION['saclasse'][$x] = decripte_classe($_GET['cl' . $x]);
                $_SESSION['parentde'][$x] = decripte_uid($_GET['ef' . $x], $_SESSION['saclasse'][$x]);
                //enregistrement stats
                $date = date("YmdHis");
                $Kl = $_SESSION['saclasse'][$x];
                # Enregistrement dans la table statusages
                #
                $result = mysql_query("$DBAUTH", "INSERT INTO statusages VALUES ('Parent', 'Cdt', '$date', 'wan', '$Kl')", $authlink);

                #
            }
            }
            //eleve
             if (isset($_GET['el' . $x]) && $x==1) {
            $toto = decripte_uid($_GET['el' . $x], decripte_classe($_GET['cl' . $x]));
            if ((decripte_classe($_GET['cl' . $x]) != "") && ($toto[0] != "")) {
                $_SESSION['saclasse'][$x] = decripte_classe($_GET['cl' . $x]);
                $gugus = decripte_uid($_GET['el' . $x], $_SESSION['saclasse'][$x]);
                //enregistrement stats
                $date = date("YmdHis");
                $Kl = $_SESSION['saclasse'][$x];
                # Enregistrement dans la table statusages
                #
                $result = mysql_query("$DBAUTH", "INSERT INTO statusages VALUES ('Eleve', 'Cdt', '$date', 'wan', '$gugus[0]')", $authlink);
                break;
                #
            }
           }
            //
        }
    }
    if (isset($_SESSION['saclasse'])) {
        header("location: ./scripts/cahier_text_eleve.php");
        exit;
    } else {
        header("location: ./scripts/accessfilter.php");
        exit;
    }
} elseif (isset($_GET['prof'])) {
    //validation du lien
    unset($_SESSION['aliasprof']);
    unset($_SESSION['proffull']);

    if (validkey($_GET['prof'], $_GET['limit'], $_GET['key']) == "OK" && $_GET['limit'] > time()) {
        $_SESSION['aliasprof'] = $_GET['prof'];
        list($us_er, $groups) = people_get_variables($_GET['prof'], false);
        $_SESSION['proffull'] = $us_er["fullname"];
        $_SESSION['RT'] = rand();
        header("location: ./scripts/cahier_texte_prof_ro.php");
        exit;
    } elseif (validkey($_GET['prof'], $_GET['limit'], $_GET['key']) != "OK") {
        $err = "pas";
        header("location: ./scripts/error.php?error=" . $err . "");
        exit;
    } elseif ($_GET['limit'] < time()) {
        $err = "plus";
        header("location: ./scripts/error.php?error=" . $err . "");
        exit;
    }
} else {
    header("location: ./scripts/accessfilter.php");
    exit;
}
?>
