<?php

include ("../lcs/includes/headerauth.inc.php");
include ("../Annu/includes/ldap.inc.php");
include ("../Annu/includes/ihm.inc.php");
include ("../lcs/includes/menu.inc.php");

list ($idpers, $login)= isauth();

if ($idpers == "0") header("Location:$urlauth");
?>
<HTML>
<HEAD>
<title>Interface d'administration LCS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="style/menu.css">
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

// image change on mouse over
a1=new Image(20,12)
a1.src="../lcs/images/menu/up.png"
a2=new Image(20,12)
a2.src="../lcs/images/menu/up_over.png"

a3=new Image(20,12)
a3.src="../lcs/images/menu/down.png"
a4=new Image(20,12)
a4.src="../lcs/images/menu/down_over.png"

function filter(imagename,objectsrc){
if (document.images)
document.images[imagename].src=eval(objectsrc+".src")
}

//-->
</script>
</head>
<?php

function menuprint($login)
{
    global $liens,$menu;

    for ($idmenu=0; $idmenu<count($liens); $idmenu++)
    {
        echo "<div id=\"menu$idmenu\" style=\"position:absolute; left:10px; top:12px; width:200px; z-index:" . $idmenu ." ";
        if ($idmenu!=$menu) {
            echo "; visibility: hidden";
        }
        echo "\">\n";

        echo "
        <table width=\"200\" border=\"0\" cellspacing=\"3\" cellpadding=\"6\">\n";
		$ldapright["lcs_is_admin"]=ldap_get_right("lcs_is_admin",$login);

        for ($menunbr=1; $menunbr<count($liens); $menunbr++)
        {
		// Test des droits pour affichage
			$afftest=$ldapright["lcs_is_admin"]=="Y";
			$rightname=$liens[$menunbr][1];
			if (($rightname=="") or ($afftest)) $afftest=1==1;
			else {
				if ($ldapright[$rightname]=="") $ldapright[$rightname]=ldap_get_right($rightname,$login);
				$afftest=($ldapright[$rightname]=="Y");
			}
			if ($afftest)
            if (($idmenu==$menunbr)&&($idmenu!=0)) {
	            echo "
                <tr>
                    <td class=\"menuheader_up\">
                        <p>
			  <a class=\"menuheader_up\" href=\"javascript:;\" 
			     onClick=\"P7_autoLayers('menu0');return false;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$liens[$menunbr][0]."
			  </a>
			</p>
                    </td>		    
                    </tr>
                    <tr>
                    <td class=\"menucell\">";
                for ($i=2; $i<count($liens[$menunbr]); $i+=3) {
			// Test des droits pour affichage
					$afftest=$ldapright["lcs_is_admin"]=="Y";
					$rightname=$liens[$menunbr][$i+2];
					if (($rightname=="") or ($afftest)) $afftest=1==1;
					else {
						if ($ldapright[$rightname]=="") $ldapright[$rightname]=ldap_get_right($rightname,$login);
						$afftest=($ldapright[$rightname]=="Y");
					}
					if ($afftest)
                    echo "
                        <img src=\"../lcs/images/menu/typebullet.gif\" width=\"30\" height=\"11\">
                            <a href=\"" . $liens[$menunbr][$i+1] . "\" TARGET='main'>" . $liens[$menunbr][$i]  . "</a><br>\n";
                } // for i : bouche d'affichage des entrées de sous-menu
                echo "
                    </td></tr>\n";
            } else
            {
                echo "
                <tr>
                    <td class=\"menuheader_down\">
                    <p>
		      <a class=\"menuheader_down\" href=\"javascript:;\" 
			onClick=\"P7_autoLayers('menu" . $menunbr .  "');return false;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$liens[$menunbr][0]."
		      </a>
		    </p>
                    </td></tr>\n";
            }
        } //for menunbr : boucle d'affichage des entrées de menu principales

        echo "
        </table>
</div>\n";
    } // for idmenu : boucle d'affichage des différents calques
} // function menuprint


if (! isset($menu)) $menu=0;
echo "<body BGCOLOR=\"ghostwhite\" onLoad=\"P7_autoLayers('menu" . $menu ."')\">";
menuprint($login);
?>

</BODY>
</HTML>
