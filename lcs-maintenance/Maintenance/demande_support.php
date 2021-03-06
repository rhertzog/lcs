<?php
include "Includes/checking.php";
//check des variables si on ne vient pas du desktop ou d'applis.php
if ( (!mb_ereg("($domain/lcs/applis.php|$domain/desktop\/)$", $_SERVER['HTTP_REFERER']))){
    if (! check_acces()) exit;
}
else {
    if (! check_acces(1)) exit;
}

header ('Content-type" => "text/html; charset=utf-8');
include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ldap.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";
$uid=  $_SESSION['login'];
include ("$BASEDIR/lcs/includes/htmlpurifier/library/HTMLPurifier.auto.php");
if (count($_GET)>0) {
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    // Register Global GET
    $bat=$purifier->purify($_GET['bat']);
    $etage=$purifier->purify($_GET['etage']);
    $salle=$purifier->purify($_GET['salle']);
    $action=$purifier->purify($_GET['action']);
    $secteur=$purifier->purify($_GET['secteur']);
}
if (count($_POST)>0) {
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    // Register Global POST
    $nom=$purifier->purify($_POST['nom']);
    $marque=$purifier->purify($_POST['marque']);
    $poste=$purifier->purify($_POST['poste']);
    $se=$purifier->purify($_POST['se']);
    $typpb=$purifier->purify($_POST['typpb']);
    $mail=$purifier->purify($_POST['mail']);
    $texte=$purifier->purify($_POST['texte']);
}

if ($uid== "") {
    // L'utilisateur n'est pas authentifie
    table_alert ("Vous devez pr&#233;alablement vous authentifier sur votre &#171; Espace perso  LCS &#187; pour acc&#233;der &#224; cette application !");
} else {
    include "Includes/config.inc.php";
    include "Includes/func_maint.inc.php";
    # DEBUG
    if ($DEBUG) echo "DEBUG   bat etage >> $bat, $etage<br>";
    html();
    // L'utilisateur est authentifie
    // Lecture des parametres LDAP associes a cet utilisateur
    list($user, $groups)=people_get_variables($uid, false);
    // Affichage du menu haut
    if (is_admin("Maint_is_admin",$uid)=="Y") $type_mnu="team"; else $type_mnu="user";
    Aff_mnu($type_mnu);
    ?>
    <form name="demande" id="demandeForm" action="action/envoi.ajax.php" method="POST">
    <input name="jeton_ajax" type="hidden" id="jet" value="<?php echo md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/action/requete_topo.php')); ?>" />
    <input name="jeton_ajax2" type="hidden" id="jet2" value="<?php echo md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/action/topo.ajax.php')); ?>" />
    <input name="jeton" type="hidden"  value="<?php echo md5($_SESSION['token'].htmlentities('/Plugins/Maintenance/action/envoi.ajax.php')); ?>" />
    <?php
      Aff_messIntro();
    ?>
    <div class="tableintro divintro info tableau">
          <img src="Style/img/exclamation.png" alt="Important" />
          <i>Tous les champs marqu&eacute;s d'un ast&eacute;risque ' * ' doivent obligatoirement &ecirc;tre compl&eacute;t&eacute;s.</i>
          <br class="cleaner" style="clear:both"/>
    </div>
    <div class="separate" style="height:10px"></div>
    <div class="tableau tableint">
        <h3 class="subconfigsubtitle"><img src="Style/img/24/user.png" alt="" />&nbsp;Identification</h3>
        <div class="fieldcontainer">
          <label for="nom">Vos nom et pr&#233;nom:</label>&nbsp;
          <input type="text" value="<?php echo $user["fullname"];?>" name="nom" id="nom" class="required">
        </div>
    </div>
    <div id="divAjax">
        <div id="loadingContainer">
                <div id="loading">
                        Chargement...
                </div>
    </div>
    <a name="localise"></a>
    <div id="contentDiv">
    <?php
    //include "topo.php";
    ?>
    </div>
    <div id="ajaxPreview"></div><!-- :: Pour tests - style="height:24px;background:#ffcc33;font-size:.8em;" -->
    </div>
    <div class="tableau tableint">
        <h3 class="subconfigsubtitle"><img src="Style/img/24/computer.png" alt="" />&nbsp;Ordinateur</h3>
        <div class="fieldcontainer">
            <table style="width:940px">
                <tr>
                    <td class="tableau" style="width:30%"><label for="marque">Marque&nbsp;</label><input type="text" name="marque" size="15" id="marque"></td>
                    <td class="tableau" style="width:30%"><label for="poste">N&deg; de poste&nbsp;</label><span>&nbsp;*&nbsp;</span><input type="text" name="poste" id="poste" size="5" maxlength="5" class="required"></td>
                    <td class="tableau">
                    <label for="se">Syst&#232;me d'exploitation :&nbsp;</label>
                     <select name="se" id="se">
                        <option>MAC OS X</option>
                        <option>Linux</option>
                        <option>Windows 10</option>
                        <option>Windows 8</option>
                        <option>Windows 7</option>
                        <option>Windows Vista</option>
                        <option>Windows XP</option>
                        <option>Windows 2000</option>
                        <option>Windows 95</option>
                        <option>Windows 98 SE</option>
                        <option>Windows Mill&#233;nium</option>
                        <option>Windows 3.1</option>
                        <option>DOS</option>
                      </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="tableau tableint">
        <h3 class="subconfigsubtitle"><img src="Style/img/24/bug_error.png" alt="" />&nbsp;Probl&egrave;me</h3>
        <div class="fieldcontainer">
            <table>
                <tr>
                <td class="tableau">
                <label for="typpb">L'origine du probl&#232;me constat&#233; :&nbsp;</label>
                </td>
                <td>
                <select name="typpb" id="typpb">
                <option>Ne sait pas</option>
                <option>Logiciel</option>
                <option>Mat&#233;riel</option>
                </select>
                </td>
                </tr>
                <tr>
                <td VALIGN=TOP class="tableau"><div style="float:right"><span>&nbsp;*&nbsp;</span></div>
                <label for="texte">Description du probl&egrave;me:&nbsp;</label></td>
                <td VALIGN=TOP class="tableau">
                <textarea name="texte" id="texte" rows="6" cols="90"  class="required" ></textarea>
                </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="tableau tableint tablenul" style="text-align:center;">
        <input type="hidden" value="<?php echo $user["email"] ?>" name="mail">
        <input type="submit" value="Soumettre" name="submit" id="submitTopo" class="button">
        <input type="button" value="Recommencer" name="reset" class="button">
    </div>
    </form>
    <script>
      $(document).ready(function(){
              $('div.mnu>a').removeClass('active');
              //$('div.mnu>a.".$mnuchoice ."').addClass('active');
              $('div.mnu>a.demand_sup').addClass('active');
      });
    /*
      $(document).ready(function(){
              initPageTopo();
      });
    */
    </script>
<?php
  }
 include "Includes/pieds_de_page.inc.php";
?>
