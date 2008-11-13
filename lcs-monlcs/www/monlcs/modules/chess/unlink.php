<?php
	$do1 = $_COOKIE["achess_do"];
	$id1 = $_COOKIE["achess_id"];

	if ($_REQUEST["d"] != "") {
	if ($id1 != "") {
		$str1 = "sessions/" . $id1 . ".txt";
		$str2 = "sessions/" . $id1 . "_chat.txt";

		unlink($str1);
		unlink($str2);
		echo "deleted $str1";
	}
	}
?>