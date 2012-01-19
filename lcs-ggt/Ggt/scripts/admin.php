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
elseif ($_SESSION['login']!="admin") exit;

//include "../Includes/functions2.inc.php";
$tsmp=time();
$ind=0;
$tsmp2=time() + 604800;//j+7

include ("/var/www/lcs/includes/user_lcs.inc.php");
include ("/var/www/lcs/includes/functions.inc.php");

// Connexion a la base de donnaes
require_once ('../Includes/config.inc.php');
// Creer la requete
$rq = "SELECT id_niv,nom FROM niveaux  ORDER BY ordre ASC ";

 // lancer la requ&egrave;ete
$result = mysql_query ($rq) or die (mysql_error());

if ( mysql_num_rows($result)>0)
	{
	$loop=0;
	while ($enrg = mysql_fetch_array($result, MYSQL_NUM))
		{
		$niveaux[$loop]=$enrg[1];
                                    $id_niveau[$loop]=$enrg[0];
                                    if ($id_niveau[$loop]==$_POST['niv_fich'] || $id_niveau[$loop]==$_POST['niv_fich1']) $ind=$loop;
		$loop++;

		}

	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html  xmlns="http://www.w3.org/1999/xhtml" >
<head>
<title>Gestion de fiches ateliers</title>
<meta name="author" content="Philippe LECLERC -TICE CAEN" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link  href="../../../libjs/jquery-ui/css/redmond/jquery-ui.css" rel="stylesheet" type="text/css" />
<link href="../style/ap.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../../../libjs/jquery/jquery.js"></script>
<script type="text/javascript" src="../../../libjs/jquery-ui/jquery-ui.js"></script>
<script type="text/javascript" src="../../../libjs/tiny-mce/tiny_mce.js"></script>
<script type="text/javascript" src="../Includes/conf-tiny_mce.js"></script>
          <script type="text/javascript" src="../Includes/admin.js"></script>
 </head>
<body>
<p></p>
<?php
//$niv=$id_niveau[0];
//$niv = (isset($_GET['rubrique'])) ? $_GET['rubrique'] :  $_POST['numongl'];
$niv = (isset($_POST['niv_fich'])) ? $_POST['niv_fich'] : $id_niveau[$ind];
$niv1 = (isset($_POST['niv_fich1'])) ? $_POST['niv_fich1'] : $id_niveau[$ind];
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
echo '<button id="add_tab">Ajouter un groupe de travail </button>&nbsp;&nbsp;';
if ( count ($niveaux)>1) echo '<span class="ui-state-highlight ui-corner-all">  L\'ordre des onglets est modifiable par glisser d&eacute;poser&nbsp; </span>';
echo '<form id="form1" action="'. htmlentities($_SERVER['PHP_SELF']).'" method="post">
<input type="hidden" name="niv_fich" id="niveau" value= "'.$id_niveau[$ind].'" />
</form> ';

//creation niveau
echo ' <div id="dialog" title="Cr&eacute;ation d\'un groupe de travail">
<form>
<fieldset class="ui-helper-reset">
<p class="validateTips"></p>
<label for="tab_title">Nom</label>
<input type="text" name="tab_title" id="tab_title" value="" class="ui-widget-content ui-corner-all" />
<label for="coord">Professeur coordinateur</label>';
$uids = search_uids ("cn=Profs", "half");
$people = search_people_groups ($uids,"(sn=*)","cat");
echo '<select name="coordinateur" id="coord" class="ui-widget-content ui-corner-all">';
for ($loop=0; $loop <count($people); $loop++)
        {
        $uname = $people[$loop]['uid'];
        $nom = $people[$loop]["fullname"];
        echo '<option value="'.$uname.'"';
        echo '>'.$nom.'</option>';
        }
echo ' </select>';
echo '</fieldset>


</form>
</div>';

//edition niveau
echo ' <div id="dialogbis" title="Edition d\'un groupe de travail">
<formbis>
<fieldset class="ui-helper-reset">
<p class="validateTips"></p>
<label for="tab_titlebis">Nom</label>
<input type="text" name="tab_titlebis" id="tab_titlebis" value="" class="ui-widget-content ui-corner-all" />
<label for="coordbis">Professeur coordinateur</label>';
$uids = search_uids ("cn=Profs", "half");
$people = search_people_groups ($uids,"(sn=*)","cat");
echo '<select name="coordinateur" id="coordbis" class="ui-widget-content ui-corner-all">';
//echo '<option value="0">---</option>';
for ($loop=0; $loop <count($people); $loop++)
        {
        $uname = $people[$loop]['uid'];
        $nom = $people[$loop]["fullname"];
        echo '<option value="'.$uname.'"';
        echo '>'.$nom.'</option>';
        }
echo ' </select>';
echo '<input type="hidden" name="niv_id" id="niv_id" value= "" />';
echo '</fieldset>
</form>
</div>';



echo '<div id="tabs">
	<ul>';
	for ($loop=0; $loop < count ($niveaux); $loop++)
		{
		echo '<li class="ong"><a  href="#tabs-'.$loop.'" tabindex="'.$id_niveau[$loop].'">'.$niveaux[$loop].'</a>
                    <span class="ui-icon ui-icon-pencil"></span><span class="ui-icon ui-icon-close">Remove Tab</span></li>';
		}

	echo '</ul>';
 ?>

<span id="aide">&nbsp;</span>

<!-- End masquable -->

<?php
echo '<div id="sortable">';
for ($loop=0; $loop < count ($niveaux); $loop++)
	{
	echo '<div id="tabs-'.$loop.'">

                <ul id="sortable'.$loop.'" class="connectedSortable ui-helper-reset  ">';
	$rq = "SELECT id_at,nom,description,prof,niveau,is_propose FROM ateliers WHERE  niveau='".$id_niveau[$loop]."' order by id_at ASC";

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
		</li>';
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
