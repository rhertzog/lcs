/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2009-2015
 * 
 * ****************************************************************************************************
 * SACoche <http://sacoche.sesamath.net> - Suivi d'Acquisitions de Compétences
 * © Thomas Crespin pour Sésamath <http://www.sesamath.net> - Tous droits réservés.
 * Logiciel placé sous la licence libre Affero GPL 3 <https://www.gnu.org/licenses/agpl-3.0.html>.
 * ****************************************************************************************************
 * 
 * Ce fichier est une partie de SACoche.
 * 
 * SACoche est un logiciel libre ; vous pouvez le redistribuer ou le modifier suivant les termes 
 * de la “GNU Affero General Public License” telle que publiée par la Free Software Foundation :
 * soit la version 3 de cette licence, soit (à votre gré) toute version ultérieure.
 * 
 * SACoche est distribué dans l’espoir qu’il vous sera utile, mais SANS AUCUNE GARANTIE :
 * sans même la garantie implicite de COMMERCIALISABILITÉ ni d’ADÉQUATION À UN OBJECTIF PARTICULIER.
 * Consultez la Licence Publique Générale GNU Affero pour plus de détails.
 * 
 * Vous devriez avoir reçu une copie de la Licence Publique Générale GNU Affero avec SACoche ;
 * si ce n’est pas le cas, consultez : <http://www.gnu.org/licenses/>.
 * 
 */

