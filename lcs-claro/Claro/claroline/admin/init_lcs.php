<?php /* ==================================================
   Projet LCS : Linux Communication Server
   Plugin "Claroline"
   par philippe LECLERC
   philippe.leclerc1@ac-caen.fr
   - script d'initialisation depuis le LDAP-
			_-=-_
   =================================================== */
define('DISP_REGISTRATION_SUCCEED','DISP_REGISTRATION_SUCCEED');
define('DISP_REGISTRATION_FORM','DISP_REGISTRATION_FORM');
$cidReset = TRUE;
$gidReset = TRUE;
$tidReset = TRUE;
require '../inc/claro_init_global.inc.php';

// Security Check
if ( ! claro_is_user_authenticated() ) claro_disp_auth_form();
if ( ! claro_is_platform_admin() ) claro_die(get_lang('Not allowed'));

// Include library
require claro_get_conf_repository() . 'user_profile.conf.php';

require_once get_path('incRepositorySys') . '/lib/user.lib.php';
require_once get_path('incRepositorySys') . '/lib/sendmail.lib.php';

// Initialise variables
$nameTools = 'Initialisation de la plate-forme via  l\'annuaire du LCS';
$error = false;
$messageList = array();
$display = DISP_REGISTRATION_FORM;

//fichiers nécessaires à l'exploitation de l'API
	$BASEDIR="/var/www";
	//include "../Includes/basedir.inc.php";
	include "$BASEDIR/lcs/includes/headerauth.inc.php";
	include "$BASEDIR/Annu/includes/ldap.inc.php";
	include "$BASEDIR/Annu/includes/ihm.inc.php";  
	
/*=====================================================================
  my fonctions
 =====================================================================*/
 function is_prof ($login) {
        global $ldap_server, $ldap_port, $dn;
        global $error;
        $error="";

        $filter = "(&(cn=profs*)(memberUid=$login))";
        $ldap_groups_attr = array (
        // LDAP attribute
                "cn",
                "memberUid"    // Membre du Group Profs, Eleves, Administration
        );

        /*-----------------------------------------------------*/
        $ds = @ldap_connect ( $ldap_server, $ldap_port );
        if ( $ds ) {
                $r = @ldap_bind ( $ds );
                if (!$r) {
                        $error = "Echec du bind anonyme";
                } else {
                        // Recherche du groupe d'appartenance de l'utilisateur connecté
                        $result=@ldap_list ($ds, $dn["groups"], $filter, $ldap_groups_attr);
                        if ($result) {
                                $info = @ldap_get_entries( $ds, $result );
                                if ($info["count"]) {
                                        $is_prof = true;
                                } else {
                                        $is_prof = false;
                                }
                        }
                }
        }
        @ldap_unbind ($ds);
        @ldap_close ($ds);
        return $is_prof;
 }
 
function is_present($login) {
	$Sql= "SELECT user_id FROM cl_user WHERE  username='$login' ";
	$res = @mysql_query ($Sql) or die (mysql_error());
	$tst=mysql_fetch_array($res, MYSQL_NUM);
	if (mysql_num_rows($res)>0) return $tst[0]; else return false;	

}

function add_eleve($name,$firstname,$log){
	global $domain;
	$mail=$log."@".$domain;
	$Sql ="INSERT INTO cl_user (nom,prenom,username,authSource,email,creatorId ) 
					VALUES ( '$name','$firstname', '$log', 'CAS', '$mail','1')";
	$res = @mysql_query ($Sql) or die (mysql_error());
	if ($res) return true; else return false;
}

function add_prof($name,$firstname,$log){
 	global $domain;
	$mail=$log."@".$domain;
	$Sql ="INSERT INTO cl_user (nom,prenom,username,authSource,email,isCourseCreator,creatorId ) 
					VALUES ( '$name','$firstname', '$log', 'CAS', '$mail', '1', '1')";
	$res = @mysql_query ($Sql) or die (mysql_error());
	if ($res) return true; else return false;
}

function user_in_classe($id_cl,$id_el){
	$Sql ="INSERT INTO cl_rel_class_user (class_id,user_id) 
	VALUES ( '$id_cl','$id_el')";
	$res = @mysql_query ($Sql) or die (mysql_error());
	if ($res) return true; else return false;
}

if ( isset($_REQUEST['cmd']) ) $cmd = $_REQUEST['cmd'];
else                           $cmd = '';



/*=====================================================================
  Display Section
 =====================================================================*/

$interbredcrump[] = array ('url' => get_path('rootAdminWeb'), 'name' => get_lang('Administration'));
$noQUERY_STRING   = TRUE;

// Display Header

include get_path('incRepositorySys') . '/claro_init_header.inc.php';

// Display title


echo claro_html_tool_title( array('mainTitle'=>$nameTools ) )
.    claro_html_msg_list($messageList)
;

if ( $display == DISP_REGISTRATION_SUCCEED )
{
    echo claro_html_menu_vertical($newUserMenu);
}
else // $display == DISP_REGISTRATION_FORM;

