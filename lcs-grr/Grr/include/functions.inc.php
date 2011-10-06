<?php
/**
 * include/functions.inc.php
 * fichier Biblioth�que de fonctions de GRR
 * Derni�re modification : $Date: 2010-04-07 15:38:14 $
 * @author		Laurent Delineau <laurent.delineau@ac-poitiers.fr>
 * @author		Marc-Henri PAMISEUX <marcori@users.sourceforge.net>
 * @copyright	Copyright 2003-2005 Laurent Delineau
 * @copyright	Copyright 2008 Marc-Henri PAMISEUX
 * @link		http://www.gnu.org/licenses/licenses.html
 * @package		include
 * @version		$Id: functions.inc.php,v 1.33 2010-04-07 15:38:14 grr Exp $
 * @filesource
 *
 * This file is part of GRR.
 *
 * GRR is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GRR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GRR; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * $Log: functions.inc.php,v $
 * Revision 1.33  2010-04-07 15:38:14  grr
 * *** empty log message ***
 *
 * Revision 1.32  2010-03-03 14:41:57  grr
 * *** empty log message ***
 *
 * Revision 1.31  2010-01-06 10:21:19  grr
 * *** empty log message ***
 *
 * Revision 1.30  2009-12-16 14:52:31  grr
 * *** empty log message ***
 *
 * Revision 1.29  2009-12-02 21:37:20  grr
 * *** empty log message ***
 *
 * Revision 1.28  2009-12-02 20:11:08  grr
 * *** empty log message ***
 *
 * Revision 1.27  2009-10-09 07:55:48  grr
 * *** empty log message ***
 *
 * Revision 1.26  2009-09-29 18:02:57  grr
 * *** empty log message ***
 *
 * Revision 1.25  2009-06-04 20:52:24  grr
 * *** empty log message ***
 *
 * Revision 1.24  2009-06-04 15:30:17  grr
 * *** empty log message ***
 *
 * Revision 1.23  2009-04-14 12:59:17  grr
 * *** empty log message ***
 *
 * Revision 1.22  2009-04-10 21:05:45  grr
 * *** empty log message ***
 *
 * Revision 1.21  2009-04-09 14:52:31  grr
 * *** empty log message ***
 *
 * Revision 1.20  2009-03-24 14:03:10  grr
 * *** empty log message ***
 *
 * Revision 1.19  2009-03-24 13:30:07  grr
 * *** empty log message ***
 *
 * Revision 1.18  2009-02-27 22:05:03  grr
 * *** empty log message ***
 *
 * Revision 1.17  2009-01-28 16:01:31  grr
 * *** empty log message ***
 *
 * Revision 1.16  2009-01-20 07:19:17  grr
 * *** empty log message ***
 *
 * Revision 1.15  2008-11-16 22:00:59  grr
 * *** empty log message ***
 *
 * Revision 1.14  2008-11-14 07:29:09  grr
 * *** empty log message ***
 *
 * Revision 1.13  2008-11-13 21:32:51  grr
 * *** empty log message ***
 *
 * Revision 1.12  2008-11-11 22:01:15  grr
 * *** empty log message ***
 *
 * Revision 1.11  2008-11-10 08:17:34  grr
 * *** empty log message ***
 *
 * Revision 1.10  2008-11-10 07:06:39  grr
 * *** empty log message ***
 *
 * Revision 1.9  2008-11-07 21:39:41  grr
 * *** empty log message ***
 *
 *
 */

/*
Affiche un lien email
$option_affichage
-----------------
Si $option_affichage="afficher_toujours", en l'absence de mail, on affiche quand m�me l'intitul�
Sinon, en l'absence de mail, on affiche rien.

$_type_cible
------------
si "identifiant:non" -> $_cible n'est pas l'identifiant d'un utilisateur de la base
si "identifiant:oui" -> $_cible est l'identifiant d'un utilisateur de la base
*/


function get_request_uri()
{
  global $grr_script_name;
  $RequestUri = "";
  if (isset($_SERVER['REQUEST_URI']))
    $RequestUri = $_SERVER['REQUEST_URI'];
  else if (isset($_ENV['REQUEST_URI']))
    $RequestUri = $_ENV['REQUEST_URI'];
  else if (isset($_SERVER['HTTP_X_REWRITE_URL']))
    $RequestUri = $_SERVER['HTTP_X_REWRITE_URL'];
  else {
    if (!isset($_SERVER['QUERY_STRING'])) $_SERVER['QUERY_STRING']="";
    // Dans certaines configuration (reverse proxy, ...) les variables $_SERVER["SCRIPT_NAME"] ou $_SERVER['PHP_SELF']
    // sont mal interpr�t�es entra�nant des liens erron�s sur certaines pages.
    if ((getSettingValue("use_grr_url")=="y") and (getSettingValue("grr_url")!="")) {
        if (substr(getSettingValue("grr_url"), -1) != "/") $ad_signe = "/"; else $ad_signe = "";
        $RequestUri = getSettingValue("grr_url").$ad_signe.$grr_script_name.$_SERVER['QUERY_STRING'];
    } else {
      if (isset($_SERVER['PHP_SELF']))
        $RequestUri = $_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING'];
    }
  }
  return $RequestUri;
}

/*
Affiche un lien email
$option_affichage
-----------------
Si $option_affichage="afficher_toujours", en l'absence de mail, on affiche quand m�me l'intitul�
Sinon, en l'absence de mail, on affiche rien.

$_type_cible
------------
si "identifiant:non" -> $_cible n'est pas l'identifiant d'un utilisateur de la base
si "identifiant:oui" -> $_cible est l'identifiant d'un utilisateur de la base
*/
function affiche_lien_contact($_cible,$_type_cible,$option_affichage) {
    /*
    On commence par d�terminer l'adresse email et l'identit� de la personne
    */
    if ($_type_cible=="identifiant:non") {
      if ($_cible=="contact_administrateur") {
        $_email = getSettingValue("webmaster_email");
        $_identite = get_vocab('administrator_contact');
      } else if ($_cible=="contact_support") {
        $_email = getSettingValue("technical_support_email");
        $_identite = get_vocab('technical_contact');
      } else {
        $_email = "";
        $_identite = "";
      }
    } else {
        $sql_cible = "SELECT prenom, nom, email FROM ".TABLE_PREFIX."_utilisateurs WHERE login = '".$_cible."'";
        $res_cible = grr_sql_query($sql_cible);
        if ($res_cible) {
            $row_cible = grr_sql_row($res_cible, 0);
            $_email = $row_cible[2];
            $_identite = $row_cible[0]." ".$row_cible[1];
            grr_sql_free($res_cible);
        } else {
            $_email = "";
            $_identite = "";
        }
    }

  if (getSettingValue("envoyer_email_avec_formulaire")=="yes") {
  /*
   On affiche un lien qui renvoie vers un formulaire.
  */
      if ($_email == "") {
        if ($option_affichage=="afficher_toujours")
            $affichage = $_identite;
        else
            $affichage = "";
      } else {
          $affichage = "<a href='javascript:centrerpopup(\"contact.php?cible=$_cible&amp;type_cible=$_type_cible\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")' title=\"".$_identite."\">".$_identite."</a>";
      }
  } else {
  /*
   On affiche un lien avec l'adresse email
   */
    ?>
    <script type="text/javascript">
    function encode_adresse(user,domain,debut) {
        var address = user+'@'+domain;
        var toWrite = '';
        if (debut > 0) {toWrite += '<'+'a href="mailto:';} else {toWrite +=';'};
        toWrite +=address
        document.write(toWrite);
    }
    function encode_fin_adresse(label) {
        var toWrite = '';
        toWrite +='">'+label+'</'+'a>';
        document.write(toWrite);
    }
    </script>
    <?php
    $affichage = "";
    if ($_email == "") {
        if ($option_affichage=="afficher_toujours")
            $affichage = $_identite;
    } else {
        $tab_email = explode(';',trim($_email));
        $i=0;
        foreach($tab_email as $item_email){
            $item_email_explode = explode('@',$item_email);
            $person = $item_email_explode[0];
            if (isset($item_email_explode[1])) {
                $i++;
                $domain = $item_email_explode[1];
                if ($i==1) { // Premi�re adresse
                    $affichage .= "<script type=\"text/javascript\">";
                    $affichage .=  "encode_adresse('".$person."','".$domain."',1);";
                } else {
                    $affichage .=  "encode_adresse('".$person."','".$domain."',0);";
                }
            }
        }
        $affichage .=  "encode_fin_adresse('".AddSlashes($_identite)."');";
        $affichage .=  "</script>";
    }
  }
  return $affichage;
}
/*
Fonction qui calcule $room, $area et $id_site � partir de $_GET['room'], $_GET['area'], $_GET['id_site']
*/
function Definition_ressource_domaine_site() {
  global $room, $area, $id_site;
  if (isset($_GET['room'])) {
  $room = $_GET['room'];
  settype($room,"integer");
  // Todo : s'agit-il d'une ressource valide ?
  // la ressource est d�finie, on peut en d�duire le domaine et le site
  $area=mrbsGetRoomArea($room);
  $id_site=mrbsGetAreaSite($area);
} else {
  $room=NULL;
  // la ressource n'est pas d�finie
  if (isset($_GET['area'])) {
    $area = $_GET['area'];
    settype($area,"integer");
    // Le domaine est d�finie, on peut en d�duire le site
    // Todo : s'agit-il d'un domaine valide ?
    $id_site=mrbsGetAreaSite($area);
  } else {
     $area=NULL;
     // le domaine n'est pas d�finie
     if (isset($_GET["id_site"])) {
         $id_site = $_GET["id_site"];
         settype($id_site,"integer");
         $area = get_default_area($id_site);
     } else {
         $id_site = get_default_site();
         $area = get_default_area($id_site);
     }
  }
}

}



/*
function affiche_ressource_empruntee
- $id_room : identifiant de la ressource
- Si la ressource est emprunt�e, affiche une ic�ne avec un lien vers la r�servation pour laquelle la ressource est emprunt�e.
*/
function affiche_ressource_empruntee($id_room, $type="logo"){
    $active_ressource_empruntee = grr_sql_query1("select active_ressource_empruntee from ".TABLE_PREFIX."_room where id = '".$id_room."'");
    if ($active_ressource_empruntee == 'y') {
        $id_resa = grr_sql_query1("select id from ".TABLE_PREFIX."_entry where room_id = '".$id_room."' and statut_entry='y'");
        if ($id_resa !=-1) {
            if ($type=="logo")
                echo "<a href='view_entry.php?id=$id_resa'><img src=\"img_grr/buzy_big.png\"  alt=\"".get_vocab("ressource actuellement empruntee")."\" title=\"".get_vocab("reservation_en_cours")."\" width=\"30\" height=\"30\" class=\"image\"  /></a>";
            else if ($type=="texte") {
                $beneficiaire = grr_sql_query1("select beneficiaire from ".TABLE_PREFIX."_entry where room_id = '".$id_room."' and statut_entry='y'");
                $beneficiaire_ext = grr_sql_query1("select beneficiaire_ext from ".TABLE_PREFIX."_entry where room_id = '".$id_room."' and statut_entry='y'");
                echo "<br /><b><span class='avertissement'><img src=\"img_grr/buzy_big.png\" alt=\"".get_vocab("ressource actuellement empruntee")."\" title=\"".get_vocab("ressource actuellement empruntee")."\" width=\"30\" height=\"30\" class=\"image\"  />
                &nbsp;".get_vocab("ressource actuellement empruntee")." ".get_vocab("nom emprunteur").get_vocab("deux_points").affiche_nom_prenom_email($beneficiaire,$beneficiaire_ext,"withmail").
                ". <a href='view_entry?id=$id_resa'>".get_vocab("entryid").$id_resa.
                "</a></span></b>";
            } else
               return "yes";
        }
    }
}

function bbCode($t,$type)
// remplace les balises BBCode par des balises HTML
{
 // on nettoie le bbcode
 if ($type=="nobbcode") {
   // barre horizontale
   $t=str_replace("[/]", "", $t);
   $t=str_replace("[hr]", "", $t);

   // alignement centr�
   $t=str_replace("[center]", "", $t);
   $t=str_replace("[/center]", "", $t);

   // alignement � droite
   $t=str_replace("[right]", "", $t);
   $t=str_replace("[/right]", "", $t);

   // alignement justifi�
   $t=str_replace("[justify]", "", $t);
   $t=str_replace("[/justify]", "", $t);

   // lien
   $regLienSimple="`\[url\] ?([^\[]*) ?\[/url\]`";
   $regLienEtendu="`\[url ?=([^\[]*) ?] ?([^]]*) ?\[/url\]`";
   if (preg_match($regLienSimple, $t)) $t=preg_replace($regLienSimple, "\\1", $t);
   else $t=preg_replace($regLienEtendu, "\\1", $t);

   // mail
   $regMailSimple="`\[email\] ?([^\[]*) ?\[/email\]`";
   $regMailEtendu="`\[email ?=([^\[]*) ?] ?([^]]*) ?\[/email\]`";
   if (preg_match($regMailSimple, $t)) $t=preg_replace($regMailSimple, "\\1", $t);
   else $t=preg_replace($regMailEtendu, "\\1", $t);

   // image
   $regImage="`\[img\] ?([^\[]*) ?\[/img\]`";
   $regImageAlternatif="`\[img ?= ?([^\[]*) ?\]`";
   if (preg_match($regImage, $t)) $t=preg_replace($regImage, "", $t);
   else $t=preg_replace($regImageAlternatif, "", $t);


   // gras
   $t=str_replace("[b]", "", $t);
   $t=str_replace("[/b]", "", $t);

   // italique
   $t=str_replace("[i]", "", $t);
   $t=str_replace("[/i]", "", $t);

   // soulignement
   $t=str_replace("[u]", "", $t);
   $t=str_replace("[/u]", "", $t);

   // couleur
   $t=str_replace("[/color]", "</span>", $t);
   $regCouleur="`\[color= ?(([[:alpha:]]+)|(#[[:digit:][:alpha:]]{6})) ?\]`";
   $t=preg_replace($regCouleur, "", $t);

   // taille des caract�res
   $t=str_replace("[/size]", "</span>", $t);
   $regCouleur="`\[size= ?([[:digit:]]+) ?\]`";
   $t=preg_replace($regCouleur, "", $t);
 }
 // Dans le titre des r�servations
 if ($type!="titre") {
   // barre horizontale
   $t=str_replace("[/]", "<hr width=\"100%\" size=\"1\" />", $t);
   $t=str_replace("[hr]", "<hr width=\"100%\" size=\"1\" />", $t);

   // alignement centr�
   $t=str_replace("[center]", "<div style=\"text-align: center\">", $t);
   $t=str_replace("[/center]", "</div>", $t);

   // alignement � droite
   $t=str_replace("[right]", "<div style=\"text-align: right\">", $t);
   $t=str_replace("[/right]", "</div>", $t);

   // alignement justifi�
   $t=str_replace("[justify]", "<div style=\"text-align: justify\">", $t);
   $t=str_replace("[/justify]", "</div>", $t);

   // lien
   $regLienSimple="`\[url\] ?([^\[]*) ?\[/url\]`";
   $regLienEtendu="`\[url ?=([^\[]*) ?] ?([^]]*) ?\[/url\]`";
   if (preg_match($regLienSimple, $t)) $t=preg_replace($regLienSimple, "<a href=\"\\1\">\\1</a>", $t);
   else $t=preg_replace($regLienEtendu, "<a href=\"\\1\" target=\"_blank\">\\2</a>", $t);
 }
   // mail
   $regMailSimple="`\[email\] ?([^\[]*) ?\[/email\]\`";
   $regMailEtendu="`\[email ?=([^\[]*) ?] ?([^]]*) ?\[/email\]`";
   if (preg_match("'".$regMailSimple."'", $t)) $t=preg_replace($regMailSimple, "<a href=\"mailto:\\1\">\\1</a>", $t);
   else $t=preg_replace($regMailEtendu, "<a href=\"mailto:\\1\">\\2</a>", $t);

   // image
   $regImage="`\[img\] ?([^\[]*) ?\[/img\]`";
   $regImageAlternatif="`\[img ?= ?([^\[]*) ?\]`";
   if (preg_match($regImage, $t)) $t=preg_replace($regImage, "<img src=\"\\1\" alt=\"\" class=\"image\" />", $t);
   else $t=preg_replace($regImageAlternatif, "<img src=\"\\1\" alt=\"\" class=\"image\" />", $t);


   // gras
   $t=str_replace("[b]", "<strong>", $t);
   $t=str_replace("[/b]", "</strong>", $t);

   // italique
   $t=str_replace("[i]", "<em>", $t);
   $t=str_replace("[/i]", "</em>", $t);

   // soulignement
   $t=str_replace("[u]", "<u>", $t);
   $t=str_replace("[/u]", "</u>", $t);

   // couleur
   $t=str_replace("[/color]", "</span>", $t);
   $regCouleur="/\[color= ?(([[:alpha:]]+)|(#[[:digit:][:alpha:]]{6})) ?\]/";
   $t=preg_replace($regCouleur, "<span style=\"color: \\1\">", $t);

   // taille des caract�res
   $t=str_replace("[/size]", "</span>", $t);
   $regCouleur="`\[size= ?([[:digit:]]+) ?\]`";
   $t=preg_replace($regCouleur, "<span style=\"font-size: \\1px\">", $t);

   return $t;
}


/**
 * FUNCTION: how_many_connected()
 * DESCRIPTION: Si c'est un admin qui est connect�, affiche le nombre de personnes actuellement connect�es.
 */
function how_many_connected() {
    if(authGetUserLevel(getUserName(),-1) >= 6)
    {
      $sql = "SELECT LOGIN FROM ".TABLE_PREFIX."_log WHERE END > now()";
      $res = grr_sql_query($sql);
      $nb_connect = grr_sql_count($res);
      grr_sql_free($res);
      if ($nb_connect == 1)
        echo "<a href='admin_view_connexions.php'>".$nb_connect.get_vocab("one_connected")."</a>";
      else
        echo "<a href='admin_view_connexions.php'>".$nb_connect.get_vocab("several_connected")."</a>";
        // V�rification du num�ro de version
      if (verif_version())
         affiche_pop_up(get_vocab("maj_bdd_not_update").get_vocab("please_go_to_admin_maj.php"),"force");
    }
}

/*
Teste s'il reste ou non des plages libres sur une journ�e donn�e pour un domaine donn�.
Arguments :
$id_room : identifiant de la ressource
$month_week : mois
$day_week : jour
$year_week : ann�e
Renvoie vraie s'il reste des plages non r�serv�es sur la journ�e
Renvoie faux dans le cas contraire
*/
function plages_libre_semaine_ressource($id_room, $month_week, $day_week, $year_week) {
    global $morningstarts, $eveningends, $eveningends_minutes, $resolution, $enable_periods;
    $date_end = mktime($eveningends, $eveningends_minutes, 0, $month_week, $day_week, $year_week);
    $date_start = mktime($morningstarts,0,0,$month_week,$day_week,$year_week);
    $t = $date_start-1;
    $plage_libre = FALSE;
    $plage_libre = 0;
    while ($t < $date_end) {
        $t += $resolution;
        $test = grr_sql_query1("SELECT id FROM ".TABLE_PREFIX."_entry where
         room_id='".$id_room."' and
         start_time <= ".$t." AND
         end_time >= ".$t." ");
        if ($test == -1) {
            $plage_libre = TRUE;
            break;
        }
    }
    return $plage_libre ;
}


/*
Arguments :
$mot_clef : mot cl� r�f�renc� dans la base de donn�es de la documentation et permettant d'acc�der au bon article
$ancre : ancre � l'int�rieur de l'article (facultatif)

Renvoie une portion de code html correspondant � l'affichage d'une image indiquant la pr�sence d'une aide
Un lien sur l'image renvoie sur un article de la documentation sur le site http://grr.mutualibre.org/documentation
*/
function grr_help($mot_clef,$ancre="") {
    // lien aide sur la page d'accueil
    if  ($mot_clef=="") {
       if (getSettingValue("gestion_lien_aide")=='perso') {
        $tag_help = "&nbsp;<a href='javascript:centrerpopup(\"";
        $tag_help .= getSettingValue("lien_aide");
        $tag_help .= "\",800,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>";
        $tag_help .= get_vocab("help")."</a>";
        //$tag_help = "&nbsp;<a href='".getSettingValue("lien_aide")."' target='_blank'>".get_vocab("help")."</a>";
       } else {
        $tag_help = "&nbsp;<a href='javascript:centrerpopup(\"";
        $tag_help .= "http://grr.mutualibre.org/documentation/index.php";
        $tag_help .= "\",800,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>";
        $tag_help .= get_vocab("help")."</a>";
        //$tag_help = "&nbsp;<a href='http://grr.mutualibre.org/documentation/index.php' target='_blank'>".get_vocab("help")."</a>";
       }
    } else {
        $tag_help = "&nbsp;<a href='javascript:centrerpopup(\"";
        $tag_help .= "http://grr.mutualibre.org/documentation/index.php";
        $tag_help .= "?mot_clef=".$mot_clef;
            if ($ancre !="") $tag_help .="&amp;ancre=".$ancre;
        $tag_help .= "\",800,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>";
        $tag_help .= "<img src=\"img_grr/help.png\" alt=\"Help\" title=\"Help\" width=\"16\" height=\"16\" class=\"image\"  />";
        $tag_help .= "</a>";
    }
    return $tag_help;
}

/* Fonction sp�ciale SE3
 $grp : le nom du groupe
 $uid : l'uid de l'utilisateur
 Cette fonction retourne "oui" ou "non" selon que $uid appartient au groupe $grp, ou bien "faux" si l'interrogation du LDAP �choue
 Seuls les groupes de type "posixGroup" sont support�s (les groupes de type "groupOfNames" ne sont pas support�s).
*/
function se3_grp_members ($grp,$uid) {
    include "config_ldap.inc.php";
    $est_membre="non";
    // LDAP attributs
    $members_attr = array (
    "memberUid"   // Recherche des Membres du groupe
    );
    // Avec des GroupOfNames, ce ne serait pas �a.
    $ds=@ldap_connect($ldap_adresse,$ldap_port);
    if($ds){
        $r=@ldap_bind ( $ds ); // Bind anonyme
        if($r){
            // La requ�te est adapt�e � un serveur SE3...
            $result=@ldap_read($ds,"cn=$grp,ou=Groups,$ldap_base","cn=*",$members_attr);
            // Peut-�tre faudrait-il dans le $tab_grp_autorise mettre des chaines 'cn=$grp,ou=Groups'
            if($result){
                $info=@ldap_get_entries($ds,$result);
                if($info["count"]==1){
                    $init=0;
                    for($loop=0;$loop<$info[0]["memberuid"]["count"];$loop++){
                        if($info[0]["memberuid"][$loop]==$uid){
                            $est_membre="oui";
                        }
                    }
                }
                @ldap_free_result($result);
            }
        }
        else{
            return false;
        }
        @ldap_close($ds);
    }
    else{
        return false;
    }
    return $est_membre;
}

/*
Arguments :
$id_entry : identifiant de la r�servation
$login_moderateur : identifiant du mod�rateur
$motivation_moderation : texte facultatif

Ins�re dans la table ".TABLE_PREFIX."_entry_moderate les valeurs de ".TABLE_PREFIX."_entry dont l'identifiant est $id_entry
*/
function  grr_backup($id_entry,$login_moderateur,$motivation_moderation) {
    $sql = "SELECT * FROM ".TABLE_PREFIX."_entry WHERE id='".$id_entry."'";
    $res = grr_sql_query($sql);
    if (! $res)  return FALSE;
    $row = grr_sql_row_keyed($res, 0);
    grr_sql_free($res);

    $req = "insert into ".TABLE_PREFIX."_entry_moderate set
    id = '".$row['id']."',
    start_time = '".$row['start_time']."',
    end_time  = '".$row['end_time']."',
    entry_type  = '".$row['entry_type']."',
    repeat_id  = '".$row['repeat_id']."',
    room_id = '".$row['room_id']."',
    timestamp = '".$row['timestamp']."',
    create_by = '".$row['create_by']."',
    beneficiaire = '".$row['beneficiaire']."',
    name = '".protect_data_sql($row['name'])."',
    type = '".$row['type']."',
    description = '".protect_data_sql($row['description'])."',
    statut_entry = '".$row['statut_entry']."',
    option_reservation = '".$row['option_reservation']."',
    overload_desc  = '".protect_data_sql($row['overload_desc'])."',
    moderate = '".$row['moderate']."',
    motivation_moderation = '".protect_data_sql(strip_tags($motivation_moderation))."',
    login_moderateur = '".protect_data_sql($login_moderateur)."'";

    $res = grr_sql_query($req);
    if (! $res)
        return FALSE;
    else {
        grr_sql_free($res);
        return TRUE;
    }
}
/*
Remplace la fonction PHP html_entity_decode(), pour les utilisateurs ayant des versions ant�rieures � PHP 4.3.0 :
En effet, la fonction html_entity_decode() est disponible a partir de la version 4.3.0 de php.
*/
function html_entity_decode_all_version ($string)
{
   global $use_function_html_entity_decode;
   if (isset($use_function_html_entity_decode) and ($use_function_html_entity_decode == 0)) {
       // Remplace les entit�s num�riques
       $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
       $string = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $string);
       // Remplace les entit�s lit�rales
       $trans_tbl = get_html_translation_table (HTML_ENTITIES);
       $trans_tbl = array_flip ($trans_tbl);
       return strtr ($string, $trans_tbl);
   } else
       return html_entity_decode($string);
}

