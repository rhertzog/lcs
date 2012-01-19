<?php

/* ==================================================
  Projet LCS : Linux Communication Server
  Plugin "Gestion ateliers AP"
  VERSION 1.0 du 15/12/2011
  par philippe LECLERC
  philippe.leclerc1@ac-caen.fr
  - script de redirection -
  _-=-_
  =================================================== */
session_name("gestap_Lcs");
@session_start();
if (isset($_SESSION['saclasse']))
    unset($_SESSION['saclasse']);
// Inclusion des fonctions de l'API-LCS
include ("/var/www/lcs/includes/user_lcs.inc.php");
include ("/var/www/lcs/includes/functions.inc.php");
include ("./Includes/functions2.inc.php");


// recuperation des donnees de l'utilisateur

$login = auth_lcs();

// Si $login, on recupere les datas de l'utilisateur
if ($login)
    {
    list($user, $groups) = people_get_variables($login, true);
    $_SESSION['login'] = $login;
    $_SESSION['name'] = $user["nom"];
    $_SESSION['nomcomplet'] = $user["fullname"];
    $_SESSION['RT'] = rand();
    if (is_prof($login))
        {
        $_SESSION['cequi'] = "prof";
        }
    elseif (is_eleve($login))
        {
        $_SESSION['cequi'] = "eleve";
        if (count($groups))
            {
            for ($loop = 0; $loop < count($groups); $loop++)
                {
                if (mb_ereg("^Classe", $groups[$loop]["cn"]))
                    {
                    $classe_courte=mb_split("_",$groups[$loop]["cn"],2);
                    $_SESSION['saclasse'] =$classe_courte[1];
                    break;
                    }
                }
            }
        else  $_SESSION['saclasse'] ="inconnue";
                    }

    elseif (is_administratif($login)) {
        $_SESSION['cequi'] = "administratif";
    }
    }

    //redirection d'acces

    if ($_SESSION['login'] == "admin") {
        header("location: ./scripts/admin.php");
        exit;
    } elseif ($_SESSION['cequi'] == "prof" && (ldap_get_right("Gest_ap_boss", $_SESSION['login']) == "Y")) {
        header("location: ./scripts/gestion.php");
        exit;
    } elseif (($_SESSION['cequi'] == "eleve" && $_SESSION['saclasse'][1] != "") ) {
        header("location: ./scripts/inscription.php");
        exit;
    } elseif ($_SESSION['cequi'] == "prof") {
        header("location: ./scripts/gestion.php");
        exit;
    }
    else {
        echo 'Acc&#232;s non autoris&#233; ! '; exit;
}
?>
