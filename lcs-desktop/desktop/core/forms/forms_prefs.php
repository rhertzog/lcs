<?php
require  "/var/www/lcs/includes/headerauth.inc.php";
require "/var/www/Annu/includes/ldap.inc.php";
?>
<div id="tabs" style="">
	<ul>
		<li><a href="core/forms/form_prefs_bg.php">Wallpaper</a></li>
		<li><a href="core/forms/form_prefs_icons.php">Icônes</a></li>
		<li><a href="core/forms/form_prefs_win.php">Fenêtres</a></li>
		<li><a href="core/forms/form_prefs_dock.php">Quicklaunch</a></li>
<?php
list ($idpers, $login)= isauth();
if ( acces_btn_admin($idpers, $login) == "Y" ) {
	echo '<li class="admin_only"><a href="core/forms/form_admin.php">Admin</a></li>';
}
?>
		<li><span class="separator"></span></li>
		<li><a class="save"  onclick="JQD.save_prefs_dev( 'DEFAULT','admin',1) ;console.log('JQD.options',JQD.options);">Enregistrer</a></li>
		<li class="close"><a onclick="$('#btnsetbg').trigger('click');">Fermer</a></li>
	</ul>
</div>
<script>
$(document).ready(function() {
	$('.admin_only').hide();
});
</script>
