$(document).ready(function() {

var calendar=$('#calendar');
var dialog=$( "#dialog" ).dialog({autoOpen: false}),
    dialog2=$( "#dialog2" ).dialog({autoOpen: false});

calendar.fullCalendar({
    height: 500,
    theme: true,
    editable:true,
    weekMode:'variable',
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
        },
    allDaySlot: false,
    allDayText: 'jour entier',
    firstHour: 6,
    slotMinutes: 30,
    defaultEventMinutes: 60,
    axisFormat: 'HH:mm',
    defaultView: 'agendaWeek',
    aspectRatio: 1.5,
    weekends: true,
    allDayDefault: true,
    ignoreTimezone: true,
    // event ajax
    lazyFetching: true,
    startParam: 'start',
    endParam: 'end',

    // time formats
    titleFormat: {
            month: 'MMMM yyyy',
            week: "d[ MMMM][ yyyy]{ '&#8212;' d MMMM  yyyy}",
            day: 'dddd d MMMM  yyyy'
        },
    columnFormat: {
            month: 'ddd',
            week: 'ddd d/M',
            day: 'dddd d/M'
        },
     // locale
    isRTL: false,
    firstDay: 1,
    monthNames: ['Janvier','F\u00E9vrier','Mars','Avril','Mai','Juin','Juillet','Ao\u00FBt','Septembre','Octobre','Novembre','D\u00E9cembre'],
                monthNamesShort: ['Jan','F\u00E9v','Mar','Avr','Mai','Jun','Jul','Ao\u00FB','Sep','Oct','Nov','D\u00E9c'],
                dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
                dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
                dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
                dateFormat: 'dd/mm/yy',
    buttonText: {
            prev: '&nbsp;&#9668;&nbsp;',
            next: '&nbsp;&#9658;&nbsp;',
            prevYear: '&nbsp;&lt;&lt;&nbsp;',
            nextYear: '&nbsp;&gt;&gt;&nbsp;',
            today: 'aujourd\'hui',
            month: 'mois',
            week: 'semaine',
            day: 'jour'
    },

    unselectAuto: true,
    dropAccept: '*',
    timeFormat: {
        agenda: 'HH:mm{ - HH:mm}',
                        '': 'HH(:mm)'
        },

    dragOpacity: {
        agenda: .5
        },
    minTime: 8,
    maxTime: 18,
    events: '../scripts/myfeed.php',
     // jquery-ui theming
    buttonIcons: {
        prev: 'circle-triangle-w',
        next: 'circle-triangle-e'
        },

    selectable: true,
    selectHelper: true,

    //clic sur creneau vide
    select: function(start, end) {
    var $dialog = $( "#dialog" ).dialog({
    autoOpen: false,
    modal:true,
    width:500,
    buttons: {
        Enregistrer: function() {
               ($("#new_classe").val()!="") ? (groupe=$("#new_classe").val()) : (groupe=$("#tab_title").val());
               ($("#new_mat").val()!="") ? (matiR=$("#new_mat").val()) : (matiR=$("#tab_descr").val());
               if ($("#new_classe").val()!="")
                    {
                    $('#tab_title').append('<option value='+$("#new_classe").val()+' >'+$("#new_classe").val()+'</option>');
                    $('#tab_title2').append('<option value='+$("#new_classe").val()+' >'+$("#new_classe").val()+'</option>');
                    }
               if ($("#new_mat").val()!="")
                    {
                    $('#tab_descr').append('<option value='+$("#new_mat").val()+' >'+$("#new_mat").val()+'</option>');
                    $('#tab_descr2').append('<option value='+$("#new_mat").val()+' >'+$("#new_mat").val()+'</option>');
                    }
               var
                hdb=$("#timepicker_start").val().split(':'),
                hdf=$("#timepicker_end").val().split(':'),
                date_deb_modif=new Date(start),
                date_fin_modif=new Date(end);
                date_deb_modif.setHours(hdb[0]);
                date_deb_modif.setMinutes(hdb[1]);
                date_fin_modif.setHours(hdf[0]);
                date_fin_modif.setMinutes(hdf[1]);
            var d = date_deb_modif.getTime()/1000,
                f = date_fin_modif.getTime()/1000;
             var request=$.ajax({
                    type: "POST",
                    url : "updatejson.php",
                    data : {check_id : 'new' },
                    async: false,
                    success :function(data)
                        {
                        if (data != "") return(data);
                        }
                    });
                request.done(function(msg) {  the_url=  'cahier_texte_prof.php?id='+msg+'&start='+d;});

                //var the_url=  'cahier_texte_prof.php?id='+request+'&start='+d;
                if (groupe)
                    {
                    calendar.fullCalendar('renderEvent',
                        {
                        title: groupe,
                        start :  date_deb_modif,
                        end : date_fin_modif,
                        url : the_url,
                        allDay : false
                        },
                        false // make the event "stick"
                    );
                    $.ajax({
                    type: "POST",
                    url : "updatejson.php",
                    data : {title : groupe , ereitam: matiR,trats: d,dne: f,dayall: false,lru: the_url, action : "new_event" },
                    async: false,
                    success :function(data)
                        {
                        if (data != "OK") alert('Erreur' +data);
                        calendar.fullCalendar( 'refetchEvents' );
                        }
                    });
                    //

                $("#new_classe").val( '');
                $("#new_mat").val('');
                $( this ).dialog( "close" );
                }
            }
        },
        close: function() {
            $("#new_classe").val( '');
            $("#new_mat").val('');
            }
    });
    //assigne les dates du dialog avec le creneau clicke
    var madate = new Date(start),
            madate2 = new Date(end),
            heur_deb=madate.getHours();
            (madate.getMinutes()<10)? (minu_deb='0'+madate.getMinutes()) : (minu_deb=madate.getMinutes());
    var debut=heur_deb +":"+minu_deb,
          heur_fin=madate2.getHours();
          (madate2.getMinutes()<10)? (minu_fin='0'+madate2.getMinutes()) : (minu_fin=madate2.getMinutes());
    var fin=heur_fin +":"+minu_fin;
    $("#timepicker_start").val(debut);
    $("#timepicker_end").val(fin);
    function tpEndOnHourShowCallback(hour) {
            var tpStartHour = $('#timepicker_start').timepicker('getHour');
            // Check if proposed hour is after or equal to selected start time hour
            if (hour >= tpStartHour) { return true; }
            // if hour did not match, it can not be selected
            return false;
        }
    function tpEndOnMinuteShowCallback(hour, minute) {
        var tpStartHour = $('#timepicker_start').timepicker('getHour');
        var tpStartMinute = $('#timepicker_start').timepicker('getMinute');
        // Check if proposed hour is after selected start time hour
        if (hour > tpStartHour) { return true; }
        // Check if proposed hour is equal to selected start time hour and minutes is after
        if ( (hour == tpStartHour) && (minute > tpStartMinute) ) { return true; }
        // if minute did not match, it can not be selected
        return false;
        }
    function tpStartOnHourShowCallback(hour) {
        var tpEndHour = $('#timepicker_end').timepicker('getHour');
        // Check if proposed hour is prior or equal to selected end time hour
        if (hour <= tpEndHour) { return true; }
        // if hour did not match, it can not be selected
        return false;
        }
    function tpStartOnMinuteShowCallback(hour, minute) {
        var tpEndHour = $('#timepicker_end').timepicker('getHour');
        var tpEndMinute = $('#timepicker_end').timepicker('getMinute');
        // Check if proposed hour is prior to selected end time hour
        if (hour < tpEndHour) { return true; }
        // Check if proposed hour is equal to selected end time hour and minutes is prior
        if ( (hour == tpEndHour) && (minute < tpEndMinute) ) { return true; }
        // if minute did not match, it can not be selected
        return false;
        }
    $('#timepicker_start').timepicker({
            showLeadingZero: false,
            showPeriodLabels: false,
            hourText: 'Heure',
            minuteText: 'Minute',
            hours: {starts: 7,ends: 18},
            showCloseButton: true,
            closeButtonText: 'OK',
            onHourShow: tpStartOnHourShowCallback,
            onMinuteShow: tpStartOnMinuteShowCallback
            });
    $('#timepicker_end').timepicker({
            showLeadingZero: false,
            showPeriodLabels: false,
            hourText: 'Heure',
            minuteText: 'Minute',
            hours: {starts: 7,ends: 18},
            showCloseButton: true,
            closeButtonText: 'OK',
            onHourShow: tpEndOnHourShowCallback,
            onMinuteShow: tpEndOnMinuteShowCallback
             });
    $dialog.dialog( "open" );
    calendar.fullCalendar('unselect');
    },

eventClick: function(event,jsEvent,view)
        {
        $("span.ui-icon-close").hide();
        $("span.ui-icon-pencil").hide();
        //reaffiche les boutons si clic sur entete ou un bouton
        if (jsEvent.target.attributes[0].value=="fc-event-time" || (jsEvent.target.childNodes.length>0 &&( jsEvent.target.childNodes[0].nodeValue=="supprimer" ||
        jsEvent.target.childNodes[0].nodeValue=="modifier" )))
            {
            jsEvent.preventDefault();//desactive le lien
            $(this) .find(".ui-icon-close").show("fast");
            $(this) .find(".ui-icon-pencil").show("fast");
            }
        if (jsEvent.target.childNodes.length>0) // un des 2 boutons
            {
/*---- suppression cours ----*/

             if (jsEvent.target.childNodes[0].nodeValue=="supprimer")
                {
                 if (confirm("Confirmer la suppression du cours :\n " +event.title+" \n" +event.matiere ))
                    {
                        if (event.id !="")
                            {
                                if (calendar.fullCalendar( 'removeEvents',event.id ))
                                    {
                                    $.ajax({
                                    type: "POST",
                                    url : "updatejson.php",
                                    data : {di : event.id ,  action : "del_event" },
                                    async: false,
                                    success :function(data)
                                        {
                                        if (data != "OK") alert('Erreur' +data);
                                        calendar.fullCalendar( 'refetchEvents' );
                                        }
                                    });
                                }
                            }
                        }
                }

/*-----modif cours ----*/
            else if (jsEvent.target.childNodes[0].nodeValue=="modifier")
                {
                var madate = new Date(event.start),
                      madate2 = new Date(event.end),
                      heur_deb=madate.getHours();
                      (madate.getMinutes()<10)? (minu_deb='0'+madate.getMinutes()) : (minu_deb=madate.getMinutes());
                var debut=heur_deb +":"+minu_deb,
                      heur_fin=madate2.getHours();
                      (madate2.getMinutes()<10)? (minu_fin='0'+madate2.getMinutes()) : (minu_fin=madate2.getMinutes());
                var fin=heur_fin +":"+minu_fin;

                $( "#dialog2" ).dialog({
                autoOpen: false,
                modal:true,
                width:500,
                buttons: {
                    Enregistrer: function() {
                        ($("#new_classe2").val()!="") ? (groupe=$("#new_classe2").val()) : (groupe=$("#tab_title2").val());
                        ($("#new_mat2").val()!="") ? (matiR=$("#new_mat2").val()) : (matiR=$("#tab_descr2").val());
                         if ($("#new_classe2").val()!="")
                            {
                            $('#tab_title').append('<option value='+$("#new_classe2").val()+' >'+$("#new_classe2").val()+'</option>');
                            $('#tab_title2').append('<option value='+$("#new_classe2").val()+' >'+$("#new_classe2").val()+'</option>');
                            }
                        if ($("#new_mat2").val()!="")
                            {
                            $('#tab_descr').append('<option value='+$("#new_mat2").val()+' >'+$("#new_mat2").val()+'</option>');
                            $('#tab_descr2').append('<option value='+$("#new_mat2").val()+' >'+$("#new_mat2").val()+'</option>');
                            }
                        var di=event.id,
                            hdb=$("#timepicker_start2").val().split(':'),
                            hdf=$("#timepicker_end2").val().split(':'),
                            date_deb_modif=new Date(event.start),
                            date_fin_modif=new Date(event.end);
                            date_deb_modif.setHours(hdb[0]);
                            date_deb_modif.setMinutes(hdb[1]);
                            date_fin_modif.setHours(hdf[0]);
                            date_fin_modif.setMinutes(hdf[1]);
                        var d = date_deb_modif.getTime()/1000,
                            f = date_fin_modif.getTime()/1000,
                             the_url= 'cahier_texte_prof.php?id='+event.id+'&start='+d;
                            if (groupe) {
                                $.ajax({
                                type: "POST",
                                url : "updatejson.php",
                                data : {di:event.id ,title : groupe , ereitam: matiR,trats: d,dne: f,dayall: false,lru: the_url, action : "modif_event" },
                                async: false,
                                success :function(data)
                                    {
                                    if (data != "OK") alert('Erreur' +data);
                                    }
                                });
                                //
                            calendar.fullCalendar( 'refetchEvents' );
                            $("#new_classe2").val( '');
                            $("#new_mat2").val('');
                            $( this ).dialog( "close" );
                            }
                        }
                    },
                    close: function() {
                        $("#new_classe2").val( '');
                        $("#new_mat2").val('');
                        }
                });

                function tpEndOnHourShowCallback2(hour) {
                        var tpStartHour = $('#timepicker_start2').timepicker('getHour');
                        // Check if proposed hour is after or equal to selected start time hour
                        if (hour >= tpStartHour) { return true; }
                        // if hour did not match, it can not be selected
                        return false;
                    }
                function tpEndOnMinuteShowCallback2(hour, minute) {
                    var tpStartHour = $('#timepicker_start2').timepicker('getHour');
                    var tpStartMinute = $('#timepicker_start2').timepicker('getMinute');
                    // Check if proposed hour is after selected start time hour
                    if (hour > tpStartHour) { return true; }
                    // Check if proposed hour is equal to selected start time hour and minutes is after
                    if ( (hour == tpStartHour) && (minute > tpStartMinute) ) { return true; }
                    // if minute did not match, it can not be selected
                    return false;
                    }
                function tpStartOnHourShowCallback2(hour) {
                    var tpEndHour = $('#timepicker_end2').timepicker('getHour');
                    // Check if proposed hour is prior or equal to selected end time hour
                    if (hour <= tpEndHour) { return true; }
                    // if hour did not match, it can not be selected
                    return false;
                    }
                function tpStartOnMinuteShowCallback2(hour, minute) {
                    var tpEndHour = $('#timepicker_end2').timepicker('getHour');
                    var tpEndMinute = $('#timepicker_end').timepicker('getMinute');
                    // Check if proposed hour is prior to selected end time hour
                    if (hour < tpEndHour) { return true; }
                    // Check if proposed hour is equal to selected end time hour and minutes is prior
                    if ( (hour == tpEndHour) && (minute < tpEndMinute) ) { return true; }
                    // if minute did not match, it can not be selected
                    return false;
                    }
                $("#timepicker_start2").val(debut);
                $("#timepicker_end2").val(fin);
                $('#timepicker_start2').timepicker({
                            showLeadingZero: false,
                            showPeriodLabels: false,
                            hourText: 'Heure',
                            minuteText: 'Minute',
                            hours: {starts: 7,ends: 18},
                            showCloseButton: true,
                            closeButtonText: 'OK',
                            onHourShow: tpStartOnHourShowCallback2,
                            onMinuteShow: tpStartOnMinuteShowCallback2
                        });
                $('#timepicker_end2').timepicker({
                        showLeadingZero: false,
                        showPeriodLabels: false,
                        hourText: 'Heure',
                        minuteText: 'Minute',
                        hours: {starts: 7,ends: 18},
                        showCloseButton: true,
                        closeButtonText: 'OK',
                        onHourShow: tpEndOnHourShowCallback2,
                        onMinuteShow: tpEndOnMinuteShowCallback2
                    });
                dialog2.dialog( "open" );
                $("#tab_title2").attr("value",event.title);
                $("#tab_descr2").attr("value",event.matiere);
            }
        }
        else if (event.url) {
            window.location(url);
            return false;}
    },

/*----- fin modif cours ----*/

    eventMouseover: function(event) {
         },
     eventMouseout: function(event) {
     },
     eventRender:function(event,element) {
         element.find('div.fc-event-content').attr("title",event.matiere);
         $('div.fc-event-time').attr("title","Modifier Supprimer");
     },
    eventResize: function(event) {
        var date_deb=new Date(event.start),
                date_fin=new Date(event.end),
                d = date_deb.getTime()/1000,
                f = date_fin.getTime()/1000,
                urlmod='cahier_texte_prof.php?id='+event.id+'&start='+d;
        $.ajax({
                    type: "POST",
                    url : "updatejson.php",
                    data : {di:event.id ,trats: d,dne:f, lru:urlmod, action : "update_event" },
                    async: false,
                    success :function(data)
                        {
                        if (data != "OK") alert('Erreur' +data);
                        }
                    });
            calendar.fullCalendar( 'refetchEvents' );
            },

        eventDrop: function(event) {
        var date_deb=new Date(event.start),
                date_fin=new Date(event.end),
                d = date_deb.getTime()/1000,
                f = date_fin.getTime()/1000,
                urlmod='cahier_texte_prof.php?id='+event.id+'&start='+d;
        $.ajax({
                    type: "POST",
                    url : "updatejson.php",
                    data : {di:event.id ,trats: d,dne:f, lru:urlmod,action : "update_event" },
                    async: false,
                    success :function(data)
                        {
                        if (data != "OK") alert('Erreur' +data);
                        }
                    });
            calendar.fullCalendar( 'refetchEvents' );
            },

        loading : function() {
           $("div.ui-resizable-handle").after("<span class='ui-icon ui-icon-pencil' title='Modifier ce cours'>modifier</span>");
           $("div.ui-resizable-handle").after("<span class='ui-icon ui-icon-close' title='Supprimer ce cours'>supprimer</span>");
           $("span.ui-icon-close").hide();
           $("span.ui-icon-pencil").hide();
           $("div.fc-event-time,div.fc-event-content, span.ui-icon").tooltip({
           track: true,
           show: {
                effect: "slideDown"            }
             });

             if ($('a.fc-event').get().length==0)
                 {
                  $('#mess').html('<a href="cahier_texte_prof.php" class="message">Acc\350s direct au cahier de texte</a>') ;
                 }
                 else $('#mess').text('');
        }

    });

});