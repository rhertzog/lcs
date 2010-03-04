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
				
                        $sql = "select * from monlcs_db.ml_ressources where 1";
                        $c = mysql_query($sql) or die ("ERR ". $sql);
 	                while ($res = mysql_fetch_object($c)) {
				$test_url = patchUrl($res->url,$baseurl);
				//echo '<br />'.$test_url;
				if ($test_url == htmlentities($content)) {
        	                	$rep = "$('chk_ress_".$id."').src ='./images/check1.png';";
					//$res = mysql_fetch_object($c);
					$rep .= "$('ress_titre".$id."').innerHTML +=' (".$res->id.") ';";
					$rep .= "$('check_ress_".$id."').checked = true;";
					die($rep);
					//la ressource existe donc rien a faire
				}
				
			}
        	        
	        	$rep = "$('chk_ress_".$id."').src ='./images/check2.png';";
			$rep .= "$('check_ress_".$id."').checked = true;";
			die($rep);
		} 
		else if (trim($type) =='note') {
			
       	                $rep .= "$('chk_ress_".$id."').src ='./images/note.png';";
			$rep .= "$('check_ress_".$id."').checked = true;";
			die($rep);
			//c'est une note
                             	 
		}
        }
?>