function verif_version() {
    global $version_grr, $version_grr_RC;
    $_version_grr = $version_grr; // On pr�serve la variable $version_grr
    $_version_grr_RC = $version_grr_RC;// On pr�serve la variable $version_grr_RC
    $version_old = getSettingValue("version");
    $versionRC_old = getSettingValue("versionRC");
    // S'il s'agit de la version stable, on positionne malgr� tout $versionRC_old = 9 pour la coh�rence du test
    if ($versionRC_old == "") $versionRC_old = 9;
    // S'il s'agit de la version stable, on positionne malgr� tout $versionRC_old = 9 pour la coh�rence du test
    if ($_version_grr_RC == "") $_version_grr_RC = 9;
    if (
        ($version_old =='')
        or ($_version_grr > $version_old)
        or (($_version_grr == $version_old) and ($_version_grr_RC > $versionRC_old))
       )
        return true;
    else
        return false;
}

// Affiche le num�ro de version de GRR selon les cas sous la forme "GRR x.x.x_RCx" ou "GRR x.x.x
function affiche_version() {
    global $version_grr, $version_grr_RC, $sous_version_grr;
    if (getSettingValue("versionRC")!="")
        return "GRR ".getSettingValue("version")."_RC".getSettingValue("versionRC").$sous_version_grr;
    else
        return "GRR ".getSettingValue("version").$sous_version_grr;
}

/*


*/
function affiche_date($x) {
 $j   = date("d",$x);
$m = date("m",$x);
$a  = date("Y",$x);
$h  = date("H",$x);
$mi = date("i",$x);
$s = date("s",$x);
//$result = $h.":".$mi.":".$s.": le ".$j."/".$m."/".$a;
$result = $j."/".$m."/".$a;
return $result;
}


# L'heure d'�t� commence le dernier dimanche de mars * et se termine le dernier dimanche d'octobre
# Passage � l'heure d'hiver : -1h, le changement s'effectue � 3h
# Passage � l'heure d'�t� : +1h, le changement s'effectue � 2h
# Si type = hiver => La fonction retourne la date du jour de passage � l'heure d'hiver
# Si type = ete =>  La fonction retourne la date du jour de passage � l'heure d'�t�
function heure_ete_hiver($type, $annee, $heure)
 {
    if ($type == "ete")
       $debut = mktime($heure,0,0,03,31,$annee); // 31-03-$annee
    else
       $debut = mktime($heure,0,0,10,31,$annee); // 31-10-$annee

    while (date("D", $debut ) !='Sun')
    {
       $debut = mktime($heure,0,0,date("m",$debut), date("d",$debut)-1, date("Y",$debut)); //On retire 1 jour par rapport � la date examin�e
    }
    return $debut;
}

# Remove backslash-escape quoting if PHP is configured to do it with
# magic_quotes_gpc. Use this whenever you need the actual value of a GET/POST
# form parameter (which might have special characters) regardless of PHP's
# magic_quotes_gpc setting.
function unslashes($s)
{
//    if (get_magic_quotes_gpc())
    if (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc())
      return stripslashes($s);
    else
      return $s;
}

// Corrige les caracteres degoutants utilises par les Windozeries
function corriger_caracteres($texte) {
    // 145,146,180 = simple quote ; 147,148 = double quote ; 150,151 = tiret long
    $texte = strtr($texte, chr(145).chr(146).chr(180).chr(147).chr(148).chr(150).chr(151), "'''".'""--');
    return $texte;
}

// Traite les donn�es avant insertion dans une requ�te SQL
function protect_data_sql($_value) {
    global $use_function_mysql_real_escape_string;
//    if (get_magic_quotes_gpc())
    if (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc())
        $_value = stripslashes($_value);
    if (!is_numeric($_value)) {
        if (isset($use_function_mysql_real_escape_string) and ($use_function_mysql_real_escape_string==0))
             $_value = mysql_escape_string($_value);
        else
             $_value = mysql_real_escape_string($_value);
    }
    return $_value;
}

// Traite les donn�es envoy�es par la methode GET de la variable $_GET["page"]
function verif_page() {
    if (isset($_GET["page"]))
        if (($_GET["page"] == "day") or ($_GET["page"] == "week") or ($_GET["page"] == "month") or ($_GET["page"] == "week_all") or ($_GET["page"] == "month_all"))
            return $_GET["page"];
        else
            return "day";
    else
       return "day";
}

function page_accueil($param='no') {
   // Definition de $defaultroom
   if (isset($_SESSION['default_room']) and ($_SESSION['default_room'] >0)) {
      $defaultroom = $_SESSION['default_room'];
   } else {
      $defaultroom = getSettingValue("default_room");
   }

   // Definition de $defaultsite
   if (isset($_SESSION['default_site']) and ($_SESSION['default_site'] >0)) {
      $defaultsite = $_SESSION['default_site'];
   } else if (getSettingValue("default_site") > 0) {
      $defaultsite = getSettingValue("default_site");
   } else
      $defaultsite = get_default_site();

   // Definition de $defaultarea
   if (isset($_SESSION['default_area']) and ($_SESSION['default_area'] >0)) {
      $defaultarea = $_SESSION['default_area'];
   } else if (getSettingValue("default_area") > 0) {
      $defaultarea = getSettingValue("default_area");
   } else
      $defaultarea = get_default_area($defaultsite);

   // Calcul de $page_accueil
   if ($defaultarea == -1) {
      $page_accueil="day.php?noarea="; // le param�tre noarea ne sert � rien, il est juste l� pour �viter un cas particulier � traiter avec &amp;id_site= et $param
   } else if ($defaultroom == -1) {
      $page_accueil="day.php?area=$defaultarea";
   } else if ($defaultroom == -2) {
      $page_accueil="week_all.php?area=$defaultarea";
   } else if ($defaultroom == -3) {
      $page_accueil="month_all.php?area=$defaultarea";
   } else if ($defaultroom == -4) {
      $page_accueil="month_all2.php?area=$defaultarea";
   } else {
      $page_accueil="week.php?area=$defaultarea&amp;room=$defaultroom";
   }
   if ((getSettingValue("module_multisite") == "Oui") and ($defaultsite>0))
       $page_accueil .= "&amp;id_site=".$defaultsite;
   if ($param=='yes') $page_accueil .= "&amp;";
   return $page_accueil ;
}

function begin_page($title,$page="with_session")
{
if ($page=="with_session")
  {
    if (isset($_SESSION['default_style'])) $sheetcss = "themes/".$_SESSION['default_style']."/css/style.css";
    else $sheetcss="themes/default/css/style.css";

    if (isset($_GET['default_language']))
      {
        $_SESSION['default_language'] = $_GET['default_language'];
        if (isset($_SESSION['chemin_retour']) and ($_SESSION['chemin_retour'] != ''))
            header("Location: ".$_SESSION['chemin_retour']);
        else
            header("Location: ".traite_grr_url());
        die();
      }
  }
 else {
     if (getSettingValue("default_css")) $sheetcss = "themes/".getSettingValue("default_css")."/css/style.css";
     else $sheetcss="themes/default/css/style.css";
   }

 global $vocab, $charset_html, $unicode_encoding, $clock_file;
 /*
// Essai de prise en compte de l'environnement lemonldap
$page = "/grr/test.php";
$host = "127.0.0.1";  // ou bien www.tonsite.fr
$fp = pfsockopen($host, 80, $errno, $errstr);
if (!$fp) {
   echo "$errstr ($errno)<br/>\n";
   echo $fp;
   die();
} else {
   $header = "GET $page  HTTP/1.1\r\n";
   $header .=  "Host: $host\r\n";
   $header .= "Authorization: Basic ".base64_encode("delineau")."\r\n";
   $header .= "Connection: close\r\n\r\n";
   fwrite($fp, $header);
   $result = "";
   while (!feof($fp)) {
       $result .= fgets($fp, 128);
   }
   echo $result;
   fclose($fp);
}
//header("Authorization: Basic ".base64_encode('user:mot_de_passe')."=");
*/

 header("Content-Type: text/html;charset=". ((isset($unicode_encoding) and ($unicode_encoding==1)) ? "utf-8" : $charset_html)); header("Pragma: no-cache");                          // HTTP 1.0
 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
//$a='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
$a='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
$a.='<html>
<head>
<link rel="stylesheet" href="'.$sheetcss.'" type="text/css" />'."\n";
$a.='<link href="include/admin_grr.css" rel="stylesheet" type="text/css" />'."\n";
// Pour le format imprimable, on impose un fond de page blanc
if ((isset($_GET['pview'])) and ($_GET['pview'] == 1))
    $a .= '<link rel="stylesheet" href="themes/print/css/style.css" type="text/css" />'."\n";
/*
if (function_exists("getSettingValue")) {
    $a.='<LINK REL="SHORTCUT ICON" href="'.traite_grr_url().'favicon.ico" />';
}
*/
$a.='<style type="text/css">div#fixe   { position: fixed; bottom: 5%; right: 5%;}</style>'."\n";
$a.='<link rel="SHORTCUT ICON" href="./favicon.ico" />';
$a .="\n<title>$title</title>";
$a .="\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=";
if ($unicode_encoding)
    $a .= "utf-8";
else
    $a .= $charset_html;
$a .= "\" />";
$a .="\n<meta name=\"Robots\" content=\"noindex\" />";
$a .="\n</head>\n
<body>\n";
$a .='<script src="functions.js" type="text/javascript" ></script>';
if (@file_exists($clock_file)) {
   $a .='<script type="text/javascript" src="'.$clock_file.'"></script>';
}
# show a warning if this is using a low version of php
if (substr(phpversion(), 0, 1) == 3)  $a .=get_vocab('not_php3');
return $a;
}

function print_header($day='',$month='',$year='',$area='',$type_session='with_session',$page='no_admin',$room='')
{
   global $vocab, $search_str, $grrSettings, $clock_file, $desactive_VerifNomPrenomUser, $grr_script_name;
   global $use_prototype, $use_tooltip_js, $desactive_bandeau_sup, $id_site;

   if (!($desactive_VerifNomPrenomUser)) $desactive_VerifNomPrenomUser = 'n';
   // On v�rifie que les noms et pr�noms ne sont pas vides
   VerifNomPrenomUser($type_session);
   if ($type_session == "with_session")
       echo begin_page(get_vocab("mrbs").get_vocab("deux_points").getSettingValue("company"),"with_session");
   else
       echo begin_page(get_vocab("mrbs").get_vocab("deux_points").getSettingValue("company"),"no_session");

   // Si nous ne sommes pas dans un format imprimable
   if ((!isset($_GET['pview'])) or ($_GET['pview'] != 1)) {

   # If we dont know the right date then make it up
     if (!isset($day) or !isset($month) or !isset($year) or ($day == '') or ($month == '') or ($year == '')) {
         $date_now = mktime();
         if ($date_now < getSettingValue("begin_bookings"))
             $date_ = getSettingValue("begin_bookings");
         else if ($date_now > getSettingValue("end_bookings"))
             $date_ = getSettingValue("end_bookings");
         else
             $date_ = $date_now;
        $day   = date("d",$date_);
        $month = date("m",$date_);
        $year  = date("Y",$date_);
     }
   if (!(isset($search_str))) $search_str = get_vocab("search_for");
   if (empty($search_str)) $search_str = "";
   ?>
   <script type="text/javascript">
    chaine_recherche = "<?php echo $search_str; ?>";
   	function onsubmitForm()
	{
	if(document.pressed == 'a')
	{
  	document.getElementById('day').selectedIndex=<?php $date_now = mktime();echo (date("d",$date_now)-1); ?>;
		document.getElementById('month').selectedIndex=<?php echo (date("m",$date_now)-1);?>;
		document.getElementById('year').selectedIndex=<?php echo (date("Y",$date_now)-strftime("%Y", getSettingValue("begin_bookings")));?>;
  	var p=location.pathname;
	   	if(!p.match("day.php") && !p.match("week.php") && !p.match("week_all.php") && !p.match("month.php") && !p.match("month_all.php") && !p.match("month_all2.php") && !p.match("year.php"))
    document.getElementById('myform').action ="day.php";
	}
    if(document.pressed == 'd')
      document.getElementById('myform').action ="day.php";
    if(document.pressed == 'w')
    <?php
    echo "		document.getElementById('myform').action = \"";
    if ($room=="")
      echo "week_all.php";
		else
      echo "week.php";
    echo "\";\n";
    ?>
    if(document.pressed == 'm')
    <?php
    echo "		document.getElementById('myform').action = \"";
    if ($room=="") {
      if (isset($_SESSION['type_month_all'])) {echo $_SESSION['type_month_all'].".php";}
      else {echo "month_all.php";}
    } else
      echo "month.php";
    echo "\";\n";
    ?>
    return true;
		}
		</script>
    <?php

if (!(isset($desactive_bandeau_sup) and ($desactive_bandeau_sup==1) and ($type_session != 'with_session'))) {
    // On fabrique une date valide pour la r�servation si ce n'est pas le cas
    $date_ = mktime(0, 0, 0, $month, $day, $year);
    if ($date_ < getSettingValue("begin_bookings"))
        $date_ = getSettingValue("begin_bookings");
    else if ($date_ > getSettingValue("end_bookings"))
        $date_ = getSettingValue("end_bookings");
    $day   = date("d",$date_);
    $month = date("m",$date_);
    $year  = date("Y",$date_);
?>

   <table width="100%" border="0">
    <tr>
      <td class="border_banner">
       <table width="100%" border="0">
        <tr>
        <?php
        $nom_picture = "./images/".getSettingValue("logo");
        if ((getSettingValue("logo")!='') and (@file_exists($nom_picture)))
         echo "<td class=\"banner\"><img src=\"".$nom_picture."\" class=\"image\" alt=\"logo\" /></td>\n";
         echo "<td class=\"banner\">\n";
          echo "&nbsp;<a href=\"".page_accueil('yes')."day=$day&amp;year=$year&amp;month=$month\">".get_vocab("welcome")."</a>";
          echo " - <b>".getSettingValue("company")."</b>";
          if ($type_session == 'no_session') {
            if ((getSettingValue('sso_statut') == 'cas_visiteur') or (getSettingValue('sso_statut') == 'cas_utilisateur'))
					  {
					    echo "<br />&nbsp;<a href='index.php?force_authentification=y'>".get_vocab("authentification")."</a>";
//					    echo "<br />&nbsp;<small><i><a href='login.php?url=".rawurlencode(str_replace('&amp;','&',get_request_uri()))."'>".get_vocab("connect_local")."</a></i></small>";
// corrige un bug dans le calcul de la page d'accueil apr�s connexion.
					    echo "<br />&nbsp;<small><i><a href='login.php'>".get_vocab("connect_local")."</a></i></small>";
					  }
				    else
					  {
// echo "<br />&nbsp;<a href='login.php?url=".rawurlencode(str_replace('&amp;','&',get_request_uri()))."'>".get_vocab("connect")."</a>";
// corrige un bug dans le calcul de la page d'accueil apr�s connexion.
					    echo "<br />&nbsp;<a href='login.php'>".get_vocab("connect")."</a>";
            }
          } else {
            echo "<br />&nbsp;<b>".get_vocab("welcome_to").htmlspecialchars($_SESSION['prenom'])." ".htmlspecialchars($_SESSION['nom'])."</b>";           
            echo "<br />&nbsp;<a href=\"my_account.php?day=".$day."&amp;year=".$year."&amp;month=".$month."\">".get_vocab("manage_my_account")."</a>";
            //if ($type_session == "with_session") {
            $parametres_url = '';
                 $_SESSION['chemin_retour'] = '';
                 if (isset($_SERVER['QUERY_STRING']) and ($_SERVER['QUERY_STRING'] != '')) {
                     // Il y a des param�tres � passer
                     $parametres_url = htmlspecialchars($_SERVER['QUERY_STRING'])."&amp;";
                     $_SESSION['chemin_retour'] = traite_grr_url($grr_script_name)."?". $_SERVER['QUERY_STRING'];
                 }
                 echo " - <a href=\"".traite_grr_url($grr_script_name)."?".$parametres_url."default_language=fr\"><img src=\"img_grr/fr_dp.png\" alt=\"France\" title=\"france\" width=\"20\" height=\"13\" class=\"image\" /></a>\n";
                 echo "<a href=\"".traite_grr_url($grr_script_name)."?".$parametres_url."default_language=de\"><img src=\"img_grr/de_dp.png\" alt=\"Deutch\" title=\"deutch\" width=\"20\" height=\"13\" class=\"image\" /></a>\n";
                 echo "<a href=\"".traite_grr_url($grr_script_name)."?".$parametres_url."default_language=en\"><img src=\"img_grr/en_dp.png\" alt=\"English\" title=\"English\" width=\"20\" height=\"13\" class=\"image\" /></a>\n";
                 echo "<a href=\"".traite_grr_url($grr_script_name)."?".$parametres_url."default_language=it\"><img src=\"img_grr/it_dp.png\" alt=\"Italiano\" title=\"Italiano\" width=\"20\" height=\"13\" class=\"image\" /></a>\n";
                 echo "<a href=\"".traite_grr_url($grr_script_name)."?".$parametres_url."default_language=es\"><img src=\"img_grr/es_dp.png\" alt=\"Spanish\" title=\"Spanish\" width=\"20\" height=\"13\" class=\"image\" /></a>\n";

            //}
            $disconnect_link = false;
            if (!((getSettingValue("cacher_lien_deconnecter")=='y') and (isset($_SESSION['est_authentifie_sso'])))) {
               // on n'affiche pas le lien logout dans le cas d'un utilisateur LCS connect�.
               $disconnect_link = true;
               if (getSettingValue("authentification_obli") == 1) {
                   echo "<br />&nbsp;<a href=\"./logout.php?auto=0\" >".get_vocab('disconnect')."</a>";
               } else {
                   echo "<br />&nbsp;<a href=\"./logout.php?auto=0&amp;redirect_page_accueil=yes\" >".get_vocab('disconnect')."</a>";
               }
            }
            if ((getSettingValue("Url_portail_sso")!='') and (isset($_SESSION['est_authentifie_sso']))) {
                if ($disconnect_link)
                   echo "&nbsp;-&nbsp;";
                else
                   echo "<br />&nbsp;";
                echo('<a href="'.getSettingValue("Url_portail_sso").'">'.get_vocab("Portail_accueil").'</a>');
             }
             // Cas d'une authentification LASSO
             if ((getSettingValue('sso_statut') == 'lasso_visiteur') or (getSettingValue('sso_statut') == 'lasso_utilisateur')) {
               echo "<br />&nbsp;";
               if ($_SESSION['lasso_nameid'] == NULL)
                 echo "<a href=\"lasso/federate.php\">".get_vocab('lasso_federate_this_account')."</a>";
               else
                 echo "<a href=\"lasso/defederate.php\">".get_vocab('lasso_defederate_this_account')."</a>";
               }
          }
      ?>
     </td>
     <?php
	   if (((isset($area)) and ($area > 0)) or ((isset($room)) and ($room > 0)))
	      // si aucune ressource ni domaine ne sont d�finis, on affiche pas la colonne de s�lection du jour
        $affiche_col_date = TRUE;
	   else
	      $affiche_col_date = FALSE;

     if (($page=="no_admin") and ($affiche_col_date)) {
     ?>
         <td class="banner"  align="center">
           <form id="myform" action="" method="get" onsubmit="return onsubmitForm();"><div>
           <?php
           genDateSelector("", $day, $month, $year,"");
    		   if ((isset($area)) and ($area > 0))
             echo "<input type=\"hidden\" id=\"area_\" name=\"area\" value=\"$area\" />";
    		   if ((isset($room)) and ($room > 0))
             echo "<input type=\"hidden\" id=\"room_\" name=\"room\" value=\"$room\" />";
           ?>
		   <input type="submit" value="<?php echo get_vocab("gototoday") ?>" onclick="document.pressed='a'" />
           <br />
           <br />
           <input type="submit" value="<?php echo get_vocab("allday") ?>" onclick="document.pressed='d'" />
           <input type="submit" value="<?php echo get_vocab("week") ?>" onclick="document.pressed='w'" />
           <input type="submit" value="<?php echo get_vocab("month") ?>" onclick="document.pressed='m'" />
           </div></form>
         </td>
         <?php
     }
     if ($type_session == "with_session") {
          if ((authGetUserLevel(getUserName(),-1,'area') >= 4) or (authGetUserLevel(getUserName(),-1,'user') == 1))  {
           echo "<td class=\"banner\" align=\"center\">";
           echo "<a href='admin_accueil.php?day=$day&amp;month=$month&amp;year=$year'>".get_vocab("admin")."</a>\n";
           if(authGetUserLevel(getUserName(),-1,'area') >= 6)  {
              echo "<br />\n<form action=\"admin_save_mysql.php\" method=\"get\"><div>\n
              <input type=\"hidden\" name=\"flag_connect\" value=\"yes\" />\n
              <input type=\"submit\" value=\"".get_vocab("submit_backup")."\" /></div>\n
              </form>";
              how_many_connected();
           }
           echo "\n</td>";
      }
     }
      ?>
          <td class="banner" align="center">
      <?php
      if (@file_exists($clock_file)) {
        echo "<script type=\"text/javascript\">";
        echo "<!--\n";
        echo "new LiveClock();\n";
        echo "//-->";
        echo "</script><br />";
      }

      echo grr_help("","")."<br />";
      if (verif_access_search(getUserName())) {
          echo "<a href=\"report.php\">".get_vocab("report")."</a><br />";
      }
      echo "<span class=\"small\">".affiche_version()."</span> - ";
      if ($type_session == "with_session") {
          if ($_SESSION['statut'] == 'administrateur') {
              echo affiche_lien_contact("contact_support","identifiant:non","seulement_si_email");
          } else {
              echo affiche_lien_contact("contact_administrateur","identifiant:non","seulement_si_email");
          }
      } else {
          echo affiche_lien_contact("contact_administrateur","identifiant:non","seulement_si_email");
      }

          ?>
         </td>
        </tr>
       </table>
      </td>
     </tr>
    </table>
<?php
}
if (isset($use_prototype))
    echo "<script type=\"text/javascript\" src=\"./include/prototype-1.6.0.3.js\"></script>";
if (isset($use_tooltip_js))
    echo "<script type=\"text/javascript\" src=\"./include/tooltip.js\"></script>";
echo getSettingValue('message_accueil');
  }
}
/*
  V�rifie que les noms et pr�noms d'un utilisateur ne sont pas vides
  Dans le cas contraire, redirige vers la page de "g�rer mon compte"
*/
function VerifNomPrenomUser($type) {
    global $desactive_VerifNomPrenomUser; // ne pas prendre en compte la page my_account.php
    if (($type == "with_session") and ($desactive_VerifNomPrenomUser!='y') and (IsAllowedToModifyProfil())) {
        $test = grr_sql_query1("select login from ".TABLE_PREFIX."_utilisateurs where (login = '".getUserName()."' and (nom='' or prenom = ''))");
        if ($test != -1) {
            header("Location:my_account.php");
            die();
        }
    }


}

