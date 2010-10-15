<?php
      include "Includes/basedir.inc.php";
      include "$BASEDIR/lcs/includes/headerauth.inc.php";
      include "$BASEDIR/Annu/includes/ldap.inc.php";
      include "$BASEDIR/Annu/includes/ihm.inc.php";

      // Register Global GET
      $bat=$_GET['bat'];
      $etage=$_GET['etage']; 
      $salle=$_GET['salle'];
      $action=$_GET['action'];

      // Register Global POST
      $nom=$_POST['nom'];
      $secteur=$_POST['secteur'];
      #$bat=$_POST['bat'];
      #$etage=$_POST['etage'];
      #$salle=$_POST['salle'];
      $marque=$_POST['marque'];
      $poste=$_POST['poste'];
      $se=$_POST['se'];
      $typpb=$_POST['typpb'];
      $mail=$_POST['mail'];
      $texte=$_POST['texte'];

      list ($idpers,$uid)= isauth();
      if ($idpers == "0") {
        // L'utilisateur n'est pas authentifie
	table_alert ("Vous devez pr&#233;alablement vous authentifier sur votre «Espace perso  LCS» pour acc&#233;der &#233; cette application !");
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
<table WIDTH="100%" class="tablenul" >
  <tr>
    <td ALIGN=CENTER HEIGHT="339">
      <form name="demande" action="envoi.php" method="POST">
      <table BORDER=0 WIDTH="100%" class="tableintro" >
        <tr>
          <td HEIGHT="27" class="tableau">
            <ul>
              <li>Vous avez constat&eacute; un dysfonctionnement sur un des ordinateurs que vous utilisez et vous d&eacute;sirez l'intervention d'un technicien.&nbsp;</li>
              <li>Compl&eacute;tez le formulaire ci-dessous et votre demande sera prise en compte le plus rapidement possible.</li>
            </ul>
            <div align=right>L'&eacute;quipe de maintenance informatique.</div>
          </td>
        </tr>
      </table>
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
      <table BORDER=0 WIDTH="100%" >
        <tr>
          <td COLSPAN="2" HEIGHT="2" class="titreParag">Identification</td>
        </tr>
        <tr>
          <td WIDTH="100%" class="tableau">
            Vos nom et pr&#233;nom:&nbsp;<input type="text" value="<?php echo $user["fullname"];?>" name="nom">
          </td>
        </tr>
      </table>
      <table BORDER=0 WIDTH="100%" >
        <tr>
          <td WIDTH="30%" HEIGHT="8" class="titreParag">Localisation</td>
          <td WIDTH="20%" HEIGHT="8" class="tableau">&nbsp;</td>
          <td WIDTH="20%" HEIGHT="8" class="tableau">&nbsp;</td>
          <td WIDTH="30%" HEIGHT="8" class="tableau">&nbsp;</td>
        </tr>
        <tr>
        <!-- Secteur d'enseignement -->
        <td WIDTH="30%" class="tableau">
          Secteur d'enseignement&nbsp;
          <select name="secteur">
<?php
// lecture de la table secteur
$result = @mysql_query("SELECT descr from secteur ORDER BY id ASC");
if ($result)
while ($r = @mysql_fetch_array($result))
  echo "<option>".$r["descr"]."</option>\n";
@mysql_free_result($result);
?>        
	</select>
  </td>
  <!-- Batiment -->
  <td WIDTH="20%" class="tableau">
	Bâtiment :&nbsp;
        <select name="bat" onChange="location = this.options[this.selectedIndex].value;">
<?php
// lecture de la table topologie pour affichage de la liste des batiments
$loop=0;
$result = @mysql_query("SELECT batiment from topologie ORDER BY batiment ASC");
if ($result)
while ($r = @mysql_fetch_array($result)) {
  if ( !isset ($bat ) ) $bat = $r["batiment"];
  $batiment[$loop] = $r["batiment"]; 
  if ( !isset( $batiment[$loop-1] ) || ( $batiment[$loop-1] != $r["batiment"] ) ) {
    echo "        <option value=\"demande_support.php?bat=".$r["batiment"]."\"";
    if ( $bat == $r["batiment"] ) echo "selected";
    echo ">".$r["batiment"]."</option>\n";
  }
  $loop++;  
}
@mysql_free_result($result);
?>         
	</select>
  </td>
  <!-- Etage -->
  <td WIDTH="20%" class="tableau">
    Etage :&nbsp;&nbsp;&nbsp;
    <select name="etage" onChange="location = this.options[this.selectedIndex].value;">
<?php
// lecture de la table topologie pour affichage de la liste des etages
$loop=0;
$result = @mysql_query("SELECT etage from topologie WHERE batiment='$bat' ORDER BY etage ASC");
if ($result)
while ($r = @mysql_fetch_array($result)) {
  if ( !isset ($etage) ) $etage = $r["etage"];
  $etage_[$loop] = $r["etage"]; 
  if ( !isset( $etage_[$loop-1] ) || ( $etage_[$loop-1] != $r["etage"] ) ) {
    echo "      <option value=\"demande_support.php?bat=".$bat."&etage=".$r["etage"]."\"";
    if ( $etage == $r["etage"] ) echo "selected";
    echo ">".$r["etage"]."</option>\n";
  }    
  $loop++;  
}
@mysql_free_result($result);
?>  
    </select>
  </td>
  <!-- Salle -->
  <td WIDTH="30%" class="tableau">
    Salle :&nbsp;
    <select name="salle" onChange="location = this.options[this.selectedIndex].value;">
<?php
// lecture de la table topologie pour affichage de la liste des salles
$loop=0;
$result = @mysql_query("SELECT salle from topologie WHERE batiment='$bat' AND etage='$etage' ORDER BY salle ASC");
if ($result)
while ($r = @mysql_fetch_array($result)) {
  $salle_[$loop] = $r["salle"]; 
  if ( !isset( $salle_[$loop-1] ) || ( $salle_[$loop-1] != $r["salle"] ) ) {
    echo "      <option value=\"demande_support.php?bat=".$bat."&etage=".$etage."&salle=".$r["salle"]."\"";
    if ( $salle == $r["salle"] ) echo "selected";
    echo ">".$r["salle"]."</option>\n";
  }    
  $loop++;  
}
@mysql_free_result($result);
?>
    </select>      
  </td>
</tr>
</table>

<table BORDER=0 WIDTH="100%" >
<tr>
<td WIDTH="27%" class="titreParag">Ordinateur</td>

<td COLSPAN="2" class="tableau">&nbsp;</td>
</tr>

<tr>
<td WIDTH="27%" class="tableau">Marque&nbsp;<input type="text" name="marque" size="15"></td>

<td WIDTH="23%" class="tableau">N&deg; de poste&nbsp;<input type="text" name="poste" size="5" maxlength="5">*</td>

<td WIDTH="50%" class="tableau">
Syst&#232;me d'exploitation :&nbsp;
	<select name="se">
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

<table BORDER=0 WIDTH="100%" >
<tr>
<td COLSPAN="2" class="titreParag">Probl&egrave;me</td>
</tr>

<tr>
  <td COLSPAN="2" HEIGHT="25" class="tableau">
    L'origine du probl&#232;me constat&#233; :&nbsp;
    <select name="typpb">
      <option>Ne sait pas</option>
      <option>Logiciel</option>
      <option>Mat&#233;riel</option>
    </select>
  </td>
</tr>

<tr>
<td VALIGN=TOP COLSPAN="2" HEIGHT="0" class="tableau">Description du probl&egrave;me
:&nbsp;</td>
</tr>

<tr>
<td VALIGN=TOP WIDTH="2%" HEIGHT="2" class="tableau">
<div align=right>*&nbsp;</div>
</td>

<td VALIGN=TOP WIDTH="100%" HEIGHT="2" class="tableau">
  	<textarea name="texte" rows="6" cols="90"></textarea>
</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
</table>
<table class='tablenul' BORDER=0 WIDTH="40%" >
<tr>
  <td ALIGN=CENTER WIDTH="33%">
    <input type="hidden" value="<?php echo $user["email"] ?>" name="mail">
  </td>
  <td ALIGN=CENTER WIDTH="33%">
    <input type="submit" value="Soumettre" name="submit">
  </td>
  <td WIDTH="33%">
    <input type="reset" value="Recommencer" name="reset">
  </td>
</tr>
</table>
<p>
</form>
</td>
</tr>
</table>
<?php
	}
 include "Includes/pieds_de_page.inc.php";
?>
