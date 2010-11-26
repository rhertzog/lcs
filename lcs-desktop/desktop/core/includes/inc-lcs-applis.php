<?php
list($user, $groups)=people_get_variables($login, true);
$i=0;
$list_applis="listapplis {";
// Les blocs fenetres, menu icone, et onglets
$html='';// le bloc qui va recevoir les fenetres de chaque appli activee
$html_menu='';// bloc menu deroulant
$html_menu_services='';// bloc menu deroulant services
$html_icon='';// bloc icones du bureau
$html_status_bar='';// bloc barre d'etat
$left=20; //marge gauche des icones du bureau
$top=20;//marge superieure des icones du bureau
#############
// parametres des liens applis
$liste = array();
$liste['Liens'] = array();
$liste['Images'] = array();
$liste['Titres'] = array();
$liste['Blocks'] = array();
$liste['Ids'] = array();// ajout pour l'ID des fenetres !!Voir si on peut eviter!!
#############

  // lecture lcs_applis 
	$queryM="SELECT  name, value from applis where type='M' order by name";
	$resultM=mysql_query($queryM);
	if ($resultM) {
        while ( $r=mysql_fetch_object($resultM) ) {
            if ( $r->name == "clientftp" ) $ftpclient = true;
            if ( $r->name == "pma" ) $pma = true;
            if ( $r->name == "smbwebclient" ) $smbwebclient = true;            
        }
    }
    mysql_free_result($resultM);

// Affichage des Menus users non privilégies

// Appli Annuaire
$liste['Images'][] = "../lcs/images/bt-V2-3.png";
$liste['Liens'][] = "../lcs/statandgo.php?use=Annu";
$liste['Titres'][] = "Annuaire des utilisateurs";
$liste['Blocks'][] = "#icon_dock_lcs_annu";
$liste['Ids'][] = "annu";
// spip
if(isset($spip)){
	$liste['Images'][] = "../lcs/images/barre1/BP_r1_c6_f3.gif";
	$liste['Liens'][] = "../lcs/statandgo.php?use=spip";
	$liste['Titres'][] = "Forum";
	$liste['Blocks'][] = "#icon_dock_lcs_spip";
	$liste['Ids'][] = "spip";
}
// webmail
if(isset($squirrelmail)){
	$liste['Images'][] = "../lcs/images/barre1/BP_r1_c5_f3.gif";
	$liste['Liens'][] = "../lcs/statandgo.php?use=squirrelmail";
	$liste['Titres'][] = "Webmail";
	$liste['Blocks'][] = "#icon_dock_lcs_squirrelmail";
	$liste['Ids'][] = "squirrelmail";
}
// ftp
if (isset($ftpclient)) {
	$liste['Images'][] = "../lcs/images/bt-V1-2.jpg";
	$liste['Liens'][] = "../clientftp/";
	$liste['Titres'][] = "Client FTP";
	$liste['Blocks'][] = "#icon_dock_lcs_ftpclient";
	$liste['Ids'][] = "ftpclient";
}
// phpmyadmin
if (isset($pma)) {
	$liste['Images'][] = "../lcs/images/bt-V1-3.jpg";
	$liste['Liens'][] = "../lcs/statandgo.php?use=pma";
	$liste['Titres'][] = "Gestion base de donn&eacute;es";
	$liste['Blocks'][] = "#icon_dock_lcs_pma";
	$liste['Ids'][] = "pma";
}
// smbwebclient
if ( $se3netbios != "" && $se3domain != "" && isset($smbwebclient) ) {
	$liste['Images'][] = "../lcs/images/bt-V1-4.jpg";
	$liste['Liens'][] = "../lcs/statandgo.php?use=smbwebclient";
	$liste['Titres'][] = "Client SE3";
	$liste['Blocks'][] = "#icon_dock_lcs_smbwc";
	$liste['Ids'][] = "smbwc";
}

