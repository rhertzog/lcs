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

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Permettre l'utilisation de caractères spéciaux
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
var tab_entite_nom = new Array('&sup2;','&sup3;','&times;','&divide;','&minus;','&pi;','&rarr;','&radic;','&infin;','&asymp;','&ne;','&le;','&ge;');
var tab_entite_val = new Array('²'     ,'³'     ,'×'      ,'÷'       ,'–'      ,'π'   ,'→'     ,'√'      ,'∞'      ,'≈'      ,'≠'   ,'≤'   ,'≥'   );
var imax = tab_entite_nom.length;
function entity_convert(string)
{
	for(i=0;i<imax;i++)
	{
		var reg = new RegExp(tab_entite_nom[i],"g");
		string = string.replace(reg,tab_entite_val[i]);
	}
	return string;
}
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Equivalent javascript de htmlspecialchars() en php pour éviter le pb des < > " saisis dans un input et reportés dans la page
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
function htmlspecialchars(string)
{
	string = string.replace(/&/g,"&amp;").replace(/'/g,"&#039;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
	return string;
}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Pour mémoriser les liens des ressources avant que le tooltip ne bouffe les title.
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
var tab_ressources = new Array();

// jQuery !
$(document).ready
(
	function()
	{

		// initialisation
		var matiere_id = 0;
		var objet = false;

		$('#zone_socle ul').css("display","none");
		$('#zone_socle ul.ul_m1').css("display","block");

		var images = new Array();
		images[1]  = '';
		images[1] += '<q class="n1_edit" lang="edit" title="Renommer ce domaine (avec sa référence)."></q>';
		images[1] += '<q class="n1_add" lang="add" title="Ajouter un domaine à la suite."></q>';
		images[1] += '<q class="n1_move" lang="move" title="Déplacer ce domaine (et renuméroter)."></q>';
		images[1] += '<q class="n1_del" lang="del" title="Supprimer ce domaine ainsi que tout son contenu."></q>';
		images[1] += '<q class="n2_add" lang="add" title="Ajouter un thème au début de ce domaine (et renuméroter)."></q>';
		images[2]  = '';
		images[2] += '<q class="n2_edit" lang="edit" title="Renommer ce thème."></q>';
		images[2] += '<q class="n2_add" lang="add" title="Ajouter un thème à la suite (et renuméroter)."></q>';
		images[2] += '<q class="n2_move" lang="move" title="Déplacer ce thème (et renuméroter)."></q>';
		images[2] += '<q class="n2_del" lang="del" title="Supprimer ce thème ainsi que tout son contenu (et renuméroter)."></q>';
		images[2] += '<q class="n3_add" lang="add" title="Ajouter un item au début de ce thème (et renuméroter)."></q>';
		images[3]  = '';
		images[3] += '<q class="n3_edit" lang="edit" title="Renommer, coefficienter, autoriser, lier cet item."></q>';
		images[3] += '<q class="n3_add" lang="add" title="Ajouter un item à la suite (et renuméroter)."></q>';
		images[3] += '<q class="n3_move" lang="move" title="Déplacer cet item (et renuméroter)."></q>';
		images[3] += '<q class="n3_fus" lang="fus" title="Fusionner avec un autre item (et renuméroter)."></q>';
		images[3] += '<q class="n3_del" lang="del" title="Supprimer cet item (et renuméroter)."></q>';

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Charger le div zone_compet en ajax
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q.modifier').click
		(
			function()
			{
				id = $(this).parent().attr('id');
				matiere = $(this).parent().parent().attr('lang');
				matiere_id = id.substring(3);
				afficher_masquer_images_action('hide');
				new_label = '<label for="'+id+'" class="loader">Demande envoyée... Veuillez patienter.</label>';
				$(this).after(new_label);
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Voir&matiere='+matiere_id,
						dataType : "html",
						error : function(msg,string)
						{
							$('label[for='+id+']').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.').fadeOut(2000,function(){$('label[for='+id+']').remove();afficher_masquer_images_action('show');});
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML.substring(0,16)=='<ul class="ul_m1')	// Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
							{
								$('label[for='+id+']').removeAttr("class").addClass("valide").html("Contenu affiché ci-dessous !").fadeOut(2000,function(){$('label[for='+id+']').remove();afficher_masquer_images_action('show');});
								$('#zone_compet').html('<h2>'+matiere+'</h2>'+responseHTML);
//								$('#zone_compet ul').css("display","none");
								$('#zone_compet').css("display","block");
//								$('#zone_compet ul.ul_m1').css("display","block");
								// Récupérer le contenu des title des ressources avant que le tooltip ne l'enlève
								$('#zone_compet li.li_n3').each
								(
									function()
									{
										id = $(this).attr('id').substring(3);
										titre = $(this).children('b').children('img:eq(3)').attr('title');
										tab_ressources[id] = (titre=='Absence de ressource.') ? '' : titre ;
									}
								);
								infobulle();
							}
							else
							{
								$('label[for='+id+']').removeAttr("class").addClass("alerte").html(responseHTML).fadeOut(2000,function(){$('label[for='+id+']').remove();afficher_masquer_images_action('show');});
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Ajouter un domaine, ou un thème, ou un item
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q[lang=add]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				// On récupère le contexte de la demande : n1 ou n2 ou n3
				contexte = $(this).attr('class').substring(0,2);
				afficher_masquer_images_action('hide');
				// On créé le formulaire à valider
				new_li  = '<li class="li_'+contexte+'">';
				switch(contexte)
				{
					case 'n1' :	// domaine
						new_li += '<i>Ref.</i> <input id="f_ref" name="f_ref" size="1" maxlength="1" type="text" value="" /> <i>Nom</i> <input id="f_nom" name="f_nom" size="100" maxlength="128" type="text" value="" /> <img alt="" src="./_img/bulle_aide.png" title="Indiquer une lettre référence et un nom de domaine." />';
						texte = 'ce domaine';
						break;
					case 'n2' :	// thème
						new_li += '<i>Nom</i> <input id="f_nom" name="f_nom" size="100" maxlength="128" type="text" value="" /> <img alt="" src="./_img/bulle_aide.png" title="Indiquer un nom de thème." />';
						texte = 'ce thème';
						break;
					case 'n3' :	// item
						new_li += '<i>Nom</i> <input id="f_nom" name="f_nom" size="125" maxlength="256" type="text" value="" /> <img alt="" src="./_img/bulle_aide.png" title="Indiquer un nom d\'item." /><br />';
						new_li += '<i>Socle</i> <input id="f_intitule" name="f_intitule" size="125" maxlength="256" type="text" value="Hors-socle." readonly /><input id="f_socle" name="f_socle" type="hidden" value="0" /><q class="choisir_compet" title="Sélectionner un item du socle commun."></q> <img alt="" src="./_img/bulle_aide.png" title="Appartenance éventuelle au socle commun." /><br />';
						new_li += '<i>Coef.</i> <input id="f_coef" name="f_coef" type="text" value="1" size="1" maxlength="2" /> <img alt="" src="./_img/bulle_aide.png" title="Coefficient facultatif (entier entre 0 et 20)." /> - <input id="f_cart1" name="f_cart" type="radio" value="1" checked /><label for="f_cart1"><img src="./_img/cart1.png" title="Demande possible." /></label> <input id="f_cart0" name="f_cart" type="radio" value="0" /><label for="f_cart0"><img src="./_img/cart0.png" title="Demande interdite." /></label> - <i>Lien</i> <input id="f_lien" name="f_lien" type="text" value="" size="100" /> <img alt="" src="./_img/bulle_aide.png" title="Lien (facultatif) vers une ressource internet (entraînement, remédiation&hellip;).." />';
						texte = 'cet item';
						break;
					default :
						texte = '???';
				}
				new_li += '<q class="valider" lang="ajouter" title="Valider l\'ajout de '+texte+'."></q><q class="annuler" lang="ajouter" title="Annuler l\'ajout de '+texte+'."></q> <label id="ajax_msg">&nbsp;</label>';
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
				infobulle();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Éditer un domaine, ou un thème, ou un item
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q[lang=edit]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				// On récupère le contexte de la demande : n1 ou n2 ou n3
				contexte = $(this).attr('class').substring(0,2);
				afficher_masquer_images_action('hide');
				// On créé le formulaire à valider
				new_div  = '<div id="form_edit">';
				switch(contexte)
				{
					case 'n1' :	// domaine
						// on récupère la référence et le nom
						span = $(this).parent().children('span').text();
						ref = span.charAt(0);
						nom = span.substring(4);
						new_div += '<i>Ref.</i> <input id="f_ref" name="f_ref" size="1" maxlength="1" type="text" value="'+ref+'" /> <i>Nom</i> <input id="f_nom" name="f_nom" size="'+Math.min(10+nom.length,118)+'" maxlength="128" type="text" value="'+nom+'" /> <img alt="" src="./_img/bulle_aide.png" title="Indiquer une lettre référence et un nom de domaine." />';
						texte = 'ce domaine';
						break;
					case 'n2' :	// thème
						// On récupère le nom
						nom = $(this).parent().children('span').text();
						new_div += '<i>Nom</i> <input id="f_nom" name="f_nom" size="'+Math.min(10+nom.length,128)+'" maxlength="128" type="text" value="'+nom+'" /> <img alt="" src="./_img/bulle_aide.png" title="Indiquer un nom de thème." />';
						texte = 'ce thème';
						break;
					case 'n3' :	// item
						// On récupère le nom
						nom = $(this).parent().children('b').text();
						// On récupère le coefficient
						coef = parseInt( $(this).parent().children('b').children('img:eq(0)').attr('src').substring(12,14) );
						// On récupère l'autorisation de demande
						cart = $(this).parent().children('b').children('img:eq(1)').attr('src').substring(11,12);
						check1 = (cart=='1') ? ' checked' : '' ;
						check0 = (cart=='0') ? ' checked' : '' ;
						// On récupère le socle
						socle_id  = $(this).parent().children('b').children('img:eq(2)').attr('lang').substring(3);
						socle_txt = $('label[for=socle_'+socle_id+']').text();
						// On récupère le lien
						item_id = $(this).parent().attr('id').substring(3);
						lien = tab_ressources[item_id];
						new_div += '<i>Nom</i> <input id="f_nom" name="f_nom" size="'+Math.min(10+nom.length,128)+'" maxlength="256" type="text" value="'+nom+'" /> <img alt="" src="./_img/bulle_aide.png" title="Indiquer un nom d\'item." /><br />';
						new_div += '<i>Socle</i> <input id="f_intitule" name="f_intitule" size="110" maxlength="256" type="text" value="'+socle_txt+'" readonly /><input id="f_socle" name="f_socle" type="hidden" value="'+socle_id+'" /><q class="choisir_compet" title="Sélectionner un item du socle commun."></q> <img alt="" src="./_img/bulle_aide.png" title="Appartenance éventuelle au socle commun." /><br />';
						new_div += '<i>Coef.</i> <input id="f_coef" name="f_coef" type="text" value="'+coef+'" size="1" maxlength="2" /> <img alt="" src="./_img/bulle_aide.png" title="Coefficient facultatif (entier entre 0 et 20)." /> - <input id="f_cart1" name="f_cart" type="radio" value="1"'+check1+' /><label for="f_cart1"><img src="./_img/cart1.png" title="Demande possible." /></label> <input id="f_cart0" name="f_cart" type="radio" value="0"'+check0+' /><label for="f_cart0"><img src="./_img/cart0.png" title="Demande interdite." /></label> - <i>Lien</i> <input id="f_lien" name="f_lien" type="text" value="'+lien+'" size="90" /> <img alt="" src="./_img/bulle_aide.png" title="Lien (facultatif) vers une ressource internet (entraînement, remédiation&hellip;)." />';
						texte = 'cet item';
						break;
					default :
						texte = '???';
				}
				new_div += '<q class="valider" lang="editer" title="Valider la modification de '+texte+'."></q><q class="annuler" lang="editer" title="Annuler la modification de '+texte+'."></q> <label id="ajax_msg">&nbsp;</label>';
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
				infobulle();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Supprimer un domaine (avec son contenu), ou un thème (avec son contenu), ou un item
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q[lang=del]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				// On récupère le contexte de la demande : n1 ou n2 ou n3
				contexte = $(this).attr('class').substring(0,2);
				afficher_masquer_images_action('hide');
				// On créé le formulaire à valider
				switch(contexte)
				{
					case 'n1' :	// domaine
						alerte = 'Tout le contenu de ce domaine ainsi que tous les résultats des items concernés seront perdus !';
						texte = 'ce domaine';
						break;
					case 'n2' :	// thème
						alerte = 'Tout le contenu de ce thème ainsi que les résultats des items concernés seront perdus (et les thèmes suivants seront renumérotés) !';
						texte = 'ce thème';
						break;
					case 'n3' :	// item
						alerte = 'Tout les résultats associés seront perdus et les items suivants seront renumérotés !';
						texte = 'cet item';
						break;
					default :
						alerte = '???';
						texte = '???';
				}
				new_div = '<div id="form_del" class="danger">'+alerte;	// un div.danger est utilisé au lieu du span.danger car un clic sur un span enroule/déroule le contenu
				new_div += '<q class="valider" lang="supprimer" title="Valider la suppression de '+texte+'."></q><q class="annuler" lang="supprimer" title="Annuler la suppression de '+texte+'."></q> <label id="ajax_msg">&nbsp;</label>';
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
				infobulle();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Fusionner deux items
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q[lang=fus]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				afficher_masquer_images_action('hide');
				// On ajoute les boutons à cocher
				id = $(this).parent().attr('id');
				$('#zone_compet li.li_n3').each( function(){ if($(this).attr('id')!=id){$(this).children('b').after('<q class="n3_fus2" lang="fus2" title="Valider l\'absorption de l\'item choisi en 1er par celui-ci."></q>');} } );
				new_img = '<q class="annuler" lang="fusionner" title="Annuler la fusion de cet item."></q><label id="ajax_msg">&nbsp;</label>';
				// On insère le formulaire dans la page
				$(this).after(new_img);
				infobulle();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Déplacer un domaine (avec son contenu), ou un thème (avec son contenu), ou un item
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q[lang=move]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				// On récupère le contexte de la demande : n1 ou n2 ou n3
				contexte = $(this).attr('class').substring(0,2);
				afficher_masquer_images_action('hide');
				// On ajoute les boutons à cocher
				id = $(this).parent().attr('id');
				switch(contexte)
				{
					case 'n1' :	// domaine
						$('#zone_compet li.li_m2').each( function(){ $(this).children('span').after('<q class="n1_move2" lang="move2" title="Valider le déplacement du domaine au début de ce niveau."></q>'); } );
						$('#zone_compet li.li_n1').each( function(){ if($(this).attr('id')!=id){$(this).children('span').after('<q class="n1_move2" lang="move2" title="Valider le déplacement du domaine à la suite de celui-ci."></q>');} } );
						break;
					case 'n2' :	// thème
						$('#zone_compet li.li_n1').each( function(){ $(this).children('span').after('<q class="n2_move2" lang="move2" title="Valider le déplacement du thème au début de ce domaine (et renuméroter)."></q>'); } );
						$('#zone_compet li.li_n2').each( function(){ if($(this).attr('id')!=id){$(this).children('span').after('<q class="n2_move2" lang="move2" title="Valider le déplacement du thème à la suite de celui-ci."></q>');} } );
						break;
					case 'n3' :	// item
						$('#zone_compet li.li_n2').each( function(){ $(this).children('span').after('<q class="n3_move2" lang="move2" title="Valider le déplacement de l\'item au début de ce thème (et renuméroter)."></q>'); } );
						$('#zone_compet li.li_n3').each( function(){ if($(this).attr('id')!=id){$(this).children('b').after('<q class="n3_move2" lang="move2" title="Valider le déplacement de l\'item à la suite de celui-ci."></q>');} } );
						break;
				}
				// On créé le formulaire à valider
				switch(contexte)
				{
					case 'n1' :	// domaine
						texte = 'ce domaine';
						break;
					case 'n2' :	// thème
						texte = 'ce thème';
						break;
					case 'n3' :	// item
						texte = 'cet item';
						break;
					default :
						texte = '???';
				}
				new_img = '<q class="annuler" lang="deplacer" title="Annuler le déplacement de '+texte+'."></q><label id="ajax_msg">&nbsp;</label>';
				// On insère le formulaire dans la page
				$(this).after(new_img);
				infobulle();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour afficher les items du socle
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q.choisir_compet').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				// récupérer le nom de l'item et le reporter
				item_nom = htmlspecialchars( entity_convert( $('#f_nom').val() ) );
				$('#zone_socle span.f_nom').html(item_nom);
				// récupérer la relation au socle commun et la cocher
				socle_id = $(this).prev().val();
				// 1. Décocher tout
				$("#zone_socle input[type=radio]").each
				(
					function()
					{
						this.checked = false;
					}
				);
				// 2. Cocher et afficher ce qui doit l'être (on laisse aussi ouvert ce qui a pu l'être précédemment)
				if(socle_id!='0')
				{
					if($('#socle_'+socle_id).length)
					{
						$('#socle_'+socle_id).prop('checked',true);
						$('#socle_'+socle_id).parent().parent().css("display","block");	// les items
						$('#socle_'+socle_id).parent().parent().parent().parent().css("display","block");	// le section
						$('#socle_'+socle_id).parent().parent().parent().parent().parent().parent().css("display","block");	// le pilier
					}
				}
				else
				{
					$('#socle_0').prop('checked',true);
				}
				// montrer le cadre
				$('#zone_socle q').show();
				$('#socle_0').parent().parent().css("display","block");
				$('#zone_socle').show('fast');
				$('#socle_'+socle_id).focus();
				objet = 'choisir_compet';
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour confirmer la relation au socle d'un item
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q.valider[lang=choisir_compet]').click
		(
			function()
			{
				// récupérer la relation au socle (id + nom)
				socle_id = $("#zone_socle input[type=radio]:checked").val();
				if(isNaN(socle_id))	// normalement impossible, sauf si par exemple on triche avec la barre d'outils Web Developer...
				{
					socle_id = 0;
					socle_nom = 'Hors-socle.';
				}
				else
				{
					socle_nom = $("#zone_socle input[type=radio]:checked").next('label').text();
				}
				// L'envoyer dans le formulaire
				$('#f_socle').val(socle_id);
				$('#f_intitule').val(socle_nom);
				// masquer le cadre
				$('#zone_socle').hide('fast');
				objet = 'editer';
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour confirmer l'ajout d'un domaine, ou d'un thème, ou d'un item
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q.valider[lang=ajouter]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
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
				// On récupère le coefficient, l'autorisation de demande, le lien au socle et le lien de remédiation de l'élément (item uniquement)
				if(contexte=='n3')
				{
					coef  = parseInt( $('#f_coef').val() );
					cart  = $("input[name=f_cart]:checked").val();
					socle = $('#f_socle').val();
					lien  = $('#f_lien').val();
					if( (isNaN(coef)) || (coef<0) || (coef>20) )
					{
						$('#ajax_msg').removeAttr("class").addClass("erreur").html("Le coefficient doit être un nombre entier entre 0 et 20 !");
						$('#f_coef').focus();
						return false;
					}
					if(isNaN(cart))	// normalement impossible, sauf si par exemple on triche avec la barre d'outils Web Developer...
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
					lien  = '';
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
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=add&contexte='+contexte+'&matiere='+matiere_id+'&parent='+parent_id+'&ordre='+ordre+'&tab_id='+tab_id+'&ref='+ref+'&coef='+coef+'&cart='+cart+'&socle='+socle+'&nom='+encodeURIComponent(nom)+'&lien='+encodeURIComponent(lien),
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML.substring(0,2)==contexte)	// Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
							{
								switch(contexte)
								{
									case 'n1' :	// domaine
										texte = '<span>' + ref + ' - ' + htmlspecialchars(nom) + '</span>' + images[contexte.charAt(1)] + '<ul class="ul_n2"></ul>';
										break;
									case 'n2' :	// thème
										texte = '<span>' + htmlspecialchars(nom) + '</span>' + images[contexte.charAt(1)] + '<ul class="ul_n3"></ul>';
										break;
									case 'n3' :	// item
										coef_texte  = '<img src="./_img/coef/'+coef+'.gif" alt="" title="Coefficient '+coef+'." />';
										cart_title  = (cart>0) ? 'Demande possible.' : 'Demande interdite.' ;
										cart_texte  = '<img src="./_img/cart'+cart+'.png" title="'+cart_title+'" />';
										socle_image = (socle>0) ? 'on' : 'off' ;
										socle_nom   = $('#f_intitule').val();
										socle_texte = '<img src="./_img/socle_'+socle_image+'.png" alt="" title="'+socle_nom+'" lang="id_'+socle+'" />';
										lien_image  = (lien=='') ? 'off' : 'on' ;
										lien_nom    = (lien=='') ? 'Absence de ressource.' : htmlspecialchars(lien) ;
										lien_texte  = '<img src="./_img/link_'+lien_image+'.png" alt="" title="'+lien_nom+'" />';
										texte = '<b>' + coef_texte + cart_texte + socle_texte + lien_texte + htmlspecialchars(nom) + '</b>' + images[contexte.charAt(1)];
										element_id = responseHTML.substring(3);
										tab_ressources[element_id] = (lien=='') ? '' : lien_nom ;
										break;
									default :
										texte = '???';
								}
								$('#ajax_msg').parent().attr('id',responseHTML).html(texte);
								afficher_masquer_images_action('show');
								infobulle();
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

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour confirmer l'édition d'un domaine, ou d'un thème, ou d'un item
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q.valider[lang=editer]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
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
				// On récupère le coefficient, le lien au socle et le lien de remédiation de l'élément (item uniquement)
				if(contexte=='n3')
				{
					coef  = parseInt( $('#f_coef').val() );
					cart  = $("input[name=f_cart]:checked").val();
					socle = $('#f_socle').val();
					lien  = $('#f_lien').val();
					if( (isNaN(coef)) || (coef<0) || (coef>20) )
					{
						$('#ajax_msg').removeAttr("class").addClass("erreur").html("Le coefficient doit être un nombre entier entre 0 et 20 !");
						$('#f_coef').focus();
						return false;
					}
					if(isNaN(cart))	// normalement impossible, sauf si par exemple on triche avec la barre d'outils Web Developer...
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
					lien  = '';
				}
				// On récupère l'id de l'élément concerné (domaine ou theme ou item)
				element_id = $(this).parent().parent().attr('id').substring(3);
				// Envoi des infos en ajax pour le traitement de la demande
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=edit&contexte='+contexte+'&element='+element_id+'&ref='+ref+'&coef='+coef+'&cart='+cart+'&socle='+socle+'&nom='+encodeURIComponent(nom)+'&lien='+encodeURIComponent(lien),
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML=='ok')	// Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
							{
								texte = (contexte=='n1') ? ref + ' - ' + htmlspecialchars(nom) : htmlspecialchars(nom) ;
								if(contexte=='n3')
								{
									coef_texte  = '<img src="./_img/coef/'+coef+'.gif" alt="" title="Coefficient '+coef+'." />';
									cart_title  = (cart>0) ? 'Demande possible.' : 'Demande interdite.' ;
									cart_texte  = '<img src="./_img/cart'+cart+'.png" title="'+cart_title+'" />';
									socle_image = (socle>0) ? 'on' : 'off' ;
									socle_nom   = $('#f_intitule').val();
									socle_texte = '<img src="./_img/socle_'+socle_image+'.png" alt="" title="'+socle_nom+'" lang="id_'+socle_id+'" />';
									lien_image  = (lien=='') ? 'off' : 'on' ;
									lien_nom    = (lien=='') ? 'Absence de ressource.' : htmlspecialchars(lien) ;
									lien_texte  = '<img src="./_img/link_'+lien_image+'.png" alt="" title="'+lien_nom+'" />';
									$('#ajax_msg').parent().parent().children('b').html(coef_texte+cart_texte+socle_texte+lien_texte+texte).show();
									tab_ressources[element_id] = (lien=='') ? '' : lien_nom ;
									infobulle();
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

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour confirmer la suppression d'un domaine (avec son contenu), ou d'un thème (avec son contenu), ou d'un item
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q.valider[lang=supprimer]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				// On récupère le contexte de la demande : n1 ou n2 ou n3
				contexte = $(this).parent().parent().attr('id').substring(0,2);
				// On récupère l'id de l'élément concerné (domaine ou theme ou item)
				element_id = $(this).parent().parent().attr('id').substring(3);
				// On récupère la liste des éléments suivants dont il faudra diminuer l'ordre
				li = $(this).parent().parent();
				tab_id = new Array();
				while(li.next().length)
				{
					li = li.next();
					tab_id.push(li.attr('id').substring(3));
				}
				// Envoi des infos en ajax pour le traitement de la demande
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=del&contexte='+contexte+'&element='+element_id+'&tab_id='+tab_id,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML=='ok')	// Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
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
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour confirmer la fusion d'un item avec un second qui l'absorbe
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q[lang=fus2]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				//
				// Element de départ
				//
				li = $('q.annuler[lang=fusionner]').parent();
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
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=fus&element='+element_id+'&tab_id='+tab_id+'&element2='+element2_id,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML=='ok')	// Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
							{
								$('#ajax_msg').parent().remove();
								$('q[lang=fus2]').remove();
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

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour confirmer le déplacement d'un domaine, ou d'un thème, ou d'un item
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q[lang=move2]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				//
				// Element de départ
				//
				li = $('q.annuler[lang=deplacer]').parent();
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
				if(contexte2==contexte)	// Si on demande à l'insérer après un élément de même niveau
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
						if(test_id!=element_id)	// sans compter éventuellement celui qui va être déplacé...
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
						if(test_id!=element_id)	// sans compter éventuellement celui qui va être déplacé...
						{
							tab_id2.push(test_id);
						}
					}
				}
				else	// Si on demande à l'insérer au début d'un élément de niveau supérieur
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
							if(test_id!=element_id)	// sans compter éventuellement celui qui va être déplacé...
							{
								tab_id2.push(test_id);
							}
						}
					);
				}
				// Envoi des infos en ajax pour le traitement de la demande
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=move&contexte='+contexte+'&element='+element_id+'&tab_id='+tab_id+'&parent='+parent_id+'&ordre='+ordre+'&tab_id2='+tab_id2,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							if(responseHTML=='ok')	// Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
							{
								if(contexte2==contexte)	// Si on demande à l'insérer après un élément de même niveau
								{
									$('#'+li_id_arrivee).after( $('#'+li_id_depart) );
								}
								else	// Si on demande à l'insérer au début d'un élément de niveau supérieur
								{
									$('#'+li_id_arrivee).children('ul').prepend( $('#'+li_id_depart) );
								}
								$('q.annuler[lang=deplacer]').remove();
								$('#ajax_msg').remove();
								$('q[lang=move2]').remove();
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

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Annuler le choix dans le socle
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q.annuler[lang=choisir_compet]').click
		(
			function()
			{
				$('#zone_socle').hide('fast');
				objet = 'editer';
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Annuler un ajout
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q.annuler[lang=ajouter]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				$(this).parent().remove();
				afficher_masquer_images_action('show');
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Annuler un renommage
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q.annuler[lang=editer]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				$(this).parent().parent().children().show();
				$(this).parent().remove();
				afficher_masquer_images_action('show');
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Annuler une suppression
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q.annuler[lang=supprimer]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				$(this).parent().parent().children().show();
				$(this).parent().remove();
				afficher_masquer_images_action('show');
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Annuler une fusion
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q.annuler[lang=fusionner]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				$(this).remove();
				$('#ajax_msg').remove();
				$('q[lang=fus2]').remove();
				afficher_masquer_images_action('show');
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Annuler un déplacement
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('q.annuler[lang=deplacer]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				$(this).remove();
				$('#ajax_msg').remove();
				$('q[lang=move2]').remove();
				afficher_masquer_images_action('show');
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Intercepter la touche entrée ou escape pour valider ou annuler les modifications
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
		$('input').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('keyup',
			function(e)
			{
				if(e.which==13)	// touche entrée
				{
					if(objet=='choisir_compet') {$('#zone_socle q.valider').click();} else {$('#zone_compet q.valider').click();}
				}
				else if(e.which==27)	// touche escape
				{
					if(objet=='choisir_compet') {$('#zone_socle q.annuler').click();} else {$('#zone_compet q.annuler').click();}
				}
			}
		);

	}
);
