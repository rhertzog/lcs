     <?
        include "includes/secure_no_header.inc.php";

        if ($_POST || $_GET) {
                extract($_POST);
                extract($_GET);
                
		if (trim($type) =='ressource') {

			if (eregi('/monlcs/modules',$content)) {
        	                $rep = "$('chk_ress_".$id."').src ='./images/plug.png';";
				$rep .= "$('check_ress_".$id."').checked = true;";
				
				die($rep);
				//dans le cas d'un module on sort
			}
				

                        $sql = "select * from monlcs_db.ml_ressources where url = '".htmlentities($content)."'";
                        $c = mysql_query($sql) or die ("ERR ". $sql);
 	               if (mysql_num_rows($c) != 0) {
        	                $rep = "$('chk_ress_".$id."').src ='./images/check1.png';";
				$res = mysql_fetch_object($c);
				$rep .= "$('ress_titre".$id."').innerHTML +=' (".$res->id.") ';";
				$rep .= "$('check_ress_".$id."').checked = true;";
				

				die($rep);
				//la ressource existe donc rien a faire
                       } else {
        	                $rep = "$('chk_ress_".$id."').src ='./images/check2.png';";
				$rep .= "$('check_ress_".$id."').checked = true;";
			        
				die($rep);
				//la ressource n'existe pas

			}
		} 
		else if (trim($type) =='note') {
			
       	                $rep .= "$('chk_ress_".$id."').src ='./images/note.png';";
			$rep .= "$('check_ress_".$id."').checked = true;";

			die($rep);
			//c'est une note
                             	 
		}
        }
?>

