<?
require  "../lcs/includes/headerauth.inc.php";
$query = "SELECT * from applis";
$result=@mysql_db_query("$DBAUTH",$query, $authlink);
if ($result)
    while ($r=@mysql_fetch_array($result))
                $$r["name"]=$r["value"];
else
    die ("parametres absents de la base de donnees");
@mysql_free_result($result);

if ( $url_redirect == "accueil.php" || $url_redirect == "../squidGuard/pageinterdite.html" ) $url_accueil = $url_redirect;

if ( $url_accueil == "accueil.php" && is_dir ("/var/www/monlcs") )  
  $url_accueil = "/monlcs/index.php";
  $url_accueil = "../monlcs/index.php";
  
/* lcs/barre.php derniere mise a jour : 12/06/2008 */
//require "includes/headerauth.inc.php";
require "../Annu/includes/ldap.inc.php";
require "../Annu/includes/ihm.inc.php";
 
// A supprimer ?
$path ="barre1";

//tests

//Recuperation des groupes d'appartenance du user
list ($idpers, $login)= isauth();

//on recupere le groupe principal du user 
	        	
////// script de MrPhi (extrait)
// Recherche des groupes d'appartenance de l'utilisateur $login

//	include ("/var/www/lcs/includes/user_lcs.inc.php");
list($user, $groups)=people_get_variables($login, true);
$i=0;

// Recherche du groupe principal 
for ($loop=0; $loop < count ($groups) ; $loop++) {
	if ( $groups[$loop]["cn"] == "Administratifs" ) $group_principal = "Administratifs";
	elseif ( $groups[$loop]["cn"] == "Profs" ) $group_principal = "Profs";
	elseif ( $groups[$loop]["cn"] == "Eleves" ) $group_principal = "Eleves";
	
	elseif ( ereg ("Classe", $groups[$loop]["cn"] ) ) {
		$groups_secondaires[$i] = $groups[$loop]["cn"];
		$i++;
	}
	
	elseif ( ereg ("Equipe", $groups[$loop]["cn"] ) ) {
		$groups_secondaires[$i] = $groups[$loop]["cn"];
		$i++;
	}
}
			
// verification de l'existance d'une zone Profs ou Administratifs 
if ($group_principal!='' && $group_principal!='Eleve'){
	// Affichage si prof ou administratif
}
				
if($groups_secondaires!=''){
	$equips = array();
	$equipes = '<ul>';
	$classes = '<ul>';
	$maclasse = '<ul>';
	$equipes_select ='<ul class="classes_select">';
	foreach($groups_secondaires as $val_gs){

		if ( ereg ("Equipe", $val_gs)){
			$equips[] = $val_gs;
			$equipes .= "<li>".$val_gs."</li>"."\n\r";
			$equipes_select .= '<li class="'.$val_gs.'"><span class="select"></span>'.$val_gs.'</li>'."\n\r";
		}

		if ( ereg ("Classe", $val_gs)){
			$classes .= "<li>".$val_gs."</li>"."\n\r";
			$classe_el = $val_gs;
		}

	}
	$maclasse .= "<li>".$classe_el."</li>"."\n\r";						
	$equipes .= '<li></li></ul>'."\n\r";
	$classes .= '<li></li></ul>'."\n\r";
	$maclasse .= '<li></li></ul>'."\n\r";
	$equipes_select .='<li></li></ul>'."\n\r";
}

// Recuperer aussi la liste des eleves de la classe ?

// Les blocs fenetres, menu icone, et onglets
$html='';// le bloc qui va recevoir les fenetres de chaque appli activee
$html_menu='';// bloc menu deroulant
$html_menu_services='';// bloc menu deroulant
$html_icon='';// bloc icones du bureau
$html_status_bar='';// bloc barre d'etat
$left=20;
$top=20;
#############
$liste = array();
$liste['Liens'] = array();
$liste['Images'] = array();
$liste['Titres'] = array();
$liste['Blocks'] = array();
$liste['Ids'] = array();
#############

