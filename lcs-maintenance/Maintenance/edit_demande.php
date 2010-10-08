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

			// (1) Affichage form
		?>
<table WIDTH="100%" HEIGHT="210" class="tablenul" >
<tr>
<td ALIGN=CENTER HEIGHT="339"><form action="edit_demande.php?Rid=<?php echo $Rid ?>" METHOD="POST">

<?php
 if ( $post && ( empty($secteur) || empty($bat) || empty($salle) || empty($poste) || empty($se) || empty($texte) ) ) {
?>
<table BORDER=0 WIDTH="100%" >
	<tr>
		<td WIDTH="20%" HEIGHT="2" BGCOLOR="#003366" class="tableau">
			<center><b><u><font color="#FFFFFF">ATTENTION!</font></u></b></center>
		</td>
		<td WIDTH="80%" HEIGHT="2" class="tableau">
			<i>Tous les champs marqu&eacute;s d'un ast&eacute;risque ' * ' doivent obligatoirement &ecirc;tre compl&eacute;t&eacute;s.</i>
		</td>
	</tr>
</table>
<?php } ?>

<table BORDER=0 WIDTH="100%" >
	<tr>
		<td COLSPAN="2" HEIGHT="2" class="titreParag">Identification</td>
	</tr>
	<tr>
		<td WIDTH="100%" class="tableau">Vos nom et pr&#233;nom:&nbsp;<?php echo "<b>".$user["fullname"]."</b>";?>&nbsp;</td>
	</tr>
</table>

<table BORDER=0 WIDTH="100%" >
	<tr>
		<td WIDTH="45%" HEIGHT="8" class="titreParag">Localisation</td>
		<td WIDTH="24%" HEIGHT="8" class="tableau">&nbsp;</td>
		<td WIDTH="31%" HEIGHT="8" class="tableau">&nbsp;</td>
	</tr>
	<tr>
	<td WIDTH="45%" class="tableau">
		Secteur d'enseignement&nbsp;
		<select name="secteur">
			<option><?php echo $Sector ?></option>
			<option>G&eacute;n&eacute;ral</option>
			<option>Industriel</option>
			<option>Tertiaire</option>
			<option>Administratif</option>
		</select>*&nbsp;
	</td>
	<td WIDTH="24%" class="tableau">
		B&acirc;timent :&nbsp;
		<select name="bat">
			<option><?php echo $Building ?></option>
			<option>A</option>
			<option>B</option>
			<option>B'</option>
			<option>C</option>
			<option>D</option>
			<option>E</option>
		</select>*&nbsp;
	</td>
	<td WIDTH="31%" class="tableau">
		Salle :&nbsp;<input type="text" name="salle" value="<?php echo $Room?>" size="3" maxlength="3">*
		<i><font size=-1>(3 chiffres)</font></i>
	</td>
</tr>
</table>

<table BORDER=0 WIDTH="100%" >
	<tr>
		<td WIDTH="27%" class="titreParag">Ordinateur</td>
		<td COLSPAN="2" class="tableau">&nbsp;</td>
	</tr>
	<tr>
		<td WIDTH="27%" class="tableau">
			Marque&nbsp;<input type="text" name="marque" value="<?php echo $Mark ?>"size="15">
		</td>
		<td WIDTH="23%" class="tableau">
			N&deg; de poste&nbsp;<input type="text" name="poste" value="<?php echo $NumComp ?>" size="5" maxlength="5">*
		</td>
		<td WIDTH="50%" class="tableau">
			SO<i><font size=-1>(Syst&egrave;me d'exploitation)</font></i>:&nbsp;
			<select name="se">
				<option><?php echo $Os ?></option></option>
				<option>Windows 98 SE</option>
				<option>Windows Mill&eacute;nium</option>
				<option>Windows 2000</option>
				<option>Windows XP</option>
				<option>Windows 95</option>
				<option>MAC OS X</option>
				<option>Linux</option>
				<option>Windows 3.1</option>
				<option>DOS</option>
			</select>
		</td>
	</tr>
</table>

<table BORDER=0 WIDTH="100%" >
	<tr>
		<td COLSPAN="2" class="titreParag">Probl&egrave;me</td>
	</tr>
	<tr>
		<td COLSPAN="2" HEIGHT="25" class="tableau">
			Le probl&egrave;me constat&eacute; est un probl&egrave;me :&nbsp;
			<select name="typpb">
				<option><?php echo $Cat ?></option>
				<option>Ne sait pas</option>
				<option>Logiciel</option>
				<option>Mat&eacute;riel</option>
			</select>
		</td>
	</tr>
	<tr>
		<td VALIGN=TOP COLSPAN="2" HEIGHT="0" class="tableau">
			Description du probl&egrave;me &nbsp;
		</td>
	</tr>
	<tr>
		<td VALIGN=TOP WIDTH="2%" HEIGHT="2" class="tableau">
			<div align=right>*&nbsp;</div>
		</td>
		<td VALIGN=TOP WIDTH="100%" HEIGHT="2" class="tableau">
  			<textarea name="texte" wrap="PHYSICAL" rows="6" cols="90"><?php echo $Content ?></textarea>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>
<input type="hidden" value="<?php echo $user["email"] ?>" name="mail">
<input type="hidden" value="<?php echo $Rid ?>" name="rid">
<input type="hidden" value="<?php echo $OpenTimeStamp ?>" name="OpenTimeStamp">
<input type="hidden" value="true" name="post">
<table class='tablenul' BORDER=0 WIDTH="40%" >
	<tr>
		<td ALIGN=CENTER WIDTH="50%"><input type="submit" value="Soumettre" name="submit"></td>
		<td WIDTH="50%"><input type="reset" value="Recommencer" name="reset"></td>
	</tr>
</table>

<p></form>
</td>
</tr>
</table>
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
			Aff_feed_wait($mode,$filter);
			Aff_bar_mode ("Votre modification &#233; &#233;t&#233; prise en compte !");
		}
	}
 include "Includes/pieds_de_page.inc.php";
 ?>
