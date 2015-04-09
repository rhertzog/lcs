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

    var matiere_items_requis = false;
    var socle_item_requis    = false;
    var socle_pilier_requis  = false;
    var acquisition_requis   = false;
    var validation_requis    = false;
    var coef_requis          = false;
    var mode_requis          = false;
    var mode_manuel          = false;

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Afficher masquer des éléments du formulaire
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#f_critere_objet').change
    (
      function()
      {
        var objet = $(this).val();
        // item(s) / compétence
        if(objet.indexOf('matiere_items')!=-1) {$('#span_matiere_items').show();matiere_items_requis = true;} else {$('#span_matiere_items').hide();matiere_items_requis = false;}
        if(objet.indexOf('socle_item')!=-1)    {$('#span_socle_item').show();socle_item_requis = true;}       else {$('#span_socle_item').hide();socle_item_requis = false;}
        if(objet.indexOf('socle_pilier')!=-1)  {$('#span_socle_pilier').show();socle_pilier_requis = true;}   else {$('#span_socle_pilier').hide();socle_pilier_requis = false;}
        // état (acquisition / validation)
        var is_validation = (objet.indexOf('validation')!=-1) ? true : false ;
        if(is_validation)                      {$('#span_validation').show();validation_requis = true;}       else {$('#span_validation').hide();validation_requis = false;}
        if( (!is_validation) && (objet!='') )  {$('#span_acquisition').show();acquisition_requis = true;}     else {$('#span_acquisition').hide();acquisition_requis = false;}
        // mélange des deux
        if(objet=='matiere_items_bilanMS')     {$('#div_matiere_items_bilanMS').show();coef_requis = true;}   else {$('#div_matiere_items_bilanMS').hide();coef_requis = false;}
        if(objet=='socle_item_pourcentage')    {$('#div_socle_item_pourcentage').show();mode_requis = true;}  else {$('#div_socle_item_pourcentage').hide();mode_requis = false;}
        // initialisation
        $('#ajax_msg').removeAttr("class").html("&nbsp;");
        $('#bilan').html("&nbsp;");
      }
    );

    $('#f_mode_auto').click
    (
      function()
      {
        $("#div_matiere").hide();
        mode_manuel = false;
      }
    );

    $('#f_mode_manuel').click
    (
      function()
      {
        $("#div_matiere").show();
        mode_manuel = true;
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Demande pour sélectionner d'une liste d'items mémorisés
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#f_selection_items').change
    (
      function()
      {
        cocher_matieres_items( $("#f_selection_items").val() );
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Clic sur le bouton pour mémoriser un choix d'items
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#f_enregistrer_items').click
    (
      function()
      {
        memoriser_selection_matieres_items( $("#f_liste_items_nom").val() );
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Choisir les items matière : mise en place du formulaire
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    var choisir_matieres_items = function()
    {
      $('#f_selection_items option:first').prop('selected',true);
      cocher_matieres_items( $('#f_matiere_items_liste').val() );
      $.fancybox( { 'href':'#zone_matieres_items' , onStart:function(){$('#zone_matieres_items').css("display","block");} , onClosed:function(){$('#zone_matieres_items').css("display","none");} , 'modal':true , 'centerOnScroll':true } );
    };

    $('#span_matiere_items q').click( choisir_matieres_items );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Choisir un item du socle : mise en place du formulaire
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    var choisir_socle_item = function()
    {
      cocher_socle_item( $('#f_socle_item_id').val() );
      $.fancybox( { 'href':'#zone_socle_item' , onStart:function(){$('#zone_socle_item').css("display","block");} , onClosed:function(){$('#zone_socle_item').css("display","none");} , 'modal':true , 'centerOnScroll':true } );
    };

    $('#span_socle_item q').click( choisir_socle_item );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Clic sur le bouton pour fermer le cadre des items matière (annuler / retour)
    // Clic sur le bouton pour fermer le cadre des items du socle (annuler / retour)
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#annuler_matieres_items , #annuler_socle_item').click
    (
      function()
      {
        $.fancybox.close();
        return false;
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Clic sur le bouton pour valider le choix des items matière
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#valider_matieres_items').click
    (
      function()
      {
        var liste = '';
        var nombre = 0;
        $("#zone_matieres_items input[type=checkbox]:checked").each
        (
          function()
          {
            liste += $(this).val()+'_';
            nombre++;
          }
        );
        var compet_liste  = liste.substring(0,liste.length-1);
        var compet_nombre = (nombre==0) ? '' : ( (nombre>1) ? nombre+' items' : nombre+' item' ) ;
        $('#f_matiere_items_liste').val(compet_liste);
        $('#f_matiere_items_nombre').val(compet_nombre);
        $('#annuler_matieres_items').click();
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Clic sur le bouton pour valider le choix d'un item du socle
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#valider_socle_item').click
    (
      function()
      {
        var socle_id = $("#zone_socle_item input[type=radio]:checked").val();
        if(isNaN(socle_id))  // normalement impossible, sauf si par exemple on triche avec la barre d'outils Web Developer...
        {
          socle_id = 0;
          var socle_nom = '';
        }
        else
        {
          var socle_nom = $("#zone_socle_item input[type=radio]:checked").parent().text();
        }
        $('#f_socle_item_nom').val(socle_nom);
        $('#f_socle_item_id').val(socle_id);
        $('#annuler_socle_item').click();
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Tout cocher ou tout décocher
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#bilan').on
    (
      'click',
      'q.cocher_tout , q.cocher_rien',
      function()
      {
        var etat = ( $(this).attr('class').substring(7) == 'tout' ) ? true : false ;
        $('#form_synthese td.nu input[type=checkbox]').prop('checked',etat);
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Soumettre le formulaire principal
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    // Le formulaire qui va être analysé et traité en AJAX
    var formulaire = $("#form_select");

    // Vérifier la validité du formulaire (avec jquery.validate.js)
    var validation = formulaire.validate
    (
      {
        rules :
        {
          f_groupe                   : { required:true },
          f_critere_objet            : { required:true },
          f_matiere_items_liste      : { required:function(){return matiere_items_requis;} },
          f_socle_item_id            : { required:function(){return socle_item_requis;} , min:1 },
          f_select_pilier            : { required:function(){return socle_pilier_requis;} },
          f_mode                     : { required:function(){return mode_requis;} },
          'f_matiere[]'              : { required:function(){return mode_manuel;} },
          'f_critere_seuil_acquis[]' : { required:function(){return acquisition_requis;} , maxlength:3 },
          'f_critere_seuil_valide[]' : { required:function(){return validation_requis;} , maxlength:2 }
        },
        messages :
        {
          f_groupe                   : { required:"groupe manquant" },
          f_critere_objet            : { required:"objet manquant" },
          f_matiere_items_liste      : { required:"item(s) manquant(s)" },
          f_socle_item_id            : { required:"item manquant" , min:"item manquant" },
          f_select_pilier            : { required:"compétence manquante" },
          f_mode                     : { required:"choix manquant" },
          'f_matiere[]'              : { required:"matière(s) manquante(s)" },
          'f_critere_seuil_acquis[]' : { required:"états(s) manquant(s)" , maxlength:"trop d'états sélectionnés" },
          'f_critere_seuil_valide[]' : { required:"états(s) manquant(s)" , maxlength:"trop d'états sélectionnés" }
        },
        errorElement : "label",
        errorClass : "erreur",
        errorPlacement : function(error,element)
        {
          if(element.is("select"))                  {element.after(error);}
          else if(element.attr("type")=="text")     {element.next().after(error);}
          else if(element.attr("type")=="radio")    {element.parent().next().after(error);}
          else if(element.attr("type")=="checkbox") {element.parent().parent().next().after(error);}
        }
        // success: function(label) {label.text("ok").removeAttr("class").addClass("valide");} Pas pour des champs soumis à vérification PHP
      }
    );

    // Options d'envoi du formulaire (avec jquery.form.js)
    var ajaxOptions =
    {
      url : 'ajax.php?page='+PAGE+'&csrf='+CSRF,
      type : 'POST',
      dataType : "html",
      clearForm : false,
      resetForm : false,
      target : "#ajax_msg",
      beforeSubmit : test_form_avant_envoi,
      error : retour_form_erreur,
      success : retour_form_valide
    };

    // Envoi du formulaire (avec jquery.form.js)
    formulaire.submit
    (
      function()
      {
        // récupération de l'id, du type et du nom du groupe
        var groupe_val = $("#f_groupe option:selected").val();
        var groupe_nom = $("#f_groupe option:selected").val();
        // Pour un directeur ou un administrateur, groupe_val est de la forme d3 / n2 / c51 / g44
        if(isNaN(parseInt(groupe_val,10)))
        {
          var groupe_type = groupe_val.substring(0,1);
          var groupe_id   = groupe_val.substring(1);
        }
        // Pour un professeur, groupe_val est un entier, et il faut récupérer la 1ère lettre du label parent
        else
        {
          var groupe_type = $("#f_groupe option:selected").parent().attr('label').substring(0,1).toLowerCase();
          var groupe_id   = groupe_val;
        }
        $('#f_groupe_id').val( groupe_id );
        $('#f_groupe_type').val( groupe_type );
        $('#f_groupe_nom').val( groupe_nom );
        $(this).ajaxSubmit(ajaxOptions);
        return false;
      }
    ); 

    // Fonction précédent l'envoi du formulaire (avec jquery.form.js)
    function test_form_avant_envoi(formData, jqForm, options)
    {
      $('#ajax_msg').removeAttr("class").html("&nbsp;");
      var readytogo = validation.form();
      if(readytogo)
      {
        $('button').prop('disabled',true);
        $('#ajax_msg').removeAttr("class").addClass("loader").html("En cours&hellip;");
        $('#bilan').html('');
      }
      return readytogo;
    }

    // Fonction suivant l'envoi du formulaire (avec jquery.form.js)
    function retour_form_erreur(jqXHR, textStatus, errorThrown)
    {
      $('button').prop('disabled',false);
      $('#ajax_msg').removeAttr("class").addClass("alerte").html("Échec de la connexion !");
    }

    // Fonction suivant l'envoi du formulaire (avec jquery.form.js)
    function retour_form_valide(responseHTML)
    {
      initialiser_compteur();
      $('button').prop('disabled',false);
      if(responseHTML.substring(0,6)=='<hr />')
      {
        $('#ajax_msg').removeAttr("class").addClass("valide").html("Résultat ci-dessous.");
        $('#bilan').html(responseHTML);
      }
      else
      {
        $('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
      }
    }

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Préparer une évaluation | Constituer un groupe de besoin
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#bilan').on
    (
      'click',
      'button.ajouter',
      function()
      {
        if( $('#form_synthese input[name=id_user\\[\\]]:checked').length )
        {
          $('#check_msg').removeAttr('class').html('');
          $('#form_synthese').attr( 'action' , './index.php?page='+$(this).attr('name') );
          $('#form_synthese').submit();
        }
        else
        {
          $('#check_msg').removeAttr('class').addClass('alerte').html('Aucun élève coché !');
          return false;
        }
      }
    );

  }
);
