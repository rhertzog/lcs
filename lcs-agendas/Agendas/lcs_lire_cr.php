<?php
//indique que le type de la reponse renvoyee au client sera du Texte
header("Content-Type: text/plain" ); 
//anti Cache pour HTTP/1.1
header("Cache-Control: no-cache , private");
//anti Cache pour HTTP/1.0
header("Pragma: no-cache");
if(isset($_REQUEST['requete']))
	{	 
	if ($_REQUEST['requete']=="yes")
		{
		$nom_file_cr="./includes/lcs_cr_import.html";
		if (is_file($nom_file_cr)) readfile($nom_file_cr); else echo 'Pas de rapport disponible';			
		}	   
	else
		echo $_REQUEST['requete'];		   
}
			
?>