/*
  V�rifie si utilisateur autoris� � changer ses noms et pr�noms et mail
  Renvoie True (peut changer ses noms et pr�noms et email) ou False (ne peut pas)
*/
function sso_IsAllowedModify() {
    if (getSettingValue("sso_IsNotAllowedModify")=="y") {
        $source = grr_sql_query1("select source from grr_utilisateurs where login = '".getUserName()."'");
        if ($source == "ext")
            return FALSE;
        else
            return TRUE;
    } else
        return TRUE;
}


/*
  V�rifie que l'utilisateur est autoris� � changer ses noms et pr�noms
  Renvoie True (peut changer ses noms et pr�noms) ou False (ne peut pas)
*/
function IsAllowedToModifyProfil() {
    if (!(sso_IsAllowedModify()))
        return FALSE;
    // l'utilisateur connect� n'a pas le niveau suffisant pour modifier son compte
    if (authGetUserLevel(getUserName(),-1) < getSettingValue("allow_users_modify_profil"))
        return FALSE;
    else
        return TRUE;
}
/*
  V�rifie que l'utilisateur est autoris� � changer son emai
  Renvoie True (peut changer son email) ou False (ne peut pas)
*/
function IsAllowedToModifyEmail() {
    if (!(sso_IsAllowedModify()))
        return FALSE;
    // l'utilisateur connect� n'a pas le niveau suffisant pour modifier son compte
    if (authGetUserLevel(getUserName(),-1) < getSettingValue("allow_users_modify_email"))
        return FALSE;
    else
        return TRUE;
}

/*
  V�rifie que l'utilisateur est autoris� � changer son mot de passe
  Renvoie True (peut changer) ou False (ne peut pas)
*/
function IsAllowedToModifyMdp() {
    // l'utilisateur connect� n'a pas le niveau suffisant pour modifier son compte
    if (authGetUserLevel(getUserName(),-1) < getSettingValue("allow_users_modify_mdp"))
        return FALSE;
    else if ((getSettingValue("sso_statut") != "") or  (getSettingValue("ldap_statut") != '') or  (getSettingValue("imap_statut") != '')) {
        // ou bien on est dans un environnement SSO ou ldap et l'utilisateur n'est pas un utilisateur local
        $source = grr_sql_query1("select source from ".TABLE_PREFIX."_utilisateurs where login = '".getUserName()."'");
        if ($source == "ext")
            return FALSE;
        else
            return TRUE;
    } else
        return TRUE;
}

// Transforme $dur en une dur�e exprim�e en ann�es, semaines, jours, heures, minutes et secondes
// OU en dur�e num�rique exprim�e dans l'une des unit�s de fa�on fixe, pour l'�dition des
// r�servations par dur�e.
// $dur : dur�e sous forme d'une chaine de caract�re quandd $edition=false, sinon, dur�e en valeur num�rique.
// $units : variable conserv�e uniquement pour compatibilit� avec la fonction toTimeString originale
//          si $edition=false, sinon, contient l'unit� utilis�e pour $dur
// $edition : Valeur par d�faut : false. Indique si le retour est pour affichage ou pour modifier la dur�e.
// Version �crite par David M - E-Concept Applications
function toTimeString(&$dur, &$units, $edition = false)
{
    global $vocab;

    if($edition)
    {
        if($dur >= 60)
        {
            $dur = $dur/60;

            if($dur >= 60)
            {
                $dur /= 60;

                if(($dur >= 24) && ($dur % 24 == 0))
                {
                    $dur /= 24;

                    if(($dur >= 7) && ($dur % 7 == 0))
                    {
                        $dur /= 7;

                        if(($dur >= 52) && ($dur % 52 == 0))
                        {
                            $dur  /= 52;
                            $units = get_vocab("years");
                        }
                        else
                            $units = get_vocab("weeks");
                    }
                    else
                        $units = get_vocab("days");
                }
                else
                    $units = get_vocab("hours");

            }
            else
                $units = get_vocab("minutes");
        }
        else
            $units = get_vocab("seconds");
    }
    else
    {
        $duree_formatee = "";
        $not_first_unit = false;

        // On d�finit la dur�e en secondes de chaque type d'unit�
        $annee   = 60 * 60 * 24 * 365;
        $semaine = 60 * 60 * 24 * 7;
        $jour    = 60 * 60 * 24;
        $heure   = 60 * 60;
        $minute  = 60;

        // On calcule le nombre d'ann�es.
        $nb_annees = floor($dur / $annee);
        if($nb_annees > 0)
        {
            if($not_first_unit)
            {
                $duree_formatee .= ", ";
            }
            else
                $not_first_unit = true;

            $duree_formatee .= $nb_annees . " " . get_vocab("years");

            // On soustrait le nombre d'ann�es d�j� d�termin�es � la dur�e initiale.
            $dur = $dur - $nb_annees * $annee;
        }




        // On calcule le nombre de semaines.
        $nb_semaines = floor($dur / $semaine);
        if($nb_semaines > 0)
        {
            if($not_first_unit)
            {
                $duree_formatee .= ", ";
            }
            else
                $not_first_unit = true;

            $duree_formatee .= $nb_semaines . " " . get_vocab("weeks");

            // On soustrait le nombre de semaines d�j� d�termin�es � la dur�e initiale.
            $dur = $dur - $nb_semaines * $semaine;
        }




        // On calcule le nombre de jours.
        $nb_jours = floor($dur / $jour);
        if($nb_jours > 0)
        {
            if($not_first_unit)
            {
                $duree_formatee .= ", ";
            }
            else
                $not_first_unit = true;

            $duree_formatee .= $nb_jours . " " . get_vocab("days");

            // On soustrait le nombre de jours d�j� d�termin�s � la dur�e initiale.
            $dur = $dur - $nb_jours * $jour;
        }




        // On calcule le nombre d'heures.
        $nb_heures = floor($dur / $heure);
        if($nb_heures > 0)
        {
            if($not_first_unit)
            {
                $duree_formatee .= ", ";
            }
            else
                $not_first_unit = true;

            $duree_formatee .= $nb_heures . " " . get_vocab("hours");

            // On soustrait le nombre d'heures d�j� d�termin�es � la dur�e initiale.
            $dur = $dur - $nb_heures * $heure;
        }




        // On calcule le nombre de minutes.
        $nb_minutes = floor($dur / $minute);
        if($nb_minutes > 0)
        {
            if($not_first_unit)
            {
                $duree_formatee .= ", ";
            }
            else
                $not_first_unit = true;

            $duree_formatee .= $nb_minutes . " " . get_vocab("minutes");

            // On soustrait le nombre de minutes d�j� d�termin�es � la dur�e initiale.
            $dur = $dur - $nb_minutes * $minute;
        }




        // On calcule le nombre de secondes.
        if($dur > 0)
        {
            if($not_first_unit)
            {
                $duree_formatee .= ", ";
            }
            else
                $not_first_unit = true;

            $duree_formatee .= $dur . " " . get_vocab("seconds");
        }

        // On s�pare les diff�rentes unit�s de la chaine.
        $tmp = explode(", ", $duree_formatee);
        // Si on a plus d'une unit�e...
        if(count($tmp) > 1)
        {
            // ... on d�pile le tableau par la fin...
            $tmp_fin = array_pop($tmp);
            // ... on reconstiture la chaine avec les premiers �l�ments...
            $duree_formatee = implode(", ", $tmp);
            // ... et on ajoute le dernier �l�ment
            $duree_formatee .= " et " . $tmp_fin;
        }
        // Sinon, on ne change rien.

        $dur = $duree_formatee;
        $units = "";
    }
}

// Transforme $dur en un nombre entier
// $dur : dur�e
// $units : unit�
function toPeriodString($start_period, &$dur, &$units)
{
  // la dur�e est donn�e en secondes
  global $enable_periods, $periods_name, $vocab;
  $max_periods = count($periods_name);
  $dur /= 60; // on transforme la dur�e en minutes
  // Chaque minute correspond � un cr�neau
  if( $dur >= $max_periods || $start_period == 0 )
  {
    if( $start_period == 0 && $dur == $max_periods )
    {
      $units = get_vocab("periods");
      $dur = $max_periods;
     return;
    }
    $dur /= 60;
    if(($dur >= 24) && is_int($dur))
    {
      $dur /= 24;
      $units = get_vocab("days");
      return;
    } else {
      $dur *= 60;
      $dur = ($dur % $max_periods) + floor( $dur/(24*60) ) * $max_periods;
      $units = get_vocab("periods");
      return;
    }
  } else
    $units = get_vocab("periods");
}




function genDateSelectorForm($prefix, $day, $month, $year,$option)
{
    global $nb_year_calendar;
    $selector_data = "";

    // Compatibilit� avec version GRR < 1.9
    if (!isset($nb_year_calendar)) $nb_year_calendar = 5;

    if(($day   == 0) and ($day != "")) $day = date("d");
    if($month == 0) $month = date("m");
    if($year  == 0) $year = date("Y");

    if ($day != "") {
     $selector_data .= "<select name=\"${prefix}day\" id=\"${prefix}day\">\n";
     for($i = 1; $i <= 31; $i++)
      $selector_data .= "<option" . ($i == $day ? " selected=\"selected\"" : "") . ">$i</option>\n";

     $selector_data .= "</select>";
    }
    $selector_data .= "<select name=\"${prefix}month\" id=\"${prefix}month\">\n";

    for($i = 1; $i <= 12; $i++)
    {
        $m = utf8_strftime("%b", mktime(0, 0, 0, $i, 1, $year));

        $selector_data .=  "<option value=\"$i\"" . ($i == $month ? " selected=\"selected\"" : "") . ">$m</option>\n";
    }

    $selector_data .=  "</select>";
    $selector_data .=  "<select name=\"${prefix}year\" id=\"${prefix}year\">\n";

    $min = strftime("%Y", getSettingValue("begin_bookings"));
    if ($option == "more_years") $min = date("Y") - $nb_year_calendar;

    $max = strftime("%Y", getSettingValue("end_bookings"));
    if ($option == "more_years") $max = date("Y") + $nb_year_calendar;

    for($i = $min; $i <= $max; $i++)
      $selector_data .= "<option value=\"$i\" " . ($i == $year ? " selected=\"selected\"" : "") . ">$i</option>\n";

    $selector_data .= "</select>";

    return $selector_data;
}


function genDateSelector($prefix, $day, $month, $year,$option)
{
  echo genDateSelectorForm($prefix, $day, $month, $year,$option);
}




# Error handler - this is used to display serious errors such as database
# errors without sending incomplete HTML pages. This is only used for
# errors which "should never happen", not those caused by bad inputs.
# If $need_header!=0 output the top of the page too, else assume the
# caller did that. Alway outputs the bottom of the page and exits.
function fatal_error($need_header, $message)
{
    global $vocab;
    if ($need_header) print_header(0, 0, 0, 0);
    echo $message;
    include "trailer.inc.php";
    exit;
}

function compare_ip_adr($ip1,$ip2){
  if ($ip2=="") {
    return true;
  }
  $tab_ip1=explode(".",$ip1);
  $tab_ip2=explode(".",$ip2);
  $i=0;
  $ip1 ="";
  $ip2 ="";
  while($i<4) {
      // On traite ip1
      if (strlen($tab_ip1[$i])==0) {
          $ip1.="000";
      } else if (strlen($tab_ip1[$i])==1) {
          $ip1.="00".$tab_ip1[$i];
      } else if (strlen($tab_ip1[$i])==2) {
          $ip1.="0".$tab_ip1[$i];
      } else
          $ip1.=$tab_ip1[$i];
      // On traite ip2
      if (!isset($tab_ip2[$i]))
          $ip2.="***";
      else if (strlen($tab_ip2[$i])==0) {
          $ip2.="***";
      } else if (strlen($tab_ip2[$i])==1) {
          if ($tab_ip2[$i]=="*")
              $ip2.="**".$tab_ip2[$i];
          else
              $ip2.="00".$tab_ip2[$i];
      } else if (strlen($tab_ip2[$i])==2) {
          $ip2.="0".$tab_ip2[$i];
      } else
          $ip2.=$tab_ip2[$i];
      $i++;
  }
  // On compare ip1 et ip2
  $i=0;
  while($i<12) {
      if (($ip1[$i]!=$ip2[$i]) and ($ip2[$i]!="*")) {
          return false;
      }
      $i++;
  }
  return true;
}

