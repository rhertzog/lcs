<?php
/* =============================================
   Projet LCS : Linux Communication Server
   Consultation de l'annuaire LDAP
   Annu/Search.php
   [LCS CoreTeam]
   « jLCF >:> » jean-luc.chretien@tice.ac-caen.fr
   « oluve » olivier.le_monnier@crdp.ac-caen.fr
   Equipe Tice académie de Caen
   V 1.3 maj : 21/11/2003
   ============================================= */
  include "../lcs/includes/headerauth.inc.php";
  include "includes/ldap.inc.php";
  include "includes/ihm.inc.php";

  list ($idpers)= isauth();
  if ($idpers == "0") header("Location:$urlauth");
  header_html();
  aff_trailer ("2");
?>
<H2>Rechercher un utilisateur</H2>
   <form action="peoples_list.php" method = post>
        <table>
	<tbody>
	  <tr>
	    <td>Nom complet :</td>
	    <td>
	      <select name="priority_surname">
		<option value="contient">contient</option>
		<option value="commence">commence par</option>
		<option value="finit">finit par</option>
	      </select>
	    </td>
	    <td><input type="text" name="prenom"></td>
	  </tr>
	  <tr>
	    <td>Nom :</td>
	    <td>
	      <select name="priority_name">
		<option value="contient">contient</option>
		<option value="commence">commence par</option>
		<option value="finit">finit par</option>
	      </select>
	    </td>
	    <td><input type="text" name="nom"></td>
	  </tr>
	  <tr>
	    <td>Classe :</td>
	    <td>
	      <select name="priority_classe">
		<option value="contient">contient</option>
		<option value="commence">commence par</option>
		<option value="finit">finit par</option>
	      </select>
	    </td>
	    <td><input type="text" name="classe"></td>
	  </tr>
	  <tr>
	    <td></td>
	    <td></td>
	    <td align="right"><input type="submit" Value="Lancer la recherche"></td>
	  </tr>
	</tbody>
      </table>
    </form>
    <!-- Recherche d'un groupe (classe, Equipe, Cours ...) -->
        <h2>Rechercher un groupe (classe, équipe, cours ...)</h2>
    <form action="groups_list.php" method = post>
      <table>
	<tbody>
	  <tr>
	    <td>Groupe :</td>
	    <td>
	      <select name="priority_group">
		<option value="contient">contient</option>
		<option value="commence">commence par</option>
		<option value="finit">finit par</option>
	      </select>
	    </td>
	    <td><input type="text" name="group"></td>
	  </tr>
	  <tr>
	    <td></td>
	    <td></td>
	    <td align="right"><input type="submit" Value="Lancer la recherche"></td>
	  </tr>
	</tbody>
      </table>
    </form>


<?
  include ("../lcs/includes/pieds_de_page.inc.php");
?>
