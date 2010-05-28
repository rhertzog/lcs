<?php
/* lcs/barre.php derniere mise a jour : 28/05/2010 */

// HelpDesk Ajout variable session userHD pour memoriser choix user helpdesk autre qu'admin
session_start();
$_SESSION['userHD'] = null;

require "includes/headerauth.inc.php";
require "../Annu/includes/ldap.inc.php";
require "../Annu/includes/ihm.inc.php";

$result=@mysql_db_query("$DBAUTH","SELECT * from applis ", $authlink);
if ($result)
    while ($r=mysql_fetch_array($result))
        if ($r["type"]!='')  
            $$r["name"]=$r["type"];
        else
            $$r["name"]=$r["value"];
else
    die ("param&#232;tres absents de la base de donn&#233;es");
mysql_free_result($result);

$path ="barre1";

list ($idpers, $login)= isauth();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>...::: Barre LCS 2.0 :::...</title>
  <meta HTTP-EQUIV="Content-Type" CONTENT="tetx/html; charset=ISO-8859-1">
  <script type="text/javascript" src="/lib/js/prototype.js"></script>
<style type="text/css">
body	{
	margin-right: 6%;
}
#entete{
	height:100px;
	background: url('images/barre1/bandeau_lcs.gif');
	background-repeat: no-repeat;
}
#blocleft{
	float: left;
	width: 100%;
}
.logo{
	float: left;
	margin-left:10px;
	width: 120px;
}
.bouton{
	float: left;
	padding-top: 5px;
	text-align: center;
	width: 70px;
	height: 90px;
}

.deconnect{
	float: right;
	padding-top: 65px;
	text-align: center;
	width: 70px;
	height: 90px;
}

.connexion{
	float: right;
	width: 70px;
	height: 90px;
	padding-top: 50px;
}
	
.login {
  font-size: small;
  font-weight: bold;
  text-align: right;
  color:#1A2B63;
  font-family: arial, helvetica, sans-serif;
}
</style>
<script type="text/javascript">
<!--
function MM_findObj(n, d) { //v3.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
}
function MM_nbGroup(event, grpName) { //v3.0
  var i,img,nbArr,args=MM_nbGroup.arguments;
  if (event == "init" && args.length > 2) {
    if ((img = MM_findObj(args[2])) != null && !img.MM_init) {
      img.MM_init = true; img.MM_up = args[3]; img.MM_dn = img.src;
      if ((nbArr = document[grpName]) == null) nbArr = document[grpName] = new Array();
      nbArr[nbArr.length] = img;
      for (i=4; i < args.length-1; i+=2) if ((img = MM_findObj(args[i])) != null) {
        if (!img.MM_up) img.MM_up = img.src;
        img.src = img.MM_dn = args[i+1];
        nbArr[nbArr.length] = img;
    } }
  } else if (event == "over") {
    document.MM_nbOver = nbArr = new Array();
    for (i=1; i < args.length-1; i+=3) if ((img = MM_findObj(args[i])) != null) {
      if (!img.MM_up) img.MM_up = img.src;
      img.src = (img.MM_dn && args[i+2]) ? args[i+2] : args[i+1];
      nbArr[nbArr.length] = img;
    }
  } else if (event == "out" ) {
    for (i=0; i < document.MM_nbOver.length; i++) {
      img = document.MM_nbOver[i]; img.src = (img.MM_dn) ? img.MM_dn : img.MM_up; }
  } else if (event == "down") {
    if ((nbArr = document[grpName]) != null)
      for (i=0; i < nbArr.length; i++) { img=nbArr[i]; img.src = img.MM_up; img.MM_dn = 0; }
    document[grpName] = nbArr = new Array();
    for (i=2; i < args.length-1; i+=2) if ((img = MM_findObj(args[i])) != null) {
      if (!img.MM_up) img.MM_up = img.src;
      img.src = img.MM_dn = args[i+1];
      nbArr[nbArr.length] = img;
  } }
}

