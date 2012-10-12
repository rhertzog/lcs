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
		// Actualiser l'affichage des vignettes élèves au changement du select
		// ////////////////////////////////////////////////////////////////////////////////////////////////////

		function maj_affichage()
		{
			$('#bilan').html('');
			// On récupère le regroupement
			var groupe_val = $("#f_groupe option:selected").val();
			if(!groupe_val)
			{
				$('#ajax_msg').removeAttr("class").html("&nbsp;");
				return false
			}
			// Pour un directeur ou un administrateur, groupe_val est de la forme d3 / n2 / c51 / g44
			if(isNaN(parseInt(groupe_val,10)))
			{
				groupe_type = groupe_val.substring(0,1);
				groupe_id   = groupe_val.substring(1);
			}
			// Pour un professeur, groupe_val est un entier, et il faut récupérer la 1ère lettre du label parent
			else
			{
				groupe_type = $("#f_groupe option:selected").parent().attr('label').substring(0,1).toLowerCase();
				groupe_id   = groupe_val;
			}
			groupe_nom = $("#f_groupe option:selected").text();
			$('#ajax_msg').removeAttr("class").addClass("loader").html("Connexion au serveur&hellip;");
			$.ajax
			(
				{
					type : 'POST',
					url : 'ajax.php?page='+PAGE,
					data : 'csrf='+CSRF+'&f_groupe_id='+groupe_id+'&f_groupe_type='+groupe_type+'&f_groupe_nom='+groupe_nom,
					dataType : "html",
					error : function(jqXHR, textStatus, errorThrown)
					{
						$('#ajax_msg').removeAttr("class").addClass("alerte").html("Échec de la connexion !");
					},
					success : function(responseHTML)
					{
						initialiser_compteur();
						if(responseHTML.substring(0,4)!='<h2>')
						{
							$('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
						}
						else
						{
							$('#ajax_msg').removeAttr("class").addClass("valide").html("Demande réalisée !");
							$('#bilan').html(responseHTML);
							format_liens('#bilan');
							infobulle();
						}
					}
				}
			);
		}

		$("#f_groupe").change
		(
			function()
			{
				maj_affichage();
			}
		);

		// ////////////////////////////////////////////////////////////////////////////////////////////////////
		// Imprimer un trombinoscope
		// ////////////////////////////////////////////////////////////////////////////////////////////////////

		$('#bilan q.imprimer').live
		('click',
			function()
			{
				imprimer(document.getElementById('bilan').innerHTML);
			}
		);

	}
);
