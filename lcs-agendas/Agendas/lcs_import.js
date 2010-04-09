function getXhr(){
                var xhr = null; 
				if(window.XMLHttpRequest) // Firefox et autres
				   xhr = new XMLHttpRequest(); 
				else if(window.ActiveXObject){ // Internet Explorer 
				   try {
			                xhr = new ActiveXObject("Msxml2.XMLHTTP");
			            } catch (e) {
			                xhr = new ActiveXObject("Microsoft.XMLHTTP");
			            }
				}
				else { // XMLHttpRequest non supporté par le navigateur 
				   alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest..."); 
				   xhr = false; 
				} 
                 return xhr;
			}
			
function go(cib){
				var xhr = getXhr();
				var contenu = "aa";
				var data = "test=" + cib;
				// On défini ce qu'on va faire quand on aura la réponse
				 function resultat(){
					// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
					if(xhr.readyState == 4) {
						if(xhr.status == 200){
						if(xhr.responseText !='')	
						alert(xhr.responseText);
						else {
							alert('Import termin\351');
							}
						}
						
				   	}	
				}
				xhr.onreadystatechange = resultat;
				xhr.open("POST","lcs_insert_db.php",true);
				xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");				
				xhr.send(data);
			}

function affiche_cr() {
var xhr2 = getXhr();
var div_cr = document.getElementById("cr");
var datas = "requete=yes"
	function result(){
					// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
					if(xhr2.readyState == 4) {
						if(xhr2.status == 200){
						if(xhr2.responseText !='')
						{
						if (div_cr) document.getElementById("cr").innerHTML = xhr2.responseText;
						}
						else
						{
						if (div_cr) document.getElementById("cr").innerHTML = "Pas de compte rendu diponible";
						}
					}
				}
	}
	xhr2.onreadystatechange = result;
				xhr2.open("POST","lcs_lire_cr.php",true);
				xhr2.setRequestHeader("Content-type","application/x-www-form-urlencoded");				
				xhr2.send(datas);
 if (div_cr) setTimeout("affiche_cr()",3000);
 
}
