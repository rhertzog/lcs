<?php
/* Derniere mise à jour 09/06/2009 */
	if (!isset($plugins_commun))
		{ 
		  $plugins_commun = true;

		  $chemin_vers_fichier_xml_des_plugins_dispos = $urlmajplug . "/applilcs.xml";
		  $chemin_des_uploads = "/usr/share/lcs/Plugins/Uploadtmp";
		  $chemin_de_desarchivage_des_plugins = "/usr/share/lcs/Plugins";
		  $chemin_administration_plugin = "Admin";
		  $chemin_maj_plugin =  $chemin_administration_plugin . "/" . "Maj";
		  $fichier_installation_mysql_plugin = "install_mysql.sql";
		  $fichier_installation_ldap_plugin = "install_ldap.ldif";
		  $fichier_script_install_plugin = "install";
		  $fichier_desinstallation_mysql_plugin = "desinstall_mysql.sql";
		  $fichier_desinstallation_ldap_plugin = "desinstall_ldap.ldif";
		  $fichier_script_desinstall_plugin = "desinstall";
		  $fichier_script_maj_plugin =  "maj";
		  $fichier_script_sudo = "/usr/share/lcs/scripts/execution_script_plugin.sh";
		  $chemin_des_logs = "/usr/share/lcs/Plugins/Logs";
		  $fichier_script_patchconf_plugin = "patchconf";
		  $fichier_maj_mysql_plugin = "mysql";
		  $fichier_maj_ldap_plugin = "ldap";

		  $map_array = array("MODULES" => "TABLE BORDER=1",
				     "MODULE" => "TR", 
				     "INTITULE" => "TD",
				     "VERSION" => "TD",
				     "ID" => "TD",
				     "ETAT" => "TD",
				     "SERVEUR" => "TD",
                                     "MD5" => "TD",
				     "AIDE" => "TD",
				     "ETAB" => "TD",
				     "MODEFENETRAGE" => "TD",
				     "DEPENDANCE" => "TD"
				     ); 
		  $plugins = array();
		  $plugincourant = "";
		  $versioncourante = "";
		  $elementcourant = "";

		  function get_mysql_root_pwd() // renvoi le login et le mot de passe de l'administrateur du serveur mysql
		  	{
			 	//$ctn = file("/etc/LcSeConfig.ph");
				$ctn = array();
			        $cmd = "cat /etc/LcSeConfig.ph | grep mysqlServer";
			        exec($cmd,$ctn,$ret_val);
				for($i = 0; $i < count($ctn); $i++)	
					{
						$ligne = explode("=",$ctn[$i]); // on separe les variable des valeurs lors des affectations
						$ligne[0] = str_replace(" ","",$ligne[0]); // on retire tous les blancs
						if ($ligne[0] == "\$mysqlServerUsername")
							{
							  $tab = array("\"", " ",";");
							  $ligne[1] = str_replace($tab,"",$ligne[1]); // on retire les guillemets, les blancs et les points virgules
							  $res[0] = $ligne[1];
							}
						if ($ligne[0] == "\$mysqlServerPw")
							{
							   $tab = array("\"", " ",";"); 
							   $ligne[1] = str_replace($tab,"",$ligne[1]); // on retire les guillemets, les blancs et les points virgules
							   $res[1] = $ligne[1];
							}
					}
				return $res;
			}

		  function elementDebut($parser, $name, $attrs)
			{
			  //global $map_array;
			  global $plugins, $plugincourant, $versioncourante, $elementcourant;
			  if ($name == "MODULES")
			  	; // on ne fait rien
			  if ($name == "MODULE")
			  	{	
					$plugincourant = $attrs["NOM"];
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
			}

		  function elementFin($parser, $name)
			{
			  global $plugins, $plugincourant, $versioncourante, $elementcourant;
			  if ($name == "MODULES")
			    ; // on ne fait rien
			  if ($name == "MODULE")
			  	{
					$plugincourant = "";
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
			}
			
		function characterData($parser, $data)
			{
			  global $plugins, $plugincourant, $versioncourante, $elementcourant;
			    if ($elementcourant == "INTITULE")
			    	{
			    		$plugins[$plugincourant]["intitule"] = $data;
//			    		print "|||$data|||";
				}	
			  if ($elementcourant == "ID")
			  	{
					$plugins[$plugincourant]["version"][$versioncourante]["id"] = $data;
				}
			  if ($elementcourant == "AIDE")
			  	{
					$plugins[$plugincourant]["version"][$versioncourante]["aide"] = $data;
				}
			  if ($elementcourant == "SERVEUR")
			  	{
					$plugins[$plugincourant]["version"][$versioncourante]["serveur"] = $data;
				}
			  if ($elementcourant == "MD5")
			  	{
					$plugins[$plugincourant]["version"][$versioncourante]["md5"] = $data;
				}                                
			}

		function parsage_du_fichier_xml()
			{
			  global $chemin_vers_fichier_xml_des_plugins_dispos;
			  $commande = "cd /tmp; rm -f applilcs.xml; wget -q --cache=off $chemin_vers_fichier_xml_des_plugins_dispos;";
			  $fichiertmp = "/tmp/applilcs.xml";
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
				$sepv1 = recherche_sep($v1);
				$sepv2 = recherche_sep($v2);

				$tv1 = @explode($sepv1,$v1);
				$tv2 = @explode($sepv2,$v2);
				
				// on complete les sous versions à 0 s'il y a lieu
				for($i = count($tv1); $i < count($tv2); $i++)
					$tv1[$i] = 0;
				for($i = count($tv2); $i < count($tv1); $i++)
					$tv2[$i] = 0;
			
				for($i = 0; $i < count($tv1); $i++)
					{
					  if ($tv1[$i] > $tv2[$i]) return -1;
					  if ($tv1[$i] < $tv2[$i]) return 1;
					}
				return 0;
			}

		function get_nom_de_fichier($url) // renvoi seulement le nom de fichier de l'url (sans la base)
			{ 
			  $pos = mb_strrpos($url,"/");
			  return mb_substr($url,$pos+1);
			}

		function creation_log($p,$v,$t) // cree le fichier de log d'un plugin $p de version $v du type $t (installation/desinstallation/maj)
			{
				global $chemin_des_logs;
				switch($t)
					{
					  case "1" : $type = "install"; break;
					  case "2" : $type = "desinstall"; break;
					  case "3" : $type = "maj"; break;
					}
				$fichier = "log_" . $type . "_" . $p . "_" . date("Ymd_His") . ".log";
				$chemin = $chemin_des_logs . "/" . $fichier;
				$df = fopen($chemin,"w");
				switch($t)
					{
					  case "1" : $action = "l'installation"; break;
					  case "2" : $action = "la désinstallation"; break;
					  case "3" : $action = "la mise à jour"; break;
					}
				fputs($df,">>>Début de " . $action . " du plugin $p (version $v) le " . date("d/m/Y à H\hi") . "\n"); 
				fclose($df);
				return $fichier;
			}

		function ecrit_log($f,$s) // ecrit dans le fichier $f la ligne $s
			{
			  global $chemin_des_logs;
			  $chemin = $chemin_des_logs . "/" . $f;
			  $df = fopen($chemin,"a");
			  fputs($df,$s . "\n");   
			  fclose($df);
			}
			
		function cree_nom_fichier_ecran($p) // construit le nom du fichier html de la succession des ecrans d'installation
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
			  global $plugins;
 			  reset($plugins);

			  $query="SELECT * from applis WHERE name='$pname'";
			  $result=mysql_query($query);
			  if ($result)
			          {
				    $row = mysql_fetch_object($result);
				    $version = $row->version;
				    mysql_free_result($result);
					
				  }
			  if (isset($plugins[$pname])) // le plugin est present dans le fichier xml
			  	
				{
		          	  uksort($plugins[$pname]["version"],"version_moins_recente"); // on trie de la plus recente a la moins recente
			  	  list($v,$plug) = each($plugins[$pname]["version"]); // on selectionne la derniere version
		          	  if (version_moins_recente($v,$version) == -1)
				  	  return array ($v,$plug);
				  	  if (version_moins_recente($v,$version) == 0)
				  	  return array ("",$plug);
					  				}
			  return false;
			}
			
		
		}
?>
