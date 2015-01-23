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
            Mettre_a_jour: function() {
                var lejeton=$("#jeton").val();
                var donnees= $("#contenu").val();
                $.ajax({
                        type: "POST",
                        url : "dns_ajax.php",
                        data : {  jeton: lejeton,action: "majz",cont:donnees},
                        async: false,
                         success :function(data){
                            if (data == 1){
                                updateTips( "Une erreur s'est produite lors de la mise à jour !", "ui-state-error" );}
                            else {
                                 updateTips( "La zone DNS locale a été mise à jour.", "ui-state-highlight");
                            }
                        }
                     });
                    //setTimeout(function() {($( this ).dialog( "close" ));},1000);
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
                            if (data == "NOK") alert ("Erreur dans la lecture du fichier: " );
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


});


