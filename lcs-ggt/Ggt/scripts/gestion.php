<?php
/* ==================================================
   Projet LCS : Linux Communication Server
  Plugin "Gestion ateliers AP"
  VERSION 1.0 du 15/12/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   -  gestion des ateliers -
			_-=-_
    "Valid XHTML 1.0 Strict"
   =================================================== */

header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
session_name("gestap_Lcs");
@session_start();

//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;

//include "../Includes/functions2.inc.php";
$tsmp=time();
$ind=0;
$tsmp2=time() + 604800;//j+7

include ("/var/www/lcs/includes/user_lcs.inc.php");
include ("/var/www/lcs/includes/functions.inc.php");
$liste_classes=search_groups('cn=classe*');
// Connexion a la base de donnaes
require_once ('../Includes/config.inc.php');
// Creer la requete
$rq = "SELECT id_niv,nom,coordinateur FROM niveaux  ORDER BY ordre ASC ";

 // lancer la requ&egrave;ete
$result = mysql_query ($rq) or die (mysql_error());

if ( mysql_num_rows($result)>0)
	{
	$loop=0;
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
		{
                                    $coord[$loop]=$enrg[2];
		$niveaux[$loop]=$enrg[1];
                                    $id_niveau[$loop]=$enrg[0];
                                    if ($id_niveau[$loop]==$_POST['niv_fich'] || $id_niveau[$loop]==$_POST['niv_fich1']) $ind=$loop;
		$loop++;

		}

	}