# Retourne le domaine par d�faut; Utilis� si aucun domaine n'a �t� d�fini.
function get_default_area($id_site=-1)
{
    if (getSettingValue("module_multisite") == "Oui")
      $use_multisite = TRUE;
    else
      $use_multisite = FALSE;
    if (OPTION_IP_ADR==1) {
        // Affichage d'un domaine par defaut en fonction de l'adresse IP de la machine cliente
        /*if (($id_site != -1) and ($use_multisite))
          $sql= "SELECT a.ip_adr, a.id
          FROM ".TABLE_PREFIX."_area a, ".TABLE_PREFIX."_j_site_area j
          WHERE a.id=j.id_area and j.id_site=$id_site and a.ip_adr!=''
          ORDER BY a.access, a.order_display, a.area_name";
        else */
          $sql = "SELECT ip_adr, id FROM ".TABLE_PREFIX."_area WHERE ip_adr!='' ORDER BY access, order_display, area_name";
        $res = grr_sql_query($sql);
        if ($res) {
          for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
          {
            if (compare_ip_adr($_SERVER['REMOTE_ADDR'],$row[0])) {
                return $row[1];}
          }
        }
    }
    if(authGetUserLevel(getUserName(),-1) >= 6)
        // si l'admin est connect�, on cherche le premier domaine venu
        if (($id_site != -1) and ($use_multisite))
          $res = grr_sql_query("SELECT a.id
          FROM ".TABLE_PREFIX."_area a, ".TABLE_PREFIX."_j_site_area j
          WHERE a.id=j.id_area and j.id_site=$id_site
          ORDER BY a.order_display, a.area_name");
        else
          $res = grr_sql_query("SELECT id FROM ".TABLE_PREFIX."_area ORDER BY access, order_display, area_name");
    else
        // s'il ne s'agit pas de l'admin, on cherche le premier domaine � acc�s non restreint
        if (($id_site != -1) and ($use_multisite))
          $res = grr_sql_query("SELECT a.id
          FROM ".TABLE_PREFIX."_area a, ".TABLE_PREFIX."_j_site_area j
          WHERE a.id=j.id_area and j.id_site=$id_site and a.access!='r'
          ORDER BY a.order_display, a.area_name");
        else
          $res = grr_sql_query("SELECT id FROM ".TABLE_PREFIX."_area where access!='r' ORDER BY access, order_display, area_name");

    if ($res && grr_sql_count($res)>0 ) {
        $row = grr_sql_row($res, 0);
        grr_sql_free($res);
        return $row[0];
    } else {
        // On cherche le premier domaine � acc�s restreint
        if (($id_site != -1) and ($use_multisite))
          $res = grr_sql_query("SELECT a.id
          FROM ".TABLE_PREFIX."_area a, ".TABLE_PREFIX."_j_site_area j, ".TABLE_PREFIX."_j_user_area u
          WHERE a.id=j.id_area and j.id_site=$id_site and a.id=u.id_area and u.login='" . getUserName() . "'
          ORDER BY a.order_display, a.area_name");
        else
          $res = grr_sql_query("select id from ".TABLE_PREFIX."_area, ".TABLE_PREFIX."_j_user_area where
          ".TABLE_PREFIX."_area.id=".TABLE_PREFIX."_j_user_area.id_area and
          login='" . getUserName() . "'
          ORDER BY order_display, area_name");
        if ($res && grr_sql_count($res)>0 ) {
            $row = grr_sql_row($res, 0);
            grr_sql_free($res);
            return $row[0];
        }
    else
        return -1;
    }

}

# Retourne le site par d�faut;
function get_default_site()
{
        $res = grr_sql_query1("SELECT min(id) FROM ".TABLE_PREFIX."_site");
        return $res;
}


# Get the local day name based on language. Note 2000-01-02 is a Sunday.
function day_name($daynumber)
{
    return utf8_strftime("%A", mktime(0,0,0,1,2+$daynumber,2000));
}

function affiche_heure_creneau($t,$resolution) {
    global $twentyfourhour_format;
    if ($twentyfourhour_format)
        $hour_min_format = "H:i";
    else
        $hour_min_format = "h:ia";
    return date($hour_min_format,$t) ."&nbsp;-&nbsp;".date($hour_min_format,$t+$resolution);
}

function hour_min_format()
{
        global $twentyfourhour_format;
        if ($twentyfourhour_format)
    {
              return "H:i";
    }
    else
    {
        return "h:ia";
    }
}

/*
Fonction utilis�e dans le cas o� les cr�neaux de r�servation sont bas�s sur des intitul�s pr�-d�finis :
Formatage de la date de d�but ou de fin de r�servation.
Dans le cas du d�but de r�servation on a $mod_time=0
Dans le cas de la fin de r�servation on a $mod_time=-1
*/
function period_date_string($t, $mod_time=0)
{
    global $periods_name, $dformat;
    $time = getdate($t);
    $p_num = $time["minutes"] + $mod_time;
    if( $p_num < 0 ) {
        // fin de r�servation : cas $time["minutes"] = 0. il faut afficher le dernier cr�neau de la journ�e pr�c�dente
        $t = $t - 60*60*24;
        $p_num = count($periods_name) - $p_num;
    }
    if( $p_num >= count($periods_name) - 1 ) $p_num = count($periods_name) - 1;
    return array($p_num, $periods_name[$p_num] . utf8_strftime(", ".$dformat,$t));
}

/*
Fonction utilis�e dans le cas o� les cr�neaux de r�servation sont bas�s sur des intitul�s pr�-d�finis :
Formatage des p�riodes de d�but ou de fin de r�servation.
Dans le cas du d�but de r�servation on a $mod_time=0
Dans le cas de la fin de r�servation on a $mod_time=-1
*/
function period_time_string($t, $mod_time=0)
{
    global $periods_name;
    $time = getdate($t);
    $p_num = $time["minutes"] + $mod_time;
    if( $p_num < 0 ) $p_num = 0;
    if( $p_num >= count($periods_name) - 1 ) $p_num = count($periods_name) - 1;
    return $periods_name[$p_num];
}


function time_date_string($t,$dformat)
{
        global $twentyfourhour_format;
        # This bit's necessary, because it seems %p in strftime format
        # strings doesn't work
        $ampm = date("a",$t);
        if ($twentyfourhour_format)
    {
              return utf8_strftime("%H:%M - ".$dformat,$t);
    }
    else
    {
            return utf8_strftime("%I:%M$ampm - ".$dformat,$t);
    }
}

function time_date_string_jma($t,$dformat)
{
        global $twentyfourhour_format;
        # This bit's necessary, because it seems %p in strftime format
        # strings doesn't work
        $ampm = date("a",$t);
        if ($twentyfourhour_format)
    {
              return utf8_strftime($dformat,$t);
    }
    else
    {
            return utf8_strftime($dformat,$t);
    }
}


// Renvoie une balise span avec un style backgrounf-color correspondant au type de  la r�servation
function span_bgground($colclass)
{
    global $tab_couleur;
    static $ecolors;
    $num_couleur = grr_sql_query1("select couleur from ".TABLE_PREFIX."_type_area where type_letter='".$colclass."'");
    echo "<span style=\"background-color: ".$tab_couleur[$num_couleur]."; background-image: none; background-repeat: repeat; background-attachment: scroll;\">";
}


# Output a start table cell tag <td> with color class and fallback color.
function tdcell($colclass, $width='')
{
    if ($width!="") $temp = " style=\"width:".$width."%;\" "; else $temp = "";
    global $tab_couleur;
    static $ecolors;
    if (($colclass >= "A") and ($colclass <= "Z")) {
        $num_couleur = grr_sql_query1("select couleur from ".TABLE_PREFIX."_type_area where type_letter='".$colclass."'");
        echo "<td style=\"background-color:".$tab_couleur[$num_couleur].";\" ".$temp.">";
    } else
        echo "<td class=\"$colclass\" ".$temp.">";
}

// Paul Force
function tdcell_rowspan($colclass , $step)
{
    global $tab_couleur;
    static $ecolors;
    if (($colclass >= "A") and ($colclass <= "Z")) {
        $num_couleur = grr_sql_query1("select couleur from ".TABLE_PREFIX."_type_area where type_letter='".$colclass."'");
        echo "<td rowspan=\"$step\" style=\"background-color:".$tab_couleur[$num_couleur].";\">";
    } else
        echo "<td rowspan=\"$step\" td class=\"".$colclass."\">";
}


# Display the entry-type color key. This has up to 2 rows, up to 10 columns.
function show_colour_key($area_id)
{
    echo "<table border=\"0\"><tr>\n";
    $nct = 0;
    $sql = "SELECT DISTINCT t.id, t.type_name, t.type_letter FROM ".TABLE_PREFIX."_type_area t
    LEFT JOIN ".TABLE_PREFIX."_j_type_area j on j.id_type=t.id
    WHERE (j.id_area  IS NULL or j.id_area != '".$area_id."')
    ORDER BY t.order_display";
    $res = grr_sql_query($sql);
    if ($res) {
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
    {
        // La requ�te sql pr�c�dente laisse passer les cas o� un type est non valide dans le domaine concern� ET au moins dans un autre domaine, d'o� le test suivant
        $test = grr_sql_query1("select id_type from ".TABLE_PREFIX."_j_type_area where id_type = '".$row[0]."' and id_area='".$area_id."'");
        if ($test == -1) {
            $id_type        = $row[0];
            $type_name        = $row[1];
            $type_letter          = $row[2];
            if (++$nct > 10)
                {
                    $nct = 0;
                    echo "</tr><tr>";
                }
            tdcell($type_letter);
            echo "$type_name</td>\n";
        }
    }
    echo "</tr></table>\n";
    }
}




# Round time down to the nearest resolution
function round_t_down($t, $resolution, $am7)
{
        return (int)$t - (int)abs(((int)$t-(int)$am7)
                  % $resolution);
}

# Round time up to the nearest resolution
function round_t_up($t, $resolution, $am7)
{
    if (($t-$am7) % $resolution != 0)
    {
        return $t + $resolution - abs(((int)$t-(int)
                           $am7) % $resolution);
    }
    else
    {
        return $t;
    }
}
/**
 * TODO: Enter description here...
 *
 * @param unknown_type $link
 * @param unknown_type $site
 * @param unknown_type $current
 * @param unknown_type $year
 * @param unknown_type $month
 * @param unknown_type $day
 * @param unknown_type $user
 * @return unknown
 */
 function make_site_select_html($link,$current_site,$year,$month,$day,$user)
 {
   global $vocab;
   $out_html = '<b><i>'.get_vocab('sites').get_vocab('deux_points').'</i></b>
       <form id="site_001" action="'.$_SERVER['PHP_SELF'].'">
         <div><select name="site" onchange="site_go()">';
   // On vient lister les sites qui ont un domaine li�
   // Pour mysql >= 4.1
   /*
   $sql = "SELECT id,sitename
           FROM ".TABLE_PREFIX."_site
           WHERE ".TABLE_PREFIX."_site.id IN (SELECT id_site FROM ".TABLE_PREFIX."_j_site_area GROUP BY id_site)
           ORDER BY id ASC";
   */
   // Pour mysql < 4.1
   $sql = "SELECT id, sitename
           FROM ".TABLE_PREFIX."_site
           left join ".TABLE_PREFIX."_j_site_area on ".TABLE_PREFIX."_site.id = ".TABLE_PREFIX."_j_site_area.id_site
           where ".TABLE_PREFIX."_j_site_area.id_site is not null
           GROUP BY id_site
           ORDER BY id ASC
           ";

   $res = grr_sql_query($sql);
   if ($res)
   {
     $nb_sites_a_afficher = 0;
     for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
     {
       // Pour chaque site, on d�termine le premier domaine disponible
       $sql = "SELECT id_area
               FROM ".TABLE_PREFIX."_j_site_area
               WHERE ".TABLE_PREFIX."_j_site_area.id_site='".$row[0]."'";
       $res2 = grr_sql_query($sql);

       // A on un r�sultat ?
       $default_area=-1;
       if ($res2 && grr_sql_count($res2) > 0)
       {
         $row2 = grr_sql_row($res2, 0);
         for ($j = 0; ($row2 = grr_sql_row($res2, $j)); $j++)
         {
           if (authUserAccesArea($user,$row2[0])==1) {
             // on a trouv� un domaine autoris�
             $default_area=$row2[0];
             $j=grr_sql_count($res2)+1; // On arr�te la boucle
           }
         }
       }
       // On lib�re la ressource2
       grr_sql_free($res2);
       if ($default_area!=-1) { // on affiche le site uniquement si au moins un domaine est visible par l'utilisateur
         $nb_sites_a_afficher++;
         $selected = ($row[0] == $current_site) ? 'selected="selected"' : '';
         $link2 = $link.'?year='.$year.'&amp;month='.$month.'&amp;day='.$day.'&amp;area='.$default_area;
         $out_html.="\n".'           <option '.$selected.' value="'.$link2.'">'.htmlspecialchars($row[1]).'</option>';
       }
     }
   }
   if ($nb_sites_a_afficher > 1) {
     // s'il y a au moins deux sites � afficher, on met une liste d�roulante, sinon, on affiche rien.
     $out_html .= '
         </select></div>
         <script type="text/javascript">
         <!--
         function site_go()
         {
           box = document.getElementById("site_001").site;
           destination = box.options[box.selectedIndex].value;
           if (destination) location.href = destination;
         }
         // -->
         </script>

         <noscript>
           <div><input type="submit" value="change" /></div>
         </noscript>
       </form>';
     return $out_html;
   }
 }

# generates some html that can be used to select which area should be
# displayed.
function make_area_select_html( $link, $current_site, $current_area, $year, $month, $day, $user)
{
   global $vocab;
   if (getSettingValue("module_multisite") == "Oui")
     $use_multi_site = 'y';
   else
     $use_multi_site = 'n';
   if ($use_multi_site=='y') { // on a activ� les sites
     if ($current_site!=-1)
       $sql = "SELECT a.id, a.area_name,a.access
             FROM ".TABLE_PREFIX."_area a, ".TABLE_PREFIX."_j_site_area j
             WHERE a.id=j.id_area and j.id_site=$current_site
             ORDER BY a.order_display, a.area_name";
     else
       $sql = "";
   } else {
     $sql = "SELECT id, area_name,access
           FROM ".TABLE_PREFIX."_area
           ORDER BY order_display, area_name";
   }

    $out_html = "<b><i>".get_vocab("areas")."</i></b>\n";
    $out_html .= "<form id=\"area_001\" action=\"".$_SERVER['PHP_SELF']."\">\n";
    $out_html .= "<div><select name=\"area\"";
    $out_html .= " onchange=\"area_go()\"";
    $out_html .= ">\n";


       $res = grr_sql_query($sql);
       if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
       {
        $selected = ($row[0] == $current_area) ? "selected=\"selected\"" : "";
        $link2 = "$link?year=$year&amp;month=$month&amp;day=$day&amp;area=$row[0]";
        if (authUserAccesArea($user,$row[0])==1) {
            $out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($row[1])."</option>\n";
        }
       }
    $out_html .= "</select></div>
       <script type=\"text/javascript\">
       <!--
       function area_go()
        {
        box = document.getElementById(\"area_001\").area;
        destination = box.options[box.selectedIndex].value;
        if (destination) location.href = destination;
        }
        // -->
        </script>

        <noscript>
        <div><input type=\"submit\" value=\"Change\" /></div>
        </noscript>";
     $out_html .= "</form>";

    return $out_html;
} # end make_area_select_html

function make_room_select_html( $link, $current_area, $current_room, $year, $month, $day )
{
    global $vocab;
    $out_html = "<b><i>".get_vocab('rooms').get_vocab("deux_points")."</i></b><br /><form id=\"room_001\" action=\"".$_SERVER['PHP_SELF']."\">
                 <div><select name=\"room\" onchange=\"room_go()\">";

    $out_html .= "<option value=\"".$link."_all.php?year=$year&amp;month=$month&amp;day=$day&amp;area=$current_area\">".get_vocab("all_rooms")."</option>";
    $sql = "select id, room_name, description from ".TABLE_PREFIX."_room where area_id='".protect_data_sql($current_area)."' order by order_display,room_name";
       $res = grr_sql_query($sql);
       if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
       {
        if (verif_acces_ressource(getUserName(),$row[0])) {
          if ($row[2]) {$temp = " (".htmlspecialchars($row[2]).")";} else {$temp="";}
          $selected = ($row[0] == $current_room) ? "selected=\"selected\"" : "";
          $link2 = "$link.php?year=$year&amp;month=$month&amp;day=$day&amp;room=$row[0]";
          $out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($row[1].$temp)."</option>";
        }
       }
    $out_html .= "</select></div>
       <script type=\"text/javascript\">
       <!--
       function room_go()
        {
        box = document.getElementById(\"room_001\").room;
        destination = box.options[box.selectedIndex].value;
        if (destination) location.href = destination;
        }
        // -->
        </script>

        <noscript>
        <div><input type=\"submit\" value=\"Change\" /></div>
        </noscript>
        </form>";

    return $out_html;
} # end make_room_select_html

/**
 * Affichage des domaines sous la forme d'une liste
 *
 * @param unknown_type $link
 * @param unknown_type $current
 * @param unknown_type $year
 * @param unknown_type $month
 * @param unknown_type $day
 * @param unknown_type $id_site
 * @param unknown_type $user
 */
 function make_site_list_html($link,$current_site,$year,$month,$day,$user)
 {
   global $vocab;
   // On affiche le site
   if (getSettingValue("module_multisite") == "Oui") {
     $out_html = '
      <b><i><span class="bground">'.get_vocab('sites').get_vocab('deux_points').'</span></i></b>
      <br />';
     $sql = "SELECT id,sitename
     FROM ".TABLE_PREFIX."_site
     ORDER BY sitename";
     $res = grr_sql_query($sql);
     if ($res)
     {
       $nb_sites_a_afficher = 0;
       for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
       {
       // Pour chaque site, on d�termine s'il y a des domaines visibles par l'utilisateur
       $sql = "SELECT id_area
               FROM ".TABLE_PREFIX."_j_site_area
               WHERE ".TABLE_PREFIX."_j_site_area.id_site='".$row[0]."'";
       $res2 = grr_sql_query($sql);
       $au_moins_un_domaine = FALSE;
       if ($res2 && grr_sql_count($res2) > 0)
       {
         $row2 = grr_sql_row($res2, 0);
         for ($j = 0; ($row2 = grr_sql_row($res2, $j)); $j++)
         {
           if (authUserAccesArea($user,$row2[0])==1) {
             // on a trouv� un domaine autoris�
             $au_moins_un_domaine=TRUE;
             $j=grr_sql_count($res2)+1; // On arr�te la boucle
           }
         }
       }
       // On lib�re la ressource2
       grr_sql_free($res2);
       if ($au_moins_un_domaine) { // on affiche le site uniquement si au moins un domaine est visible par l'utilisateur
         $nb_sites_a_afficher++;
         if ($row[0] == $current_site)
         {
           $out_html .= '
       <b><span class="week">&gt;&nbsp;<a href="'.$link.'?year='.$year.'&amp;month='.$month.'&amp;day='.$day.'&amp;id_site='.$row[0].'" title="'.$row[1].'">'.htmlspecialchars($row[1]).'</a></span></b>
       <br />'."\n";
         } else {
           $out_html .= '
      <a href="'.$link.'?year='.$year.'&amp;month='.$month.'&amp;day='.$day.'&amp;id_site='.$row[0].'" title="'.$row[1].'">'.htmlspecialchars($row[1]).'</a>
      <br />'."\n";
         }
       }
       }
       grr_sql_free($res2);
     }
     grr_sql_free($res2);
     if ($nb_sites_a_afficher>1)
       return $out_html;
     else
       return '';
   }
 }


function make_area_list_html($link, $current_site, $current_area, $year, $month, $day, $user) {
   global $vocab;
   if (getSettingValue("module_multisite") == "Oui")
     $use_multi_site = 'y';
   else
     $use_multi_site = 'n';
   echo "<b><i><span class=\"bground\">".get_vocab("areas")."</span></i></b><br />";
   if ($use_multi_site=='y') { // on a activ� les sites
     if ($current_site!=-1)
       $sql = "SELECT a.id, a.area_name,a.access
             FROM ".TABLE_PREFIX."_area a, ".TABLE_PREFIX."_j_site_area j
             WHERE a.id=j.id_area and j.id_site=$current_site
             ORDER BY a.order_display, a.area_name";
     else
       $sql = "";
   } else {
     $sql = "SELECT id, area_name,access
           FROM ".TABLE_PREFIX."_area
           ORDER BY order_display, area_name";
   }
   if (($current_site!=-1) or ($use_multi_site=='n'))
     $res = grr_sql_query($sql);
   if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
    {
    if (authUserAccesArea($user,$row[0])==1) {
       if ($row[0] == $current_area)
          {
             echo "<b><span class=\"week\">&gt;&nbsp;<a href=\"".$link."?year=$year&amp;month=$month&amp;day=$day&amp;area=$row[0]\">".htmlspecialchars($row[1])."</a></span></b><br />\n";
          } else {
             echo "<a href=\"".$link."?year=$year&amp;month=$month&amp;day=$day&amp;area=$row[0]\">".htmlspecialchars($row[1])."</a><br />\n";
          }
       }
   }
   grr_sql_free($res);
}
function make_room_list_html($link,$current_area, $current_room, $year, $month, $day) {
   global $vocab;
   echo "<b><i><span class=\"bground\">".get_vocab("rooms").get_vocab("deux_points")."</span></i></b><br />";
   $sql = "select id, room_name, description from ".TABLE_PREFIX."_room where area_id='".protect_data_sql($current_area)."' order by order_display,room_name";
   $res = grr_sql_query($sql);
   if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
   {
     // On affiche uniquement les ressources autoris�es
     if (verif_acces_ressource(getUserName(), $row[0])) {
      if ($row[0] == $current_room)
      {
        echo "<b><span class=\"week\">&gt;&nbsp;".htmlspecialchars($row[1])."</span></b><br />\n";
      } else {
        echo "<a href=\"".$link."?year=$year&amp;month=$month&amp;day=$day&amp;&amp;room=$row[0]\">".htmlspecialchars($row[1]). "</a><br />\n";
      }
     }
   }
}


function send_mail($id_entry,$action,$dformat,$tab_id_moderes=array())
{
    $message_erreur = "";
// $action = 1 -> Cr�ation
// $action = 2 -> Modification
// $action = 3 -> Suppression
// $action = 4 -> Suppression automatique
// $action = 5 -> r�servation en attente de mod�ration
// $action = 6 -> R�sultat d'une d�cision de mod�ration
// $action = 7 -> Notification d'un retard dans la restitution d'une ressource.
    global $vocab, $grrSettings, $locale, $weekstarts, $enable_periods, $periods_name;
    require_once ("./include/mail.inc.php");
    $m= new my_phpmailer();
    $m->SetLanguage("fr","./phpmailer/language/");
    setlocale(LC_ALL,$locale);

    // R�cup�ration des donn�es concernant la r�servation
    $sql = "
    SELECT ".TABLE_PREFIX."_entry.name,
    ".TABLE_PREFIX."_entry.description,
    ".TABLE_PREFIX."_entry.beneficiaire,
    ".TABLE_PREFIX."_room.room_name,
    ".TABLE_PREFIX."_area.area_name,
    ".TABLE_PREFIX."_entry.type,
    ".TABLE_PREFIX."_entry.room_id,
    ".TABLE_PREFIX."_entry.repeat_id,
    " . grr_sql_syntax_timestamp_to_unix("".TABLE_PREFIX."_entry.timestamp") . ",
    (".TABLE_PREFIX."_entry.end_time - ".TABLE_PREFIX."_entry.start_time),
    ".TABLE_PREFIX."_entry.start_time,
    ".TABLE_PREFIX."_entry.end_time,
    ".TABLE_PREFIX."_room.area_id,
    ".TABLE_PREFIX."_room.delais_option_reservation,
    ".TABLE_PREFIX."_entry.option_reservation,
    ".TABLE_PREFIX."_entry.moderate,
    ".TABLE_PREFIX."_entry.beneficiaire_ext,
    ".TABLE_PREFIX."_entry.jours
    FROM ".TABLE_PREFIX."_entry, ".TABLE_PREFIX."_room, ".TABLE_PREFIX."_area
    WHERE ".TABLE_PREFIX."_entry.room_id = ".TABLE_PREFIX."_room.id
    AND ".TABLE_PREFIX."_room.area_id = ".TABLE_PREFIX."_area.id
    AND ".TABLE_PREFIX."_entry.id='".protect_data_sql($id_entry)."'
    ";
    $res = grr_sql_query($sql);
    if (! $res) fatal_error(0, grr_sql_error());
    if(grr_sql_count($res) < 1) fatal_error(0, get_vocab('invalid_entry_id'));
    $row = grr_sql_row($res, 0);
    grr_sql_free($res);

    // R�cup�ration des donn�es concernant l'affichage du planning du domaine
/*Renvoie les param�tres d'affichage du domaine
Cas o� les cr�neaux sont bas�s sur les intitul�s :
$enable_periods = y

Dans ce cas chaque cr�neau correspond � une minute entre 12 h et 12 h 59 (on peut donc d�finir au plus 59 cr�neaux !)

$periods_name[] = tableau des intitul�s des cr�neaux
$resolution = 60 : on impose un ��pas�� de 60 secondes, c'est-�-dire 1 minute
$morningstarts = 12 : d�but des r�servation � 12 h
$eveningends = 12 :heure de fin des r�servations : 12 h
$eveningends_minutes : nombre de minutes � ajouter � l'heure $eveningends pour avoir la fin r�elle d'une journ�e. Dans ce cas, il est �gal � : (nombre d'intitul� � 1)
$weekstarts =
$twentyfourhour_format = $row_[6];

Cas o� les cr�neaux sont bas�s sur le temps
$enable_periods = n
$resolution
$morningstarts
$eveningends
$eveningends_minutes
$weekstarts
$twentyfourhour_format
*/


    get_planning_area_values($row[12]);

    $breve_description        = bbcode(removeMailUnicode(htmlspecialchars($row[0])),'nobbcode');
    $description  = bbcode(removeMailUnicode(htmlspecialchars($row[1])),'nobbcode');
    $beneficiaire    = htmlspecialchars($row[2]);
    $room_name    = removeMailUnicode(htmlspecialchars($row[3]));
    $area_name    = removeMailUnicode(htmlspecialchars($row[4]));
    $type         = $row[5];
    $room_id      = $row[6];
    $repeat_id    = $row[7];
    $updated      = time_date_string($row[8],$dformat);
    $date_avis    = strftime("%Y/%m/%d",$row[10]);
    $delais_option_reservation = $row[13];
    $option_reservation = $row[14];
    $moderate = $row[15];
    $beneficiaire_ext = htmlspecialchars($row[16]);
    $jours_cycle = htmlspecialchars($row[17]);
    $duration     = $row[9];
    if($enable_periods=='y')
        list( $start_period, $start_date) =  period_date_string($row[10]);
    else
        $start_date = time_date_string($row[10],$dformat);
    if($enable_periods=='y')
        list( , $end_date) =  period_date_string($row[11], -1);
    else
        $end_date = time_date_string($row[11],$dformat);
    $rep_type = 0;

    if($repeat_id != 0)
    {
        $res = grr_sql_query("SELECT rep_type, end_date, rep_opt, rep_num_weeks FROM ".TABLE_PREFIX."_repeat WHERE id='".protect_data_sql($repeat_id)."'");
        if (! $res) fatal_error(0, grr_sql_error());

        if (grr_sql_count($res) == 1)
        {
            $row2 = grr_sql_row($res, 0);

            $rep_type     = $row2[0];
            $rep_end_date = strftime($dformat,$row2[1]);
            $rep_opt      = $row2[2];
            $rep_num_weeks = $row2[3];
        }
        grr_sql_free($res);
    }
    if ($enable_periods=='y')
        toPeriodString($start_period, $duration, $dur_units);
    else
        toTimeString($duration, $dur_units);
    $weeklist = array("unused","every week","week 1/2","week 1/3","week 1/4","week 1/5");
    if ($rep_type == 2)
        $affiche_period = $vocab[$weeklist[$rep_num_weeks]];
    else
        $affiche_period = $vocab['rep_type_'.$rep_type];

    // Le b�n�ficiaire
    $beneficiaire_email = affiche_nom_prenom_email($beneficiaire,$beneficiaire_ext,"onlymail");
    if ($beneficiaire != "") {
         $beneficiaire_actif = grr_sql_query1("select etat from ".TABLE_PREFIX."_utilisateurs where login='$beneficiaire'");
         if ($beneficiaire_actif == -1)
             $beneficiaire_actif = 'actif'; // cas des administrateurs
    } else if (($beneficiaire_ext != "")  and ($beneficiaire_email!="")) {
        $beneficiaire_actif = "actif";
    } else $beneficiaire_actif = "inactif";

    // Utilisateur ayant agit sur la r�servation
    $user_login=getUserName();
    $user_email = grr_sql_query1("select email from ".TABLE_PREFIX."_utilisateurs where login='$user_login'");
    //
    // Elaboration du message destin� aux utilisateurs d�sign�s par l'admin dans la partie "Mails automatiques"
    //
    //Nom de l'�tablissement et mention "mail automatique"
    $message = removeMailUnicode(getSettingValue("company"))." - ".$vocab["title_mail"];
    // Url de GRR
    $message = $message.traite_grr_url("","y")."\n\n";

    $sujet = $vocab["subject_mail1"].$room_name." - ".$date_avis;
    if ($action == 1) {
        // Nouvelle r�servation
        $sujet = $sujet.$vocab["subject_mail_creation"];// - Nouvelle r�servation
        // L'utilisateur nom pr�nom (email)
        $message .= $vocab["the_user"].affiche_nom_prenom_email($user_login,"","formail");
        $message = $message.$vocab["creation_booking"]; // a r�serv�
        // la ressource "nom de la ressource" ("nom du domaine")
        $message=$message.$vocab["the_room"].$room_name." (".$area_name.") \n";
    } else if ($action == 2) {
        // Modification d'une r�servation
        $sujet = $sujet.$vocab["subject_mail_modify"];// - Modification d'une r�servation
        if ($moderate == 1) $sujet .= " (".$vocab["en_attente_moderation"].")";// (en attente de mod�ration)
        // L'utilisateur nom pr�nom (email)
        $message .= $vocab["the_user"].affiche_nom_prenom_email($user_login,"","formail");
        $message = $message.$vocab["modify_booking"];// a modifi� la r�servation de
        // la ressource "nom de la ressource" ("nom du domaine")
        $message=$message.$vocab["the_room"].$room_name." (".$area_name.") ";
    } else if ($action == 3) {
        // Suppression d'une r�servation
        $sujet = $sujet.$vocab["subject_mail_delete"];//  - Suppression d'une r�servation
        if ($moderate == 1) $sujet .= " (".$vocab["en_attente_moderation"].")";// (en attente de mod�ration)
        // L'utilisateur nom pr�nom (email)
        $message .= $vocab["the_user"].affiche_nom_prenom_email($user_login,"","formail");
        $message = $message.$vocab["delete_booking"]; // a supprim� la r�servation de
        // la ressource "nom de la ressource" ("nom du domaine")
        $message=$message.$vocab["the_room"].$room_name." (".$area_name.") \n";
    } else if ($action == 4) {
        // Suppression automatique
        $sujet = $sujet.$vocab["subject_mail_delete"]; // - Suppression d'une r�servation
        // Le d�lai de confirmation de r�servation a �t� d�pass�.\nSuppression automatique de la r�servation de
        $message = $message.$vocab["suppression_automatique"];
        // la ressource "nom de la ressource" ("nom du domaine")
        $message=$message.$vocab["the_room"].$room_name." (".$area_name.") \n";
    } else if ($action == 5) {
        // En attente de mod�ration
        $sujet = $sujet.$vocab["subject_mail_moderation"];// - R�servation en attente de mod�ration
        //La r�servation suivante est en attente de mod�ration pour
        $message = $message.$vocab["reservation_en_attente_de_moderation"];
        // la ressource "nom de la ressource" ("nom du domaine")
        $message=$message.$vocab["the_room"].$room_name." (".$area_name.") \n";
    } else if ($action == 6) {
        // D�cision de la mod�ration
        $sujet = $sujet.$vocab["subject_mail_decision_moderation"];// - Traitement d'une r�servation en attente de mod�ration
        // On r�cup�re les infos du traitement
        $resmoderate = grr_sql_query("select moderate, motivation_moderation from ".TABLE_PREFIX."_entry_moderate where id ='".protect_data_sql($id_entry)."'");
        if (! $resmoderate) fatal_error(0, grr_sql_error());
        if (grr_sql_count($resmoderate) < 1) fatal_error(0, get_vocab('invalid_entry_id'));
        $rowModerate = grr_sql_row($resmoderate, 0);
        grr_sql_free($resmoderate);
        $moderate_decision = $rowModerate[0];
        $moderate_description = $rowModerate[1];

        // L'utilisateur nom pr�nom (email)
        $message .= $vocab["the_user"].affiche_nom_prenom_email($user_login,"","formail");
        $message = $message.$vocab["traite_moderation"]; // a trait� la demande de r�servation de
        // la ressource "nom de la ressource" ("nom du domaine")
        $message=$message.$vocab["the_room"].$room_name." (".$area_name.") ";
        $message = $message.$vocab["reservee au nom de"];// reservee au nom de
        // L'utilisateur nom pr�nom (email)
        $message = $message.$vocab["the_user"].affiche_nom_prenom_email($beneficiaire,$beneficiaire_ext,"formail")." \n";

        if ($moderate_decision == 2)
            $message .= "\n".$vocab["moderation_acceptee"]; // Votre demande a �t� accept�e.
        else if ($moderate_decision == 3)
            $message .= "\n".$vocab["moderation_refusee"]; // Votre demande a �t� refus�e.
        if ($moderate_description != "") {
            $message .= "\n".$vocab["motif"].$vocab["deux_points"]; // Motif :
            $message .= $moderate_description." \n----";
        }
        $message .= "\n".$vocab["voir_details"].$vocab["deux_points"]."\n"; // Voir les d�tails :
        if (count($tab_id_moderes) == 0 )
            $message .= "\n".traite_grr_url("","y")."view_entry.php?id=".$id_entry;
        else {
            foreach($tab_id_moderes as $id_moderes) {
                $message .= "\n".traite_grr_url("","y")."view_entry.php?id=".$id_moderes;
            }
        }
        $message .= "\n\n".$vocab["rappel_de_la_demande"].$vocab["deux_points"]."\n"; // Rappel de la demande :
    // Notification d'un retard dans la restitution de la ressource
    } else if ($action == 7) {
        $sujet .= $vocab["subject_mail_retard"]; // - Urgent : Retard dans la restitution d'une ressource emprunt�e"
        // La r�servation suivante n'a pas �t� restitu�e
        $message .= $vocab["message_mail_retard"].$vocab["deux_points"]." \n";
        // la ressource "nom de la ressource" ("nom du domaine")
        $message .=$room_name." (".$area_name.") \n";
        // Nom de l'emprunteur
        $message .= $vocab["nom emprunteur"].$vocab["deux_points"];
        $message .= affiche_nom_prenom_email($beneficiaire,$beneficiaire_ext,"formail")." \n";
        if ($beneficiaire_email != "") $message .= $vocab["un email envoye"].$beneficiaire_email." \n";
        $message .= "\n".$vocab["changer statut lorsque ressource restituee"].$vocab["deux_points"];
        $message .= "\n".traite_grr_url("","y")."view_entry.php?id=".$id_entry." \n";

    }


    if (($action == 2) or ($action==3)) {
        $message = $message.$vocab["reservee au nom de"];// reservee au nom de
        // L'utilisateur nom pr�nom (email)
        $message = $message.$vocab["the_user"].affiche_nom_prenom_email($beneficiaire,$beneficiaire_ext,"formail")." \n";
    }

    if (($action == 5) or ($action == 7))
        $repondre = getSettingValue("webmaster_email");
    else
        $repondre = $user_email;

    //
    // Infos sur la r�servation
    //
    $reservation = '';
    $reservation = $reservation.$vocab["start_of_the_booking"]." ".$start_date."\n";
    $reservation = $reservation.$vocab["duration"]." ".$duration." ".$dur_units."\n";
    if (trim($breve_description) != "")
        $reservation = $reservation.$vocab["namebooker"].preg_replace("/&nbsp;/", " ",$vocab["deux_points"])." ".$breve_description."\n";
    else
        $reservation = $reservation.$vocab["entryid"].$room_id."\n";
    if ($description !='') {
        $reservation = $reservation.$vocab["description"]." ".$description."\n";
    }
    // Champ additionnels
    $reservation .= affichage_champ_add_mails($id_entry);

    #Type de r�servation
    $temp = grr_sql_query1("select type_name from ".TABLE_PREFIX."_type_area where type_letter='".$row[5]."'");
    if ($temp == -1) $temp = "?".$row[5]."?"; else $temp = removeMailUnicode($temp);
    $reservation = $reservation.$vocab["type"].preg_replace("/&nbsp;/", " ",$vocab["deux_points"])." ".$temp."\n";
    if($rep_type != 0) {
        $reservation = $reservation.$vocab["rep_type"]." ".$affiche_period."\n";
    }

    if($rep_type != 0)
    {
        // cas d'une periodicit� "une semaine sur n", on affiche les jours de p�riodicit�
        if ($rep_type == 2)
        {
            $opt = "";
            # Display day names according to language and preferred weekday start.
            for ($i = 0; $i < 7; $i++)
            {
                $daynum = ($i + $weekstarts) % 7;
                if ($rep_opt[$daynum]) $opt .= day_name($daynum) . " ";
            }
            if($opt)
                $reservation = $reservation.$vocab["rep_rep_day"]." ".$opt."\n";
        }
        // cas d'une periodicit� "Jour Cycle", on affiche le num�ro du jour cycle
        if ($rep_type == 6) {
            if (getSettingValue("jours_cycles_actif") == "Oui")
            $reservation = $reservation.$vocab["rep_type_6"].preg_replace("/&nbsp;/", " ",$vocab["deux_points"]).ucfirst(substr($vocab["rep_type_6"],0,1)).$jours_cycle."\n";
        }

        $reservation = $reservation.$vocab["rep_end_date"]." ".$rep_end_date."\n";

    }
    if (($delais_option_reservation > 0) and ($option_reservation != -1))
        $reservation = $reservation."*** ".$vocab["reservation_a_confirmer_au_plus_tard_le"]." ".time_date_string_jma($option_reservation,$dformat)." ***\n";


    $reservation = $reservation."-----\n";

    // message complet du message
    $message = $message.$reservation;
    // Si vous ne souhaitez plus recevoir ces messages automatiques, �crivez en ce sens au gestionnaire de Grr :
    $message = $message.$vocab["msg_no_email"].getSettingValue("webmaster_email");;
    $message = html_entity_decode_all_version($message);
    // Fin de l'�laboration du message destin� aux utilisateurs devant recevoir les mails automatiques
    //
    // maintenant, on envoie le message
    //
    // D�commenter la ligne suivante (et une ligne un peu plus bas) si on veut, pour une ressource mod�r�e, ne pas envoyer de mails tant que la r�sa n'est pas accept�e
    //if ((($action != 5) and ($action!=6)) or (($action==6) and ($moderate_decision==2))) {
    $sql = "SELECT u.email FROM ".TABLE_PREFIX."_utilisateurs u, ".TABLE_PREFIX."_j_mailuser_room j WHERE
    (j.id_room='".protect_data_sql($room_id)."' and u.login=j.login and u.etat='actif')  order by u.nom, u.prenom";
    $res = grr_sql_query($sql);
    $nombre = grr_sql_count($res);
    if ($nombre>0) {
        $tab_destinataire = array();
        for ($i = 0; ($row = grr_sql_row($res, $i)); $i++)
        {
          if ($row[0] != "") {
            $tab_destinataire[] = $row[0];
          }
        }
      foreach($tab_destinataire as $value) {
        if (getSettingValue("grr_mail_Bcc") == "y")
            $m->AddBCC( $value );
        else
            $m->AddAddress( $value );
      }
      $m->Subject = $sujet;
      $m->Body = $message;
      $m->AddReplyTo( $repondre );
      if(!$m->Send())
          $message_erreur .= $m->ErrorInfo;
    }
    $m->ClearAddresses();
    $m->ClearBCCs();
    $m->ClearReplyTos();
    // D�commenter la ligne suivante (voir �galement un peu plus haut) si on veut, pour une ressource mod�r�e, ne pas envoyer de mails tant que la r�sa n'est pas accept�e
    //}

    // Cas d'une notification de retard : on envoie le *** m�me message *** aus gestionnaires de la ressources
    // ou aux administrateurs du domaine

    if ($action == 7)  {
        $mail_admin = find_user_room ($room_id);
        if (count($mail_admin) > 0) {
        foreach($mail_admin as $value) {
            if (getSettingValue("grr_mail_Bcc") == "y")
                $m->AddBCC( $value );
            else
                $m->AddAddress( $value );
        }
        $m->Subject = $sujet;
        $m->Body = $message;
        $m->AddReplyTo( $repondre );
        if(!$m->Send())
            $message_erreur .= $m->ErrorInfo;
        }
        $m->ClearAddresses();
        $m->ClearBCCs();
        $m->ClearReplyTos();
    }

    // Cas d'une notification de retard
    // On envoie un message � l'emprunteur
    if ($action == 7)  {
        $sujet7 = $vocab["subject_mail1"].$room_name." - ".$date_avis;
        $sujet7 .= $vocab["subject_mail_retard"];
        $message7 = removeMailUnicode(getSettingValue("company"))." - ".$vocab["title_mail"];
        $message7 .= traite_grr_url("","y")."\n\n";
        // Sauf erreur, la ressource suivante que vous avez emprunt� n'a pas �t� restitu�e. S'il s'agit d'une erreur, veuillez ne pas tenir compte de ce courrier.
        $message7 .= $vocab["ressource empruntee non restitu�e"]."\n";
        $message7 .= $room_name." (".$area_name.")";
        $message7 .= "\n".$reservation;
        $message7 = html_entity_decode_all_version($message7);
        $destinataire7 = $beneficiaire_email;
        $repondre7 = getSettingValue("webmaster_email");
        $m->AddAddress( $destinataire7 );
        $m->Subject = $sujet7;
        $m->Body = $message7;
        $m->AddReplyTo( $repondre7 );
        if(!$m->Send())
            $message_erreur .= $m->ErrorInfo;
        $m->ClearAddresses();
        $m->ClearReplyTos();
    }

    // Cas d'une suppression automatique
    // On envoie un message � l'emprunteur
    if ($action == 4)  {
        $destinataire4 = $beneficiaire_email;
        $repondre4 = getSettingValue("webmaster_email");
        $m->AddAddress( $destinataire4 );
        $m->Subject = $sujet;
        $m->Body = $message;
        $m->AddReplyTo( $repondre4 );
        if(!$m->Send())
            $message_erreur .= $m->ErrorInfo;
        $m->ClearAddresses();
        $m->ClearReplyTos();
    }

    // Cas d'une moderation
    // On envoie un message au gestionnaires de la ressources ou aux administrateurs du domaine
    // pour pr�venir qu'une r�servation est en attente de mod�ration
    if ($action == 5)  {
        $mail_admin = find_user_room ($room_id);
        if (count($mail_admin) > 0) {
          foreach($mail_admin as $value) {
            if (getSettingValue("grr_mail_Bcc") == "y")
                $m->AddBCC( $value );
            else
                $m->AddAddress( $value );
          }
        $sujet5 = $vocab["subject_mail1"].$room_name." - ".$date_avis;
        $sujet5 .= $vocab["subject_mail_moderation"];// - R�servation en attente de mod�ration
        $message5 = removeMailUnicode(getSettingValue("company"))." - ".$vocab["title_mail"];
        $message5 .= traite_grr_url("","y")."\n\n";
        $message5 .= $vocab["subject_a_moderer"];
        $message5 .= "\n".traite_grr_url("","y")."view_entry.php?id=".$id_entry;
        $message5 .= "\n\n".$vocab['created_by'].affiche_nom_prenom_email($user_login,"","formail");
        $message5 .= "\n".$vocab['room'].$vocab['deux_points'].$room_name." (".$area_name.") \n";

        $message5 = html_entity_decode_all_version($message5);
        $repondre5 = getSettingValue("webmaster_email");
        $m->Subject = $sujet5;
        $m->Body = $message5;
        $m->AddReplyTo( $repondre5 );
        if(!$m->Send())
            $message_erreur .= $m->ErrorInfo;
        }
        $m->ClearAddresses();
        $m->ClearBCCs();
        $m->ClearReplyTos();
    }

    // Cas d'une moderation
    // On envoie un message au b�n�ficiaire de la r�servation pour l'avertir que sa demande est en attente de mod�ration
    //
    if (($action == 5) and ($beneficiaire_email!='') and ($beneficiaire_actif=='actif')) {
        $sujet5 = $vocab["subject_mail1"].$room_name." - ".$date_avis;
        $sujet5 .= $vocab["subject_mail_moderation"];
        $message5 = removeMailUnicode(getSettingValue("company"))." - ".$vocab["title_mail"];
        $message5 .= traite_grr_url("","y")."\n\n";
        $message5 .= $vocab["texte_en_attente_de_moderation"];
        $message5 .= "\n".$vocab["rappel_de_la_demande"].$vocab["deux_points"];
        $message5 .= "\n".$vocab["the_room"].$room_name." (".$area_name.")";
        $message5 .= "\n".$reservation;
        $message5 = html_entity_decode_all_version($message5);
        $destinataire5 = $beneficiaire_email;
        $repondre5 = getSettingValue("webmaster_email");
        $m->AddAddress( $destinataire5 );
        $m->Subject = $sujet5;
        $m->Body = $message5;
        $m->AddReplyTo( $repondre5 );
        if(!$m->Send())
            $message_erreur .= $m->ErrorInfo;
        $m->ClearAddresses();
        $m->ClearReplyTos();
    }

    // Cas d'une mod�ration
    // On envoie un message au b�n�ficiaire de la r�servation pour l'avertir de la d�sision d'une mod�ration
    //
    if (($action == 6) and ($beneficiaire_email!='') and ($beneficiaire_actif=='actif')) {
        // D�cision de la mod�ration
        $sujet6 = $vocab["subject_mail1"].$room_name." - ".$date_avis;
        $sujet6 .= $vocab["subject_mail_decision_moderation"];// - Traitement d'une r�servation en attente de mod�ration
        // Pour le message : on reprend le m�me que celui constitu� pour le pr�pos�s aux mails automatiques
        $message6 = $message;
        $destinataire6 = $beneficiaire_email;
        $repondre6 = $user_email;
        $m->AddAddress( $destinataire6 );
        $m->Subject = $sujet6;
        $m->Body = $message6;
        $m->AddReplyTo( $repondre6 );
        if(!$m->Send())
            $message_erreur .= $m->ErrorInfo;
        $m->ClearAddresses();
        $m->ClearReplyTos();
    }

    // Cas d'une cr�ation, modification ou suppression d'un message par un utilisateur diff�rent du b�n�ficiaire :
    // On envoie un message au b�n�ficiaire de la r�servation pour l'avertir d'une modif ou d'une suppression
    //
    if ((($action == 1) or ($action == 2) or ($action==3))  and   ((strtolower($user_login) != strtolower($beneficiaire)) or (getSettingValue('send_always_mail_to_creator')=='1')) and ($beneficiaire_email!='') and ($beneficiaire_actif=='actif')) {
        $sujet2 = $vocab["subject_mail1"].$room_name." - ".$date_avis;
        $message2 = removeMailUnicode(getSettingValue("company"))." - ".$vocab["title_mail"];
        $message2 = $message2.traite_grr_url("","y")."\n\n";
        $message2 = $message2.$vocab["the_user"].affiche_nom_prenom_email($user_login,"","formail");
        if ($action == 1) {
            $sujet2 = $sujet2.$vocab["subject_mail_creation"];
            $message2 = $message2.$vocab["creation_booking_for_you"];
            $message2=$message2.$vocab["the_room"].$room_name." (".$area_name.").";
        } else if ($action == 2) {
            $sujet2 = $sujet2.$vocab["subject_mail_modify"];
            $message2 = $message2.$vocab["modify_booking"];
            $message2=$message2.$vocab["the_room"].$room_name." (".$area_name.")";
            $message2 = $message2.$vocab["created_by_you"];
        } else {
            $sujet2 = $sujet2.$vocab["subject_mail_delete"];
            $message2 = $message2.$vocab["delete_booking"];
            $message2=$message2.$vocab["the_room"].$room_name." (".$area_name.")";
            $message2 = $message2.$vocab["created_by_you"];
        }
        $message2 = $message2."\n".$reservation;
        $message2 = html_entity_decode_all_version($message2);
        $destinataire2 = $beneficiaire_email;
        $repondre2 = $user_email;
        $m->AddAddress( $destinataire2 );
        $m->Subject = $sujet2;
        $m->Body = $message2;
        $m->AddReplyTo( $repondre2 );
        if(!$m->Send())
            $message_erreur .= $m->ErrorInfo;
        $m->ClearAddresses();
        $m->ClearReplyTos();
    }
    return $message_erreur;
}
function getUserName()
{
    if (isset($_SESSION['login']))
        return $_SESSION['login'];
    else
        return '';
}

/* getWritable($beneficiaire, $user, $id)
 *
 * Determines if a user is able to modify an entry
 *
 * $beneficiaire - The beneficiaire of the entry
 * $user    - Who wants to modify it
 * $id -   Which booking are we checking
 *
 * Returns:
 *   0        - The user does not have the required access
 *   1        - The user has the required access
 */
function getWritable($beneficiaire, $user, $id)
{
    $id_room = grr_sql_query1("SELECT room_id FROM ".TABLE_PREFIX."_entry WHERE id='".protect_data_sql($id)."'");
    // Modifications permises si l'utilisateur a les droits suffisants
    if (getSettingValue("allow_gestionnaire_modify_del") == 0)
        $temp = 3;
    else
        $temp = 2;
    if(authGetUserLevel($user,$id_room) > $temp)
        return 1;
    //if ($beneficiaire == "") $beneficiaire = $user;  // Dans le cas d'un b�n�ficiaire ext�rieure, $beneficiaire est vide. On fait comme si $user �tait le b�n�ficiaire

    $dont_allow_modify = grr_sql_query1("select dont_allow_modify from ".TABLE_PREFIX."_room where id = '".$id_room."'");
    $owner = strtolower(grr_sql_query1("SELECT create_by FROM ".TABLE_PREFIX."_entry WHERE id='".protect_data_sql($id)."'"));
    $beneficiaire = strtolower($beneficiaire);
    $user = strtolower($user);

    /* Il reste � �tudier le cas d'un utilisateur sans droits particuliers. quatre cas possibles :
    Cas 1 : l'utilisateur (U) n'est ni le cr�ateur (C) ni le b�n�ficiaire (B)
      R1 -> on retourne 0
    Cas 2 : U=B et et U<>C  ou ...
    Cas 3 : U=B et et U=C
      R2 -> on retourne 0 si personne hormis les gestionnaires et les administrateurs ne peut modifier ou supprimer ses propres r�servations.
      R3 -> on retourne 1 sinon
    Cas 4 : U=C et U<>B
      R4 -> on retourne 0 si personne hormis les gestionnaires et les administrateurs ne peut modifier ou supprimer ses propres r�servations.
      -> sinon
         R5 -> on retourne 1 si l'utilisateur U peut r�server la ressource pour B
         R6 -> on retourne 0 sinon (si on permettait � U d'�diter la r�sa, il ne pourrait de toute fa�on pas la modifier)
     */
    if (($user!=$beneficiaire) and ($user!=$owner)) // Cas 1
        return 0;
    else if ($user==$beneficiaire) // Cas 2 et Cas 3
        if ($dont_allow_modify == 'y')
            return 0; //  on applique R2
        else
            return 1; //  on applique R3
    else if ($user==$owner) // Cas 4 (pas la peine de pr�ciser que U est diff�rent de B)
        if ($dont_allow_modify == 'y')
            return 0; //  on applique R4
        else {
         $qui_peut_reserver_pour  = grr_sql_query1("select qui_peut_reserver_pour from ".TABLE_PREFIX."_room where id='".$id_room."'");
         if(authGetUserLevel($user, $id_room) >= $qui_peut_reserver_pour)
             return 1; //  on applique R5
         else
             return 0; //  on applique R6
        }
    else // A priori inutile car tous les cas ont �t� �puis�s.
        return 0;

    // Unathorised access
    return 0;
}

/* auth_visiteur($user,$id_room)
 *
 * Determine si un visiteur peut r�server une ressource
 *
 * $user - l'identifiant de l'utilisateur
 * $id_room -   l'identifiant de la ressource
 *
 * Retourne le niveau d'acc�s de l'utilisateur
 */
function auth_visiteur($user,$id_room)
{
    global $id_room_autorise;
    $level = "";
    // User not logged in, user level '0'
    if ((!isset($user)) or (!isset($id_room))) return 0;
    $res = grr_sql_query("SELECT statut FROM ".TABLE_PREFIX."_utilisateurs WHERE login ='".protect_data_sql($user)."'");
    if (!$res || grr_sql_count($res) == 0) return 0;
    $status = mysql_fetch_row($res);
    if (strtolower($status[0]) == 'visiteur') {
        if ((in_array($id_room,$id_room_autorise)) and ($id_room_autorise != ""))
            return 1;
        else
            return 0;
    } else return 0;

}

/* authGetUserLevel($user,$id,$type)
 *
 * Determine le niveau d'acc�s de l'utilisateur
 *
 * $user - l'identifiant de l'utilisateur
 * $id -   l'identifiant de la ressource ou du domaine
 * $type - argument optionnel : 'room' (par d�faut) si $id d�signe une ressource et 'area' si $id d�signe un domaine.
 *
 * Retourne le niveau d'acc�s de l'utilisateur
 */
function authGetUserLevel($user,$id,$type='room')
{
  // Initialise les variables
  $level = '';

  /**
  * user level '0': User not logged in, or User value is NULL (getUserName()='')
  */
  if(!isset($user) or ($user=='')) {
    return 0;
  }
  // On vient lire le statut de l'utilisateur courant dans la database
  $sql = "SELECT statut
    FROM ".TABLE_PREFIX."_utilisateurs
    WHERE login='".protect_data_sql($user)."' "."
    AND etat='actif'";
  $res = grr_sql_query($sql);
  $nbraw = grr_sql_count($res);
  /**
  * user level '0': User not defined in database
  */
  if (!$res || $nbraw == 0)
    return 0;
  // On vient lire le r�sultat de la requ�te
  $status = grr_sql_row($res,$nbraw-1);
  /**
  * user level '0': Same User defined multiple time in database !!!
  */
  if ($status === 0)
    return 0;

  // Teste si le type concerne la gestion des utilisateurs
  if ($type === 'user')
  {
    if (strtolower($status[0]) == 'gestionnaire_utilisateur')
      return 1;
    else
      return 0;
  }
  switch (strtolower($status[0]))
  {
    case 'visiteur':
      return 1;
    break;
    case 'administrateur':
      return 6;
    break;
    default:
    break;
  }
  if ((strtolower($status[0]) == 'utilisateur') or (strtolower($status[0]) == 'gestionnaire_utilisateur')) {
    if ($type == 'room')
    {
      // On regarde si l'utilisateur est administrateur du site auquel la ressource $id appartient

      // calcul de l'id du domaine
      $id_area = grr_sql_query1("SELECT area_id FROM ".TABLE_PREFIX."_room WHERE id='".protect_data_sql($id)."'");
      // calcul de l'id du site
      $id_site = grr_sql_query1("SELECT id_site FROM ".TABLE_PREFIX."_j_site_area  WHERE id_area='".protect_data_sql($id_area)."'");

      if (getSettingValue("module_multisite") == "Oui") {
        $res3 = grr_sql_query("SELECT login FROM ".TABLE_PREFIX."_j_useradmin_site j WHERE j.id_site='".protect_data_sql($id_site)."' AND j.login='".protect_data_sql($user)."'");
        if (grr_sql_count($res3) > 0) {
          grr_sql_free($res3);
          return 5;
        }
      }

      // On regarde si l'utilisateur est administrateur du domaine auquel la ressource $id appartient
      $res3 = grr_sql_query("SELECT u.login
            FROM ".TABLE_PREFIX."_utilisateurs u, ".TABLE_PREFIX."_j_useradmin_area j
                WHERE (u.login=j.login AND j.id_area='".protect_data_sql($id_area)."' AND u.login='".protect_data_sql($user)."')");
      if (grr_sql_count($res3) > 0)
        return 4;
      // On regarde si l'utilisateur est gestionnaire des r�servations pour une ressource
      $str_res2 = "SELECT *
          FROM ".TABLE_PREFIX."_utilisateurs u, ".TABLE_PREFIX."_j_user_room j
     WHERE u.login=j.login and u.login='".protect_data_sql($user)."' ";
      if ($id!=-1)
        $str_res2.="AND j.id_room='".protect_data_sql($id)."'";
      $res2 = grr_sql_query($str_res2);
      if (grr_sql_count($res2) > 0)
        return 3;
      // Sinon il s'agit d'un simple utilisateur
      return 2;
    }
    // On regarde si l'utilisateur est administrateur d'un domaine
    if ($type == 'area')
    {
      if ($id == '-1')
      {
       if (getSettingValue("module_multisite") == "Oui") {
        //On regarde si l'utilisateur est administrateur d'un site quelconque
        $res2 = grr_sql_query("SELECT u.login
              FROM ".TABLE_PREFIX."_utilisateurs u, ".TABLE_PREFIX."_j_useradmin_site j
                    WHERE (u.login=j.login and u.login='".protect_data_sql($user)."')");
        if (grr_sql_count($res2) > 0)
          return 5;
       }

        //On regarde si l'utilisateur est administrateur d'un domaine quelconque
        $res2 = grr_sql_query("SELECT u.login
              FROM ".TABLE_PREFIX."_utilisateurs u, ".TABLE_PREFIX."_j_useradmin_area j
                    WHERE (u.login=j.login and u.login='".protect_data_sql($user)."')");
        if (grr_sql_count($res2) > 0)
          return 4;
      }
      else
      {
        if (getSettingValue("module_multisite") == "Oui") {
         // On regarde si l'utilisateur est administrateur du site auquel le domaine $id appartient
         $id_site = grr_sql_query1("SELECT id_site FROM ".TABLE_PREFIX."_j_site_area  WHERE id_area='".protect_data_sql($id)."'");
         $res3 = grr_sql_query("SELECT login FROM ".TABLE_PREFIX."_j_useradmin_site j WHERE j.id_site='".protect_data_sql($id_site)."' AND j.login='".protect_data_sql($user)."'");
         if (grr_sql_count($res3) > 0)
         return 5;
        }
        //On regarde si l'utilisateur est administrateur du domaine dont l'id est $id
        $res3 = grr_sql_query("SELECT u.login
              FROM ".TABLE_PREFIX."_utilisateurs u, ".TABLE_PREFIX."_j_useradmin_area j
                    WHERE (u.login=j.login and j.id_area='".protect_data_sql($id)."' and u.login='".protect_data_sql($user)."')");
        if (grr_sql_count($res3) > 0)
          return 4;
      }
      // Sinon il s'agit d'un simple utilisateur
      return 2;
    }
    // On regarde si l'utilisateur est administrateur d'un site
    if (($type == 'site') and (getSettingValue("module_multisite") == "Oui"))
    {
      if ($id == '-1')
      {
        //On regarde si l'utilisateur est administrateur d'un site quelconque
        $res2 = grr_sql_query("SELECT u.login
              FROM ".TABLE_PREFIX."_utilisateurs u, ".TABLE_PREFIX."_j_useradmin_site j
                    WHERE (u.login=j.login and u.login='".protect_data_sql($user)."')");
        if (grr_sql_count($res2) > 0)
          return 5;
      }
      else
      {
        //On regarde si l'utilisateur est administrateur du domaine dont l'id est $id
        $res3 = grr_sql_query("SELECT u.login
              FROM ".TABLE_PREFIX."_utilisateurs u, ".TABLE_PREFIX."_j_useradmin_site j
                    WHERE (u.login=j.login and j.id_site='".protect_data_sql($id)."' and u.login='".protect_data_sql($user)."')");
        if (grr_sql_count($res3) > 0)
          return 5;
      }
      // Sinon il s'agit d'un simple utilisateur
      return 2;
    }
  }
}

/* authUserAccesArea($user,$id)
 *
 * Determines if the user access area
 *
 * $user - The user name
 * $id -   Which area are we checking
 *
 */
function authUserAccesArea($user,$id)
{
    if ($id=='') {
        return 0;
        die();
    }
    $sql = "SELECT login FROM ".TABLE_PREFIX."_utilisateurs WHERE (login = '".protect_data_sql($user)."' and statut='administrateur')";
    $res = grr_sql_query($sql);
    if (grr_sql_count($res) != "0") return 1;


    if (getSettingValue("module_multisite") == "Oui") {
        $id_site = mrbsGetAreaSite($id);
        $sql = "SELECT login FROM ".TABLE_PREFIX."_j_useradmin_site j WHERE j.id_site='".$id_site."' AND j.login='".protect_data_sql($user)."'";
        $res = grr_sql_query($sql);
        if (grr_sql_count($res) != "0") return 1;
    }



    $sql = "SELECT id FROM ".TABLE_PREFIX."_area WHERE (id = '".protect_data_sql($id)."' and access='r')";
    $res = grr_sql_query($sql);
    $test = grr_sql_count($res);
    if ($test == "0") {
        return 1;
    } else {
        $sql2 = "SELECT login FROM ".TABLE_PREFIX."_j_user_area WHERE (login = '".protect_data_sql($user)."' and id_area = '".protect_data_sql($id)."')";
        $res2 = grr_sql_query($sql2);
        $test2 = grr_sql_count($res2);
        if ($test2 != "0") {
            return 1;
        } else {
            return 0;
        }
    }
}
// function UserRoomMaxBooking
// Cette fonction teste si l'utilisateur a la possibilit� d'effectuer une r�servation, compte tenu
// des limitations �ventuelles de la ressources et du nombre de r�servations d�j� effectu�es.
//

function UserRoomMaxBooking($user, $id_room, $number) {
  global $enable_periods,$id_room_autorise;
  $level = authGetUserLevel($user,$id_room);
  if ($id_room == '') return 0;
  if($level >= 3) {
     // l'utilisateur est soit admin, soit gestionnaire de la ressource.
     return 1;
     exit();
  } else if (($level == 1 ) and  !((in_array($id_room,$id_room_autorise)) and ($id_room_autorise != ""))) {
     // l'utilisateur est simple visiteur.
     return 0;
     exit();
  } else if ($level  < 1 ) {
     return 0;
     exit();
  }
  // A ce niveau, l'utilisateur est simple utilisateur ou bien simple visiteur sur un domaine autoris�

  // On regarde si le nombre de r�servation de la ressource est limit�
  $max_booking_per_room = grr_sql_query1("SELECT max_booking FROM ".TABLE_PREFIX."_room WHERE id = '".protect_data_sql($id_room)."'");
  // Calcul de l'id de l'area de la ressource.
  $id_area = mrbsGetRoomArea($id_room);
  // On regarde si le nombre de r�servation du domaine est limit�
  $max_booking_per_area = grr_sql_query1("SELECT max_booking FROM ".TABLE_PREFIX."_area WHERE id = '".protect_data_sql($id_area)."'");
  // On regarde si le nombre de r�servation pour l'ensemble des ressources est limit�
  $max_booking = getSettingValue("UserAllRoomsMaxBooking");
  // Si aucune limitation
  if (($max_booking_per_room<0) and ($max_booking_per_area<0) and ($max_booking<0)) {
     return 1;
     exit();
  }
  // A ce niveau, il s'agit d'un utilisateur et il y a au moins une limitation
  $day   = date("d");
  $month = date("m");
  $year  = date("Y");
  $hour  = date("H");
  $minute = date("i");
  if ($enable_periods == 'y')
      $now = mktime(0, 0, 0, $month, $day, $year);
  else
      $now = mktime($hour, $minute, 0, $month, $day, $year);

  // y-a-t-il d�passement pour l'ensemble des ressources ?
  if ($max_booking > 0) {
     $nb_bookings = grr_sql_query1("SELECT count(id) FROM ".TABLE_PREFIX."_entry r WHERE (beneficiaire = '".protect_data_sql($user)."' and end_time > '$now')");
     $nb_bookings += $number;
     if ($nb_bookings > $max_booking) {
        return 0;
        exit();
     }
  } else if ($max_booking == 0) {
      return 0;
      exit();
  }

  // y-a-t-il d�passement pour l'ensemble des ressources du domaine ?
  if ($max_booking_per_area > 0) {
     $nb_bookings = grr_sql_query1("SELECT count(e.id) FROM ".TABLE_PREFIX."_entry e, ".TABLE_PREFIX."_room r WHERE (e.room_id=r.id and r.area_id='".$id_area."' and e.beneficiaire = '".protect_data_sql($user)."' and e.end_time > '$now')");
     $nb_bookings += $number;
     if ($nb_bookings > $max_booking_per_area) {
        return 0;
        exit();
     }
  } else if ($max_booking_per_area == 0) {
      return 0;
      exit();
  }

  // y-a-t-il d�passement pour la ressource
  if ($max_booking_per_room > 0) {
     $nb_bookings = grr_sql_query1("SELECT count(id) FROM ".TABLE_PREFIX."_entry WHERE (room_id = '".protect_data_sql($id_room)."' and beneficiaire = '".protect_data_sql($user)."' and end_time > '$now')");
     $nb_bookings += $number;
     if ($nb_bookings > $max_booking_per_room) {
        return 0;
        exit();
     }
  } else if ($max_booking_per_room == 0) {
      return 0;
      exit();
  }

  // A ce stade, il s'agit d'un utilisateu et il n'y a pas eu de d�passement, ni pour l'ensemble des domaines, ni pour le domaine, ni pour la ressource
  return 1;
}

/* function verif_booking_date($user, $id, $date_booking, $date_now)
 $user : le login de l'utilisateur
 $id : l'id de la r�sa. Si -1, il s'agit d'une nouvelle r�servation
 $id_room : id de la ressource
 $date_booking : la date de la r�servation (n'est utile que si $id=-1)
 $date_now : la date actuelle
*/
function verif_booking_date($user, $id, $id_room, $date_booking, $date_now, $enable_periods, $endtime='') {
  global $correct_diff_time_local_serveur, $can_delete_or_create;
  $can_delete_or_create="y";
  // On teste si l'utilisateur est administrateur
  $sql = "select statut from ".TABLE_PREFIX."_utilisateurs WHERE login = '".protect_data_sql($user)."'";
  $statut_user = grr_sql_query1($sql);
  if ($statut_user == 'administrateur') {
    return true;
    die();
  }
  // A-t-on le droit d'agir dans le pass� ?
  $allow_action_in_past = grr_sql_query1("select allow_action_in_past from ".TABLE_PREFIX."_room where id = '".protect_data_sql($id_room)."'");
  if ($allow_action_in_past == 'y') {
    return true;
    die();
  }
  // Correction de l'avance en nombre d'heure du serveur sur les postes clients
  if ((isset($correct_diff_time_local_serveur)) and ($correct_diff_time_local_serveur!=0))
      $date_now -= 3600*$correct_diff_time_local_serveur;

  // Cr�neaux bas�s sur les intitul�s
  // Dans ce cas, on prend comme temps pr�sent le jour m�me � minuit.
  // Cela signifie qu'il est possible de modifier/r�server/supprimer tout au long d'une journ�e
  // m�me si l'heure est pass�e.
  // Cela demande donc � �tre am�liorer en introduisant pour chaque cr�neau une heure limite de r�servation.
  if ($enable_periods == "y") {
      $month =  date("m",$date_now);
      $day =  date("d",$date_now);
      $year = date("Y",$date_now);
      $date_now = mktime(0, 0, 0, $month, $day, $year);
  }
  if ($id != -1) {
    // il s'agit de l'edition d'une r�servation existante
    if (($endtime != '') and ($endtime < $date_now)) {
      return false;
      die();
    }
    if ((getSettingValue("allow_user_delete_after_begin") == 1) or (getSettingValue("allow_user_delete_after_begin") == 2))
        $sql = "SELECT end_time FROM ".TABLE_PREFIX."_entry WHERE id = '".protect_data_sql($id)."'";
    else
        $sql = "SELECT start_time FROM ".TABLE_PREFIX."_entry WHERE id = '".protect_data_sql($id)."'";
    $date_booking = grr_sql_query1($sql);
    if ($date_booking < $date_now) {
      return false;
      die();
    } else {
      // dans le cas o� le cr�neau est entam�, on teste si l'utilisateur a le droit de supprimer la r�servation
      // Si oui, on transmet la variable $only_modify = TRUE avant que la fonction de retourne true.
      if (getSettingValue("allow_user_delete_after_begin") == 2) {
          $date_debut = grr_sql_query1("SELECT start_time FROM ".TABLE_PREFIX."_entry WHERE id = '".protect_data_sql($id)."'");
          if ($date_debut < $date_now) $can_delete_or_create = "n"; else $can_delete_or_create = "y";
      }
      return true;
    }

  } else {
    if (getSettingValue("allow_user_delete_after_begin")==1) {
        $id_area = grr_sql_query1("select area_id from ".TABLE_PREFIX."_room where id = '".protect_data_sql($id_room)."'");
        $resolution_area = grr_sql_query1("select resolution_area from ".TABLE_PREFIX."_area where id = '".$id_area."'");
        if ($date_booking>$date_now-$resolution_area) {
          return true;
        } else {
         return false;
        }
    } else {
        if ($date_booking>$date_now) {
          return true;
        } else {
         return false;
        }
    }

  }
}

// function verif_duree_max_resa_area($user, $id_room, $starttime, $endtime)
// $user : le login de l'utilisateur
// $id_room : l'id de la ressource. Si -1, il s'agit d'une nouvelle ressource.
// $starttime : d�but de la r�servation
// $endtime : fin de la r�servation
//
function verif_duree_max_resa_area($user, $id_room, $starttime, $endtime) {
  if(authGetUserLevel($user,$id_room) >= 3) {
  // On teste si l'utilisateur est gestionnaire de la ressource
    return true;
    die();
  }
  $id_area = grr_sql_query1("select area_id from ".TABLE_PREFIX."_room where id='".protect_data_sql($id_room)."'");
  $duree_max_resa_area = grr_sql_query1("select duree_max_resa_area from ".TABLE_PREFIX."_area where id='".$id_area."'");
  $enable_periods =  grr_sql_query1("select enable_periods from ".TABLE_PREFIX."_area where id='".$id_area."'");
  if ($enable_periods == 'y') $duree_max_resa_area = $duree_max_resa_area*24*60;
  if ($duree_max_resa_area < 0) {
    return true;
    die();
  } else if ($endtime - $starttime > $duree_max_resa_area*60) {
    return false;
    die();
  } else {
    return true;
    die();
  }
}

// function verif_delais_max_resa_room($user, $id_room, $date_booking)
// $user : le login de l'utilisateur
// $id_room : l'id de la ressource. Si -1, il s'agit d'une nouvelle ressoure
// $date_booking : la date de la r�servation (n'est utile que si $id=-1)
// $date_now : la date actuelle
//
function verif_delais_max_resa_room($user, $id_room, $date_booking) {
    $day   = date("d");
    $month = date("m");
    $year  = date("Y");
    $datenow = mktime(0,0,0,$month,$day,$year);

  if(authGetUserLevel($user,$id_room) >= 3) {
  // On teste si l'utilisateur est administrateur
    return true;
    die();
  }
  $delais_max_resa_room = grr_sql_query1("select delais_max_resa_room from ".TABLE_PREFIX."_room where id='".protect_data_sql($id_room)."'");
  if ($delais_max_resa_room == -1) {
    return true;
    die();
  } else if ($datenow + $delais_max_resa_room*24*3600 +1 < $date_booking) {
    return false;
    die();
  } else {
    return true;
    die();
  }
}

// function verif_access_search : v�rifier l'acc�s � l'outil de recherche
// $user : le login de l'utilisateur
// $id_room : l'id de la ressource.
//
function verif_access_search($user) {
    if(authGetUserLevel($user,-1) >= getSettingValue("allow_search_level"))
        return TRUE;
    else
        return FALSE;
}

// function verif_display_fiche_ressource : v�rifier l'acc�s � la visualisation de la fiche d'une ressource
// $user : le login de l'utilisateur
// $id_room : l'id de la ressource.
//
function verif_display_fiche_ressource($user, $id_room) {
    $show_fic_room = grr_sql_query1("select show_fic_room from ".TABLE_PREFIX."_room where id='".$id_room."'");
    if ($show_fic_room == "y") {
        if(authGetUserLevel($user,$id_room) >= getSettingValue("visu_fiche_description"))
            return TRUE;
        else
            return FALSE;
    } else {
        return FALSE;
    }
}

// function verif_acces_fiche_reservation : v�rifier l'acc�s � la fiche de r�servation d'une ressource
// $user : le login de l'utilisateur
// $id_room : l'id de la ressource.
//
function verif_acces_fiche_reservation($user, $id_room) {
    if(authGetUserLevel($user,$id_room) >= getSettingValue("acces_fiche_reservation"))
        return TRUE;
    else
        return FALSE;
}

/* function verif_display_email : v�rifier l'acc�s � l'adresse email
 *$user : le login de l'utilisateur
 * $id_room : l'id de la ressource.
 */
function verif_display_email($user, $id_room) {
    if(authGetUserLevel($user,$id_room) >= getSettingValue("display_level_email"))
        return TRUE;
    else
        return FALSE;
}

/* function verif_acces_ressource : v�rifier l'acc�s � la ressource
 *$user : le login de l'utilisateur
 * $id_room : l'id de la ressource.
 */
function verif_acces_ressource($user, $id_room) {
  if ($id_room != 'all') {
    $who_can_see = grr_sql_query1("select who_can_see from ".TABLE_PREFIX."_room where id='".$id_room."'");
    if(authGetUserLevel($user,$id_room) >= $who_can_see)
        return TRUE;
    else
        return FALSE;
  } else {
    $tab_rooms_noaccess = array();
    $sql = "select id, who_can_see from ".TABLE_PREFIX."_room";
    $res = grr_sql_query($sql);
    if (! $res) fatal_error(0, grr_sql_error());
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
      if(authGetUserLevel($user,$row[0]) < $row[1])
          $tab_rooms_noaccess[] = $row[0];
    }
    return $tab_rooms_noaccess;
  }
}




// function verif_delais_min_resa_room($user, $id_room, $date_booking)
// $user : le login de l'utilisateur
// $id_room : l'id de la ressource. Si -1, il s'agit d'une nouvelle ressoure
// $date_booking : la date de la r�servation (n'est utile que si $id=-1)
// $date_now : la date actuelle
//
function verif_delais_min_resa_room($user, $id_room, $date_booking) {
  if(authGetUserLevel($user,$id_room) >= 3) {
  // On teste si l'utilisateur est administrateur
    return true;
    die();
  }
  $delais_min_resa_room = grr_sql_query1("select delais_min_resa_room from ".TABLE_PREFIX."_room where id='".protect_data_sql($id_room)."'");
  if ($delais_min_resa_room == 0) {
    return true;
    die();
  } else {
    $hour = date("H");
    $minute  = date("i")+$delais_min_resa_room;
    $day   = date("d");
    $month = date("m");
    $year  = date("Y");
    $date_limite = mktime($hour,$minute,0,$month,$day,$year);
    if ($date_limite > $date_booking) {
        return false;
        die();
    } else {
        return true;
        die();
    }
  }
}

// V�rifie que la date de confirmation est inf�rieur � la date de d�but de r�servation
function verif_date_option_reservation($option_reservation, $starttime) {
    if ($option_reservation == -1)
        return true;
    else {
        $day   = date("d",$starttime);
        $month = date("m",$starttime);
        $year  = date("Y",$starttime);
        $date_starttime = mktime(0,0,0,$month,$day,$year);
        if ($option_reservation < $date_starttime)
            return true;
        else
            return false;
    }
}

// V�rifie que $_create_by peut r�server la ressource $_room_id pour $_beneficiaire
function verif_qui_peut_reserver_pour($_room_id, $user, $_beneficiaire) {
    if ($_beneficiaire == "") {
    // cas o� il s'agit d'un b�n�ficiaire ext�rieure : c'est normal que $_beneficiaire soir vide
        return TRUE;
        die();
    }
    if (strtolower($user) == strtolower($_beneficiaire)) {
        return TRUE;
        die();
    }
    $qui_peut_reserver_pour  = grr_sql_query1("select qui_peut_reserver_pour from ".TABLE_PREFIX."_room where id='".$_room_id."'");
    if(authGetUserLevel($user, $_room_id) >= $qui_peut_reserver_pour) {
        return TRUE;
        die();
    } else {
        return FALSE;
        die();
    }
}
/*
function verif_heure_debut_fin($start_time,$end_time,$area)
V�rifie si l'heure de d�but ou l'heure de fin de r�servation est en dehors des cr�neaux autoris�s.
*/
function verif_heure_debut_fin($start_time,$end_time,$area) {
    global $enable_periods, $resolution, $morningstarts, $eveningends, $eveningends_minutes;
    // R�cup�ration des donn�es concernant l'affichage du planning du domaine
    get_planning_area_values($area);
    // On ne traite pas le cas des plannings bas�s sur les intitul�s pr�d�finis
    if ($enable_periods != "y") {
        $day = date("d",$start_time);
        $month = date("m",$start_time);
        $year = date("Y",$start_time);
        $startday = mktime($morningstarts, 0, 0, $month, $day  , $year);
        $day = date("d",$end_time);
        $month = date("m",$end_time);
        $year = date("Y",$end_time);
        $endday   = mktime($eveningends, $eveningends_minutes , $resolution, $month, $day, $year);
        if ($start_time < $startday ) {
            return FALSE;
        } else if ($end_time > $endday ) {
            return FALSE;
        }
    }
    return TRUE;
}



/* VerifyModeDemo()
 *
 * Affiche une page "op�ration non autoris�e" pour certaines op�rations dans le cas le mode demo est activ�.
 *
 * Returns: Nothing
 */
function VerifyModeDemo() {
    if (getSettingValue("ActiveModeDemo")=='y') {
      print_header("","","","","");
      ?>
      <h1>Op&eacute;ration non autoris&eacute;e</h1>
      <p>Vous �tes dans une <b>version de d�monstration de GRR</b>.
      <br />Certaines fonctions ont �t� volontairement brid�es. C'est le cas pour l'op�ration que vous avez tent� d'effectuer.</p>
      </body></html>
      <?php
      die();
    }
}

/* MajMysqlModeDemo()
 * dans le cas le mode demo est activ� :
 * Met � jour la base mysql une fois par jour, lors de la premi�re connexion
 *
 */
function MajMysqlModeDemo() {
    // Nom du fichier sql � ex�cuter
    $fic_sql = "grr_maj_quotidienne.sql";
    if ((getSettingValue("ActiveModeDemo")=='y') and (file_exists($fic_sql))) {
        $date_now = mktime(0,0,0,date("m"),date("d"),date("Y"));
        if ((getSettingValue("date_verify_demo") == "") or (getSettingValue("date_verify_demo") < $date_now )) {
            $fd = fopen($fic_sql, "r");
            $result_ok = 'yes';
            while (!feof($fd)) {
                $query = fgets($fd, 5000);
                $query = trim($query);
                if ($query != '') {
                   $reg = mysql_query($query);
                }
            }
            fclose($fd);
            if (!saveSetting("date_verify_demo", $date_now)) {
                echo "Erreur lors de l'enregistrement de date_verify_demo !<br />";
                die();
            }
        }
    }
}

/* showAccessDenied()
 *
 * Displays an appropate message when access has been denied
 *
 * Returns: Nothing
 */
function showAccessDenied($day, $month, $year, $area, $back)
{
    global $vocab;
    if ((getSettingValue("authentification_obli")==0) and (getUserName()=='')) {
        $type_session = "no_session";
    } else {
        $type_session = "with_session";
    }
    print_header($day, $month, $year, $area,$type_session);
    ?>
   <h1><?php echo get_vocab("accessdenied")?></h1>
   <p>
   <?php echo get_vocab("norights")?>
   </p>
   <p>
   <a href="<?php echo $back; ?>"><?php echo get_vocab("returnprev"); ?></a>
   </p>
</body>
</html>
<?php
}

/* showNoReservation()
 *
 * Displays an appropate message when access has been denied
 *
 * Returns: Nothing
 */
function showNoReservation($day, $month, $year, $area, $back)
{
    global $vocab;
    if ((getSettingValue("authentification_obli")==0) and (getUserName()=='')) {
        $type_session = "no_session";
    } else {
        $type_session = "with_session";
    }
    print_header($day, $month, $year, $area,$type_session);
    ?>
   <h1><?php echo get_vocab("accessdenied")?></h1>
   <p>
   <?php echo get_vocab("noreservation")?>
   </p>
   <p>
   <a href="<?php echo $back; ?>"><?php echo get_vocab("returnprev"); ?></a>
   </p>
</body>
</html>
<?php
}


/* showAccessDeniedMaxBookings()
 *
 * Displays an appropate message when access has been denied
 *
 * Returns: Nothing
 */
function showAccessDeniedMaxBookings($day, $month, $year, $area, $id_room,$back)
{
    global $vocab;

    print_header($day, $month, $year, $area);
    ?>
    <h1><?php echo get_vocab("accessdenied")?></h1>
    <p>
    <?php
    // Limitation par ressource
    $max_booking_per_room = grr_sql_query1("SELECT max_booking FROM ".TABLE_PREFIX."_room WHERE id='".protect_data_sql($id_room)."'");
    if ($max_booking_per_room >= 0)
        echo get_vocab("msg_max_booking").get_vocab("deux_points").$max_booking_per_room."<br />";
    // Calcul de l'id de l'area de la ressource.
    $id_area = mrbsGetRoomArea($id_room);
    // Limitation par domaine
    $max_booking_per_area = grr_sql_query1("SELECT max_booking FROM ".TABLE_PREFIX."_area WHERE id = '".protect_data_sql($id_area)."'");
    if ($max_booking_per_area >= 0)
        echo get_vocab("msg_max_booking_area").get_vocab("deux_points").$max_booking_per_area."<br />";
    // Limitation sur l'ensemble des ressources
    $max_booking_all = getSettingValue("UserAllRoomsMaxBooking");
    if ($max_booking_all >= 0)
        echo get_vocab("msg_max_booking_all").get_vocab("deux_points").$max_booking_all."<br />";

    echo "<br />".get_vocab("accessdeniedtoomanybooking");

    ?>
    </p>
    <p>
    <a href="<?php echo $back; ?>"><?php echo get_vocab("returnprev"); ?></a>
    </p>
    </body>
    </html>
    <?php
}


function check_begin_end_bookings($day, $month, $year) {
    $date = mktime(0,0,0,$month,$day,$year);
    if (($date < getSettingValue("begin_bookings")) or ($date > getSettingValue("end_bookings")))
    return -1;
}
function showNoBookings($day, $month, $year, $area, $back, $type_session)
{
 global $vocab;
 print_header($day, $month, $year, $area,$type_session);
 $date = mktime(0,0,0,$month,$day,$year);
 echo '<h2>'.get_vocab("nobookings").' '.affiche_date($date).'</h2>';
 echo '<p>'.get_vocab("begin_bookings").'<b>'.affiche_date(getSettingValue("begin_bookings")).'</b></p>';
 echo '<p>'.get_vocab("end_bookings").'<b>'.affiche_date(getSettingValue("end_bookings")).'</b></p>';
 ?>
 <p>
  <?php if ($back != "") { ?>
   <a href="<?php echo $back; ?>"><?php echo get_vocab("returnprev"); ?></a>
   <?php }
  ?>
  </p>
 </body>
</html>
<?php
}

function date_time_string($t,$dformat)
{
    global $twentyfourhour_format;
    if ($twentyfourhour_format)
                $timeformat = "%T";
    else
    {
                $ampm = date("a",$t);
                $timeformat = "%I:%M$ampm";
    }
    return utf8_strftime($dformat.$timeformat, $t);
}

# Convert a start period and end period to a plain language description.
# This is similar but different from the way it is done in view_entry.
function describe_period_span($starts, $ends)
{
    global $enable_periods, $periods_name, $vocab, $duration;
    list( $start_period, $start_date) =  period_date_string($starts);
    list( , $end_date) =  period_date_string($ends, -1);
    $duration = $ends - $starts;
    toPeriodString($start_period, $duration, $dur_units);
    if ($duration > 1) {
        list( , $start_date) =  period_date_string($starts);
        list( , $end_date) =  period_date_string($ends, -1);
        $temp = $start_date . " ==> " . $end_date;
    } else {
        $temp = $start_date . " - " . $duration . " " . $dur_units;
    }
    return $temp;
}



#Convertit l'heure de d�but et de fin en p�riode.
function describe_span($starts, $ends, $dformat)
{
    global $vocab, $twentyfourhour_format;
    $start_date = utf8_strftime($dformat, $starts);
        if ($twentyfourhour_format)
    {
                $timeformat = "%T";
    }
    else
    {
                $ampm = date("a",$starts);
                $timeformat = "%I:%M$ampm";
    }
    $start_time = strftime($timeformat, $starts);
    $duration = $ends - $starts;
    if ($start_time == "00:00:00" && $duration == 60*60*24)
        return $start_date . " - " . get_vocab("all_day");
    toTimeString($duration, $dur_units);
    return $start_date . " " . $start_time . " - " . $duration . " " . $dur_units;
}

function get_planning_area_values($id_area) {
    global $resolution, $morningstarts, $eveningends, $eveningends_minutes, $weekstarts, $twentyfourhour_format, $enable_periods, $periods_name, $display_day, $nb_display_day;
    $sql = "SELECT calendar_default_values, resolution_area, morningstarts_area, eveningends_area, eveningends_minutes_area, weekstarts_area, twentyfourhour_format_area, enable_periods, display_days
    FROM ".TABLE_PREFIX."_area
    WHERE id = '".protect_data_sql($id_area)."'";
    $res = grr_sql_query($sql);
    if (! $res) {
    //    fatal_error(0, grr_sql_error());
        include "trailer.inc.php";
        exit;
    }
    $row_ = grr_sql_row($res, 0);

    $nb_display_day = 0;
    for ($i = 0; $i < 7; $i++)
    {
      if (substr($row_[8],$i,1) == 'y') {
          $display_day[$i] = 1;
          $nb_display_day++;
      } else
          $display_day[$i] = 0;
    }


    // Cr�neaux bas�s sur les intitul�s
    if ($row_[7] == 'y') {
        $resolution = 60;
        $morningstarts = 12;
        $eveningends = 12;
        $sql_periode = grr_sql_query("SELECT nom_periode FROM ".TABLE_PREFIX."_area_periodes where id_area='".$id_area."'");
        $eveningends_minutes = grr_sql_count($sql_periode)-1;
        $i = 0;
        while ($i < grr_sql_count($sql_periode)) {
            $periods_name[$i] = grr_sql_query1("select nom_periode FROM ".TABLE_PREFIX."_area_periodes where id_area='".$id_area."' and num_periode= '".$i."'");
            $i++;
        }
        $enable_periods = "y";
        $weekstarts = $row_[5];
        $twentyfourhour_format = $row_[6];
    // Cr�neaux bas�s sur le temps
    } else {
        if ($row_[0] != 'y') {
            $resolution = $row_[1];
            $morningstarts = $row_[2];
            $eveningends = $row_[3];
            $eveningends_minutes = $row_[4];
            $enable_periods = "n";
            $weekstarts = $row_[5];
            $twentyfourhour_format = $row_[6];
        }
    }
}

// Dans le cas ou $unicode_encoding = 1 (UTF-8) cette fonction encode les cha�nes pr�sentes dans
// le code "en dur", en UTF-8 avant affichage
function encode_message_utf8($tag)
{
  global $charset_html, $unicode_encoding;

  if ($unicode_encoding)
  {
    return iconv($charset_html,"utf-8",$tag);
  }
  else
  {
    return $tag;
  }
}

function removeMailUnicode($string)
{
    global $unicode_encoding, $charset_html;
    //
    if ($unicode_encoding)
    {
        return @iconv("utf-8", $charset_html, $string);
    }
    else
    {
        return $string;
    }
}

// Cette fonction v�rifie une fois par jour si le d�lai de confirmation des r�servations est d�pass�
// Si oui, les r�servations concern�es sont supprim�es et un mail automatique est envoy�.
function verify_confirm_reservation() {
    global $dformat;
    $day   = date("d");
    $month = date("m");
    $year  = date("Y");
    $date_now = mktime(0,0,0,$month,$day,$year);
    if ((getSettingValue("date_verify_reservation") == "") or (getSettingValue("date_verify_reservation") < $date_now )) {
        $res = grr_sql_query("select id from ".TABLE_PREFIX."_room where delais_option_reservation > 0");
        if (! $res) {
            //    fatal_error(0, grr_sql_error());
            include "trailer.inc.php";
            exit;
        } else {
            for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
                $res2 = grr_sql_query("select id from ".TABLE_PREFIX."_entry where option_reservation < '".$date_now."' and option_reservation != '-1' and room_id='".$row[0]."'");
                if (! $res2) {
                    //    fatal_error(0, grr_sql_error());
                    include "trailer.inc.php";
                    exit;
                } else {
                    for ($j = 0; ($row2 = grr_sql_row($res2, $j)); $j++) {
                        if (getSettingValue("automatic_mail") == 'yes') $_SESSION['session_message_error'] = send_mail($row2[0],4,$dformat);
                        // On efface la r�servation
                        grr_sql_command("DELETE FROM ".TABLE_PREFIX."_entry WHERE id=" . $row2[0]);
                        // On efface le cas �cheant �galement  dans ".TABLE_PREFIX."_entry_moderate
                        grr_sql_command("DELETE FROM ".TABLE_PREFIX."_entry_moderate WHERE id=" . $row2[0]);

                     }
                }
            }
        }
        if (!saveSetting("date_verify_reservation", $date_now)) {
            echo "Erreur lors de l'enregistrement de date_verify_reservation !<br />";
            die();
        }
    }
}
// Cette fonction v�rifie une fois par jour si les r�servations devant �tre rendus ne sont pas
// en retard
// Si oui, les utilisateurs concern�es recoivent un mail automatique pour leur notifier.
function verify_retard_reservation() {
    global $dformat;
    $day   = date("d");
    $month = date("m");
    $year  = date("Y");
    $date_now = mktime(0,0,0,$month,$day,$year);
    if (((getSettingValue("date_verify_reservation2") == "") or (getSettingValue("date_verify_reservation2") < $date_now )) and (getSettingValue("automatic_mail") == 'yes')) {
        //$res = grr_sql_query("SELECT r.id FROM ".TABLE_PREFIX."_room r, ".TABLE_PREFIX."_area a WHERE a.retour_resa_obli = 1 AND r.area_id = a.id");
        $res = grr_sql_query("SELECT id FROM ".TABLE_PREFIX."_room");
        if (! $res) {
                // fatal_error(0, grr_sql_error());
            include "trailer.inc.php";
            exit;
        } else {
             for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
                $res2 = grr_sql_query("select id from ".TABLE_PREFIX."_entry where statut_entry='e' and end_time < '".$date_now."' and room_id='".$row[0]."'");
                if (! $res2) {
                       // fatal_error(0, grr_sql_error());
                    include "trailer.inc.php";
                    exit;
                } else {
                    for ($j = 0; ($row2 = grr_sql_row($res2, $j)); $j++) {
                         $_SESSION['session_message_error'] = send_mail($row2[0],7,$dformat);
                    }
                }
            }
        }
        if (!saveSetting("date_verify_reservation2", $date_now)) {
            echo "Erreur lors de l'enregistrement de date_verify_reservation2 !<br />";
            die();
        }
    }
}



function est_hors_reservation($time,$area="-1") {
    // Premier test : s'agit-il d'un jour du calendrier "hors r�servation" ?
    $test = grr_sql_query1("select DAY from ".TABLE_PREFIX."_calendar where DAY = '".$time."'");
    if ($test != -1)
        return TRUE;
    // 2�me test : s'agit-il d'une journ�e qui n'est pas affich�e pour le domaine consid�r� ?
    if ($area!=-1) {
        $sql = "SELECT display_days FROM ".TABLE_PREFIX."_area WHERE id = '".protect_data_sql($area)."'";
        $result = grr_sql_query1($sql);
        $jour_semaine = date("w",$time);
        if (substr($result,$jour_semaine,1) == 'n')
           return TRUE;
    }
    return FALSE;
}

function resa_est_hors_reservation($start_time,$end_time) {
    // On teste si la r�servation est dans le calendrier "hors r�servations"
    $test = grr_sql_query1("select DAY from ".TABLE_PREFIX."_calendar where DAY = '".$start_time."' or DAY = '".$end_time."'");
    if ($test != -1)
        return TRUE;
    else
        return FALSE;
}

function resa_est_hors_reservation2($start_time,$end_time,$area) {
    // S'agit-il d'une journ�e qui n'est pas affich�e pour le domaine consid�r� ?
    $sql = "SELECT display_days FROM ".TABLE_PREFIX."_area WHERE id = '".protect_data_sql($area)."'";
    $result = grr_sql_query1($sql);
    $jour_semaine = date("w",$start_time);
    if (substr($result,$jour_semaine,1) == 'n')
           return TRUE;
    $jour_semaine = date("w",$end_time);
    if (substr($result,$jour_semaine,1) == 'n')
           return TRUE;
    return FALSE;
}


// trouve les utilisateurs gestionnaires de ressource
function find_user_room ($id_room)
{
    $emails = array ();
    $sql = "select email from ".TABLE_PREFIX."_utilisateurs, ".TABLE_PREFIX."_j_user_room
    where ".TABLE_PREFIX."_utilisateurs.login = ".TABLE_PREFIX."_j_user_room.login and id_room='".$id_room."'";
    $res = grr_sql_query($sql);
    if ($res) {
        for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
            if (validate_email($row[0])) $emails[] = $row[0];
        }
    }
    // Si la table des emails des gestionnaires de la ressource est vide, on avertit les administrateurs du domaine
    if (count($emails) == 0) {
        $id_area = mrbsGetAreaIdFromRoomId($id_room);
        $sql_admin = grr_sql_query("select email from ".TABLE_PREFIX."_utilisateurs, ".TABLE_PREFIX."_j_useradmin_area
        where ".TABLE_PREFIX."_utilisateurs.login = ".TABLE_PREFIX."_j_useradmin_area.login and ".TABLE_PREFIX."_j_useradmin_area.id_area='".$id_area."'");
        if ($sql_admin) {
            for ($i = 0; ($row = grr_sql_row($sql_admin, $i)); $i++) {
                if (validate_email($row[0])) $emails[] = $row[0];
            }
        }
    }
    // Si la table des emails des administrateurs du domaines est vide, on avertit les administrateurs des sites
    if (getSettingValue("module_multisite") == "Oui") {
     if (count($emails) == 0) {
        $id_area = mrbsGetAreaIdFromRoomId($id_room);
        $id_site = mrbsGetAreaSite($id_area);
        $sql_admin = grr_sql_query("select email from ".TABLE_PREFIX."_utilisateurs, ".TABLE_PREFIX."_j_useradmin_site
        where ".TABLE_PREFIX."_utilisateurs.login = ".TABLE_PREFIX."_j_useradmin_site.login and ".TABLE_PREFIX."_j_useradmin_site.id_site='".$id_site."'");
        if ($sql_admin) {
            for ($i = 0; ($row = grr_sql_row($sql_admin, $i)); $i++) {
                if (validate_email($row[0])) $emails[] = $row[0];
            }
        }
     }
    }

    // Si la table des emails des administrateurs des sites est vide, on avertit les administrateurs g�n�rauxd
    if (count($emails) == 0) {
        $sql_admin = grr_sql_query("select email from ".TABLE_PREFIX."_utilisateurs where statut = 'administrateur'");
        if ($sql_admin) {
            for ($i = 0; ($row = grr_sql_row($sql_admin, $i)); $i++) {
                if (validate_email($row[0])) $emails[] = $row[0];
            }
        }
    }
    return $emails;
}

function validate_email ($email)
{
    $atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // caract�res autoris�s avant l'arobase
    $domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // caract�res autoris�s apr�s l'arobase (nom de domaine)

    $regex = '/^' . $atom . '+' .   // Une ou plusieurs fois les caract�res autoris�s avant l'arobase
    '(\.' . $atom . '+)*' .         // Suivis par z�ro point ou plus
                                    // s�par�s par des caract�res autoris�s avant l'arobase
    '@' .                           // Suivis d'un arobase
    '(' . $domain . '{1,63}\.)+' .  // Suivis par 1 � 63 caract�res autoris�s pour le nom de domaine
                                    // s�par�s par des points
    $domain . '{2,63}$/i';          // Suivi de 2 � 63 caract�res autoris�s pour le nom de domaine

    if (preg_match($regex, $email)) {
            return true;
    } else {
            return false;
    }

}
/** grrDelOverloadFromEntries()
 * Supprime les donn�es du champ $id_field de toutes les r�servations
 */

function grrDelOverloadFromEntries($id_field)
{
  $begin_string = "<".$id_field.">";
  $end_string = "</".$id_field.">";
  // On cherche � quel domaine est rattach� le champ additionnel
  $id_area = grr_sql_query1("select id_area from ".TABLE_PREFIX."_overload where id='".$id_field."'");
  if ($id_area == -1) fatal_error(0, get_vocab('error_area') . $id_area . get_vocab('not_found'));
  // On cherche toutes les ressources du domaine
  $call_rooms = grr_sql_query("select id from ".TABLE_PREFIX."_room where area_id = '".$id_area."'");
  if (! $call_rooms) fatal_error(0, get_vocab('error_room') . $id_room . get_vocab('not_found'));
  for ($i = 0; ($row = grr_sql_row($call_rooms, $i)); $i++) {
      // On cherche toutes les resas de cette resources
      $call_resa = grr_sql_query("select id, overload_desc from ".TABLE_PREFIX."_entry where room_id ='".$row[0]."'");
      if (! $call_resa) fatal_error(0, get_vocab('invalid_entry_id'));
      for ($j = 0; ($row2 = grr_sql_row($call_resa, $j)); $j++) {
          $overload_desc = $row2[1];
          $begin_pos = strpos($overload_desc,$begin_string);
          $end_pos = strpos($overload_desc,$end_string);
          if ( $begin_pos !== false && $end_pos !== false ) {
              $endpos = $end_pos + 1 + strlen($begin_string);
              $debut_new_chaine = substr($overload_desc,0,$begin_pos);
              $fin_new_chaine = substr($overload_desc,$endpos);
              $new_chaine = $debut_new_chaine.$fin_new_chaine;
              grr_sql_command("update ".TABLE_PREFIX."_entry set overload_desc = '".$new_chaine."' where id = '".$row2[0]."'");
          }

       }
      // On cherche toutes les resas de cette resources
      $call_resa = grr_sql_query("select id, overload_desc from ".TABLE_PREFIX."_repeat where room_id ='".$row[0]."'");
      if (! $call_resa) fatal_error(0, get_vocab('invalid_entry_id'));
      for ($j = 0; ($row2 = grr_sql_row($call_resa, $j)); $j++) {
          $overload_desc = $row2[1];
          $begin_pos = strpos($overload_desc,$begin_string);
          $end_pos = strpos($overload_desc,$end_string);
          if ( $begin_pos !== false && $end_pos !== false ) {
              $endpos = $end_pos + 1 + strlen($begin_string);
              $debut_new_chaine = substr($overload_desc,0,$begin_pos);
              $fin_new_chaine = substr($overload_desc,$endpos);
              $new_chaine = $debut_new_chaine.$fin_new_chaine;
              grr_sql_command("update ".TABLE_PREFIX."_repeat set overload_desc = '".$new_chaine."' where id = '".$row2[0]."'");
          }

       }
    }
}
function traite_grr_url($grr_script_name="",$force_use_grr_url="n") {
    // Dans certaines configuration (reverse proxy, ...) les variables $_SERVER["SCRIPT_NAME"] ou $_SERVER['PHP_SELF']
    // sont mal interpr�t�es entra�nant des liens erron�s sur certaines pages.
    if (((getSettingValue("use_grr_url")=="y") and (getSettingValue("grr_url")!="")) or ($force_use_grr_url=="y")) {
        if (substr(getSettingValue("grr_url"), -1) != "/") $ad_signe = "/"; else $ad_signe = "";
        return getSettingValue("grr_url").$ad_signe.$grr_script_name;
    } else {
        return $_SERVER['PHP_SELF'];
    }
}
// Pour les Jours/Cycles
//Cr�e le calendrier Jours/Cycles
function cree_calendrier_date_valide($n,$i) {
        if ($i <= getSettingValue("nombre_jours_Jours/Cycles")) {
            $sql = "INSERT INTO ".TABLE_PREFIX."_calendrier_jours_cycle set DAY='".$n."', Jours = $i";
            if (grr_sql_command($sql) < 0) {
                fatal_error(1, "<p>" . grr_sql_error());
            }
            $i++;
        }
        else {
            $i = 1;
            $sql = "INSERT INTO ".TABLE_PREFIX."_calendrier_jours_cycle set DAY='".$n."', Jours = $i";
            if (grr_sql_command($sql) < 0) {
                fatal_error(1, "<p>" . grr_sql_error());
            }
            $i++;
        }
        return $i;
    }

function numero_semaine($date)
{
    /*
     * Norme ISO-8601:
     * - La semaine 1 de toute ann�e est celle qui contient le 4 janvier ou que la semaine 1 de toute ann�e est celle qui contient le 1er jeudi de janvier.
     * - La majorit� des ann�es ont 52 semaines mais les ann�es qui commence un jeudi et les ann�es bissextiles commen�ant un mercredi en poss�de 53.
     * - Le 1er jour de la semaine est le Lundi
     */

    // D�finition du Jeudi de la semaine
    if (date("w",$date)==0) // Dimanche
        $jeudiSemaine = $date-3*24*60*60;
    else if (date("w",$date)<4) // du Lundi au Mercredi
        $jeudiSemaine = $date+(4-date("w",$date))*24*60*60;
    else if (date("w",$date)>4) // du Vendredi au Samedi
        $jeudiSemaine = $date-(date("w",$date)-4)*24*60*60;
    else // Jeudi
        $jeudiSemaine = $date;

    // D�finition du premier Jeudi de l'ann�e
    if (date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine)))==0) // Dimanche
    {
        $premierJeudiAnnee = mktime(12,0,0,1,1,date("Y",$jeudiSemaine))+4*24*60*60;
    }
    else if (date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine)))<4) // du Lundi au Mercredi
    {
        $premierJeudiAnnee = mktime(12,0,0,1,1,date("Y",$jeudiSemaine))+(4-date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine))))*24*60*60;
    }
    else if (date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine)))>4) // du Vendredi au Samedi
    {
        $premierJeudiAnnee = mktime(12,0,0,1,1,date("Y",$jeudiSemaine))+(7-(date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine)))-4))*24*60*60;
    }
    else // Jeudi
    {
        $premierJeudiAnnee = mktime(12,0,0,1,1,date("Y",$jeudiSemaine));
    }

    // D�finition du num�ro de semaine: nb de jours entre "premier Jeudi de l'ann�e" et "Jeudi de la semaine";
    $numeroSemaine =     (
                    (
                        date("z",mktime(12,0,0,date("m",$jeudiSemaine),date("d",$jeudiSemaine),date("Y",$jeudiSemaine)))
                        -
                        date("z",mktime(12,0,0,date("m",$premierJeudiAnnee),date("d",$premierJeudiAnnee),date("Y",$premierJeudiAnnee)))
                    ) / 7
                ) + 1;

    // Cas particulier de la semaine 53
    if ($numeroSemaine==53)
    {
        // Les ann�es qui commence un Jeudi et les ann�es bissextiles commen�ant un Mercredi en poss�de 53
        if (date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine)))==4 || (date("w",mktime(12,0,0,1,1,date("Y",$jeudiSemaine)))==3 && date("z",mktime(12,0,0,12,31,date("Y",$jeudiSemaine)))==365))
        {
            $numeroSemaine = 53;
        }
        else
        {
            $numeroSemaine = 1;
        }
    }

    return sprintf("%02d",$numeroSemaine);
}

