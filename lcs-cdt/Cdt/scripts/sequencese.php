<?php
/* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "cahier de textes"
  VERSION 2.3 du 06/01/2011
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script de gestion des sequences -
			_-=-_
    "Valid XHTML 1.0 Strict"
   =================================================== */
   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache"); 
session_name("Cdt_Lcs");
@session_start();

//si la page est appelee par un utilisateur non identifie
if (!isset($_SESSION['login']) )exit;

//si la page est appelee par un utilisateur non prof
elseif ($_SESSION['cequi']!="prof") exit;

include "../Includes/functions2.inc.php";
$tsmp=time();
$tsmp2=time() + 604800;//j+7


// Connexion a la base de donnaes
require_once ('../Includes/config.inc.php');
// Creer la requete (Recuperer les rubriques de l'utilisateur// doit etre identique  a la requete du cdt prof) 
$rq = "SELECT classe,matiere,id_prof FROM onglets
 WHERE login='{$_SESSION['login']}' ORDER BY id_prof ASC ";

 // lancer la requ&egrave;ete
$result = mysql_query ($rq) or die (mysql_error());

if ( mysql_num_rows($result)>0) 
	{
	$loop=0;
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM)) 
		{
		$clas[$loop]=$enrg[0];
		$mat[$loop]=$enrg[1];
		$numero[$loop]=$enrg[2];
		if ($numero[$loop]==$_GET['rubrique'] || $numero[$loop]==$_POST['numongl'] ) $ind=$loop;
		$loop++;
		}
		
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Gestion des s&eacute;quences</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link href="../style/style.css" rel="stylesheet" type="text/css" />
        <link href="../style/sequences.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.all.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.datepicker.css" rel="stylesheet" type="text/css" />
	<link  href="../style/ui.theme.css" rel="stylesheet" type="text/css" />
	<link  href="../style/my_ui.tabs.css" rel="stylesheet" type="text/css" />

	<!--[if IE]>
        <link href="../style/style-ie.css"  rel="stylesheet" type="text/css"/>
        <![endif]-->

	<script type="text/javascript" src="../Includes/JQ/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="../Includes/JQ/ui.core.js"></script>  
	<script type="text/javascript" src="../Includes/JQ/ui.datepicker.js"></script>
	<script type="text/javascript" src="../Includes/JQ/cdt-script.js"></script>
	<script type="text/javascript" src="../Includes/JQ/ui.sortable.js"></script>
	<script type="text/javascript" src="../Includes/JQ/ui.droppable.js"></script>
	<script type="text/javascript" src="../Includes/JQ/ui.tabs.js"></script>
	<script type="text/javascript" src="../Includes/JQ/ui.dialog.js"></script>
	<script type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript" src="../Includes/conf-tiny_mce.js"></script>
        <script type="text/javascript" src="../Includes/JQ/JQ-sequences.js"></script>

	
</head>
<body>
    <p></p>
<?php
$ru = (isset($_GET['rubrique'])) ? $_GET['rubrique'] :  $_POST['numongl'];
$ru = (isset($_POST['numongl'])) ? $_POST['numongl'] : $_GET['rubrique'];
echo '<fieldset id="field10">
<legend id="legende">Gestion des s&#233;quences p&#233;dagogiques</legend>';
//activation de l'onglet
echo '<script  type="text/javascript"> ';
echo 'function active(){';
echo 'var $tabs = $("#tabs").tabs(); ';
echo '$tabs.tabs("select",'.$ind.'); ';
echo 'return false;';
echo '};';
echo 'setTimeout("active()",1);';
//
echo 'setTimeout("reorganise(\'sortable'.$ind.'\')",1);';
echo '</script> ';
//fin des scripts
//html
echo '<div id="tabs">
	<ul>';
	for ($loop=0; $loop < count ($clas); $loop++)
		{
		echo '<li class="ong"><a  href="#tabs-'.$loop.'" tabindex="'.$numero[$loop].'">'.$mat[$loop].'<br />'. $clas[$loop].'</a></li>';
		}
	
	echo '</ul>';
 ?>
<button id="showr" >Cr&eacute;er une s&eacute;quence </button>
<span id="aide">&nbsp;</span>

<div id="masquable">
<form id="form_seq" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
	<p>Titre Court : <input type="text"  id="tc" value="" size="20" maxlength="20" /></p>
	<p>Titre Long : <input type="text" id="tl" value="" size="100" maxlength="100" /> </p>
	<p>Description de la s&#233;quence  :</p>
        <div><textarea id="contenu_sequence" name="Cont_seq" class="SeqmceAdvanced"  style="width:100%" rows="30" cols="90"></textarea>
	<input type="hidden" name="numongl" id="numong" value= "<?php echo $ru; ?>" />
	<input type="hidden" id="numseq" value= "" />
	<input type="hidden" id="closeandsubmit" value= "" />
        </div>
</form> 
<button id="record">Enregistrer</button> <button id="update">Enregistrer les modifications </button> <button id="hider">Fermer</button>
</div>
<!-- End masquable -->
<div id="dialog" title="Aide sur l'utilisation des séquences">
	<p>Le dispositif de <span style="color: #993300;">s&eacute;quences</span> p&eacute;dagogiques permet de regrouper visuellement plusieurs s&eacute;ances dans l'affichage du cahier de textes.</p>
