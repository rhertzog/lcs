<?php
/*
 $Id: draw_graphe_star.php 6729 2011-03-30 09:33:15Z crob $
*/

	header("Content-type:image/png");

	// On pr�cise de ne pas traiter les donn�es avec la fonction anti_inject
	$traite_anti_inject = 'no';
	// En quoi cela consiste-t-il?

	// Initialisations files
	require_once("../lib/initialisations.inc.php");

	// R�cup�ration des valeurs:
	//$nb_data = $_GET['nb_data'];
	$nb_series= $_GET['nb_series'];
	if((strlen(preg_replace("/[0-9]/","",$nb_series))!=0)||($nb_series=="")){
		exit;
	}

	//$eleves= $_GET['eleves'];
	$id_classe=$_GET['id_classe'];
	if((strlen(preg_replace("/[0-9]/","",$id_classe))!=0)||($id_classe=="")){
		exit;
	}

	for($i=1;$i<=$nb_series;$i++){
		$mgen[$i]=isset($_GET['mgen'.$i]) ? $_GET['mgen'.$i] : "";
	}


	function writinfo($chemin,$type,$chaine){
		//$debug=1;
		$debug=0;
		if($debug==1){
			$fich=fopen($chemin,$type);
			fwrite($fich,$chaine);
			fclose($fich);
		}
	}

	/*
	// Fonction d�plac�e vers /lib/share.inc.php avec ajout du remplacement des espaces et apostrophes par des tirets '_'
	function remplace_accents($chaine){
		//$retour=strtr(my_ereg_replace("�","OE",my_ereg_replace("�","oe",$chaine)),"������������������������������","AAAEEEEIIOOUUUCcaaaeeeeiioouuu");
		//$retour=strtr(my_ereg_replace("�","AE",my_ereg_replace("�","ae",my_ereg_replace("�","OE",my_ereg_replace("�","oe","$chaine"))))," '���������������������զ����ݾ�������������������������������","__AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz");
		$retour=strtr(my_ereg_replace("�","AE",my_ereg_replace("�","ae",my_ereg_replace("�","OE",my_ereg_replace("�","oe","$chaine")))),"���������������������զ����ݾ�������������������������������","AAAAAAACEEEEIIIINOOOOOSUUUUYYZaaaaaaceeeeiiiinoooooosuuuuyyz");
		return $retour;
	}
	*/


	//============================================
	writinfo('/tmp/infos_graphe.txt','w+',"Avant la r�cup�ration des moyennes.\n");

	// R�cup�ration des moyennes:
	$moytmp=array();
	$moyenne=array();
	//$nb_series=$nb_data-1;
	//$nb_series=2;

	for($k=1;$k<=$nb_series;$k++){
		$moytmp[$k]=array();
		$moytmp[$k]=explode("|",$_GET['temp'.$k]);
		$moyenne[$k]=array();
		// On d�cale pour commencer � compter � 1:
		for($i=1;$i<=count($moytmp[$k]);$i++){
			$moyenne[$k][$i]=$moytmp[$k][$i-1];
			//fwrite($fich,"\$moyenne[$k][$i]=".$moyenne[$k][$i]."\n");
			// PROBLEME: en register_global=on, les 2�me, 3�me,... s�ries ne sont pas r�cup�r�es.
			//           On obtient juste moyenne[2][1]=- et rien apr�s.
			writinfo('/tmp/infos_graphe.txt','a+',"\$moyenne[$k][$i]=".$moyenne[$k][$i]."\n");
		}
	}
	//============================================


	writinfo('/tmp/infos_graphe.txt','a+',"\n");

	$periode=isset($_GET['periode']) ? $_GET['periode'] : '';

	// Valeurs en dur, � modifier par la suite...
	//$largeurTotale=700;
	//$hauteurTotale=600;

	$largeurTotale=isset($_GET['largeur_graphe']) ? $_GET['largeur_graphe'] : '700';
	if((strlen(preg_replace("/[0-9]/","",$largeurTotale))!=0)||($largeurTotale=="")){
		$largeurTotale=700;
	}
	$hauteurTotale=isset($_GET['hauteur_graphe']) ? $_GET['hauteur_graphe'] : '600';
	if((strlen(preg_replace("/[0-9]/","",$hauteurTotale))!=0)||($hauteurTotale=="")){
		$hauteurTotale=600;
	}

	$tronquer_nom_court=isset($_GET['tronquer_nom_court']) ? $_GET['tronquer_nom_court'] : '0';
	writinfo('/tmp/infos_graphe.txt','a+',"\$tronquer_nom_court=$tronquer_nom_court\n");
	if((!ctype_digit($tronquer_nom_court))||($tronquer_nom_court<0)||($tronquer_nom_court>10)){
		$tronquer_nom_court=0;
	}
	writinfo('/tmp/infos_graphe.txt','a+',"\$tronquer_nom_court=$tronquer_nom_court\n");

	//settype($largeurTotale,'integer');
	//settype($hauteurTotale,'integer');

	// $taille_police de 1 � 6
	//$taille_police=3;
	$taille_police=isset($_GET['taille_police']) ? $_GET['taille_police'] : '3';
	if((strlen(preg_replace("/[0-9]/","",$taille_police))!=0)||($taille_police<1)||($taille_police>6)||($taille_police=="")){
		$taille_police=3;
	}

	if($taille_police>1){
		$taille_police_inf=$taille_police-1;
	}
	else{
		$taille_police_inf=$taille_police;
	}

	//$epaisseur_traits=2;
	$epaisseur_traits=isset($_GET['epaisseur_traits']) ? $_GET['epaisseur_traits'] : '2';
	if((strlen(preg_replace("/[0-9]/","",$epaisseur_traits))!=0)||($epaisseur_traits<1)||($epaisseur_traits>6)||($epaisseur_traits=="")){
		$epaisseur_traits=2;
	}
	writinfo('/tmp/infos_graphe.txt','a+',"\$epaisseur_traits=$epaisseur_traits\n");

	$epaisseur_axes=2;
	$epaisseur_grad=1;


	writinfo('/tmp/infos_graphe.txt','a+',"\nAvant la r�cup�ration des mati�res.\n");

	$eleve=array();

	$legendy = array();

	//============================================
	// R�cup�ration des mati�res:
	$mattmp=explode("|", $_GET['etiquette']);
	for($i=1;$i<=count($mattmp);$i++){
		$matiere[$i]=$mattmp[$i-1];

		$call_matiere = mysql_query("SELECT nom_complet FROM matieres WHERE matiere = '".$matiere[$i]."'");
		$matiere_nom_long[$i] = mysql_result($call_matiere, "0", "nom_complet");
		$matiere_nom_long[$i]=remplace_accents($matiere_nom_long[$i],'simple');

		writinfo('/tmp/infos_graphe.txt','a+',"\$matiere[$i]=".$matiere[$i]."\n");
		$matiere[$i]=remplace_accents($matiere[$i],'simple');
		writinfo('/tmp/infos_graphe.txt','a+',"\$matiere[$i]=".$matiere[$i]."\n");
	}

	writinfo('/tmp/infos_graphe.txt','a+',"\nAvant les titres...\n");
	$titre = unslashes($_GET['titre']);
	$k = 1;
	//while ($k < $nb_data) {
	//while ($k<=$nb_series) {
	for($k=1;$k<=2;$k++){
		if (isset($_GET['v_legend'.$k])) {
			$legendy[$k] = unslashes($_GET['v_legend'.$k]);
		} else {
			$legendy[$k]='' ;
		}
		// $eleve peut en fait �tre une moyenne de classe ou m�me un trimestre...
		$eleve[$k]=$legendy[$k];
		writinfo('/tmp/infos_graphe.txt','a+',"\$eleve[$k]=".$eleve[$k]."\n");
		//$k++;
	}
	//============================================


	$eleve1=$_GET['v_legend1'];
	$sql="SELECT * FROM eleves WHERE login='$eleve1'";
	$resultat_infos_eleve1=mysql_query($sql);
	$ligne=mysql_fetch_object($resultat_infos_eleve1);
	//$nom_eleve1=$ligne->nom." ".$ligne->prenom;
	$nom_eleve[1]=$ligne->nom." ".$ligne->prenom;
	if($periode!=''){
		$nom_eleve[1]=$nom_eleve[1]." ($periode)";
	}
	$nom_eleve[1]=remplace_accents($nom_eleve[1],'simple');

	// Variable destin�e � tenir compte de la moyenne annuelle...
	$nb_series_bis=$nb_series;
	if($legendy[2]=='Toutes_les_p�riodes'){
		$eleve2="";

		$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode";
		$result_periode=mysql_query($sql);
		$nb_periode=mysql_num_rows($result_periode);

		$cpt=1;
		while($lign_periode=mysql_fetch_object($result_periode)){
			$nom_periode[$cpt]=$lign_periode->nom_periode;
			$nom_periode[$cpt]=remplace_accents($nom_periode[$cpt],'simple');
			$cpt++;
		}

		// Si la moyenne annuelle est demand�e, on calcule:
		if(isset($_GET['affiche_moy_annuelle'])){
			writinfo('/tmp/infos_graphe.txt','a+',"\nAvant la moyenne annuelle...\n");

			// La moyenne annuelle am�ne une s�rie de plus:
			$nb_series_bis++;

			$moy_annee=array();
			for($i=1;$i<=count($matiere);$i++){
				$cpt=0;
				$total_tmp[$i]=0;
				// Boucle sur les p�riodes...
				for($k=1;$k<=$nb_periode;$k++){
					//if((strlen(preg_replace("/[0-9]/","",$largeur_imposee_photo))!=0)||($largeur_imposee_photo=="")){$largeur_imposee_photo=100;}



					writinfo('/tmp/infos_graphe.txt','a+',"strlen(preg_replace(\"/[0-9.]/\",\"\",\$moyenne[".$k."][".$i."]))=strlen(preg_replace(\"/[0-9.]/\",\"\",".$moyenne[$k][$i]."))=".strlen(preg_replace("/[0-9\.]/","",$moyenne[$k][$i]))."\n");

					//if((strlen(preg_replace("/[0-9]/","",$moyenne[$k][$i]))!=0)&&($moyenne[$k][$i]!="")){
					if(($moyenne[$k][$i]!='-')&&(strlen(preg_replace("/[0-9\.]/","",$moyenne[$k][$i]))==0)&&($moyenne[$k][$i]!="")){
						$total_tmp[$i]=$total_tmp[$i]+$moyenne[$k][$i];
						$cpt++;
					}
				}
				if($cpt>0){
					$moy_annee[$i]=round($total_tmp[$i]/$cpt,1);
				}
				else{
					$moy_annee[$i]="-";
				}
				$moyenne[$nb_periode+1][$i]=$moy_annee[$i];
				$indice_per_suppl=$nb_periode+1;
				writinfo('/tmp/infos_graphe.txt','a+',"\$moyenne[".$indice_per_suppl."][$i]=".$moyenne[$indice_per_suppl][$i]."\n");
			}
		}
	}
	else{
		// R�cup�ration des noms des �l�ves.
		$eleve2=$_GET['v_legend2'];
		switch($eleve2){
			case 'moyclasse':
					//$nom_eleve2="Moyennes de la classe";
					$nom_eleve[2]="Moyennes de la classe";
				break;
			case 'moymin':
					//$nom_eleve2="Moyennes minimales";
					$nom_eleve[2]="Moyennes minimales";
				break;
			case 'moymax':
					//$nom_eleve2="Moyennes maximales";
					$nom_eleve[2]="Moyennes maximales";
				break;
			default:
				$sql="SELECT * FROM eleves WHERE login='$eleve2'";
				$resultat_infos_eleve2=mysql_query($sql);
				$ligne=mysql_fetch_object($resultat_infos_eleve2);
				//$nom_eleve2=$ligne->nom." ".$ligne->prenom;
				$nom_eleve[2]=$ligne->nom." ".$ligne->prenom;
				break;
		}
		$nom_eleve[2]=remplace_accents($nom_eleve[2],'simple');
	}


	writinfo('/tmp/infos_graphe.txt','a+',"\nAvant seriemin, seriemax,...\n");

	// R�cup�ration des moyennes minimales et maximales
	// si elles ont �t� transmises:
	if(isset($_GET['seriemin'])){
		$seriemin=$_GET['seriemin'];
		$moy_min_tmp=explode("|", $_GET['seriemin']);
		// On d�cale pour commencer � compter � 1:
		for($i=1;$i<=count($moy_min_tmp);$i++){
			$moy_min[$i]=$moy_min_tmp[$i-1];
			writinfo('/tmp/infos_graphe.txt','a+',"\$moy_min[$i]=".$moy_min[$i]."\n");
		}
	}

	if(isset($_GET['seriemax'])){
		$seriemax=$_GET['seriemax'];
		$moy_max_tmp=explode("|", $_GET['seriemax']);
		// On d�cale pour commencer � compter � 1:
		for($i=1;$i<=count($moy_max_tmp);$i++){
			$moy_max[$i]=$moy_max_tmp[$i-1];
			writinfo('/tmp/infos_graphe.txt','a+',"\$moy_max[$i]=".$moy_max[$i]."\n");
		}
	}



	//============================================
	//Cr�ation de l'image:
	$img=imageCreate($largeurTotale,$hauteurTotale);
	// Epaisseur initiale des traits...
	imagesetthickness($img,2);
	//============================================

	writinfo('/tmp/infos_graphe.txt','a+',"\nApr�s imageCreate, imagethickness...\n");



	//============================================
	// A r�cup�rer d'une table MySQL... d'apr�s un choix de l'utilisateur...

	$tab=array('Fond','Bande_1','Bande_2','Axes','Eleve_1','Eleve_2','Moyenne_classe','Periode_1','Periode_2','Periode_3');
	$comp=array('R','V','B');

	$tabcouleurs=array();
	$tabcouleurs['Fond']=array();
	$tabcouleurs['Fond']['R']=255;
	$tabcouleurs['Fond']['V']=255;
	$tabcouleurs['Fond']['B']=255;

	$tabcouleurs['Bande_1']=array();
	$tabcouleurs['Bande_1']['R']=255;
	$tabcouleurs['Bande_1']['V']=255;
	$tabcouleurs['Bande_1']['B']=255;

	$tabcouleurs['Bande_2']=array();
	$tabcouleurs['Bande_2']['R']=255;
	$tabcouleurs['Bande_2']['V']=255;
	$tabcouleurs['Bande_2']['B']=133;

	$tabcouleurs['Axes']=array();
	$tabcouleurs['Axes']['R']=0;
	$tabcouleurs['Axes']['V']=0;
	$tabcouleurs['Axes']['B']=0;

	$tabcouleurs['Eleve_1']=array();
	$tabcouleurs['Eleve_1']['R']=0;
	$tabcouleurs['Eleve_1']['V']=100;
	$tabcouleurs['Eleve_1']['B']=255;

	$tabcouleurs['Eleve_2']=array();
	$tabcouleurs['Eleve_2']['R']=0;
	$tabcouleurs['Eleve_2']['V']=255;
	$tabcouleurs['Eleve_2']['B']=0;

	$tabcouleurs['Moyenne_classe']=array();
	$tabcouleurs['Moyenne_classe']['R']=100;
	$tabcouleurs['Moyenne_classe']['V']=100;
	$tabcouleurs['Moyenne_classe']['B']=100;

	$tabcouleurs['Periode_1']=array();
	$tabcouleurs['Periode_1']['R']=0;
	$tabcouleurs['Periode_1']['V']=100;
	$tabcouleurs['Periode_1']['B']=255;

	$tabcouleurs['Periode_2']=array();
	$tabcouleurs['Periode_2']['R']=255;
	$tabcouleurs['Periode_2']['V']=0;
	$tabcouleurs['Periode_2']['B']=0;

	$tabcouleurs['Periode_3']=array();
	$tabcouleurs['Periode_3']['R']=255;
	$tabcouleurs['Periode_3']['V']=0;
	$tabcouleurs['Periode_3']['B']=0;

	for($i=0;$i<count($tab);$i++){
		for($j=0;$j<count($comp);$j++){
			$sql="SELECT value FROM setting WHERE name='couleur_".$tab[$i]."_".$comp[$j]."'";
			$res_couleur=mysql_query($sql);
			if(mysql_num_rows($res_couleur)>0){
				$tmp=mysql_fetch_object($res_couleur);
				$tabcouleurs[$tab[$i]][$comp[$j]]=$tmp->value;
			}
		}
		$couleur[$tab[$i]]=imageColorAllocate($img,$tabcouleurs[$tab[$i]]['R'],$tabcouleurs[$tab[$i]]['V'],$tabcouleurs[$tab[$i]]['B']);
	}

	$fond=$couleur['Fond'];
	$bande1=$couleur['Bande_1'];
	$bande2=$couleur['Bande_2'];
	$couleureleve[1]=$couleur['Eleve_1'];
	$couleureleve[2]=$couleur['Eleve_2'];

	$transp=$bande1;

	if($legendy[2]=='Toutes_les_p�riodes'){
		$couleureleve[1]=$couleur['Periode_1'];
		$couleureleve[2]=$couleur['Periode_2'];
		$couleureleve[3]=$couleur['Periode_3'];
	}

	$i=4;
	if(($legendy[2]=='Toutes_les_p�riodes')&&($nb_series>=4)){
		for($i=4;$i<=$nb_series;$i++){
			for($j=0;$j<count($comp);$j++){
				$sql="SELECT value FROM setting WHERE name='couleur_Periode_".$i."_".$comp[$j]."'";
				$res_couleur=mysql_query($sql);
				if(mysql_num_rows($res_couleur)>0){
					$tmp=mysql_fetch_object($res_couleur);
					$tabcouleurs["Periode_".$i][$comp[$j]]=$tmp->value;
				}
				else{
					$tabcouleurs["Periode_".$i][$comp[$j]]=0;
				}
			}
			$couleur["Periode_".$i]=imageColorAllocate($img,$tabcouleurs["Periode_".$i]['R'],$tabcouleurs["Periode_".$i]['V'],$tabcouleurs["Periode_".$i]['B']);
			$couleureleve[$i]=$couleur["Periode_".$i];
		}
	}
	$couleurmoyenne=$couleur['Moyenne_classe'];
	$axes=$couleur['Axes'];

	// IL FAUT UNE COULEUR DE PLUS POUR LA MOYENNE ANNUELLE...
	$couleureleve[$i]=$couleur['Moyenne_classe'];

	//============================================


	// On force la couleur pour les moyennes classe/min/max
	if(($eleve2=='moyclasse')||($eleve2=='moymin')||($eleve2=='moymax')){
		$couleureleve[2]=$couleurmoyenne;
	}



	//===========================================
	$nbMat=count($matiere);
	//===========================================




	//===========================================
	//===========================================

	// Rayon en pixels du cercle pour aller de 0 � 20:
	//$L=200;
	//$L=round(($hauteurTotale-3*(ImageFontHeight($taille_police)+5))/2);
	//$L=round(($hauteurTotale-4*(ImageFontHeight($taille_police)+5))/2);

	//$x0=round($largeurTotale/2);
	//$y0=round($hauteurTotale/2);
	$x0=round($largeurTotale/2);
	if($legendy[2]=='Toutes_les_p�riodes'){
		$L=round(($hauteurTotale-6*(ImageFontHeight($taille_police)+5))/2);
		//$y0=round(3*(ImageFontHeight($taille_police))+5)+$L;
		$y0=round(4*(ImageFontHeight($taille_police))+5)+$L;
	}
	else{
		$L=round(($hauteurTotale-4*(ImageFontHeight($taille_police)+5))/2);
		$y0=round(2*(ImageFontHeight($taille_police))+5)+$L;
	}

	writinfo('/tmp/infos_graphe.txt','a+',"\$x0=$x0\n");
	writinfo('/tmp/infos_graphe.txt','a+',"\$y0=$y0\n");

	$pi=pi();


	function coordcirc($note,$angle) {
		// $note sur 20 (s'assurer qu'il y a le point pour s�parateur et non la virgule)
		// $angle en degr�s
		global $pi;
		global $L;
		global $x0;
		global $y0;

		$x=round($note*$L*cos($angle*$pi/180)/20)+$x0;
		$y=round($note*$L*sin($angle*$pi/180)/20)+$y0;

		return array($x,$y);
	}


	//=================================
	// Epaisseur des traits
	imagesetthickness($img,1);
	//=================================


	//=================================
	// Polygone 20/20
	unset($tab20);
	$tab20=array();
	for($i=0;$i<$nbMat;$i++){
		$angle=round($i*360/$nbMat);
		//writinfo('/tmp/infos_graphe.txt','a+',"\$angle=$angle\n");
		$tab=coordcirc(20,$angle);

		$tab20[]=$tab[0];
		$tab20[]=$tab[1];
	}
	ImageFilledPolygon($img,$tab20,count($tab20)/2,$bande2);
	//=================================


	//=================================
	// Polygone 15/20
	unset($tab15);
	$tab15=array();
	for($i=0;$i<$nbMat;$i++){
		$angle=round($i*360/$nbMat);
		//writinfo('/tmp/infos_graphe.txt','a+',"\$angle=$angle\n");
		$tab=coordcirc(15,$angle);

		$tab15[]=$tab[0];
		$tab15[]=$tab[1];
	}

	ImageFilledPolygon($img,$tab15,count($tab15)/2,$bande1);
	//=================================

	//=================================
	// Polygone 10/20
	unset($tab10);
	$tab10=array();
	for($i=0;$i<$nbMat;$i++){
		$angle=round($i*360/$nbMat);
		//writinfo('/tmp/infos_graphe.txt','a+',"\$angle=$angle\n");
		$tab=coordcirc(10,$angle);

		$tab10[]=$tab[0];
		$tab10[]=$tab[1];
	}

	ImageFilledPolygon($img,$tab10,count($tab10)/2,$bande2);
	//=================================

	//=================================
	// Polygone 5/20
	unset($tab5);
	$tab5=array();
	for($i=0;$i<$nbMat;$i++){
		$angle=round($i*360/$nbMat);
		//writinfo('/tmp/infos_graphe.txt','a+',"\$angle=$angle\n");
		$tab=coordcirc(5,$angle);

		$tab5[]=$tab[0];
		$tab5[]=$tab[1];
	}

	ImageFilledPolygon($img,$tab5,count($tab5)/2,$bande1);
	//=================================


	//=================================
	// Axes
	for($i=0;$i<count($tab20)/2;$i++){
		imageline ($img,$x0,$y0,$tab20[2*$i],$tab20[2*$i+1],$axes);
		if($i>0){
			imageline ($img,$tab20[2*($i-1)],$tab20[2*($i-1)+1],$tab20[2*$i],$tab20[2*$i+1],$axes);
		}
		else{
			//imageline ($img,$tab20[2*count($tab20)/2],$tab20[2*count($tab20)/2+1],$tab20[2*$i],$tab20[2*$i+1],$axes);
		}
	}
	imageline ($img,$tab20[0],$tab20[1],$tab20[2*($i-1)],$tab20[2*($i-1)+1],$axes);
	//=================================


	//Epaisseur des traits:
	imagesetthickness($img,$epaisseur_traits);

	//=================================
	// Trac� des courbes des s�ries
	for($k=1;$k<=$nb_series_bis;$k++){
		$xprec="";
		$yprec="";
		$temoin_prec="";

		// On place les points
		$tab_x=array();
		$tab_y=array();
		for($i=1;$i<$nbMat+1;$i++){
			if(($moyenne[$k][$i]!="")&&($moyenne[$k][$i]!="-")&&($moyenne[$k][$i]!="N.NOT")&&($moyenne[$k][$i]!="ABS")&&($moyenne[$k][$i]!="DIS")){

				$angle=round(($i-1)*360/$nbMat);
				$tab=coordcirc($moyenne[$k][$i],$angle);

				imageFilledRectangle($img,$tab[0]-2,$tab[1]-2,$tab[0]+2,$tab[1]+2,$couleureleve[$k]);

				$tab_x[]=$tab[0];
				$tab_y[]=$tab[1];
			}
			else{
				$tab_x[]="";
				$tab_y[]="";
			}
		}


		// On joint ces points
		$xprec="";
		$yprec="";
		for($i=0;$i<count($tab_x);$i++){
			if($i==0){
				if(($tab_x[$i]!="")&&($tab_x[count($tab_x)-1]!="")){
					imageline ($img,$tab_x[$i],$tab_y[$i],$tab_x[count($tab_x)-1],$tab_y[count($tab_y)-1],$couleureleve[$k]);
				}
			}

			if($tab_x[$i]!=""){
				if(isset($tab_x[$i+1])){
					if($tab_x[$i+1]!=""){
						imageline ($img,$tab_x[$i],$tab_y[$i],$tab_x[$i+1],$tab_y[$i+1],$couleureleve[$k]);
					}
				}
			}
		}
	}
	//=================================


	//=================================
	// L�gendes Mati�res:
	for($i=0;$i<count($tab20)/2;$i++){
		$angle=round($i*360/$nbMat);

		writinfo('/tmp/infos_graphe.txt','a+',"\$angle=$angle\n");

		//$texte=$matiere[$i+1];
		$texte=$matiere_nom_long[$i+1];

		$tmp_taille_police=$taille_police;

		if($angle==0){
			$x=$tab20[2*$i]+5;

			$x_verif=$x+strlen($texte)*ImageFontWidth($taille_police);

			if($x_verif>$largeurTotale){
				for($j=$taille_police;$j>1;$j--){
					$x_verif=$x+strlen($texte)*ImageFontWidth($j);
					writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x_verif=$x_verif\n");
					if($x_verif<=$largeurTotale){
						break;
					}
				}
				if($x_verif>$largeurTotale){
					$j=1;
				}
				$tmp_taille_police=$j;
			}

			$y=round($tab20[2*$i+1]+$angle*(ImageFontHeight($taille_police)+2)/90);
		}
		elseif(($angle>0)&&($angle<90)){
			$x=$tab20[2*$i]+5;
			$x_verif=$x+strlen($texte)*ImageFontWidth($taille_police);

			if($x_verif>$largeurTotale){
				for($j=$taille_police;$j>1;$j--){
					$x_verif=$x+strlen($texte)*ImageFontWidth($j);
					writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x_verif=$x_verif\n");
					if($x_verif<=$largeurTotale){
						break;
					}
				}
				if($x_verif>$largeurTotale){
					$j=1;
				}
				$tmp_taille_police=$j;
			}

			$y=round($tab20[2*$i+1]+$angle*(ImageFontHeight($taille_police)+2)/90);
		}
		elseif($angle==90){
			$x=round($tab20[2*$i]-strlen($texte)*ImageFontWidth($taille_police)/2);
			$y=$tab20[2*$i+1]+ImageFontHeight($taille_police)+2;
		}
		elseif(($angle>90)&&($angle<180)){
			$x=$tab20[2*$i]-(strlen($texte)*ImageFontWidth($taille_police)+5);

			if($x<0){
				for($j=$taille_police;$j>1;$j--){
					$x=$tab20[2*$i]-(strlen($texte)*ImageFontWidth($j)+5);
					writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x=$x\n");
					if($x>=0){
						break;
					}
				}
				if($x<0){
					$x=1;
					$j=1;
				}
				$tmp_taille_police=$j;
			}

			$y=round($tab20[2*$i+1]+($angle-90)*(ImageFontHeight($taille_police)-2)/90);
		}
		elseif($angle==180){
			$x=$tab20[2*$i]-strlen($texte)*ImageFontWidth($taille_police)-5;

			if($x<0){
				for($j=$taille_police;$j>1;$j--){
					$x=$tab20[2*$i]-strlen($texte)*ImageFontWidth($j)-5;
					writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x=$x\n");
					if($x>=0){
						break;
					}
				}
				if($x<0){
					$x=1;
					$j=1;
				}
				$tmp_taille_police=$j;
			}

			$y=round($tab20[2*$i+1]-ImageFontHeight($taille_police)/2);
		}
		elseif(($angle>180)&&($angle<270)){
			$x=$tab20[2*$i]-(strlen($texte)*ImageFontWidth($taille_police)+5);

			if($x<0){
				for($j=$taille_police;$j>1;$j--){
					$x=$tab20[2*$i]-(strlen($texte)*ImageFontWidth($j)+5);
					writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x=$x\n");
					if($x>=0){
						break;
					}
				}
				if($x<0){
					$x=1;
					$j=1;
				}
				$tmp_taille_police=$j;
			}

			$y=round($tab20[2*$i+1]-($angle-180)*(ImageFontHeight($taille_police)-2)/90);
		}
		elseif($angle==270){
			$x=round($tab20[2*$i]-strlen($texte)*ImageFontWidth($taille_police)/2);
			//$y=$tab20[2*$i+1]-ImageFontHeight($taille_police)-2;
			$y=$tab20[2*$i+1]-2*ImageFontHeight($taille_police)-2;
		}
		else{
			$x=$tab20[2*$i]+5;
			$x_verif=$x+strlen($texte)*ImageFontWidth($taille_police);

			if($x_verif>$largeurTotale){
				for($j=$taille_police;$j>1;$j--){
					$x_verif=$x+strlen($texte)*ImageFontWidth($j);
					writinfo('/tmp/infos_graphe.txt','a+',"\$j=$j et \$x_verif=$x_verif\n");
					if($x_verif<=$largeurTotale){
						break;
					}
				}
				if($x_verif>$largeurTotale){
					$j=1;
				}
				$tmp_taille_police=$j;
			}

			$y=round($tab20[2*$i+1]-(90-($angle-270))*(ImageFontHeight($taille_police)-2)/90);
		}

		writinfo('/tmp/infos_graphe.txt','a+',"\$x=$x\n");
		writinfo('/tmp/infos_graphe.txt','a+',"\$y=$y\n");

		//imagestring ($img, $taille_police, $x, $y, strtr($texte,"_"," "), $axes);
		//imagestring ($img, $tmp_taille_police, $x, $y, strtr($angle." ".$texte,"_"," "), $axes);
		imagestring ($img, $tmp_taille_police, $x, $y, strtr($texte,"_"," "), $axes);



		// Ajout des notes sous le nom de mati�re:
		$ytmp=$y+2+ImageFontHeight($taille_police);
		//**************
		// A FAIRE:
		// Correctif � arranger... pour positionner au mieux en fonction de l'angle
		if(($angle>270)&&($angle<360)){$xtmp=$x+30;}else{$xtmp=$x;}
		//**************
		for($k=1;$k<=$nb_series_bis;$k++){
			//imagestring ($img, $taille_police, $xtmp, $ytmp, $moyenne[$k][$i+1], $couleureleve[$k]);
			imagestring ($img, $taille_police, $xtmp, $ytmp, nf($moyenne[$k][$i+1]), $couleureleve[$k]);
			//imagestring ($img, $taille_police, $xtmp, $ytmp, "A", $couleureleve[$k]);
			//$xtmp=$xtmp+strlen($moyenne[$k][$i+1]." - ")*ImageFontWidth($taille_police);
			$xtmp=$xtmp+strlen(nf($moyenne[$k][$i+1])." ")*ImageFontWidth($taille_police_inf);
		}
	}
	//=================================



	//=================================
	// Titre de l'image,...
	if($legendy[2]=='Toutes_les_p�riodes'){
		$chaine=$nom_periode;
	}
	else{
		//$chaine=$eleve;
		$chaine=$nom_eleve;
	}

	// Calcul de la largeur occup�e par les noms d'�l�ves:
	//$total_largeur_eleves=0;
	$total_largeur_chaines=0;
	//for($k=1;$k<$nb_data;$k++){
	for($k=1;$k<=$nb_series;$k++){
		//$largeur_eleve[$k] = strlen($eleve[$k]) * ImageFontWidth($taille_police);
		//$total_largeur_eleves=$total_largeur_eleves+$largeur_eleve[$k];

		//$largeur_chaine[$k] = strlen($chaine[$k]) * ImageFontWidth($taille_police);
		//$largeur_chaine[$k] = strlen($chaine[$k]." (".nf($mgen[$k]).")") * ImageFontWidth($taille_police);
		if($mgen[$k]!="") {
			$chaine_mgen=" (".nf($mgen[$k]).")";
		}
		else {
			$chaine_mgen="";
		}
		$largeur_chaine[$k] = strlen($chaine[$k].$chaine_mgen) * ImageFontWidth($taille_police);

		$total_largeur_chaines=$total_largeur_chaines+$largeur_chaine[$k];
	}

	// Calcul de l'espace entre ces noms d'�l�ves:
	// Espace �quilibr� comme suit:
	//     espace|Eleve1|espace|Eleve2|espace
	// Il faudrait �tre s�r que l'espace ne va pas devenir n�gatif...
	//$espace=($largeur-$total_largeur_eleves)/($nb_series+1);
	//$espace=($largeur-$total_largeur_chaines)/($nb_series+1);
	$espace=($largeurTotale-$total_largeur_chaines)/($nb_series+1);


	if($legendy[2]=='Toutes_les_p�riodes'){
		$chaine=$nom_periode;

		imagestring ($img, $taille_police, round(($largeurTotale-strlen($nom_eleve[1]) * ImageFontWidth($taille_police))/2), 5, $nom_eleve[1], $axes);

		// Positionnement des noms d'�l�ves:
		//$xtmp=$largeurGrad;
		$xtmp=0;
		//for($k=1;$k<$nb_data;$k++){
		for($k=1;$k<=$nb_series;$k++){
			$xtmp=$xtmp+$espace;
			//imagestring ($img, $taille_police, $xtmp, 5, $eleve[$k], $couleureleve[$k]);
			//$xtmp=$xtmp+$largeur_eleve[$k];
			//imagestring ($img, $taille_police, $xtmp, ImageFontHeight($taille_police)+5, strtr($chaine[$k],"_"," "), $couleureleve[$k]);
			//imagestring ($img, $taille_police, $xtmp, ImageFontHeight($taille_police)+5, strtr($chaine[$k],"_"," ")." (".nf($mgen[$k]).")", $couleureleve[$k]);
			if($mgen[$k]!="") {
				$chaine_mgen=" (".nf($mgen[$k]).")";
			}
			else {
				$chaine_mgen="";
			}
			imagestring ($img, $taille_police, $xtmp, ImageFontHeight($taille_police)+5, strtr($chaine[$k],"_"," ").$chaine_mgen, $couleureleve[$k]);

			$xtmp=$xtmp+$largeur_chaine[$k];
		}

	}
	else{
		//$chaine=$eleve;
		$chaine=$nom_eleve;

		// Positionnement des noms d'�l�ves:
		//$xtmp=$largeurGrad;
		$xtmp=0;
		//for($k=1;$k<$nb_data;$k++){
		for($k=1;$k<=$nb_series;$k++){
			$xtmp=$xtmp+$espace;
			//imagestring ($img, $taille_police, $xtmp, 5, $eleve[$k], $couleureleve[$k]);
			//$xtmp=$xtmp+$largeur_eleve[$k];
			//imagestring ($img, $taille_police, $xtmp, 5, strtr($chaine[$k],"_"," "), $couleureleve[$k]);
			//imagestring ($img, $taille_police, $xtmp, 5, strtr($chaine[$k],"_"," ")." (".nf($mgen[$k]).")", $couleureleve[$k]);
			if($mgen[$k]!="") {
				$chaine_mgen=" (".nf($mgen[$k]).")";
			}
			else {
				$chaine_mgen="";
			}
			imagestring ($img, $taille_police, $xtmp, 5, strtr($chaine[$k],"_"," ").$chaine_mgen, $couleureleve[$k]);
			$xtmp=$xtmp+$largeur_chaine[$k];
		}
	}
	//=================================


	imagePNG($img);

	imageDestroy($img);
	exit();
?>