# Calcule le nombre de jours dans un mois en tenant compte des ann�es bissextiles.
function getDaysInMonth($month, $year)
    {
        if ($month < 1 || $month > 12)
            return 0;
        $days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $d = $days[$month - 1];
        if ($month == 2)
        {
                #V�rification de l'ann�e bissextile.
            if ($year%4 == 0)
            {
                if ($year%100 == 0)
                {
                    if ($year%400 == 0)
                        $d = 29;
                    }
                else
                    $d = 29;
                }
            }
        return $d;
}
function getFirstDays()
    {
      global $weekstarts, $display_day;
      $basetime = mktime(12,0,0,6,11+$weekstarts,2000);
      for ($i = 0, $s = ""; $i < 7; $i++)
      {
         $j = ($i + 7 + $weekstarts) % 7;
         $show = $basetime + ($i * 24 * 60 * 60);
         $fl = strftime('%a',$show);
         if ($display_day[$j] == 1)
             $s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\">$fl</td>\n";
         else
             $s .= "";
      }
      return $s;
}
/*
Construit les informations � afficher sur les plannings
*/
function affichage_lien_resa_planning($breve_description, $id_resa) {
    $affichage = "";
    if ((getSettingValue("display_short_description")==1) and ($breve_description!=""))
        $affichage = $breve_description;
    else {
        $affichage = get_vocab("entryid").$id_resa;
    }
    return bbCode(htmlspecialchars($affichage,ENT_NOQUOTES),'titre');
}

