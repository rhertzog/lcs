<?php
/*
deleteoldversions.php
Copyright 2006  Raymond S�n�que

Modifi� par le lyc�e laetitia Bonaparte 2008 pour le LCS

L'action permet de supprimer l'ensemble des anciennes versions des pages pr�sentes dans la base de donn�e ou l'ensemble des anciennes versions d'une page bien d�finie.
*/

if (!defined('WIKINI_VERSION')) { exit("acc�s direct interdit"); }

if ($_POST["executer"])
{
$deletepages = $_POST['deletepages'];
$nompage = $_POST['nom_page'];

// Suppression de toutes les anciennes versions de toutes les pages
    if ($deletepages == "ON")
    {
        $this->query("DELETE from ".$this->config["table_prefix"]."pages where latest = 'N'");
        $message = "La suppression de toutes les anciennes versions de pages a �t� effectu�e avec succ�s.";
        $deletepages = '';
    }
    else if ($nompage != '--------'){
	// Suppression de toutes les anciennes versions d'une page choisie dans la liste
        $this->query("DELETE from ".$this->config["table_prefix"]."pages where tag = '".$nompage."' and latest = 'N'");
        $message = "La suppression des anciennes versions de la page $nompage, a �t� effectu�e avec succ�s.";
        $nompage = '';
    }
    else if($nompage = '--------'){
	// Si rien n'a �t� choisi
        $message = "Veuillez choisir une page dans la liste d�roulante ou cochez la case pour s�lectionner toutes les pages.";
        $nompage = '';
    }

//affichage du message javascript
$this->SetMessage("$message");
header("Location: ".$this->href());
}

echo $this->FormOpen();
echo '<div class="div_listes">';
echo '<table class="table_listes">';
echo '<tr>';
echo '<tr class="th_listes"><u>Suppression de toutes les anciennes versions d\'une page :</u></tr>';
echo '</tr>';
echo '<tr>';
            $req = $this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages where latest='N' order by tag ");
            $nbr = count($req);
	    if($nbr != 0){
	    	echo    '<td><select name="nom_page">';
		echo' <option value="--------"> -------- </option>';
            	foreach ($req as $res) {
                	$page = $res['tag'];
                	echo' <option value="'.$page.'"> '.$page.'</option>';
                }
echo    '</select></td>';
echo '</tr>';
echo '<tr>';
echo '<td class="td2_listes">Suppression globale des anciennes versions de toutes les pages<input type="checkbox" name="deletepages" value="ON"></td>';
echo '</tr>';
echo '<tr><td class="td1_listes"><span style="color:red;">Attention action irr&eacute;versible&nbsp;&nbsp;</span><input type="submit" name="executer" value="Ex�cuter"></td></tr>';
echo '</table>';
echo '</div>';
}
else {
	echo ' <td><b>Il n\'y a pas d\'anciennes versions de pages dans la base de donn�es.</b></td>';
	echo ' </tr></table>';
}
					
echo $this->FormClose();
?> 
