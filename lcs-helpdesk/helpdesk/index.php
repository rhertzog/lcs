<?php
/* helpdesk/index.php derniere mise a jour : 10/2009 */
require_once "./include/common.inc.php";

list ($idpers, $login)= isauth();
$administrator = (is_admin('Lcs_is_admin', $login) == "Y");

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
	<link rel="stylesheet" type="text/css" href="/lib/js/ext/resources/css/ext-all.css"></link>
	<link rel="stylesheet" type="text/css" href="/helpdesk/css/tree.css"></link>
<?php endif; ?>

<script type='text/javascript' src='/lib/js/prototype.js'></script>

<?php if ($administrator) {  
	if (!$_SESSION['user']) {
		$_SESSION['user']= base64_encode($login);
	}
 }
 ?>	
	<script type='text/javascript' src='/helpdesk/js/arbo.js'></script>
	<script type='text/javascript' src='/lib/js/ext/adapter/prototype/ext-prototype-adapter.js'></script>
	<script type='text/javascript' src='/lib/js/ext/adapter/ext/ext-base.js'></script>
	<script type='text/javascript' src='/lib/js/ext/ext-all-debug.js'></script>
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
