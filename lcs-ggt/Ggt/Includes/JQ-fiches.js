$(document).ready(function() {


var tips = $( ".validateTips" ),
allFields = $( [] ).add( $("#datepicker1") ).add( $("#datepicker2")).add( $("#datepicker3") ).add( $("#datepicker4") ).add(tips);

function cmpDates(d1,d2){
    var tableau1=d1.split("/"),
           newdat1=tableau1[2]+tableau1[1]+tableau1[0],
           tableau2=d2.split("/"),
           newdat2=tableau2[2]+tableau2[1]+tableau2[0];
           if (newdat2>newdat1) return true;
           else return false ;
}

function updateTips( t ) {
    tips
            .text( t )
            .addClass( "ui-state-error" );
}

$.datepicker.setDefaults({
    closeText: 'Fermer',
            prevText: '&#x3c;Pr\u00E9c',
            nextText: 'Suiv&#x3e;',
            currentText: 'Courant',
            monthNames: ['Janvier','F\u00E9vrier','Mars','Avril','Mai','Juin','Juillet','Ao\u00FBt','Septembre','Octobre','Novembre','D\u00E9cembre'],
            monthNamesShort: ['Jan','F\u00E9v','Mar','Avr','Mai','Jun','Jul','Ao\u00FB','Sep','Oct','Nov','D\u00E9c'],
            dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
            dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
            dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showButtonPanel: true,
                                showOn: "button"

    });

$(function() {
        $( "#datepicker1" ).datepicker();
        $( "#datepicker2" ).datepicker();
        $( "#datepicker3" ).datepicker();
        $( "#datepicker4" ).datepicker();
    });

//modele fiche
 var modele='<p><span style="font-size: medium;"><span style="text-decoration: underline;"><span style="color: #333399;">Professeur</span></span> :</span></p>';
modele+='<p><span style="font-size: medium;">Dur&eacute;e :</span></p>';
modele+='<p><span style="font-size: medium;">Salle :</span></p>';
modele+='<p><span style="font-size: medium;">&Eacute;l&egrave;ves concern&eacute;s :</span></p>';
modele+='<p><span style="font-size: medium;"><br /></span></p>';
modele+='<p><span style="font-size: medium;"><span style="text-decoration: underline;"><span style="color: #008000;">Objectifs </span></span>:</span></p>';
modele+='<ul><li><span style="font-size: medium;">-</span></li>';
modele+='<li><span style="font-size: medium;">-</span></li>';
modele+='</ul><p>&nbsp;</p><p><span style="font-size: medium;"><span style="color: #0000ff;">Contenu</span> :</span></p>';
modele+='<ul><li><span style="font-size: medium;">-</span></li><li><span style="font-size: medium;">-</span></li><li><span style="font-size: medium;">-</span></li>';
modele+='</ul><p>&nbsp;</p><p><span style="font-size: medium;"><span style="color: #ff6600;">Contraintes mat&eacute;rielles</span> :</span></p>';
modele+='<p>&nbsp;</p><p><span style="font-size: medium;">&nbsp;</span></p>';



//suppression d'une fiche
$(function(){
    $("button.delet").click(function() {
        var lop=$(this).attr('tabindex');
        var num= $("button.delet").index(this)  ;
        var num2=$('li.ong').get().length;
        var id_sortable = $('li').eq(num+num2).parent()[0].id;
        $('#li'+lop).addClass("sel");
        var ttc=$('li').eq(num+num2).attr('title');
        //alert ('test : ' +ttc);
        if (confirm("Confirmer la suppression de la fiche :\n  " + ttc ))
            {
            $.post("get_fiche.php", { num_fich: lop , actionF : "delete" },function(data){
            if (data != "OK") alert ("Erreur : " + data);
            else
                {
                $('li').eq(num+num2).hide(300);
                $('li').eq(num+num2).remove(400);
                 }
            });
            }
        else $('li').eq(num+num2).removeClass("sel");
    });
});


//edition d'une fiche: recuperation des donnees depuis la bdd
$(function(){
            $("button.showform").click(function() {
            var num= $("button.showform").index(this)  ;
            var lop=$(this).attr('tabindex');
            $.post("get_fiche.php", { num_fich: lop , actionF : "lire" },function(data){
            var id_fi =$(data).filter('#id_fic').text();
            var n_fi = $(data).filter('#n_fic').text();
            var d_fi = $(data).filter('#d_fic').text();
            var is_fi = $(data).filter('#is_fic').text();
            $("#int_fich").val(n_fi );
            document.getElementById("is_prop").checked = eval(is_fi);
            tinyMCE.get('desc_fich').setContent(d_fi);
            $("#id_fich").val(lop);
            });
        });
});

// mise a jour du niveau dans les forms
$(function() {
        $("a").click(function() {
        var nums=$(this).attr("tabindex");
        $("#niv_fich").val(nums);
        $("#niv_fich1").val(nums);
        $("#niveau").val(nums);
        $.ajax({
            type: "POST",
            url : "get_fiche.php",
            data : {  num_niv: nums  , action : "iscoord"  },
            async: false,
            success :function(data)
                {
                if (data != "YES" && data != "NO") alert ("Erreur : " + data);
                else if (data == "YES")
                    {
                    $("#prop").removeClass("v_no");
                    $("#repa").removeClass("v_no");
                    }
                else if (data == "NO")
                    {
                    $("#prop").addClass("v_no");
                    $("#repa").addClass("v_no");
                    }
                }
            });
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

    //dialog creation fiche
    var $dialog1 = $( "#dialog1" ).dialog({
        autoOpen: false,
        modal: true,
        position: ['center','top'],
        width: 840,
        buttons: {
        Enregistrer: function() {
            var nfiche=$("#int_fich1").val();
            var isfiche=$("#is_prop1").is(':checked');
            var descr= tinyMCE.get('desc_fich1').getContent();
            var niv=$("#niv_fich1").val();
            if (nfiche != "")
                {
                $.ajax({
                type: "POST",
                url : "get_fiche.php",
                data : {  titre: nfiche, statut: isfiche, descript: descr, actionF : "save", level : niv},
                async: false,
                success :function(data){
                     //$.post("get_fiche.php", { num_fic: idfiche , titre: nfiche, statut: isfiche, descript: descr, action : "update" },function(data){
                    if (data != "OK") alert ("Erreur : " + data);
                    else
                        {
                        tinyMCE.get('desc_fich1').setProgressState(true);
                        setTimeout(function() {tinyMCE.get('desc_fich1').setProgressState(false);},300);
                         }
                    }
                });
                $("#form_seq1").submit();
                setTimeout(function() {$( this ).dialog( "close" );},400);
                }
        else
            $("#int_fich1").addClass( "ui-state-error" );
            updateTips( "Un intitulé valide est requis." );
        },
        Fermer: function() { $( this ).dialog( "close" ); } },

        open: function() {$("#int_fich1").focus(); },

        close: function() {
                //vide les champs quand on ferme le form
                $("#int_fich1").val( '');
                $("#is_prop1").val('');
                tinyMCE.get('desc_fich1').setContent('');
                $("#id_fich1").val('');
        }
    });

//modification fiche
var $dialog2 = $( "#dialog2" ).dialog({
        autoOpen: false,
        modal: true,
        position: ['center','top'],
        width: 900,
        buttons: {
        Enregistrer: function() {
            var idfiche=$("#id_fich").val();
            var nfiche=$("#int_fich").val();
            var isfiche=$("#is_prop").is(':checked')
            var descr= tinyMCE.get('desc_fich').getContent();
             if (nfiche != ""){
             $.ajax({
                type: "POST",
                url : "get_fiche.php",
                data : { num_fic: idfiche , titre: nfiche, statut: isfiche, descript: descr, actionF : "update"},
                async: false,
                 success :function(data){
                     //$.post("get_fiche.php", { num_fic: idfiche , titre: nfiche, statut: isfiche, descript: descr, action : "update" },function(data){
                    if (data != "OK") alert ("Erreur : " + data);
                    else {
                    tinyMCE.get('desc_fich').setProgressState(true);
                    setTimeout(function() {tinyMCE.get('desc_fich').setProgressState(false);},300);
                     }
                 }
             });
           //setTimeout(function() {$( this ).dialog( "close" );},400);
            }
        else
            $("#int_fich").addClass( "ui-state-error" );
            updateTips( "Un intitulé valide est requis." );
        },
        Fermer: function() {
            $("#form_seq").submit();
            $( this ).dialog( "close" );
             }
        },

        open: function() {
            $("#int_fich").focus();
        },
        close: function() {
            $("#int_fich").val( '');
            $("#is_prop").val('');
            tinyMCE.get('desc_fich').setContent('');
            $("#id_fich").val('');
        }
    });

    //dialog proposer
    var $dialog3 = $( "#dialog3" ).dialog({
            autoOpen: false,
            modal: true,
            position: ['center','top'],
            width: 900,
            buttons: {
                Aide: function() {
                  $dialog6.dialog( "open" );
                },
                Initialiser: function() {
                   var id_niveau=$("#niveau").val();
                    if (confirm("Confirmer la suppression :\n - des voeux existants \n - de la proposition d\'inscription existante \n pour ce groupe de travail  " ))
                    {
                   $.ajax({
                        type: "POST",
                        url : "get_fiche.php",
                        data : { var1: id_niveau , actionP : "init"},
                        async: false,
                        success :function(data){
                        if (data != "OK") alert ("Erreur : " + data);
                         }
                     });
                    $( this ).dialog( "close" );
                    }
                },
                Valider: function() {
                    allFields.removeClass( "ui-state-error" );
                    tips.text("");
                    var id_niveau=$("#niveau").val();
                    var checkbox_on = '';
                    var at_on="";
                    var dat1=$("#datepicker1").val();
                    var dat2=$("#datepicker2").val();
                    var dat3=$("#datepicker3").val();
                    var dat4=$("#datepicker4").val();
                   var tableau1=dat1.split("/");
                   var newdat1=tableau1[2]+tableau1[1]+tableau1[0];
                    $('input[name="cl_proposed"]:checked').each(function(i){
                    checkbox_on += (i>0 ? ',' : '')+$(this).attr('value');
                    });
                     $("#tab_title31").find("li").each(function(i) {
                     at_on += (i>0 ? ',' : '')+$(this).attr('liindex');
                    });
                     if (checkbox_on != "" && dat1 != "" && dat2 != ""&& dat3 != ""&& dat4 != "" && cmpDates(dat1,dat2) && cmpDates(dat3,dat4)){
                    $.ajax({
                            type: "POST",
                            url : "get_fiche.php",
                            data : { var1: id_niveau , var2:at_on, var3: dat1, var4: dat2, var6: dat3, var7: dat4,var5: checkbox_on, actionP : "save"},
                            async: false,
                             success :function(data){
                                 if (data != "OK") alert ("Erreur : " + data);

                             }
                         });
                    $( this ).dialog( "close" );
                     }
                    else {
                        var msg="";
                        if ( dat1==""){
                        $("#datepicker1").addClass( "ui-state-error" );
                        msg+="Date début inscription non-valide. " ;
                        }
                    if ( dat2==""){
                        $("#datepicker2").addClass( "ui-state-error" );
                        msg+= "Date fin inscription non-valide. " ;
                        }
                        if ( dat3==""){
                        $("#datepicker3").addClass( "ui-state-error" );
                        msg+="Date début déroulement non-valide. " ;
                        }
                        if ( dat4==""){
                        $("#datepicker4").addClass( "ui-state-error" );
                        msg+="Date fin déroulement non-valide. " ;
                        }
                         if ( ! cmpDates(dat1,dat2)){
                        $("#datepicker1").addClass( "ui-state-error" );
                        $("#datepicker2").addClass( "ui-state-error" );
                        msg+= "Incohérence dans les dates d\'inscription. " ;
                        }
                         if ( ! cmpDates(dat3,dat4)){
                        $("#datepicker3").addClass( "ui-state-error" );
                        $("#datepicker4").addClass( "ui-state-error" );
                        msg+= "Incohérence dans les dates de déroulement . " ;
                        }
                        if(checkbox_on==""){

                        $("#tab_title32").addClass( "ui-state-error" );
                        msg+= "Sélectionnez au moins une classe";
                        }
                        if (msg != "")  updateTips(msg);
                }
                },
                Annuler: function() {
                    $( this ).dialog( "close" );
                }
            },
            open: function() {
                //$tab_title32_input.focus();
                allFields.removeClass( "ui-state-error" );
                tips.text("");
                var id_niveau=$("#niveau").val();
                $("#datepicker1").val('');
                $("#datepicker2").val('');
                $("#datepicker3").val('');
                $("#datepicker4").val('');
                 $("#tab_title32").html('');
                $("#tab_title31").html('');
                $.ajax({
                type: "POST",
                url : "get_fiche.php",
                data : { num_niv: id_niveau , actionP : "lire"  },
                async: false,
                success :function(data)
                    {
                    var list_atelier =$(data).filter('#li_at').html();
                    if (list_atelier=='<ul class="connectedSortable ui-helper-reset  "></ul>') $("#tab_title31").html("<p> &nbsp;aucun ! !</p>");
                    else $("#tab_title31").html(list_atelier );
                    var list_classe=$(data).filter('#li_cl').html();
                    $("#tab_title32").html(list_classe);
                    var dbu=$(data).filter('#dbu').text();
                     if (dbu!="")$("#datepicker1").val(dbu);
                    var f1=$(data).filter('#fin').text();
                    if (f1!="") $("#datepicker2").val(f1);
                    var dbu_deroul=$(data).filter('#dbu_d').text();
                     if (dbu_deroul!="")$("#datepicker3").val(dbu_deroul);
                    var f1_deroul=$(data).filter('#fin_d').text();
                    if (f1_deroul!="") $("#datepicker4").val(f1_deroul);
                    }
                });

            },
            close: function() {
               $("#form_p").reset();
            }
    });

    //dialog repartition
    var $dialog4 = $( "#dialog4" ).dialog({
        autoOpen: false,
        modal: true,
        position: ['center','top'],
        width: 1000,
        height:600,
        buttons: {
        Aide : function() {
            $dialog5.dialog( "open" );
         },

        Réinitialiser:function(){
            $("#dialog4").html("" );
            var id_niveau=$("#niveau").val();
            $.ajax({
            type: "POST",
            url : "get_fiche.php",
            data : { num_niv: id_niveau , actionP : "init_liste"  },
            async: false,
            success :function(data)
                {
                var contenu=$(data).filter('#dbu').html();
                $("#dialog4").html(contenu );
                $( ".column" ).sortable({ connectWith: ".column"});
                $( ".portlet-content" ).sortable({ connectWith: ".portlet-content", items : "li" ,
                stop :function(event,ui) {
                var dep= $( this ).parent()[0].id
                var j=0;
                $("#"+dep).find("li").each(function() {
                    j++;
                    $( this ).find("span.rang").text(j);
                     });
                },
                 receive :function(event,ui) {
                var rec= $( this ).parent()[0].id;
                var i=0;
                $("#"+rec).find("li").each(function() {
                    i++;
                    $( this ).find("span.rang").text(i);
                     });
                 }
                });
                var num_icon=$('span.ui-icon-minusthick').get().length + $('span.ui-icon-plusthick').get().length;
                if (num_icon==0){
                    $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
                    .find( ".portlet-header" )
                    .addClass( "ui-widget-header ui-corner-all" )
                    .prepend( "<span class='ui-icon ui-icon-minusthick'></span>")
                    .end()
                    .find( ".portlet-content" );
                    }
                $( ".portlet-header .ui-icon" ).click(function() {
                    $( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
                    $( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
                    });
                $( ".column" ).disableSelection();
               }
        });
            },
        Enregistrer: function() {
            var contenu=$( "#dialog4" ).html();
            var id_niveau=$("#niveau").val();
             $.ajax({
            type: "POST",
            url : "get_fiche.php",
            data : {  num_niv: id_niveau ,cont: contenu , actionP : "saveliste"  },
            async: false,
            success :function(data)
                {
                if (data != "OK") alert ("Erreur : " + data);
                else alert('Enregistrement effectu\351.');
                }
            });
        //$( this ).dialog( "close" );
        },
        Diffuser: function() {
            var loop=0;
            $("div.portlet").each(function() {
               var iddiv=$( this ).attr('id');
               $("#"+iddiv).find("li").each(function() {
                   var sonlog= $( this ).find("span.log").attr('id');
                    var id_niveau=$("#niveau").val();
                 $.ajax({
                    type: "POST",
                    url : "get_fiche.php",
                    data : { var_niv: id_niveau , var_log:sonlog, var_at: iddiv, actionP : "export"},
                    async: false,
                     success :function(data){
                         if (data != "OK") alert ("Erreur : " + data);
                         else loop++;
                     }
                 });
               });
            });
            alert(loop + " inscriptions ont \351t\351 diffus\351es");
            },

            Imprimer:function(){
                 var id_niveau=$("#niveau").val();
                window.open("imprim.php?niveau="+id_niveau+"","","width=950,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,status=no");
              },

            Annuler: function() {
                $( this ).dialog( "close" );
                }
          },

        open: function() {
           var id_niveau=$("#niveau").val();
            $.ajax({
            type: "POST",
            url : "get_fiche.php",
            data : { num_niv: id_niveau , actionP : "liste"  },
            async: false,
            success :function(data)
                {
                var contenu=$(data).filter('#dbu').html();
                $("#dialog4").html(contenu );
                $( ".column" ).sortable({ connectWith: ".column"});
                $( ".portlet-content" ).sortable({ connectWith: ".portlet-content", items : "li" ,
                stop :function(event,ui) {
                var dep= $( this ).parent()[0].id
                var j=0;
                $("#"+dep).find("li").each(function() {
                    j++;
                    $( this ).find("span.rang").text(j);
                    });
                },
                receive :function(event,ui) {
                var rec= $( this ).parent()[0].id;
                var i=0;
                $("#"+rec).find("li").each(function() {
                    i++;
                    $( this ).find("span.rang").text(i);
                     });
                 }
                });
                var num_icon=$('span.ui-icon-minusthick').get().length + $('span.ui-icon-plusthick').get().length;
                if (num_icon==0){
                    $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
                    .find( ".portlet-header" )
                    .addClass( "ui-widget-header ui-corner-all" )
                    .prepend( "<span class='ui-icon ui-icon-minusthick'></span>")
                    .end()
                    .find( ".portlet-content" );
                    }
                $( ".portlet-header .ui-icon" ).click(function() {
                    $( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
                    $( this ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
                    });
                $( ".column" ).disableSelection();
               }
            });
        },
        close: function() {

        }
    });

    //Aide repartiton
    var $dialog5 = $( "#dialog5" ).dialog({
            autoOpen: false,
            modal: false,
            position: ['center','center'],
            width: 700,
            show: "puff",
            //hide: "explode",
            buttons: {
                Fermer: function() {
                    $( this ).dialog( "close" );
                }
            }
    });

 //Aide proposer
 var $dialog6 = $( "#dialog6" ).dialog({
                    autoOpen: false,
                    modal: false,
                    position: ['center','center'],
                    width: 700,
                    show: "puff",
                    //hide: "explode",
                    buttons: {
                        Fermer: function() {
                            $( this ).dialog( "close" );
                        }
                    }
 });

//Aide prof
 var $dialog7= $( "#dialog7" ).dialog({
                    autoOpen: false,
                    modal: true,
                    position: ['center','center'],
                    width: 700,
                    show: "puff",
                    //hide: "explode",
                    buttons: {
                        Fermer: function() {
                            $( this ).dialog( "close" );
                        }
                    }
 });
 // Ouverture des form type dialog

//creation d'une fiche
$( "#showr" )
        .button()
        .click(function() {
            $dialog1.dialog( "open" );
            tinyMCE.get('desc_fich1').setContent(modele);
            });
//proposition des ateliers
$( "#prop" )
        .button()
        .click(function() {
            $dialog3.dialog( "open" );
             });
//repartion des eleves dans les ateliers
$( "#repa" )
        .button()
        .click(function() {
            $dialog4.dialog( "open" );
            });

//edition d'une fiche
$( "button.showform" )
        .click(function() {
            $dialog2.dialog( "open" );
          });
//Aide
$( "#helpp" )
        .button()
        .click(function() {
            $dialog7.dialog( "open" );
            });
    });
});
