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

		// Préparation de select utiles
		var select_partage = '<select id="f_partage" name="f_partage"><option value="oui">Partagé sur le serveur communautaire.</option><option value="bof">Partage sans intérêt (pas novateur).</option><option value="non">Non partagé avec la communauté.</option></select>';
		var select_methode = '<select id="f_methode" name="f_methode"><option value="geometrique">Coefficients &times;2</option><option value="arithmetique">Coefficients +1</option><option value="classique">Moyenne classique</option><option value="bestof1">La meilleure</option><option value="bestof2">Les 2 meilleures</option><option value="bestof3">Les 3 meilleures</option></select>';
		var select_limite  = '<select id="f_limite" name="f_limite"><option value="0">de toutes les notes.</option><option value="1">de la dernière note.</option>';
		var tab_options = new Array(2,3,4,5,6,7,8,9,10,15,20,30,40,50);
		for(i=0 ; i<tab_options.length ; i++)
		{
			select_limite += '<option value="'+tab_options[i]+'">des '+tab_options[i]+' dernières notes.</option>';
		}
		select_limite += '</select>';

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Changement de méthode -> desactiver les limites autorisées suivant les cas
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		// Tableaux utilisés pour savoir quelles options desactiver
		var tableau_limites_autorisees = new Array();
		tableau_limites_autorisees['geometrique']  = '.1.2.3.4.5.';
		tableau_limites_autorisees['arithmetique'] = '.1.2.3.4.5.6.7.8.9.';
		tableau_limites_autorisees['classique']    = '.1.2.3.4.5.6.7.8.9.10.15.20.30.40.50.0.';
		tableau_limites_autorisees['bestof1']      = '.1.2.3.4.5.6.7.8.9.10.15.20.30.40.50.0.';
		tableau_limites_autorisees['bestof2']      =   '.2.3.4.5.6.7.8.9.10.15.20.30.40.50.0.';
		tableau_limites_autorisees['bestof3']      =     '.3.4.5.6.7.8.9.10.15.20.30.40.50.0.';
		// La fonction qui s'en occupe
		var actualiser_select_limite = function()
		{
			// Déterminer s'il faut modifier l'option sélectionnée
			var limite_valeur            = $('#f_limite option:selected').val();
			var findme                   = '.'+limite_valeur+'.';
			var methode_valeur           = $('#f_methode option:selected').val();
			var chaine_autorisee         = tableau_limites_autorisees[methode_valeur];
			var modifier_limite_selected = (chaine_autorisee.indexOf(findme)==-1) ? true : false ; // 1|3 Si true alors il faudra changer le selected actuel qui ne sera plus dans les nouveaux choix.
			if(modifier_limite_selected)
			{
				modifier_limite_selected = chaine_autorisee.substr(chaine_autorisee.length-2,1) ; // 2|3 On prendra alors la valeur maximale dans les nouveaux choix.
			}
			$("#f_limite option").each
			(
				function()
				{
					limite_valeur = $(this).val();
					findme = '.'+limite_valeur+'.';
					if(chaine_autorisee.indexOf(findme)==-1)
					{
						$(this).attr('disabled','disabled');
					}
					else
					{
						$(this).removeAttr('disabled');
					}
					if(limite_valeur===modifier_limite_selected) // === pour éviter un (false==0) qui sélectionne la 1ère option...
					{
						$(this).attr('selected','selected'); // 3|3 C'est ici que le selected se fait.
					}
				}
			);
		};
		// Appel de la fonction à chaque changement de méthode
		$('#f_methode').live('change', actualiser_select_limite );

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Changement de nb de demandes autorisées pour une matière -> ajouter un bouton de validation
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('select[name=f_eleve_demandes]').change
		(
			function()
			{
				$(this).parent().find('div').remove();
				$(this).parent().append('<div><button name="enregistrer" type="button" value="'+$(this).val()+'"><img alt="" src="./_img/bouton/valider.png" /> Enregistrer.</button></div>');
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur le bouton pour maj le nb de demandes autorisées pour une matière
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('button[name=enregistrer]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var bouton = $(this);
				var nb_demandes = $(this).attr('value');
				var td_id = $(this).parent().parent().attr('id');
				var matiere_id = td_id.substring(4);
				bouton.html('<img alt="" src="./_img/ajax/ajax_loader.gif" /> Patienter...');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=NbDemandes&matiere_id='+matiere_id+'&nb_demandes='+nb_demandes,
						dataType : "html",
						error : function(msg,string)
						{
							bouton.html('<img alt="" src="./_img/ajax/ajax_alerte.png" /> Erreur ! Recommencer.');
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML!='ok')
							{
								bouton.html('<img alt="" src="./_img/ajax/ajax_alerte.png" /> Erreur ! Recommencer.');
							}
							else
							{
								bouton.parent().remove();
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Voir un référentiel de son établissement
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('table.comp_view q.voir').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var ids = $(this).parent().attr('id');
				afficher_masquer_images_action('hide');
				var new_label = '<label for="'+ids+'" class="loader">Demande envoyée... Veuillez patienter.</label>';
				$(this).after(new_label);
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Voir&ids='+ids,
						dataType : "html",
						error : function(msg,string)
						{
							$('label[for='+ids+']').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.').fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,18)!='<ul class="ul_m1">')
							{
								$('label[for='+ids+']').removeAttr("class").addClass("alerte").html(responseHTML).fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
							}
							else
							{
								$('#voir_referentiel').html(responseHTML+'<p />');
								infobulle();
								$('label[for='+ids+']').removeAttr("class").addClass("valide").html("Contenu affiché ci-dessous !").fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Modifier le partage d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.partager').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				afficher_masquer_images_action('hide');
				var partage = $(this).parent().prev().prev().attr('lang');
				var new_span = '<span>'+select_partage.replace('"'+partage+'"','"'+partage+'" selected="selected"')+'<q class="valider" lang="partager" title="Valider les modifications du partage de ce référentiel."></q><q class="annuler" title="Annuler la modification du partage de ce référentiel."></q> <label id="ajax_msg">&nbsp;</label></span>';
				$(this).after(new_span);
				infobulle();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Mettre à jour sur le serveur de partage la dernière version d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.envoyer').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var ids = $(this).parent().attr('id');
				afficher_masquer_images_action('hide');
				var new_label = '<label for="'+ids+'" class="loader">Demande envoyée... Veuillez patienter.</label>';
				$(this).after(new_label);
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Envoyer&ids='+ids,
						dataType : "html",
						error : function(msg,string)
						{
							$('label[for='+ids+']').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.').fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,10)!='<img title')
							{
								$('label[for='+ids+']').removeAttr("class").addClass("alerte").html(responseHTML).fadeOut(4000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
							}
							else
							{
								$('#'+ids).prev().prev().html('Référentiel présent. '+responseHTML);
								infobulle();
								$('label[for='+ids+']').removeAttr("class").addClass("valide").html("Référentiel transmis au serveur de partage avec succès !").fadeOut(2000,function(){$('label[for='+ids+']').remove();afficher_masquer_images_action('show');});
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Modifier le mode de calcul d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.calculer').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				afficher_masquer_images_action('hide');
				var param   = $(this).parent().prev().attr('lang');
				var tableau = param.split('_');
				var methode = tableau[0];
				var limite  = tableau[1];
				var new_span = '<span>'+select_methode.replace('"'+methode+'"','"'+methode+'" selected="selected"')+select_limite.replace('"'+limite+'"','"'+limite+'" selected="selected"')+'<q class="valider" lang="calculer" title="Valider les modifications du mode de calcul de ce référentiel."></q><q class="annuler" title="Annuler la modification du mode de calcul de ce référentiel."></q> <label id="ajax_msg">&nbsp;</label></span>';
				$(this).after(new_span);
				actualiser_select_limite();
				infobulle();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Retirer un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.supprimer').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				afficher_masquer_images_action('hide');
				var new_span = '<span class="danger">Tous les items et les résultats associés des élèves seront perdus !<q class="valider" lang="retirer" title="Confirmer la suppression de ce référentiel."></q><q class="annuler" title="Annuler la suppression de ce référentiel."></q> <label id="ajax_msg">&nbsp;</label></span>';
				$(this).after(new_span);
				infobulle();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Valider la modification du partage d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.valider[lang=partager]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var ids = $(this).parent().parent().attr('id');
				var partage = $('#f_partage').val();
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Partager&ids='+ids+'&partage='+partage,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,10)!='<img title')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								$('#'+ids).prev().prev().attr('lang',partage).html('Référentiel présent. '+responseHTML);
								if(partage=='oui')
								{
									$('#'+ids).children('q.envoyer_non').attr('class','envoyer').attr('title','Mettre à jour sur le serveur de partage la dernière version de ce référentiel.');
								}
								else
								{
									$('#'+ids).children('q.envoyer').attr('class','envoyer_non').attr('title','Un référentiel non partagé ne peut pas être transmis à la collectivité.');
								}
								$('#ajax_msg').parent().remove();
								afficher_masquer_images_action('show');
								infobulle();
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Valider la modification du mode de calcul d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.valider[lang=calculer]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var ids = $(this).parent().parent().attr('id');
				var methode = $('#f_methode').val();
				var limite  = $('#f_limite').val();
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Calculer&ids='+ids+'&methode='+methode+'&limite='+limite,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,2)!='ok')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								$('#'+ids).prev().attr( 'lang',methode+'_'+limite ).html( responseHTML.substring(2,responseHTML.length) );
								$('#ajax_msg').parent().remove();
								afficher_masquer_images_action('show');
								infobulle();
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Valider la suppression d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.valider[lang=retirer]').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var ids = $(this).parent().parent().attr('id');
				var partage = $(this).parent().parent().prev().prev().attr('lang');
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Retirer&ids='+ids+'&partage='+partage,
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML!='ok')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								var proposition = (ids.substring(0,5)=='ids_1') ? '' : ' ou importer un référentiel existant' ;
								$('#'+ids).html('<q class="ajouter" title="Créer un référentiel vierge'+proposition+'."></q>');
								$('#'+ids).prev().removeAttr("class").addClass("r").html('Sans objet.');
								$('#'+ids).prev().prev().removeAttr("class").addClass("r").html('Absence de référentiel.');
								afficher_masquer_images_action('show');
								infobulle();
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Ajouter un référentiel ; => affichage de choisir_referentiel même dans le cas d'une matière spécifique à l'établissement
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.ajouter').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var ids = $(this).parent().attr('id');
				$('#voir_referentiel').html("&nbsp;");
				afficher_masquer_images_action('hide');
				var new_span = '<span><input id="succes" name="succes" type="hidden" value="" /><label for="'+ids+'" class="valide">Faites votre choix ci-dessous...</label></span>';
				$(this).after(new_span);
				$('#choisir_importer').parent().hide();
				$('#choisir_referentiel').show();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur le bouton pour Annuler le choix d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_annuler').click
		(
			function()
			{
				$('#voir_referentiel').html("&nbsp;");
				$('#choisir_referentiel').hide();
				$('#ajax_msg_choisir').removeAttr("class").html("&nbsp;");
				$('#succes').parent().remove();
				afficher_masquer_images_action('show');
				return(false);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Charger le formulaire listant les structures ayant partagées un référentiel (appel au serveur communautaire)
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		var charger_formulaire_structures = function()
		{
			$('#rechercher').hide("fast");
			$('#ajax_msg').removeAttr("class").addClass("loader").html('Chargement du formulaire... Veuillez patienter.');
			$.ajax
			(
				{
					type : 'POST',
					url : 'ajax.php?page='+PAGE,
					data : 'action=Afficher_structures',
					dataType : "html",
					error : function(msg,string)
					{
						$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! <a href="#" id="charger_formulaire_structures">Veuillez essayer de nouveau.</a>');
						return false;
					},
					success : function(responseHTML)
					{
						maj_clock(1);
						if(responseHTML.substring(0,7)!='<option')
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML+' <a href="#" id="charger_formulaire_structures">Veuillez essayer de nouveau.</a>');
						}
						else
						{
							$('#ajax_msg').removeAttr("class").html('&nbsp;');
							$('#f_structure').html(responseHTML);
							$('#rechercher').removeAttr("class").show("fast"); // Pour IE7 le show() ne suffit pas
						}
					}
				}
			);
		};

		// Charger au clic sur le lien obtenu si échec
		$('#charger_formulaire_structures').live(  'click' , charger_formulaire_structures );

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur le bouton pour Afficher le formulaire de recherche sur le serveur communautaire
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_rechercher').click
		(
			function()
			{
				// Récup des infos
				var ids = $('#succes').parent().parent().attr('id');
				var tab_ids = ids.split('_');
				var matiere_id = tab_ids[2];
				var niveau_id  = tab_ids[3];
				//MAJ et affichage du formulaire
				charger_formulaire_structures();
				$('#f_matiere option[value='+matiere_id+']').attr('selected',true);
				$('#f_niveau option[value='+niveau_id+']').attr('selected',true);
				$('#choisir_referentiel_communautaire ul').html('<li></li>');
				$('#lister_referentiel_communautaire').hide("fast");
				$('#voir_referentiel_communautaire ul li.li_m1').html('').parent().parent().hide();
				$('#form_instance').hide();
				$('#form_communautaire').show();
				maj_clock(1);
				return(false);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Réagir au changement dans un select
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_referentiel_communautaire select').change
		(
			function()
			{
				$('#ajax_msg').removeAttr("class").html("&nbsp;");
				$('#choisir_referentiel_communautaire ul').html('<li></li>');
				$('#voir_referentiel_communautaire').hide("fast");
				$('#lister_referentiel_communautaire').hide("fast");
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Changement de matière -> desactiver les niveaux classiques en cas de matière transversale
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#f_matiere').change
		(
			function()
			{
				var modif_niveau_selected = 0; // 0 = pas besoin modifier / 1 = à modifier / 2 = déjà modifié
				var matiere_id = $('#f_matiere').val();
				$("#f_niveau option").each
				(
					function()
					{
						var niveau_id = $(this).val();
						var findme = '.'+niveau_id+'.';
						// Les niveaux "cycles" sont tout le temps accessibles
						if(listing_id_niveaux_cycles.indexOf(findme) == -1)
						{
							// matière classique -> tous niveaux actifs
							if(matiere_id != id_matiere_transversale)
							{
								$(this).removeAttr('disabled');
							}
							// matière transversale -> desactiver les autres niveaux
							else
							{
								$(this).attr('disabled','disabled');
								modif_niveau_selected = Math.max(modif_niveau_selected,1);
							}
						}
						// C'est un niveau cycle ; le sélectionner si besoin
						else if(modif_niveau_selected==1)
						{
							$(this).attr('selected','selected');
							modif_niveau_selected = 2;
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur le bouton pour chercher des référentiels partagés sur d'autres niveaux ou matières
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#rechercher').click
		(
			function()
			{
				$('#voir_referentiel_communautaire').hide();
				var matiere_id   = $('#f_matiere').val();
				var niveau_id    = $('#f_niveau').val();
				var structure_id = $('#f_structure').val();
				if( (matiere_id==0) && (niveau_id==0) && (structure_id==0) )
				{
					$('#ajax_msg').removeAttr("class").addClass("erreur").html("Il faut préciser au moins un critère !");
					return false;
				}
				$('#rechercher').attr('disabled','disabled');
				$('#ajax_msg').removeAttr("class").addClass("loader").html('Demande envoyée... Veuillez patienter.');
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Lister_referentiels&matiere_id='+matiere_id+'&niveau_id='+niveau_id+'&structure_id='+structure_id,
						dataType : "html",
						error : function(msg,string)
						{
							$('#rechercher').removeAttr('disabled');
							$('#ajax_msg').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
							return false;
						},
						success : function(responseHTML)
						{
							$('#rechercher').removeAttr('disabled');
							if(responseHTML.substring(0,3)!='<li')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								maj_clock(1);
								$('#ajax_msg').removeAttr("class").html("&nbsp;");
								var reg = new RegExp('</q>',"g"); // Si on ne prend pas une expression régulière alors replace() ne remplace que la 1e occurence
								responseHTML = responseHTML.replace(reg,'</q><q class="valider" title="Sélectionner ce référentiel.<br />(choix à confirmer de retour à la page principale)"></q>'); // Ajouter les paniers
								$('#choisir_referentiel_communautaire ul').html(responseHTML);
								$('#lister_referentiel_communautaire').show("fast");
								infobulle();
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur une image pour choisir un référentiel donné
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_referentiel_communautaire q.valider').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var referentiel_id = $(this).parent().attr('id').substr(3);
				var description    = $(this).parent().text(); // Pb : il prend le contenu du <sup> avec
				var longueur_sup   = $(this).prev().prev().text().length;
				var description    = description.substring(0,description.length-longueur_sup);
				$('#reporter').html(description).parent('#choisir_importer').val('id_'+referentiel_id).parent().show();
				maj_clock(1);
				$('#rechercher_annuler').click();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Voir le détail d'un référentiel partagé
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_referentiel_communautaire q.voir').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var referentiel_id = $(this).parent().attr('id').substr(3);
				var description    = $(this).parent().text(); // Pb : il prend le contenu du <sup> avec
				var longueur_sup   = $(this).prev().text().length;
				var description    = description.substring(0,description.length-longueur_sup);
				var new_label = '<label id="temp" class="loader">Demande envoyée... Veuillez patienter.</label>';
				$(this).next().after(new_label);
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Voir_referentiel&referentiel_id='+referentiel_id,
						dataType : "html",
						error : function(msg,string)
						{
							$('label[id=temp]').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.').fadeOut(2000,function(){$('label[id=temp]').remove();});
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,18)!='<ul class="ul_n1">')
							{
								$('label[id=temp]').removeAttr("class").addClass("alerte").html(responseHTML).fadeOut(2000,function(){$('label[id=temp]').remove();});
							}
							else
							{
								$('#voir_referentiel_communautaire ul li.li_m1').html('<b>'+description+'</b><q class="imprimer" title="Imprimer le référentiel."></q>'+responseHTML).parent().parent().show();
								infobulle();
								$('label[id=temp]').removeAttr("class").addClass("valide").html("Contenu affiché ci-dessous !").fadeOut(2000,function(){$('label[id=temp]').remove();});
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur une image pour Imprimer un referentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#voir_referentiel_communautaire q.imprimer').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				window.print();
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur le bouton pour Annuler la recherche sur le serveur communautaire
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#rechercher_annuler').click
		(
			function()
			{
				$('#form_instance').show();
				$('#form_communautaire').hide();
				$('#lister_referentiel_communautaire').hide();
				return(false);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur un bouton pour Valider le choix d'un referentiel (vierge ou issu du serveur communautaire)
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#choisir_initialiser , #choisir_importer').click
		(
			function()
			{
				var ids = $('#succes').parent().parent().attr('id');
				var referentiel_id = $(this).val().substring(3);
				$('button').attr('disabled','disabled');
				$('#ajax_msg_choisir').removeAttr("class").addClass("loader").html("Demande envoyée... Veuillez patienter.");
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'action=Ajouter&ids='+ids+'&referentiel_id='+referentiel_id,
						dataType : "html",
						error : function(msg,string)
						{
							$('button').removeAttr('disabled');
							$('#ajax_msg_choisir').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							$('button').removeAttr('disabled');
							if(responseHTML!='ok')
							{
								$('#ajax_msg_choisir').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								var test_matiere_perso = (ids.substring(0,5)=='ids_1') ? true : false ;
								var q_partager = (test_matiere_perso) ? '<q class="partager_non" title="Le référentiel d\'une matière spécifique à l\'établissement ne peut être partagé."></q>' : '<q class="partager" title="Modifier le partage de ce référentiel."></q>' ;
								$('#'+ids).html('<q class="voir" title="Voir le détail de ce référentiel."></q>'+q_partager+'<q class="envoyer_non" title="Un référentiel non partagé ne peut pas être transmis à la collectivité."></q><q class="calculer" title="Modifier le mode de calcul associé à ce référentiel."></q><q class="supprimer" title="Supprimer ce référentiel."></q>');
								$('#'+ids).prev().removeAttr("class").addClass("v").attr('lang',methode_calcul_langue).html(methode_calcul_texte);
								if(test_matiere_perso)
								{
									$('#'+ids).prev().prev().removeAttr("class").addClass("v").attr('lang','hs').html('Référentiel présent. <img title="Référentiel dont le partage est sans objet (matière spécifique)." src="./_img/partage0.gif" />');
								}
								else if(referentiel_id!='0')
								{
									$('#'+ids).prev().prev().removeAttr("class").addClass("v").attr('lang','bof').html('Référentiel présent. <img title="Référentiel dont le partage est sans intérêt (pas novateur)." src="./_img/partage0.gif" />');
								}
								else
								{
									$('#'+ids).prev().prev().removeAttr("class").addClass("v").attr('lang','non').html('Référentiel présent. <img title="Référentiel non partagé avec la communauté." src="./_img/partage0.gif" />');
								}
								infobulle();
								$('#choisir_annuler').click();
								$('#voir_referentiel').html('<ul class="puce"><li><label class="valide">Référentiel importé</label></li><li><span class="astuce">Pour éditer ce nouveau référentiel, utiliser la page "<a href="./index.php?page=professeur_referentiel&amp;section=edition">modifier le contenu des référentiels</a>".</span></li></ul>');
							}
						}
					}
				);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur l'image pour Annuler la suppression ou la modification du partage ou la modification du mode de calcul d'un référentiel
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('q.annuler').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				$(this).parent().remove();
				afficher_masquer_images_action('show');
			}
		);

	}
);
