$(document).ready(function() {

 $(function() {
     var nombre_rubrique = $("#sortable2 li").length;
            if (nombre_rubrique ==3) {
                $( "#draggable1" ).sortable( "option", "containment", 'parent' );
                $( "#draggable1 li" ).addClass("ui-state-disabled");
            }
 });


 $(function() {
    $( "#draggable1" ).sortable({
        connectWith: ".connectedSortable",
        items: "li:not(.ui-state-disabled)",
        stop: function() {
            var nombre_rubrique = $("#sortable2 li").length;
            if (nombre_rubrique ==3) {
                $( "#draggable1" ).sortable( "option", "containment", 'parent' );
                $( "#draggable1 li" ).addClass("ui-state-disabled");
                var pos=0;
                var num_niv=$("#niv_ins").val();
                var num = new Array();
                $("#sortable2 ").find("li").each(function() {
                num[pos]=$(this).find("button.showform").attr('tabindex');
                pos++;

                });

                $.ajax({
                                type: "POST",
                                url : "get_fiche.php",
                                data : { voeu1:num[0] ,voeu2:num[1] ,voeu3:num[2] , var1:num_niv, actionE : "save" },
                                async: false,
                                success :function(data)
                                 {
                                if (data != "OK") alert ("Erreur : " + data);
                                else alert ("Votre demande a \351t\351 enregistr\351e");
                                }
                        });
            }
            //alert (nombre_rubrique);
        }
        });
    $( "#sortable2" ).sortable({
        connectWith: ".connectedSortable",
        stop: function() {
            var nombre_rubrique = $("#sortable2 li").length;
            if (nombre_rubrique <3)
            {
                $( "#draggable1" ).sortable(  "option", "containment",'document' );
                $( "#draggable1 li" ).removeClass("ui-state-disabled");
            }
            if (nombre_rubrique ==3) {
                var pos=0;
                var num = new Array();
                var num_niv=$("#niv_ins").val();
                $("#sortable2 ").find("li").each(function() {
                num[pos]=$(this).find("button.showform").attr('tabindex');
                pos++;
                });
                $.ajax({
                                type: "POST",
                                url : "get_fiche.php",
                                data : { voeu1:num[0] ,voeu2:num[1] ,voeu3:num[2] , var1:num_niv, actionE : "save" },
                                async: false,
                                success :function(data)
                                 {
                                if (data != "OK") alert ("Erreur : " + data);
                                else alert ("Votre inscription a \351t\351 mise \340 jour");
                                }
                        });
            }
        }
        });
    $( "draggable1, #sortable2" ).disableSelection();

});

var $dialog2 = $( "#dialog2" ).dialog({
                    autoOpen: false,
                    modal: true,
                    position: ['center','top'],
                    width: 900,
                    height:500,
                    close: function() {
                        $( this ).dialog( "close" );
                    }
            });
  var $dialog = $( "#dialog" ).dialog({
                    autoOpen: false,
                    modal: true,
                    position: ['center','top'],
                    width:500,
                    close: function() {
                        $( this ).dialog( "close" );
                    }
            });

 $( "#aide" )
                    .click(function() {
                            $dialog.dialog( "open" );
                    });


$(function(){
                $("button.showform").click(function() {
                            $dialog2.dialog( "open" );
                            var lop=$(this).attr('tabindex');
                            $.ajax({
                                type: "POST",
                                url : "get_fiche.php",
                                data : { num_fich: lop , actionF : "lire2" },
                                async: false,
                                success :function(data)
                                 {
                                var d_fi = $(data).filter('#d_fic').text();
                                var n_fi = $(data).filter('#n_fic').text();
                                //alert("=" + n_fi);
                                $("#descript").html(d_fi);
                                $("#ui-dialog-title-dialog2").text(n_fi);
                                }
                        });

                    });
});

});