// jQuery !
$(document).ready
(
  function()
  {

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Enlever le message ajax et le résultat précédent au changement d'un élément de formulaire
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#form_select').on
    (
      'change',
      'select, input',
      function()
      {
        $('#ajax_msg_enregistrement').removeAttr("class").html("&nbsp;");
        $('#bilan').hide();
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Charger le select f_niveau en ajax (au changement de f_matiere)
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    function maj_niveau(matiere_val)
    {
      $.ajax
      (
        {
          type : 'POST',
          url : 'ajax.php?page=_maj_select_niveaux',
          data : 'f_matiere='+matiere_val+'&f_first=1',
          dataType : "html",
          error : function(jqXHR, textStatus, errorThrown)
          {
            $('#ajax_maj_matiere').removeAttr("class").addClass("alerte").html("Échec de la connexion !");
          },
          success : function(responseHTML)
          {
            initialiser_compteur();
            if(responseHTML.substring(0,7)=='<option')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
            {
              $('#ajax_maj_matiere').removeAttr("class").html("&nbsp;");
              $('#f_niveau').html(responseHTML);
              $('#bloc_niveau').show();
            }
          else
            {
              $('#ajax_maj_matiere').removeAttr("class").addClass("alerte").html(responseHTML);
            }
          }
        }
      );
    }

    $('#f_matiere').change
    (
      function()
      {
        $('#bloc_niveau , #bloc_item , #zone_validation').hide();
        $('#f_niveau').html('<option value="">&nbsp;</option>');
        var matiere_val = $("#f_matiere").val();
        if(matiere_val)
        {
          $('#ajax_maj_matiere').removeAttr("class").addClass("loader").html("En cours&hellip;");
          maj_niveau(matiere_val);
        }
        else
        {
          $('#ajax_maj_matiere').removeAttr("class").html("&nbsp;");
          return false;
        }
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Charger le select f_item en ajax (au changement de f_niveau)
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    function maj_item(matiere_val,niveau_val)
    {
      $.ajax
      (
        {
          type : 'POST',
          url : 'ajax.php?page=_maj_select_items',
          data : 'f_matiere='+matiere_val+'&f_niveau='+niveau_val,
          dataType : "html",
          error : function(jqXHR, textStatus, errorThrown)
          {
            $('#ajax_maj_niveau').removeAttr("class").addClass("alerte").html("Échec de la connexion !");
          },
          success : function(responseHTML)
          {
            initialiser_compteur();
            if(responseHTML.substring(0,7)=='<option')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
            {
              $('#ajax_maj_niveau').removeAttr("class").html("&nbsp;");
              $('#f_item').html(responseHTML);
              $('#bloc_item').show();
            }
          else
            {
              $('#ajax_maj_niveau').removeAttr("class").addClass("alerte").html(responseHTML);
            }
          }
        }
      );
    }

    $('#f_niveau').change
    (
      function()
      {
        $('#bloc_item , #zone_validation').hide();
        $('#f_item').html('<option value="">&nbsp;</option>');
        var matiere_val = $("#f_matiere").val();
        var niveau_val = $("#f_niveau").val();
        if(matiere_val && niveau_val)
        {
          $('#ajax_maj_niveau').removeAttr("class").addClass("loader").html("En cours&hellip;");
          maj_item(matiere_val,niveau_val);
        }
        else
        {
          $('#ajax_maj_niveau').removeAttr("class").html("&nbsp;");
          return false;
        }
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Charger le select f_eleve en ajax (au changement de f_classe)
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    function maj_eleve(groupe_id,groupe_type)
    {
      $.ajax
      (
        {
          type : 'POST',
          url : 'ajax.php?page=_maj_select_eleves',
          data : 'f_groupe_id='+groupe_id+'&f_groupe_type='+groupe_type+'&f_eleves_ordre=alpha'+'&f_statut=1',
          dataType : "html",
          error : function(jqXHR, textStatus, errorThrown)
          {
            $('#ajax_maj_groupe').removeAttr("class").addClass("alerte").html("Échec de la connexion !");
          },
          success : function(responseHTML)
          {
            initialiser_compteur();
            if(responseHTML.substring(0,7)=='<option')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
            {
              $('#ajax_maj_groupe').removeAttr("class").html("&nbsp;");
              $('#f_eleve').html(responseHTML);
              $('#bloc_eleve').show();
            }
          else
            {
              $('#ajax_maj_groupe').removeAttr("class").addClass("alerte").html(responseHTML);
            }
          }
        }
      );
    }

    $('#f_classe').change
    (
      function()
      {
        $('#bloc_eleve , #zone_validation').hide();
        $('#f_eleve').html('<option value="">&nbsp;</option>');
        var groupe_id = $("#f_classe").val();
        if(groupe_id)
        {
          groupe_type = $("#f_classe option:selected").parent().attr('label');
          $('#ajax_maj_groupe').removeAttr("class").addClass("loader").html("En cours&hellip;");
          maj_eleve(groupe_id,groupe_type);
        }
        else
        {
          $('#ajax_maj_groupe').removeAttr("class").html("&nbsp;");
        }
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Afficher la dernière partie du formulaire (au changement de f_item ou f_eleve)
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#f_item , #f_eleve').change
    (
      function()
      {
        var item_id  = $("#f_item").val();
        var eleve_id = $("#f_eleve").val();
        if( item_id && eleve_id )
        {
          $('#zone_validation').show();
        }
        else
        {
          $('#zone_validation').hide();
        }
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Enregistrement d'une note
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#bouton_valider').click
    (
      function()
      {
        valeur = $('#zone_validation input[name=f_note]:checked').val();
        if(typeof(valeur)=='undefined')	// normalement impossible, sauf si par exemple on triche avec la barre d'outils Web Developer...
        {
          $('#ajax_msg_enregistrement').removeAttr("class").addClass("erreur").html("Choisir une note !");
          return false;
        }
        $('#form_select button').prop('disabled',true);
        $('#ajax_msg_enregistrement').removeAttr("class").addClass("loader").html("En cours&hellip;");
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&f_action=enregistrer_note'+'&'+$("#form_select").serialize(),
            dataType : 'json',
            error : function(jqXHR, textStatus, errorThrown)
            {
              $('#form_select button').prop('disabled',false);
              $('#ajax_msg_enregistrement').removeAttr("class").addClass("alerte").html(afficher_json_message_erreur(jqXHR,textStatus));
              return false;
            },
            success : function(responseJSON)
            {
              initialiser_compteur();
              $('#form_select button').prop('disabled',false);
              if(responseJSON['statut']==true)
              {
                $('#ajax_msg_enregistrement').removeAttr("class").addClass("valide").html("Note enregistrée !");
                $("#f_devoir").val(responseJSON['devoir_id']);
                $('#f_groupe').val(responseJSON['groupe_id']);
                $('#bilan_lien').attr('href','./index.php?page=evaluation_gestion&section=selection&devoir_id='+responseJSON['devoir_id']+'&groupe_type='+'E'+'&groupe_id='+responseJSON['groupe_id']);
                $('#bilan').show();
              }
              else
              {
                $('#ajax_msg_enregistrement').removeAttr("class").addClass("alerte").html(responseJSON['value']);
              }
            }
          }
        );
      }
    );

  }
);
