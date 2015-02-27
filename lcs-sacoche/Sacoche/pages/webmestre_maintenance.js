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

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Afficher / masquer le choix du motif du blocage
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#f_debloquer , #f_bloquer').click
    (
      function()
      {
        if($('#f_bloquer').is(':checked'))
        {
          $('#span_motif').show();
          $('#f_motif').focus();
        }
        else
        {
          $('#span_motif').hide();
        }
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Autocompléter le motif du blocage
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    tab_proposition = new Array();
    tab_proposition["rien"]         = "";
    tab_proposition["mise-a-jour"]  = "Mise à jour des fichiers en cours.";
    tab_proposition["maintenance"]  = "Maintenance sur le serveur en cours.";
    tab_proposition["demenagement"] = "Déménagement de l'application en cours.";

    $('#f_proposition').change
    (
      function()
      {
        $('#f_motif').val( tab_proposition[ $(this).val() ] ).focus();
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Mise à jour des label comparant la version installée et la version disponible
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    function maj_label_versions()
    {
      var classe = ( $('#ajax_version_installee').text() == $('#ajax_version_disponible').text() ) ? 'valide' : 'alerte' ;
      $('#ajax_version_installee').removeAttr("class").addClass(classe);
    }

    maj_label_versions();

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Verrouillage de l'application
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    // Le formulaire qui va être analysé et traité en AJAX
    var formulaire = $('#form');

    // Vérifier la validité du formulaire (avec jquery.validate.js)
    var validation = formulaire.validate
    (
      {
        rules :
        {
          f_action : { required:true }
        },
        messages :
        {
          f_action : { required:"choix manquant" }
        },
        errorElement : "label",
        errorClass : "erreur",
        errorPlacement : function(error,element) { $('#ajax_msg').html(error); }
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
        $('#ajax_msg').removeAttr("class").addClass("loader").html("En cours&hellip;");
      }
      return readytogo;
    }

    // Fonction suivant l'envoi du formulaire (avec jquery.form.js)
    function retour_form_erreur(jqXHR, textStatus, errorThrown)
    {
      $("#bouton_valider").prop('disabled',false);
      $('#ajax_msg').removeAttr("class").addClass("alerte").html("Échec de la connexion !");
    }

    // Fonction suivant l'envoi du formulaire (avec jquery.form.js)
    function retour_form_valide(responseHTML)
    {
      initialiser_compteur();
      $("#bouton_valider").prop('disabled',false);
      if(responseHTML.substring(0,13)=='<label class=')
      {
        
        $('#ajax_msg').removeAttr("class").html("");
        $('#ajax_acces_actuel').html(responseHTML);
      }
      else
      {
        $('#ajax_msg').removeAttr("class").addClass("alerte").html(responseHTML);
      }
    }

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Mise à jour automatique des fichiers
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    var etape_numero = 0 ;

    function maj_etape(etape_info)
    {
      etape_numero++;
      if(etape_numero==6)
      {
        var tab_infos = etape_info.split('_#_');
        $('#ajax_maj').removeAttr("class").addClass("valide").html('Mise à jour terminée !');
        $('#ajax_version_installee').html(tab_infos[0]);
        maj_label_versions();
        $('button').prop('disabled',false);
        $.fancybox( { 'href':tab_infos[1] , 'type':'iframe' , 'width':'80%' , 'height':'80%' , 'centerOnScroll':true } );
        initialiser_compteur();
        return false;
      }
      $('#ajax_maj').removeAttr("class").addClass("loader").html('Etape '+etape_numero+' - '+etape_info);
      $.ajax
      (
        {
          type : 'POST',
          url : 'ajax.php?page='+PAGE,
          data : 'csrf='+CSRF+'&f_action=maj_etape'+etape_numero,
          dataType : "html",
          error : function(jqXHR, textStatus, errorThrown)
          {
            $('button').prop('disabled',false);
            $('#ajax_maj').removeAttr("class").addClass("alerte").html('Échec de la connexion !');
            return false;
          },
          success : function(responseHTML)
          {
            var tab_infos = responseHTML.split(']¤[');
            if( (tab_infos.length!=3) || (tab_infos[0]!='') )
            {
              $('button').prop('disabled',false);
              $('#ajax_maj').removeAttr("class").addClass("alerte").html(tab_infos[0]);
              return false;
            }
            if(tab_infos[1]!='ok')
            {
              $('button').prop('disabled',false);
              $('#ajax_maj').removeAttr("class").addClass("alerte").html(tab_infos[2]);
              return false;
            }
            maj_etape(tab_infos[2]);
          }
        }
      );
    }

    $('#bouton_maj').click
    (
      function()
      {
        etape_numero = 0 ;
        if( $('#ajax_version_installee').text() > $('#ajax_version_disponible').text() )
        {
          $('#ajax_maj').removeAttr("class").addClass("erreur").html("Version installée postérieure à la version disponible !");
          return false;
        }
        $('button').prop('disabled',true);
        maj_etape("Récupération de l'archive <em>zip</em>&hellip;");
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Vérification des fichiers de l'application en place
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    function verif_file_appli_etape(etape_info)
    {
      etape_numero++;
      if(etape_numero==6)
      {
        $('#ajax_verif_file_appli').removeAttr("class").addClass("valide").html('Vérification terminée !');
        $('button').prop('disabled',false);
        $.fancybox( { 'href':etape_info , 'type':'iframe' , 'width':'80%' , 'height':'80%' , 'centerOnScroll':true } );
        initialiser_compteur();
        return false;
      }
      $('#ajax_verif_file_appli').removeAttr("class").addClass("loader").html('Etape '+etape_numero+' - '+etape_info);
      $.ajax
      (
        {
          type : 'POST',
          url : 'ajax.php?page='+PAGE,
          data : 'csrf='+CSRF+'&f_action=verif_file_appli_etape'+etape_numero,
          dataType : "html",
          error : function(jqXHR, textStatus, errorThrown)
          {
            $('button').prop('disabled',false);
            $('#ajax_verif_file_appli').removeAttr("class").addClass("alerte").html('Échec de la connexion !');
            return false;
          },
          success : function(responseHTML)
          {
            var tab_infos = responseHTML.split(']¤[');
            if( (tab_infos.length!=3) || (tab_infos[0]!='') )
            {
              $('button').prop('disabled',false);
              $('#ajax_verif_file_appli').removeAttr("class").addClass("alerte").html(tab_infos[0]);
              return false;
            }
            if(tab_infos[1]!='ok')
            {
              $('button').prop('disabled',false);
              $('#ajax_verif_file_appli').removeAttr("class").addClass("alerte").html(tab_infos[2]);
              return false;
            }
            verif_file_appli_etape(tab_infos[2]);
          }
        }
      );
    }

    $('#bouton_verif_file_appli').click
    (
      function()
      {
        etape_numero = 0 ;
        $('button').prop('disabled',true);
        verif_file_appli_etape("Récupération de l'archive <em>zip</em>&hellip;");
      }
    );

    // ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Vérification des dossiers additionnels par établissement
    // ////////////////////////////////////////////////////////////////////////////////////////////////////

    $('#bouton_verif_dir_etabl').click
    (
      function()
      {
        $('button').prop('disabled',true);
        $('#ajax_verif_dir_etabl').removeAttr("class").addClass("loader").html("En cours&hellip;");
        $.ajax
        (
          {
            type : 'POST',
            url : 'ajax.php?page='+PAGE,
            data : 'csrf='+CSRF+'&f_action=verif_dir_etabl',
            dataType : "html",
            error : function(jqXHR, textStatus, errorThrown)
            {
              $('button').prop('disabled',false);
              $('#ajax_verif_dir_etabl').removeAttr("class").addClass("alerte").html('Échec de la connexion !');
              return false;
            },
            success : function(responseHTML)
            {
              $('button').prop('disabled',false);
              var tab_infos = responseHTML.split(']¤[');
              if( (tab_infos.length!=2) || (tab_infos[0]!='') )
              {
                $('#ajax_verif_dir_etabl').removeAttr("class").addClass("alerte").html(tab_infos[0]);
                return false;
              }
              else
              {
                $('#ajax_verif_dir_etabl').removeAttr("class").addClass("valide").html('Vérification terminée !');
                $.fancybox( { 'href':tab_infos[1] , 'type':'iframe' , 'width':'80%' , 'height':'80%' , 'centerOnScroll':true } );
                initialiser_compteur();
              }
            }
          }
        );
      }
    );

  }
);
