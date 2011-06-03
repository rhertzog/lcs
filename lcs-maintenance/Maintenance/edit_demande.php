<?php
	include "Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
  	include "$BASEDIR/Annu/includes/ldap.inc.php";
  	include "$BASEDIR/Annu/includes/ihm.inc.php";
	include "Includes/func_maint.inc.php";

        // Register Global GET
        $Rid=$_GET['Rid'];

        // Register Global POST
        $secteur=$_POST['secteur'];
        $bat=$_POST['bat'];
        $etage=$_POST['etage'];
        $salle=$_POST['salle'];
        $marque=$_POST['marque'];
        $poste=$_POST['poste'];
        $se=$_POST['se'];
        $typpb=$_POST['typpb'];
        $mail=$_POST['mail'];
        $texte=$_POST['texte'];
        $rid=$_POST['rid'];
        $OpenTimeStamp=$_POST['OpenTimeStamp'];
        $post=$_POST['post'];

		//
        html();
	list ($idpers,$uid)= isauth();
  	if ($idpers == "0") {
		// L'utilisateur n'est pas authentifie
		table_alert ("Vous devez pr&#233;alablement vous authentifier sur votre «Espace perso  LCS» pour acc&#233;der &#233; cette application !");
	} else {
		// L'utilisateur est authentifie
		// Lecture des parametres LDAP associes a cet utilisateur
		list($user, $groups)=people_get_variables($uid, false);
		// Affichage du menu haut
		Aff_mnu("user");
		//Verification si rid appartient a Owner
		include "Includes/config.inc.php";
		list ($Sector, $Owner, $OwnerMail, $Author, $Building, $Room, $NumComp, $Mark, $Cat, $Os, $Content, $OpenTimeStamp, $NumBoost)=read_task($Rid);
		if ( $Owner == $user["fullname"] && $Acq == 0 ) {
			Aff_bar_mode ("Modification demande");
			#table_alert ("Desole, cette fonctionnalite n'est pas encore disponible!");
			$edit = true;
		} else {
			table_alert ("Vous ne pouvez pas modifier cette demande d'intervention !");
			$edit = false;
		}
		if ( $edit && (
		                    !$post || ( $post && (
								empty($secteur) ||
								empty($bat) ||
								empty($salle) ||
								empty($poste) ||
								empty($se) ||
								empty($texte)
								)
						)
				)
		   ) {
		?>
<script>
 	$(document).ready(function(){
		$('div.mnu>a').removeClass('active');
 		$('div.mnu>a.wait').addClass('active');
 	});
 	</script>
 
 <form action="edit_demande.php?Rid=<?php echo $Rid ?>" method="POST">

<?php
 if ( $post && ( empty($secteur) || empty($bat) || empty($salle) || empty($poste) || empty($se) || empty($texte) ) ) {
?>
    <div class="tableintro divintro info tableau">
            <img src="Style/img/exclamation.png" alt="Important" />
            <i>Tous les champs marqu&eacute;s d'un ast&eacute;risque ' * ' doivent obligatoirement &ecirc;tre compl&eacute;t&eacute;s.</i>
            <br class="cleaner" style="clear:both"/>
    </div>
<?php } ?>
     <div class="tableau tableint">
          <h3 class="subconfigsubtitle"><img src="Style/img/24/user.png" alt="" />&nbsp;Identification</h3>
          
          <div class="fieldcontainer">
            Vos nom et pr&#233;nom:&nbsp;<?php echo "<b>".$user["fullname"]."</b>";?>&nbsp;
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
<div id="ajaxPreview"></div><!-- :: Pour tests - style="height:24px;background:#ffcc33;font-size:.8em;" -->
</div>

     <div class="tableau tableint">
          <h3 class="subconfigsubtitle"><img src="Style/img/24/computer.png" alt="" />&nbsp;Ordinateur</h3>
          <div class="fieldcontainer">
<table style="width:940px">

<tr>
<td class="tableau" style="width:30%"><label for="marque">Marque&nbsp;</label><input type="text" name="marque" size="15" id="marque" value="<?php echo $Mark ?>"/></td>

<td class="tableau" style="width:30%"><label for="poste">N&deg; de poste&nbsp;</label><span>&nbsp;*&nbsp;</span><input type="text" name="poste" id="poste" size="5" maxlength="5" class="required" value="<?php echo $NumComp ?>" ></td>

<td class="tableau">
<label for="se">Syst&#232;me d'exploitation :&nbsp;</label>
	<select name="se" id="se">
		<option>Windows XP</option>
		<option>Windows 2000</option>
		<option>MAC OS X</option>
		<option>Linux</option>
		<option>Windows Vista</option>
		<option>Windows 7</option>
		<option>Windows 95</option>
		<option>Windows 98 SE</option>
		<option>Windows Mill&#233;nium</option>
		<option>Windows 3.1</option>
		<option>DOS</option>
	</select>
</td>
</tr>
</table>
</div></div>

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
  	<textarea name="texte" id="texte" rows="6" cols="90"  class="required" ><?php echo $Content ?></textarea>
</td>
</tr>
</table>
</div>
</div>

<div class="tableau tableint tablenul" style="text-align:center;">
	<input type="hidden" value="<?php echo $user["email"] ?>" name="mail"/>
	<input type="hidden" value="<?php echo $Rid ?>" name="rid"/>
	<input type="hidden" value="<?php echo $OpenTimeStamp ?>" name="OpenTimeStamp"/>
	<input type="hidden" value="true" name="post">
    <input type="submit" value="Soumettre" name="submit" id="submitTopo" class="button"/>
    <input type="button" value="Recommencer" name="reset" class="button"/>
</div>

</form>

<div id="maintData">
</div>

		<?php
		// (1) Fin Affichage form
		} else {
			// Update de la modification de la demande

			$requete = mysql_query("UPDATE maint_task SET  Sector='$secteur',  Building='$bat', Room='$salle', NumComp='$poste', Mark='$marque', Cat='$typpb', Os='$se', Content='$texte' WHERE Rid = '$Rid'");
			// Mail de la modification de la demande
			$texte = stripslashes($texte);
			$Subject = "[MaintInfo]Modification demande d'intervention du $OpenTimeStamp";

			$Body = "Le ".date("d-m-Y H:i:s")."\n";
			$Body .= "Emetteur : $Owner\n";
			$Body .= "Modification de la demande du $OpenTimeStamp\n";
			$Body .= "Secteur d'enseignement : $secteur\n";
			$Body .= "	Salle: $bat $salle\n";
			$Body .= "	Ordinateur: Marque $marque Poste $poste SE $se\n";
			$Body .= "	Probl&#232;me: $typpb\n";
			$Body .= "	\nDescription :\n";
 			$Body .= "$texte\n";

			mail_to ($MAILMAINT, $OwnerMail, $Subject, $Body, $OwnerMail);
			// Reaffichage de la demande
			$filter = "Acq='0' AND Rid='$Rid'";
			Aff_feed_wait($mode,$filter,"desc");
			Aff_bar_mode ("Votre modification &#233; &#233;t&#233; prise en compte !");
		}
	}
 include "Includes/pieds_de_page.inc.php";
 ?>
