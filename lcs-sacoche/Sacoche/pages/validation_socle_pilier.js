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
//	Tester l'affichage du bouton de validation au changement des formulaires
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		var maj_bouton_validation = function()
		{
			if($("#f_eleve").val())
			{
				$('#Afficher_validation').show();
			}
			else
			{
				$('#Afficher_validation').hide();
			}
		};

//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*
//	Charger le select f_pilier en ajax
//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*

		var maj_pilier = function()
		{
			$("#f_pilier").html('<option value=""></option>').hide();
			palier_id = $("#f_palier").val();
			if(palier_id)
			{
				$('#ajax_maj_pilier').removeAttr("class").addClass("loader").html("Actualisation en cours... Veuillez patienter.");
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page=_maj_select_piliers',
						data : 'f_palier='+palier_id+'&f_first='+'non',
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_maj_pilier').removeAttr("class").addClass("alerte").html("Echec de la connexion ! Veuillez essayer de nouveau.");
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,7)=='<option')	// Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
							{
								$('#ajax_maj_pilier').removeAttr("class").html('&nbsp;');
								$('#f_pilier').html(responseHTML).attr('size',$('#f_pilier option').size()).show();
							}
							else
							{
								$('#ajax_maj_pilier').removeAttr("class").addClass("alerte").html(responseHTML);
							}
						}
					}
				);
			}
			else
			{
				$('#ajax_maj_pilier').removeAttr("class").html("&nbsp;");
			}
		};

		$("#f_palier").change( maj_pilier );

		maj_pilier();