// Appli Annuaire
$liste['Images'][] = "images/bt-V2-3.png";
$liste['Liens'][] = "statandgo.php?use=Annu";
$liste['Titres'][] = "Annuaire des utilisateurs";
$liste['Blocks'][] = "#icon_dock_lcs_annu";
$liste['Ids'][] = "annu";

// spip
$liste['Images'][] = "images/barre1/BP_r1_c6_f3.gif";
$liste['Liens'][] = "statandgo.php?use=spip";
$liste['Titres'][] = "Forum";
$liste['Blocks'][] = "#icon_dock_lcs_spip";
$liste['Ids'][] = "spip";

// webmail
$liste['Images'][] = "images/barre1/BP_r1_c5_f3.gif";
$liste['Liens'][] = "statandgo.php?use=squirrelmail";
$liste['Titres'][] = "Webmail";
$liste['Blocks'][] = "#icon_dock_lcs_squirrelmail";
$liste['Ids'][] = "squirrelmail";

// Affichage des Menus users non privilégies

  // lecture lcs_applis 
  $query="SELECT  name, value from applis where type='M' order by name";
  $result=mysql_query($query);
  if ($result) {
        while ( $r=mysql_fetch_object($result) ) {
            if ( $r->name == "clientftp" ) $ftpclient = true;
            if ( $r->name == "pma" ) $pma = true;
            if ( $r->name == "smbwebclient" ) $smbwebclient = true;            
        }
    }
    mysql_free_result($result);

if ( $ftpclient ) {
  $liste['Images'][] = "images/bt-V1-2.jpg";
  $liste['Liens'][] = "../clientftp/";
  $liste['Titres'][] = "Client FTP";
  $liste['Blocks'][] = "#icon_dock_lcs_ftpclient";
  $liste['Ids'][] = "ftpclient";
}
if ( $pma ) {
  $liste['Images'][] = "images/bt-V1-3.jpg";
  $liste['Liens'][] = "statandgo.php?use=pma";
  $liste['Titres'][] = "Gestion base de donn&eacute;es";
  $liste['Blocks'][] = "#icon_dock_lcs_pma";
  $liste['Ids'][] = "pma";
}
if ( $se3netbios != "" && $se3domain != "" && $smbwebclient ) {
  $liste['Images'][] = "images/bt-V1-4.jpg";
  $liste['Liens'][] = "statandgo.php?use=smbwebclient";
  $liste['Titres'][] = "Client SE3";
  $liste['Blocks'][] = "#icon_dock_lcs_smbwc";
  $liste['Ids'][] = "smbwc";
}

$services=array("ftpclient", "pma", "smbwc", "annu", "squirrelmail", "maintinfo");
// Liens dynamiques vers les plugins installes 
$query="SELECT * from applis where type='P' OR type='N' order by name";
$result=mysql_query($query);
if ($result) {
        while ($r=mysql_fetch_object($result)) {
          if (( $r->value == "1" ) and ! ( file_exists("/usr/share/lcs/Plugins/".$r->chemin."/.applihide"))) {
            $liste['Images'][] = "../Plugins/".$r->chemin."/Images/plugin_icon.png";
            $liste['Liens'][] = "statandgo.php?use=".$r->name;
            $liste['Titres'][] = $r->descr;
            $liste['Blocks'][] = "#icon_dock_lcs_".strtolower($r->name);
			$liste['Ids'][] = strtolower($r->name);
            }
        }
}

