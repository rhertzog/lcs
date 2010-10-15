<?
require_once('../include/common.inc.php');
require_once('../include/HttpQuery.class.inc.php');



$upload_dir = '/usr/share/lcs/helpdesk/tmp';
$tf = $upload_dir.'/'.md5(rand()).".test";
$f = @fopen($tf, "w");
if ($f == false) 
    die("Erreur fatale ne peut &#233;crire sur {$upload_dir} . Pensez &#224;  'chmod 777 {$upload_dir}' ");
fclose($f);
unlink($tf);
        	
$url = $HD->urlHD."AjaxAPI/AddReplyFile";
//die($url);


// FILEFRAME section of the script
if (isset($_POST['fileframe'])) 
{
    $result = 'ERROR';
    $result_msg = 'No FILE field found';

    if (isset($_FILES['file']))  // file was send from browser
    {
        if ($_FILES['file']['error'] == UPLOAD_ERR_OK)  // no error
        {
            $filename = $_FILES['file']['name']; // file name
	    $infos = array_reverse(explode('.',$filename));
            $filename = 'file_reply-'.$_POST['ticket_id'].'.'.$infos[0]; 
	    move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir.'/'.$filename);
            //TODO - transmettre le fichier enctype vers le HelpDesk en curl ... 

	try {
    		$url = str_replace('https:','http:',$url);
		$http = new HTTPQuery($url);
    		//var_dump($http);
    		$http->addPostFile('images[0]', $upload_dir.'/'.$filename);
    		$http->addPostData('ticket_id', $_POST['ticket_id']);
    		if ($http->doRequest()) 
			$result_msg = 'Ok';
	} catch (Exception $e) {
    		$result_msg = $e->getMessage(); 
	}

	    $handle = fopen("/usr/share/lcs/helpdesk/tmp/log.txt", "w+");
	    fwrite($handle, $result_msg);
	    fclose($handle);	
	    // main action -- move uploaded file to $upload_dir 
            $result = 'OK';
        }
        elseif ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE)
            $result_msg = 'La taille de votre image est trop grande cf php.ini ';
	 elseif ($_FILES['file']['error'] == UPLOAD_ERR_FORM_SIZE)
            $result_msg ="Le fichier d&#233;passe la limite autoris&#233;e dans le formulaire HTML !";

        else 
            $result_msg = 'Erreur inconnue';

        
    }

    
    echo '<html><head><title>-</title></head><body>';
    echo '<script language="JavaScript" type="text/javascript">'."\n";
    echo 'var parDoc = window.parent.document;';
   
    if ($result == 'OK')
    {
        // Simply updating status of fields and submit button
        echo 'parDoc.getElementById("upload_status").innerHTML = "Le fichier est correctement charg&#233;";';
        echo 'parDoc.getElementById("filename").value = "'.$filename.'";';
        echo 'parDoc.getElementById("upload_status").innerHTML = "STATUS: '.$result_msg.'";';

	 //echo 'parDoc.getElementById("filenamei").value = "'.$filename.'";';
        //echo 'parDoc.getElementById("upload_button").disabled = false;';
	//echo "parDoc.getElementById('liste_images').innerHTML = '".liste_vignettes()."';";
	//echo "parDoc.getElementById('liste_images').value = '".$filename."';";
	//echo "parDoc.getElementById('view_image').src = '".$url_vignettes.$filename."';";
    }
    else
    {
        echo 'parDoc.getElementById("upload_status").innerHTML = "ERROR: '.$result_msg.'";';
    }

    echo "\n".'</script></body></html>';

    exit(); // do not go futher 
}
// FILEFRAME section END



// just userful functions
// which 'quotes' all HTML-tags and special symbols 
// from user input 
function safehtml($s)
{
    $s=str_replace("&", "&amp;", $s);
    $s=str_replace("<", "&lt;", $s);
    $s=str_replace(">", "&gt;", $s);
    $s=str_replace("'", "&apos;", $s);
    $s=str_replace("\"", "&quot;", $s);
    return $s;
}

if (isset($_POST['description']))
{
    $filename = $_POST['filename'];
    $size = filesize($upload_dir.'/'.$filename);
    $date = date('r', filemtime($upload_dir.'/'.$filename));

} 
 
?>
<!-- Beginning of main page -->

<?php
	echo "<input type=\"hidden\" name=\"filename\" id=\"filename\">";
?>


<br />
<div id="upload_panel">
<form action="/helpdesk/ajaxlib/upload.php" target="upload_iframe" method="post" enctype="multipart/form-data">
<INPUT type="hidden" name="MAX_FILE_SIZE"  VALUE="1638400"></input>
<input type="hidden" name="fileframe" value="true"></input>
<input type="hidden" id="ticket_id" name="ticket_id" value="?"></input>
<!-- Target of the form is set to hidden iframe -->
<!-- From will send its post data to fileframe section of 
     this PHP script (see above) -->

<label for="file">Fichier (image ou pdf): (Taille limit&#233;e &#224; 1500Ko)</label><br>
<!-- JavaScript is called by OnChange attribute -->
<input id="file" type="file" name="file" id="file" onChange="jsUpload(this)">
</form>
</div>
<div id="upload_status">&nbsp;</div>
<iframe name="upload_iframe" style="width: 400px; height: 100px; display: none;">
</iframe>

