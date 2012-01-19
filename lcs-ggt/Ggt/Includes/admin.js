$(document).ready(function() {

var name = $( "#tab_title" ),
tips = $( ".validateTips" );

function updateTips( t ) {
    tips.text( t ).addClass( "ui-state-highlight" );
    setTimeout(function() {
    tips.removeClass( "ui-state-highlight", 1500 );
    }, 500 );
}

//fonction ajout d'un niveau appelee par le form dialog
function addTab(thename,prof_coor) {
    if (thename != "" && prof_coor != "")
        {
        $.ajax({
            type: "POST",
            url : "get_fiche.php",
            data : {name_level : thename , innateur: prof_coor, actionN : "new_level" },
            async: false,
             success :function(data)
                {
                if (data != "OK") alert('Erreur' +data);
                }
            });
        $("#form1").submit();
    }
}


 //maj bdd apres deplacement niveau
 function updatebdd() {
        var pos=0;
        $("#tabs").find("li.ong").each(function() {
                var num_niv = $(this).find('a').attr('tabindex');
                var num_ordre=$('li').index(this);
                $.ajax({
                    type: "POST",
                    url : "get_fiche.php",
                    data : {num_level : num_niv , posission : num_ordre, actionN: "up_ordre" },
                    async: false,
                     success :function(data)
                        {
                        if (data !="OK")   alert('Erreur' +data);
                        }
                    });
        });
 }

//
//deplacement d'un niveau
 $(function() {
    var $tab_items =$( "#tabs" ).tabs().find( ".ui-tabs-nav" ).sortable({
        axis: "x" ,
        stop: function(ev, ui) {
          updatebdd();
         }
        });
 });



// mise a jour du niveau dans les forms
$(function() {
        $("a").click(function() {
        var nums=$(this).attr("tabindex");
        $("#niveau").val(nums);
    });
});



//tabs

$(function() {
     var $tab_items =$( "#tabs" ).tabs().find( ".ui-tabs-nav" ).sortable({
            axis: "x" ,
            stop: function(ev, ui) {
            updatebdd();
            }
        });
    });


$(function() {
            var $tab_title_input = $( "#tab_title");
            var $tab_content_input = $( "#tab_content" );
            var tab_counter = 2;

            // tabs init with a custom tab template and an "add" callback filling in the content
            var $tabs = $( "#tabs").tabs({
                    tabTemplate: "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close'>Remove Tab</span></li>",
                    add: function( event, ui ) {
                    var tab_content = $tab_content_input.val() || "Tab " + tab_counter + " content.";
                    $( ui.panel ).append( "<p>" + tab_content + "</p>" );
                    }
            });


    // dialog creation niveau
    var $dialog = $( "#dialog" ).dialog({
            autoOpen: false,
            modal: true,
            position: ['center','center'],
            width: 500,
            show: "puff",
            //hide: "explode",
            buttons: {
            Valider: function() {
                var tab_title = $( "#tab_title" );
                var prof=$("#coord option:selected").val();
                if (tab_title.val() !="") {
                    addTab(tab_title.val(),prof);
                    $( this ).dialog( "close" );
                    }
                else
                    {
                    name.addClass( "ui-state-error" );
                    updateTips( "Un nom valide est requis." );
                    }
                },
            Annuler: function() {
                $( this ).dialog( "close" );
                }
            },
            open: function() {
                   // $tab_title_input.focus();
            },
            close: function() {
                    //$form[ 0 ].reset();
            }
    });


    //dialog modifier niveau
    var $dialogbis = $( "#dialogbis" ).dialog({
            autoOpen: false,
            modal: true,
            position: ['center','top'],
            width: 500,
            show: "puff",
            //hide: "explode",
            buttons: {
                Valider: function() {
                    var id_niveau=$("#niv_id").val();
                    var nom_niveau=$("#tab_titlebis").val();
                    var prof_niveau=$("#coordbis option:selected").val();
                    if (nom_niveau !="") {
                    $.ajax({
                        type: "POST",
                        url : "get_fiche.php",
                        data : { num_niveau: id_niveau , titre: nom_niveau, statut: prof_niveau,  actionN : "update_niveau"},
                        async: false,
                         success :function(data){
                            if (data != "OK") alert ("Erreur : " + data);
                            else $("#form1").submit();
                            }
                     });
                   $( this ).dialog( "close" );
                    }
                    else {
                        $("#tab_titlebis").addClass( "ui-state-error" );
                        updateTips( "Un nom valide est requis." );
                    }
                },
                Annuler: function() {
                    $( this ).dialog( "close" );
                }
            },
            open: function() {
                 //recuperation des donnees
                var indexe = $( "li", $tabs ).index( $( this ).parent() );
                var numni =$("a:eq("+indexe+")").attr('tabindex');
                $.ajax({
                type: "POST",
                url : "get_fiche.php",
                data : { num_niv: numni , actionN : "lireniveau"  },
                async: false,
                success :function(data)
                    {
                    var name_nivo =$(data).filter('#nom_nivo').text();
                     var coor_nivo = $(data).filter('#coord_nivo').text();
                    $("#tab_titlebis").val(name_nivo );
                    $("#coordbis").attr("value",coor_nivo);
                    $("#niv_id").val(numni);
                    }
                });
            },
            close: function() {
                $form[ 0 ].reset();
            }
    });

 // Ouverture des form type dialog
//edition creneau
    $( "#add_tab" )
            .button()
            .click(function() {
                    $dialog.dialog( "open" );
            });


//suppression d'un niveau
    $( "#tabs span.ui-icon-close" ).live( "click", function() {
            var indexe = $( "li", $tabs ).index( $( this ).parent() );
            var numni =$("a:eq("+indexe+")").attr('tabindex');
            var nom=$("a:eq("+indexe+")").text();
           // $.post("get_fiche.php", { num_seq: numni , action : "test" },function(data){
           $.ajax({
            type: "POST",
            url : "get_fiche.php",
            data : { num_seq: numni , actionN : "test"  },
            async: false,
            success :function(data)
                {
                if (data== "NOK") alert ("Erreur : " + data);
                else if (data>1) alert ("Erreur : ce groupe de travail ne peut pas \352tre supprim\351 car "+ data + " fiches y sont associ\351es.");
                else if (data==1) alert ("Erreur : ce groupe de travail ne peut pas \352tre supprim\351 car  "+ data + " fiche y est associ\351e.");
                else if (data==0) {
                if (confirm("Confirmer la suppression du groupe :\n  " + nom ))
                    {
                    $.post("get_fiche.php", { num_niv:numni , actionN : "deleteniv" },function(data){
                    if (data != "OK") alert ("Erreur : " + data);
                    else
                            {$("#form1").submit();
                            //$tabs.tabs( "remove", indexe );
                            updatebdd();
                             }
                        });
                    }
                }
            }
         });
    });

        //edition niveau
         $( "#tabs span.ui-icon-pencil" ).live( "click", function() {
             //ouverture du form
             $dialogbis.dialog( "open" );
             //recuperation des donnees
            var indexe = $( "li", $tabs ).index( $( this ).parent() );
            var numni =$("a:eq("+indexe+")").attr('tabindex');
            $.ajax({
            type: "POST",
            url : "get_fiche.php",
            data : { num_niv: numni , actionN : "lireniveau"  },
            async: false,
            success :function(data)
                {
                var name_nivo =$(data).filter('#nom_nivo').text();
                 var coor_nivo = $(data).filter('#coord_nivo').text();
                $("#tab_titlebis").val(name_nivo );
                $("#coordbis").attr("value",coor_nivo);
                $("#niv_id").val(numni);
                }
            });
        });
    });

});
