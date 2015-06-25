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
    // Initialisation
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    // Il est plus simple d'initialiser à 0 les valeurs manquantes que de tenter d'ajouter et surtout de supprimer des éléments par la suite
    var tab_matiere = new Array(); $("#f_matiere input").each(function(){tab_matiere.push($(this).val());});
    var tab_prof    = new Array(); $("#f_prof    input").each(function(){tab_prof.push($(this).val());});
    // On compare par rapport au tableau js pour savoir ce qui a changé
    for ( var key_matiere in tab_matiere )
    {
      for ( var key_prof in tab_prof )
      {
        var matiere_id = tab_matiere[key_matiere];
        var prof_id    = tab_prof[key_prof];
        if(typeof(tab_join[matiere_id][prof_id])=='undefined')
        {
          tab_join[matiere_id][prof_id] = 0;
        }
      }
    }

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Alerter au changement d'un élément de formulaire
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#form_select').on
    (
      'change',
      'select, input',
      function()
      {
        $('#ajax_msg').removeAttr("class").addClass("alerte").html("Pensez à valider vos choix !");
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Réagir au clic sur un bouton (soumission du formulaire)
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#ajouter , #retirer').click
    (
      function()
      {
        var action = $(this).attr('id');
        if( !$("#f_prof input:checked").length || !$("#f_matiere input:checked").length )
        {
          $('#ajax_msg').removeAttr("class").addClass("erreur").html("Sélectionnez dans les deux listes !");
          return false;
        }
        // On récupère les id des profs et des matières concernés
        var select_matiere = new Array(); $("#f_matiere input:checked").each(function(){select_matiere.push($(this).val());});
        var select_prof    = new Array(); $("#f_prof    input:checked").each(function(){select_prof.push($(this).val());});
        // On compare par rapport au tableau js pour savoir ce qui a changé
        var tab_modifs = new Array();
        for ( var key_matiere in select_matiere )
        {
          for ( var key_prof in select_prof )
          {
            var matiere_id = select_matiere[key_matiere];
            var prof_id    = select_prof[key_prof];
            if( ( (tab_join[matiere_id][prof_id]==0) && (action=='ajouter') ) || ( (tab_join[matiere_id][prof_id]>0) && (action=='retirer') ) )
            {
              tab_modifs.push(matiere_id+'_'+prof_id);
            }
          }
        }
        if(!tab_modifs.length)
        {
          $('#ajax_msg').removeAttr("class").addClass("erreur").html("Aucune nouveauté détectée !");
          return false;
        }
        // On envoie les changements
        $('#form_select button').prop('disabled',true);
        $('#ajax_msg').removeAttr("class").addClass("loader").html("En cours&hellip;");
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&f_action='+action+'&tab_modifs='+tab_modifs,
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $('#form_select button').prop('disabled',false);
              $('#ajax_msg').removeAttr("class").addClass("alerte").html("Échec de la connexion !");
              return false;
            },
            success : function(responseHTML)
            {
              initialiser_compteur();
              $('#form_select button').prop('disabled',false);
              if(responseHTML!='ok')
              {
                $('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
              }
              else
              {
                $('#ajax_msg').removeAttr("class").addClass("valide").html("Demande réalisée !");
                maj_tableaux(action,tab_modifs);
              }
            }
          }
        );
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Mettre à jour les tableaux bilans et le javascript
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    function maj_tableaux(action,tab_modifs)
    {
      var total_matiere = new Array();
      var total_prof    = new Array();
      // lignes et javascript
      for ( var key in tab_modifs )
      {
        var id_modifs  = tab_modifs[key].split('_');
        var matiere_id = id_modifs[0];
        var prof_id    = id_modifs[1];
        id_modifs[0];
        if(action=='ajouter')
        {
          var prof_nom    = $('#f_prof_'+prof_id      ).parent().text();
          var matiere_nom = $('#f_matiere_'+matiere_id).parent().text();
          $('#mpb_'+matiere_id).append('<div id="mp_'+matiere_id+'_'+prof_id+'" class="off"><input type="checkbox" id="'+matiere_id+'cp'+prof_id+'" value="" /> <label for="'+matiere_id+'cp'+prof_id+'">'+prof_nom+'</label></div>');
          $('#pmb_'+prof_id   ).append('<div id="pm_'+prof_id+'_'+matiere_id+'" class="off"><input type="checkbox" id="'+prof_id+'pc'+matiere_id+'" value="" /> <label for="'+prof_id+'pc'+matiere_id+'">'+matiere_nom+'</label></div>');
          tab_join[matiere_id][prof_id] = 1;
        }
        else if(action=='retirer')
        {
          $('#mp_'+matiere_id+'_'+prof_id).remove();
          $('#pm_'+prof_id+'_'+matiere_id).remove();
          tab_join[matiere_id][prof_id] = 0;
        }
        total_matiere[matiere_id] = true;
        total_prof[prof_id]       = true;
      }
      // totaux
      for ( var matiere_id in total_matiere )
      {
        var nb_profs = $('#mpb_'+matiere_id+' div').length;
        var s_profs = (nb_profs>1) ? 's' : '' ;
        $('#mpf_'+matiere_id).html(nb_profs+' professeur'+s_profs);
      }
      for ( var prof_id in total_prof )
      {
        var nb_matieres = $('#pmb_'+prof_id+' div').length;
        var s_matieres = (nb_matieres>1) ? 's' : '' ;
        $('#pmf_'+prof_id).html(nb_matieres+' matière'+s_matieres);
      }
    }

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Ajouter / Retirer une affectation en tant que professeur coordonnateur
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('table.affectation').on
    (
      'click',
      'input[type=checkbox]',
      function()
      {
        var obj_bouton = $(this);
        var action     = (obj_bouton.is(':checked')) ? 'ajouter_coord' : 'retirer_coord' ;
        var tab_id     = obj_bouton.parent().attr('id').split('_');
        var prof_id    = (tab_id[0]=='pm') ? tab_id[1] : tab_id[2] ;
        var matiere_id  = (tab_id[0]=='mp') ? tab_id[1] : tab_id[2] ;
        var check_old  = (action=='ajouter_coord') ? false : true ;
        var check_new  = (action=='ajouter_coord') ? true : false ;
        var class_old  = (action=='ajouter_coord') ? 'off' : 'on' ;
        var class_new  = (action=='ajouter_coord') ? 'on' : 'off' ;
        var js_val     = (action=='ajouter_pp') ? 2 : 1 ;
        obj_bouton.prop('disabled',true).parent().removeAttr('class').addClass('load');
        $.ajax
        (
          {
            type : 'POST',
            url  : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&f_action='+action+'&prof_id='+prof_id+'&matiere_id='+matiere_id,
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              obj_bouton.prop('disabled',false).prop('checked',check_old).parent().removeAttr('class').addClass(class_old);
              $.fancybox( '<label class="alerte">'+'Échec de la connexion !\nVeuillez recommencer.'+'</label>' , {'centerOnScroll':true} );
              return false;
            },
            success : function(responseHTML)
            {
              if(responseHTML!='ok')
              {
                $.fancybox( '<label class="alerte">'+responseHTML+'</label>' , {'centerOnScroll':true} );
                obj_bouton.prop('disabled',false).prop('checked',check_old).parent().removeAttr('class').addClass(class_old);
              }
              else
              {
                obj_bouton.prop('disabled',false).parent().removeAttr('class').addClass(class_new);
                // MAJ tableaux bilans et javascript
                var id_autre = (tab_id[0]=='mp') ? prof_id+'pm'+matiere_id : matiere_id+'mp'+prof_id ;
                $('#'+id_autre).prop('checked',check_new).parent().removeAttr('class').addClass(class_new);
                tab_join[matiere_id][prof_id] = js_val;
              }
            }
          }
        );
      }
    );

  }
);
