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
		$nom_file_cr="./includes/cr_import.html";
		readfile($nom_file_cr);			
		}	   
	else
		echo $_REQUEST['requete'];		   
}
			
?>