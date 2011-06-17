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

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Formulaire et traitement
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		// Le formulaire qui va être analysé et traité en AJAX
		var formulaire = $("#newsletter");

		// Vérifier la validité du formulaire (avec jquery.validate.js)
		var validation = formulaire.validate
		(
			{
				rules :
				{
					f_base    : { required:true },
					f_titre   : { required:true },
					f_contenu : { required:true }
				},
				messages :
				{
					f_base    : { required:"destinataire(s) manquant(s)" },
					f_titre   : { required:"titre manquant" },
					f_contenu : { required:"contenu manquant" }
				},
				errorElement : "label",
				errorClass : "erreur",
				errorPlacement : function(error,element) { element.after(error); }
				// success: function(label) {label.text("ok").removeAttr("class").addClass("valide");} Pas pour des champs soumis à vérification PHP
			}
		);

		// Options d'envoi du formulaire (avec jquery.form.js)
		var ajaxOptions =
		{
			url : 'ajax.php?page='+PAGE,
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
				// grouper les select multiples => normalement pas besoin si name de la forme nom[], mais ça plante curieusement sur le serveur competences.sesamath.net
				// alors j'ai copié le tableau dans un champ hidden...
				var bases = new Array(); $("#f_base option:selected").each(function(){bases.push($(this).val());});
				$('#f_listing_id').val(bases);
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
				$("#bouton_valider").prop('disabled',true);
				$('#ajax_msg').removeAttr("class").addClass("loader").html("Préparation de l'envoi... Veuillez patienter.");
			}
			return readytogo;
		}

		// Fonction suivant l'envoi du formulaire (avec jquery.form.js)
		function retour_form_erreur(msg,string)
		{
			$("#bouton_valider").prop('disabled',false);
			$('#ajax_msg').removeAttr("class").addClass("alerte").html("Echec de la connexion ! Veuillez valider de nouveau.");
		}

		// Fonction suivant l'envoi du formulaire (avec jquery.form.js)
		function retour_form_valide(responseHTML)
		{
			maj_clock(1);
			if(responseHTML.substring(0,2)!='ok')
			{
				$("#bouton_valider").prop('disabled',false);
				$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
			}
			else
			{
				var max = responseHTML.substring(3,responseHTML.length);
				$('#ajax_msg1').removeAttr("class").addClass("loader").html('Lettre d\'information en cours d\'envoi : étape 1 sur ' + max + '...');
				$('#ajax_msg2').html('Ne pas interrompre la procédure avant la fin du traitement !');
				$('#ajax_num').html(1);
				$('#ajax_max').html(max);
				$('#ajax_info').show('fast');
				$('#newsletter').hide('fast');
				envoyer();
			}
		} 

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
// Etapes d'envoi de la newsletter
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		function envoyer()
		{
			var num = parseInt( $('#ajax_num').html() );
			var max = parseInt( $('#ajax_max').html() );
			// Appel en ajax
			$.ajax
			(
				{
					type : 'POST',
					url : 'ajax.php?page='+PAGE,
					data : 'f_action=' + 'envoyer' + '&num=' + num + '&max=' + max,
					dataType : "html",
					error : function(msg,string)
					{
						$('#ajax_msg1').removeAttr("class").addClass("alerte").html('Echec lors de la connexion au serveur !');
						$('#ajax_msg2').html('<a id="a_reprise" href="#">Reprendre la procédure à l\'étape ' + num + ' sur ' + max + '.</a>');
					},
					success : function(responseHTML)
					{
						if(responseHTML=='ok')
						{
							num++;
							if(num > max)	// Utilisation de parseInt obligatoire sinon la comparaison des valeurs pose ici pb
							{
								$('#ajax_msg1').removeAttr("class").addClass("valide").html('Envoi de la lettre d\'informations terminée.');
								$('#ajax_msg2').html('<a id="a_retour" href="#">Retour au formulaire.</a>');
							}
							else
							{
								$('#ajax_num').html(num);
								$('#ajax_msg1').removeAttr("class").addClass("loader").html('Lettre d\'information en cours d\'envoi : étape ' + num + ' sur ' + max + '...');
								$('#ajax_msg2').html('Ne pas interrompre la procédure avant la fin du traitement !');
								envoyer();
							}
						}
						else
						{
							$('#ajax_msg1').removeAttr("class").addClass("alerte").html(responseHTML);
							$('#ajax_msg2').html('<a id="a_reprise" href="#">Reprendre la procédure à l\'étape ' + num + ' sur ' + max + '.</a>');
						}
					}
				}
			);
		}

		// live est utilisé pour prendre en compte les nouveaux éléments html créés

		$('#a_reprise').live
		('click',
			function()
			{
				num = $('#ajax_num').html();
				max = $('#ajax_max').html();
				$('#ajax_msg1').removeAttr("class").addClass("loader").html('Lettre d\'information en cours d\'envoi : étape ' + num + ' sur ' + max + '...');
				$('#ajax_msg2').html('Ne pas interrompre la procédure avant la fin du traitement !');
				envoyer();
			}
		);

		$('#a_retour').live
		('click',
			function()
			{
				$('#ajax_msg').removeAttr("class").html("&nbsp;");
				$("#bouton_valider").prop('disabled',false);
				$('#ajax_info').hide('fast');
				$('#newsletter').show('fast');
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur un bouton pour effectuer une action sur les structures sélectionnées
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		var supprimer_structures_selectionnees = function(listing_id)
		{
			$("button").prop('disabled',true);
			// afficher_masquer_images_action('hide');
			$('#ajax_supprimer').removeAttr("class").addClass("loader").html("Demande envoyée... Veuillez patienter.");
			$.ajax
			(
				{
					type : 'POST',
					url : 'ajax.php?page='+PAGE,
					data : 'f_action=supprimer&f_listing_id='+listing_id,
					dataType : "html",
					error : function(msg,string)
					{
						$('#ajax_supprimer').removeAttr("class").addClass("alerte").html("Echec de la connexion ! Veuillez recommencer.");
						$("button").prop('disabled',false);
						// afficher_masquer_images_action('show');
					},
					success : function(responseHTML)
					{
						maj_clock(1);
						if(responseHTML!='<ok>')	// Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
						{
							$('#ajax_supprimer').removeAttr("class").addClass("alerte").html(responseHTML);
						}
						else
						{
							$("#f_base option:selected").each
							(
								function()
								{
									$(this).remove();
								}
							);
							$('#ajax_supprimer').removeAttr("class").html('&nbsp;');
							$("button").prop('disabled',false);
							// afficher_masquer_images_action('show');
						}
					}
				}
			);
		};

		$('#zone_actions button').click
		(
			function()
			{
				var listing_id = new Array(); $("#f_base option:selected").each(function(){listing_id.push($(this).val());});
				if(!listing_id.length)
				{
					$('#ajax_supprimer').removeAttr("class").addClass("erreur").html("Aucune structure sélectionnée !");
					return false;
				}
				$('#ajax_supprimer').removeAttr("class").html('&nbsp;');
				var id = $(this).attr('id');
				if(id=='bouton_supprimer')
				{
					if(confirm("Toutes les bases des structures sélectionnées seront supprimées !\nConfirmez-vous vouloir effacer les données de ces structures ?"))
					{
						supprimer_structures_selectionnees(listing_id);
					}
				}
				else
				{
					$('#listing_ids').val(listing_id);
					var tab = new Array;
					// tab['bouton_newsletter'] = "webmestre_newsletter";
					tab['bouton_stats']      = "webmestre_statistiques";
					tab['bouton_transfert']  = "webmestre_structure_transfert";
					var page = tab[id];
					var form = document.getElementById('structures');
					form.action = './index.php?page='+page;
					form.method = 'post';
					// form.target = '_blank';
					form.submit();
				}
			}
		);

	}
);
