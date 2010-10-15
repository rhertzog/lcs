<?php

/**
 * populate.php
 *
 * Author: Steve Bourgeois <owl@bozzit.com>
 * Project Founder: Chris Vincent <cvincent@project802.net>
 *
 * Copyright (c) 1999-2005 The Owl Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 */
                                                                                                                                                                    
global $default;

require_once(dirname(dirname(__FILE__)) . "/config/owl.php");
require_once($default->owl_fs_root . "/lib/disp.lib.php");
require_once($default->owl_fs_root . "/lib/owl.lib.php");
require_once($default->owl_fs_root . "/lib/security.lib.php");

$clean = ob_get_contents();
ob_end_clean();

if (!fIsAdmin(true)) die("$owl_lang->err_unauthorized");

global $index_file;
$index_file = "1";

fInsertUnzipedFiles($default->owl_FileDir . "/" . fid_to_name(1) , 1, $default->owl_def_fold_security, $default->owl_def_file_security, "", $default->owl_def_file_group_owner, $default->owl_def_file_owner, $default->owl_def_file_meta, "", 1, 0, 1, $default->use_fs_false_remove_files_on_load);

header("Location: " . "index.php?sess=$sess");
?>
