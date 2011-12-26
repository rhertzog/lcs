$(function() {
    $( "#dialog" ).dialog({
        autoOpen: false,
        show: "blind",
        hide: "explode",
        position: ['center','top'],
        width: 500,
        resizable: true
    });

    $( "#aide" ).click(function() {
        $( "#dialog" ).dialog( "open" );
        return false;
        });
    });

//insertion numero onglet dans le form
    $(function() {
        $("a").click(function() {
        nums=$(this).attr("tabindex");
        $("#numong").val(nums);
        });
    });

//copie ou deplacement d'un item
$(function() {
    var $tabs = $("#tabs").tabs();
    var $tab_items = $("ul:first li",$tabs).droppable({
            accept: ".connectedSortable li",
            hoverClass: "ui-state-hover",
            drop: function(ev, ui) {
            var $item = $(this);
            var $list = $($item.find('a').attr('href')).find('.connectedSortable');
            //recherche du numero de l'onglet de destination
            var num_dest = $item.find('a').attr('tabindex') ;
            //recherche du numero de l'onglet source
            var ref_tab=(ui.draggable.parents()[1].id);
            var num_source = $('a[href$="#'+ ref_tab +'"]').attr('tabindex');//la ligne de DOM0 :)
            //objet expediteur
            var $clo =  $(ui.sender);
            // id de la sequence dans la bdd
            var seq_id=$(ui.draggable.get(0)).attr('id').substring(2);
            //deplacement de la sequence si pas appui sur shift

             if (! ev.shiftKey) {
                //test si des seances sont assosciees a la sequence deplacee
                $.post("get_seq.php", { num_seq: seq_id , action : "test" },function(data){
                    if (data== "NOK") alert ("Erreur : " + data);
                    else if (data>1) alert ("Erreur : cette s\351quence ne peut pas \352tre d\351plac\351e car "+ data + " s\351ances y sont associ\351es.");
                    else if (data==1) alert ("Erreur : cette s\351quence ne peut pas \352tre d\351plac\351e car "+ data + " s\351ance y est associ\351e.");
                    else if (data==0) {
                        //mise a jour de la bdd : ajout dans onglet dest et supp dans onglet source
                        $.post("get_seq.php", { num_seq: seq_id , ong_dest : num_dest, action : "deplace" },function(data){
                            if (data!= "OK") alert ("Erreur : " + data);
                            else 
                                {
                                //maj du html
                                //list3=id du 'ul' parent de la sequence deplacee(sortable x)
                                var $list3 =ui.draggable.parent()[0].id ;
                                ui.draggable.hide('slow', function() {
                                    $tabs.tabs('select', $tab_items.index($item));
                                    $(this).prependTo($list).show('slow');
                                    reorganise('sortable'+$tab_items.index($item));
                                    reorganise($list3);
                                    });
                                }
                            });
                        }
                    });     
                }
                //si  shift -> copie de la sequence
                else 
                    {
                    //maj de la bdd (ajout dans onglet dest)
                    $.post("get_seq.php", { num_seq: seq_id , ong_dest : num_dest, action : "ajoute" },function(data){
                        if (data != "OK") alert ("Erreur : " + data);
                        else 
                            {
                            ui.draggable.hide(1, function() {
                            $tabs.tabs('select', $tab_items.index($item));
                            ui.draggable.prependTo($list).show('300');
                            });
                            $("#numong").val(num_dest);
                            setTimeout(function() {$('#form_seq').submit();},550);
                            }
                        });
                    }
                }
            });
    });

//Déplacement d'un item 
    $(function() {
        $( ".seq" ).sortable({
            items: 'li',
            handle: 'span.handdle',
            //helper: 'clone',
            stop :function(event,ui) {
            var ong=$(ui.item).parent()[0].id ;
            if (! event.shiftKey) reorganise(ong);
            }
        });
        $( ".seq" ).disableSelection();
    });

//creation sequence
    $(function(){
            $("#showr").click(function() {
            $("#showr").addClass("v_no");
            $("button.showform").addClass("v_no");
            $("#aide").addClass("v_no");
            $("#update").removeClass("v_yes").addClass("v_no");
            $("#masquable").slideToggle("slow");
            });
    });