mysql_free_result($result);
array_multisort($liste['Titres'],$liste['Liens'],$liste['Images'],$liste['Blocks'],$liste['Ids']);
for ($x=0;$x<count($liste['Titres']);$x++) {
	if($liste['Ids'][$x]=='spip'){
		$c='large_win';
		$c_sb='submenu';
   		$submenu = "<a class=\"ext_link ".$c_sb."\" href=\"#\" rel=\"\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> ".$liste['Titres'][$x]."</a>\n";
		$submenu   .="<ul>\n" ;
	    $submenu   .= "<li>\n";
    	$submenu   .= "<a class=\"open_win ext_link\" href=\"".$liste['Blocks'][$x]."\" rel=\"".$liste['Liens'][$x]."\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> ".$liste['Titres'][$x]."</a>\n";
	    $submenu   .= "</li>\n";
	    $submenu   .= "<li>\n";
    	$submenu   .= "<a class=\"open_win ext_link\" href=\"".$liste['Blocks'][$x]."\" rel=\"".htmlentities('../spip/ecrire/?exec=articles_edit&new=oui')."\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> &Eacute;crire un nouvel article</a>\n";
	    $submenu   .= "</li>\n";
		$submenu   .="</ul>\n" ;
	}else if($liste['Ids'][$x]=='squirrelmail'){
		$c='';
		$c_sb='submenu';
	    $submenu = "<a class=\"ext_link ".$c_sb."\" href=\"#\" rel=\"\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> ".$liste['Titres'][$x]."</a>\n";
		$submenu   .="<ul>\n" ;
		$submenu   .= "<li>\n";
	    $submenu   .= "<a class=\"open_win ext_link\" href=\"".$liste['Blocks'][$x]."\" rel=\"".$liste['Liens'][$x]."\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> Consulter vos messages</a>\n";
		$submenu   .= "</li>\n";
		$submenu   .= "<li>\n";
	    $submenu   .= "<a class=\"open_win ext_link\" href=\"".$liste['Blocks'][$x]."\" rel=\"../squirrelmail/src/compose.php?mailbox=INBOX&startMessage=1\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> Envoyer un message</a>\n";
		$submenu   .= "</li>\n";
		$submenu   .="</ul>\n" ;
	}else if($liste['Ids'][$x]=='annu'){
		$c='';
		$c_sb='submenu';
	    $submenu = "<a class=\"open_win ext_link ".$c_sb."\" href=\"\" rel=\"\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> ".$liste['Titres'][$x]."</a>\n";
		$submenu   .="<ul>\n" ;
	    $submenu   .= "<li>\n";
	  	$submenu   .= "<a class=\"open_win ext_link\" href=\"".$liste['Blocks'][$x]."\" rel=\"../Annu/search.php\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> Effectuer une recherche</a>\n";
		$submenu   .= "</li>\n";
		$submenu   .= "<li>\n";
	    $submenu   .= "<a class=\"open_win ext_link\" href=\"".$liste['Blocks'][$x]."\" rel=\"../Annu/me.php\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> Voir ma fiche</a>\n";
		$submenu   .= "</li>\n";
		$submenu   .= "<li>\n";
	    $submenu   .= "<a class=\"open_win ext_link\" href=\"".$liste['Blocks'][$x]."\" rel=\"../Annu/mod_entry.php\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> Modifier ma fiche</a>\n";
		$submenu   .= "</li>\n";
		$submenu   .= "<li>\n";
	    $submenu   .= "<a class=\"open_win ext_link\" href=\"".$liste['Blocks'][$x]."\" rel=\"../Annu/mod_pwd.php\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> Changer de mot de passe</a>\n";
		$submenu   .= "</li>\n";
		$submenu   .="</ul>\n" ;
	}else if($liste['Ids'][$x]=='maintinfo'){
		$c='large_win';
		$c_sb='submenu';
	    $submenu = "<a class=\"open_win ext_link ".$c_sb."\" href=\"\" rel=\"\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> ".$liste['Titres'][$x]."</a>\n";
		$submenu   .="<ul>\n" ;
	    $submenu   .= "<li>\n";
	  	$submenu   .= "<a class=\"open_win ext_link\" href=\"".$liste['Blocks'][$x]."\" rel=\"../Plugins/Maintenance/demande_support.php\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> Demande de support</a>\n";
		$submenu   .= "</li>\n";
		$submenu   .= "<li>\n";
	    $submenu   .= "<a class=\"open_win ext_link\" href=\"".$liste['Blocks'][$x]."\" rel=\"../Plugins/Maintenance/index.php?mnu_choice=wait\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> En attente</a>\n";
		$submenu   .= "</li>\n";
		$submenu   .= "<li>\n";
	    $submenu   .= "<a class=\"open_win ext_link\" href=\"".$liste['Blocks'][$x]."\" rel=\"../Plugins/Maintenance/index.php?mnu_choice=myspool\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> Votre encours</a>\n";
		$submenu   .= "</li>\n";
		$submenu   .= "<li>\n";
	    $submenu   .= "<a class=\"open_win ext_link\" href=\"".$liste['Blocks'][$x]."\" rel=\"../Plugins/Maintenance/index.php?mnu_choice=wait\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> Votre historique</a>\n";
		$submenu   .= "</li>\n";
		$submenu   .="</ul>\n" ;
	}else if(($liste['Ids'][$x]=='agendas') || ($liste['Ids'][$x]=='Cdt') || ($liste['Ids'][$x]=='claroline')){
		$c='large_win';
		$c_sb='';
		$submenu ="<a class=\"open_win ext_link ".$c_sb."\" href=\"".$liste['Blocks'][$x]."\" rel=\"".$liste['Liens'][$x]."\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> ".$liste['Titres'][$x]."</a>\n";
	}else{
		$c='';
		$c_sb='';
		$submenu ="<a class=\"open_win ext_link ".$c_sb."\" href=\"".$liste['Blocks'][$x]."\" rel=\"".$liste['Liens'][$x]."\" title=\"".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" alt=\"\" style=\"width:22px;height:22px;\" /> ".$liste['Titres'][$x]."</a>\n";
	}
    if(!in_array($liste['Ids'][$x], $services)){
	    $html_menu   .= "<li>\n";
	    $html_menu   .= $submenu;
		$html_menu .= "</li>\n";
    }else{
	    $html_menu_services   .= "<li>\n";
	    $html_menu_services   .= $submenu;
		$html_menu_services .= "</li>\n";
    }
    $html   .= "<div id=\"window_lcs_".$liste['Ids'][$x]."\" class=\"abs window ".$c."\">\n";
    $html   .= "<div class=\"abs window_inner\">\n";
    $html   .= "<div class=\"window_top\">\n";
    $html   .= "<span class=\"float_left\">\n";
    $html   .= "<img src=\"".$liste['Images'][$x]."\" style=\"width:16px;\" />".$liste['Titres'][$x]."\n";
    $html   .= "</span>\n";
    $html   .= "<span class=\"float_right\">\n";
    $html   .= "<a href=\"#\" class=\"window_min\"></a><a href=\"#\" class=\"window_resize\"></a>\n";
    $html   .= "<a href=\"".$liste['Blocks'][$x]."\" class=\"window_close\"></a>\n";
    $html   .= "</span>\n";
    $html   .= "</div>\n";
    $html   .= "<div class=\"abs window_content\">\n";
    $html   .= "<div class=\"window_main\" style=\"width:100%;height:100%;margin:0;\">\n";

	$html   .= "<a class=\"appli_link\" href=\"".$liste['Liens'][$x]."\" title=\"".$liste['Blocks'][$x]."\"></a>\n";
    
	$html   .= "<iframe src=\"\" name=\"ifr_lcs_".$liste['Ids'][$x]."\"width=\"100%\" height=\"98%\" style=\"width:100%;\"></iframe>\n";

    $html   .= "</div>\n";
    $html   .= "</div>\n";
    $html   .= "<div class=\"abs window_bottom\">".$liste['Titres'][$x]."</div>\n";
    $html   .= "</div>\n";
    $html   .= "<span class=\"abs ui-resizable-handle ui-resizable-se\"></span>\n";
    $html   .= "</div>\n";
    
	if($liste['Titres'][$x]=="Gestion Electronique de Documents") $liste['Titres'][$x]="Gestion de Documents";

    $html_status_bar .= "<li id=\"icon_dock_lcs_".$liste['Ids'][$x]."\"><a href=\"#window_lcs_".$liste['Ids'][$x]."\"><img src=\"".$liste['Images'][$x]."\" style=\"width:22px;\" />".$liste['Titres'][$x]."</a></li>\n";

    $html_icon_def   .= '<a class="abs icon ext_link" style="left:'.$left.'px;top:'.$top.'px;" href="'.$liste['Blocks'][$x].'" rel="'.$liste['Liens'][$x].'" title="'.$liste['Ids'][$x].'"><img src="'.$liste['Images'][$x].'"  style="width:32px;" />'.$liste['Titres'][$x].'</a>';
    
    $top=$top+80;
    if($x==5) { $left=$left+120;$top=20;}
    if($x==11) { $left=$left+120;$top=20;}
    if($x==16) { $left=$left+120;$top=20;}
}
						include("/var/www/lcs/desktop/action/load_user_prefs.php");
