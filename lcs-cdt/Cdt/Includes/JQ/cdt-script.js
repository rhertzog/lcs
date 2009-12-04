$(document).ready(function() {
//Deroulement de la partie droite du cdt.
//Au clic sur la barre verticale de boutons, les div s'affichent en se deroulant
	$(function(){ 
    	$("#deroulants .deroulant").hide(); 
    	$("#deroulants .deroulant:eq(0)").show(); 
	}); 
	$(function(){ 
    	$("#deroulants .deroulant").not(":first").hide(); 
    	$("#deroulants ul a").click(function(){ 
        	$("#deroulants .deroulant").slideUp("normal"); 
        	$(this.hash).slideDown("normal"); 
        	this.blur(); 
        	return false; 
    	}); 
	});
	
//Bouton pour switcher la barre de menu du lcs
	$(function(){
	if (window.parent.document.body.rows == "0,*") {
		$("#switch-barreLcs").removeClass("swup");
		$("#switch-barreLcs").addClass("swdown");
	}
	$("#switch-barreLcs").click(function(){
    	if ($("#switch-barreLcs").is(".swup")) {
    		window.parent.document.body.rows="0,*";
    		$("#switch-barreLcs").removeClass("swup");
    		$("#switch-barreLcs").addClass("swdown");
    	}
    	else if ($("#switch-barreLcs").is(".swdown")) {
    		window.parent.document.body.rows="90,*";
    		$("#switch-barreLcs").removeClass("swdown");
    		$("#switch-barreLcs").addClass("swup");
    	}

      });
	});

$.datepicker.setDefaults({
	closeText: 'Fermer',
		prevText: '&#x3c;Préc',
		nextText: 'Suiv&#x3e;',
		currentText: 'Courant',
		monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
		monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'],
		dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
		dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
		dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
		dateFormat: 'dd/mm/yy', 
		firstDay: 1,
		isRTL: false,
		showButtonPanel: true
	});
//Affichage du calendrier cours
	$('#datejavac').datepicker({});
	
//Affichage du calendrier a faire
	$('#datejaf').datepicker({});

//Affichage du calendrier debut absence
	$('#deb-abs').datepicker({});
	
//Affichage du calendrier fin des absences
	$('#fin-abs').datepicker({});
	
//Affichage du calendrier des absences pour cpe
	$('#cpe-abs').datepicker({});
	
//Affichage du calendrier debut des absences pour cpe
	$('#datepot_deb').datepicker({});
	
//Affichage du calendrier fin des absences pour cpe
	$('#datepot_fin').datepicker({});	

//Affichage du calendrier visible le
	$('#datejavav').datepicker({});	

//Affichage du calendrier cours diffusion
	$('#datejavac0').datepicker({});	
	$('#datejavac1').datepicker({});	
	$('#datejavac2').datepicker({});	
	$('#datejavac3').datepicker({});
	$('#datejavac4').datepicker({});
	$('#datejavac5').datepicker({});
	$('#datejavac6').datepicker({});
	$('#datejavac7').datepicker({});
	$('#datejavac8').datepicker({});
	$('#datejavac9').datepicker({});
	$('#datejavac10').datepicker({});
	$('#datejavac11').datepicker({});
	$('#datejavac12').datepicker({});
	$('#datejavac13').datepicker({});
	$('#datejavac14').datepicker({});
	$('#datejavac15').datepicker({});
	$('#datejavac16').datepicker({});
	$('#datejavac17').datepicker({});
	$('#datejavac18').datepicker({});
	$('#datejavac19').datepicker({});
	//Affichage du calendrier a faire diffusion
	$('#datejaf0').datepicker({});	
	$('#datejaf1').datepicker({});	
	$('#datejaf2').datepicker({});	
	$('#datejaf3').datepicker({});
	$('#datejaf4').datepicker({});
	$('#datejaf5').datepicker({});
	$('#datejaf6').datepicker({});
	$('#datejaf7').datepicker({});
	$('#datejaf8').datepicker({});
	$('#datejaf9').datepicker({});
	$('#datejaf10').datepicker({});
	$('#datejaf11').datepicker({});
	$('#datejaf12').datepicker({});
	$('#datejaf13').datepicker({});
	$('#datejaf14').datepicker({});
	$('#datejaf15').datepicker({});
	$('#datejaf16').datepicker({});
	$('#datejaf17').datepicker({});
	$('#datejaf18').datepicker({});
	$('#datejaf19').datepicker({});
	//Affichage du calendrier visible le diffusion
	$('#datejavav0').datepicker({});	
	$('#datejavav1').datepicker({});	
	$('#datejavav2').datepicker({});	
	$('#datejavav3').datepicker({});	
	$('#datejavav4').datepicker({});	
	$('#datejavav5').datepicker({});	
	$('#datejavav6').datepicker({});	
	$('#datejavav7').datepicker({});	
	$('#datejavav8').datepicker({});	
	$('#datejavav9').datepicker({});	
	$('#datejavav10').datepicker({});	
	$('#datejavav11').datepicker({});	
	$('#datejavav12').datepicker({});	
	$('#datejavav13').datepicker({});	
	$('#datejavav14').datepicker({});	
	$('#datejavav15').datepicker({});	
	$('#datejavav16').datepicker({});	
	$('#datejavav17').datepicker({});	
	$('#datejavav18').datepicker({});	
	$('#datejavav19').datepicker({});	
	
//	$("#input[@type=submit]:eq(0)").click(function () {
//		alert("bravo");
  //    $("#eqt").toggle("slow");
 //   });  

//appel des iframes	
//	$(".imathsframe").colorbox({width:"80%", height:"95%", iframe:true});
//	$(".a-imprime").colorbox({width:"80%", height:"400px", iframe:true});
//	$(".cdt-elv").colorbox({width:"90%", height:"500px", iframe:true});
//	$(".abs-elv").colorbox({width:"75%", height:"500px", iframe:true});

});