<?php

/*
---------------------------------------------------------
Ajout� par le lyc�e laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------
*/


Class WikiniLCS 
{
	

	//authentification LCS
	function GetUserName() { 
                
		#Utilisation de l'authentification du LCS
		#La fonction isauth() teste si une session est en cours. Si oui, retourne $idpers!=0 et $login, si non retourne $idpers=0
		list($idpers,$login) = isauth();
                
		if ($idpers != 0) {
	                
			$name = $login;

			#si l'utilisateur s'est authentifi�, on r�cup�re ses param�tres (email, groupes, etc...) � partir de la fonction people_get_variables
		        #l'option "true" permet de renvoyer 2 tableaux : $user[] et $groups[]
			#l'option "false" permet de renvoyer 1 tableau : $user[]
			list($user,$groups) = people_get_variables($login, true);
			$mel = $user["email"]; //r�cup�ration du m�l de l'utilisateur

			#teste si l'utilisateur existe d�j� dans la base de donn�es du wiki
			if ($existingUser = $this->LoadUser($name)) {
				#l'utilisateur existe
				$this->SetUser($existingUser, null);
			}
			else {
				#l'utilisateur n'existe pas donc il est automatiquement ajout�
				#les utilisateurs sont enregistr�s dans la table $this->config["table_prefix"]."users" (exemple wikini_users)
				$this->Query("insert into ".$this->config["table_prefix"]."users set signuptime = now(), name='".mysql_escape_string($name)."', email='".mysql_escape_string($mel)."' ");
			
			
				#il faut associer le nouvel utilisateur � ses groupes (si il est associ� � des groupes)
				#r�cup�ration des groupes de l'utilisateur
				if (count($groups)) {
					#pour chaque groupe du tableau $groups,
					for ($i=0; $i < count($groups); $i++) {
						#r�cup�ration du cn du groupe
						$cn_groupe = $groups[$i]["cn"];
						
						#les groupes importants � enregistrer dans le wiki sont Profs, Eleves, Classe_, Equipe_ et Administratifs
					        #la fonction UserInGroup renvoit true si l'utilisateur est d�j� associ� au groupe
						if (!$existingUserInGroup = $this->UserInGroup($cn_groupe, $user = $name)) {
							#l'utilisateur n'est pas associ� au groupe
							#ajout de l'enregistrement dans la table wikini_groups uniquement si il s'agit d'un groupe du type Profs, Eleves, Equipe_, Classe_ ou Administratifs
							if ((substr($cn_groupe,0,6)=="Equipe")||(substr($cn_groupe,0,5)=="Profs")||(substr($cn_groupe,0,6)=="Eleves")||(substr($cn_groupe,0,6)=="Classe")||(substr($cn_groupe,0,14)=="Administratifs")){
							$this->Query("insert into ".$this->config["table_prefix"]."groups set grname='".mysql_escape_string($cn_groupe)."', grmember='".mysql_escape_string($name)."' ");
							}
						}
						
						
					}
				}
			}
			
                }
                else {
                        #si l'utilisateur n'est pas authentifi�, la connexion se fait en "invit�"
	                $name = "invit�";
                }

		return $name;
	
    	}

	
	
	
	//Afficher les pages sans auteurs connus dans la base de donn�es
	function LoadPagesSansProprio() {
	$pages = $this->LoadAll("select distinct tag, owner from ".$this->config["table_prefix"]."pages left join ".$this->config["table_prefix"]."users ON owner=name where name IS NULL ORDER BY owner");
		foreach ($pages as $page) {
		echo $page["owner"];
		echo "\t \t";
		echo $this->ComposeLinkToPage($page["tag"], "", "", 0),"<br />\n" ;
		}
	}
	
	function PurgePagesSansProprio() {
	$pages = $this->LoadAll("select id, tag, owner from ".$this->config["table_prefix"]."pages left join ".$this->config["table_prefix"]."users ON owner=name where name IS NULL AND latest = 'N' ORDER BY owner");
		foreach ($pages as $page) {
		$id= $page["id"];
		$this->Query("delete from ".$this->config["table_prefix"]."pages where id=$id");
	        echo $this->ComposeLinkToPage($page["tag"], "", "", 0)," a �te supprim�e <br />\n";
		}
	}

	function DeletePagesSansProprio() {
        $pages = $this->LoadAll("select distinct tag from ".$this->config["table_prefix"]."pages left join ".$this->config["table_prefix"]."users ON owner=name where name IS NULL");
		foreach ($pages as $page) {
		$tag= $page["tag"];
		$this->Query("delete from ".$this->config["table_prefix"]."pages where tag='$tag'");
		$this->Query("delete from ".$this->config["table_prefix"]."links where from_tag='$tag'");
		$this->Query("delete from ".$this->config["table_prefix"]."acls where page_tag='$tag'");				   
		echo $this->ComposeLinkToPage($page["tag"], "", "", 0)," a �te supprim�e <br />\n";
		}
	}

	
	//Cr�e les utilisateurs relatif au groupe $grname (ou Equipes et Classes) et rajoute l'utilisateur � son groupe
	function createGroups($grname)
	{
	if (($grname=="Equipe") || ($grname=="Classe")) {
	//Liste des �quipes
  	$grname = $grname._;
	$groupes = search_uids ("(cn=$grname*)","half");
        for ( $x=0; $x<count($groupes); $x++)
           {
	   $groupe=$groupes[$x]["cat"]._.$groupes[$x]["group"];
	   $login=$groupes[$x]["uid"];
	   if (! $login) echo "<br /><b>ATTENTION</b> un login est vide : cela arrive notamment quand vous avez un membre dans le groupe $groupe qui ne correspond pas � un utilisateur valide dans la branche PEOPLE de LDAP. Mais ceci n'a pas emp�ch� la bonne r�alisation de l'op�ration. <br />";
	   	elseif ($this->UserInGroup($groupe, $user = $login))
	   	 	echo "$login d�j� associ� <br />";
			else $this->Query("INSERT INTO ".$this->config["table_prefix"]."groups (grname,grmember) values('$groupe','$login')");
	  }
	  echo "<br /><b>La cr�ation des groupes ".substr($grname,0,strlen($grname)-1)."s"." a �t� r�alis�e</b><br />";
	}
	else {
 	   $users=search_uids ("cn=$grname","full");
 	   //Cr�ation effective
 	   for ($x=0;$x<count($users);$x++)
 		{
  		$uid=$users[$x]["uid"];
  		list($user,$groups)=people_get_variables($uid, false);
  		$login=$user["uid"];
  		$mail=$user["email"];
  		//Insertion de l'utilisateur
		 if (! $login){}
		 	elseif ($this -> loadUser($login))
 				echo "<br /> $login existe d�j� dans la base de donn�es <br />";
	 			else $this->Query("INSERT INTO ".$this->config["table_prefix"]."users (name,email) values('$login','$mail')");
  		//Association avec le groupe
	 if (! $login) echo "<br /><b>ATTENTION</b> un login est vide : cela arrive notamment quand vous avez un membre dans le groupe $grname qui ne correspond pas � un utilisateur valide dans la branche PEOPLE de LDAP. Mais ceci n'a pas emp�ch� la bonne r�alisation de l'op�ration.<br />";
  			elseif ($this->UserInGroup($grname, $user = $login))
         			echo "$login d�j� associ� <br>";
         			else $this->Query("INSERT INTO ".$this->config["table_prefix"]."groups (grname,grmember) values('$grname','$login')");
 		}
	    echo "<br /><b>La cr�ation du groupe $grname et des utilisateurs correspondants a �t� r�alis�e</b><br />";	
             }
	}


	//Supprime les groupes et/ou les utilisateurs en fonction du nom du groupe pass� en param�tre
        //Si $grname="all" suppression de tous les groupes sauf admins
        //si $mode="full" supprime les utilisateurs avant la suppression des groupes sauf admin

        function supGroups($grname,$mode="")
        {
         	  
	  if ($grname=="all") {
             if ($mode!="full") {
                $this->Query("DELETE FROM ".$this->config["table_prefix"]."groups WHERE grname != 'admins' AND grmember != 'admin'");
		$message="La suppression de tous les groupes existants (sauf admins) a �t� r�alis�e.\\n";
                }
                elseif ($mode=="full") {
                        $this->Query("DELETE FROM ".$this->config["table_prefix"]."users");
                        $this->Query("DELETE FROM ".$this->config["table_prefix"]."groups WHERE grname != 'admins' OR grmember != 'admin'");
			$message="La suppression de tous les groupes existants (sauf admins) et des utilisateurs correspondants a �t� r�alis�e.\\n";
                };
          }

          if (($grname!="all") && ($mode!="full")) {
                $rep = $this->LoadSingle("SELECT* FROM ".$this->config["table_prefix"]."groups WHERE grname like '$grname%'");
		if ($rep) {	
			$this->Query("DELETE FROM ".$this->config["table_prefix"]."groups WHERE grname like '$grname%'");
			$message="La suppression du groupe $grname a �t� r�alis�e.\\n";
			}
			else {
				$message="Le groupe $grname n\'existe pas.\\n";
			}
                }
                elseif (($grname!="all") && ($mode=="full")) {
			$rep = $this->LoadSingle("SELECT* FROM ".$this->config["table_prefix"]."groups WHERE grname like '$grname%'");
		        if ($rep) {
                        	$this->Query("DELETE ".$this->config["table_prefix"]."users FROM ".$this->config["table_prefix"]."users, ".$this->config["table_prefix"]."groups WHERE grname='$grname' AND name=grmember");
                        	$this->Query("DELETE FROM ".$this->config["table_prefix"]."groups WHERE grname='$grname'");
				$message="La suppression du groupe $grname et des utilisateurs correspondants a �t� r�alis�e.\\n";
			}
				else {
					$message="Le groupe $grname n\'existe pas.\\n";
				}		
		}
		return $message;
	}
	 
}


?>
