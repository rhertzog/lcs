<?php
/* $Id: groups.php,v 1.28 2007/08/02 12:57:51 umcesrjones Exp $ */
defined ( '_ISVALID' ) or die ( 'You cannot access this file directly!' );

$count = $lastrow = 0;
$newGroupStr = translate ( 'Add New Group' );
$targetStr = 'target="grpiframe" onclick="showFrame( \'grpiframe\' );">';

ob_start ();
?>
<a name="tabgroups"></a>
	<div id="tabscontent_groups">
	<!-- Modification LCS 1/2 -->
	<h4>Gestion des groupes</h4>
	<p>La gestion des groupes dans l'application ÇAgendas LCSÈ, se fait via l'application <a style="text-decoration:underline;"href="../../Annu/">Annuaire</a>.</p>
	<!-- Modification LCS 2/2
		<?php
			echo "<a title=\"" .
				translate("Add New Group") . "\" href=\"group_edit.php\" target=\"grpiframe\" onclick=\"javascript:show('grpiframe');\">" .
				translate("Add New Group") . "</a><br />\n";
		?>
			<?php
			 $count = 0;
				$lastrow = 0;
				$res = dbi_query ( "SELECT cal_group_id, cal_name FROM webcal_group ORDER BY cal_name" );
				if ( $res ) {
					while ( $row = dbi_fetch_row ( $res ) ) {
					  if ( $count == 0 ) {
						  echo "<ul>\n";
						}
					echo "<li><a title=\"" .
						$row[1] . "\" href=\"group_edit.php?id=" . $row[0] . "\" target=\"grpiframe\" onclick=\"javascript:show('grpiframe');\">" .
						$row[1] . "</a></li>\n";
						$count++;
						$lastrow = $row[0];
					}
					if ( $count > 0 ) { echo "</ul>\n"; }
				 dbi_free_result ( $res );
				}

			echo "<iframe src=\"group_edit.php?id=" . $lastrow . "\" name=\"grpiframe\" id=\"grpiframe\" style=\"width:90%;border-width:0px; height:325px;\"></iframe>";
		?>
	Fin modification LCS 2/2  -->
</div>
<?
ob_end_flush ();

?>