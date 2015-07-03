<?php
/* =============================================
   Projet LCS-SE3
   Administration serveur LCS «Desinstallation d'un module»
   modules_commun.php
   Equipe Tice academie de Caen
   28/03/2014
   Distribue selon les termes de la licence GPL
   ============================================= */
session_name("Lcs");
@session_start();
if ( ! isset($_SESSION['login'])) {
    echo "<script type='text/javascript'>";
    echo 'alert("Suite \340 une p\351riode d\'inactivit\351 trop longue, votre session a expir\351 .\n\n Vous devez vous r\351authentifier");';
    echo 'location.href = "../lcs/logout.php"</script>';
    exit;
}
	if (!isset($Modules_commun))
		{
		  $Modules_commun = true;

		  $chemin_vers_fichier_xml_des_Modules_dispos = $urlmajmod . "moduleslcs.xml";
		  $chemin_des_logs = "/usr/share/lcs/Modules/Logs";

		  $map_array = array("MODULES" => "TABLE BORDER=1",
				     "MODULE" => "TR",
				     "INTITULE" => "TD",
				     "VERSION" => "TD",
				     "ID" => "TD",
				     "ETAT" => "TD",
				     "SERVEUR" => "TD",
                                                                              "MD5" => "TD",
				     "AIDE" => "TD",
				     "TYPE" => "TD",
				     "MODEFENETRAGE" => "TD",
				     "DEPENDANCE" => "TD"
				     );
		  $Modules = array();
		  $Modulecourant = "";
		  $versioncourante = "";
		  $elementcourant = "";


		  function elementDebut($parser, $name, $attrs)
			{
			  //global $map_array;
			  global $Modules, $Modulecourant, $versioncourante, $elementcourant;
			  if ($name == "MODULES")
			  	; // on ne fait rien
			  if ($name == "MODULE")
			  	{
					$Modulecourant = $attrs["NOM"];
				}
			  if ($name == "INTITULE")
			  	{
					$elementcourant = "INTITULE";
				}
			  if ($name == "VERSION")
			  	{
					$versioncourante = $attrs["VER"];
				}
			  if ($name == "ID")
			  	{
					$elementcourant = "ID";
				}
			  if ($name == "AIDE")
			  	{
					$elementcourant = "AIDE";
				}
			  if ($name == "SERVEUR")
			  	{
					$elementcourant = "SERVEUR";
				}
 			  if ($name == "MD5")
			  	{
					$elementcourant = "MD5";
				}
			 if ($name == "TYPE")
			  	{
					$elementcourant = "TYPE";
				}
			}

		  function elementFin($parser, $name)
			{
			  global $Modules, $Modulecourant, $versioncourante, $elementcourant;
			  if ($name == "MODULES")
			    ; // on ne fait rien
			  if ($name == "MODULE")
			  	{
					$Modulecourant = "";
				}
                          if ($name == "INTITULE")
                                {
                                  $elementcourant = "";
			        }
			  if ($name == "VERSION")
			  	{
				  $versioncourante = "";
				}
			  if ($name == "ID")
			  	{
				  $elementcourant = "";
				}
			  if ($name == "AIDE")
			  	{
				  $elementcourant = "";
				}
			  if ($name == "SERVEUR")
			  	{
			 	  $elementcourant = "";
				}
 			  if ($name == "MD5")
			  	{
			 	  $elementcourant = "";
				}
			  if ($name == "TYPE")
			  	{
					$elementcourant = "";
				}
			}

		function characterData($parser, $data)
			{
			  global $Modules, $Modulecourant, $versioncourante, $elementcourant;
			    if ($elementcourant == "INTITULE")
			    	{
			    		$Modules[$Modulecourant]["intitule"] = $data;
//			    		print "|||$data|||";
				}
			  if ($elementcourant == "ID")
			  	{
					$Modules[$Modulecourant]["version"][$versioncourante]["id"] = $data;
				}
			  if ($elementcourant == "AIDE")
			  	{
					$Modules[$Modulecourant]["version"][$versioncourante]["aide"] = $data;
				}
			  if ($elementcourant == "SERVEUR")
			  	{
					$Modules[$Modulecourant]["version"][$versioncourante]["serveur"] = $data;
				}
			  if ($elementcourant == "MD5")
			  	{
					$Modules[$Modulecourant]["version"][$versioncourante]["md5"] = $data;
				}
			if ($elementcourant == "TYPE")
			  	{
					$Modules[$Modulecourant]["version"][$versioncourante]["type"] = $data;
				}
			}

		function parsage_du_fichier_xml()
			{
			  global $chemin_vers_fichier_xml_des_Modules_dispos;
			  $commande = "cd /tmp; rm -f moduleslcs.xml; wget -q --cache=off $chemin_vers_fichier_xml_des_Modules_dispos;";
			  $fichiertmp = "/tmp/moduleslcs.xml";
			  system($commande);
			  //creation du parser xml
			  $xml_parser = xml_parser_create();
			  xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
			  xml_set_element_handler($xml_parser, "elementDebut", "elementFin");
			  xml_set_character_data_handler($xml_parser, "characterData");
			  if (!($fp = fopen($fichiertmp, "r")))
			  	{
			          die("je ne peux ouvrir le fichier $fichiertmp");
			        }
			  while ($data = fread($fp, 51200))
			  	{
			          if (!xml_parse($xml_parser, $data, feof($fp)))
				  	{
				          die(sprintf("erreur XML: %s \xe0 la ligne %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
					 }
				}
			   xml_parser_free($xml_parser);
			   unlink($fichiertmp);
			}

		function est_un_chiffre($c) // renvoi vrai si le caractere est un chiffre
			{
			  switch($c)
				{
					case '0': case '1': case '2': case '3': case '4': case '5': case '6': case '7': case '8': case '9': return true;
				}
			  return false;
			}

		// recherche du caratere separateur
                function recherche_sep($v)
		        {
		           for ($i = 0; $i < mb_strlen($v); $i++)
		                if (!est_un_chiffre($v[$i]))
			             return $v[$i];
			   return "";

			}

		function version_moins_recente($v1,$v2) // renvoi -1 si v1 est plus recente que v2
						       // renvoi 0 si v1 est egale à v2
						       // renvoi 1 si v1 est moins recente que v2
			{
				$cmd_cmp= "dpkg --compare-versions ".escapeshellarg($v1)." gt ".escapeshellarg($v2);
				exec($cmd_cmp,$rien,$ret_val);
				if ($ret_val == 0)	return -1;
				$cmd_cmp= "dpkg --compare-versions ".escapeshellarg($v1)." lt ".escapeshellarg($v2);
				exec($cmd_cmp,$rien,$ret_val);
				if ($ret_val == 0)	return 1;
				$cmd_cmp= "dpkg --compare-versions ".escapeshellarg($v1)." eq ".escapeshellarg($v2);
				exec($cmd_cmp,$rien,$ret_val);
				if ($ret_val == 0)	return 0;
			}

		function get_nom_de_fichier($url) // renvoi seulement le nom de fichier de l'url (sans la base)
			{
			  $pos = mb_strrpos($url,"/");
			  return mb_substr($url,$pos+1);
			}


		function cree_nom_fichier_ecran($p) // construit le nom du fichier html de la succesion des ecrans d'installation
			{
			  return "/tmp/ecran_install_" . $p . ".html";
			}

		function creation_ecran($f,$msgIntro) // cree le fichier temporaire des suites d'ecran lors de l'install
			{
			  $df = fopen($f,"w");
			  fputs($df,"<HTML>\n");
			  fputs($df,"  <HEAD>\n");
			  fputs($df, "          <TITLE>...::: Interface d'administration Serveur LCS :::...</TITLE>\n");
			  fputs($df, "          <LINK  href='../Annu/style.css' rel='StyleSheet' type='text/css'>\n");
			  fputs($df, "  </HEAD>\n");
			  fputs($df, "  <BODY>\n");
			  fputs($df,$msgIntro);
			  fclose($df);
			}

		function ecrit_ecran($f,$s) // ecrit dans le fichier $f la ligne $s
			{
			  $df = fopen($f,"a");
			  fputs($df,$s);
			  fclose($df);
			}

		function maj_dispo($pname) // renvoi n tableau de parametres s'il existe une maj disponible
			{
			  global $Modules;
 			  reset($Modules);
                                                        $pname=((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $pname) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
			  $query="SELECT * from applis WHERE name='$pname'";
			  $result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
			  if ($result)
			          {
				    $row = mysqli_fetch_object($result);
				    $version = $row->version;
				    ((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

				  }
			  if (isset($Modules[$pname])) // le Module est present dans le fichier xml

				{
		          	  uksort($Modules[$pname]["version"],"version_moins_recente"); // on trie de la plus recente a la moins recente
			  	  list($v,$Mod) = each($Modules[$pname]["version"]); // on sélectionne la dernière version
		          	  if (version_moins_recente($v,$version) == -1)
				  	  return array ($v,$Mod);
					  if (version_moins_recente($v,$version) == 0)
				  	  return array ("",$Mod);

				}
			  return false;
			}


		}
?>
