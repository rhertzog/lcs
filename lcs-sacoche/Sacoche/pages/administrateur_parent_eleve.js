/**
 * @version $Id$
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 * @copyright Thomas Crespin 2010-2014
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
// Variables devant être accessible
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    var memo_td_html = '';
    var eleve_id = 0;

    // Initialisation

    $("#select_eleve").hide();

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Charger le select f_eleve en ajax
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    function maj_eleve(groupe_id,groupe_type)
    {
      $.ajax
      (
        {
          type : 'POST',
          url : 'ajax.php?page=_maj_select_eleves',
          data : 'f_groupe_id='+groupe_id+'&f_groupe_type='+groupe_type+'&f_statut=1'+'&f_multiple=0',
          dataType : "html",
          error : function(jqXHR, textStatus, errorThrown)
          {
            $('#ajax_msg').removeAttr("class").addClass("alerte").html("Échec de la connexion !");
          },
          success : function(responseHTML)
          {
            initialiser_compteur();
            if(responseHTML.substring(0,7)=='<option')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
            {
              $('#ajax_msg').removeAttr("class").addClass("valide").html("");
              $('#select_eleve').html(responseHTML).show();
            }
            else
            {
              $('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
            }
          }
        }
      );
    }
    function changer_groupe()
    {
      $("#select_eleve").html('<option value=""></option>').hide();
      var groupe_val = $("#f_groupe option:selected").val();
      if(groupe_val)
      {
        // type = $("#f_groupe option:selected").parent().attr('label');
        groupe_type = groupe_val.substring(0,1);
        groupe_id   = groupe_val.substring(1);
        $('#ajax_msg').removeAttr("class").addClass("loader").html("En cours&hellip;");
        maj_eleve(groupe_id,groupe_type);
      }
      else
      {
        $('#ajax_msg').removeAttr("class").html("&nbsp;");
      }
    }
    $("#f_groupe").change
    (
      function()
      {
        changer_groupe();
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Charger la liste des responsables d'un élève
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $("#select_eleve").change
    (
      function()
      {
        $("#fieldset_parents").html('');
        $("#p_valider").hide();
        $('#ajax_msg2').removeAttr("class").html("&nbsp;").parent().hide();
        eleve_id = $("#select_eleve").val();
        if(!eleve_id)
        {
          $('#ajax_msg').removeAttr("class").html("&nbsp;");
          return false;
        }
        $('#ajax_msg').removeAttr("class").addClass("loader").html("En cours&hellip;");
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&f_action=afficher_parents'+'&f_eleve_id='+eleve_id,
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $('#ajax_msg').removeAttr("class").addClass("alerte").html("Échec de la connexion !");
            },
            success : function(responseHTML)
            {
              initialiser_compteur();
              if(responseHTML.substring(0,6)=='<table')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
              {
                $('#ajax_msg').removeAttr("class").html("");
                $('#fieldset_parents').html(responseHTML).show();
              }
              else
              {
                $('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
              }
            }
          }
        );
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// ORDONNER => Clic sur une image pour échanger deux responsables
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#fieldset_parents').on
    (
      'click',
      'q.echanger',
      function()
      {
        para_clic = $(this).parent();
        table_prev = para_clic.prev('table');
        table_next = para_clic.next('table');
        titre_prev = table_prev.find('th.vu').html();
        titre_next = table_next.find('th.vu').html();
        table_prev.find('th.vu').html(titre_next);
        table_next.find('th.vu').html(titre_prev);
        para_clic.before(table_next);
        para_clic.after(table_prev);
        $('#ajax_msg2').removeAttr("class").addClass("alerte").html("Modification(s) non enregistrée(s) !").parent().show();
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// SUPPRIMER => Clic sur une image pour retirer un responsable
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $(document).on
    (
      'click',
      'q.supprimer',
      function()
      {
        $(this).parent().html('<q class="ajouter" title="Ajouter un responsable."></q>').prev('td').html('---').parent().parent().parent().removeAttr('id');
        $('#ajax_msg2').removeAttr("class").addClass("alerte").html("Modification(s) non enregistrée(s) !").parent().show();
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// AJOUTER => Clic sur une image pour ajouter un responsable
// MODIFIER => Clic sur une image pour modifier un responsable
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $(document).on
    (
      'click',
      'q.ajouter , q.modifier',
      function()
      {
        memo_td_html = $(this).parent().prev('td').html();
        afficher_masquer_images_action('hide');
        $(this).parent().prev('td').html('<select id="f_parent" name="f_parent">'+select_parent+'</select><q class="valider" title="Choisir ce responsable."></q><q class="annuler" title="Annuler."></q><br /><label id="ajax_msg_select">&nbsp;</label>');
        $('#f_parent').focus();
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// VALIDER => Clic sur une image pour valider l'ajout / la modification d'un responsable
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $(document).on
    (
      'click',
      'q.valider',
      function()
      {
        var parent_id  = $('#f_parent option:selected').val();
        var parent_nom = $('#f_parent option:selected').text();
        if(!parent_id)
        {
          $('#ajax_msg_select').removeAttr("class").addClass("alerte").html("Aucun responsable choisi !");
          return false;
        }
        if($('#parent_'+parent_id).length)
        {
          $('#ajax_msg_select').removeAttr("class").addClass("alerte").html("Ce responsable est déjà associé à l'élève !");
          return false;
        }
        $(this).parent().html('<em>'+parent_nom+'</em><hr /><div class="astuce">Penser à enregistrer pour confirmer ce changement.</div>').next('th').html('<q class="modifier" title="Changer ce responsable."></q><q class="supprimer" title="Retirer ce responsable."></q>').parent().parent().parent().attr('id','parent_'+parent_id);
        afficher_masquer_images_action('show');
        $('#ajax_msg2').removeAttr("class").addClass("alerte").html("Modification(s) non enregistrée(s) !").parent().show();
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// ANNULER => Clic sur une image pour annuler l'ajout / la modification d'un responsable
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $(document).on
    (
      'click',
      'q.annuler',
      function()
      {
        $(this).parent().html(memo_td_html);
        afficher_masquer_images_action('show');
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// ENVOYER les modifications apportées
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $("#Enregistrer").click
    (
      function()
      {
        var tab_parents_id = new Array();
        // Récupérer les identifiants des parents
        $('#fieldset_parents table').each
        (
          function()
          {
            var id = (typeof($(this).attr('id'))=='undefined') ? 0 : $(this).attr('id').substring(7) ;
            tab_parents_id.push(id);
          }
        );
        // Zy va : envoi ajax
        $('button').prop('disabled',true);
        afficher_masquer_images_action('hide');
        $('#ajax_msg2').removeAttr("class").addClass("loader").html("En cours&hellip;");
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&f_action=enregistrer_parents'+'&f_eleve_id='+eleve_id+'&f_parents_id='+tab_parents_id,
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $('button').prop('disabled',false);
              afficher_masquer_images_action('show');
              $('#ajax_msg2').removeAttr("class").addClass("alerte").html("Échec de la connexion !");
            },
            success : function(responseHTML)
            {
              initialiser_compteur();
              if(responseHTML.substring(0,6)=='<table')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
              {
                $('button').prop('disabled',false);
                $('#ajax_msg2').removeAttr("class").html("").parent().hide();
                $('#fieldset_parents').html(responseHTML).show();
              }
              else
              {
                $('button').prop('disabled',false);
                afficher_masquer_images_action('show');
                $('#ajax_msg2').removeAttr("class").addClass("alerte").html(responseHTML);
              }
            }
          }
        );
      }
    );

  }
);
