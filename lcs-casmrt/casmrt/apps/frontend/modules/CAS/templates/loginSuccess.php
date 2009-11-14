<!--
<body onload="if (document.getElementById('username')) document.getElementById('username').focus()">
-->

<table id="login-box">
	<tr><td colspan="2"><div id="headline-container"><strong>CAS</strong> Central Login</div></td></tr>
	<tr><td id="logo-container"><img src="/symf_cas/css/themes/simple/logo.png" id="logo"/></td>
	    <td id="login-form-container">
	<form method="post" id="login-form" action="<?php echo url_for('CAS/login'); ?>" 
	    onsubmit="submitbutton = document.getElementById('login-submit'); submitbutton.value='Please wait...'; submitbutton.disabled=true; return true;">
	<table id="form-layout">
		<tr>
			<td><?php if ($usr) { echo $usr." est connecté sur le LCS"; } else { echo "<A href=\"/lcs/\" >Connectez vous depuis le LCS.</A>"; } ?></td>
			<td id="submit-container">
			<input type="hidden" value="" name="service" id="service"/>
		</tr>
		<tr>
			<td colspan="2" id="infoline">Powered by CRDP-TICE</td></tr></table></form></td>
		</tr>
</table>

<?php if (isset($usr) && ($usr != '') && isset($url) && ($url != "")) { 
	//die($url);
        echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
        echo "<!--\n";
        echo "top.location.href = '".$url."';\n";
        echo "//-->\n";
        echo "</script>\n";


} ?>
