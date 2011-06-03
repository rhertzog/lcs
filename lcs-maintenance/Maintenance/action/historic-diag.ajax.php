<?php
/**
 * historic_list.ajax.php
 * Affichage de l'historique sous forme de tableau
 * 
 * 
*/
include "../Includes/config.inc.php";
include "../Includes/func_maint.inc.php";
$mode="team";
$filter = "Acq='2' ";
$html = '';
//table_alert("ici vient s'afficher le bilan pour la période choisie");
$html .= '<div class="tableau tableint" style="font-size:12px;">
<h3 class="subconfigsubtitle">Choix de la p&#233;riode</h3>
<label for="from">Date de d&#233;but</label>
<input type="text" id="from" name="from"/>
<label for="to">Date de fin</label>
<input type="text" id="to" name="to"/>
</div>';//<!-- End select date-->
$html .= '<script>
	$(function() {
		var dates = $( "#from, #to" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true,
			//numberOfMonths: 3,
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
	});
</script>';
$date=time();
$html .= Aff_bilan(0, $date);
$html .= "<script>
		var h=0;
		$.each($('#diagramm>div'), function(){
			$(this).height()>h? h=$(this).height()+50:'';
		});
		$('#diagramm').css('height',h);
		$('div.order').not('#btnHystoric').hide()
		</script>";

echo $html;
?>