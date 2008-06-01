<?php
/* lcs/barre.php derniere mise a jour : 29/01/2008 «Barre a Mine» powered by guillaume.marquis@crdp.ac-caen.fr */
require "includes/headerauth.inc.php";
require "../Annu/includes/ldap.inc.php";
require "../Annu/includes/ihm.inc.php";

$result=@mysql_db_query("$DBAUTH","SELECT * from applis", $authlink);
if ($result)
    while ($r=mysql_fetch_array($result))
                $$r["name"]=$r["value"];
else
    die ("paramètres absents de la base de données");
mysql_free_result($result);

if ( $barre1 == 1 ) $path ="barre1"; else $path ="barre2";

list ($idpers, $login)= isauth();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>...::: Barre LCS V1.4 :::...</title>
  <meta HTTP-EQUIV="Content-Type" CONTENT="tetx/html; charset=ISO-8859-1">
<style type="text/css">
body	{
	margin-right: 6%;
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
<table border="0" cellpadding="0" cellspacing="0" width="100%">
   <tr>
   <!-- Logo LCS -->
   <td width="153">
   	<a href="<? echo $url_logo ?>" target="principale">
		<img name="BP_r1_c1" title="Documentation LCS" alt="Documentation LCS" src="images/<? echo $path ?>/BP_r1_c1.gif" width="153" height="86" border="0">   		
	</a>
   </td>      
   <!-- Espace -->
   <td><img name="BP_r1_c2" title="Espace perso" alt="Espace perso" src="images/<? echo $path ?>/BP_r1_c2.gif" width="68" height="86" border="0"></td>
   <!-- Mon LCS -->
<?
if ( $idpers==0 ) {
 	// Un utilisateur n'est pas authentifié  
   	echo "<td><a href=\"statandgo.php?use=Accueil\" target=\"principale\" onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c3','images/$path/BP_r1_c3_f2.gif','images/$path/BP_r1_c3_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c3','images/$path/BP_r1_c3_f3.gif',1);\" ><img name=\"BP_r1_c3\" title=\"Authentification\" alt=\"Authentification\" src=\"images/$path/BP_r1_c3.gif\" width=\"69\" height=\"86\" border=\"0\"></a></td>\n";
   	echo "<td><a href=\"statandgo.php?use=Aide\" target=\"principale\" onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c8','images/$path/BP_r1_c8_f2.gif','images/$path/BP_r1_c8_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c8','images/$path/BP_r1_c8_f3.gif',1);\" ><img name=\"BP_r1_c8\" title=\"Aide\" alt=\"Aide\" src=\"images/$path/BP_r1_c8.gif\" width=\"68\" height=\"86\" border=\"0\"></a></td>\n";
        echo "<td><img name=\"BP_r1_c2\" alt=\"separateur\" src=\"images/$path/BP_r1_c2.gif\" width=\"408\" height=\"86\" border=\"0\"></td>\n";	
	echo "<td width=\"50%\" valign=\"bottom\" align=\"right\"><img title=\"Déconnecté\" alt=\"Déconnecté\" src=\"images/deconnect.png\" width=\"26\" height=\"9\"  hspace=\"5\" align=\"bottom\" border=\"0\"></td>\n";
	
} else {
 	// Un utilisateur est authentifié
	echo "<td><a href=\"statandgo.php?use=Accueil\" target=\"principale\" onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c3','images/$path/mon_LCS_BP_r1_c3_f2.gif','images/$path/mon_LCS_BP_r1_c3_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c3','images/$path/mon_LCS_BP_r1_c3_f3.gif',1);\" ><img name=\"BP_r1_c3\" title=\"Mon LCS\" alt=\"Mon LCS\" src=\"images/$path/mon_LCS_BP_r1_c3.gif\" width=\"69\" height=\"86\" border=\"0\"></a></td>\n";
   	echo "<td><a href=\"statandgo.php?use=Applis\" target=\"principale\"  onMouseOut=\"MM_nbGroup('out');\" onMouseOver=\"MM_nbGroup('over','BP_r1_c4','images/$path/BP_r1_c4_f2.gif','images/$path/BP_r1_c4_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c4','images/$path/BP_r1_c4_f3.gif',1);\" ><img name=\"BP_r1_c4\" title=\"Applications\" alt=\"Applications\" src=\"images/$path/BP_r1_c4.gif\" width=\"69\" height=\"86\" border=\"0\"></a></td>\n";
	if ( $squirrelmail==1 ) // Webmail
   		echo "<td><a href=\"statandgo.php?use=squirrelmail\" target=\"principale\" onMouseOut=\"MM_nbGroup('out');\" onMouseOver=\"MM_nbGroup('over','BP_r1_c5','images/$path/BP_r1_c5_f2.gif','images/$path/BP_r1_c5_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c5','images/$path/BP_r1_c5_f3.gif',1);\" ><img name=\"BP_r1_c5\" title=\"Webmail\" alt=\"Webmail\"  src=\"images/$path/BP_r1_c5.gif\" width=\"68\" height=\"86\" border=\"0\"></a></td>\n";
	if ($phpweblog==1) // weblog	
   		echo "<td><a href=\"statandgo.php?use=Forum\" target=\"principale\" onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c6','images/$path/BP_r1_c6_f2.gif','images/$path/BP_r1_c6_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c6','images/$path/BP_r1_c6_f3.gif',1);\" ><img name=\"BP_r1_c6\" title=\"Forum\" alt=\"Forum\"  src=\"images/$path/BP_r1_c6.gif\" width=\"68\" height=\"86\" border=\"0\"></a></td>\n";
	elseif ($spip==1) // CMS
   		echo "<td><a href=\"statandgo.php?use=spip\" target=\"principale\" onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c6','images/$path/BP_r1_c6_f2.gif','images/$path/BP_r1_c6_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c6','images/$path/BP_r1_c6_f3.gif',1);\" ><img name=\"BP_r1_c6\" title=\"Forum\" alt=\"Forum\" src=\"images/$path/BP_r1_c6.gif\" width=\"68\" height=\"86\" border=\"0\"></a></td>\n";	
        if ( is_admin("Lcs_is_admin",$login) == "Y" ) // acces au menu d'administration	
   	echo "<td><a href=\"statandgo.php?use=Admin\" target=\"principale\"onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c7','images/$path/BP_r1_c7_f2.gif','images/$path/BP_r1_c7_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c7','images/$path/BP_r1_c7_f3.gif',1);\" ><img name=\"BP_r1_c7\" title=\"Administration\" alt=\"Administration\" src=\"images/$path/BP_r1_c7.gif\" width=\"69\" height=\"86\" border=\"0\"></a></td>\n";
	// Aide
   	echo "<td><a href=\"statandgo.php?use=Aide\" target=\"principale\"  onMouseOut=\"MM_nbGroup('out');\"  onMouseOver=\"MM_nbGroup('over','BP_r1_c8','images/$path/BP_r1_c8_f2.gif','images/$path/BP_r1_c8_f3.gif',1);\"  onClick=\"MM_nbGroup('down','navbar1','BP_r1_c8','images/$path/BP_r1_c8_f3.gif',1);\" ><img name=\"BP_r1_c8\" title=\"Aide\" alt=\"Aide\" src=\"images/$path/BP_r1_c8.gif\" width=\"68\" height=\"86\" border=\"0\"></a></td>\n";
	// Case vierge
   	echo "<td><img name=\"BP_r1_c9\" alt=\"séparateur\"src=\"images/$path/BP_r1_c9.gif\" width=\"70\" height=\"86\" border=\"0\"></td>\n";
   	// Deconnexion LCS
   	echo "<td><a href=\"logout.php\" onMouseOut=\"MM_nbGroup('out');\" onMouseOver=\"MM_nbGroup('over','BP_r1_c10','images/$path/BP_r1_c10_f2.gif','images/$path/BP_r1_c10_f3.gif',1);\" onClick=\"MM_nbGroup('down','navbar1','BP_r1_c10','images/$path/BP_r1_c10_f3.gif',1);\" ><img name=\"BP_r1_c10\" title=\"Déconnexion\" alt=\"Déconnexion\" src=\"images/$path/BP_r1_c10.gif\" width=\"68\" height=\"86\" border=\"0\"></a></td>\n";
	echo "<td width=\"50%\" valign=\"bottom\" align=\"right\"><img src=\"images/connect.png\" alt=\"Login\" width=\"26\" height=\"9\"  hspace=\"5\" align=\"bottom\" border=\"0\"><font class=\"login\">$login</font></td>\n";	
}
?>
  </tr>
</table>
</body>
</html>