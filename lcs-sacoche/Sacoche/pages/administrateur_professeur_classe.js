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

		// Réagir au clic dans un select
		$('select').click
		(
			function()
			{
				$('#ajax_msg').removeAttr("class").addClass("alerte").html("Pensez à valider vos modifications !");
			}
		);

		// Réagir au clic sur un bouton (soumission du formulaire)
		$('#ajouter , #retirer').click
		(
			function()
			{
				id = $(this).attr('id');
				if( $("#select_professeurs option:selected").length==0 || $("#select_classes option:selected").length==0 )
				{
					$('#ajax_msg').removeAttr("class").addClass("erreur").html("Sélectionnez dans les deux listes !");
					return(false);
				}
				$('button').prop('disabled',true);
				$('#ajax_msg').removeAttr("class").addClass("loader").html("Demande envoyée...");
				$.ajax
				(
					{
						type : 'POST',
						url : 'ajax.php?page='+PAGE+'&action='+id,
						data : $("form").serialize(),
						dataType : "html",
						error : function(msg,string)
						{
							$('button').prop('disabled',false);
							$('#ajax_msg').removeAttr("class").addClass("alerte").html("Echec de la connexion !");
							return false;
						},
						success : function(responseHTML)
						{
							initialiser_compteur();
							$('button').prop('disabled',false);
							if(responseHTML.substring(0,6)!='<hr />')
							{
								$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
							}
							else
							{
								$('#ajax_msg').removeAttr("class").addClass("valide").html("Demande réalisée !");
								$('#bilan').html(responseHTML);
							}
						}
					}
				);
			}
		);

		// Initialisation : charger au chargement l'affichage du bilan
		$('#ajax_msg').addClass("loader").html("Chargement en cours...");
		$.ajax
		(
			{
				type : 'POST',
				url : 'ajax.php?page='+PAGE+'&action=initialiser',
				data : '',
				dataType : "html",
				error : function(msg,string)
				{
					$('#ajax_msg').removeAttr("class").addClass("alerte").html("Echec de la connexion !");
					return false;
				},
				success : function(responseHTML)
				{
					initialiser_compteur();
					if(responseHTML.substring(0,6)!='<hr />')
					{
						$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
					}
					else
					{
						$('#ajax_msg').removeAttr("class").html("&nbsp;");
						$('#bilan').html(responseHTML);
					}
				}
			}
		);

	}
);
