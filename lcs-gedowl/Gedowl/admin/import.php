<?
//fichiers nécessaires à l'exploitation de l'API
$BASEDIR="/var/www";
//include "../Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";  
/*
if (isset($_POST['Importer']))
		{*/
//initialisation d'un tableau
		$data=array();
		$mess1="";
		//recherche des groupes classes
		$groups=search_groups('cn=classe*');
		if (count($groups))
			{    
			for ($loup=0; $loup < count($groups); $loup++)
		        {
				echo $groups[$loup]["cn"]."<BR>";
				
			//recherche des élèves
			$uids = search_uids ("(cn=".$groups[$loup]["cn"].")", "half");
  			$people = search_people_groups ($uids,"(sn=*)","cat");
  			 for ($loop=0; $loop < count($people); $loop++) 
  			 	{
      				echo "    *     ". $people[$loop]['uid']." - " .$people[$loop]["fullname"]."<BR>";
			       }
			
			//recherche des profs
			$filter2 = ereg_replace("Classe_","Equipe_",$groups[$loup]["cn"]);
    			$uids2 = search_uids ("(cn=".$filter2.")", "half");
    			$people2 = search_people_groups ($uids2,"(sn=*)","cat");
   		 	if (count($people2)) {
    				for ($loop=0; $loop < count($people2); $loop++) {
       				 if ($people2[$loop]["cat"] == "Equipe") {
       				 
       				 echo "  -  ".$people2[$loop]["uid"]." - ".$people2[$loop]["fullname"]."<BR>";
       				 }
   			 }
			}
			}
			}
		else  $mess1= "<h3 class='ko'> Erreur dans l'importation <BR></h3>";
		
	//}
?>
