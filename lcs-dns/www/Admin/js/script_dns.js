/*===========================================
   Projet LcSE3
   Administration du serveur LCS
   Equipe Tice academie de Caen
   Distribue selon les termes de la licence GPL
   Derniere modification : 23/01/2015
   ============================================= */

jQuery().ready(function() {
    $( "input[type=submit], a, button" ).button() ;

var tips = $( ".validateTips" );
//affichage messages
function updateTips( t,klass ) {
    tips.removeClass("ui-state-highlight ui-state-error")
    tips.text( t ).addClass(klass);
    }

// config : dialog
    var $dialog = $( "#dialog" ).dialog({
            autoOpen: false,
            modal: true,
            position: ['center','center'],
            width: 600,
            show: "puff",
            hide: "explode",
            buttons: {
            Mettre_à_jour: function() {
                var lejeton=$("#jeton").val();
                var donnees= $("#contenu").val();
                $.ajax({
                        type: "POST",
                        url : "dns_ajax.php",
                        data : {  jeton: lejeton,action: "majz",cont:donnees},
                        async: false,
                         success :function(data){
                            if (data == 1){
                                updateTips( "Une erreur a empêché la mise à jour de la zone DNS ! ", "ui-state-error" );}
                            else {
                                 updateTips( "La zone DNS locale a été mise à jour.", "ui-state-highlight");
                            }
                        }
                     });
                 },
            Quitter: function() {
                $( this ).dialog( "close" );
                }
            },
            open: function() {
                   // $tab_title_input.focus();
                   var lejeton=$("#jeton").val();
                   //$("#contenu").text("init");
                   $.ajax({
                        type: "POST",
                        url : "dns_ajax.php",
                        data : { jeton: lejeton, action: "edite"},
                        async: false,
                         success :function(data){
                            if (data == "NOK") updateTips("Erreur dans la lecture du fichier: ","ui-state-error" );
                            else $("#contenu").text(data);
                            }
                     });

            },
            close: function() {
                $("#contenu").text("");
                tips.text("");
                setTimeout(function() {$("#form1").submit();},300);
            }
    });


//ouverture dialog
    $( "#genere" )
            .button()
            .click(function() {
                    $dialog.dialog( "open" );
            });

$("#contenu").mouseenter(function() {tips.text("");tips.removeClass("ui-state-highlight ui-state-error")});
});

