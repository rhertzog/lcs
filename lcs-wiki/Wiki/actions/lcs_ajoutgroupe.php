<?php

/*
---------------------------------------------------------
Ajout� par le lyc�e laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------
*/



if (!defined('WIKINI_VERSION'))
{
	exit("acc�s direct interdit");
}


//fonction javascript qui permet de tester la validit� du nom du groupe � ajouter

echo "<script language=\"javascript\">";

echo "function verif()"; //cr�ation d'une fonction de v�rification
echo "{";
echo "nomgp = document.newgp.group.value ; "; //r�cup�ration du nom de groupe saisi par l'utilisateur
echo "vartest = 0 ;";

//recup�ration de la liste des groupes WIKI
$lig = $this->Query("SELECT DISTINCT grname FROM ".$this->config["table_prefix"]."groups WHERE grname NOT LIKE 'Equipe%' AND grname NOT LIKE 'Classe%' AND grname<>'admins' AND grname<>'Eleves' AND grname<>'Profs' AND grname<>'Administratifs' ");

//verifie si le groupe existe dej�. Si vartest = 1 c'est qu'il existe
$i = 0 ;
while ( $res = mysql_fetch_array($lig) ) {
	echo " if ( nomgp == \"".$res['grname']."\" ) { vartest = 1 ; }";
	$i++ ;
}

echo "var regequipe = new RegExp(\"^equipe\",\"gi\");"; //declarations des expressions reguli�res de recherche
echo "var regprof = new RegExp(\"^prof\",\"gi\");";
echo "var regeleve = new RegExp(\"^eleve\",\"gi\");";
echo "var regclasse = new RegExp(\"^classe\",\"gi\");";
echo "var regadmin = new RegExp(\"^admin\",\"gi\");";
echo "var regadministratif = new RegExp(\"^administratif\",\"gi\");";


echo "if ( vartest == 1 ) { ";
echo "alert(\" Ce nom de groupe existe d�j�.  \"); return false; ";
echo "} ";


 //si le resultat de la recherche (methode search) est diff�rent de -1, le nom de groupe n'est pas valide (il commence par un des mots "interdits")
echo "else if ( ( nomgp.search(regequipe) != \"-1\" ) || ( nomgp.search(regprof) != \"-1\" ) || ( nomgp.search(regeleve) != \"-1\" ) || ( nomgp.search(regclasse) != \"-1\" ) || ( nomgp.search(regadmin) != \"-1\" ) || ( nomgp.search(regadministratif) != \"-1\" ) ) { ";
 //affichage d'un message d'erreur
echo "alert(\"Le nom du groupe que vous souhaitez cr�er: \" + nomgp + \", n'est pas un nom valide. Veuillez lire la REMARQUE IMPORTANTE ci-dessous. \");";
 //on renvoit "false" pour rester sur la page d'ajout de groupe
echo "return false; }";
echo "else if ( nomgp == \"\" ) { alert(\"Si vous souhaitez cr�er un nouveau groupe, veuillez saisir son nom.\"); return false; }"; //verifie si le champ est vide
echo "else { return true; }";//sinon le mot est valide. On renvoit "true" pour �tre redirig� sur la page d'ajout de membres au nouveau groupe.
echo "}";

echo "</script>";



//formulaire
echo "<form name=newgp action=\"wakka.php\" onSubmit=\"return verif()\">";
echo "Indiquer le nom du groupe � cr�er :\n";
echo "<input type=\"hidden\" name=\"wiki\" value=\"EditGroup\" />\n";
echo "<input name=\"group\" size=\"15\" class=\"text\" />\n"; //c'est le contenu de cette variable qui est r�cup�r�e dans wakka.php : $grname
echo "<input type=\"submit\" name=\"groupe\" value=\"ajouter\" />\n";
echo "</form>";
echo "<br /><b><u>REMARQUE IMPORTANTE</u> :</b><br /> Le ou les groupes ajout�s ne peuvent pas commencer ou �tre �gaux aux mots de la liste suivante :\n";
echo "<ul><li>Equipe</li><li>Profs</li><li>Eleves</li><li>Classe</li><li>admin</li><li>Administratifs</li></ul>";

?>
