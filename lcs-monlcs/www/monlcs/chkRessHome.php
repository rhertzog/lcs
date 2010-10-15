     <?
        include "includes/secure_no_header.inc.php";

        if ($_POST || $_GET) {
                extract($_POST);
                extract($_GET);
      		$file = str_replace("/~".$uid,"/home/$uid/public_html",$file);
		//echo $file;
		if ( file_exists(urldecode($file)))
			die("1");
		else
			die("0");          
        }
?>