//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*
//	Charger le select f_eleve en ajax
//	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*	*

		var maj_eleve = function()
		{
			$("#f_eleve").html('<option value=""></option>').hide();
			groupe_id = $("#f_groupe").val();
			if(groupe_id)
			{
				groupe_type = $("#f_groupe option:selected").parent().attr('label');
				if(typeof(groupe_type)=='undefined') {groupe_type = 'Classes';} // Cas d'un P.P.
				$('#ajax_maj_eleve').removeAttr("class").addClass("loader").html("Actualisation en cours... Veuillez patienter.");
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page=_maj_select_eleves',
						data : 'f_groupe='+groupe_id+'&f_type='+groupe_type+'&f_statut=1',
						dataType : "html",
						error : function(msg,string)
						{
							$('#ajax_maj_eleve').removeAttr("class").addClass("alerte").html("Echec de la connexion ! Veuillez essayer de nouveau.");
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							if(responseHTML.substring(0,7)=='<option')	// Attention aux caractères accentués : l'utf-8 pose des pbs pour ce test
							{
								$('#ajax_maj_eleve').removeAttr("class").html("&nbsp;");
								$('#f_eleve').html(responseHTML).show();
								maj_bouton_validation();
							}
							else
							{
								$('#ajax_maj_eleve').removeAttr("class").addClass("alerte").html(responseHTML);
								maj_bouton_validation();
							}
						}
					}
				);
			}
			else
			{
				$('#ajax_maj_eleve').removeAttr("class").html("&nbsp;");
				maj_bouton_validation();
			}
		};

		$("#f_groupe").change( maj_eleve );

		maj_eleve(); // Dans le cas d'un P.P.

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Traitement du premier formulaire pour afficher le tableau avec les états de validations
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		// Le formulaire qui va être analysé et traité en AJAX
		var formulaire0 = $('#zone_choix');

		// Vérifier la validité du formulaire (avec jquery.validate.js)
		var validation0 = formulaire0.validate
		(
			{
				rules :
				{
					f_pilier : { required:true },
					f_groupe : { required:true },
					f_eleve  : { required:true }
				},
				messages :
				{
					f_pilier : { required:"compétence(s) manquante(s)" },
					f_groupe : { required:"classe / groupe manquant" },
					f_eleve  : { required:"élève(s) manquant(s)" }
				},
				errorElement : "label",
				errorClass : "erreur",
				errorPlacement : function(error,element){element.after(error);}
			}
		);

		// Options d'envoi du formulaire (avec jquery.form.js)
		var ajaxOptions0 =
		{
			type : 'POST',
			url : 'ajax.php?page='+PAGE,
			dataType : "html",
			clearForm : false,
			resetForm : false,
			target : "#ajax_msg_choix",
			beforeSubmit : test_form_avant_envoi0,
			error : retour_form_erreur0,
			success : retour_form_valide0
		};

		// Envoi du formulaire (avec jquery.form.js)
		formulaire0.submit
		(
			function()
			{
				// grouper les select multiples => normalement pas besoin si name de la forme nom[], mais ça plante curieusement sur le serveur competences.sesamath.net
				// alors j'ai copié le tableau dans un champ hidden...
				var tab_eleve = new Array(); $("#f_eleve option:selected").each(function(){tab_eleve.push($(this).val());});
				$('#eleves').val(tab_eleve);
				var tab_pilier = new Array(); $("#f_pilier option:selected").each(function(){tab_pilier.push($(this).val());});
				$('#piliers').val(tab_pilier);
				$(this).ajaxSubmit(ajaxOptions0);
				return false;
			}
		); 

		// Fonction précédent l'envoi du formulaire (avec jquery.form.js)
		function test_form_avant_envoi0(formData, jqForm, options)
		{
			$('#ajax_msg_choix').removeAttr("class").html("&nbsp;");
			var readytogo = validation0.form();
			if(readytogo)
			{
				$("button").prop('disabled',true);
				$('#ajax_msg_choix').removeAttr("class").addClass("loader").html("Demande envoyée... Veuillez patienter.");
			}
			return readytogo;
		}

		// Fonction suivant l'envoi du formulaire (avec jquery.form.js)
		function retour_form_erreur0(msg,string)
		{
			$("button").prop('disabled',false);
			$('#ajax_msg_choix').removeAttr("class").addClass("alerte").html("Echec de la connexion ! Veuillez recommencer.");
		}

		// Fonction suivant l'envoi du formulaire (avec jquery.form.js)
		function retour_form_valide0(responseHTML)
		{
			maj_clock(1);
			$("button").prop('disabled',false);
			if(responseHTML.substring(0,7)!='<thead>')
			{
				$('#ajax_msg_choix').removeAttr("class").addClass("alerte").html(responseHTML);
			}
			else
			{
				$('#ajax_msg_choix').removeAttr("class").addClass("valide").html("Affichage réalisé !").fadeOut(3000,function(){$(this).removeAttr("class").html("").show();});
				responseHTML = responseHTML.replace( '@PALIER@' , $("#f_palier option:selected").text() );
				$('#tableau_validation').html(responseHTML);
				infobulle();
				$('#zone_validation').show('fast');
				$('#zone_choix').hide('fast');
				$('#zone_information').show('fast');
				$("body").oneTime("1s", function() {window.scrollTo(0,1000);} );
			}
		}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur une cellule du tableau => Modifier visuellement des états de validation
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		var tab_class_next = new Array;
		tab_class_next['1'] = ['0'];
		tab_class_next['0'] = ['2'];
		tab_class_next['2'] = ['1'];

		$('#tableau_validation tbody td').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				// Appliquer un état pour un item pour un élève
				var classe = $(this).attr('class');
				var new_classe = classe.charAt(0) + tab_class_next[classe.charAt(1)] ;
				$(this).removeAttr("class").addClass(new_classe);
				$('#ajax_msg_validation').removeAttr("class").addClass("alerte").html('Penser à valider les modifications !');
				$('#fermer_zone_validation').html('<img alt="" src="./_img/bouton/annuler.png" /> Annuler / Retour');
				return false;
			}
		);

		$('#tableau_validation tbody th').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				var classe = $(this).attr('class');
				if(classe=='nu')
				{
					// Intitulé du socle
					return false;
				}
				$('#ajax_msg_validation').removeAttr("class").addClass("alerte").html('Penser à valider les modifications !');
				$('#fermer_zone_validation').html('<img alt="" src="./_img/bouton/annuler.png" /> Annuler / Retour');
				var classe_debut = classe.substring(0,4);
				var classe_fin   = classe.charAt(4);
				var new_classe_th = classe_debut + tab_class_next[classe_fin] ;
				var new_classe_td = 'v' + classe_fin ;
				if(classe_debut=='left')
				{
					// Appliquer un état pour un pilier pour tous les élèves
					$(this).removeAttr("class").addClass(new_classe_th).parent().children('td').removeAttr("class").addClass(new_classe_td);
					return false;
				}
				if(classe_debut=='down')
				{
					// Appliquer un état pour tout le palier pour un élève
					var id = $(this).attr('id') + 'C';
					$(this).removeAttr("class").addClass(new_classe_th).parent().parent().find('td[id^='+id+']').removeAttr("class").addClass(new_classe_td);
					return false;
				}
				if(classe_debut=='diag')
				{
					// Appliquer un état pour tous les piliers pour tous les élèves
					$(this).removeAttr("class").addClass(new_classe_th).parent().parent().find('td').removeAttr("class").addClass(new_classe_td);
					return false;
				}
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Survol prolongé d'une cellule du tableau => Recharger la zone d'informations
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		var last_id_survole  = '';
		var last_id_memorise = '';
		var last_id_affiche = '';

		$("#tableau_validation tbody td").live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('mouseout',
			function()
			{
				last_id_survole = '';
			}
		);

		$("#tableau_validation tbody td").live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('mouseover',
			function()
			{
				last_id_survole = $(this).attr('id');
			}
		);

		function surveiller_id()
		{
			$("body").everyTime
			('5ds', function()
				{
					if( (last_id_survole=='') || (last_id_survole!=last_id_memorise) || (last_id_survole==last_id_affiche) )
					{
						last_id_memorise = last_id_survole;
					}
					else
					{
						last_id_memorise = last_id_survole;
						last_id_affiche  = last_id_survole;
						maj_zone_information(last_id_survole);
					}
				}
			);
		}

		surveiller_id();

		function maj_zone_information(last_id_survole)
		{
			var pos_C = last_id_survole.indexOf('C');
			var pilier_id = last_id_survole.substring(pos_C+1);
			var user_id   = last_id_survole.substring(1,pos_C);
			$('#identite').html( $('#I'+user_id).attr('alt') );
			$('#pilier').html( $('#C'+pilier_id).next('th').children('div').text() );
			$('#stats').html('');
			$('#items').html('');
			$('#ajax_msg_information').removeAttr("class").addClass("loader").html("Demande d'informations envoyée... Veuillez patienter.");
			$.ajax
			(
				{
					type : 'POST',
					url : 'ajax.php?page='+PAGE,
					data : 'f_action=Afficher_information&f_user='+user_id+'&f_pilier='+pilier_id,
					dataType : "html",
					error : function(msg,string)
					{
						$('#ajax_msg_information').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
						return false;
					},
					success : function(responseHTML)
					{
						// maj_clock(1);
						pos = responseHTML.indexOf('@');
						if(pos==-1)
						{
							$('#ajax_msg_information').removeAttr("class").addClass("alerte").html(responseHTML);
						}
						else
						{
							$('#ajax_msg_information').removeAttr("class").html('&nbsp;');
							$('#stats').html( responseHTML.substring(0,pos) );
							$('#items').html( responseHTML.substring(pos+1) );
						}
					}
				}
			);
		}

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur le bouton pour fermer la zone de validation
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#fermer_zone_validation').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				$('#zone_choix').show('fast');
				$('#zone_validation').hide('fast');
				$('#tableau_validation').html('<tbody><tr><td></td></tr></tbody>');
				// Vider aussi la zone d'informations
				$('#zone_information').hide('fast');
				$('#identite').html('');
				$('#pilier').html('');
				$('#stats').html('');
				$('#items').html('');
				$('#ajax_msg_information').removeAttr("class").html('');
				return(false);
			}
		);

