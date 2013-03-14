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

// jQuery !
$(document).ready
(
  function()
  {

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Charger le formulaire listant les structures ayant partagées un référentiel (appel au serveur communautaire)
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    var charger_formulaire_structures = function()
    {
      $('#ajax_msg').removeAttr("class").addClass("loader").html("En cours&hellip;");
      $.ajax
      (
        {
          type : 'POST',
          url : 'ajax.php?page='+PAGE,
          data : 'csrf='+CSRF+'&action=Afficher_structures',
          dataType : "html",
          error : function(jqXHR, textStatus, errorThrown)
          {
            $('#ajax_msg').removeAttr("class").addClass("alerte").html('Échec de la connexion ! <a href="#" id="charger_formulaire_structures">Veuillez essayer de nouveau.</a>');
            return false;
          },
          success : function(responseHTML)
          {
            initialiser_compteur();
            if(responseHTML.substring(0,7)!='<option')
            {
              $('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML+' <a href="#" id="charger_formulaire_structures">Veuillez essayer de nouveau.</a>');
            }
            else
            {
              modification = false;
              $('#ajax_msg').removeAttr("class").html('&nbsp;');
              $('#f_structure').html(responseHTML);
              $('#rechercher').prop('disabled',false);
            }
          }
        }
      );
    };

    // Charger au démarrage et au clic sur le lien obtenu si échec
    charger_formulaire_structures();
    $('#ajax_msg').on( 'click', '#charger_formulaire_structures', charger_formulaire_structures );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Charger le select f_matiere en ajax
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    function maj_matiere(matiere_famille_id)
    {
      $.ajax
      (
        {
          type : 'POST',
          url : 'ajax.php?page=_maj_select_matieres_famille',
          data : 'f_famille_matiere='+matiere_famille_id,
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
              $('#f_matiere').html(responseHTML);
            }
          else
            {
              $('#ajax_maj_matiere').removeAttr("class").addClass("alerte").html(responseHTML);
            }
          }
        }
      );
    }

    $("#f_famille_matiere").change
    (
      function()
      {
        matiere_famille_id = $("#f_famille_matiere").val();
        if(matiere_famille_id)
        {
          maj_matiere(matiere_famille_id);
        }
        else
        {
          $('#f_matiere').html('<option value="0">Toutes les matières</option>');
          $('#ajax_maj_matiere').removeAttr("class").html("&nbsp;");
        }
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Charger le select f_niveau en ajax
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    function maj_niveau(niveau_famille_id)
    {
      $.ajax
      (
        {
          type : 'POST',
          url : 'ajax.php?page=_maj_select_niveaux_famille',
          data : 'f_famille_niveau='+niveau_famille_id,
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
              $('#f_niveau').html(responseHTML);
            }
          else
            {
              $('#ajax_maj_niveau').removeAttr("class").addClass("alerte").html(responseHTML);
            }
          }
        }
      );
    }

    $("#f_famille_niveau").change
    (
      function()
      {
        niveau_famille_id = $("#f_famille_niveau").val();
        if(niveau_famille_id)
        {
          maj_niveau(niveau_famille_id);
        }
        else
        {
          $('#f_niveau').html('<option value="0">Tous les niveaux</option>');
          $('#ajax_maj_niveau').removeAttr("class").html("&nbsp;");
        }
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Réagir au changement dans un select
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('select').change
    (
      function()
      {
        $('#ajax_msg').removeAttr("class").html("&nbsp;");
        $('#choisir_referentiel_communautaire').hide("fast");
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur le bouton pour chercher des référentiels partagés sur d'autres niveaux ou matières
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#rechercher').click
    (
      function()
      {
        matiere_id   = $('#f_matiere').val();
        niveau_id    = $('#f_niveau').val();
        structure_id = $('#f_structure').val();
        if( (matiere_id==0) && (niveau_id==0) && (structure_id==0) )
        {
          $('#ajax_msg').removeAttr("class").addClass("erreur").html("Il faut préciser au moins un critère parmi matière / niveau / structure !");
          return false;
        }
        $('#rechercher').prop('disabled',true);
        $('#ajax_msg').removeAttr("class").addClass("loader").html("En cours&hellip;");
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&action=Lister_referentiels'+'&matiere_id='+matiere_id+'&niveau_id='+niveau_id+'&structure_id='+structure_id,
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $('#rechercher').prop('disabled',false);
              $('#ajax_msg').removeAttr("class").addClass("alerte").html('Échec de la connexion !');
              return false;
            },
            success : function(responseHTML)
            {
              $('#rechercher').prop('disabled',false);
              if(responseHTML.substring(0,3)!='<li')
              {
                $('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
              }
              else
              {
                initialiser_compteur();
                $('#ajax_msg').removeAttr("class").html("&nbsp;");
                $('#choisir_referentiel_communautaire ul').html(responseHTML).parent().show();
              }
            }
          }
        );
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour Voir le détail d'un référentiel partagé
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#choisir_referentiel_communautaire').on
    (
      'click',
      'q.voir',
      function()
      {
        referentiel_id = $(this).parent().attr('id').substr(3);
        description    = $(this).parent().text(); // Pb : il prend le contenu du <sup> avec
        longueur_sup   = $(this).prev().text().length;
        description    = description.substring(0,description.length-longueur_sup);
        new_label = '<label id="temp" class="loader">En cours&hellip;</label>';
        $(this).after(new_label);
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&action=Voir_referentiel'+'&referentiel_id='+referentiel_id,
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $.fancybox( '<label class="alerte">'+'Échec de la connexion !'+'</label>' , {'centerOnScroll':true} );
              $('label[id=temp]').remove();
            },
            success : function(responseHTML)
            {
              initialiser_compteur();
              if(responseHTML.substring(0,18)!='<ul class="ul_n1">')
              {
                $.fancybox( '<label class="alerte">'+responseHTML+'</label>' , {'centerOnScroll':true} );
              }
              else
              {
                $.fancybox( '<p class="noprint">Afin de préserver l\'environnement, n\'imprimer qu\'en cas de nécessité !</p>'+'<ul class="ul_m1"><li class="li_m1"><b>'+description+'</b><q class="imprimer_arbre" title="Imprimer le référentiel."></q>'+responseHTML+'</li></ul>' , {'centerOnScroll':true} );
              }
              $('label[id=temp]').remove();
            }
          }
        );
      }
    );

  }
);
