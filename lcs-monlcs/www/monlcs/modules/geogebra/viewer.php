<?php
require_once('/var/www/lcs/includes/headerauth.inc.php');
$ggb = '/monlcs/modules/geogebra/ggb1.ggb';

if ($_POST || $_GET) {

	extract($_POST);
	extract($_GET);
}
	
	$ggb = str_replace(' ','%20',$ggb);
	$ggb = $baseurl.$ggb;

?>

<applet name="ggbApplet" code="geogebra.GeoGebraApplet" codebase="/" archive="/monlcs/modules/geogebra/geogebra.jar" width="100%" height="100%">

	<param name="filename" value="<?php echo $ggb; ?>">
	<param name="framePossible" value="false">
	<param name="showResetIcon" value="false">
	<param name="enableRightClick" value="false">
	<param name="showMenuBar" value="false">
	<param name="showToolBar" value="false">
	<param name="showToolBarHelp" value="false">
	<param name="showAlgebraInput" value="false">
Sorry, the GeoGebra Applet could not be started. Please make sure that Java 1.4.2 (or later) is installed and active in your browser (<a href="http://java.sun.com/getjava">Click here to install Java now</a>)

</applet>
<?php
?>
