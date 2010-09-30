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
 */

 /* $Id$ */

define('SM_PATH','../../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/imap.php');


// global vars set by SM
echo 'user : '.$_SESSION['username'];
$username = $_SESSION['username'];
$key = $_COOKIE['key'];

// display address from user specified personal information
$display = getPref($data_dir, $username, 'email_address', $username);

if ($display == '') {
  $display = $username;
}

// user defined option - period between checks in minutes
$refresh = getPref($data_dir, $username, 'notify_period', '5');

// user defined option - whether to play sound
$sound = getPref($data_dir, $username, 'notify_sound', 'Y');

if ($refresh < 1 || $refresh > 30) {
  $refresh = 5;
}

$refresh *= 60000;

// vars for page content
$script = '';
$msg = '';

// Login to IMAP server and check for unread mail
$imap = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);
$status = sqimap_status_messages($imap, 'INBOX');
$check = $status['UNSEEN'];
sqimap_logout($imap);

// Form message and output HTML
if ($check > 0) {
  $msg = '<P STYLE="FONT-SIZE:11pt;FONT-WEIGHT:BOLD;COLOR:BLUE">'.$check;
  if ($check == 1) {
    $msg .= ' nouveau message';
  } else {
    $msg .= ' nouveaux messages';
  }

  $msg .= "</P>\n";

}
else {
  $msg = "<P STYLE=\"FONT-SIZE:11pt;FONT-WEIGHT:BOLD\">Pas de nouveau message</P>\n";
}

echo $msg;

?>