//Fermeture
    $(function(){
            $("#hider").click(function() {
            $("#masquable").slideToggle(550);
            $("#showr").removeClass("v_no")
            $("button.showform").removeClass("v_no")
            $("#update").removeClass("v_no")
            $("#record").removeClass("v_no")
            $("#aide").removeClass("v_no");
            $("#tc").val("");
            $("#tl").val("" );
            tinyMCE.get('contenu_sequence').setContent("");
            if ($("#closeandsubmit").val() == "yes") {
                    setTimeout(function() {$('#form_seq').submit();},550);
                    }
            });
    });

//edition d'une sequence
    $(function(){
            $("button.showform").click(function() {
                    $("#masquable").slideToggle("slow");
                    $("button.showform").addClass("v_no");
                    $("#showr").addClass("v_no");
                    $("#record").addClass("v_no");
                    $("#aide").addClass("v_no");
                    var num= $("button.showform").index(this)  ;
                    var lop=$(this).attr('tabindex');
                    $.post("get_seq.php", { num_seq: lop , action : "lire" },function(data){
                    var $resp = $(data);
                    var smalltitre =$(data).filter('#sht').text();
                    var longtitre = $(data).filter('#lgt').text();
                    var description = $(data).filter('#dn').text();
                    $("#tc").val( smalltitre );
                    $("#tl").val( longtitre );
                    tinyMCE.get('contenu_sequence').setContent( description);
                    $("#numseq").val(lop);
                    });
            });
    });

//enregistrement initial d'une sequence
    $(function(){
            $("#record").click(function() {
                    var idong=$("#numong").val();
                    var shorti=$("#tc").val();
                    var longi=$("#tl").val();
                    var descr= tinyMCE.get('contenu_sequence').getContent();
                            $.post("get_seq.php", { num_rub: idong , titre1: shorti, titre2: longi, descript: descr, action : "save" },function(data){
                            if (data != "OK") alert ("retour" + data);
                            else {
                            tinyMCE.get('contenu_sequence').setProgressState(true);
                            setTimeout(function() {tinyMCE.get('contenu_sequence').setProgressState(false);},400);
                            $("#record").addClass("v_no");
                            $("#update").removeClass("v_no");
                            $("#closeandsubmit").val("yes");
                            }
                    });
            });
    });

//enregistrement des modifications d'une sequence
    $(function(){
            $("#update").click(function() {
                    var idong=$("#numseq").val();
                    var shorti=$("#tc").val();
                    var longi=$("#tl").val();
                    var descr= tinyMCE.get('contenu_sequence').getContent();
                            $.post("get_seq.php", { num_rub: idong , titre1: shorti, titre2: longi, descript: descr, action : "update" },function(data){
                            if (data != "OK") alert ("Erreur : " + data);
                            else {
                            tinyMCE.get('contenu_sequence').setProgressState(true);
                            $("#closeandsubmit").val("yes");
                            setTimeout(function() {tinyMCE.get('contenu_sequence').setProgressState(false);},300);
                            }
                    });
            });
    });

//suppression d'une sequence
    $(function(){
            $("button.delet").click(function() {
                    var lop=$(this).attr('tabindex');
                    var num= $("button.delet").index(this)  ;
                    var num2=$('li.ong').get().length;
                    var id_sortable = $('li').eq(num+num2).parent()[0].id;
                    $('li').eq(num+num2).addClass("sel");
                    var ttc=$('li').eq(num+num2).attr('title');
                    //alert ('test : ' +ttc);
                    if (confirm("Confirmer la suppression de la s\351quence :\n  " + ttc ))
                            {
                            $.post("get_seq.php", { num_seq: lop , action : "delete" },function(data){
                            if (data != "OK") alert ("Erreur : " + data);
                            else
                                    {
                                    $('li').eq(num+num2).hide(300);
                                    $('li').eq(num+num2).remove();
                                    setTimeout(function() {reorganise(id_sortable);},350);
                                    }
                            });
                    }
                    else $('li').eq(num+num2).removeClass("sel");
            });
    });

//mise a jour de l'ordre d'affichage
function reorganise(n){
        var pos=0;
        $("#" + n +".seq").find("li.sequ").each(function() {
                pos++;
                $(this).find(".order").text(pos);
                var num=$(this).find("button.showform").attr('tabindex');
                $.post("get_seq.php", { num_seq: num , posission: pos, action : "up_ordre" },function(data){
                if (data != "OK") alert ("Erreur : " + data);	});
                });
}