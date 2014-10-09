<?php

/*
---------------------------------------------------------
Ajouté par le lycée laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------
*/

$message = "";

if (!defined('WIKINI_VERSION'))
{
	exit("accès direct interdit");
}


if ($_POST["valider"])
{
 
 if ($_POST["supgroupes"])
  {
  //Suppression de tous les groupes sauf le groupe "admins" avec l'user "admin"
  $message.=$this->supGroups("all");
  }
     
 if ($_POST["supusers"])
 {
 //Suppression des users et des groupes correspondants sauf admin et admins
 $message.=$this->supGroups("all","full");
 }

 if ($_POST["supgreleves"])
 {
 //Suppression du groupe sans supprimer les users
 $message.=$this->supGroups("Eleves");
 }

 if ($_POST["supeleves"])
 {
 //Suppression du groupe avec suppression des users
 $message.=$this->supGroups("Eleves","full");
 $message.=$this->supGroups("Classe");
 }
     

 if ($_POST["supgrprofs"])
 {
 //Suppression du groupe sans supprimer les users
 $message.=$this->supGroups("Profs");
 }

 if ($_POST["supprofs"])
 {
 //Suppression du groupe avec suppression des users
 $message.=$this->supGroups("Profs","full");
 $message.=$this->supGroups("Equipe");
 }
    
 if ($_POST["supgradms"])
 {
 //Suppression du groupe sans supprimer les users
 $message.=$this->supGroups("Administratifs");
 }

 if ($_POST["supadms"])
 {
 //Suppression du groupe avec suppression des users
 $message.=$this->supGroups("Administratifs","full");
 }
     
 if ($_POST["supgrequipes"])
 {
 //Suppression du groupe sans supprimer les users
 $message.=$this->supGroups("Equipe");
 }
     
 if ($_POST["supgrclasses"])
 {
 //Suppression du groupe sans supprimer les users
 $message.=$this->supGroups("Classe");
 }
 
 if ($_POST["createtout"])
 {
  $this->createGroups("Eleves","full");
  $this->createGroups("Profs","full");
  $this->createGroups("Administratifs","full");
  $this->createGroups("Equipe");
  $this->createGroups("Classe");
  }
   
 
 if ($_POST["createeleves"])
 {
 $this->createGroups("Eleves","full");
 }

 if ($_POST["createprofs"])
 {
 $this->createGroups("Profs","full");
 } 
 
 if ($_POST["createadms"])
 {
 $this->createGroups("Administratifs","full");
 }


 if ($_POST["createequipes"])
 {
 $this->createGroups("Equipe");
 } 

 if ($_POST["createclasses"])
 {
 $this->createGroups("Classe");
 }

//affichage du message javascript pour la suppression
if($message!=""){
	$this->SetMessage(" $message ");
	header("Location: ".$this->href());
}


}
else {
	//Création du formulaire
	echo  $this->FormOpen("");
	?>

	<b>Cochez le ou les cases qui conviennent à ce que vous voulez faire  :</b>
	<br />
	<br />
	<b>Suppression </b><br />
	<ul>
	  <li>Tous les groupes existants sauf admins : <INPUT type="checkbox" name="supgroupes">
	  <li>Tous les utilisateurs : <INPUT type="checkbox" name="supusers">
	  <li> Le groupe "Élève" :  <INPUT type="checkbox" name="supgreleves">
	  <li> Les élèves :  <INPUT type="checkbox" name="supeleves">
	  <li> Le groupe "Profs" : <INPUT type="checkbox" name="supgrprofs">
	  <li> Les "Profs" : <INPUT type="checkbox" name="supprofs">
	  <li> Le groupe "Administratifs" : <INPUT type="checkbox" name="supgradms">
	  <li> Les Administratifs : <INPUT type="checkbox" name="supadms">
	  <li> Les groupes "Équipes" : <INPUT type="checkbox" name="supgrequipes">
	  <li> Les groupes "Classes" : <INPUT type="checkbox" name="supgrclasses">
	</ul>
	<br />
	<br />
	<b>Création</b><br>
	<ul>
	  <li>Tous les utilisateurs et groupes : <INPUT type="checkbox" name="createtout">
  	  <li> Le groupe "Élève" :  <INPUT type="checkbox" name="createeleves">
	  <li> Le groupe "Profs" : <INPUT type="checkbox" name="createprofs">
	  <li> Le groupe "Administratifs" : <INPUT type="checkbox" name="createadms">
	  <li> Les groupes "Équipes" : <INPUT type="checkbox" name="createequipes">
	  <li> Les groupes "Classes" : <INPUT type="checkbox" name="createclasses">
	</ul>
	<br />
	<br />
	<INPUT type="submit" name="valider" value="VALIDER">
	<?php
	echo $this->FormClose("");
	}
	?>
