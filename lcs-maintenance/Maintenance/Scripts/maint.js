$(function(){
	//References
	var loading = $("#loading");
	var content = $("#contentDiv");
	var room = {};
	
	/**
	 * utils
	*/
	//show loading bar
	function showLoading(){
		loading
			.css({display:"block",'z-index':10})
			.animate({opacity:1},100)
		;
	}
	//hide loading bar
	function hideLoading(){
		loading
			.animate({opacity:0},1500, function(){
			$(this).css({display:"none",'z-index':-1 })
			})
	}
	// recup des valeurs d'un form 
	function formValues(f,d) {
		$.each(f.find('input,select'), function() {
			d[$(this).attr('id')] = $(this).val();
		});
		return d;
	}
	// injection des valeurs d'une table dans les form (pour add/edit/delete topo)
	function valuesToForm(d,f) {
		var arr = []; 
		$.each(f.find('input'), function(i,v) {
			arr[i] = d[$(this).attr('id')];
			$(this).attr('value',arr[i] );
			 $('#'+$(this).attr('id')).focus();
		});
		return d;
	}
	/**
	 * topo
	*/
	// ajax request to load topo
	function loadTopo(d) {
			$.ajax({
				type: "POST",
				url: "action/topo.ajax.php",
				cache: false,
				data: ({
					secteur: d.secteur,
					bat: d.bat,
					etage: d.etage,
					salle: d.salle,
					Rid: typeof d.Rid ? d.Rid: 0,
					addTopo: typeof d.addtopo ? d.addtopo : 0
				}),
				dataType: "html",
				success : function(data, status) {
					if(data!== null ){
						content.empty()
						.html(data, hideLoading()).animate({opacity:1},500)
						.find('select').uniform() ;
						selectChange();
						$( "#btnAddTopo" ).click(function() {
							var v = formValues($( "#topoTr" ),room);
							valuesToForm(v, $('#addTopoForm') );
				//			if(v.bat=="" || v.etage=="") alert(v.bat);
							$( "#dialogFormAddTopo" ).dialog( "open" );
						});
						$( "#btnDelTopo" ).click(function() {
							var v = formValues($( "#topoTr" ),room);
							valuesToForm(v, $('#delTopoForm') );
							$( "#dialogDelTopo" ).dialog( "open" ).find('.dialog-content').html('Supprimer la salle '+d.salle+' ?');
							
						});
						$( "#btnEditTopo" ).click(function() {
							var v = formValues($( "#topoTr" ),room);
							valuesToForm(v, $('#editTopoForm') );
							$( "#dialogEditTopo" ).dialog( "open" );
						});
						 $('div.tableau.tableint').width('800px;');
					}
				}
			});	
	}
	// list the select topo option value
	function listTopo(){
		var d=new Array();
		$('#topoTr').find('select').each(function(){
			d[$(this).attr('id')] = $(this).val();
		})
		console.log('hidden : ',$('#topoTr').find(':hidden').length )
		d['addtopo']=$('#addTopo').val();
		return d;
	}
	//Manage change events for form
	function selectChange() {
		$('#topoTr').find('select').each(function(){
			$(this).change(function(){
				// make array oh the topo values
				var lTp = listTopo();
				//show the loading bar
				showLoading();
				//Load content
				content.animate({opacity:.05},100,function(){loadTopo(lTp) });
			});
		});
	}
	/**
	 * datePicker
	*/
	// init du datepicker
	function maintDatePicker() {
		var dates = $( "#from, #to" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 3,
			onSelect: function( selectedDate ) {
				var option = this.id == "from" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		});
	}	
	/**
	 * pages historic
	*/
	//
	function htmlLoad(url, container,opts) {
		showLoading();
		$.ajax({
			type: "POST",
			url: url,
			cache: false,
			data:  opts,
			dataType: "html",
			success : function(data, status) {
				if(data!== null ){
					$(container).html(data);
				}
				return false;
			},
					
			complete: function(){hideLoading();$(container).animate({opacity:1},1000)}
		});	
	}
	//
	function initBtnHistory(){
		var img = $('#btnHystoric img');
		img.click(function(el) {
			el.stopPropagation();
			if($(this).hasClass('disabled')) return false;
			img.removeClass('disabled');
			var tImg = $(this), mode = tImg.attr('class'),btnTri=$('div.order').not('#btnHystoric');
				tImg.addClass('disabled');
			$('#contentHystoric').animate({opacity:.1},500, function(){
				htmlLoad('action/historic-'+mode+'.ajax.php', '#contentHystoric',{});
				mode!="list"?btnTri.hide():btnTri.show();
			});
		});
	}
	/**
	 * taritement des forms
	*/
	// pre-submit callback
	function validate(formData, jqForm, options) { 
	    jqForm.validate();
	}	
	// post-submit callback 
	function showResponse(responseText, statusText, xhr, $form)  { 
		//console.log(statusText);
  		$('#tabInfo').dialog({
			width:700,
			modal:true,
			close: function(event, ui) { 
				$(this).remove();
				statusText=='success'? $(location).attr('href','./'): '';
			},
			buttons:{
				"Fermer": function(){
					$( this ).dialog( "close" );
				}
			}
		});
	} 	

	//validation et envoidu form
	function initForm(opts){
		$("#demandeForm").validate({
			submitHandler: function(form) {
				$(form).ajaxSubmit(opts); 
				// !!! Important !!! 
				return false; 
			}
		});
	}
	
	//restor db
	function sendFile(){
		$("#submitRestor").validate({
			submitHandler: function(form) {
				$(form).ajaxSubmit(opts); 
				// !!! Important !!! 
				return false; 
			}
		});
	}
	/**
	 * init des elements
	*/
	// page demande_support.php
	if( document.location.href.match('demande_support') ) {
		// init du form
	    var optsDmdFrm = { 
	        target :        		'#ajaxPreview',   	// target element(s) to be updated with server response
	        //beforeSubmit :  	validate,  	// pre-submit callback -> validation
	        success :       		showResponse,  	// post-submit callback 
	        clearForm: true        // clear all form fields after successful submit 
	        //resetForm : 		true        			// reset the form after successful submit 
	
	        // other available options: 
	        //url:       url         // override for form's 'action' attribute 
	        //type:      type        // 'get' or 'post', override for form's 'method' attribute 
	        //dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
	 
	        // $.ajax options can be used here too, for example: 
	        //timeout:   3000 
	    }; 
		initForm(optsDmdFrm);
		
		// appel initial de la localisation
		var dataTopo={};
		loadTopo(dataTopo);
	}
	// page edit_demande.php
	if( document.location.href.match('edit_demande') ) {
		var substr = document.location.href.split('=');
		// appel initial de la localisation
		var dataTopo={ Rid: substr[1] };
		loadTopo(dataTopo);
	}
	
	// page historique generale
	if( document.location.search.match('=historique') ) {
		initBtnHistory();
		maintDatePicker();
	}
	// page config.php
	if( document.location.href.match('config') ) {
		if( document.location.search.match('=restor') ) {
			$('#submitRestor').ajaxForm({
				target: '#showdata',
				beforeSerialize: function($form, options) { 
					$('#loading').css({'margin-top':'50px','margin-left':'150px'});
					showLoading();
					$('#submitRestor').animate({opacity:0.3},500);            
				},
				success: function(data) {
					hideLoading();
					$('#showdata').fadeIn('slow');
					setTimeout(function(){
						$('#showdata').fadeOut('slow') 
						$('#submitRestor').animate({opacity:1},500);            
					},2000);	
				}
			});
		}
	if( document.location.search.match('=topo')) {
	 	$(document).ready(function(){
	  		$('div#divAddTopo').load('Includes/form_edit_topo.php', function(){
				$('#addTopoForm').ajaxForm({
					target: '#showdata',
					success: function(data) {
						$('#showdata').fadeIn('slow');
						var dataTopo={ addtopo: 'Y' };
						loadTopo(dataTopo);
						setTimeout(function(){
							$('#showdata').fadeOut('slow') 
						},2000);	
					}
				});
				$('#delTopoForm').ajaxForm({
					target: '#showdata',
					success: function(data) {
						loadTopo({ addtopo: 'Y' });
						$('#showdata').fadeIn('slow');
						setTimeout(function(){
							$('#showdata').fadeOut('slow') 
						},2000);	
					}
				});
				$('#editTopoForm').ajaxForm({
					target: '#showdata',
					success: function(data) {
						loadTopo({ addtopo: 'Y' });
						$('#showdata').fadeIn('slow');
						setTimeout(function(){
							$('#showdata').fadeOut('slow') 
						},2000);	
					}
				});
				var dataTopo={ addtopo: 'Y' };
				loadTopo(dataTopo);
			    $('#topoTab>div.tableau').width('785px');
				$( "#dialogFormAddTopo" ).dialog({
					autoOpen: false,
					height: 220,
					width: 350,
					modal: true,
					buttons: {
						"Ajouter": function() {
							$('#addTopoForm').trigger('submit');
							$( this ).dialog( "close" );
						},
						"Annuler": function() {
							$( this ).dialog( "close" );
						}
					}
				});
				$( "#dialogDelTopo" ).dialog({
					autoOpen: false,
					resizable: false,
					height:200,
					modal: true,
					buttons: {
						"Supprimer cette élément ?": function() {
							$('#delTopoForm').trigger('submit');
							$( this ).dialog( "close" );
						},
						"Annuler": function() {
							$( this ).dialog( "close" );
						}
					}
				});
				$( "#dialogEditTopo" ).dialog({
					autoOpen: false,
					resizable: false,
					height:220,
					width: 350,
					modal: true,
					buttons: {
						"Valider": function() {
							$('#editTopoForm').trigger('submit');
							$( this ).dialog( "close" );
						},
						"Annuler": function() {
							$( this ).dialog( "close" );
						}
					}
				});

	  		});		
	 	});
	}
	}
});