/*
Construit les informations � afficher sur les plannings
*/
function affichage_resa_planning($_description, $id_resa) {
    $affichage = "";
    if (getSettingValue("display_full_description")==1)
        $affichage = bbCode(htmlspecialchars($_description,ENT_NOQUOTES),'');
    // Les champs add :
    $overload_data = mrbsEntryGetOverloadDesc($id_resa);
    foreach ($overload_data as $fieldname=>$field) {
        if (($field["affichage"] == 'y') and ($field["valeur"]!="")) {
            if ($affichage != "") $affichage .= "<br />";
            $affichage .= bbCode(htmlspecialchars($fieldname,ENT_NOQUOTES).get_vocab("deux_points").htmlspecialchars($field["valeur"],ENT_NOQUOTES),'');
        }
    }
    return $affichage;
}

/*
Construit les informations � afficher sur les plannings
*/
function affichage_champ_add_mails($id_resa) {
    $affichage = "";
    // Les champs add :
    $overload_data = mrbsEntryGetOverloadDesc($id_resa);
    foreach ($overload_data as $fieldname=>$field) {
        if (($field["overload_mail"] == 'y') and ($field["valeur"]!="")) {
            $affichage .= bbcode(htmlspecialchars($fieldname).get_vocab("deux_points").htmlspecialchars($field["valeur"]),'nobbcode')."\n";;
        }
    }
    return $affichage;
}

