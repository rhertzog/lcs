$(document).ready(function() {

//afichage, masquage description sequence
    $(function(){
        $(".switch_seq").click(function(){
        var  no=$(this).attr('id');
        if ($(this).is(".clos")) {
            $(this).removeClass("clos");
            $(this).addClass("open");
            $(this).attr('title','- de d\351tails');
            $("#d" + no ).slideToggle("slow");
            }
        else if ($(this).is(".open")) {
            $(this).removeClass("open");
            $(this).addClass("clos");
             $(this).attr('title','+ de d\351tails');
            $("#d" + no ).slideToggle("slow");
            }
        });
    });
 
 //modification ordre afffichage contenu sequence
    $(function(){
          $(".order").click(function(){
          var  no=$(this).attr('id');
          var rqq=$("#r" + no).val();
          var butt=$("#b" + no).val();
          var tic=$("#t" + no).val();
          if ($(this).is(".up")) {
            $(this).removeClass("up");
            $(this).addClass("down");
            $(this).attr('title','Afficher par date d\351croissante');
            var senss="asc";
            }
           else if ($(this).is(".down")) {
                $(this).removeClass("down");
                $(this).addClass("up");
                $(this).attr('title','Afficher par date croissante');
                var senss="desc";
                }
           $.ajax({
                    type: "POST",
                    url : "refresh-seq.php",
                    data : {rqt : rqq , sens : senss, buttons:butt ,tiket:tic},
                    async: false,
                     success :function(data)
                        {
                        if (data =="error")   
                         alert('Erreur' +data);
                        else 
                        $("#c"+no).html(data);
			//document.getElementById("c"+no).innerHTML =  data;
                       }
             });
        });
    });
 //popup qrcode   
 $(function() {
        $( "#dialog" ).dialog({
            autoOpen: false,
            show: "blind",
            hide: "explode",
                        position: ['center','top'],
                        width:430,
                        resizable: true
           });

        $( "#bt-qrcode" ).click(function() {
            $( "#dialog" ).dialog( "open" );
            return false;
        });
    });
});
