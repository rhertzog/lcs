<?PHP
/*
 * Notify SquirrelMail Plugin
 *
 * Provides a minimal new mail notification page that will restore from
 * minimized in Javascript supporting browsers.
 *
 * Unfortunately this plugin requires Javascript on the browser.
 *
 * By Richard Gee (richard.gee@pseudocode.co.uk)
 *
 * Version 1.3
 * Copyright 2002 Pseudocode Limited.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * Plugin modifié pour la notification de message dans le lcs desktop.
 * Ce fichier peut être utilisé seul ou avec le reste du plugin Notify
 * Les modifications ont été apportées par Yannick Chistel.
 */

 /* $Id$ */

define('SM_PATH','../../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/imap.php');

require  "/var/www/lcs/includes/headerauth.inc.php";
list ($idpers, $login)= isauth();
// global vars set by SM
$username = $login;
$key = $_COOKIE['key'];

$msg = '';

// Login to IMAP server and check for unread mail
$imap = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);
$status = sqimap_status_messages($imap, 'INBOX');
$check = $status['UNSEEN'];
sqimap_logout($imap);

// Form message and output HTML
if ($check > 0) {
  $msg = '<P STYLE="FONT-SIZE:11pt;FONT-WEIGHT:BOLD;COLOR:#fdb218">Vous avez '.$check;
  if ($check == 1) {
    $msg .= ' nouveau message';
  } else {
    $msg .= ' nouveaux messages';
  }

  $msg .= "</P>\n";

}
else {
  $msg = "";
}

echo $msg;

?>