/*
Affiche un message pop-up
$type_affichage = "user" -> Affichage des "pop-up" de confirmation apr�s la cr�ation/modification/suppression d'une r�servation
Dans ce cas, l'affichage n'a lieu que si $_SESSION['displ_msg']='yes'
$type_affichage = "admin" -> Affichage des "pop-up" de confirmation dans les menus d'administration
$type_affichage = "force" -> On force l'affichage du pop-up m�me si javascript_info_admin_disabled est TRUE
*/
function affiche_pop_up($msg="",$type_affichage="user"){
    // Si $_SESSION["msg_a_afficher"] est d�fini, on l'affiche, sinon, on affiche $msg pass� en variable
    if ((isset($_SESSION["msg_a_afficher"])) and ($_SESSION["msg_a_afficher"] != "")) {
        $msg = $_SESSION["msg_a_afficher"];
    }
    if ($msg != "") {
      if ($type_affichage == "user") {
        if (!(getSettingValue("javascript_info_disabled"))) {
          echo "<script type=\"text/javascript\">";
          if ((isset($_SESSION['displ_msg'])) and ($_SESSION['displ_msg']=='yes'))  echo " alert(\"".$msg."\")";
          echo "</script>";
       }
      } else if ($type_affichage == "admin") {
        if (!(getSettingValue("javascript_info_admin_disabled")))  {
            echo "<script type=\"text/javascript\">";
            echo "<!--\n";
            echo " alert(\"".$msg."\")";
            echo "//-->";
            echo "</script>";
        }
     } else {
            echo "<script type=\"text/javascript\">";
            echo "<!--\n";
            echo " alert(\"".$msg."\")";
            echo "//-->";
            echo "</script>";
     }
    }
    $_SESSION['displ_msg']="";
    $_SESSION["msg_a_afficher"] = "";

}

