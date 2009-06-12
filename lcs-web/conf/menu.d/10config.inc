<?
$liens1=array(array(
        	"Configuration&nbsp;g&#233;n&#233;rale","lcs_is_admin", // Nom du menu, Droit
        	"Param&#232;tres serveur","edit_params.php?cat=1","lcs_is_admin", // Nom du lien, URL du lien, Droit
		"Param&#232;tres LDAP","edit_params.php?cat=2","lcs_is_admin",
        	"Cl&#233;s d'authentification","../lcs/setup_keys.php","lcs_is_admin",
		"Choix des applications","../lcs/select_briques.php","lcs_is_admin",
		"Action sur le serveur","action.php","lcs_is_admin",
	        "Etat version","majlog.php","lcs_is_admin"
        ));
$liens=array_merge($liens,$liens1);
?>