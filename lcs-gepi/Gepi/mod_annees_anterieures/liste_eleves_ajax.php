<?php
	header('Content-type: text/html; charset=iso-8859-1');

	// Initialisations files
	require_once("../lib/initialisations.inc.php");

	// Resume session
	$resultat_session = $session_gepi->security_check();
	if ($resultat_session == 'c') {
		header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
		die();
	} else if ($resultat_session == '0') {
		header("Location: ../logout.php?auto=1");
		die();
	};

	//INSERT INTO `droits` VALUES ('/mod_annees_anterieures/liste_eleves_ajax.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Recherche d �l�ves', '');
	if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
	}

	// Contr�ler que l'on acc�de pas � cette page de n'importe o�?


	include("../secure/connect.inc.php");
	$mysql_db = @mysql_connect("localhost", $dbUser, $dbPass);
	@mysql_select_db($dbDb);

	// CONTROLER CE QUI EST POST�
	if((strlen(my_ereg_replace("[A-Za-z���������������������զ����ݾ������������������������������� -]","",$_POST['nom_ele']))!=0)||(strlen(my_ereg_replace("[A-Za-z���������������������զ����ݾ������������������������������� -]","",$_POST['prenom_ele']))!=0)){
		$chaine="Les caract�res propos�s dans la recherche doivent �tre des caract�res alphab�tiques<br />(<i>ou �ventuellement le tiret '-' et l'espace ' '</i>).";
	}
	else{
		$sql="SELECT no_gep,nom,prenom,naissance FROM eleves WHERE nom LIKE '%".$_POST['nom_ele']."%' AND prenom LIKE '%".$_POST['prenom_ele']."%' ";

		$res=@mysql_query($sql);

		if(mysql_num_rows($res)==0){
			$chaine="Aucun r�sultat retourn�.";
		}
		else{
			$chaine="<table class='table_annee_anterieure'>";
			$chaine.="<tr style='background-color: white;'>";

			$chaine.="<th>";
			$chaine.="Nom";
			$chaine.="</th>";

			$chaine.="<th>";
			$chaine.="Prenom";
			$chaine.="</th>";

			$chaine.="<th>";
			$chaine.="Naissance";
			$chaine.="</th>";

			$chaine.="<th>";
			$chaine.="INE";
			$chaine.="</th>";

			$chaine.="</tr>";

			$alt=-1;
			while($lig=mysql_fetch_object($res)){
				//$chaine.="<tr>";

				$alt=$alt*(-1);
				$chaine.="<tr style='background-color:";
				if($alt==1){
					$chaine.="silver";
				}
				else{
					$chaine.="white";
				}
				$chaine.="; text-align: center;'>";

				$chaine.="<td>";
				$chaine.="$lig->nom";
				$chaine.="</td>";

				$chaine.="<td>";
				$chaine.="$lig->prenom";
				$chaine.="</td>";

				$chaine.="<td>";
				$chaine.=formate_date($lig->naissance);
				$chaine.="</td>";

				$chaine.="<td>";
				//$chaine.="$lig->no_gep";
				//$chaine.="<a href='#' onClick=\"document.getElementById(document.getElementById('ine_recherche').value).value='$lig->no_gep';return false;\">$lig->no_gep</a>";
				if($lig->no_gep!=""){
					$chaine.='<a href=\'#\' onClick=\"document.getElementById(document.getElementById(\'ine_recherche\').value).value=\''.$lig->no_gep.'\';cacher_div(\'div_search\');return false;\">'.$lig->no_gep.'</a>';
				}
				else{
					$chaine.="<span style='color:red'>Non renseign�</span>";
				}
				$chaine.="</td>";

				$chaine.="</tr>";
			}
			$chaine.="</table>";
			//$chaine.="$sql";

			// ATTENTION: IL NE FAUT PAS DE RETOUR A LA LIGNE DANS LA CHAINE RENVOY�E (ne pas mettre de \n donc)
			//            Et c'est vite coton de jouer avec les guillemets et apostrophes dans ce que l'on �crit.
		}
	}
	echo "document.getElementById('div_resultat').innerHTML=\"$chaine\";";

	@mysql_close($mysql_db);
?>
