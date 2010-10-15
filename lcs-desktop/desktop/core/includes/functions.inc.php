<?php

function createThumbs( $pathToImages, $pathToThumbs, $thumbWidth=150 )
{
  // open the directory
  $dir = opendir( $pathToImages );

  // loop through it, looking for any/all JPG files:
  while (false !== ($fname = readdir( $dir ))) {
    // parse path for the extension
    $info = pathinfo($pathToImages . $fname);
    // continue only if this is a JPEG image
    if ( strtolower($info['extension']) == 'jpg' )
    {
    	if(is_file($pathToThumbs . $fname)) {return;}
      echo "Creating thumbnail for {$fname} <br />";

      // load image and get image size
      $img = imagecreatefromjpeg( "{$pathToImages}{$fname}" );
      $width = imagesx( $img );
      $height = imagesy( $img );

      // calculate thumbnail size
      $new_width = $thumbWidth;
      $new_height = floor( $height * ( $thumbWidth / $width ) );

      // create a new temporary image
      $tmp_img = imagecreatetruecolor( $new_width, $new_height );

      // copy and resize old image into new image
      imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

      // save thumbnail into a file
      imagejpeg( $tmp_img, "{$pathToThumbs}{$fname}" );
    }
  }
  // close the directory
  closedir( $dir );
}
// call createThumb function and pass to it as parameters the path
// to the directory that contains images, the path to the directory
// in which thumbnails will be placed and the thumbnail's width.
// We are assuming that the path will be a relative path working
// both in the filesystem, and through the web for links
// ** createThumbs("upload/","upload/thumbs/",100);**


function alt_stat($file) {
 
 clearstatcache();
 $ss=@stat($file);
 if(!$ss) return false; //Couldnt stat file
 
 $ts=array(
  0140000=>'ssocket',
  0120000=>'llink',
  0100000=>'-file',
  0060000=>'bblock',
  0040000=>'ddir',
  0020000=>'cchar',
  0010000=>'pfifo'
 );
 
 $p=$ss['mode'];
 $t=decoct($ss['mode'] & 0170000); // File Encoding Bit
 
 $str =(array_key_exists(octdec($t),$ts))?$ts[octdec($t)]{0}:'u';
 $str.=(($p&0x0100)?'r':'-').(($p&0x0080)?'w':'-');
 $str.=(($p&0x0040)?(($p&0x0800)?'s':'x'):(($p&0x0800)?'S':'-'));
 $str.=(($p&0x0020)?'r':'-').(($p&0x0010)?'w':'-');
 $str.=(($p&0x0008)?(($p&0x0400)?'s':'x'):(($p&0x0400)?'S':'-'));
 $str.=(($p&0x0004)?'r':'-').(($p&0x0002)?'w':'-');
 $str.=(($p&0x0001)?(($p&0x0200)?'t':'x'):(($p&0x0200)?'T':'-'));
 
 $s=array(
 'perms'=>array(
  'umask'=>sprintf("%04o",@umask()),
  'human'=>$str,
  'octal1'=>sprintf("%o", ($ss['mode'] & 000777)),
  'octal2'=>sprintf("0%o", 0777 & $p),
  'decimal'=>sprintf("%04o", $p),
  'fileperms'=>@fileperms($file),
  'mode1'=>$p,
  'mode2'=>$ss['mode']),
 
 'owner'=>array(
  'fileowner'=>$ss['uid'],
  'filegroup'=>$ss['gid'],
  'owner'=>
  (function_exists('posix_getpwuid'))?
  @posix_getpwuid($ss['uid']):'',
  'group'=>
  (function_exists('posix_getgrgid'))?
  @posix_getgrgid($ss['gid']):''
  ),
 
 'file'=>array(
  'filename'=>$file,
  'realpath'=>(@realpath($file) != $file) ? @realpath($file) : '',
  'dirname'=>@dirname($file),
  'basename'=>@basename($file)
  ),

 'filetype'=>array(
  'type'=>substr($ts[octdec($t)],1),
  'type_octal'=>sprintf("%07o", octdec($t)),
  'is_file'=>@is_file($file),
  'is_dir'=>@is_dir($file),
  'is_link'=>@is_link($file),
  'is_readable'=> @is_readable($file),
  'is_writable'=> @is_writable($file)
  ),
 
 'device'=>array(
  'device'=>$ss['dev'], //Device
  'device_number'=>$ss['rdev'], //Device number, if device.
  'inode'=>$ss['ino'], //File serial number
  'link_count'=>$ss['nlink'], //link count
  'link_to'=>($s['type']=='link') ? @readlink($file) : ''
  ),
 
 'size'=>array(
  'size'=>$ss['size'], //Size of file, in bytes.
  'blocks'=>$ss['blocks'], //Number 512-byte blocks allocated
  'block_size'=> $ss['blksize'] //Optimal block size for I/O.
  ),
 
 'time'=>array(
  'mtime'=>$ss['mtime'], //Time of last modification
  'atime'=>$ss['atime'], //Time of last access.
  'ctime'=>$ss['ctime'], //Time of last status change
  'accessed'=>@date('d M Y H:i:s',$ss['atime']),
  'modified'=>@date('d M Y H:i:s',$ss['mtime']),
  'created'=>@date('d M Y H:i:s',$ss['ctime'])
  ),
 );
 
 clearstatcache();
 return $s;
}