{
    //  if registration failed display error message

    echo 'L\'initialisation : <br />
    <ul>
    <li> supprime les classes existantes : <span id="cr0" class="message"></span></li>
    <li> supprime l\'inscription des classes aux cours : <span id="cr1"class="message"></span></li>
    <li> supprime les &#233;l&#232;ves qui ne sont plus dans l\'annuaire : <span id="cr6"class="message"></span></li>
    <li> supprime les profs qui ne sont plus dans l\'annuaire : <span id="cr7"class="message"></span></li>
    <li> d&#233;clare "orphelins" les cours des profs supprim&#233;s : <span id="cr9"class="message"></span></li>
    <li> cr&#233;e les classes pr&#233;sentes dans l\'annuaire : <span id="cr2"class="message"></span></li>
    <li> affecte les &#233;l&#232;ves &#224; leur classe : <span id="cr3" class="message"></span></li>
    <li> cr&#233;e une "classe" Prof  : <span id="cr4" class="message"></span></li>
    <li> affecte les profs &#224; la classe Profs : <span id="cr5" class="message"></span></li>';
    
    if ( $cmd == '' )
	{
    $html = '<form action="' . $_SERVER['PHP_SELF'] . '" method="post"  >' . "\n"
    .       claro_form_relay_context().       form_input_hidden('cmd', 'registration');
    $html .= ' <br /><input type="submit"   value="' . get_lang('Ok') . '" />&nbsp;'
        . claro_html_button($_SERVER['HTTP_REFERER'], get_lang('Cancel')) ;
    echo $html.'</form>' . "\n";
    }    
}
/*=====================================================================
  Main Section
 =====================================================================*/


