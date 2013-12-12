<?php
/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
 * Logiciel placé sous la licence libre GPL 3 <http://www.rodage.org/gpl-3.0.fr.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la “GNU General Public License” telle que publiée par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (à votre gré) toute version ultérieure.
 * 
 * SACoche est distribué dans l’espoir qu’il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans même la garantie implicite de COMMERCIALISABILITÉ ni d’ADÉQUATION À UN OBJECTIF PARTICULIER.
 * Consultez la Licence Générale Publique GNU pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Générale Publique GNU avec SACoche ;
 * si ce n’est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}
$TITRE = "Rechercher un utilisateur";

// Javascript
$GLOBALS['HEAD']['js']['inline'][] = 'var input_date = "'.TODAY_FR.'";';
$GLOBALS['HEAD']['js']['inline'][] = 'var date_mysql = "'.TODAY_MYSQL.'";';
?>

<ul class="puce">
  <li><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=support_administrateur__user_recherche">DOC : Rechercher un utilisateur.</a></span></li>
  <li><span class="manuel"><a class="pop_up" href="<?php echo SERVEUR_DOCUMENTAIRE ?>?fichier=faq_documentation__detail_identifiants">DOC : A quoi correspondent les différents identifiants ?</a></span></li>
</ul>

<hr />

<form action="#" method="post" id="form_user_search">
  <p>
    <h4>Champs à identifiant unique :</h4>
    <label class="tab" for="search_id_ent">Id. ENT <img alt="" src="./_img/bulle_aide.png" title="Pour rapprocher les comptes en cas d'identification via un ENT." /> :</label><input type="radio" name="search_champ" value="id_ent" /> <input id="search_id_ent" name="search_id_ent" type="text" value="" size="30" maxlength="63" /><br />
    <label class="tab" for="search_id_gepi">Id. GEPI <img alt="" src="./_img/bulle_aide.png" title="En cas d'utilisation du logiciel GEPI." /> :</label><input type="radio" name="search_champ" value="id_gepi" /> <input id="search_id_gepi" name="search_id_gepi" type="text" value="" size="30" maxlength="63" /><br />
    <label class="tab" for="search_sconet_id">Id. Sconet <img alt="" src="./_img/bulle_aide.png" title="Pour un élève : ELEVE.ELEVE_ID de Siècle (ex-Sconet) ; 6 chiffres en général.<br />Pour un professeur / directeur : INDIVIDU.ID de STS-Web.<br />Pour un responsable légal : PERSONNE.PERSONNE_ID de Siècle (ex-Sconet)." /> :</label><input type="radio" name="search_champ" value="sconet_id" /> <input id="search_sconet_id" name="search_sconet_id" type="text" value="" size="15" maxlength="8" /><br />
    <label class="tab" for="search_sconet_elenoet">N° Sconet <img alt="" src="./_img/bulle_aide.png" title="Pour un élève : ELEVE.ELENOET de Siècle (ex-Sconet) ; 4 chiffres en général.<br />Inutilisé pour les autres profils." /> :</label><input type="radio" name="search_champ" value="sconet_elenoet" /> <input id="search_sconet_elenoet" name="search_sconet_elenoet" type="text" value="" size="15" maxlength="5" /><br />
    <label class="tab" for="search_reference">Référence <img alt="" src="./_img/bulle_aide.png" title="Pour un élève : ELEVE.ID_NATIONAL ou 'INE' de Siècle (ex-Sconet) ; 10 chiffres et une lettre.<br />Import tableur : référence pour rapprocher les comptes." /> :</label><input type="radio" name="search_champ" value="reference" /> <input id="search_reference" name="search_reference" type="text" value="" size="15" maxlength="11" /><br />
    <label class="tab" for="search_login">Login :</label><input type="radio" name="search_champ" value="login" /> <input id="search_login" name="search_login" type="text" value="" size="15" maxlength="20" /><br />
    <label class="tab" for="search_email">Courriel :</label><input type="radio" name="search_champ" value="email" /> <input id="search_email" name="search_email" type="text" value="" size="30" maxlength="63" /><br />
  </p>
  <p>
    <h4>Champs sans unicité imposée :</h4>
    <label class="tab" for="search_nom">Nom :</label><input type="radio" name="search_champ" value="nom" /> <input id="search_nom" name="search_nom" type="text" value="" size="30" maxlength="25" /><br />
    <label class="tab" for="search_prenom">Prénom :</label><input type="radio" name="search_champ" value="prenom" /> <input id="search_prenom" name="search_prenom" type="text" value="" size="30" maxlength="25" /><br />
  </p>
  <p>
    <span class="tab"></span><button id="bouton_chercher" type="submit" class="rechercher">Lancer la recherche.</button><label id="ajax_msg">&nbsp;</label>
  </p>
