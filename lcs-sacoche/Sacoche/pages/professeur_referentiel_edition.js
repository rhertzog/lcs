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

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Permettre l'utilisation de caractères spéciaux
// ////////////////////////////////////////////////////////////////////////////////////////////////////

var tab_entite_nom   = new Array('&sup2;','&sup3;','&times;','&divide;','&minus;','&pi;','&rarr;','&radic;','&infin;','&asymp;','&ne;','&le;','&ge;');
var tab_entite_val   = new Array('²'     ,'³'     ,'×'      ,'÷'       ,'–'      ,'π'   ,'→'     ,'√'      ,'∞'      ,'≈'      ,'≠'   ,'≤'   ,'≥'   );
var imax             = tab_entite_nom.length;
var memo_text_delete = '';
var memo_objet       = null;
function entity_convert(string)
{
  for(i=0;i<imax;i++)
  {
    var reg = new RegExp(tab_entite_nom[i],"g");
    string = string.replace(reg,tab_entite_val[i]);
  }
  return string;
}

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Pour mémoriser les liens des ressources avant que le tooltip ne bouffe les title.
// ////////////////////////////////////////////////////////////////////////////////////////////////////

var tab_ressources = new Array();

// jQuery !
$(document).ready
(
  function()
  {

    // initialisation
    var matiere_id = 0;
    var objet = false;
    var images = new Array();
    images[1]  = '';
    images[1] += '<q class="n1_edit" data-action="edit" title="Renommer ce domaine (avec sa référence)."></q>';
    images[1] += '<q class="n1_add"  data-action="add"  title="Ajouter un domaine à la suite."></q>';
    images[1] += '<q class="n1_move" data-action="move" title="Déplacer ce domaine (et renuméroter)."></q>';
    images[1] += '<q class="n1_del"  data-action="del"  title="Supprimer ce domaine ainsi que tout son contenu."></q>';
    images[1] += '<q class="n2_add"  data-action="add"  title="Ajouter un thème au début de ce domaine (et renuméroter)."></q>';
    images[2]  = '';
    images[2] += '<q class="n2_edit" data-action="edit" title="Renommer ce thème."></q>';
    images[2] += '<q class="n2_add"  data-action="add"  title="Ajouter un thème à la suite (et renuméroter)."></q>';
    images[2] += '<q class="n2_move" data-action="move" title="Déplacer ce thème (et renuméroter)."></q>';
    images[2] += '<q class="n2_del"  data-action="del"  title="Supprimer ce thème ainsi que tout son contenu (et renuméroter)."></q>';
    images[2] += '<q class="n3_add"  data-action="add"  title="Ajouter un item au début de ce thème (et renuméroter)."></q>';
    images[3]  = '';
    images[3] += '<q class="n3_edit" data-action="edit" title="Renommer, coefficienter, autoriser cet item."></q>';
    images[3] += '<q class="n3_add"  data-action="add"  title="Ajouter un item à la suite (et renuméroter)."></q>';
    images[3] += '<q class="n3_move" data-action="move" title="Déplacer cet item (et renuméroter)."></q>';
    images[3] += '<q class="n3_fus"  data-action="fus"  title="Fusionner avec un autre item (et renuméroter)."></q>';
    images[3] += '<q class="n3_del"  data-action="del"  title="Supprimer cet item (et renuméroter)."></q>';

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Charger le form zone_elaboration_referentiel en ajax
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_choix_referentiel q.modifier').click
    (
      function()
      {
        id = $(this).parent().attr('id');
        matiere_id  = id.substring(3);
        matiere_nom = $(this).parent().prev().prev().text();
        afficher_masquer_images_action('hide');
        new_label = '<label for="'+id+'" class="loader">Demande envoyée...</label>';
        $(this).after(new_label);
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&action=Voir'+'&matiere='+matiere_id,
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $.fancybox( '<label class="alerte">'+'Échec de la connexion !'+'</label>' , {'centerOnScroll':true} );
              $('label[for='+id+']').remove();
              afficher_masquer_images_action('show');
            },
            success : function(responseHTML)
            {
              initialiser_compteur();
              if(responseHTML.substring(0,16)!='<ul class="ul_m1')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
              {
                $.fancybox( '<label class="alerte">'+responseHTML+'</label>' , {'centerOnScroll':true} );
              }
              else
              {
                $('#zone_choix_referentiel').hide();
                initialiser_action_groupe();
                $('#zone_elaboration_referentiel').html('<p><span class="tab"></span>Tout déployer / contracter :<q class="deployer_m2"></q><q class="deployer_n1"></q><q class="deployer_n2"></q><q class="deployer_n3"></q><br /><span class="tab"></span><button id="fermer_zone_elaboration_referentiel" type="button" class="retourner">Retour à la liste des matières</button></p>'+'<h2>'+matiere_nom+'</h2>'+responseHTML);
                // Récupérer le contenu des title des ressources avant que le tooltip ne les enlève
                $('#zone_elaboration_referentiel li.li_n3').each
                (
                  function()
                  {
                    id2 = $(this).attr('id').substring(3);
                    titre = $(this).children('b').children('img:eq(3)').attr('title');
                    tab_ressources[id2] = (titre=='Absence de ressource.') ? '' : titre ;
                  }
                );
              }
              $('label[for='+id+']').remove();
              afficher_masquer_images_action('show');
            }
          }
        );
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur le bouton pour fermer la zone compet
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      '#fermer_zone_elaboration_referentiel',
      function()
      {
        $('#zone_elaboration_referentiel').html("&nbsp;");
        afficher_masquer_images_action('show'); // au cas où on serait en train d'éditer qq chose
        $('#zone_choix_referentiel').show('fast');
        return false;
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour Ajouter un domaine, ou un thème, ou un item
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q[data-action=add]',
      function()
      {
        // On récupère le contexte de la demande : n1 ou n2 ou n3
        contexte = $(this).attr('class').substring(0,2);
        afficher_masquer_images_action('hide');
        // On créé le formulaire à valider
        new_li  = '<li class="li_'+contexte+'">';
        switch(contexte)
        {
          case 'n1' :  // domaine
            new_li += '<i>Ref.</i> <input id="f_ref" name="f_ref" size="1" maxlength="1" type="text" value="" /> <i>Nom</i> <input id="f_nom" name="f_nom" size="100" maxlength="128" type="text" value="" /> <img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Indiquer une lettre référence et un nom de domaine." />';
            texte = 'ce domaine';
            break;
          case 'n2' :  // thème
            new_li += '<i>Nom</i> <input id="f_nom" name="f_nom" size="100" maxlength="128" type="text" value="" /> <img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Indiquer un nom de thème." />';
            texte = 'ce thème';
            break;
          case 'n3' :  // item
            new_li += '<i class="tab"><img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Indiquer un nom d\'item." /> Nom</i><input id="f_nom" name="f_nom" size="125" maxlength="256" type="text" value="" /><br />';
            new_li += '<i class="tab"><img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Appartenance éventuelle au socle commun." /> Socle</i><input id="f_intitule" name="f_intitule" size="90" maxlength="256" type="text" value="Hors-socle." readonly /><input id="f_socle" name="f_socle" type="hidden" value="0" /><q class="choisir_compet" title="Sélectionner un item du socle commun."></q><br />';
            new_li += '<i class="tab"><img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Coefficient facultatif (entier entre 0 et 20)." /> Coef.</i><input id="f_coef" name="f_coef" type="text" value="1" size="1" maxlength="2" class="sep" />';
            new_li += '<i>Demande</i> <input id="f_cart1" name="f_cart" type="radio" value="1" checked /><label for="f_cart1"><img src="./_img/etat/cart_oui.png" title="Demande possible." /></label> <input id="f_cart0" name="f_cart" type="radio" value="0" /><label for="f_cart0" class="sep"><img src="./_img/etat/cart_non.png" title="Demande interdite." /></label>';
            new_li += 'Lien <img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Utiliser la page &#34;Associer des ressources aux items&#34; pour affecter à l\'item un lien vers des ressources (entraînement, remédiation&hellip;)." class="sep" />';
            new_li += '<i>Action</i> ';
            texte = 'cet item';
            break;
          default :
            texte = '???';
        }
        new_li += '<q class="valider" data-action="ajouter" title="Valider l\'ajout de '+texte+'."></q><q class="annuler" data-action="ajouter" title="Annuler l\'ajout de '+texte+'."></q> <label id="ajax_msg">&nbsp;</label>';
        new_li += '</li>';
        // On insère le formulaire dans la page
        if($(this).parent().attr('id').substring(0,2)==contexte)
        {
          // A ajouter à la suite d'un autre élément de même contexte
          $(this).parent().after(new_li);
        }
        else
        {
          // A ajouter au début d'un contexte supérieur
          $(this).next().show().prepend(new_li);
        }
        if(contexte=='n1')
        {
          $('#f_ref').focus();
        }
        else
        {
          $('#f_nom').focus();
        }
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour Éditer un domaine, ou un thème, ou un item
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q[data-action=edit]',
      function()
      {
        // On récupère le contexte de la demande : n1 ou n2 ou n3
        contexte = $(this).attr('class').substring(0,2);
        afficher_masquer_images_action('hide');
        // On créé le formulaire à valider
        new_div  = '<div id="form_edit">';
        switch(contexte)
        {
          case 'n1' :  // domaine
            // on récupère la référence et le nom
            span = $(this).parent().children('span').text();
            ref = span.charAt(0);
            nom = span.substring(4);
            new_div += '<i>Ref.</i> <input id="f_ref" name="f_ref" size="1" maxlength="1" type="text" value="'+ref+'" /> <i>Nom</i> <input id="f_nom" name="f_nom" size="'+Math.min(10+nom.length,118)+'" maxlength="128" type="text" value="'+escapeQuote(nom)+'" /> <img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Indiquer une lettre référence et un nom de domaine." />';
            texte = 'ce domaine';
            break;
          case 'n2' :  // thème
            // On récupère le nom
            nom = $(this).parent().children('span').text();
            new_div += '<i>Nom</i> <input id="f_nom" name="f_nom" size="'+Math.min(10+nom.length,128)+'" maxlength="128" type="text" value="'+escapeQuote(nom)+'" /> <img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Indiquer un nom de thème." />';
            texte = 'ce thème';
            break;
          case 'n3' :  // item
            // On récupère le nom
            nom = $(this).parent().children('b').text();
            // On récupère le coefficient
            adresse = $(this).parent().children('b').children('img:eq(0)').attr('src');
            coef = parseInt( adresse.substr(adresse.length-6,2) , 10 );
            // On récupère l'autorisation de demande
            adresse = $(this).parent().children('b').children('img:eq(1)').attr('src');
            cart = adresse.substr(adresse.length-7,3);
            check1 = (cart=='oui') ? ' checked' : '' ;
            check0 = (cart=='non') ? ' checked' : '' ;
            // On récupère le socle
            socle_id  = $(this).parent().children('b').children('img:eq(2)').data('id');
            socle_txt = $('label[for=socle_'+socle_id+']').text();
            // On assemble
            new_div += '<i class="tab"><img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Indiquer un nom d\'item." /> Nom</i><input id="f_nom" name="f_nom" size="'+Math.min(10+nom.length,128)+'" maxlength="256" type="text" value="'+escapeQuote(nom)+'" /><br />';
            new_div += '<i class="tab"><img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Appartenance éventuelle au socle commun." /> Socle</i><input id="f_intitule" name="f_intitule" size="90" maxlength="256" type="text" value="'+socle_txt+'" readonly /><input id="f_socle" name="f_socle" type="hidden" value="'+socle_id+'" /><q class="choisir_compet" title="Sélectionner un item du socle commun."></q><br />';
            new_div += '<i class="tab"><img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Coefficient facultatif (entier entre 0 et 20)." /> Coef.</i><input id="f_coef" name="f_coef" type="text" value="'+coef+'" size="1" maxlength="2" class="sep" />';
            new_div += '<i>Demande</i> <input id="f_cart1" name="f_cart" type="radio" value="1"'+check1+' /><label for="f_cart1"><img src="./_img/etat/cart_oui.png" title="Demande possible." /></label> <input id="f_cart0" name="f_cart" type="radio" value="0"'+check0+' /><label for="f_cart0" class="sep"><img src="./_img/etat/cart_non.png" title="Demande interdite." /></label>';
            new_div += 'Lien <img alt="" src="./_img/bulle_aide.png" width="16" height="16" title="Utiliser la page &#34;Associer des ressources aux items&#34; pour affecter à l\'item un lien vers des ressources (entraînement, remédiation&hellip;)." class="sep" />';
            new_div += '<i>Action</i>';
            texte = 'cet item';
            break;
          default :
            texte = '???';
        }
        new_div += '<q class="valider" data-action="editer" title="Valider la modification de '+texte+'."></q><q class="annuler" data-action="editer" title="Annuler la modification de '+texte+'."></q> <label id="ajax_msg">&nbsp;</label>';
        new_div += '</div>';
        // On insère le formulaire dans la page
        if(contexte=='n3')
        {
          $(this).before(new_div).parent().children('b').hide();
        }
        else
        {
          $(this).before(new_div).parent().children('span').hide();
        }
        if(contexte=='n1')
        {
          $('#f_ref').focus();
        }
        else
        {
          $('#f_nom').focus();
        }
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour Supprimer un domaine (avec son contenu), ou un thème (avec son contenu), ou un item
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q[data-action=del]',
      function()
      {
        // On récupère le contexte de la demande : n1 ou n2 ou n3
        contexte = $(this).attr('class').substring(0,2);
        afficher_masquer_images_action('hide');
        // On créé le formulaire à valider
        var matiere_nom = $('#zone_elaboration_referentiel h2').text();
        switch(contexte)
        {
          case 'n1' :  // domaine
            alerte = 'Tout le contenu de ce domaine ainsi que tous les résultats des items concernés seront perdus !';
            texte1 = 'ce domaine';
            texte2 = 'le domaine'+' &laquo;&nbsp;'+matiere_nom+'&nbsp;||&nbsp;'+$(this).parent().children('span').text()+'&nbsp;&raquo;';
            break;
          case 'n2' :  // thème
            alerte = 'Tout le contenu de ce thème ainsi que les résultats des items concernés seront perdus (et les thèmes suivants seront renumérotés) !';
            texte1 = 'ce thème';
            texte2 = 'le thème'+' &laquo;&nbsp;'+matiere_nom+'&nbsp;||&nbsp;'+$(this).parent().children('span').text()+'&nbsp;&raquo;';
            break;
          case 'n3' :  // item
            alerte = 'Tous les résultats associés seront perdus et les items suivants seront renumérotés !';
            texte1 = 'cet item';
            texte2 = 'l\'item sélectionné';
            break;
          default :
            alerte = '???';
            texte1 = '???';
            texte2 = '???';
        }
        memo_text_delete = texte2;
        new_div = '<div id="form_del" class="danger">'+alerte;  // un div.danger est utilisé au lieu du span.danger car un clic sur un span enroule/déroule le contenu
        new_div += '<q class="valider" data-action="supprimer" title="Valider la suppression de '+texte1+'."></q><q class="annuler" data-action="supprimer" title="Annuler la suppression de '+texte1+'."></q> <label id="ajax_msg">&nbsp;</label>';
        new_div += '</div>';
        // On insère le formulaire dans la page
        if(contexte=='n3')
        {
          $(this).before(new_div).parent().children('b').hide();
        }
        else
        {
          $(this).before(new_div).parent().children('span').hide();
        }
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour Fusionner deux items
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q[data-action=fus]',
      function()
      {
        afficher_masquer_images_action('hide');
        // On ajoute les boutons à cocher
        id = $(this).parent().attr('id');
        $('#zone_elaboration_referentiel li.li_n3').each( function(){ if($(this).attr('id')!=id){$(this).children('b').after('<q class="n3_fus2" data-action="fus2" title="Valider l\'absorption de l\'item choisi en 1er par celui-ci."></q>');} } );
        new_img = '<q class="annuler" data-action="fusionner" title="Annuler la fusion de cet item."></q><label id="ajax_msg">&nbsp;</label>';
        // On insère le formulaire dans la page
        $(this).after(new_img);
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour Déplacer un domaine (avec son contenu), ou un thème (avec son contenu), ou un item
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q[data-action=move]',
      function()
      {
        // On récupère le contexte de la demande : n1 ou n2 ou n3
        contexte = $(this).attr('class').substring(0,2);
        afficher_masquer_images_action('hide');
        // On ajoute les boutons à cocher
        id = $(this).parent().attr('id');
        switch(contexte)
        {
          case 'n1' :  // domaine
            $('#zone_elaboration_referentiel li.li_m2').each( function(){ $(this).children('span').after('<q class="n1_move2" data-action="move2" title="Valider le déplacement du domaine au début de ce niveau."></q>'); } );
            $('#zone_elaboration_referentiel li.li_n1').each( function(){ if($(this).attr('id')!=id){$(this).children('span').after('<q class="n1_move2" data-action="move2" title="Valider le déplacement du domaine à la suite de celui-ci."></q>');} } );
            break;
          case 'n2' :  // thème
            $('#zone_elaboration_referentiel li.li_n1').each( function(){ $(this).children('span').after('<q class="n2_move2" data-action="move2" title="Valider le déplacement du thème au début de ce domaine (et renuméroter)."></q>'); } );
            $('#zone_elaboration_referentiel li.li_n2').each( function(){ if($(this).attr('id')!=id){$(this).children('span').after('<q class="n2_move2" data-action="move2" title="Valider le déplacement du thème à la suite de celui-ci."></q>');} } );
            break;
          case 'n3' :  // item
            $('#zone_elaboration_referentiel li.li_n2').each( function(){ $(this).children('span').after('<q class="n3_move2" data-action="move2" title="Valider le déplacement de l\'item au début de ce thème (et renuméroter)."></q>'); } );
            $('#zone_elaboration_referentiel li.li_n3').each( function(){ if($(this).attr('id')!=id){$(this).children('b').after('<q class="n3_move2" data-action="move2" title="Valider le déplacement de l\'item à la suite de celui-ci."></q>');} } );
            break;
        }
        // On créé le formulaire à valider
        switch(contexte)
        {
          case 'n1' :  // domaine
            texte = 'ce domaine';
            break;
          case 'n2' :  // thème
            texte = 'ce thème';
            break;
          case 'n3' :  // item
            texte = 'cet item';
            break;
          default :
            texte = '???';
        }
        new_img = '<q class="annuler" data-action="deplacer" title="Annuler le déplacement de '+texte+'."></q><label id="ajax_msg">&nbsp;</label>';
        // On insère le formulaire dans la page
        $(this).after(new_img);
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour afficher les items du socle
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q.choisir_compet',
      function()
      {
        // récupérer le nom de l'item et le reporter
        item_nom = escapeHtml( entity_convert( $('#f_nom').val() ) );
        $('#zone_socle_item span.f_nom').html(item_nom);
        // récupérer la relation au socle commun et la cocher
        cocher_socle_item( $('#f_socle').val() );
        // montrer le cadre
        $.fancybox( { 'href':'#zone_socle_item' , onStart:function(){$('#zone_socle_item').css("display","block");} , onClosed:function(){$('#zone_socle_item').css("display","none");} , 'modal':true , 'centerOnScroll':true } );
        objet = 'choisir_compet';
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur le bouton pour confirmer la relation au socle d'un item
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#choisir_socle_valider').click
    (
      function()
      {
        // récupérer la relation au socle (id + nom)
        socle_id = $("#zone_socle_item input[type=radio]:checked").val();
        if(isNaN(socle_id))  // normalement impossible, sauf si par exemple on triche avec la barre d'outils Web Developer...
        {
          socle_id = 0;
          socle_nom = 'Hors-socle.';
        }
        else
        {
          socle_nom = $("#zone_socle_item input[type=radio]:checked").parent('label').text();
        }
        // L'envoyer dans le formulaire
        $('#f_socle').val(socle_id);
        $('#f_intitule').val(socle_nom);
        // masquer le cadre
        $.fancybox.close();
        objet = 'editer';
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur le bouton pour Annuler le choix dans le socle
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#choisir_socle_annuler').click
    (
      function()
      {
        $.fancybox.close();
        objet = 'editer';
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour confirmer l'ajout d'un domaine, ou d'un thème, ou d'un item
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q.valider[data-action=ajouter]',
      function()
      {
        // On récupère le contexte de la demande : n1 ou n2 ou n3
        contexte = $(this).parent().attr('class').substring(3,5);
        // On récupère la référence de l'élément (domaine uniquement)
        if(contexte=='n1')
        {
          ref = $('#f_ref').val();
          if(ref=='')
          {
            $('#ajax_msg').removeAttr("class").addClass("erreur").html("Référence manquante !");
            $('#f_ref').focus();
            return false;
          }
          if('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'.indexOf(ref)==-1)
          {
            $('#ajax_msg').removeAttr("class").addClass("erreur").html("La référence doit être une lettre ou un chiffre !");
            $('#f_ref').focus();
            return false;
          }
        }
        else
        {
          ref = '';
        }
        // On récupère le nom de l'élément
        nom = entity_convert($('#f_nom').val());
        if(nom=='')
        {
          $('#ajax_msg').removeAttr("class").addClass("erreur").html("Nom manquant !");
          $('#f_nom').focus();
          return false;
        }
        // On récupère le coefficient, l'autorisation de demande, le lien au socle et le lien de ressources de l'élément (item uniquement)
        if(contexte=='n3')
        {
          coef  = parseInt( $('#f_coef').val() , 10 );
          cart  = $("input[name=f_cart]:checked").val();
          socle = $('#f_socle').val();
          if( (isNaN(coef)) || (coef<0) || (coef>20) )
          {
            $('#ajax_msg').removeAttr("class").addClass("erreur").html("Le coefficient doit être un nombre entier entre 0 et 20 !");
            $('#f_coef').focus();
            return false;
          }
          if(isNaN(cart))  // normalement impossible, sauf si par exemple on triche avec la barre d'outils Web Developer...
          {
            $('#ajax_msg').removeAttr("class").addClass("erreur").html("Cocher si l'élève peut ou non demander une évaluation !");
            return false;
          }
        }
        else
        {
          coef  = 1;
          cart  = 0;
          socle = 0;
        }
        // On récupère l'id de l'élément parent concerné (niveau ou domaine ou theme)
        parent_id = $(this).parent().parent().parent().attr('id').substring(3);
        // On calcule le n° d'ordre de l'élément à partir de la recherche du nb d'éléments précédents pour l'élément parent concerné
        li = $(this).parent();
        ordre = (contexte=='n3') ? 0 : 1;
        while(li.prev().length)
        {
          li = li.prev();
          ordre++;
        }
        // On récupère la liste des éléments suivants dont il faudra augmenter l'ordre
        li = $(this).parent();
        tab_id = new Array();
        while(li.next().length)
        {
          li = li.next();
          tab_id.push(li.attr('id').substring(3));
        }
        // Envoi des infos en ajax pour le traitement de la demande
        $('#ajax_msg').removeAttr("class").addClass("loader").html("En cours&hellip;");
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&action=add'+'&contexte='+contexte+'&matiere='+matiere_id+'&parent='+parent_id+'&ordre='+ordre+'&tab_id='+tab_id+'&ref='+ref+'&coef='+coef+'&cart='+cart+'&socle='+socle+'&nom='+encodeURIComponent(nom),
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $('#ajax_msg').removeAttr("class").addClass("alerte").html('Échec de la connexion !');
            },
            success : function(responseHTML)
            {
              initialiser_compteur();
              if(responseHTML.substring(0,2)==contexte)  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
              {
                switch(contexte)
                {
                  case 'n1' :  // domaine
                    texte = '<span>' + ref + ' - ' + escapeHtml(nom) + '</span>' + images[contexte.charAt(1)] + '<ul class="ul_n2"></ul>';
                    break;
                  case 'n2' :  // thème
                    texte = '<span>' + escapeHtml(nom) + '</span>' + images[contexte.charAt(1)] + '<ul class="ul_n3"></ul>';
                    break;
                  case 'n3' :  // item
                    coef_image  = (coef<10) ? '0'+coef : coef ;
                    coef_texte  = '<img src="./_img/coef/'+coef_image+'.gif" alt="" title="Coefficient '+coef+'." />';
                    cart_image  = (cart>0) ? 'oui' : 'non' ;
                    cart_title  = (cart>0) ? 'Demande possible.' : 'Demande interdite.' ;
                    cart_texte  = '<img src="./_img/etat/cart_'+cart_image+'.png" title="'+cart_title+'" />';
                    socle_image = (socle>0) ? 'oui' : 'non' ;
                    socle_title = $('#f_intitule').val();
                    socle_texte = '<img src="./_img/etat/socle_'+socle_image+'.png" alt="" title="'+socle_title+'" data-id="'+socle+'" />';
                    lien_image  = 'non';
                    lien_title  = 'Absence de ressource.';
                    lien_texte  = '<img src="./_img/etat/link_'+lien_image+'.png" alt="" title="'+lien_title+'" />';
                    texte = '<b>' + coef_texte + cart_texte + socle_texte + lien_texte + escapeHtml(nom) + '</b>' + images[contexte.charAt(1)];
                    element_id = responseHTML.substring(3);
                    tab_ressources[element_id] = '';
                    break;
                  default :
                    texte = '???';
                }
                $('#ajax_msg').parent().attr('id',responseHTML).html(texte);
                afficher_masquer_images_action('show');
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
// Clic sur l'image pour confirmer l'édition d'un domaine, ou d'un thème, ou d'un item
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q.valider[data-action=editer]',
      function()
      {
        // On récupère le contexte de la demande : n1 ou n2 ou n3
        contexte = $(this).parent().parent().attr('id').substring(0,2);
        // On récupère la référence de l'élément (domaine uniquement)
        if(contexte=='n1')
        {
          ref = $('#f_ref').val();
          if(ref=='')
          {
            $('#ajax_msg').removeAttr("class").addClass("erreur").html("Référence manquante !");
            $('#f_ref').focus();
            return false;
          }
          if('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'.indexOf(ref)==-1)
          {
            $('#ajax_msg').removeAttr("class").addClass("erreur").html("La référence doit être une lettre ou un chiffre !");
            $('#f_ref').focus();
            return false;
          }
        }
        else
        {
          ref = '';
        }
        // On récupère le nom de l'élément
        nom = entity_convert($('#f_nom').val());
        if(nom=='')
        {
          $('#ajax_msg').removeAttr("class").addClass("erreur").html("Nom manquant !");
          $('#f_nom').focus();
          return false;
        }
        // On récupère le coefficient, le lien au socle (item uniquement)
        if(contexte=='n3')
        {
          coef  = parseInt( $('#f_coef').val() , 10 );
          cart  = $("input[name=f_cart]:checked").val();
          socle = $('#f_socle').val();
          if( (isNaN(coef)) || (coef<0) || (coef>20) )
          {
            $('#ajax_msg').removeAttr("class").addClass("erreur").html("Le coefficient doit être un nombre entier entre 0 et 20 !");
            $('#f_coef').focus();
            return false;
          }
          if(isNaN(cart))  // normalement impossible, sauf si par exemple on triche avec la barre d'outils Web Developer...
          {
            $('#ajax_msg').removeAttr("class").addClass("erreur").html("Cocher si l'élève peut ou non demander une évaluation !");
            return false;
          }
        }
        else
        {
          coef  = 1;
          cart  = 0;
          socle = 0;
        }
        // On récupère l'id de l'élément concerné (domaine ou theme ou item)
        element_id = $(this).parent().parent().attr('id').substring(3);
        // Envoi des infos en ajax pour le traitement de la demande
        $('#ajax_msg').removeAttr("class").addClass("loader").html("En cours&hellip;");
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&action=edit'+'&contexte='+contexte+'&element='+element_id+'&ref='+ref+'&coef='+coef+'&cart='+cart+'&socle='+socle+'&nom='+encodeURIComponent(nom),
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $('#ajax_msg').removeAttr("class").addClass("alerte").html('Échec de la connexion !');
            },
            success : function(responseHTML)
            {
              initialiser_compteur();
              if(responseHTML=='ok')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
              {
                texte = (contexte=='n1') ? ref + ' - ' + escapeHtml(nom) : escapeHtml(nom) ;
                if(contexte=='n3')
                {
                  coef_image  = (coef<10) ? '0'+coef : coef ;
                  coef_texte  = '<img src="./_img/coef/'+coef_image+'.gif" alt="" title="Coefficient '+coef+'." />';
                  cart_image  = (cart>0) ? 'oui' : 'non' ;
                  cart_title  = (cart>0) ? 'Demande possible.' : 'Demande interdite.' ;
                  cart_texte  = '<img src="./_img/etat/cart_'+cart_image+'.png" title="'+cart_title+'" />';
                  socle_image = (socle>0) ? 'oui' : 'non' ;
                  socle_title = $('#f_intitule').val();
                  socle_texte = '<img src="./_img/etat/socle_'+socle_image+'.png" alt="" title="'+socle_title+'" data-id="'+socle_id+'" />';
                  lien_image  = (tab_ressources[element_id]) ? 'oui' : 'non' ;
                  lien_title  = (tab_ressources[element_id]) ? tab_ressources[element_id] : 'Absence de ressource.' ;
                  lien_texte  = '<img src="./_img/etat/link_'+lien_image+'.png" alt="" title="'+lien_title+'" />';
                  $('#ajax_msg').parent().parent().children('b').html(coef_texte+cart_texte+socle_texte+lien_texte+texte).show();
                }
                else
                {
                  $('#ajax_msg').parent().parent().children('span').html(texte).show();
                }
                $('#ajax_msg').parent().remove();
                afficher_masquer_images_action('show');
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
// Clic sur l'image pour confirmer la suppression d'un domaine (avec son contenu), ou d'un thème (avec son contenu), ou d'un item
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q.valider[data-action=supprimer]',
      function()
      {
        memo_objet = $(this);
        $.prompt(prompt_etapes);
      }
    );

    var prompt_etapes = {
      etape_2: {
        title   : 'Demande de confirmation (2/3)',
        html    : "Tous les résultats des élèves qui en dépendent seront perdus !<br />Souhaitez-vous vraiment supprimer cet élément de référentiel ?",
        buttons : {
          "Non, c'est une erreur !" : false ,
          "Oui, je confirme !" : true
        },
        submit  : function(event, value, message, formVals) {
          if(value) {
            event.preventDefault();
            $('#referentiel_infos_prompt').html(memo_text_delete);
            $.prompt.goToState('etape_3');
            return false;
          }
          else {
            $('q.annuler').click();
          }
        }
      },
      etape_3: {
        title   : 'Demande de confirmation (3/3)',
        html    : "Attention : dernière demande de confirmation !!!<br />Êtes-vous bien certain de vouloir supprimer "+'<span id="referentiel_infos_prompt"></span>'+" ?<br />Est-ce définitivement votre dernier mot ???",
        buttons : {
          "Oui, j'insiste !" : true ,
          "Non, surtout pas !" : false
        },
        submit  : function(event, value, message, formVals) {
          if(value) {
            envoyer_action_confirmee();
            return true;
          }
          else {
            $('q.annuler').click();
          }
        }
      }
    };

    function envoyer_action_confirmee()
    {
      // On récupère le contexte de la demande : n1 ou n2 ou n3
      contexte = memo_objet.parent().parent().attr('id').substring(0,2);
      // On récupère l'id de l'élément concerné (domaine ou theme ou item)
      element_id = memo_objet.parent().parent().attr('id').substring(3);
      // On récupère la liste des éléments suivants dont il faudra diminuer l'ordre
      li = memo_objet.parent().parent();
      tab_id = new Array();
      while(li.next().length)
      {
        li = li.next();
        tab_id.push(li.attr('id').substring(3));
      }
      // Envoi des infos en ajax pour le traitement de la demande
      $('#ajax_msg').removeAttr("class").addClass("loader").html("En cours&hellip;");
      $.ajax
      (
        {
          type : 'POST',
          url : 'ajax.php?page='+PAGE,
          data : 'csrf='+CSRF+'&action=del'+'&contexte='+contexte+'&element='+element_id+'&tab_id='+tab_id,
          dataType : "html",
          error : function(jqXHR, textStatus, errorThrown)
          {
            $('#ajax_msg').removeAttr("class").addClass("alerte").html('Échec de la connexion !');
          },
          success : function(responseHTML)
          {
            initialiser_compteur();
            if(responseHTML=='ok')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
            {
              $('#ajax_msg').parent().parent().remove();
              afficher_masquer_images_action('show');
            }
            else
            {
              $('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
            }
          }
        }
      );
    }

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour confirmer la fusion d'un item avec un second qui l'absorbe
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q[data-action=fus2]',
      function()
      {
        //
        // Element de départ
        //
        li = $('q.annuler[data-action=fusionner]').parent();
        li_id_depart = li.attr('id');
        element_id = li_id_depart.substring(3);
        // On récupère la liste des éléments suivants dont il faudra diminuer l'ordre
        tab_id = new Array();
        while(li.next().length)
        {
          li = li.next();
          tab_id.push(li.attr('id').substring(3));
        }
        //
        // Element d'arrivée
        //
        li_id_arrivee = $(this).parent().attr('id');
        element2_id = li_id_arrivee.substring(3);
        // Envoi des infos en ajax pour le traitement de la demande
        $('#ajax_msg').removeAttr("class").addClass("loader").html("En cours&hellip;");
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&action=fus'+'&element='+element_id+'&tab_id='+tab_id+'&element2='+element2_id,
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $('#ajax_msg').removeAttr("class").addClass("alerte").html('Échec de la connexion !');
            },
            success : function(responseHTML)
            {
              initialiser_compteur();
              if(responseHTML=='ok')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
              {
                $('#ajax_msg').parent().remove();
                $('q[data-action=fus2]').remove();
                afficher_masquer_images_action('show');
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
// Clic sur l'image pour confirmer le déplacement d'un domaine, ou d'un thème, ou d'un item
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q[data-action=move2]',
      function()
      {
        //
        // Element de départ
        //
        li = $('q.annuler[data-action=deplacer]').parent();
        li_id_depart = li.attr('id');
        // On récupère le contexte de la demande : n1 ou n2 ou n3
        // On récupère l'id de l'élément concerné (domaine ou theme ou item)
        contexte = li_id_depart.substring(0,2);
        element_id = li_id_depart.substring(3);
        // On récupère la liste des éléments suivants dont il faudra diminuer l'ordre
        tab_id = new Array();
        while(li.next().length)
        {
          li = li.next();
          tab_id.push(li.attr('id').substring(3));
        }
        //
        // Element d'arrivée
        //
        li_id_arrivee = $(this).parent().attr('id');
        contexte2 = li_id_arrivee.substring(0,2);
        if(contexte2==contexte)  // Si on demande à l'insérer après un élément de même niveau
        {
          // On récupère l'id de l'élément parent concerné (niveau ou domaine ou theme)
          parent_id = $(this).parent().parent().parent().attr('id').substring(3);
          // On calcule le n° d'ordre de l'élément à partir de la recherche du nb d'éléments précédents pour l'élément parent concerné
          li = $(this).parent();
          ordre = (contexte=='n3') ? 1 : 2;
          while(li.prev().length)
          {
            li = li.prev();
            test_id = li.attr('id').substring(3);
            if(test_id!=element_id)  // sans compter éventuellement celui qui va être déplacé...
            {
              ordre++;
            }
          }
          // On récupère la liste des éléments suivants dont il faudra augmenter l'ordre
          li = $(this).parent();
          tab_id2 = new Array();
          while(li.next().length)
          {
            li = li.next();
            test_id = li.attr('id').substring(3);
            if(test_id!=element_id)  // sans compter éventuellement celui qui va être déplacé...
            {
              tab_id2.push(test_id);
            }
          }
        }
        else  // Si on demande à l'insérer au début d'un élément de niveau supérieur
        {
          // On récupère l'id de l'élément parent concerné (niveau ou domaine ou theme)
          parent_id = $(this).parent().attr('id').substring(3);
          // On calcule le n° d'ordre de l'élément à partir de la recherche du nb d'éléments précédents pour l'élément parent concerné
          ordre = (contexte=='n3') ? 0 : 1;
          // On récupère la liste des éléments suivants dont il faudra augmenter l'ordre
          tab_id2 = new Array();
          $(this).parent().children('ul').children('li').each
          (
            function()
            {
              test_id = $(this).attr('id').substring(3);
              if(test_id!=element_id)  // sans compter éventuellement celui qui va être déplacé...
              {
                tab_id2.push(test_id);
              }
            }
          );
        }
        // Envoi des infos en ajax pour le traitement de la demande
        $('#ajax_msg').removeAttr("class").addClass("loader").html("En cours&hellip;");
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&action=move'+'&contexte='+contexte+'&element='+element_id+'&tab_id='+tab_id+'&parent='+parent_id+'&ordre='+ordre+'&tab_id2='+tab_id2,
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $('#ajax_msg').removeAttr("class").addClass("alerte").html('Échec de la connexion !');
            },
            success : function(responseHTML)
            {
              initialiser_compteur();
              if(responseHTML=='ok')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
              {
                if(contexte2==contexte)  // Si on demande à l'insérer après un élément de même niveau
                {
                  $('#'+li_id_arrivee).after( $('#'+li_id_depart) );
                }
                else  // Si on demande à l'insérer au début d'un élément de niveau supérieur
                {
                  $('#'+li_id_arrivee).children('ul').prepend( $('#'+li_id_depart) );
                }
                $('q.annuler[data-action=deplacer]').remove();
                $('#ajax_msg').remove();
                $('q[data-action=move2]').remove();
                afficher_masquer_images_action('show');
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
// Clic sur l'image pour Annuler un ajout
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q.annuler[data-action=ajouter]',
      function()
      {
        $(this).parent().remove();
        afficher_masquer_images_action('show');
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour Annuler un renommage
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q.annuler[data-action=editer]',
      function()
      {
        $(this).parent().parent().children().show();
        $(this).parent().remove();
        afficher_masquer_images_action('show');
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour Annuler une suppression
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q.annuler[data-action=supprimer]',
      function()
      {
        $(this).parent().parent().children().show();
        $(this).parent().remove();
        afficher_masquer_images_action('show');
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour Annuler une fusion
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q.annuler[data-action=fusionner]',
      function()
      {
        $(this).remove();
        $('#ajax_msg').remove();
        $('q[data-action=fus2]').remove();
        afficher_masquer_images_action('show');
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur l'image pour Annuler un déplacement
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#zone_elaboration_referentiel').on
    (
      'click',
      'q.annuler[data-action=deplacer]',
      function()
      {
        $(this).remove();
        $('#ajax_msg').remove();
        $('q[data-action=move2]').remove();
        afficher_masquer_images_action('show');
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Intercepter la touche entrée ou escape pour valider ou annuler les modifications
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $(document).on
    (
      'keyup',
      'input',
      function(e)
      {
        if(e.which==13)  // touche entrée
        {
          if(objet=='choisir_compet') {$('#choisir_socle_valider').click();}
          else {$('#zone_elaboration_referentiel q.valider').click();}
        }
        else if(e.which==27)  // touche escape
        {
          if(objet=='choisir_compet') {$('#choisir_socle_annuler').click();}
          else {$('#zone_elaboration_referentiel q.annuler').click();}
        }
        return false;
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Gestion des manipulations complémentaires
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    function lister_options_select( granulosite , select_id , matiere_id_a_eviter )
    {
      var id_matieres = (matiere_id_a_eviter) ? listing_id_matieres_autorisees.replace(','+matiere_id_a_eviter+',',',') : listing_id_matieres_autorisees ;
      id_matieres = id_matieres.substring(1,id_matieres.length-1);
      $('#ajax_msg_groupe').removeAttr("class").addClass("loader").html("En cours&hellip;");
      $.ajax
      (
        {
          type : 'POST',
          url : 'ajax.php?page='+PAGE,
          data : 'csrf='+CSRF+'&action=lister_options'+'&granulosite='+granulosite+'&id_matieres='+id_matieres,
          dataType : "html",
          error : function(jqXHR, textStatus, errorThrown)
          {
            $('#ajax_msg_groupe').removeAttr("class").addClass("alerte").html('Échec de la connexion !');
          },
          success : function(responseHTML)
          {
            initialiser_compteur();
            if(responseHTML.substring(0,7)=='<option')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
            {
              $('#ajax_msg_groupe').removeAttr('class').html('');
              $('#'+select_id).html(responseHTML).show(0);
            }
            else
            {
              $('#ajax_msg_groupe').removeAttr("class").addClass("alerte").html(responseHTML);
            }
          }
        }
      );
    }

    var modifier_action_groupe = function()
    {
      var action_groupe = $('#select_action_groupe_choix option:selected').val();
      $('#bouton_valider_groupe').prop('disabled',true);
      if(!action_groupe)
      {
        $('#groupe_modifier_avertissement , #select_action_groupe_modifier_objet , #select_action_groupe_modifier_id , #select_action_groupe_modifier_coef , #select_action_groupe_modifier_cart , #select_action_groupe_deplacer_id_initial , #select_action_deplacer_explication , #select_action_groupe_deplacer_id_final').css("display","none"); // hide(0) ne donne rien si appelé par initialiser_action_groupe()...
        $('#ajax_msg_groupe').removeAttr('class').html('');
      }
      else if( (action_groupe=='modifier_coefficient') || (action_groupe=='modifier_panier') )
      {
        $('#select_action_groupe_modifier_id , #select_action_groupe_modifier_coef , #select_action_groupe_modifier_cart , #select_action_groupe_deplacer_id_initial , #select_action_deplacer_explication , #select_action_groupe_deplacer_id_final').hide(0);
        $('#select_action_groupe_modifier_objet option:first').prop('selected',true);
        $('#select_action_groupe_modifier_objet').show(0);
        $('#ajax_msg_groupe').removeAttr('class').html('');
      }
      else if( (action_groupe=='deplacer_domaine') || (action_groupe=='deplacer_theme') )
      {
        $('#select_action_groupe_modifier_objet , #select_action_groupe_modifier_id , #select_action_groupe_modifier_coef , #select_action_groupe_modifier_cart , #select_action_deplacer_explication , #select_action_groupe_deplacer_id_final').hide(0);
        $('#groupe_modifier_avertissement').show(0);
        $('#select_action_groupe_deplacer_id_initial').html('<option value=""></option>');
        lister_options_select( action_groupe.substring(9) , 'select_action_groupe_deplacer_id_initial' , 0 );
      }
    };

    $("#select_action_groupe_choix").change( modifier_action_groupe );

    function initialiser_action_groupe()
    {
      $('#select_action_groupe_choix option:first').prop('selected',true);
      modifier_action_groupe();
    }

    $("#select_action_groupe_modifier_objet").change
    (
      function()
      {
        var modifier_objet = $('#select_action_groupe_modifier_objet option:selected').val();
        $('#bouton_valider_groupe').prop('disabled',true);
        if(!modifier_objet)
        {
          $('#select_action_groupe_modifier_id , #select_action_groupe_modifier_coef , #select_action_groupe_modifier_cart').hide(0);
          $('#ajax_msg_groupe').removeAttr('class').html('');
        }
        else
        {
          $('#select_action_groupe_modifier_id').html('<option value=""></option>');
          lister_options_select( modifier_objet , 'select_action_groupe_modifier_id' , 0 );
        }
      }
    );

    $("#select_action_groupe_modifier_id").change
    (
      function()
      {
        var action_groupe = $('#select_action_groupe_choix option:selected').val();
        var modifier_id = $('#select_action_groupe_modifier_id option:selected').val();
        $('#bouton_valider_groupe').prop('disabled',true);
        if(!modifier_id)
        {
          $('#select_action_groupe_modifier_coef , #select_action_groupe_modifier_cart').hide(0);
          $('#ajax_msg_groupe').removeAttr('class').html('');
        }
        else
        {
          if(action_groupe=='modifier_coefficient')
          {
            $('#select_action_groupe_modifier_cart').hide(0);
            $('#select_action_groupe_modifier_coef option:first').prop('selected',true);
            $('#select_action_groupe_modifier_coef').show(0);
          }
          else if(action_groupe=='modifier_panier')
          {
            $('#select_action_groupe_modifier_coef').hide(0);
            $('#select_action_groupe_modifier_cart option:first').prop('selected',true);
            $('#select_action_groupe_modifier_cart').show(0);
          }
        }
      }
    );

    $("#select_action_groupe_deplacer_id_initial").change
    (
      function()
      {
        var action_groupe = $('#select_action_groupe_choix option:selected').val();
        var deplacer_id_initial = $('#select_action_groupe_deplacer_id_initial option:selected').val();
        $('#bouton_valider_groupe').prop('disabled',true);
        if(!deplacer_id_initial)
        {
          $('#select_action_deplacer_explication , #select_action_groupe_deplacer_id_final').hide(0);
          $('#ajax_msg_groupe').removeAttr('class').html('');
        }
        else
        {
          var tab_ids = deplacer_id_initial.split('_');
          var matiere_id_a_eviter = tab_ids[0];
          var option_a_desactiver = (action_groupe=='deplacer_domaine') ? 'deplacer_theme' : 'deplacer_domaine' ;
          var option_a_activer    = (action_groupe=='deplacer_theme')   ? 'deplacer_theme' : 'deplacer_domaine' ;
          var granulosite         = (action_groupe=='deplacer_domaine') ? 'referentiel'    : 'domaine' ;
          $('#select_action_deplacer_explication option[value='+option_a_desactiver+']').prop('disabled',true);
          $('#select_action_deplacer_explication option[value='+option_a_activer+']').prop('disabled',false).prop('selected',true);
          $('#select_action_deplacer_explication').show(0);
          $('#select_action_groupe_deplacer_id_final').html('<option value=""></option>');
          lister_options_select( granulosite , 'select_action_groupe_deplacer_id_final' , matiere_id_a_eviter );
        }
      }
    );

    $("#select_action_groupe_modifier_coef").change
    (
      function()
      {
        var modifier_coef = $('#select_action_groupe_modifier_coef option:selected').val();
        var etat_desactive = (modifier_coef==='') ? true : false ;
        $('#bouton_valider_groupe').prop('disabled',etat_desactive);
      }
    );

    $("#select_action_groupe_modifier_cart").change
    (
      function()
      {
        var modifier_cart = $('#select_action_groupe_modifier_cart option:selected').val();
        var etat_desactive = (modifier_cart==='') ? true : false ;
        $('#bouton_valider_groupe').prop('disabled',etat_desactive);
      }
    );

    $("#select_action_groupe_deplacer_id_final").change
    (
      function()
      {
        var deplacer_id_final = $('#select_action_groupe_deplacer_id_final option:selected').val();
        var etat_desactive = (deplacer_id_final) ? false : true ;
        $('#bouton_valider_groupe').prop('disabled',etat_desactive);
      }
    );

    $("#bouton_valider_groupe").click
    (
      function()
      {
        $('#ajax_msg_groupe').removeAttr("class").addClass("loader").html("En cours&hellip;");
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&action=action_complementaire'+'&'+$('#zone_choix_referentiel').serialize(),
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $('#ajax_msg_groupe').removeAttr("class").addClass("alerte").html('Échec de la connexion !');
            },
            success : function(responseHTML)
            {
              initialiser_compteur();
              if(responseHTML=='ok')  // Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
              {
                $('#ajax_msg_groupe').removeAttr("class").addClass("valide").html("Demande réalisée !");
                var action_groupe = $('#select_action_groupe_choix option:selected').val();
                if( (action_groupe=='deplacer_domaine') || (action_groupe=='deplacer_theme') )
                {
                  // maj 1er select éléments de référentiels
                  lister_options_select( action_groupe.substring(9) , 'select_action_groupe_deplacer_id_initial' , 0 );
                  // maj 2e select éléments de référentiels
                  var deplacer_id_initial = $('#select_action_groupe_deplacer_id_initial option:selected').val();
                  var tab_ids = deplacer_id_initial.split('_');
                  var matiere_id_a_eviter = tab_ids[0];
                  var granulosite         = (action_groupe=='deplacer_domaine') ? 'referentiel'    : 'domaine' ;
                  lister_options_select( granulosite , 'select_action_groupe_deplacer_id_final' , matiere_id_a_eviter );
                }
              }
              else
              {
                $('#ajax_msg_groupe').removeAttr("class").addClass("alerte").html(responseHTML);
              }
            }
          }
        );
      }
    );

  }
);
