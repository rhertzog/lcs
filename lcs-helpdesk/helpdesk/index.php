<?php
/* helpdesk/index.php derniere mise a jour : 23/06/2011 */
require_once "./include/common.inc.php";

list ($idpers, $login)= isauth();
$administrator = (is_admin('Lcs_is_admin', $login) == "Y") || (is_admin('System_is_admin', $login) == "Y");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>...::: HELPDESK LCS 2.0 :::...</title>
  <meta HTTP-EQUIV="Content-Type" CONTENT="tetx/html; charset=ISO-8859-1">



<style type="text/css">
body	{
	margin-right: 6%;
}
</style>

<?php if ($administrator) : ?>
	<link rel="stylesheet" type="text/css" href="/libjs/ext/resources/css/ext-all.css"></link>
	<link rel="stylesheet" type="text/css" href="/helpdesk/css/tree.css"></link>
<?php endif; ?>

<script type='text/javascript' src='/libjs/prototype/prototype.js'></script>

<?php if ($administrator) {  
	if (!$_SESSION['userHD']) {
		$_SESSION['userHD']= base64_encode($login);
	}
 }
 ?>	
	<script type='text/javascript' src='/helpdesk/js/arbo.js'></script>
	<script type='text/javascript' src='/libjs/ext/adapter/prototype/ext-prototype-adapter.js'></script>
	<script type='text/javascript' src='/libjs/ext/adapter/ext/ext-base.js'></script>
	<script type='text/javascript' src='/libjs/ext/ext-all-debug.js'></script>
	<script type='text/javascript' src='/helpdesk/js/RowExpander.js'></script>
	<script type="text/javascript" src="/helpdesk/js/helpdesk.js"></script>
	<script type='text/javascript'>
		function init() {
			doHelpDesk(true);
		}
		Event.observe(window,'load',init,true);
	</script>


</head>

<body bgcolor="#f8f8ff" >
</body>
</html>