/**
 * Checks that an URL is valid and really exists (checking with a basic ping).
 * @param $url Address to check.
 * @param $required Required field?
 * @return string
 */
function ValidateUrl($url, $required = false)
{
	if(empty($url))
	{
		if(!$required) return '';
		else throw new Exception('Please fill in all mandatory fields.');
	}
	else
	{
		$regex = '#(((http|ftp|https|ftps)://)|(www\.))+(([a-zA-Z0-9\._-]+\.[a-zA-Z]{2,6})|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(/[a-zA-Z0-9\&amp;%_\./-~-]*)?#';

		// Checking only syntax
		if(preg_match($regex, $url))
		{
			// And checking that the website really exists.
			$curl = curl_init($url);

			// Setting the URL.
			curl_setopt($curl, CURLOPT_URL, $url);
			// We want to read the header.
			curl_setopt($curl, CURLOPT_HEADER, 1);
			// We do not need the page content.
			curl_setopt($curl, CURLOPT_NOBODY, 1);
			// Do not display the page, just return us its content.
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

			$datas = curl_exec($curl);

			curl_close($curl);

			if(!empty($datas)) return $url;
//			else throw new Exception('Unable to join site at '.$url.'.');
		}
//		else throw new Exception('Please enter a valid address.');
	}
	
}

/**
 * Retourne les infos utilisateur sous plusieurs formes
 * - html(array(par defaut), html, json, xml,
 **/
function lcsListGroups($groups,$fGpSt,$fGpEn,$fClSt,$fSufXTi,$fSufX,$fPrefXTiSt,$fPrefXTiEn,$fPrefX,$fGsSt,$fGsMi,$fGsEn)
{
		$lst = "Classe Cours Equipe Matiere";
    	$i=$co=$ma=$eq=$cl=0;
     	$t_prefX='';
     	for ($loop=0; $loop < count ($groups) ; $loop++) {
     		$g=$ng=$groups[$loop]["cn"];
     		$prefX=explode("_", $g);
     		if($g==="Administratifs" || $g==="Eleves" || $g==="Profs") {
     			$ar["group"]=$g;
      			$jsGp.=$fGpSt.$g.$fGpEn;
    		}
     		else if($prefX[0]==="Classe"){
      			$jsCl.=$fClSt.preg_replace("/Classe_/", "", $g).$fClEn;
     		}
     		else if($g!="Agenda_Eleve"){
     			if(preg_match("/".$prefX[0]."/", $lst)) {
     				$ng=preg_replace("/".$prefX[0]."_/", "", $g);
     				$t_prefX!=''&&($t_prefX!=$prefX[0])?$js_sufX=$fSufXTi:$js_sufX=$fSufX;
     				$t_prefX!=$prefX[0]?$js_prefX=$js_sufX.$fPrefXTiSt.$prefX[0].$fPrefXTiEn:$js_prefX=$fPrefX;
     			}
				$ar['groups'][]=$g;
      			$jsGs.=$js_prefX.$fGsSt.numToAZ($i).$fGsMi.$ng.$fGsEn;
     			
     			$t_prefX=$prefX[0];
     			$i++;
     		}
     	}
     	return $jsGp.$jsCl.$jsGs;
}

