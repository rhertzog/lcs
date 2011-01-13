<?php
$IDPERS=0;$LOGIN="";
if (! empty($_COOKIE["LCSAuth"])) { 
	$SESS=$_COOKIE["LCSAuth"];

	include ("/usr/share/lcs/roundcube/plugins/lcs_authentication/config.inc.php");
	# Search idpers
	$IDPERS=exec ("mysql -e \"SELECT idpers from $DBAUTH.sessions where sess='$SESS'\" -u $USERAUTH -p$PASSAUTH");

	# Search login
	$LOGIN=exec ("mysql -e \"SELECT login FROM $DBAUTH.personne WHERE id=$IDPERS \" -u $USERAUTH -p$PASSAUTH");
	
	# Search and decode LCS cookie pass
	if ($IDPERS != "0") 
		$PASS = urldecode( xoft_decode($_COOKIE['LCSuser'],$key_priv) );	
}
				  				  
$mbox = imap_open("{localhost:143}INBOX", $LOGIN, $PASS);
$status = imap_status($mbox, "{localhost:143}INBOX", SA_UNSEEN);

if ($status) {
	// Form message and output HTML
	if ($status->unseen > 0) {
  		$msg = '<p STYLE="FONT-SIZE:11pt;FONT-WEIGHT:BOLD;COLOR:#fdb218">Vous avez '.$status->unseen;
  		if ($status->unseen == 1) 
    		$msg .= ' nouveau message';
  		else 
    		$msg .= ' nouveaux messages';
  	
  		$msg .= "</p>\n";
	} else 
  		$msg = "";
} else
	$msg = "<p>Erreur imap : " . imap_last_error() . "</p>";
	
echo $msg;
imap_close($mbox);

?>
