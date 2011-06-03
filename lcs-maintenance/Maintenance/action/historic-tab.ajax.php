<?php
/**
 * historic_tab.ajax.php
 * Affichage de l'historique sous forme de tableau
 * 
 * 
*/
include "../Includes/config.inc.php";
include "../Includes/func_maint.inc.php";
$mode="team";
$filter = "Acq='2' ";
#Aff_bar_mode ("Historique gen.");
echo '<div style="float:right;padding:5px;"><span>Trouver : </span><input type="text" id="id_search" value="" name="search" /></div>';
$html = "<div id=\"pager\" style=\"margin:auto\"class=\"pager\">\n
			<form>\n
				<img src=\"Style/img/16/resultset_first.png\" class=\"first\"/>\n
				<img src=\"Style/img/16/resultset_previous.png\" class=\"prev\"/>\n
				<input type=\"text\" class=\"pagedisplay\"/>\n
				<img src=\"Style/img/16/resultset_next.png\" class=\"next\"/>\n
				<img src=\"Style/img/16/resultset_last.png\" class=\"last\"/>\n
				<select class=\"pagesize\">\n
					<option selected=\"selected\"  value=\"10\">10</option>\n
					<option value=\"20\">20</option>\n
					<option value=\"30\">30</option>\n
					<option value=\"40\">40</option>\n
					<option value=\"40\">50</option>\n
					<option value=\"100\">100</option>\n
					<option value=\"200\">200</option>\n
					<option value=\"500\">500</option>\n
					<option value=\"1000\">1000</option>\n
				</select>\n
			</form>\n
		</div>\n";
		echo $html;
		echo '<div id="nbRowsInfo"></div>';
#$mon_local=setlocale (LC_TIME, 'fr_FR.utf8','fra');
#echo $mon_local; 

Aff_feed_closeTab ($mode, $filter,"desc")
?>
<script>
// init input search
	$('input#id_search').quicksearch('table.tablesorter tbody tr:not(.expand-child)',
	{
		'stripeRows': ['odd', 'even'],
 		'onAfter': function () {
			var nbRows = $('table.tablesorter tbody').find('tr:visible').length,
				 st = $('input#id_search').val()!="" ?  ' requ&#234;tes concernant '+$('input#id_search').val():' requ&#234;tes ';
			$('#nbRowsInfo').html( nbRows + st).show('slow');
		}
	});

// init table sorter, collapse and pager
	$('table.tablesorter')
	.collapsible("td.collapsible", {
		collapse: true
	})
	.tablesorter({
		widgets: ['zebra'],
		headers: {
		    9: { sorter: "shortDate" },
		    10: { sorter: "isoDate" },
		    11: { sorter: "shortDate" },
		    12: { sorter: "currency" },
		    13: { sorter: "currency" },
		    14: { sorter: "digit" },
		widthFixed: true 
		}
	}).tablesorterPager({
		positionFixed: false,
		container: $('#pager')
	})
	
</script>
<?php
?>