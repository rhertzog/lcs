<?php
/* ==========================================================
   Projet LCS : Linux Communication Server
   Plugin "Biilan : Gestion administrative du B2i"
   par Jean-Louis ROSSIGNOL <jean-louis.rossignol@ac-caen.fr>   
   ========================================================== */
?>

<HTML>
<HEAD>
<TITLE>Gestion Administrative du B2I</TITLE>
</HEAD>
<?php
include "Includes/basedir.inc.php";
include "$BASEDIR/lcs/includes/headerauth.inc.php";
include "$BASEDIR/Annu/includes/ihm.inc.php";
 list ($idpers,$username) = isauth();
  if (is_admin("Biilan2_is_admin",$username)=="Y") {$menu = "mnadmin.php";}
  else
  			{
  			list ($idpers,$login) = isauth();
    		$login=strtolower($login); 
    		if ($idpers == "0") echo "<P class=\"alerte\">Vous n'êtes pas authentifié sur le LCS !</P>\n";
    		else {
    					if (is_eleve($login)=="true")
        					{$menu = "mneleve.php";}
        				else { $menu = "mnprof.php";}
			}			}
   
?>
<FRAMESET COLS="260,*" frameborder="no" border="0" framespacing="0">
<FRAME SRC="<?php print "$menu"; ?>" NAME="menu" SCROLLING=NO MARGINWIDTH=0 MARGINHEIGHT=0 FRAMEBORDER=NO>
<FRAME SRC="vide.html" NAME="main" SCROLLING=AUTO MARGINWIDTH=0 MARGINHEIGHT=0 FRAMEBORDER=NO>
</FRAMESET>

</HTML>