<ol >
<li>Une s&eacute;quence p&eacute;dagogique comporte :
<ul>
<li>un <span style="color: #0000ff;">titre court</span> permettant de s&eacute;lectionner la s&eacute;quence pour y associer une s&eacute;ance</li>
<li>un <span style="color: #0000ff;">titre long</span> qui s'affichera dans le cahier de texte pour identifier une s&eacute;quence</li>
<li>une <span style="color: #0000ff;">description</span> indiquant les objectifs, les comp&eacute;tences vis&eacute;es, etc ... La description apparait aussi dans le cahier de texte.<br /><br /></li>
</ul>
</li>
<li>Une fois cr&eacute;&eacute;e, la s&eacute;quence apparait dans la page de gestion sous la forme d'un item dans une liste num&eacute;rot&eacute;e et d&eacute;pla&ccedil;able.<br />Pour que cette nouvelle
s&eacute;quence apparaisse dans la boite de selection (page saisie du cdt), il faudra recharger la page saisie en cliquant sur l'onglet correspondant.<br /></li>
<li>Les s&eacute;quences seront <span style="color: #0000ff;">affich&eacute;es</span> en priorit&eacute; <span style="color: #0000ff;">dans l'ordre fix&eacute;</span> dans la page de gestion des s&eacute;quences. <br />La s&eacute;quence n&deg;1 est consid&eacute;r&eacute;e la plus r&eacute;cente et s'affichera donc en haut du contenu du cahier de texte, suivie de la n&deg;2, etc.. <br />Il conviendra de maintenir une coh&eacute;rence entre l'ordre d'affichage et les dates des s&eacute;ances associ&eacute;es aux s&eacute;quences <br />Les s&eacute;ances non associ&eacute;es &agrave; une s&eacute;quence s'intercaleront entre les s&eacute;quences en fonction de leur date.<br /></li>
<li>Une s&eacute;quence peut &ecirc;tre <span style="color: #0000ff;">d&eacute;plac&eacute;e  </span>par un clic maintenu sur l'ic&ocirc;ne<span class="ui-state-default"><span class="ui-icon ui-icon-arrow-4 handle"></span></span></li>
<li>L'<span style="color: #0000ff;">ordre</span> des s&eacute;quences peut &ecirc;tre <span style="color: #0000ff;">modifi&eacute; </span>simplement en d&eacute;pla&ccedil;ant verticalement une s&eacute;quence dans la liste.<br /></li>
<li>Une s&eacute;quence peut &ecirc;tre <span style="color: #0000ff;">d&eacute;plac&eacute;e</span> vers un autre onglet par un "glisser/d&eacute;poser" sur l'onglet .<br /></li>
<li>Une s&eacute;quence peut &ecirc;tre <span style="color: #0000ff;">copi&eacute;e </span>vers un autre onglet en appuyant sur la touche "<span style="color: #0000ff;">Shift</span>" pendant le "d&eacute;placement"glisser/d&eacute;poser" vers un autre onglet.</li>
<li>Pour associer une s&eacute;ance &agrave; une s&eacute;quence, il suffit lors de la saisie de la s&eacute;ance, de s&eacute;lectionner la s&eacute;quence &agrave; laquelle on veut l'associer. </li>
<li>L'association peut &ecirc;tre ajout&eacute;e/modifi&eacute;e/supprim&eacute;e &agrave; post&eacute;riori en cliquant sur le bouton modifier d'une s&eacute;ance enregistr&eacute;e.</li>
</ol>
</div>
<?php
echo '<div id="sortable">';

//creation des onglets (identiques au cdt)
for ($loop=0; $loop < count ($clas); $loop++)
	{
	echo '<div id="tabs-'.$loop.'"><ul id="sortable'.$loop.'" class="connectedSortable ui-helper-reset seq ">';
	$rq = "SELECT id_seq,titre,titrecourt,contenu,ordre FROM sequences
	WHERE id_ong='$numero[$loop]' order by ordre ASC";
	// lancer la requête
	$result = @mysql_query ($rq) or die (mysql_error());
	// Combien y a-t-il d'enregistrements ?
	$nb = mysql_num_rows($result); 
	//on récupère les données
	$j=0;
	$TitreSeq=array();
	while ($row = mysql_fetch_object($result))
		{
		$IdSeq[$j]=$row->id_seq;
		$TitreSeq[$j]=$row->titre;
		$TitreCourSeq[$j]=$row->titrecourt;
		$ContenuSeq[$j]=$row->contenu;
		$OrdreSeq[$j]=$row->ordre;
		$j++;
		} 
	//affichage des sequences de l'onglet
	for ($i=0; $i< count ($TitreSeq); $i++)
		{	
		echo '<li class="ui-state-default sequ" id="li'.$IdSeq[$i].'"  title="'.$TitreCourSeq[$i].'">
		<span class="ui-icon ui-icon-arrow-4 handdle"></span>
                <span class="order" >'.$OrdreSeq[$i].'</span>
		- '.$TitreCourSeq[$i].'
		<span class="buttons"><button class="showform" tabindex="'.$IdSeq[$i].'">Editer</button>
		<button class="delet" tabindex="'.$IdSeq[$i].'">Supprimer</button></span></li>';
		}
	echo '</ul></div>';
	}
	
echo '</div><!-- fin sortable -->
</div><!-- fin tabs -->
</fieldset>';
include ('../Includes/pied.inc');
?>
</body>
</html>
