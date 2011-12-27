function refresh_coms(quoi,quand){
    var xhr = getXhr();
    var data = "blabla="+ quoi+ "&kan=" + quand;
    // On défini ce qu'on va faire quand on aura la réponse
     function resultat2(){
            // On ne fait quelque chose que si on a tout reçu et que le serveur est ok
            if(xhr.readyState == 4) 
                {
                if(xhr.status == 200) 
                    { 
                    if (xhr.responseText =='OK') alert(xhr.responseText);
                    else 
                        {
                        document.getElementById("boite5elv").innerHTML =  xhr.responseText;
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
                        }
                    }
                else
                {
                alert('Probleme Ajax');
                }
            }	
    }
    xhr.onreadystatechange = resultat2;
    xhr.open("POST","refresh-coms_eleve.php",true);
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");				
    xhr.send(data);
    }
		
function refresh_cdt(cible,tmstp) {
    var xhr = getXhr();
    var les_datas = "rubrik=" + cible + "&thedate=" + tmstp ;
    // On défini ce qu'on va faire quand on aura la réponse
    function resultat(){
        // On ne fait quelque chose que si on a tout reçu et que le serveur est ok
        if(xhr.readyState == 4) {
            if(xhr.status == 200) {
                if(xhr.responseText =='Erreur lecture ')	
                alert(xhr.responseText);
                else 
                    {
                    document.getElementById("onglev_refresh").innerHTML =  xhr.responseText;
                    refresh_coms(cible,tmstp);   
                    }
                 }
            else    alert('Probleme Ajax');
              }	
        }
    xhr.onreadystatechange = resultat;
    xhr.open("POST","refresh-ongl_eleve.php",true);
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");				
    xhr.send(les_datas);
    }

function refresh_cdt_arch(cible,tmstp,arch_an) {
    var xhr = getXhr();
    var les_datas2 = "rubrik=" + cible + "&thedate=" + tmstp + "&thearch=" + arch_an;
    // On défini ce qu'on va faire quand on aura la réponse
     function resultat3(){
        // On ne fait quelque chose que si on a tout reçu et que le serveur est ok
        if(xhr.readyState == 4) {
            if(xhr.status == 200){
                if(xhr.responseText =='Erreur lecture ')	
                alert(xhr.responseText);
                else 
                    {
                    document.getElementById("onglev_refresh").innerHTML =  xhr.responseText;
                    refresh_coms_arch(cible,tmstp,arch_an);
                    }
                }
            else
                {
                alert('Probleme Ajax');
                }
            }	
        }
    xhr.onreadystatechange = resultat3;
    xhr.open("POST","refresh-ongl_eleve_arch.php",true);
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");				
    xhr.send(les_datas2);
}
			
function refresh_coms_arch(quoi,quand,ann_arc){
    var xhr = getXhr();
    var data2 = "blabla="+ quoi+ "&kan=" + quand + "&thean_arch=" + ann_arc;
    // On défini ce qu'on va faire quand on aura la réponse
     function resultat4(){
        // On ne fait quelque chose que si on a tout reçu et que le serveur est ok
        if(xhr.readyState == 4) {
            if(xhr.status == 200){
                if(xhr.responseText =='OK')	
                alert(xhr.responseText);
                else 
                    {
                     document.getElementById("boite5elv").innerHTML =  xhr.responseText;
                    }
                }
            else
                {
                alert('Probleme Ajax');
                }
            }	
        }
    xhr.onreadystatechange = resultat4;
    xhr.open("POST","refresh-coms_eleve_arch.php",true);
    xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");				
    xhr.send(data2);
}