if ( $cmd == 'registration' )
{
$db = @mysql_connect($dbHost, $dbLogin, $dbPass, false, CLIENT_FOUND_ROWS) 
		  			or die ('<center>' .'WARNING ! SYSTEM UNABLE TO CONNECT TO THE DATABASE SERVER.' .'</center>');	
/*=====================================================================
  Suppression des classes
 =====================================================================*/
$Sql ="TRUNCATE TABLE `cl_class` ";
$res = @mysql_query ($Sql) or die (mysql_error());
echo '<script type="text/javascript" language="JavaScript">
document.getElementById("cr0").innerHTML = "  OK " ;
</script>';
/*=====================================================================
  Suppression des relations classes -> users
 =====================================================================*/
$Sql ="TRUNCATE TABLE `cl_rel_class_user` ";
$res = @mysql_query ($Sql) or die (mysql_error());

/*=====================================================================
  Suppression des relations Cours --> classes 
 =====================================================================*/
$Sql ="TRUNCATE TABLE `cl_rel_course_class` ";
$res = @mysql_query ($Sql) or die (mysql_error());
echo '<script type="text/javascript" language="JavaScript">
document.getElementById("cr1").innerHTML = " OK " ;
</script>';
/*=====================================================================
  Suppression des eleves absents du ldap
 =====================================================================*/ 
//recherche de la liste des eleves
$Sql= "SELECT username FROM cl_user WHERE  isCourseCreator='0' and isPlatformAdmin ='0'";
	$res = @mysql_query ($Sql) or die (mysql_error());
	//$tst=mysql_fetch_array($res, MYSQL_NUM);
	if (mysql_num_rows($res)>0) 
	$nb='0';$cpt4='0';
	while ($enrg = mysql_fetch_array($res, MYSQL_NUM)) 
		{
		if (!(is_eleve($enrg[0]))) {
			$eleves_partis[$nb]=$enrg[0];
			$nb++;
			}
		}
		
		if (count($eleves_partis))
			{
			for ($loop=0; $loop < count($eleves_partis); $loop++)
			     {
			     $Sql="DELETE FROM `cl_user` WHERE `username` = '{$eleves_partis[$loop]}' LIMIT 1";
				 $res = @mysql_query ($Sql) or die (mysql_error());
				 if ($res) {
				 $cpt4++;
				 echo '<script type="text/javascript" language="JavaScript">
				 document.getElementById("cr6").innerHTML = "   '.$cpt4.'  &#233;l&#232;ves supprim&#233;s" ;
				 </script>';
				 }
			}
		}
		else echo '<script type="text/javascript" language="JavaScript">
				 document.getElementById("cr6").innerHTML = "  aucun &#233;l&#232;ve supprim&#233;" ;
				 </script>';
		
/*=====================================================================
  Suppression des profs absents du ldap
 =====================================================================*/ 
//recherche de la liste des profs
	$Sql= "SELECT username, prenom, nom FROM cl_user WHERE  isCourseCreator='1' and isPlatformAdmin ='0'";
	$res = @mysql_query ($Sql) or die (mysql_error());
	//$tst=mysql_fetch_array($res, MYSQL_NUM);
	if (mysql_num_rows($res)>0) 
	$nb='0';$cpt4='0';$cpt5='0';
	while ($enrg = mysql_fetch_array($res, MYSQL_NUM)) 
		{
		if (!(is_prof($enrg[0]))) {
			$profs_partis[$nb]=$enrg[0];
			$nom_prof_parti[$nb]=$enrg[1]." ".$enrg[2];
			$nb++;
			}
		}
		
		if (count($profs_partis))
			{
			for ($loop=0; $loop < count($profs_partis); $loop++)
			     {
			     $Sql="DELETE FROM `cl_user` WHERE `username` = '{$profs_partis[$loop]}' LIMIT 1";
				 $res = @mysql_query ($Sql) or die (mysql_error());
				 if ($res) {
				 $cpt4++;
				 echo '<script type="text/javascript" language="JavaScript">
				 document.getElementById("cr7").innerHTML = "   '.$cpt4.'  profs supprim&#233;s" ;
				 </script>';
				 
				 // Declaration  des cours orphelins
				 $Sql= "UPDATE `cl_cours` SET `titulaires` = 'personne' WHERE `titulaires` = '{$nom_prof_parti[$loop]}'";
				 $res1 = @mysql_query ($Sql) or die (mysql_error());
				  if ($res1) {
				 	$cpt5 += mysql_affected_rows();
				 	echo '<script type="text/javascript" language="JavaScript">
				 document.getElementById("cr9").innerHTML = "'.$cpt5.' cours d&#233;clar&#233;s orphelins" 
				 </script>';
				 }
				 
				 }
			}
		}
		else echo '<script type="text/javascript" language="JavaScript">
				 document.getElementById("cr7").innerHTML = "  aucun prof supprim&#233;" ;
				 </script>';
		

			
/*=====================================================================
  Creation des classes et peuplement
 =====================================================================*/ 
 

	$mess="";
		
		$filtre="Classe*";
		//recherche des groupes 
		$groups=search_groups('cn='.$filtre);
		$groups[count($groups)]["cn"]="Profs";
		$cpt=0; $cpt2=0;$cpt3=0;
		if (count($groups))
			{
			   
			for ($loup=0; $loup < count($groups); $loup++)
			        {
				$grp=$groups[$loup]["cn"];
				//création du groupe
				$db = @mysql_connect($dbHost, $dbLogin, $dbPass, false, CLIENT_FOUND_ROWS) 
		  		or die ('<center>' .'WARNING ! SYSTEM UNABLE TO CONNECT TO THE DATABASE SERVER.' .'</center>');
					$Sql ="INSERT INTO cl_class (name) VALUES ('$grp')";
					$res = @mysql_query ($Sql) or die (mysql_error());
					if (($res) && ($grp!=Profs)) {
					$cpt++;
					echo '<script type="text/javascript" language="JavaScript">
					document.getElementById("cr2").innerHTML = "   '.$cpt.'  classes cr&#233;&#233;es" ;
					</script>';
					}
					if (($res) && ($grp==Profs)) ;
					echo '<script type="text/javascript" language="JavaScript">
					document.getElementById("cr4").innerHTML = "  OK " ;
					</script>';
					//récupération de l'id du groupe créé
					$Sql ="SELECT id FROM cl_class WHERE name='$grp'";
					$res = @mysql_query ($Sql) or die (mysql_error());
					$tst=mysql_fetch_array($res, MYSQL_NUM);
					if (mysql_num_rows($res)>0) $id_grp=$tst[0];
					
					//recherche des membres
					$uids = search_uids ("(cn=".$groups[$loup]["cn"].")", "half");
		  			$people = search_people_groups ($uids,"(sn=*)","cat");
		  			//on se reconnecte à la base claroline
		  			$db = @mysql_connect($dbHost, $dbLogin, $dbPass, false, CLIENT_FOUND_ROWS) 
		  			or die ('<center>' .'WARNING ! SYSTEM UNABLE TO CONNECT TO THE DATABASE SERVER.' .'</center>');
		  			 for ($loop=0; $loop <count($people); $loop++) 
		  			 	{
		  			 	
		      				$uname = $people[$loop]['uid'];
		      				$nom = addslashes($people[$loop]["name"]);
		      				$prenom= getprenom($people[$loop]["fullname"],$nom);
		      				
		      				//insertion des membres dans la table users	
		      				
		      				if  ($grp != "Profs"){
		      				if (!(is_present($uname))) add_eleve($nom,$prenom,$uname);
		      				user_in_classe($id_grp,is_present($uname));
		      				$cpt2++;
		      				echo '<script type="text/javascript" language="JavaScript">
		      				document.getElementById("cr3").innerHTML = "   '.$cpt2.'  &#233;l&#232;ves affect&#233;s" ;
		      				</script>';
		      				}
		      				else {
		      				$cpt3++;
		      				if (!(is_present($uname))) add_prof($nom,$prenom,$uname);
		      				user_in_classe($id_grp,is_present($uname));
		      				echo '<script type="text/javascript" language="JavaScript">
		      				document.getElementById("cr5").innerHTML = "   '.$cpt3.'  Profs affect&#233;s" ;
		      				</script>';
		      				}
					       //membre suivant
					       
					       }//fin d'insertion des membres
				       $mess.="<BR>";
				        
				       
					}//fin de traitement d'une classe
				}//fin du traitement 
			else  $mess.= "<h3 class='ko'> Erreur dans l'importation de $filtre<BR></h3>";
			
 

}

// Display footer

include get_path('incRepositorySys') . '/claro_init_footer.inc.php';

?>
