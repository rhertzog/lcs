function refresh_coms(quoi,quand){
				var xhr = getXhr();
					
				var data = "blabla="+ quoi+ "&kan=" + quand;
				// On d�fini ce qu'on va faire quand on aura la r�ponse
				 function resultat2(){
					// On ne fait quelque chose que si on a tout re�u et que le serveur est ok
					if(xhr.readyState == 4) {
						if(xhr.status == 200){
						if(xhr.responseText =='OK')	
						alert(xhr.responseText);
						else {
							//
							document.getElementById("boite5elv").innerHTML =  xhr.responseText;
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
				// On d�fini ce qu'on va faire quand on aura la r�ponse
				 function resultat(){
					// On ne fait quelque chose que si on a tout re�u et que le serveur est ok
					if(xhr.readyState == 4) {
						if(xhr.status == 200){
						if(xhr.responseText =='Erreur lecture ')	
						alert(xhr.responseText);
						else {
							document.getElementById("onglev_refresh").innerHTML =  xhr.responseText;
						/////////////	
							refresh_coms(cible,tmstp);
							
						/////////////	
							
							}
						}
						else
						{
							alert('Probleme Ajax');
						}
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
				// On d�fini ce qu'on va faire quand on aura la r�ponse
				 function resultat3(){
					// On ne fait quelque chose que si on a tout re�u et que le serveur est ok
					if(xhr.readyState == 4) {
						if(xhr.status == 200){
						if(xhr.responseText =='Erreur lecture ')	
						alert(xhr.responseText);
						else {
							document.getElementById("onglev_refresh").innerHTML =  xhr.responseText;
						/////////////	
							refresh_coms_arch(cible,tmstp,arch_an);
							
						/////////////	
							
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
				// On d�fini ce qu'on va faire quand on aura la r�ponse
				 function resultat4(){
					// On ne fait quelque chose que si on a tout re�u et que le serveur est ok
					if(xhr.readyState == 4) {
						if(xhr.status == 200){
						if(xhr.responseText =='OK')	
						alert(xhr.responseText);
						else {
							//
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