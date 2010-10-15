<?php
	exec("ls sessions/*_chat.txt", $fl[], $retval);
	echo "games currently played: " . floor(count($fl[0]) / 2);
?>