// Liens dynamiques vers les plugins installes 
$query="SELECT * from applis where type='P' OR type='N' order by name";
$result=mysql_query($query);
if ($result) {
        while ($r=mysql_fetch_object($result)) {
          if (( $r->value == "1" ) and ! ( file_exists("/usr/share/lcs/Plugins/".$r->chemin."/.applihide"))) {
            $liste['Images'][] = "../Plugins/".$r->chemin."/Images/plugin_icon.png";
            $liste['Liens'][] = "../lcs/statandgo.php?use=".$r->name;
            $liste['Titres'][] = $r->descr;
            $liste['Blocks'][] = "#icon_dock_lcs_".strtolower($r->name);
			$liste['Ids'][] = strtolower($r->name);
            }
        }
}

mysql_free_result($result);
array_multisort($liste['Titres'],$liste['Liens'],$liste['Images'],$liste['Blocks'],$liste['Ids']);
for ($x=0;$x<count($liste['Titres']);$x++) { 
	// prepare sub-menus bar top
	switch($liste['Ids'][$x]) {
		case "spip":
			$c="large_win";
			$c_sb="submenu";
			$c_path="../spip/";
			$c_title=array($liste['Titres'][$x], "&Eacute;crire un nouvel article");
			$c_rel= array($liste['Liens'][$x], "../spip/ecrire/?exec=articles_edit&new=oui");
	        break;
    	case "squirrelmail":
			$c="large_win";
			$c_sb="submenu";
			$c_path="./squirrelmail/";
			$c_title=array("Consulter vos messages", "Envoyer un message");
			$c_rel=array($liste['Liens'][$x], "../squirrelmail/src/compose.php?mailbox=INBOX&startMessage=1");
	        break;
	    case "annu":
			$c="large_win";
			$c_sb="submenu";
			$c_path="../Annu/";
			$c_title=array("Effectuer une recherche", "Voir ma fiche", " Modifier ma fiche", "Changer de mot de passe");
			$c_rel=array("../Annu/search.php", "../Annu/me.php", "../Annu/mod_entry.php", "../Annu/mod_pwd.php");
	        break;
	    case "maintinfo":
			$c="large_win";
			$c_sb="submenu";
			$c_path="../Plugins/Maintenance/";
			$c_title=array("Demande de support", "En attente", " En cours", "Historique");
			$c_rel=array($c_path."demande_support.php", $c_path."index.php?mnu_choice=wait", $c_path."index.php?mnu_choice=myspool", $c_path."index.php?mnu_choice=wait");
	        break;
	    case "agendas":
	    case "cdt":
	    case "claroline":
	    case "ftpclient":
			$c="large_win";
			$c_sb="";
			$c_path="";
			$c_title="";
			$c_rel="";
	        break;
	    default :
			$c="";
			$c_sb="";
			$c_path="";
			$c_title="";
			$c_rel="";
	}
	!is_array($c_rel) ? $c_sb.=" open_win " :'';
	$liste['Ids'][$x] == "annu" ? $c_sb.=" open_win ":'';
	// Display menus bar top
	$sbmn ="<a class=\"ext_link ".$c_sb."\"";
	if (($c_rel=="") || ($liste['Ids'][$x] == "annu")) { 
		$sbmn.=" href=\"".$liste['Blocks'][$x]."\" rel=\"".$liste['Liens'][$x]."\""; 
	}else{ 
		$sbmn.=" href=\"#\" rel=\"\"" ;
	}
	$sbmn.=" rev=\"".$liste['Ids'][$x]."\">"
	."<img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:16px;height:16px;\" /> "
	.$liste['Titres'][$x]
	."</a>\n";
	
	// Display sub-menus bar top
	if (is_array($c_rel) && $c_rel[0]!="") {
		$sbmn .="<ul>\n";
		$i=0;
		foreach($c_rel as $a_rel){
			$sbmn .="<li>\n";
			$sbmn .= "<a class=\"open_win ext_link\""
			." href=\"".$liste['Blocks'][$x]."\""
			." rel=\"".$a_rel."\""
			." rev=\"".$liste['Ids'][$x]."\">"
			."<img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:16px;height:16px;\" /> "
			.$c_title[$i]."</a>\n";
			$i++;
		}
		$sbmn .="</ul>\n";
	}
	
	// Split the menu applications into two parts: services and applications
	// See if it is necessary and so, how to make choices ?	
	$services=array("ftpclient", "pma", "smbwc", "annu", "maintinfo");
    if(!in_array($liste['Ids'][$x], $services)){
	    $html_menu .= "<li>\n".$sbmn."</li>\n";
    }else{
	    $html_menu_services   .= "<li>\n".$sbmn."</li>\n";
    }
	
	// Display wins
	// on pourrait peut-etre creer les fenetres a la volee lors de l'appel d'une appli. 
	// pour diminuer le temps de chargement de la page d'accueil (qques liogne de html en moins)
	// 
/*   $html.= "\t<div id=\"window_lcs_".$liste['Ids'][$x]."\" class=\"abs window ".$c."\">\n"
    ."\t\t<div class=\"abs window_inner\">\n"
    ."\t\t\t<div class=\"window_top\">\n"
    ."\t\t\t\t<span class=\"float_left\">\n"
    ."\t\t\t\t\t<img src=\"".$liste['Images'][$x]."\" style=\"width:16px;\" />".$liste['Titres'][$x]."\n"
    ."\t\t\t\t</span>\n"
    ."\t\t\t\t<span class=\"float_right\">\n"
    ."\t\t\t\t\t<a href=\"#\" class=\"window_min\"></a><a href=\"#\" class=\"window_resize\"></a>\n"
    ."\t\t\t\t\t<a href=\"".$liste['Blocks'][$x]."\" class=\"window_close\"></a>\n"
    ."\t\t\t\t</span>\n"
    ."\t\t\t</div>\n"
    ."\t\t\t<div class=\"abs window_content\">\n"
    ."\t\t\t\t<div class=\"window_main\" style=\"width:100%;height:100%;margin:0;\">\n"
    ."\t\t\t\t\t<a class=\"appli_link\" href=\"".$liste['Liens'][$x]."\" title=\"".$liste['Blocks'][$x]."\"></a>\n"
    ."\t\t\t\t\t<iframe src=\"\" id=\"iframe_lcs_".$liste['Ids'][$x]."\" width=\"100%\" height=\"98%\" style=\"width:100%;\"></iframe>\n"
    ."\t\t\t\t</div>\n"
    ."\t\t\t</div>\n"
    ."\t\t\t<div class=\"abs window_bottom\">".$liste['Titres'][$x]."</div>\n"
    ."\t\t</div>\n"
    ."\t\t<span class=\"abs ui-resizable-handle ui-resizable-se\"></span>\n"
    ."\t</div>\n";
*/    
    // Too long title
	if($liste['Titres'][$x]=="Gestion Electronique de Documents") $liste['Titres'][$x]="Gestion de Documents";
	
	// Status-bar
	// Modify to display only icons ?
	/*
    $html_status_bar .= "<li id=\"icon_dock_lcs_".$liste['Ids'][$x]."\">"
    ."<a href=\"#window_lcs_".$liste['Ids'][$x]."\">"
    ."<img src=\"".$liste['Images'][$x]."\" />"
    .$liste['Titres'][$x]
    ."</a></li>\n";
	*/
	// Desktop icons
    $html_icon_default .= "<a class=\"abs icon ext_link\""
	    ." style=\"left:".$left."px;top:".$top."px;\""
	    ." href=\"".$liste['Blocks'][$x]."\""
	    ." rel=\"".$liste['Liens'][$x]."\""
	    ." rev=\"".$liste['Ids'][$x]."\">"
	    ."<img src=\"".$liste['Images'][$x]."\"  style=\"width:32px;\" />"
	    .$liste['Titres'][$x]
    ."</a>";
    
    // creation dune liste des applis
    $i_meta=$x.": '".$liste['Ids'][$x]."'";
    $x==0 ? $list_applis.=$i_meta : $list_applis.=", ".$i_meta ;
    // creation des colonnes Peut mieux faire
    $top=$top+80;
    if($x==5) { $left=$left+120;$top=20;}
    if($x==11) { $left=$left+120;$top=20;}
    if($x==16) { $left=$left+120;$top=20;}
}
$list_applis.="}";
?>