if(is_file("desktop/xml/".$login."/lcs_buro_".$login.".xml")){
							$html_icon= RSS_Display("desktop/xml/".$login."/lcs_buro_".$login.".xml",40,0,0);
} else{
	$html_icon= $html_icon_def;
}


// .:LCS:. function to scan dir of lists
function scanxml($dirxml){
	$htm_ret='';
	$tbl_ret = array();
	if(is_dir($dirxml) || is_readable($dirxml)){
		if($mydirxml = opendir($dirxml)){
			while($ent = readdir($mydirxml)){
				if(!is_dir($dirxml.'/'.$ent)){
					if(!preg_match("/buro/",$ent)){
						$text=preg_replace('/lcs_list_/', '', $ent);
						$htm_ret .="<li class='no_activ'><span class='to_select select'></span><a href='#' title=''>".preg_replace('/.xml/', '', $text)."</a><a href='#' class='float:right close'></a></li>\n";
						$tbl_ret[] = preg_replace('/.xml/', '', $text);
					}
				}
			}
			closedir($mydirxml);
			$ret=array($htm_ret, $tbl_ret);
		}
	}else{
	$ret ="Vous n'avez pas encore cr&eacute;&eacute; de liste";
	}
	return $ret;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>...::: Bureau LCS :::...</title>
<meta name="description" content="LCS environnement num&eacute;rique de travail" />
<link rel="stylesheet" href="desktop/stylesheets/html.css" />
<link href="desktop/stylesheets/inettuts.css" rel="stylesheet" type="text/css" />
<link href="desktop/stylesheets/inettuts.js.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="desktop/stylesheets/desktop.css" />
<link rel="shortcut-icon" href="images/favicon.ico">
<!--[if gte IE 7]>
<link rel="stylesheet" href="desktop/stylesheets/ie.css" />
<![endif]-->
</head>
<body>
<div class="abs" id="monLcs">
<iframe src="" name="ifr_lcs_monlcs" style="width:100%;height:100%;"></iframe>
</div>
<div class="abs" id="inettuts">
</div>
    
    
</div>
<div class="abs" id="desktop">
<?php
if ( $idpers==0 ) { 
	echo '<a class="abs icon" style="left:20px;top:20px;" href="#icon_dock_lcs_auth" title="Se connecter" rel="auth.php" href="#icon_dock_lcs_auth"><img src="images/barre1/BP_r1_c3_f3.gif" />Connexion</a>';
}else{
	echo $html_icon;
?>

	<div id="trash" class='trash'><h3 class="trash_item"></h3>Corbeille</div>
<!--	
	<div id="annonce" class="box_trsp_black">
		<h3 style="font-wheight:bold;text-shadow:0px 1px 0px #dddddd;">Annonce</h3>
		<p style="padding:10px;margin:10px;line-height:14px;">Vous n'avez pas encore enregistr&eacute; vos pr&eacute;f&eacute;rences de bureau</p>
		<p class="center bg_white" style=""><span class="open_win bouton"><a class="open_win bouton" href="#icon_dock_lcs_prefs">Modifier mon bureau...</a></span></p>
	</div>
-->	
<?php
}
?>
	<div id="window_lcs_auth" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="desktop/images/icons/icon_16_lcs.png" />
					LCS - Formulaire de connexion
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_auth" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
				<div class="window_main" style="width:100%;height:100%;margin:0;">
					<a class="appli_link" href="auth.php" title="auth"></a>
					<iframe src="" name="ifr_lcs_auth" style="width:100%;height:98%;height:98%;"></iframe>
				</div>
			</div>
			<div class="abs window_bottom">
				LCS -  Formulaire de connexion
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>
	
	<div id="window_lcs_admin" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="images/barre1/BP_r1_c7_f3.gif" style="width:16px;" />
					LCS - Administration
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_admin" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
				<div class="window_main" style="width:100%;height:100%;margin:0;">
					<a class="appli_link" href="../Admin" title="Admin"></a>
					<iframe src="" name="ifr_lcs_admin" style="width:100%;height:98%;"></iframe>
				</div>
			</div>
			<div class="abs window_bottom">
				LCS -Administration
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>

	<div id="window_lcs_helpdesk" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="images/barre1/BP_r1_c7_f3.gif" style="width:16px;" />
					LCS - Helpdesk
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_helpdesk" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
				<div class="window_main" style="width:100%;height:100%;margin:0;">
					<a class="appli_link" href="/helpdesk/" title="helpdesk"></a>
					<iframe src="" name="ifr_lcs_Helpdesk" style="width:100%;height:98%;"></iframe>
				</div>
			</div>
			<div class="abs window_bottom">
				LCS - Helpdesk
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>

	<div id="window_lcs_prefs" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="images/barre1/BP_r1_c7_f3.gif" style="width:16px;" />
					LCS - Pr&eacute;f&eacute;rences
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_prefs" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
			<?php
			include("desktop/includes/inc-form_prefs.php");
			?>
			<br style="clear:both;" />
			</div>
			<div class="abs window_bottom">
				LCS -Pr&eacute;f&eacute;rences
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>

	<div id="window_lcs_legal" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="images/barre1/BP_r1_c7_f3.gif" style="width:16px;" />
					LCS - A propos
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_legal" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
				<div style="width:100%; height:100%;">
				 Nous remercions : nos chiens, les mouettes (rieuses), Robert et Simone, 
				</div>

			<br style="clear:both;" />
			</div>
			<div class="abs window_bottom">
				LCS - A propos
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>

	<div id="window_lcs_texteditor" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="images/barre1/BP_r1_c7_f3.gif" style="width:16px;" />
					LCS - Editeur
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_testeditor" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
			<iframe src="desktop/javascripts/markitup/textarea.html" style="width:100%;height:98%;"></iframe>

			</div>
			<div class="abs window_bottom">
				LCS - Editeur
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>
	
<!-- LCS window  -->
	<div id="window_lcs_path" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="images/barre1/BP_r1_c7_f3.gif" style="width:16px;" />
					<span class="window_title">LCS - </span>
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_path" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
			<iframe src="" style="width:100%;height:98%;"></iframe>
			</div>
			<div class="abs window_bottom">
				LCS - 
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>
<!--End of window -->
	
<!-- LCS window  -->
	<div id="window_lcs_temp" class="abs window">
		<div class="abs window_inner">
			<div class="window_top">
				<span class="float_left">
					<img src="images/barre1/BP_r1_c7_f3.gif" style="width:16px;" />
					<span class="window_title">LCS - test lien ext</span>
				</span>
				<span class="float_right">
					<a href="#" class="window_min"></a>
					<a href="#" class="window_resize"></a>
					<a href="#icon_dock_lcs_temp" class="window_close"></a>
				</span>
			</div>
			<div class="abs window_content">
			<iframe src="" style="width:100%;height:98%;"></iframe>
			</div>
			<div class="abs window_bottom">
				LCS -  test lien ext
			</div>
		</div>
		<span class="abs ui-resizable-handle ui-resizable-se"></span>
	</div>
<!--End of window -->

	<div id="rightside" class="window abs">
		<div class="window_top"> 
			<span class="float_left window_slide_right" id="rightside_close">
				<a href="#" class=" slideon"></a>
			</span>
			<span class="float_left">
				<img src="images/barre1/BP_r1_c7_f3.gif" style="width:16px;" />
				LCS - Parcours
			</span>
		</div>
		<div class="window_content">
			<div class="list_links">
				<h3> Mes liens</h3>
			</div>
			<?php
				$tbl_list= scanxml('/var/www/lcs/desktop/xml/'.$login);
				echo "<div class='personal_list'> \n";
				echo "<h4>Mes listes perso</h4> \n";
				echo "<ul class='my_personal_lists ul_open_win'>";
				if(is_array($tbl_list[1])){
				foreach($tbl_list[1] as $val){
					echo "<li>". $val ."</li> \n";
				}
				}else{
					echo "<li class='no_click'>Aune liste enregistr&eacute;e</li> \n";
				}
				echo "</ul>";
				echo "</div> \n";
				if($group_principal=='Eleves'){
					$tbl_list_cl= scanxml('/var/www/lcs/desktop/xml/'.preg_replace('/Classe_/', '',$classe_el));
					echo "<div class='classes_list'> \n";
					echo "<h4>Les listes de ma classe</h4> \n";
					echo "<ul class='my_classe_lists'> \n";
				if(is_array($tbl_list[1])){
					foreach($tbl_list_cl[1] as $val){
						echo "<li>". $val ."</li> \n";
					}
				}else{
					echo "<li class='no_click'>Aune liste enregistr&eacute;e</li> \n";
				}
					echo "</ul> \n";
					echo "</div> \n";
				}
			?>
<!--			<div id="trash_list" class='trash'><h3 class="trash_item"></h3>Corbeille de liste</div> -->
		</div>
	</div>
<?php

	if ( $idpers!=0 ) { 
		echo $html;
	}
?>
	<div id="dialog_elt" class="" title="Ajouter ">
	</div>
</div>

<?php
include('desktop/includes/inc-window_applis.php');
?>
<!--	<a class="float_right" id="test_dialog" href="#" title="test" style="font-style:italic;text-shadow:2px 2px 2px #aaaaaa;">dialog?</a> -->
	<a class="float_right" href="#" title="LcsDevTeam" style="font-style:italic;text-shadow:2px 2px 2px #aaaaaa;">Lcs-Team
	<!--	<img src="desktop/images/misc/firehost.png" /> -->
	</a>
<div id="bar_bttm_icon" style="width:300px;float:right;"></div>
</div>
<script src="../libjs/jquery/jquery.js"></script>
<script src="desktop/javascripts/jquery.desktop.js"></script>
<script src="../libjs/jquery-ui/jquery-ui.js"></script>
<script src="desktop/javascripts/inettuts.js"></script><!-- - inettuts - -->
<!--script src="desktop/javascripts/cookie.jquery.js"></script>
<script src="desktop/javascripts/jquery.loader.js"></script-->

<?php
if ( $idpers==0 ) { 
?>

<script>

	// .:LCS:. on lance l'affichage du form de connexion.
	setTimeout(function(){
		$('#window_lcs_auth').addClass('window_stack').fadeIn('5000').css('min-height', '350px').animate({'opacity' : '0.9'}).find('iframe').attr('src',"auth.php");
		// Show the taskbar button.
		if ($('#icon_dock_lcs_auth').is(':hidden')) {
			$('#icon_dock_lcs_auth').remove().appendTo('#dock').end().show('slow');
		}
	},1500);
</script>
<?php
}else{
?>
<script>
	JQD.jqd_load_xml('admin');
	JQD.list_links_load_xml("perso_"+$("#login").val(), $("#login").val());
</script>
<?php
}
?>
<script>
//$(window).load(function() {});
	JQD.init_icons();
	JQD.init_desktop();
	JQD.initDrop();
</script>

<?php
// Cas service authentification
   if ( $login && ($lcs_cas == 1) && !isset($_COOKIE['tgt'])) 
	echo "<script type='text/javascript'>
        // <![CDATA[
		$.ajax({
                    type: 'POST',
                    url : 'includes/log2cas_ajax.php',
                    async: true,
                    error: function() {
                        alert('Echec authentification CAS');
                    }
         });
        //]]>
        </script>\n";
?>

</body>
</html>

