
<!--Merci à Gilles Jobin pour le script et le dictionnaire: http://www.cscv.qc.ca/recit/cyberediteur/-->

<script language="JavaScript">
function cocher()
{
    var cb;
    cb=document.corriger.elements['entree[]'].length
        for (j=0;j<cb;j++ ) 
        {
            document.corriger.elements['entree[]'] [j].checked=true;

        }
}

function decochermajuscule()
{
    var cb;
    cb=document.corriger.elements['entree[]'].length
        for (j=0;j<cb;j++) 
        {
            lettre = document.corriger.elements['entree[]'] [j].value;
			//alert (lettre);
			if ((lettre.charAt(0))>="A" && (lettre.charAt(0))<="Z") 
			{
			    document.corriger.elements['entree[]'] [j].checked=false;
			}
			

        }
}

function decocherchiffre()
{
    var cb;
    cb=document.corriger.elements['entree[]'].length
        for (j=0;j<cb;j++) 
        {
            lettre = document.corriger.elements['entree[]'] [j].value;
			//alert (lettre);
			if ((lettre.charAt(0))>="0" && (lettre.charAt(0))<="9") 
			{
			    document.corriger.elements['entree[]'] [j].checked=false;
			}
			

        }
}

function decocherminuscule()
{
    var cb;
    cb=document.corriger.elements['entree[]'].length
        for (j=0;j<cb;j++) 
        {
            lettre = document.corriger.elements['entree[]'] [j].value;
			//alert (lettre);
			if ((lettre.charAt(0))>="a" && (lettre.charAt(0))<="z") 
			{
			    document.corriger.elements['entree[]'] [j].checked=false;
			}
			

        }
}

function decocher()
{
    var cb;
    cb=document.corriger.elements['entree[]'].length
        for (j=0;j<cb;j++) 
        {
            
			document.corriger.elements['entree[]'] [j].checked=false;

        }
}


</script>


<?php

$letexte = stripslashes($pl);
$letexteorig=$letexte;
$letexte = trim($letexte, " \t\n\r.");
//stripper les tags


$letexte=strip_tags($letexte);


//le texte sans les ponctuations

$ponctuations = array
(
".",
",",
"/",
"@",
";",
":",
"?",
"!",
"(",
")",
"\"",
"[",
"]",
" -",
"- ",
"«",
"»",
"-"
);
    
$letexte = str_replace($ponctuations," ",$letexte);



//les lettres seules suivi d'une apostrophe
$letexte=ereg_replace("’","'",$letexte);
$letexte=ereg_replace("œ","oe",$letexte);
$letexte=ereg_replace(" [a-zA-Z]'"," ",$letexte);
$letexte=ereg_replace("’","'",$letexte);
$letexte=str_replace("\t", " ", $letexte);
$letexte=str_replace("\r", " ", $letexte);
$letexte=str_replace("\n", " ", $letexte);


//on enlève les espaces multiples
$letexte=ereg_replace(" {2,}"," ",$letexte);


$lesmotsdutexte = explode(" ",$letexte);

	if (!isset ($_POST["ajouteraudico"]) and !isset ($_POST[ainserer]))
	{
	echo "<b>Votre texte contient ".count($lesmotsdutexte)." mots.</b><hr noshade size=2>";
	}


//comment faire simple....
for ($x=0;$x<count($lesmotsdutexte) ;$x++) 
{
    $req="SELECT mot as combien from mot where `mot` LIKE \"".$lesmotsdutexte[$x]."\"";
	
	$r=mysql_query($req) or die(mysql_error());
	
	
	if (mysql_num_rows($r)<1) 
	{
	    //echo "Erreur probable dans le mot $lesmotsdutexte[$x]";
		$erreur[]=$lesmotsdutexte[$x];
		
	}
}
if (count($erreur)>1) 
{
    
	
	$erreur_unique = array_unique($erreur);
	$erreurs=implode("|",$erreur_unique);
	


	//si le dernier caractère est |, on le supprime
	if (substr($erreurs, -1)=="|") 
	{
	    $erreurs=substr($erreurs,0,-1);
	}
}
else 
{
    $erreurs=$erreur[0];
}

// corection Schweitzer Vincent

if (substr($erreurs, 0, 1)=="|") {
	$erreurs=substr($erreurs,1);
}

// corection Schweitzer Vincent fin

$acorriger = eregi_replace("($erreurs)","<span style='color:red;background-color:yellow'>\\1</span>",$letexteorig);
echo $acorriger;
$les_erreurs_uniques = explode("|",$erreurs);


?>


