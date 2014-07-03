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

// jQuery !
$(document).ready
(
  function()
  {

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Gestion de l'ordre des matières avec jQuery UI Sortable
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    var modification = false;

    function modif_ordre()
    {
      if(modification==false)
      {
        $('#ajax_msg_ordre').removeAttr("class").addClass("alerte").html("Ordre non enregistré !");
        modification = true;
        return false;
      }
    }

    $('#sortable').sortable( { cursor:'n-resize' , update:function(event,ui){modif_ordre();} } );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Clic sur le lien pour mettre à jour l'ordre des matières
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#Enregistrer_ordre').click
    (
      function()
      {
        if(!modification)
        {
          $('#ajax_msg_ordre').removeAttr("class").addClass("alerte").html("Aucune modification effectuée !");
        }
        else
        {
          // On récupère la liste des matières dans l'ordre de la page
          var tab_id = new Array();
          $('#sortable').children('li').each
          (
            function()
            {
              var test_id = $(this).attr('id').substring(2);
              if(test_id)
              {
                tab_id.push(test_id);
              }
            }
          );
          $('#form_ordonner button').prop('disabled',true);
          $('#ajax_msg_ordre').removeAttr("class").addClass("loader").html("En cours&hellip;");
          $.ajax
          (
            {
              type : 'POST',
              url : 'ajax.php?page='+PAGE,
              data : 'csrf='+CSRF+'&tab_id='+tab_id,
              dataType : "html",
              error : function(jqXHR, textStatus, errorThrown)
              {
                $('#form_ordonner button').prop('disabled',false);
                $('#ajax_msg_ordre').removeAttr("class").addClass("alerte").html('Échec de la connexion !');
                return false;
              },
              success : function(responseHTML)
              {
                initialiser_compteur();
                $('#form_ordonner button').prop('disabled',false);
                if(responseHTML!='ok')
                {
                  $('#ajax_msg_ordre').removeAttr("class").addClass("alerte").html(responseHTML);
                }
                else
                {
                  modification = false;
                  $('#ajax_msg_ordre').removeAttr("class").addClass("valide").html("Ordre enregistré !");
                }
              }
            }
          );
        }
      }
    );

  }
);
