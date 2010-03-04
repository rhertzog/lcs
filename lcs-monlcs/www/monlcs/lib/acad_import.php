<? header("text/javascript"); 
   include("../includes/config.inc.php");
   include("../includes/config_acad.inc.php");
?>
			var acad_container;


			function addTabAsUrl(link) {
				window.open(link);
			}

			function liste_fiches() {
				var id_scen = $('id_scen').innerHTML;
				var params = '?action=getToken&id_scen='+id_scen+'&profil='+trim(quiC); 
				new Ajax.Request("ajax.lib.php",{ method: 'get', parameters: params, onSuccess: function(xhr) {
                                        if (trim(xhr.responseText) == '') {
						alert('Aucune fiches');
						return false;
					}
					//requete vers la plateforme
					Element.show('spinner');
					var url = '<? echo "$dir_url_distant/UpdateListeFiche.php"; ?>';
					var complement='token='+trim(xhr.responseText)+'&profil='+trim(quiC);
					var params='url='+encodeURIComponent(url)+'&complement='+encodeURIComponent(complement);
					new Ajax.Updater("fiches_acad_list","curl_send.php",{ method: 'get', parameters: params, onComplete: function(xhr) {
						//alert(xhr.responseText);
						Element.hide('spinner');
						var ajaxWindCmd788=dhtmlwindow.open(
                        				'ajaxWindCmd788',
                        				'div',
                        				'fiches_acad_list',
                        				'Fiches disponibles',
                        				'width=400px,height=330px,left=35px,top=70px,resize=1,scrolling=1,center=0'
                        			);
                        			pretty_cmd(ajaxWindCmd788);
						var liste = document.getElementsByClassName('acad_link_file');
						//alert(liste.length);
						for (var i=0;i < liste.length-1; i++) {
							liste[i].onclick = function() {
								addTabAsUrl(this.getAttribute('tag'));
							}
						}
						return true;
 	                               }});
                                }});

			}

			function listdyn() {

				var url = '<? echo "$dir_url_distant/UpdateListeSection.php"; ?>';
				var complement='type_etab='+$('type_etab').value;
				var params='url='+encodeURIComponent(url)+'&complement='+encodeURIComponent(complement);
			
				var elem = 'section';
				Element.show('spinner');

				new Ajax.Updater(elem,"curl_send.php",{ method: 'get', parameters: params, onComplete: function(requester) {
					//alert(requester.responseText);
					Event.observe('section','change',listdyn2,false);
					Element.hide('spinner');
					listdyn2();

				}
				});
			}

			function listdyn2() {
			
				var url = '<? echo "$dir_url_distant/UpdateListeNiveaux.php"; ?>';
				var complement = 'type_etab='+$('type_etab').value+'&section='+$('section').value;

				var params='url='+encodeURIComponent(url)+'&complement='+encodeURIComponent(complement);

				var elem = 'niveau';
				Element.show('spinner');

				new Ajax.Updater(elem,'curl_send.php',{ method: 'get', parameters: params, onComplete: function(requester) {
					
					Element.hide('spinner');

				}
				});
			
					
		
			}
	
		 function acad_send() {
		
			Element.show('spinner');
			var url = 'SearchScens.php';
              		var params= '';

			if ($('chkSearchByLevel').checked){
				params+= 'type_etab='+$('type_etab').value;
				params+= '&section='+$('section').value;
				params+= '&niveau='+$('niveau').value;
			}
			else
				params+= '&type_etab=all&section=all&niveau=all';

			if ($('chkSearchByDomain').checked){
				params+= '&dom='+$('domaines').value;
			}
			else
				params+= '&dom=all';

			if ($('chkSearchByKeyword').checked){
				params+= '&key='+$('keyword').value;
			}
			else
				params+= '&key=all';
			
			var url = '<? echo "$dir_url_distant/SearchScens.php"; ?>';
			var complement=params;
			params='url='+encodeURIComponent(url)+'&complement='+encodeURIComponent(complement);
			//alert(params);
			
			Element.hide("acad");		
			new Ajax.Updater('list-results','curl_send.php',{ method: 'post', parameters: params, onComplete: function(requester) {
			
				Element.hide('spinner');
			
				var liste = document.getElementsByClassName('jeton_link');
				var size = parseInt(530+liste.length*150);
				
				$('ajaxWindCmd778').setSize(800,size);

					for (var i = 0; i < liste.length; i++) {
						liste[i].onclick = function() {
							var jeton = this.id;
							voirAcadScen(jeton);
													
						}
					}

 		       }});
			

		}		

	
		    
			 function init_synchro(elem) {
				acad_container = elem;				
				Event.observe('btnSearch','click',acad_send,false);
				var url = '<? echo "$dir_url_distant/UpdateListeDomaines.php"; ?>';
				var params='url='+url;
				new Ajax.Updater('domaines','curl_send.php',{ method: 'get', parameters: params, onComplete: function(requester) {
					//alert(requester.responseText);
				}
				});

				var url = '<? echo "$dir_url_distant/dynlist.php"; ?>';
				var params='url='+url;
				Element.show('spinner');
			
				new Ajax.Updater('type_etab','curl_send.php',{ method: 'get', parameters: params, onComplete: function(requester) {
					Event.observe('type_etab','change',listdyn,false);
					Event.observe('section','change',listdyn2,false);
					Element.hide('spinner');
					listdyn();
					

				}
				});



			}