</form>

<hr />

<div id="resultat" class="hide">
  <h2>Résultat de la recherche</h2>
  <table id="table_action" class="form t9 hsort">
    <thead>
      <tr>
        <th>Id. ENT</th>
        <th>Id. GEPI</th>
        <th>Id Sconet</th>
        <th>N° Sconet</th>
        <th>Référence</th>
        <th>Profil</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Login</th>
        <th>Courriel</th>
        <th>Date sortie</th>
        <th class="nu"></th>
      </tr>
    </thead>
    <tbody>
      <tr><td class="nu" colspan="12"></td></tr>
  </tbody>
</table>
</div>

<form action="#" method="post" id="form_gestion" class="hide">
  <h2>Modifier un utilisateur</h2>
  <p>
    <label class="tab" for="f_id_ent">Id. ENT <img alt="" src="./_img/bulle_aide.png" title="Uniquement en cas d'identification via un ENT." /> :</label><input id="f_id_ent" name="f_id_ent" type="text" value="" size="30" maxlength="63" /><br />
    <label class="tab" for="f_id_gepi">Id. GEPI <img alt="" src="./_img/bulle_aide.png" title="Uniquement en cas d'utilisation du logiciel GEPI." /> :</label><input id="f_id_gepi" name="f_id_gepi" type="text" value="" size="30" maxlength="63" /><br />
    <label class="tab" for="f_sconet_id">Id Sconet <img alt="" src="./_img/bulle_aide.png" title="Pour un élève : ELEVE.ELEVE_ID de Siècle (ex-Sconet) ; 6 chiffres en général.<br />Pour un professeur / directeur : INDIVIDU.ID de STS-Web.<br />Pour un responsable légal : PERSONNE.PERSONNE_ID de Siècle (ex-Sconet)." /> :</label><input id="f_sconet_id" name="f_sconet_id" type="text" value="" size="15" maxlength="8" /><br />
    <label class="tab" for="f_sconet_num">N° Sconet <img alt="" src="./_img/bulle_aide.png" title="Pour un élève : ELEVE.ELENOET de Siècle (ex-Sconet) ; 4 chiffres en général.<br />Inutilisé pour les autres profils." /> :</label><input id="f_sconet_num" name="f_sconet_num" type="text" value="" size="15" maxlength="5" /><br />
    <label class="tab" for="f_reference">Référence <img alt="" src="./_img/bulle_aide.png" title="Pour un élève : ELEVE.ID_NATIONAL ou 'INE' de Siècle (ex-Sconet) ; 10 chiffres et une lettre.<br />Import tableur : référence pour rapprocher les comptes." /> :</label><input id="f_reference" name="f_reference" type="text" value="" size="15" maxlength="11" />
  </p>
  <p>
    <label class="tab" for="f_nom">Nom :</label><input id="f_nom" name="f_nom" type="text" value="" size="30" maxlength="25" /><br />
    <label class="tab" for="f_prenom">Prénom :</label><input id="f_prenom" name="f_prenom" type="text" value="" size="30" maxlength="25" />
  </p>
  <p>
    <label class="tab" for="f_login">Login :</label><input id="f_login" name="f_login" type="text" value="" size="15" maxlength="20" /><br />
    <label class="tab" for="f_courriel">Courriel :</label><input id="f_courriel" name="f_courriel" type="text" value="" size="30" maxlength="63" />
  </p>
  <p>
    <label class="tab" for="f_sortie_date">Date de sortie :</label><input id="box_sortie_date" name="box_sortie_date" value="1" type="checkbox" /> <label for="box_sortie_date">sans objet</label><span><input id="f_sortie_date" name="f_sortie_date" size="8" type="text" value="" /><q class="date_calendrier" title="Cliquer sur cette image pour importer une date depuis un calendrier !"></q></span>
  </p>
  <p>
    <label class="tab"></label><input id="f_action" name="f_action" type="hidden" value="modifier" /><input id="f_id" name="f_id" type="hidden" value="" /><input id="f_profil" name="f_profil" type="hidden" value="" /><button id="bouton_valider" type="button" class="valider">Valider.</button> <button id="bouton_annuler" type="button" class="annuler">Annuler.</button><label id="ajax_msg_gestion">&nbsp;</label>
  </p>
</form>

