
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


if (! ($this->UserInGroup("admins")||($this->UserInGroup("Profs")))) //on pourrait se dire que ce test n'est pas nécessaire car seul les profs et l'administrateur ont accès à cette page, mais si un eleve "s'amuse" à repérer l'adresse complète de la page "EditGroup", il ne pourra quand même rien faire !!!
{
  //echo "<h3>Erreur: Vous n'êtes pas dans le groupe autorisé à administrer les groupes</h3>\n";
  echo "<h3>Vous n'êtes pas autorisé à administrer les groupes</h3>\n";
}
else if ($_POST)
{    
    $grname = trim($_POST["group"]);
    
    if(isset($_POST['group_members'])) {
    	//c'est le mode expert qui est utilisé
    	$grmembers = $_POST["group_members"];
    }
    else {
	$grmembers = $_POST["group_new_members"];
    }
    
    if ($grname)
    {
        
	//si l'utilisateur est un professeur, on l'affecte automatiquement au nouveau groupe
	if ( $this->UserInGroup("Profs") ) {	
		$grmembers = $grmembers."\n".$this->GetUserName() ;//on ajoute le professeur à la liste des membres
		$this->SaveGroup($grname, $grmembers);//on affecte la liste des membres choisis par le professeur
		//echo "<h3>Membres du groupe ".$grname." mis à jour!</h3>";
		$this->SetMessage("Membres du groupe ".$grname." mis à jour.");
		header("Location: ".$this->href()."/&group=".$grname);
	}
	else { //il s'agit de l'administrateur, donc on affecte uniquement les membres choisis par l'admin
                $this->SaveGroup($grname, $grmembers);
	        if($grmembers == "") {
			echo "<h3>Le groupe ".$grname." a été supprimé.</h3>";
		}
		else {
			//echo "<h3>Membres du groupe ".$grname." mis à jour!</h3>";
			$this->SetMessage("Membres du groupe ".$grname." mis à jour.");
			header("Location: ".$this->href()."/&group=".$grname);
		}
	}
    }
    else
    {
        echo "<h3> Erreur: Nom de groupe vide!</h3>";
    }
}
else if (trim($_REQUEST["group"]))
{
    $grname = trim($_REQUEST["group"]); 
    $grps = $this->LoadGroup($grname);
    $grmembers = trim($grps[$grname]);
?>

<center><h3>GESTION DES MEMBRES DU GROUPE "<?php echo $grname ?>"</h3></center>
<hr size="3" />
<hr size="6" width="120" />
<br /><br />
<u>Deux méthodes vous sont proposées pour modifier les membres d'un groupe</u> :
<ul>
  <li>"EXPERT" : vous pouvez saisir le ou les noms d'utilisateurs dans la liste ci-dessous, &agrave; raison de UN par ligne.</li>
  <li>"UTILISATION DE L'ASSISTANT" &agrave; l'aide de la liste d&eacute;roulante ci-dessous.</li>
</ul>
<br />
Pour <b>supprimer</b> un ou plusieurs utilisateurs du groupe, il suffit de se positionner dans la liste des membres du groupe puis de les effacer. 
<br />
<br />

<form name ="membres" action="<?php echo $this->config['base_url'] ?>EditGroup" method="post" >
<br />
Vous souhaitez ajouter : 
<select id="type" name="type" onchange="choixtype();" >
	<option value="0">Sélectionnez un type ... </option> 
	<option value="1">Un ou plusieurs élèves</option>
	<option value="2">Un ou plusieurs professeurs</option>
	<option value="3">Un ou plusieurs administratifs</option>
</select>

<div id="stype">
</div>

<br />

<div id="scategorie">
</div>
<br /><br />
<u>Liste des membres du groupe</u> :
<br /><br />
<textarea name="group_new_members" rows="8" cols="20" ><?php echo trim($grmembers)."\n"; ?></textarea>
<br />
<hr size="3" />
    <br />
<center> 
<b>Pour valider les modifications,cliquez sur le bouton "enregistrer".</b>
<br /><br />
	<input type="hidden" name="group" value="<?php echo $grname ?>" />
	<input type="submit" value="Enregistrer" style="width: 120px" accesskey="s" />
	<input type="button" value="Annuler" onClick="history.back();" style="width: 120px" />

</center>
<hr size="3" />
<hr size="6" width="120" />

<?php
    //print($this->FormClose());
    echo "</form>";
}
else
{
   echo "<h3>Erreur: le nom du groupe à créer est vide !</h3>\n";
}
?> 
