<?php
/* createmaildir.php */
include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");

list ($idpers,$login)= isauth();
if ($idpers == "0") header("Location:$urlauth");

$header="<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n
<html>\n
	<head>\n
		<title>Interface d'administration LCS</title>\n
		<link  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n
        <script language = 'javascript' type = 'text/javascript' src = 'includes/check.inc.js'></script>\n
        <script type='text/javascript' src='../libjs/jquery/jquery.js'></script>\n
	</head>\n
<body>\n";
echo $header;          

if (is_admin("lcs_is_admin",$login)=="Y") {

	$html="<style>\n";
	$html.="#main {height:400px}\n";
	$html.="#wait.vno {display:none;}\n";
	$html.="span#info {font-style:italic;font-size:0.8em;color:orange}\n";
	$html.="#info.vno {display:none;}\n";
	$html.="#CR {text-align:center}\n";
	$html.="</style>\n";
			
	$html.="<div id='main'>\n"; 
	$html.="<h3>Cr&eacute;ation des r&eacute;pertoires <tt class='computeroutput'>Maildir</tt> de <b>TOUS</b> les utilisateurs <span id='info'>Veuillez patienter...</span></h3>\n";
	$html.="<h4 id='wait' style='text-align:center'><img src='Images/wait.gif' /></h4>\n";
    $html.="<h4 id='CR'></h4>\n";
    $html.="</div>\n";
    

?>
<script language='JavaScript' type='text/javascript'>
// <![CDATA[
		$.ajax({
                    type: 'POST',
                    url : 'createmaildirJQ.php',
                    async: true,
                    dataType: 'text', 
  					complete: function(data) {
    					$('#CR').html(data.responseText);
    					$('#wait').addClass('vno');
    					$('#info').addClass('vno');
  					},			
  					error: function() {
                        alert('Echec creation Maildir');
                    }
         });
//]]>
</script>

<?php
	
} else 
	$html = "<div class=error_msg>Cette fonctionnalit&#233;, n&#233;cessite les droits d'administrateur du serveur LCS !</div>";
	
echo $html;
include ("../lcs/includes/pieds_de_page.inc.php");
?>