/*
Retourne un tableau contenant les nom et pr�nom et l'email de $_beneficiaire
*/
function donne_nom_email($_beneficiaire){
    $tab_benef = array();
    $tab_benef["nom"] = "";
    $tab_benef["email"] = "";
    if ($_beneficiaire == "") {
        return $tab_benef;
        die();
    }
    $temp = explode("|",$_beneficiaire);
    if (isset($temp[0])) $tab_benef["nom"] = $temp[0];
    if (isset($temp[1])) $tab_benef["email"] = $temp[1];
    return $tab_benef;
}

/*
Retourne une chaine concat�n�e des nom et pr�nom et l'email
*/
function concat_nom_email($_nom, $_email){
    // On supprime les caract�res | de $_nom
    $_nom = trim(str_replace("|","",$_nom));
    if ($_nom == "") {
        return "-1";
        die();
    }
    $_email = trim($_email);
    if ($_email != "") {
        if (strstr($_email,"|")) {
            // l'adresse email contient le catact�re | ce qui n'est pas normal et peut compromettre la suite du traitement.
            return "-2";
            die();
        }
    }
    $chaine = $_nom."|".$_email;
    return $chaine;
}
/*
Formate les noms, pr�nom et email du b�n�ficiaire ou du b�n�ficiaire ext�rieur
$type = nomail -> on affiche les pr�nom et nom sans le mail.
$type = withmail -> on affiche un lien avec le mail sur les pr�nom et nom.
$type = formail -> on formate en utf8 pour l'envoi par mail (utilis� dans l'envoi de mails automatiques)
$type = onlymail -> on affiche uniquement le mail (utilis� dans l'envoi de mails automatiques)
*/

function affiche_nom_prenom_email($_beneficiaire,$_beneficiaire_ext,$type="nomail"){
    if ($_beneficiaire !="") {
        $sql_beneficiaire = "SELECT prenom, nom, email FROM ".TABLE_PREFIX."_utilisateurs WHERE login = '".$_beneficiaire."'";
        $res_beneficiaire = grr_sql_query($sql_beneficiaire);
        if ($res_beneficiaire) {
          $nb_result = grr_sql_count($res_beneficiaire);
          if ($nb_result == 0) {
            $chaine = get_vocab("utilisateur_inconnu").$_beneficiaire.")";
          } else {
            $row_user = grr_sql_row($res_beneficiaire, 0);
            if ($type == "formail")  {
                $chaine = removeMailUnicode($row_user[0])." ".removeMailUnicode($row_user[1]);
                if ($row_user[2] != "") {
                    $chaine .= " (".$row_user[2].")";
                }
            } else if ($type == "onlymail") {
            // Cas o� en envoie uniquement le mail
                $chaine = grr_sql_query1("select email from ".TABLE_PREFIX."_utilisateurs where login='$_beneficiaire'");
            } else if (($type == "withmail") and ($row_user[2] != "")) {
            // Cas o� en envoie les noms, pr�noms et mail
                $chaine = affiche_lien_contact($_beneficiaire,"identifiant:oui","afficher_toujours");
            } else {
                // Cas o� en envoie les noms, pr�noms sans le mail
                $chaine = $row_user[0]." ".$row_user[1];
            }
          }
          return $chaine;
          die();
        } else {
            return "";
            die();
        }
    } else {
        // cas d'un b�n�ficiaire ext�rieur
        // On r�cup�re le tableau des nom et emails
        $tab_benef = donne_nom_email($_beneficiaire_ext);
        // Cas o� en envoie uniquement le mail
        if ($type == "onlymail") {
           $chaine = $tab_benef["email"];
        // Cas o� en envoie les noms, pr�noms et mail
        } else if (($type == "withmail") and ($tab_benef["email"] != "")) {
            $email = explode('@',$tab_benef["email"]);
            $person = $email[0];
            if (isset($email[1])) {
                $domain = $email[1];
                $chaine = "<script type=\"text/javascript\">encode_adresse('".$person."','".$domain."','".AddSlashes($tab_benef["nom"])."',1);</script>";
            } else {
                $chaine = $tab_benef["nom"];
            }
        } else {
            // Cas o� en envoie les noms, pr�noms sans le mail
            $chaine = $tab_benef["nom"];
        }
        return $chaine;
        die();
    }
}
/*
 Fonction permettant d'effectuer une correspondance entre
 le profil lu sous LDAP et les statuts existants dans GRR
*/
function effectuer_correspondance_profil_statut($codefonction, $libellefonction) {
    # On r�cup�re le statut par d�faut des utilisateurs CAS
    $sso = getSettingValue("sso_statut");
    if ($sso == "cas_visiteur") $_statut = "visiteur";
    else if ($sso == "cas_utilisateur") $_statut = "utilisateur";

    # Le code fonction est d�fini
    if ($codefonction != "") {
        $sql = grr_sql_query1("select statut_grr from ".TABLE_PREFIX."_correspondance_statut where code_fonction='".$codefonction."'");
        if ($sql != -1) { // Si la fonction existe dans la table de correspondance, on retourne le statut_grr associ�
            return $sql;
        }	else {
            // Le code n'existe pas dans la base, alors on l'ins�re en lui attribuant le statut par d�faut.
		        $libellefonction = protect_data_sql($libellefonction);
			      $sql = grr_sql_command("insert into grr_correspondance_statut(code_fonction,libelle_fonction,statut_grr) values('$codefonction', '$libellefonction', '$_statut')");
			      return $_statut;
		    }
    # Le code fonction n'est pas d�fini, alors on retourne le statut par d�faut.
    }	else {
        return $_statut;
    }
}

/** supprimerReservationsUtilisateursEXT()
 *
 * Supprime les r�servations des membres qui proviennent d'une source "EXT"
 *
 *
 * Returns:
 *   0        - An error occured
 *   non-zero - The entries were deleted
 */
function supprimerReservationsUtilisateursEXT($avec_resa,$avec_privileges)
{
  // R�cup�ration de tous les utilisateurs de la source EXT
	$requete_users_ext = "SELECT login FROM ".TABLE_PREFIX."_utilisateurs WHERE source='ext' and statut<>'administrateur'";
	$res = grr_sql_query($requete_users_ext);
	$logins = array();
	$logins_liaison  = array();
  if ($res) for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
      $logins[]=$row[0];
	}

	// Construction des requ�tes de suppression � partir des diff�rents utilisateurs � supprimer
  if ($avec_resa=='y') {
	  // Pour chaque utilisateur, on supprime les r�servations qu'il a cr��es et celles dont il est b�n�ficiaire
  	// Table grr_entry
	  $req_suppr_table_entry = "DELETE FROM ".TABLE_PREFIX."_entry WHERE create_by = ";
  	$first=1; // 1er tour
	  foreach($logins as $log) {
		  if ($first == 1) { // pas de OR devant le login
			  $req_suppr_table_entry .= "'$log' OR beneficiaire='$log'";
  			$first=0;
	  	}
		  else {
			  $req_suppr_table_entry .= " OR create_by = '$log' OR beneficiaire = '$log' ";
  		}
	  }

	  // Pour chaque utilisateur, on supprime les r�servations p�riodiques qu'il a cr��es et celles dont il est b�n�ficiaire
  	// Table grr_repeat
	  $req_suppr_table_repeat = "DELETE FROM ".TABLE_PREFIX."_repeat WHERE create_by = ";
  	$first=1; // 1er tour
	  foreach($logins as $log) {
		  if ($first == 1) { // pas de OR devant le login
			  $req_suppr_table_repeat .= "'$log' OR beneficiaire='$log'";
  			$first=0;
	  	}
		  else {
			  $req_suppr_table_repeat .= " OR create_by = '$log' OR beneficiaire = '$log' ";
  		}
	  }
  	// Pour chaque utilisateur, on supprime les r�servations p�riodiques qu'il a cr��es et celles dont il est b�n�ficiaire
	  // Table grr_entry_moderate
  	$req_suppr_table_entry_moderate = "DELETE FROM ".TABLE_PREFIX."_entry_moderate WHERE create_by = ";
	  $first=1; // 1er tour
  	foreach($logins as $log) {
	  	if ($first == 1) { // pas de OR devant le login
		  	$req_suppr_table_entry_moderate .= "'$log' OR beneficiaire='$log'";
			  $first=0;
  		}
	  	else {
		  	$req_suppr_table_entry_moderate .= " OR create_by = '$log' OR beneficiaire = '$log' ";
  		}
	  }
  }

  $req_j_mailuser_room = "";
  $req_j_user_area = "";
  $req_j_user_room = "";
  $req_j_useradmin_area = "";
  $req_j_useradmin_site = "";
	foreach($logins as $log) {
      // Table grr_j_mailuser_room
	  	$test = grr_sql_query1("select count(login) from ".TABLE_PREFIX."_j_mailuser_room WHERE login='".$log."'");
      if ($test >=1 ) {
        if ($avec_privileges=="y") {
          if ($req_j_mailuser_room == "") { // pas de OR devant le login
            $req_j_mailuser_room = "DELETE FROM ".TABLE_PREFIX."_j_mailuser_room WHERE login='".$log."'";
          } else {
            $req_j_mailuser_room .= " OR login = '".$log."'";
  		    }
        } else {
          $logins_liaison[]=strtolower($log);
        }
      }
      // Table grr_j_user_area
	  	$test = grr_sql_query1("select count(login) from ".TABLE_PREFIX."_j_user_area WHERE login='".$log."'");
      if ($test >=1 ) {
        if ($avec_privileges=="y") {
          if ($req_j_user_area == "") { // pas de OR devant le login
            $req_j_user_area = "DELETE FROM ".TABLE_PREFIX."_j_user_area WHERE login='".$log."'";
          } else {
            $req_j_user_area .= " OR login = '".$log."'";
  		    }
        } else {
          $logins_liaison[]=strtolower($log);
        }
      }
      // Table grr_j_user_room
	  	$test = grr_sql_query1("select count(login) from ".TABLE_PREFIX."_j_user_room WHERE login='".$log."'");
      if ($test >=1 ) {
        if ($avec_privileges=="y") {
          if ($req_j_user_room == "") { // pas de OR devant le login
            $req_j_user_room = "DELETE FROM ".TABLE_PREFIX."_j_user_room WHERE login='".$log."'";
          } else {
            $req_j_user_room .= " OR login = '".$log."'";
  		    }
        } else {
          $logins_liaison[]=strtolower($log);
        }
      }
      // Table grr_j_useradmin_area
	  	$test = grr_sql_query1("select count(login) from ".TABLE_PREFIX."_j_useradmin_area WHERE login='".$log."'");
      if ($test >=1 ) {
        if ($avec_privileges=="y") {
          if ($req_j_useradmin_area == "") { // pas de OR devant le login
            $req_j_useradmin_area = "DELETE FROM ".TABLE_PREFIX."_j_useradmin_area WHERE login='".$log."'";
          } else {
            $req_j_useradmin_area .= " OR login = '".$log."'";
  		    }
        } else {
          $logins_liaison[]=strtolower($log);
        }
      }
      // Table grr_j_useradmin_site
	  	$test = grr_sql_query1("select count(login) from ".TABLE_PREFIX."_j_useradmin_site WHERE login='".$log."'");
      if ($test >=1 ) {
        if ($avec_privileges=="y") {
          if ($req_j_useradmin_site == "") { // pas de OR devant le login
            $req_j_useradmin_site = "DELETE FROM ".TABLE_PREFIX."_j_useradmin_site WHERE login='".$log."'";
          } else {
            $req_j_useradmin_site .= " OR login = '".$log."'";
  		    }
        } else {
          $logins_liaison[]=strtolower($log);
        }
      }
  }
	// Suppression effective
	echo "<hr />\n";
	if ($avec_resa=='y') {
	  $nb=0;
    $s = grr_sql_command ($req_suppr_table_entry);
	  if ($s != -1) $nb +=$s;
	  $s = grr_sql_command (	$req_suppr_table_repeat);
	  if ($s != -1) $nb +=$s;
	  $s = grr_sql_command (	$req_suppr_table_entry_moderate);
	  if ($s != -1) $nb +=$s;
  echo "<p class='avertissement'>".get_vocab("tables_reservations").get_vocab("deux_points").$nb.get_vocab("entres_supprimees")."</p>\n";
  }
  $nb=0;

  if ($avec_privileges=="y") {
    if ($req_j_mailuser_room != "") {
    	$s = grr_sql_command ($req_j_mailuser_room);
      if ($s != -1) $nb +=$s;
    }
    if ($req_j_user_area != "") {
  	  $s = grr_sql_command ($req_j_user_area);
      if ($s != -1) $nb +=$s;
    }
    if ($req_j_user_room != "") {
    	$s = grr_sql_command ($req_j_user_room);
      if ($s != -1) $nb +=$s;
    }
    if ($req_j_useradmin_area != "") {
  	  $s = grr_sql_command ($req_j_useradmin_area);
      if ($s != -1) $nb +=$s;
    }
    if ($req_j_useradmin_site != "") {
    	$s = grr_sql_command ($req_j_useradmin_site);
      if ($s != -1) $nb +=$s;
    }
  }
  echo "<p class='avertissement'>".get_vocab("tables_liaison").get_vocab("deux_points").$nb.get_vocab("entres_supprimees")."</p>\n";

  if ($avec_privileges=="y") {
    // Enfin, suppression des utilisateurs de la source EXT qui ne sont pas administrateur
	  $requete_suppr_users_ext = "DELETE FROM ".TABLE_PREFIX."_utilisateurs WHERE source='ext' and statut<>'administrateur'";
	  $s = grr_sql_command($requete_suppr_users_ext);
    if ($s == -1) $s = 0;
    echo "<p class='avertissement'>".get_vocab("table_utilisateurs").get_vocab("deux_points").$s.get_vocab("entres_supprimees")."</p>\n";
  } else {
    $n=0;
    foreach($logins as $log) {
      if (!in_array(strtolower($log),$logins_liaison)) {
      	  grr_sql_command("DELETE FROM ".TABLE_PREFIX."_utilisateurs WHERE login='".$log."'");
          $n++;
      }
    }
    echo "<p class='avertissement'>".get_vocab("table_utilisateurs").get_vocab("deux_points").$n.get_vocab("entres_supprimees")."</p>\n";
  }
}

/** NettoyerTablesJointure()
 *
 * Supprime les lignes inutiles dans les tables de liaison
 *
 */
function NettoyerTablesJointure()
{
  $nb = 0;
	// Table grr_j_mailuser_room
	$req = "SELECT j.login FROM ".TABLE_PREFIX."_j_mailuser_room j
      LEFT JOIN ".TABLE_PREFIX."_utilisateurs u on u.login=j.login
      WHERE (u.login  IS NULL)";
  $res = grr_sql_query($req);
  if ($res) {
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
       $nb++;
       grr_sql_command("delete from ".TABLE_PREFIX."_j_mailuser_room where login='".$row[0]."'");
    }
  }


	// Table grr_j_user_area
	$req = "SELECT j.login FROM ".TABLE_PREFIX."_j_user_area j
      LEFT JOIN ".TABLE_PREFIX."_utilisateurs u on u.login=j.login
      WHERE (u.login  IS NULL)";
  $res = grr_sql_query($req);
  if ($res) {
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
       $nb++;
       grr_sql_command("delete from ".TABLE_PREFIX."_j_user_area where login='".$row[0]."'");
    }
  }


	// Table grr_j_user_room
	$req = "SELECT j.login FROM ".TABLE_PREFIX."_j_user_room j
      LEFT JOIN ".TABLE_PREFIX."_utilisateurs u on u.login=j.login
      WHERE (u.login  IS NULL)";
  $res = grr_sql_query($req);
  if ($res) {
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
       $nb++;
       grr_sql_command("delete from ".TABLE_PREFIX."_j_user_room where login='".$row[0]."'");
    }
  }


	// Table grr_j_useradmin_area
	$req = "SELECT j.login FROM ".TABLE_PREFIX."_j_useradmin_area j
      LEFT JOIN ".TABLE_PREFIX."_utilisateurs u on u.login=j.login
      WHERE (u.login  IS NULL)";
  $res = grr_sql_query($req);
  if ($res) {
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
       $nb++;
       grr_sql_command("delete from ".TABLE_PREFIX."_j_useradmin_area where login='".$row[0]."'");
    }
  }

	// Table grr_j_useradmin_site
	$req = "SELECT j.login FROM ".TABLE_PREFIX."_j_useradmin_site j
      LEFT JOIN ".TABLE_PREFIX."_utilisateurs u on u.login=j.login
      WHERE (u.login  IS NULL)";
  $res = grr_sql_query($req);
  if ($res) {
    for ($i = 0; ($row = grr_sql_row($res, $i)); $i++) {
       $nb++;
       grr_sql_command("delete from ".TABLE_PREFIX."_j_useradmin_site where login='".$row[0]."'");
    }
  }

	// Suppression effective
	echo "<hr />\n";
  echo "<p class='avertissement'>".get_vocab("tables_liaison").get_vocab("deux_points").$nb.get_vocab("entres_supprimees")."</p>\n";
}




if ( !function_exists('htmlspecialchars_decode') )
{
    function htmlspecialchars_decode($text)
    {
        return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
    }
}
// Les lignes suivantes permettent la compatibilit� de GRR avec la variables register_global � off
unset($day);
if (isset($_GET["day"])) {
    $day = $_GET["day"];
    settype($day,"integer");
    if ($day < 1) $day = 1;
    if ($day > 31) $day = 31;
}
unset($month);
if (isset($_GET["month"])) {
    $month = $_GET["month"];
    settype($month,"integer");
    if ($month < 1) $month = 1;
    if ($month > 12) $month = 12;
}
unset($year);
if (isset($_GET["year"])) {
    $year = $_GET["year"];
    settype($year,"integer");
    if ($year < 1900) $year = 1900;
    if ($year > 2100) $year = 2100;
}
?>