//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-
//	Clic sur le bouton pour envoyer les validations
//	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-	-

		$('#Enregistrer_validation').live // live est utilisé pour prendre en compte les nouveaux éléments créés
		('click',
			function()
			{
				$("button").prop('disabled',true);
				$('#ajax_msg_validation').removeAttr("class").addClass("loader").html("Demande envoyée... Veuillez patienter.");
				// Récupérer les infos
				var tab_valid = new Array();
				$("#tableau_validation tbody td").each
				(
					function()
					{
						tab_valid.push( $(this).attr('id') + $(this).attr('class').toUpperCase() );
					}
				);
				// Les envoyer en ajax
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE,
						data : 'f_action=Enregistrer_validation&f_valid='+tab_valid,
						dataType : "html",
						error : function(msg,string)
						{
							$("button").prop('disabled',false);
							$('#ajax_msg_validation').removeAttr("class").addClass("alerte").html('Echec de la connexion ! Veuillez recommencer.');
							return false;
						},
						success : function(responseHTML)
						{
							maj_clock(1);
							$("button").prop('disabled',false);
							if(responseHTML.substring(0,2)!='OK')
							{
								$('#ajax_msg_validation').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								$('td.v1').attr('lang','lock');
								$('#ajax_msg_validation').removeAttr("class").addClass("valide").html("Validations enregistrées !");
								$('#fermer_zone_validation').html('<img alt="" src="./_img/bouton/retourner.png" /> Retour');
							}
						}
					}
				);
			}
		);

	}
);

