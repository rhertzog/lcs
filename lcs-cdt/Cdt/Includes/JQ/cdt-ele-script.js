$(document).ready(function() {

// switch postit
    $(function(){
        $("#switch-postit").click(function() {
            if ($("#switch-postit").is(".postup")) {
                $("#switch-postit").removeClass("postup");
                $("#switch-postit").addClass("postdown");
                $('#postit-eleve').animate({top: '-=280'});
            }
            else if ($("#switch-postit").is(".postdown")) {
                $("#switch-postit").removeClass("postdown");
                $("#switch-postit").addClass("postup");
                $('#postit-eleve').animate({top: '+=280'}) 
            };
        });
    });

    // switch onglets: offfset est definie dans cahier_text_eleve.php
    $(function(){
        $("#switch-ongletsup").click(function() {
        $('#onglev').animate({top: '-='+ offset});
         });
    });

    $(function(){
        $("#switch-ongletsdown").click(function() {
        $('#onglev').animate({top: '+='+ offset});
         });
    });
});