function MM_preloadImages() { //v3.0
 var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
   var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
   if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
//-->
</script>
</head>

<body bgcolor="#f8f8ff" onLoad="MM_preloadImages('images/<? echo $path ?>/BP_r1_c3_f2.gif','images/<? echo $path ?>/BP_r1_c3_f3.gif','images/<? echo $path ?>/BP_r1_c4_f2.gif','images/<? echo $path ?>/BP_r1_c4_f3.gif','images/<? echo $path ?>/BP_r1_c5_f2.gif','images/<? echo $path ?>/BP_r1_c5_f3.gif','images/<? echo $path ?>/BP_r1_c6_f2.gif','images/<? echo $path ?>/BP_r1_c6_f3.gif','images/<? echo $path ?>/BP_r1_c7_f2.gif','images/<? echo $path ?>/BP_r1_c7_f3.gif','images/<? echo $path ?>/BP_r1_c8_f2.gif','images/<? echo $path ?>/BP_r1_c8_f3.gif','images/<? echo $path ?>/BP_r1_c10_f2.gif','images/<? echo $path ?>/BP_r1_c10_f3.gif');">


<?php
// Cas service
   if ( $login && ($lcs_cas == 1) && !isset($_COOKIE['tgt'])) {

	echo "<script type='text/javascript'>
        // <![CDATA[
        new Ajax.Request('/lcs/includes/log2cas_ajax.php',{ method: 'post', onSuccess: function(requester){
           if (requester.responseText)
		alert(requester.responseText);
	}});

        //]]>
        </script>\n";
  }
?>

<div id="entete">
    <div id="blocleft">
    <!-- Logo LCS -->
        <div class="logo">
   	    <a href="<?php echo $url_logo ?>" target="principale">
		<img name="BP_r1_c1" title="Documentation LCS" alt="Documentation LCS" src="images/<?php echo $path ?>/BP_r1_c1.gif" border="0">   		
	   </a>
        </div>      
        <!-- Espace -->
        <div class="bouton"></div>
        <!-- Mon LCS -->
<?php
if ( $idpers==0 ) {
 	// Un utilisateur n'est pas authentifie  
   	echo "<div class=\"bouton\"><a href=\"auth.php\" target=\"principale\" onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c3','images/$path/BP_r1_c3_f2.gif','images/$path/BP_r1_c3_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c3','images/$path/BP_r1_c3_f3.gif',1);\" ><img name=\"BP_r1_c3\" title=\"Authentification\" alt=\"Authentification\" src=\"images/$path/BP_r1_c3.gif\"  border=\"0\"></a></div>\n";
   	echo "<div class=\"bouton\"><a href=\"statandgo.php?use=Aide\" target=\"principale\" onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c8','images/$path/BP_r1_c8_f2.gif','images/$path/BP_r1_c8_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c8','images/$path/BP_r1_c8_f3.gif',1);\" ><img name=\"BP_r1_c8\" title=\"Aide\" alt=\"Aide\" src=\"images/$path/BP_r1_c8.gif\"  border=\"0\"></a></div>\n";
        echo "<div class=\"bouton\"></div>\n";	
	echo "<div class=\"deconnect\"><img title=\"D&#233;connect&#233;\" alt=\"D&#233;connect&#233;\" src=\"images/deconnect.png\" width=\"26\" height=\"9\"  hspace=\"5\" align=\"bottom\" border=\"0\"></div>\n";
	
} else {
 	// Un utilisateur est authentifie
	echo "<div class=\"bouton\"><a href=\"statandgo.php?use=Accueil\" target=\"principale\" onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c3','images/$path/mon_LCS_BP_r1_c3_f2.gif','images/$path/mon_LCS_BP_r1_c3_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c3','images/$path/mon_LCS_BP_r1_c3_f3.gif',1);\" ><img name=\"BP_r1_c3\" title=\"Mon LCS\" alt=\"Mon LCS\" src=\"images/$path/mon_LCS_BP_r1_c3.gif\" border=\"0\"></a></div>\n";
   	echo "<div class=\"bouton\"><a href=\"statandgo.php?use=Applis\" target=\"principale\"  onMouseOut=\"MM_nbGroup('out');\" onMouseOver=\"MM_nbGroup('over','BP_r1_c4','images/$path/BP_r1_c4_f2.gif','images/$path/BP_r1_c4_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c4','images/$path/BP_r1_c4_f3.gif',1);\" ><img name=\"BP_r1_c4\" title=\"Applications\" alt=\"Applications\" src=\"images/$path/BP_r1_c4.gif\" border=\"0\"></a></div>\n";
	if ( isset($squirrelmail) ) // Webmail
            echo "<div class=\"bouton\"><a href=\"statandgo.php?use=squirrelmail\" target=\"principale\" onMouseOut=\"MM_nbGroup('out');\" onMouseOver=\"MM_nbGroup('over','BP_r1_c5','images/$path/BP_r1_c5_f2.gif','images/$path/BP_r1_c5_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c5','images/$path/BP_r1_c5_f3.gif',1);\" ><img name=\"BP_r1_c5\" title=\"Webmail\" alt=\"Webmail\"  src=\"images/$path/BP_r1_c5.gif\" border=\"0\"></a></div>\n";
	if ( isset($spip) ) // CMS
            echo "<div class=\"bouton\"><a href=\"statandgo.php?use=spip\" target=\"principale\" onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c6','images/$path/BP_r1_c6_f2.gif','images/$path/BP_r1_c6_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c6','images/$path/BP_r1_c6_f3.gif',1);\" ><img name=\"BP_r1_c6\" title=\"Forum\" alt=\"Forum\" src=\"images/$path/BP_r1_c6.gif\" border=\"0\"></a></div>\n";
        if ( acces_btn_admin($idpers, $login) =="Y") // acces au menu d'administration
   	    echo "<div class=\"bouton\"><a href=\"statandgo.php?use=Admin\" target=\"principale\"onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c7','images/$path/BP_r1_c7_f2.gif','images/$path/BP_r1_c7_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c7','images/$path/BP_r1_c7_f3.gif',1);\" ><img name=\"BP_r1_c7\" title=\"Administration\" alt=\"Administration\" src=\"images/$path/BP_r1_c7.gif\" border=\"0\"></a></div>\n";
	// Aide
   	echo "<div class=\"bouton\"><a href=\"statandgo.php?use=Aide\" target=\"principale\"  onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c8','images/$path/BP_r1_c8_f2.gif','images/$path/BP_r1_c8_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c8','images/$path/BP_r1_c8_f3.gif',1);\" ><img name=\"BP_r1_c8\" title=\"Aide\" alt=\"Aide\" src=\"images/$path/BP_r1_c8.gif\" border=\"0\"></a></div>\n";
        // HelpDesk
	if ( isset($helpdesk) && ( (is_admin('Lcs_is_admin', $login) == "Y")  || (is_admin('System_is_admin', $login) == "Y") ) ) 
   	    echo "<div class=\"bouton\"><a href=\"/helpdesk/\" target=\"principale\" onMouseOut=\"MM_nbGroup('out');\" onMouseOver=\"MM_nbGroup('over','BP_r1_c11','images/$path/helpdesk-surv.gif','images/$path/helpdesk-on.gif',1);\" onClick=\"MM_nbGroup('down','navbar1','BP_r1_c11','images/$path/helpdesk-on.gif',1);\" ><img name=\"BP_r1_c11\" title=\"Helpdesk\" alt=\"Helpdesk\" src=\"images/$path/helpdesk-off.gif\" border=\"0\"></a></div>\n";	
	// Case vierge
   	echo "<div class=\"bouton\"></div>\n";
   	// Deconnection LCS
   	echo "<div class=\"bouton\"><a href=\"logout.php\" onMouseOut=\"MM_nbGroup('out');\" onMouseOver=\"MM_nbGroup('over','BP_r1_c10','images/$path/BP_r1_c10_f2.gif','images/$path/BP_r1_c10_f3.gif',1);\" onClick=\"MM_nbGroup('down','navbar1','BP_r1_c10','images/$path/BP_r1_c10_f3.gif',1);\" ><img name=\"BP_r1_c10\" title=\"D&#233;connection\" alt=\"D&#233;connection\" src=\"images/$path/BP_r1_c10.gif\" border=\"0\"></a></div>\n";
        // Info connect
	echo "<div class=\"connexion\"><img src=\"images/connect.png\" alt=\"Login\" width=\"26\" height=\"9\"  hspace=\"5\" align=\"bottom\" border=\"0\"><font class=\"login\">$login</font></div>\n";	
}
?>
    </div><!-- Fin blockleft -->
</div><!-- Fin entete -->
</body>
</html>
