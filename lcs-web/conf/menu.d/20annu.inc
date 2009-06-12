<?
$liens1=array(array(
        "Annuaire","lcs_is_admin",
#	"Explorateur LDAP","../pla/\" target='_new'\"","lcs_is_admin",
        "Sauvegarde base LDAP","export_ldif.php","lcs_is_admin",
        "Restauration base LDAP","import_ldif.php","lcs_is_admin",
       	"Nettoyage des comptes","ldap_cleaner.php","lcs_is_admin",
        "Nettoyage de l'annuaire","eff_vide_groups.php","lcs_is_admin",
	"R&#233;plication","../Annu/replica.php","lcs_is_admin"
        ));
$liens=array_merge($liens,$liens1);
?>