function lcsUserInfos($login, $f="json")
{
	list($user, $groups)=people_get_variables($login, true);
   	$ar=array("fullname"=>$user["fullname"],"description"=>$user["description"],"group"=>'',"groups"=>array());
 	$jsSt="{\"data\": {\"nom\": \"".$user["fullname"]."\",\"description\": \"".$user["description"]."\"";
   	$htEn="</ul>\n";
   	$jsEn="}}}";
   	$xmSt="<response>\n\t<fullname>".$user["fullname"]."</fullname>\n";
	if ($user["description"]) $lst = "<p>".$user["description"]."</p>";
	if ( count($groups) ) {
		switch($f) {
			case "arra" : 
				$fEn='';
				break;
			case "html" : 
				$fSt="<ul>\n\t<li class=\"fullname\"><strong>Nom : </strong>".$user["fullname"]."</li>\n"
				."\t<li class=\"description\"><strong>Description : </strong>".$user["description"]."</li>\n";
				$fEn="</ul>";
				$fGpSt="<li class=\"group\"><strong>Groupe principal : </strong>";
				$fClSt="<li class=\"classe\"><strong>Classe : <strong>";
				$fGsSt="\n<li class=\"";
				$fGsMi="\">";
				$fPrefXTiSt="<li class=\"groups\"><strong>";
				$fPrefXTiEn=" : </strong><ul><li>";
				$fSufXTi="</ul>";
				$fPrefX="</li>";
				$fSufX='';
				$fClEn=$fGpEn=$fGsEn="</li>";
				break;
			case "json" :
				$fSt="{\"data\": {\"nom\": \"".$user["fullname"]."\",\"description\": \"".$user["description"]."\"";
				$fEn="}}}";
				$fGpSt=", \"Groupe principal\" : \"";
				$fClSt=", \"Classe\" : \"";
				$fGsSt="\"";
				$fGsMi="\":\"";
				$fPrefXTiSt=",\"";
				$fPrefXTiEn="\": {";
				$fSufXTi="}";
				$fPrefX=",";
				$fSufX='';
				$fClEn=$fGpEn=$fGsEn="\"";
				break;
			case "xml" :
				$fSt="<response>\n\t<fullname>".$user["fullname"]."</fullname>\n"
				."\t<description>".$user["description"]."</description>\n";
				$fEn="</response>";
				$fGpSt="<group>";
				$fClSt="<classe>";
				$fGsSt="\n< class=\"";
				$fGsMi="\">";
				$fPrefXTiSt="<li class=\"groups\"><strong>";
				$fPrefXTiEn=" : </strong><ul><li>";
				$fSufXTi="</titiul>";
				$fPrefX="</toto>";
				$fSufX='';
				$fGsEn="</toto>";
				$fClEn="</classe>";
				$fGpEn="</group>";
				break;
			default :
				$fSt=array();
				$fEn='';
		}
		$rGroups=lcsListGroups($groups,$fGpSt,$fGpEn,$fClSt,$fSufXTi,$fSufX,$fPrefXTiSt,$fPrefXTiEn,$fPrefX,$fGsSt,$fGsMi,$fGsEn);
		$rRes=$fSt.$rGroups.$fEn;
//	return htmlspecialchars($rRes, ENT_QUOTES);
	return $rRes;
	}
}

function createThumbnail($imageDirectory, $imageName, $thumbDirectory, $thumbWidth)
{
$srcImg = imagecreatefromjpeg("$imageDirectory/$imageName");
$origWidth = imagesx($srcImg);
$origHeight = imagesy($srcImg);
//echo "owidth $origWidth <br/>";
//echo "ohight $origHeight";

$ratio = $origHeight/ $origWidth;
$thumbHeight = $thumbWidth * $ratio;

//echo "ratio= $ratio <br/>";

//echo "thumb width $thumbWidth <br/>";
//echo "thumb hight $thumbHeight";

$thumbImg = imagecreatetruecolor($thumbWidth, $thumbHeight);


imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $origWidth, $origHeight);

imagejpeg($thumbImg, "$thumbDirectory/$imageName");
}


//createThumbnail("desktop/images/misc","Virtual_Octopus.jpg","desktop/images/misc/thumbs",150);			

?>