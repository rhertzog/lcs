<?
include "../Includes/basedir.inc.php";
include "../Includes/config.inc.php";

//NOTE: MAKE SURE YOU DO YOUR OWN APPROPRIATE SERVERSIDE ERROR CHECKING HERE!!!
if(!empty($_POST) && isset($_POST))
{
	//make variables safe to insert
  $bat =  mysql_real_escape_string($_POST['bat']);
  $etage = mysql_real_escape_string($_POST['etage']);
  $salle = mysql_real_escape_string($_POST['salle']);

if(!empty($_POST['action']) && isset($_POST['action']))
	switch($_POST['action']) {
		case "add" :
			//query to insert topo into table
		    $query="INSERT INTO `topologie` (`id`, `batiment`, `etage`, `salle`) VALUES ('', '$bat', '$etage', '$salle')";                        
			$result = mysql_query($query);
			if(!$result)
			{
				echo "<span class=\"error\">Echec de l'enregistrement</span>";
			}
			else
			{
				echo "<span class=\"info\">Enregistrement de votre élément réussi</span>";
			}
		break;

		case "edit" :
			//query to insert topo into table
		    $query="UPDATE  `topologie` SET  `batiment` =  '$bat', `etage` =  '$etage', `salle` =  '$salle'  WHERE  `salle` ='$salle'";        
			$result = mysql_query($query);
			if(!$result)
			{
				echo "<span class=\"error\">Echec de la modification</span>";
			}
			else
			{
				echo "<span class=\"info\">La modification est effectuiée</span>";
			}
		break;
			
		case "delete" :
		    $query="DELETE FROM `maint_plug`.`topologie` WHERE `topologie`.`salle` = '$salle'";                        
			$result = mysql_query($query);
			if(!$result)
			{
				echo "<span class=\"error\">Echec de la suppression</span>";
			}
			else
			{
				echo "<span class=\"info\">La salle <strong>$salle </strong> a été supprimée</span>";
			}
		break;

		default:
			echo "Aucune action choisie.";
		break;
	}
}
?>
