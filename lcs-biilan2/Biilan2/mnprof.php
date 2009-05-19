<HTML>
<HEAD>
<title>menu professeur</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="Style/style.css">
<script language="JavaScript">

<!--
function MM_reloadPage(init)
{
//reloads the window if Nav4 resized
    if (init==true) with (navigator) {
        if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
            document.MM_pgW=innerWidth;
            document.MM_pgH=innerHeight;
            onresize=MM_reloadPage;
        }
    }
    else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH)
        location.reload();
}

MM_reloadPage(true);

function MM_findObj(n, d)
{
//v4.01
    var p,i,x;
    if(!d) d=document;
    if((p=n.indexOf("?"))>0&&parent.frames.length) {
        d=parent.frames[n.substring(p+1)].document;
        n=n.substring(0,p);
    }
    if(!(x=d[n])&&d.all) x=d.all[n];
    for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
    for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
    if(!x && d.getElementById) x=d.getElementById(n);
    return x;
}

function P7_autoLayers()
{
//v1.1 PVII
    var g,b,k,f,args=P7_autoLayers.arguments;
    if(!document.p7setc) {
        p7c=new Array();
        document.p7setc=true;
    }
    for(k=0; k<p7c.length; k++) {
        if((g=MM_findObj(p7c[k]))!=null) {
            b=(document.layers)?g:g.style;
            b.visibility="hidden";
        }
    }
    for(k=0; k<args.length; k++) {
        if((g=MM_findObj(args[k])) != null) {
            b=(document.layers)?g:g.style;
            b.visibility="visible";
            f=false;
            for(j=0;j<p7c.length;j++) {
                if(args[k]==p7c[j]) {f=true;}
            }
            if(!f) {p7c[p7c.length++]=args[k];}
        }
    }
}
//-->
</script>
</head>
<body BGCOLOR="ghostwhite" onLoad="P7_autoLayers('menu0')">

<div id="menu0" style="position:absolute; left:10px; top:13px; width:200px; z-index:0 ">

        <table width="250" border="0" cellspacing="3" cellpadding="6">

                <tr>
                    <td class="menuheader">
                    <p><a href="vide.html" target='main'>BiiLan</a></p>
                    </td></tr>
				
				        <tr>
                    <td class="menuheader">
                    <p><a href="javascript:;" onClick="P7_autoLayers('menu2');return false">
                    <img src="Images/menu/down.gif" width="20" height="12" border="0">Espace Professeur</a></p>
                    </td></tr>

                <tr>
                    <td class="menuheader">
                        <p><a href="javascript:;" onClick="P7_autoLayers('menu4');return false">
                        <img src="Images/menu/down.gif" width="20" height="12" border="0">Espace Commun</a></p>
                    </td></tr>


        </table>
</div>


<div id="menu2" style="position:absolute; left:10px; top:12px; width:200px; z-index:1 ; visibility: hidden">

        <table width="250" border="0" cellspacing="3" cellpadding="6">

                <tr>
                    <td class="menuheader">
                    <p><a href="vide.html" target='main'>BiiLan</a></p>
                    </td></tr>

                <tr>
                    <td class="menuheader">
                        <p><a href="javascript:;" onClick="P7_autoLayers('menu0');return false">
                        <img src="Images/menu/up.gif" width="20" height="12" border="0">Espace Professeur</a></p>
                    </td></tr>

                <tr>
                    <td class="menucell">
                       <img src="Images/menu/typebullet.gif" width="30" height="11">
                            <a href="p_demande.php" TARGET='main'>Demandes en attente </a><br>

                        <img src="Images/menu/typebullet.gif" width="30" height="11">
                            <a href="p_modif.php" TARGET='main'>Modifier une fiche</a><br>

                        <img src="Images/menu/typebullet.gif" width="30" height="11">
                            <a href="p_lot.php" TARGET='main'>Validation par lot</a><br>

                       <center>--------------------------</center>

                        <img src="Images/menu/typebullet.gif" width="30" height="11">
                            <a href="p_visufiche.php" TARGET='main'>Visualiser une fiche</a><br>

                        <img src="Images/menu/typebullet.gif" width="30" height="11">
                            <a href="p_visuclasse.php" TARGET='main'>Visualiser une classe</a><br>
                    </td></tr>

                <tr>
                    <td class="menuheader">
                        <p><a href="javascript:;" onClick="P7_autoLayers('menu4');return false">
                        <img src="Images/menu/up.gif" width="20" height="12" border="0">Espace Commun</a></p>
                    </td></tr>
                    
        </table>
</div>



<div id="menu4" style="position:absolute; left:10px; top:12px; width:200px; z-index:2 ; visibility: hidden">

        <table width="250" border="0" cellspacing="3" cellpadding="6">

                <tr>
                    <td class="menuheader">
                    <p><a href="vide.html" target='main'>BiiLan</a></p>
                    </td></tr>

                <tr>
                    <td class="menuheader">
                    <p><a href="javascript:;" onClick="P7_autoLayers('menu2');return false">
                    <img src="Images/menu/down.gif" width="20" height="12" border="0">Espace Professeur</a></p>
                    </td></tr>

                <tr>
                    <td class="menuheader">
                        <p><a href="javascript:;" onClick="P7_autoLayers('menu0');return false">
                        <img src="Images/menu/up.gif" width="20" height="12" border="0">Espace Commun</a></p>
                    </td></tr>

                <tr>
                    <td class="menucell">
                       <img src="Images/menu/typebullet.gif" width="30" height="11">
                            <a href="c_cndp.php" TARGET='main'>Liste des compétences</a><br>
                            
                        <img src="Images/menu/typebullet.gif" width="30" height="11">
                            <a href="c_cptval.php" TARGET='main'>Compétences validées</a><br>

                        <img src="Images/menu/typebullet.gif" width="30" height="11">
                            <a href="c_visu.php" TARGET='main'>Avancement</a><br>
                    </td></tr>

        </table>
</div>


</BODY>
</HTML>
