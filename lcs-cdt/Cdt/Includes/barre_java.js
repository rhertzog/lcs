function joint_popup() {
                                window.focus();
                                joint_popupWin = window.open("./joindre.php","","width=500,height=500,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                joint_popupWin.focus();
                        }
						
function diffuse_popup(rub) {
                                window.focus();
                                diffuse_popupWin = window.open("./diffuse.php?rubrique="+rub+"","","width=650,height=500,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                diffuse_popupWin.focus();
                        }
						
function image_popup() {
                                window.focus();
                                image_popupWin = window.open("./joint_picture.php","form_img","width=500,height=450,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");                          
                                image_popupWin.focus();
                                return false;
                        }
						
function lien_popup() {
                                window.focus();
                                lien_popupWin = window.open("./hyper.php","","width=500,height=350,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                lien_popupWin.focus();
                        }
                        
function postit_popup(rubr) {
                                window.focus();
                                postit_popupWin = window.open("./posti1.php?rubrique="+rubr+"","","width=2,height=2,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                postit_popupWin.focus();
                        }		
                        						
function arch_popup(numarc) {
                                window.focus();
                                arch_popupWin = window.open("./cahier_texte_arch.php?arch="+numarc+ "","","");
                                arch_popupWin.focus();
                        }
function arch_perso_popup(numarc) {
                                window.focus();
                                arch_popupWin = window.open("./cahier_texte_arch_perso.php?arch="+numarc+ "","","");
                                arch_popupWin.focus();
                        }
                        	
function form_popup() {
                                window.focus();
                                form_popupWin = window.open("./inserform.php","","width=550,height=500,directories=no,resizable=no,location=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                form_popupWin.focus();
                        }
                        
function aidemath_popup() {
                                window.focus();
                                lien_popupWin = window.open("../phpmathpublisher/doc_fr/help_fr.html","","width=550,height=500,left=560,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                lien_popupWin.focus();
                        }

function taf_popup(clas) {
                                window.focus();
                                taf_popupWin = window.open("./taf.php?div="+clas+ "","","width=980,height=500,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                taf_popupWin.focus();
                        }
function diffusedev_popup(rub) {
                                window.focus();
                                diffusedev_popupWin = window.open("./diffuse_devoirs.php?rubrique="+rub+"","","width=600,height=450,resizable=no,scrollbars=yes,toolbar=no,menubar=no,status=no");
                                diffusedev_popupWin.focus();
                        }
function abs_popup(log,fname) {
                                window.focus();
                                abs_popupWin = window.open("./pop_abs.php?uid="+log+"&fn="+fname+"","","width=680,height=560,resizable=no,scrollbars=no,toolbar=no,menubar=no,status=no");
                                abs_popupWin.focus();  
                        }	
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
			
function go(cib,key){
				var xhr = getXhr();
				var contenu = "aa";
				contenu = escape(tinyMCE.get('aide-memoire').getContent());
				// Do you ajax call here, window.setTimeout fakes ajax call
	
				var data = "blabla="+ contenu+ "&cibl=" + cib + "&TA=" + key;
				// On défini ce qu'on va faire quand on aura la réponse
				 function resultat(){
					// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
					if(xhr.readyState == 4) {
						if(xhr.status == 200){
						if(xhr.responseText !='OK')	
						alert(xhr.responseText);
						else {
							//document.getElementById("voyant").innerHTML = '<img alt="aide"   border="0"src="../images/voyant.png"   />';
							//setTimeout(function() {document.getElementById('voyant').innerHTML = '';},1000);
							tinyMCE.get('aide-memoire').setProgressState(true);
							setTimeout(function() {tinyMCE.get('aide-memoire').setProgressState(false);},800);
							}
						}
						else
						{
							alert('Probleme Ajax');
						}
				   	}	
				}
				xhr.onreadystatechange = resultat;
				xhr.open("POST","posti1.php",true);
				xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");				
				xhr.send(data);
			}

function go2(cib,key){
				var xhr = getXhr();
				var contenu = "aa";
				contenu = escape(tinyMCE.get('aide-memoire2').getContent());
				// Do you ajax call here, window.setTimeout fakes ajax call
	
				var data = "blibli="+ contenu+ "&cibl=" + cib + "&TA=" + key;
				// On défini ce qu'on va faire quand on aura la réponse
				 function resultat(){
					// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
					if(xhr.readyState == 4) {
						if(xhr.status == 200){
						if(xhr.responseText !='OK')	
						alert(xhr.responseText);
						else {
							tinyMCE.get('aide-memoire2').setProgressState(true);
							setTimeout(function() {tinyMCE.get('aide-memoire2').setProgressState(false);},800);
							}
						}
						else
						{
							alert('Probleme Ajax');
						}
				   	}	
				}
				xhr.onreadystatechange = resultat;
				xhr.open("POST","posti2.php",true);
				xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");				
				xhr.send(data);
			}
			
function modeleLoad (cib,key) {
	var xhr = getXhr();
					
				var datamod = "cibl=" + cib + "&TA=" + key;
				// On défini ce qu'on va faire quand on aura la réponse
				 function resultat(){
					// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
					if(xhr.readyState == 4) {
						if(xhr.status == 200){
						if(xhr.responseText =='NOK')	
						alert('erreur');
						else {
						var docXML= xhr.responseXML;
						var items = docXML.getElementsByTagName("donnee")
						tinyMCE.get('coursfield').setContent(items.item(0).firstChild.data);
						tinyMCE.get('afairefield').setContent(items.item(1).firstChild.data);
							}
						}
						else
						{
							alert('Probleme Ajax');
						}
				   	}	
				}
				xhr.onreadystatechange = resultat;
				xhr.open("POST","load_modele.php",true);
				xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");				
				xhr.send(datamod);
			}

function modeleSave(cib,key) {
	var xhr = getXhr();
				var modelecours = "";
				var modeleafaire = "";
				modelecours = escape(tinyMCE.get('coursfield').getContent());
				modeleafaire= escape(tinyMCE.get('afairefield').getContent());
				// Do you ajax call here, window.setTimeout fakes ajax call
	
				var datamod = "coursmod=" + modelecours + "&afmod=" + modeleafaire + "&cibl=" + cib + "&TA=" + key;
				// On défini ce qu'on va faire quand on aura la réponse
				 function resultat(){
					// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
					if(xhr.readyState == 4) {
						if(xhr.status == 200){
						if(xhr.responseText !='OK')	
						alert(xhr.responseText);
						else {
							//document.getElementById("voyant").innerHTML = '<img alt="aide"   border="0"src="../images/voyant.png"   />';
							//setTimeout(function() {document.getElementById('voyant').innerHTML = '';},1000);
							tinyMCE.get('coursfield').setProgressState(true);
							setTimeout(function() {tinyMCE.get('coursfield').setProgressState(false);},800);
							//tinyMCE.get('coursfield')setContent('HTML content that got passed from server.');
							tinyMCE.get('afairefield').setProgressState(true);
							setTimeout(function() {tinyMCE.get('afairefield').setProgressState(false);},800);
							}
						}
						else
						{
							alert('Probleme Ajax');
						}
				   	}	
				}
				xhr.onreadystatechange = resultat;
				xhr.open("POST","save_modele.php",true);
				xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");				
				xhr.send(datamod);
			}


			
			
			