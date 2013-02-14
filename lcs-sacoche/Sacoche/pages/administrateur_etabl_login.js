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

    var profil = '';

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Alerter sur la nécessité de valider
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('input').change
    (
      function()
      {
        profil = $(this).attr('id').substr(8); // f_login_XXX
        $('#ajax_msg_'+profil).removeAttr('class').addClass('alerte').html("Pensez à valider !");
      }
    );

    $('select').change
    (
      function()
      {
        profil = $(this).attr('id').substr(6); // f_mdp_XXX
        $('#ajax_msg_'+profil).removeAttr('class').addClass('alerte').html("Pensez à valider !");
      }
    );

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Format des noms d'utilisateurs
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    function test_format_login(format)
    {
      var reg1 = new RegExp("^p+[._-]?n+$","g"); // prénom puis nom
      var reg2 = new RegExp("^n+[._-]?p+$","g"); // nom puis prénom
      var reg3 = new RegExp("^p+$","g"); // prénom seul
      var reg4 = new RegExp("^n+$","g"); // nom seul
      test = ( reg1.test(format) || reg2.test(format) || reg3.test(format) || reg4.test(format) ) ? true : false ;
      return test;
    }

// ////////////////////////////////////////////////////////////////////////////////////////////////////
// Validation du formulaire
// ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('button[class=parametre]').click
    (
      function()
      {
        profil = $(this).attr('id').substr(15); // bouton_valider_XXX
        var login = $('#f_login_'+profil).val();
        var mdp   = $('#f_mdp_'+profil+' option:selected').val();
        if( test_format_login(login)==false )
        {
          $('#ajax_msg_'+profil).removeAttr('class').addClass("erreur").html("Modèle de nom d'utilisateur incorrect !");
          return(false);
        }
        $('#bouton_valider_'+profil).prop('disabled',true);
        $('#ajax_msg_'+profil).removeAttr('class').addClass('loader').html("En cours&hellip;");
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&f_profil='+profil+'&f_login='+login+'&f_mdp='+mdp,
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $('#bouton_valider_'+profil).prop('disabled',false);
              $('#ajax_msg_'+profil).removeAttr('class').addClass('alerte').html("Échec de la connexion !");
              return false;
            },
            success : function(responseHTML)
            {
              initialiser_compteur();
              $('#bouton_valider_'+profil).prop('disabled',false);
              if(responseHTML!='ok')
              {
                $('#ajax_msg_'+profil).removeAttr('class').addClass('alerte').html(responseHTML);
              }
              else
              {
                if(profil=='ALL')
                {
                  $('input[type=text]').val(login);
                  $('select option[value='+mdp+']').prop('selected',true);
                  $('label[id^=ajax_msg_]').removeAttr('class').html("&nbsp;");
                }
                $('#ajax_msg_'+profil).removeAttr('class').addClass('valide').html("Valeurs enregistrées !");
                initialiser_compteur();
              }
            }
          }
        );
      }
    );

  }
);
