<?php
    /**
     * Fichier permettant de cr�er la classe qui �tend activeRecordGepi
     * en mettant en dur les propri�t�s et les m�thodes usuelles
     *
     * @author Julien Jocal
     */

// Variables
$_classe = isset($_POST["table"]) ? $_POST["table"] : NULL;

if ($_classe !== NULL){
    // On consid�re que le nouveau fichier va �tre cr�� dans le r�pertoire courant
    $filename = ucfirst(substr($_classe, 0, -1)); // on enl�ve le s � la fin et on met une majuscule au d�but
    $fichier = fopen($filename, "r+");


    // Ici, on calcule ce qui doit �tre �crit
    $texte_a_ecrire = '';





    fwrite($fichier, $texte_a_ecrire);
    fclose();
}


?>