else 
	{
	echo "Cette application n'a pas été initialisée par l'administrateur";
	exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Gestion des groupes de travail</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link  href="../../../libjs/jquery-ui/css/redmond/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="../style/ap.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../../../libjs/jquery/jquery.js"></script>
<script type="text/javascript" src="../../../libjs/jquery-ui/jquery-ui.js"></script>
<script type="text/javascript" src="../../../libjs/tiny-mce/tiny_mce.js"></script>
<script type="text/javascript" src="../Includes/conf-tiny_mce.js"></script>
<script type="text/javascript" src="../Includes/JQ-fiches.js"></script>
 </head>
<body>
<p></p>
<?php
//$niv=$id_niveau[0];
//$niv = (isset($_GET['rubrique'])) ? $_GET['rubrique'] :  $_POST['numongl'];
$niv = (isset($_POST['niv_fich'])) ? $_POST['niv_fich'] : $id_niveau[$ind];
$niv1 = (isset($_POST['niv_fich1'])) ? $_POST['niv_fich1'] : $id_niveau[$ind];
$coor=$coord[$ind];
echo '<fieldset id="field10">
<legend id="legende">Gestion des groupes de travail</legend>';
//activation de l'onglet
echo '<script  type="text/javascript"> ';
echo 'function active(){';
echo 'var $tabs = $("#tabs").tabs(); ';
echo '$tabs.tabs("select",'.$ind.'); ';
echo 'return false;';
echo '};';
echo 'setTimeout("active()",1);';
//
//echo 'setTimeout("reorganise(\'sortable'.$ind.'\')",1);';
echo '</script> ';
//fin des scripts
//html
echo '<button id="helpp" >Aide </button>';
echo '<button id="showr" >Cr&eacute;er une fiche </button>';
echo '<button id="prop" ';
if ($coor!=$_SESSION['login'] )echo 'class="v_no"';
echo '>Proposer aux &eacute;l&egrave;ves</button>';
echo '<button id="repa" ';
if ($coor!=$_SESSION['login'] )echo 'class="v_no"';
echo '>R&eacute;partion des &eacute;l&egrave;ves</button> ';

echo '<form id="form1" action="'. htmlentities($_SERVER['PHP_SELF']).'" method="post">
<input type="hidden" name="niv_fich" id="niveau" value= "'.$id_niveau[$ind].'" />
</form> ';


//creation atelier
echo '<div id="dialog1" title="Cr&eacute;ation d\'une fiche">
<form id="form_seq1" action="'. htmlentities($_SERVER['PHP_SELF']).'" method="post" >
                    <fieldset class="ui-helper-reset">
                    <p class="validateTips"></p>
                    <label for="intitule">Intitul&eacute;</label><br />
	 <input type="text"  name="intitule" id="int_fich1" value="" size="50" maxlength="50" class="ui-widget-content ui-corner-all" /><br /><br />
                    <label for="tab_content">Description de l\'atelier  :</label><br />
	  <textarea name="Cont_seq" class="mceAdvanced" id="desc_fich1"  ></textarea><br />
                   <span class="ui-state-highlight ui-corner-all">
                   <input type="checkbox" name="proposed" id="is_prop1" class="boxcheck" checked="" />
                    <label for="boxcheck"> Cocher pour mettre cette fiche &agrave; disposition du professeur coordinateur de ce niveau </label>
                    </span>
                    <input type="hidden" id="prof" value= "'.$_SESSION['login'].'" />
                    <input type="hidden" name="niv_fich" id="niv_fich1" value= "'.$niv1.'" />
                    </fieldset>
</form>
</div>';

//edition atelier
echo '<div id="dialog2" title="Edition d\'une fiche">
<form id="form_seq" action="'. htmlentities($_SERVER['PHP_SELF']).'" method="post" >
                    <fieldset class="ui-helper-reset">
                    <p class="validateTips"></p>
                    <label for="intitule">Intitul&eacute;</label><br />
	 <input type="text"  name="intitule" id="int_fich" value="" size="50" maxlength="50" class="ui-widget-content ui-corner-all" /><br /><br />
                    <label for="tab_content">Description de l\'atelier  :</label><br />
	  <textarea name="Cont_seq" class="mceAdvanced" id="desc_fich"  ></textarea><br />
                    <span class="ui-state-highlight ui-corner-all">
                    <input type="checkbox" name="proposed" id="is_prop" class="boxcheck" checked="" />
                    <label for="boxcheck"> Cocher pour mettre cette fiche &agrave; disposition du professeur coordinateur de ce niveau :</label>
                    </span>
                    <input type="hidden" id="id_fich" value= "" />
                    <input type="hidden" id="prof" value= "'.$_SESSION['login'].'" />
                    <input type="hidden" name="niv_fich" id="niv_fich" value= "'.$niv.'" />
                    </fieldset>
</form>
</div>';
//proposer aux eleves
echo ' <div id="dialog3" title="Proposer des ateliers aux &eacute;l&egrave;ves">
<form id="form_p" action="'. htmlentities($_SERVER['PHP_SELF']).'" method="post">
<fieldset class="ui-helper-reset">
<label for="tab_title31">Liste des ateliers proposés par les professeurs de ce groupe de travail </label>
<div class="ui-widget-content ui-corner-all" id="tab_title31" >';
echo '</div>';
echo '<label for="tab_title34">P&eacute;riode de déroulement</label>
     <div class="ui-state-default ui-corner-all ">
    <p> Début : <input id="datepicker3" type="text" class ="date" value="';echo date('d/m/Y').'" >  Fin : <input id="datepicker4" type="text" class ="date"value="';echo date('d/m/Y').'"></p>
     </div>';
echo '<label for="proposed">Classes concern&eacute;es</label>
    <div class="ui-widget-content ui-corner-all" id=tab_title32>';
echo '</div>';
echo '<label for="tab_title33">P&eacute;riode d\'inscription</label>
     <div class="ui-state-default ui-corner-all ">
    <p> Début : <input id="datepicker1" type="text" class ="date" value="';echo date('d/m/Y').'" > Fin : <input id="datepicker2" type="text" class ="date"value="';echo date('d/m/Y').'"></p>
    <input type="hidden" name="niv_p" id="niv_p" value= "'.$niv.'" />
     </div>
<p class="validateTips "></p>
</fieldset>
    </form>
    </div>';
//aide proposer
echo ' <div id="dialog6" title="Aide proposer...">';
echo '<div class="ui-widget-content ui-corner-all">
    <ol>
        <li>Généralités </li>
        <ul>
            <li> Pour un groupe de travail , à un instant "t", il n\'existe qu\'une seule proposition d\'inscription</li>
            <li> Les élèves ne voient la proposition que pendant la période d\'inscription (jours de début et fin  inclus)</li>
            <li> Si la proposition doit être modifiée <u>pendant</u> la période d\'inscription, vérifier que la liste des ateliers n\'a pas changé.</li>
            <li> Ne pas supprimer de classes pendant la période d\'inscription</li>
            <li> Pour interrompre momentanément les inscriptions, "jouer" uniquement sur la date fin des inscriptions</li>
         </ul>
         <br />
        <li>Fonction des boutons</li>
            <ul>
            <li><b> Réinitialiser</b> : Supprime  les voeux des élèves et la proposition d\'inscription pour ce groupe de travail.
            Cette opération est nécessaire pour effacer les données précédentes, lorsqu\'on commence une nouvelle "série". </li>
            <li><b> Valider</b> : Enregistre la proposition d\'inscription. Celle çi peut être modifiée en dehors de la période d\'inscription</i>
            ';
            echo '</ul></ol>';
echo '</div></div>';
//
//repartition
echo ' <div id="dialog4" title="R&eacute;partition des &eacute;l&egrave;ves dans les ateliers">';
echo '</div>';
//findial4

//aide repartition
echo ' <div id="dialog5" title="Aide répartition">';
echo '<div class="ui-widget-content ui-corner-all">
    <ol>
        <li>Généralités </li>
        <ul>
            <li> Les élèves peuvent être changés de liste par un glisser/déposer</li>
            <li> Les listes peuvent aussi être déplacées dans la page</li>
            <li> Pour augmenter l\'espace de travail, les listes peuvent être pliées en cliquant sur l\'icone - </li>
            <li> Les carrrés de couleurs permettent de garder une trace des voeux (voeu 1, voeu 2, voeu 3) formulés  par les élèves</li>
         </ul>
         <br />
        <li>Fonction des boutons</li>
            <ul>
            <li><b> Réinitialiser</b> : Génère la répartition à partir des voeux des élèves. Cette opération est nécessaire pour effacer les données précédentes, lorsqu\'on commence une nouvelle "série". </li>
            <li><b> Enregistrer</b> : Sauvegarde la répartition telle qu\'elle apparait à l\'écran. C\'est la dernière répartition enregistrée qui s\'affiche lorsqu\'on ouvre
             la fenêtre "Répartition des élèves dans les ateliers"<br /><i> Si ce bouton n\'est pas utilisé pendant la période d\'inscription, on peut contrôler les inscriptions
             à chaque ouverture de la fenêtre</i></li>
            <li> <b>Diffuser</b> : Affecte les élèves à leur atelier à partir de <u>la dernière répartition enregistrée</u>. Il est donc IMPÉRATIF d\'enregistrer la répartition juste avant de Diffuser</li>
             <li><b> Imprimer</b> : Imprime une copie d\'écran. Vérifier que tous les listes sont "dépliées". </li>';
            echo '</ul></ol>';
echo '</div></div>';

//aide prof
echo ' <div id="dialog7" title="Aide">';
echo '<div class="ui-widget-content ui-corner-all">

        <ul>
            <li> Le bouton <i> Créer une fiche</i> permet de créer une fiche descriptive de l\'atelier que vous souhaitez proposer. Le contenu de la fiche est entièrement modifiable</li><br />
            <li>Vous pouvez créer autant de fiches que vous le souhaitez</li><br />
            <li> La case à cocher en bas du formulaire de création de la fiche, permet de mettre votre atelier à disposition du coordinateur pour qu\'il puisse le proposer aux élèves</li><br />
            <li> Lorsqu\'une fiche est proposée, elle apparait avec une bordure épaisse</li><br />
            <li> Normalement, il ne doit y a avoir au maximum qu\'une de vos fiches proposée.</li><br />
            <li> Pour cocher/décocher la case d\'une fiche enregistrée, cliquer sur "Editer", cocher/décocher puis Enregistrer et Fermer</li>
         </ul>';
echo '</div></div>';


echo '<div id="tabs">
	<ul >';
	for ($loop=0; $loop < count ($niveaux); $loop++)
		{
		echo '<li class="ong"><a  href="#tabs-'.$loop.'" tabindex="'.$id_niveau[$loop].'">'.$niveaux[$loop].'</a>
                    </li>';
		}

	echo '</ul>';
 ?>

<!-- End masquable -->

<?php
echo '<div id="sortable">';
for ($loop=0; $loop < count ($niveaux); $loop++)
	{
	echo '<div id="tabs-'.$loop.'">

                <ul id="sortable'.$loop.'" class="connectedSortable ui-helper-reset  ">';
	$rq = "SELECT id_at,nom,description,prof,niveau,is_propose FROM ateliers WHERE prof='".$_SESSION['login']."' AND niveau='".$id_niveau[$loop]."' order by id_at ASC";

	$result = @mysql_query ($rq) or die (mysql_error());
	$j=0;
	$TitreAt=array();
	while ($row = mysql_fetch_object($result))
		{
		$IdAt[$j]=$row->id_at;
		$TitreAt[$j]=$row->nom;
		$ContenuAt[$j]=$row->description;
		$Etat[$j]=$row->is_propose;
		$j++;
		}
	//affichage
	for ($i=0; $i< count ($TitreAt); $i++)
		{
		echo '<li class="ui-state-default sequ';
                                    if ($Etat[$i]=="1") echo ' prop';
                                    echo '" id="li'.$IdAt[$i].'"  title="'.$TitreAt[$i].'">
                		- '.$TitreAt[$i].'
		<span class="buttons"><button class="showform" tabindex="'.$IdAt[$i].'">Editer</button>
		<button class="delet" tabindex="'.$IdAt[$i].'">Supprimer</button></span></li>';
		}
                   // echo $niveaux[$loop];
	echo '</ul></div>';
	}

echo '</div><!-- fin sortable -->';
echo '</div><!-- fin tabs -->';
echo '</fieldset>';

//include ('../Includes/pied.inc');
?>
</body>
</html>
