<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>{$labels.lp_title}</title>
<link rel="stylesheet" type="text/css" href="css/default/stdtheme.css" />
<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript">
	
	var noLoginOrPasswordLabel = '{$labels.lp_error_no_login_or_pass}';
	
	{literal}
		function postIt() {
			var myForm = document.getElementById("logonForm");
			
			if (myForm.login.value == '' || myForm.password.value == '') {
				alert(noLoginOrPasswordLabel);
				return false;
			}
			
			return true;
		}
	{/literal}
</script>
</head>
<body 	leftmargin="0" 
	rightmargin="0" 
	topmargin="0" 
	bottommargin="0" 
	marginheight="0" 
	marginwidth="0" 
	class="bodyBackgroud">
<div id="contentMainFrame" align="center">
	
	<div style="margin-bottom:100px;margin-top:20px;"><img src="pict/OFB-logo-120.png"/> <span id="title">Online File Browser</span></div>
	
	{if $result eq "INVALID_CREDENTIALS"}
	    <div style="margin-bottom:20px;margin-top:20px;font-size:12px;font-weight:bold;color:#FF0000;">{$labels.lp_error_invalid_cred}</div> 
	{/if}
	
	<form name="logonForm" id="logonForm" ACTION="" METHOD="POST" onSubmit="return postIt()";>
		<table width="300" style="background-color: #FCFCFC;" align="center">
			<tr>
				<td align="left">{$labels.lp_login}</td>
				<td align="left"><input NAME="login" TYPE="text" style="width:110px;" class="textfield"></td>
			</tr>
			<tr>
				<td align="left">{$labels.lp_pass}</td>
				<td align="left"><input NAME="password" TYPE="password" style="width:110px;" class="textfield"></td>
			</tr>
			<tr>
				<td colspan="2" align="right">
					<input type=submit value="{$labels.lp_button_logon}" class="submit">
				</td>
			</tr>
		</table>
	</form>
</div>
</body>
</html>
