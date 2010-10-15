<?php

	exec("ls *_chat.txt", $fl[], $retval);

	foreach ($fl[0] as $b) {
		$a = str_replace("_chat", "", $b);

		if (file_exists($a)) {
			$arr1 = explode("-", date ("H-i-s-j", filemtime($a)));
			$arr2 = explode("-", date ("H-i-s-j"));
			
			$old_stamp = ($arr1[0] * 60 * 60) + ($arr1[1] * 60) + ($arr1[2]);
			$new_stamp = ($arr2[0] * 60 * 60) + ($arr2[1] * 60) + ($arr2[2]);
	
			$stamp_max_diff = 1.5 * 60 * 60;
					
			echo "x: " . $a . ": " . $stamp_new_diff . " - " . date ("H-i-s-j", filemtime($a)) . "<br>";
			if ($arr1[3] == $arr2[3]) {
				/* same day */
				if ($arr2[0] > 2) {
					/* if after 2 in the morning: prune */
					$stamp_new_diff = ($new_stamp - $old_stamp);
					if ($stamp_new_diff > $stamp_max_diff) {
						echo "unlinking: " . $a . ": " . $stamp_new_diff . " - " . date ("H-i-s-j", filemtime($a)) . "<br>";
//						echo "unlinking: " . $b . ": " . $stamp_new_diff . " - " . date ("H-i-s-j", filemtime($b)) . "<br>";
						unlink($a);
						unlink($b);
					}
				} /* end arr2[0] > 2 */
			} /* end if same day */ else {
						echo "unlinking: " . $a . ": " . $stamp_new_diff . " - " . date ("H-i-s-j", filemtime($a)) . " (another day)<br>";
						unlink($a);
						unlink($b);
			}
			
		} /* end if fileexists */
	} /* end foreach */

?>