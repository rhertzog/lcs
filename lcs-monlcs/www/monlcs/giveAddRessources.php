<?php

include "includes/secure_no_header.inc.php";

if (is_eleve($uid))
	die('Ceci est impossible pour un élève!');

if ($_POST['id'] == 'lcs')
	die('Ceci est impossible ici!');

$upload_dir = "/var/www/monlcs/vignettes"; 
$tf = $upload_dir.'/'.md5(rand()).".test";
$f = @fopen($tf, "w");
if ($f == false) 
    die("Erreur fatale ne peut écrire sur {$upload_dir} . Pensez à  'chmod 777 {$upload_dir}' ");
fclose($f);
unlink($tf);
?>


<?php
//vignettes
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
            move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir.'/'.$filename);
            // main action -- move uploaded file to $upload_dir 
            $resultStatus = 'OK';
            $result_msg = 'Le fichier nous est bien parvenu';
        }
        elseif ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE)
            $result_msg = 'La taille de votre image est trop grande cf php.ini ';
        else 
            $result_msg = 'Erreur inconnue';
       
    }

    
    echo '<html><head><title>-</title></head><body>';
    echo '<script language="JavaScript" type="text/javascript">'."\n";
    echo 'var parDoc = window.parent.document;';
   
    if ($resultStatus == 'OK')
    {
        echo 'parDoc.getElementById("filename").value = "'.$filename.'";';
	echo "alert('Le fichier nous est bien parvenu');";
    } else {
	echo "alert('".$result_msg."');";

    }

    echo "\n".'</script></body></html>';

    exit(); // do not go futher 
}
// FILEFRAME section END


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
<html><head>
<title>IFRAME Async file uploader example</title>
</head>
<body>

<h3>Ressource</h3>

<?php

echo "<table><tr><td><div onclick=javascript:viewUrl2(); >$view_img</div></td><td><div onclick=javascript:addUrl(); >$add_img</div></td></tr></table>";
?>
<div id="upload_panel2">
<form action="upload.php" target="iframe-upload2" method="post" enctype="multipart/form-data">
<label for="Filedata">Fichier &#224; charger:</label><br>
<!-- JavaScript is called by OnChange attribute -->
<input type="file" name="Filedata" id="Filedata" onChange="jsUpload2(this)">
</form>
<iframe name="iframe-upload2" style="width: 400px; height: 100px; display: none;"></iframe>

</div>
<?php
echo "<table>"
." <tr><td><B>Url: </B></td><td><input id=urlAdd name=urlAdd size=40 value=http://www.google.fr /></td></tr>"
." <tr><td><B>Titre: </B></td><td><input id=titreAdd name=titreAdd size=40 value=Mon_titre /></td></tr>"
." <tr><td><B>Descr: </B></td><td><input id=descrAdd name=descrAdd size=40 value=Courte_Description /></td></tr>"
." <tr><td colspan=2><input type=checkbox id=statut  onclick=javascript:checkStatus(); />&nbsp;Visible par tous (publique)</td></tr>"
." <tr><td colspan=2><input type=checkbox id=statutP onclick=javascript:checkStatusP();  />&nbsp;Visible par profs/administratifs</td></tr>"
." <tr><td colspan=2><input type=checkbox id=RSS  /> La ressource est un flux RSS</td></tr></table>"
//."<tr><td colspan=2><input type=checkbox id=siteTV  /> La ressource est une vid&eacute;o siteTV <br /> &nbsp;(mettre le jeton de la vid&eacute;o dans la case URL<br />&nbsp; example: 0523.0026.00 )</td></tr></table>"
."<input type=\"hidden\" name=\"filename\" id=\"filename\">";
?>


<h3>Miniature:.
<A href="#" onclick="javascript:check_vignette();">Vérifier</A></h3>
<input id="choix_mini" name="choix_mini" value="rien" checked type=radio href=# onclick="javascript:gen_clean();">Aucune (animation - widget)<BR />
<input id="choix_mini" name="choix_mini" value="thumbalizr" type=radio href=# onclick="javascript:gen_vignette();">Générer la vignette sur thumbalizr.org<BR />
<input id="choix_mini" name="choix_mini" value="upload" type=radio href=# onclick="javascript:gen_upload();">Uploader la vignette<BR />

<div id="upload_panel">
<form action="<?=$PHP_SELF?>" target="upload_iframe" method="post" enctype="multipart/form-data">
<input type="hidden" name="fileframe" value="true">

<label for="file">Miniature:</label><br>
<!-- JavaScript is called by OnChange attribute -->
<input type="file" name="file" id="file" onChange="jsUpload(this)">
</form>
</div>

<iframe name="upload_iframe" style="width: 400px; height: 100px; display: none;"></iframe>

</body>
</html>
