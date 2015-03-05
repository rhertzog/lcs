<?php
#error_reporting(E_ALL);
#ini_set('display_errors','On');

include ("/var/www/lcs/includes/headerauth.inc.php");

if ( $auth_mod != "ENT" ) {
        #list ($idpers, $login)= isauth();
        session_name('Lcs');
        session_start();
        $login=$_SESSION['login'];
        # Search and decode LCS cookie pass
        if ($login) {
                $pass = urldecode( xoft_decode($_COOKIE['LCSuser'],$key_priv) );                                     $
                $mbox = imap_open("{localhost:143/imap/novalidate-cert}INBOX", $login, $pass);
                $status = imap_status($mbox,"{localhost:143/imap/novalidate-cert}INBOX", SA_UNSEEN);

                if ($status) {
                        // Form message and output HTML
                        if ($status->unseen > 0) {
                                $msg = '<p STYLE="FONT-SIZE:11pt;FONT-WEIGHT:BOLD;COLOR:#fdb218">Vous avez '.$status-$
                                if ($status->unseen == 1) 
                                $msg .= ' nouveau message';
                                else 
                                $msg .= ' nouveaux messages';
                                $msg .= "</p>\n";
                        } else 
                                $msg = "";
                } else
                        $msg = "<p>Erreur imap : " . imap_last_error() . "</p>";
                imap_close($mbox);
        } else {
                $msg ="DTQ";    
        }
}
echo $msg;
?>

