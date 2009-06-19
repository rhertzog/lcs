<?php

/*
---------------------------------------------------------
Ajouté par le lycée laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------
*/


if (!defined('WIKINI_VERSION'))
{
	exit("accès direct interdit");
}

if ($_POST["valider"])
{

 if ($_POST["toutpage"])
   {
     //Consultation des pages sans auteurs dans la base de données
     $this->LoadPagesSansProprio();     
   }

 if ($_POST["vfpage"])
   {
    //garder uniquement la derniere version des pages sans auteurs
     $this->PurgePagesSansProprio();
   }

 if ($_POST["suptoutpage"])
    {
     //Supprimer toutes les pages sans propriétaires présents dans la base de données
     $this->DeletePagesSansProprio();
    }

		   

}

?>

<?php
//Création du formulaire
echo  $this->FormOpen("")
?>

<b>Cochez le ou les cases qui conviennent à ce que vous voulez faire  :</b>
<br>
<br>
<ul>
<li>Consulter toutes les pages : <INPUT type="checkbox" name="toutpage">
<li>Ne garder que la dernière version de ces pages :  <INPUT type="checkbox" name="vfpage">
<li>Supprimer toutes les pages :  <INPUT type="checkbox" name="suptoutpage">
</ul>
<br>
<br>

<INPUT type="submit" name="valider" value="VALIDER">

<?php echo  $this->FormClose("") ?>

