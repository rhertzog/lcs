<?php
/*
attach.class.php
Code original de ce fichier : Eric FELDSTEIN
Copyright (c) 2002, Hendrik Mans <hendrik@mans.de>
Copyright 2002, 2003 David DELON
Copyright 2002, 2003 Charles NEPOTE
Copyright  2003,2004  Eric FELDSTEIN
Copyright  2003  Jean-Pascal MILCENT
Copyright  2006  Pierre Lachance
All rights reserved.
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:
1. Redistributions of source code must retain the above copyright
notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
notice, this list of conditions and the following disclaimer in the
documentation and/or other materials provided with the distribution.
3. The name of the author may not be used to endorse or promote products
derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
# Classe de gestion de l'action {{attach}}
# voir actions/attach.php ppour la documentation
# copyrigth Eric Feldstein 2003-2004

class attach {
   var $wiki = '';					//objet wiki courant
   var $attachConfig = array();	//configuration de l'action
   var $file = '';					//nom du fichier
   var $haut = '';					//hauteur swf
   var $large = '';					//largeur swf
   var $separateur = '';				//s&eacute;parateur pour csv
   var $entete = '';					//entete pour csv
   var $voir = '';					//pour afficher contenu fichier
   var $style = '';					//pour choisir le player flash
   var $desc = '';					//description du fichier
   var $link = '';					//url de lien (image sensible)
   var $isPicture = 0;				//indique si c'est une image
   var $classes = '';				//classe pour afficher une image
   var $attachErr = '';				//message d'erreur
   var $pageId = 0;					//identifiant de la page
   var $isSafeMode = false;		//indicateur du safe mode de PHP
   /**
   * Constructeur. Met les valeurs par defaut aux param&egrave;tres de configuration
   */
	function attach(&$wiki){ //fonction attach avec r��criture d'url

   	$this->wiki = $wiki;
		$this->attachConfig = $this->wiki->GetConfigValue("attach_config");
		if (empty($this->attachConfig["ext_images"])) $this->attachConfig["ext_images"] = "gif|jpeg|png|jpg";
		if (empty($this->attachConfig["ext_script"])) $this->attachConfig["ext_script"] = "php|php3|asp|asx|vb|vbs|js";
		if (empty($this->attachConfig['upload_path'])) $this->attachConfig['upload_path'] = 'upload_dir';
		if (empty($this->attachConfig['update_symbole'])) $this->attachConfig['update_symbole'] = "<img src=\"". $this->wiki->config[url_site]."/images/update.png\" alt=\"handout\" class=\"handout\" />";
		if (empty($this->attachConfig['max_file_size'])) $this->attachConfig['max_file_size'] = 1024*30000;	//100ko max
		if (empty($this->attachConfig['fmDelete_symbole'])) $this->attachConfig['fmDelete_symbole'] = "<img src=\"". $this->wiki->config[url_site]."/images/supprime.png\" />";
		if (empty($this->attachConfig['fmRestore_symbole'])) $this->attachConfig['fmRestore_symbole'] = "<img src=\"". $this->wiki->config[url_site]."/images/undo.png\" />";
		if (empty($this->attachConfig['fmTrash_symbole'])) $this->attachConfig['fmTrash_symbole'] = "<img src=\"". $this->wiki->config[url_site]."/images/poubelle.png\" />";
		$this->isSafeMode = ini_get("safe_mode");
	}
	
	
	
/******************************************************************************
*	FONCTIONS UTILES
*******************************************************************************/
	/**
	* Cr�ation d'une suite de r�pertoires r�cursivement
	*/
	function mkdir_recursif ($dir) {
		if (strlen($dir) == 0) return 0;
		if (is_dir($dir)) return 1;
		elseif (dirname($dir) == $dir) return 1;
		return ($this->mkdir_recursif(dirname($dir)) and mkdir($dir,0755));
	}
	/**
	* Renvois le chemin du script
	*/
	function GetScriptPath () {
		if (preg_match("/.(php)$/i",$_SERVER["PHP_SELF"])){
			$a = explode('/',$_SERVER["PHP_SELF"]);
			$a[count($a)-1] = '';
			$path = implode('/',$a);
		}else{
			$path = $_SERVER["PHP_SELF"];
		}
		return !empty($_SERVER["HTTP_HOST"])? 'http://'.$_SERVER["HTTP_HOST"].$path : 'http://'.$_SERVER["SERVER_NAME"].$path ;
	}
	/**
	* Calcul le repertoire d'upload en fonction du safe_mode
	*/
	function GetUploadPath(){
		if ($this->isSafeMode) {
			$path = $this->attachConfig['upload_path'];
		}else{
         $path = $this->attachConfig['upload_path'].'/'.$this->wiki->GetPageTag();
			if (! is_dir($path)) $this->mkdir_recursif($path);
		}
		return $path;
	}
	/**
	* Calcule le nom complet du fichier attach� en fonction du safe_mode, du nom et de la date de
	* revision la page courante.
	* Le nom du fichier "mon fichier.ext" attache � la page "LaPageWiki"sera :
	*  mon_fichier_datepage_update.ext
	*     update : date de derniere mise a jour du fichier
	*     datepage : date de revision de la page � laquelle le fichier a ete li�/mis a jour
	*  Si le fichier n'est pas une image un '_' est ajoute : mon_fichier_datepage_update.ext_
	*  Selon la valeur de safe_mode :
	*  safe_mode = on : 	LaPageWiki_mon_fichier_datepage_update.ext_
	*  safe_mode = off: 	LaPageWiki/mon_fichier_datepage_update.ext_ avec "LaPageWiki" un sous-repertoire du r�pertoire upload
	*/
	function GetFullFilename($newName = false){
		$pagedate = $this->convertDate($this->wiki->page['time']);
		//decompose le nom du fichier en nom+extension
		if (preg_match('`^(.*)\.(.*)$`', str_replace(' ','_',$this->file), $match)){
			list(,$file['name'],$file['ext'])=$match;
			if(!$this->isPicture()) $file['ext'] .= '_';
		}else{
			return false;
		}
		//recuperation du chemin d'upload
		$path = $this->GetUploadPath($this->isSafeMode);
		//generation du nom ou recherche de fichier ?
		if ($newName){
			$full_file_name = $file['name'].'_'.$pagedate.'_'.$this->getDate().'.'.$file['ext'];
			if($this->isSafeMode){
				$full_file_name = $path.'/'.$this->wiki->GetPageTag().'_'.$full_file_name;
			}else{
				$full_file_name = $path.'/'.$full_file_name;
			}
		}else{
			//recherche du fichier
			if($this->isSafeMode){
				//TODO Recherche dans le cas ou safe_mode=on
				$searchPattern = '`^'.$this->wiki->GetPageTag().'_'.$file['name'].'_\d{14}_\d{14}\.'.$file['ext'].'$`';
			}else{
				$searchPattern = '`^'.$file['name'].'_\d{14}_\d{14}\.'.$file['ext'].'$`';
			}
			$files = $this->searchFiles($searchPattern,$path);

			$unedate = 0;
			foreach ($files as $file){
				//recherche du fichier qui une datepage <= a la date de la page
				if($file['datepage']<=$pagedate){
					//puis qui a une dateupload la plus grande
					if ($file['dateupload']>$unedate){
						$theFile = $file;
						$unedate = $file['dateupload'];
					}
				}
			}
			if (is_array($theFile)){
				$full_file_name = $path.'/'.$theFile['realname'];
			}
		}
		return $full_file_name;
	}
	/**
	* Test si le fichier est une image
	*/
	function isPicture(){
		return preg_match("/.(".$this->attachConfig["ext_images"].")$/i",$this->file)==1;
	}
	function isFreemind(){//pour freemind
		if ($this->wiki->config[view_freemind]==1 && $this->voir==oui) return preg_match("/.(mm)$/i",$this->file)==1;
	}
	function isGeogebra(){//pour geogebra
		if ($this->wiki->config[view_geogebra]==1 && $this->voir==oui) return preg_match("/.(ggb)$/i",$this->file)==1;
	}
	function isGeonext(){//pour geonext
		if ($this->wiki->config[view_geonext]==1 && $this->voir==oui) return preg_match("/.(gxt)$/i",$this->file)==1;
	}
	function isGeolabo(){//pour geolabo
		if ($this->wiki->config[view_geolabo]==1 && $this->voir==oui) return preg_match("/.(glb)$/i",$this->file)==1;
	}
	function isFlash(){//pour animation flash
		if ($this->wiki->config[view_flash]==1 && $this->voir==oui) return preg_match("/.(swf)$/i",$this->file)==1;
	}
	function isMp3(){//pour mp3 dans player flash
		if ($this->wiki->config[view_mp3]==1 && $this->voir==oui) return preg_match("/.(mp3)$/i",$this->file)==1;
	}
	function isCsv(){//pour csv dans un tableau
		if ($this->wiki->config[view_csv]==1 && $this->voir==oui)return preg_match("/.(csv)$/i",$this->file)==1;
	}
	function isFlv(){//pour video flv
		if ($this->wiki->config[view_flv]==1 && $this->voir==oui)return preg_match("/.(flv)$/i",$this->file)==1;
	}
	function isObj(){//pour objet 3D
		if ($this->wiki->config[view_obj]==1 && $this->voir==oui)return preg_match("/.(obj)$/i",$this->file)==1;
	}
	function isSqueak(){//pour projet squeak
		if ($this->wiki->config[view_squeak]==1 && $this->voir==oui)return preg_match("/.(pr)$/i",$this->file)==1;
	}
	function isScratch(){//pour projet scratch
		if ($this->wiki->config[view_scratch]==1 && $this->voir==oui)return preg_match("/.(sb)$/i",$this->file)==1;
	}
	function isIeP(){//pour projet intrumenpoche
		if ($this->wiki->config[view_iep]==1 && $this->voir==oui)return preg_match("/.(xml)$/i",$this->file)==1;
	}
	/**
	* Renvoie la date courante au format utilise par les fichiers
	*/
	function getDate(){
		return date('YmdHis');
	}
	/**
	* convertie une date yyyy-mm-dd hh:mm:ss au format yyyymmddhhmmss
	*/
	function convertDate($date){
		$date = str_replace(' ','', $date);
		$date = str_replace(':','', $date);
		return str_replace('-','', $date);
	}
	/**
	* Parse une date au format yyyymmddhhmmss et renvoie un tableau assiatif
	*/
	function parseDate($sDate){
		$pattern = '`^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$`';
		$res = '';
		if (preg_match($pattern, $sDate, $m)){
			//list(,$res['year'],$res['month'],$res['day'],$res['hour'],$res['min'],$res['sec'])=$m;
			$res = $m[1].'-'.$m[2].'-'.$m[3].' '.$m[4].':'.$m[5].':'.$m[6];
		}
		return ($res?$res:false);
	}
	/**
	* Decode un nom long de fichier
	*/
	function decodeLongFilename($filename){
		$afile = array();
		$afile['realname'] = basename($filename);
		$afile['size'] = filesize($filename);
		$afile['path'] = dirname($filename);
		if(preg_match('`^(.*)_(\d{14})_(\d{14})\.(.*)(trash\d{14})?$`', $afile['realname'], $m)){
			$afile['name'] = $m[1];
			//suppression du nom de la page si safe_mode=on
			if ($this->isSafeMode){
				$afile['name'] = preg_replace('`^('.$this->wiki->tag.')_(.*)$`i', '$2', $afile['name']);
			}
			$afile['datepage'] = $m[2];
			$afile['dateupload'] = $m[3];
			$afile['trashdate'] = preg_replace('`(.*)trash(\d{14})`', '$2', $m[4]);
			//suppression de trashxxxxxxxxxxxxxx eventuel
			$afile['ext'] = preg_replace('`^(.*)(trash\d{14})$`', '$1', $m[4]);
			$afile['ext'] = rtrim($afile['ext'],'_');
			//$afile['ext'] = rtrim($m[4],'_');
		}
		return $afile;
	}
	/**
	* Renvois un tableau des fichiers correspondant au pattern. Chaque element du tableau est un
	* tableau associatif contenant les informations sur le fichier
	*/
	function searchFiles($filepattern,$start_dir){
		$files_matched = array();
		$start_dir = rtrim($start_dir,'\/');
		$fh = opendir($start_dir);
		while (($file = readdir($fh)) !== false) {
			if (strcmp($file, '.')==0 || strcmp($file, '..')==0 || is_dir($file)) continue;
			if (preg_match($filepattern, $file)){
				$files_matched[] = $this->decodeLongFilename($start_dir.'/'.$file);
			}
		}
		return $files_matched;
	}
/******************************************************************************
*	FONCTIONS D'ATTACHEMENTS
*******************************************************************************/
	/**
	* Test les param&egrave;tres pass� � l'action
	*/
	function CheckParams(){
		//recuperation des parametres necessaire
		$this->file = $this->wiki->GetParameter("attachfile");
		if (empty($this->file)) $this->file = $this->wiki->GetParameter("fichier");
		
		$this->desc = $this->wiki->GetParameter("attachdesc");
		if (empty($this->desc)) $this->desc = $this->wiki->GetParameter("description");
		$this->haut = $this->wiki->GetParameter("haut");
		if (empty($this->haut)) $this->haut = $this->wiki->GetParameter("haut");
		$this->large = $this->wiki->GetParameter("large");
		if (empty($this->large)) $this->large = $this->wiki->GetParameter("large");

		$this->separateur = $this->wiki->GetParameter("separateur");
		if (empty($this->separateur)) $this->separateur = $this->wiki->GetParameter("separateur");

		$this->entete = $this->wiki->GetParameter("entete");
		if (empty($this->entete)) $this->entete = $this->wiki->GetParameter("entete");

		$this->voir = $this->wiki->GetParameter("voir");
		if (empty($this->voir)) $this->voir = "oui";

		$this->style = $this->wiki->GetParameter("style");
		if (empty($this->style)) $this->style = "complet";

		$this->link = $this->wiki->GetParameter("attachlink");//url de lien - uniquement si c'est une image
		if (empty($this->link)) $this->link = $this->wiki->GetParameter("lien");
		//test de validit� des parametres
		if (empty($this->file)){
			$this->attachErr = $this->wiki->Format("//action attacher : param&egrave;tre **fichier** manquant//---");
		}
		if ($this->isPicture() && empty($this->desc)){
			$this->attachErr .= $this->wiki->Format("//action attacher : param&egrave;tre **description** obligatoire pour une image//---");
		}
		if ($this->wiki->GetParameter("class")) {
   		$array_classes = explode(" ", $this->wiki->GetParameter("class"));
   		foreach ($array_classes as $c) { $this->classes = $this->classes . "attach_" . $c . " "; }
   		$this->classes = trim($this->classes);
		}
	}
	/**
	* Affiche le fichier li� comme une image
	*/

	function showAsImage($fullFilename){
		$haut=$this->haut;
		$large=$this->large;
		$size = getimagesize("$fullFilename");

		if ((!$haut) &&  (!$large)) { $haut = $size[1]; $large = $size[0];}

		elseif ((!$haut) &&  ($large)) { 
			$haut = $size[1];
			$percentage = ($large / $size[0]);
			$haut = round($haut * $percentage);

		}
		elseif (($haut) &&  (!$large)) { 
			$large = $size[0];
			$percentage = ($haut / $size[1]);
			$large = round($large * $percentage);
		}

		if ($this->wiki->config[header_action] == "header_m") {
			include_once("jquery/Mobile_Detect.php");
			$detect = new Mobile_Detect();
			$target="300";
			if (($large > $target) && ($detect->isMobile())) {
				$percentage = ($target / $large);
				$large = round($large * $percentage);
				$haut = round($haut * $percentage);	
				}
		}

		//c'est une image : balise <IMG..../>
		$img =	"<img src=\"".$this->GetScriptPath().$fullFilename."\" ".
					"alt=\"".$this->desc.($this->link?"\nLien vers: $this->link":"")."\" width=\"$large\" height=\"$haut\" />";
		//test si c'est une image sensible
		if(!empty($this->link)){
			//c'est une image sensible
			//test si le lien est un lien interwiki
			if (preg_match("/^([A-Z][A-Z,a-z]+)[:]([A-Z,a-z,0-9]*)$/s", $this->link, $matches))
			{  //modifie $link pour �tre un lien vers un autre wiki
				$this->link = $this->wiki->GetInterWikiUrl($matches[1], $matches[2]);
			}
			//calcule du lien
			$output = $this->wiki->Format('[['.$this->link." $this->file]]");
			$output = preg_replace("/>$this->file</",">$img<",$output);//insertion du tag <img...> dans le lien
		}else{
			//ce n'est pas une image sensible
			$output = $img;
		}
	if (!$this->classes) {$laclass="nul";} else {$laclass=$this->classes;}
		$output = ($this->classes?"<div class=\"$laclass\">$output</div>":$output);
		echo $output;
		$this->showUpdateLink();
	}
	/**
	* Affiche le fichier li� comme un lien
	*/
	function showAsLink($fullFilename){
		$url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
		echo '<a href="'.$url.'">'.($this->desc?$this->desc:$this->file)."</a>";
		$this->showUpdateLink();
	}
	/**
	* Affiche le lien de mise � jour
	*/
	function showUpdateLink(){
		echo	" <a href=\"".
				$this->wiki->href("upload",$this->wiki->GetPageTag(),"file=$this->file").
				"\" title='Mise � jour'>".$this->attachConfig['update_symbole']."</a>";
	}
	/**
	* Affiche un liens comme un fichier inexistant
	*/
	function showFileNotExits(){
		echo $this->file."<a href=\"".$this->wiki->href("upload",$this->wiki->GetPageTag(),"file=$this->file")."\">?</a>";
	}
	/**
	* Affiche l'attachement
	*/
	function doAttach(){
		$this->CheckParams();
		if ($this->attachErr) {
			echo $this->attachErr;
			return;
		}
		$fullFilename = $this->GetFullFilename();
		//test d'existance du fichier
		if((!file_exists($fullFilename))||($fullFilename=='')){
			$this->showFileNotExits();
			return;
		}
		//le fichier existe : affichage en fonction du type
		if($this->isPicture() && $this->voir==oui){
			$this->showAsImage($fullFilename);
		}
			elseif($this->isFreemind()){//pour freemind
			$this->showAsMindmap($fullFilename);
		}
			elseif($this->isGeogebra()){//pour geogebra
			$this->showAsGeogebra($fullFilename);
		}
			elseif($this->isGeonext()){//pour geonext
			$this->showAsGeonext($fullFilename);
		}
			elseif($this->isGeolabo()){//pour geolabo
			$this->showAsGeolabo($fullFilename);
		}
			elseif($this->isFlash()){//pour animation flash
			$this->showAsFlash($fullFilename);
		}
			elseif($this->isMp3() && $this->style==complet){
			//pour lecteur mp3
			$this->showAsMp3($fullFilename);
		}
			elseif($this->isMp3() && $this->style==mini){
			//pour lecteur mp3
			$this->showAsMp3mini($fullFilename);
		}
			elseif($this->isCsv()){//pour csv
			$this->showAsCsv($fullFilename);
		}
			elseif($this->isFlv()){
			//pour lecteur flv
			$this->showAsFlv($fullFilename);
		}
			elseif($this->isObj() && $this->style==complet){
			//pour lecteur obj
			$this->showAsObj($fullFilename);
		}
			elseif($this->isObj() && $this->style==mini){
			//pour lecteur objmin (sans les controles)
			$this->showAsObjMini($fullFilename);
		}
			elseif ($this->isSqueak()){
			$this->showAsSqueak($fullFilename);
		}
			elseif ($this->isScratch()){
			$this->showAsScratch($fullFilename);
		}
			elseif ($this->isIeP()){
			$this->showAsIeP($fullFilename);
		}
			else{
			$this->showAsLink($fullFilename);
		}
	}
/******************************************************************************
*	FONTIONS D'UPLOAD DE FICHIERS
*******************************************************************************/
	/**
	* Traitement des uploads
	*/
	function doUpload(){
		$HasAccessWrite=$this->wiki->HasAccess("write");
		if ($HasAccessWrite){
         switch ($_SERVER["REQUEST_METHOD"]) {
         	case 'GET' : $this->showUploadForm(); break;
         	case 'POST': $this->performUpload(); break;
         	default : echo $this->wiki->Format("//Methode de requete invalide//---");
			}
		}else{
			echo $this->wiki->Format("//Vous n'avez pas l'acc&egrave;s en &eacute;criture � cette page//---");
			echo $this->wiki->Format("Retour � la page ".$this->wiki->GetPageTag());
		}
	}
	/**
	* Formulaire d'upload
	*/
	function showUploadForm(){
		$repimage = $this->wiki->GetConfigValue('base_url');
		echo "<img src=\"".$repimage."images/importer.png\" alt=\"\" style=\"float: right;\" />";
		echo $this->wiki->Format("====Formulaire d'envois de fichier====\n#R#IMP: Avez-vous mis l'extension du fichier?#R#--- ---");

		$this->file = $_GET['file'];
		echo 	$this->wiki->Format("**Envois du fichier $this->file :**\n")
				."<form enctype=\"multipart/form-data\" name=\"frmUpload\" method=\"POST\" action=\"".$_SERVER["PHP_SELF"]."\">\n"
				."	<input type=\"hidden\" name=\"wiki\" value=\"".$this->wiki->GetPageTag()."/upload\" />\n"
				."	<input TYPE=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"".$this->attachConfig['max_file_size']."\" />\n"
				."	<input type=\"hidden\" name=\"file\" value=\"$this->file\" />\n"
				."	<input type=\"file\" name=\"upFile\" size=\"50\" /><br />\n"
				."	<input type=\"submit\" value=\"Envoyer\" />\n"
				."</form>\n";
	}
	/**
	* Execute l'upload
	*/
	function performUpload(){
		$this->file = $_POST['file'];

		$destFile = $this->GetFullFilename(true);	//nom du fichier destination
		//test de la taille du fichier recu
		if($_FILES['upFile']['error']==0){
			$size = filesize($_FILES['upFile']['tmp_name']);
			if ($size > $this->attachConfig['max_file_size']){
				$_FILES['upFile']['error']=2;
			}
		}
		switch ($_FILES['upFile']['error']){
			case 0:
				$srcFile = $_FILES['upFile']['tmp_name'];
				if (move_uploaded_file($srcFile,$destFile)){
					chmod($destFile,0644);
					header("Location: ".$this->wiki->href("",$this->wiki->GetPageTag(),""));
				}else{
					echo $this->wiki->Format("//Erreur lors du d�placement du fichier temporaire//---");
				}
				break;
			case 1:
				echo $this->wiki->Format("//Le fichier t&eacute;l&eacute;charg&eacute; exc&egrave;de la taille de upload_max_filesize, configur&eacute; dans le php.ini.//---");
				break;
			case 2:
				echo $this->wiki->Format("//Le fichier t&eacute;l&eacute;charg&eacute; exc&egrave;de la taille de MAX_FILE_SIZE, qui a &eacute;t&eacute; sp&eacute;cifi&eacute;e dans le formulaire HTML.//---");
				break;
			case 3:
				echo $this->wiki->Format("//Le fichier n'a &eacute;t&eacute; que partiellement t&eacute;l&eacute;charg&eacute;.//---");
				break;
			case 4:
				echo $this->wiki->Format("//Aucun fichier n'a &eacute;t&eacute; t&eacute;l&eacute;charg&eacute;.//---");
				break;
		}
		echo $this->wiki->Format("Retour � la page ".$this->wiki->GetPageTag());
	}
/******************************************************************************
*	FUNCTIONS DE DOWNLOAD DE FICHIERS
*******************************************************************************/
	function doDownload(){
		$this->file = $_GET['file'];
		$fullFilename = $this->GetUploadPath().'/'.$this->file;
		if(!file_exists($fullFilename)){
			$fullFilename = $this->GetFullFilename();
			$dlFilename = $this->file;
			$size = filesize($fullFilename);
		}else{
			$file = $this->decodeLongFilename($fullFilename);
			$size = $file['size'];
			$dlFilename =$file['name'].'.'.$file['ext'];
		}

        header('Content-Type: application/octet-stream; name="' . $dlFilename . '"'); //This should work for the rest
        header('Content-Disposition: attachment; filename="'.$dlFilename.'"');



		readfile($fullFilename);
	}
/******************************************************************************
*	FONTIONS DU FILEMANAGER
*******************************************************************************/
	function doFileManager(){
		$do = $_GET['do']?$_GET['do']:'';
		switch ($do){
			case 'restore' :
				$this->fmRestore();
				$this->fmShow(true);
				break;
			case 'erase' :
				$this->fmErase();
				$this->fmShow(true);
				break;
			case 'del' :
				$this->fmDelete();
				$this->fmShow();
				break;
			case 'trash' :
				$this->fmShow(true); break;
			case 'emptytrash' :
				$this->fmEmptyTrash();	//pas de break car apres un emptytrash => retour au gestionnaire
			default :
				$this->fmShow();
		}
	}
	/**
	* Affiche la liste des fichiers
	*/
	function fmShow($trash=false){
		$fmTitlePage = $this->wiki->Format("====Gestion des fichiers attach&eacute;s �  la page ".$this->wiki->tag."====\n---");
		if($trash){
			//Avertissement
			$fmTitlePage .= '<div class="prev_alert">Les fichiers effac&eacute;s sur cette page le sont d&eacute;finitivement</div>';
			//Pied du tableau
			$url = $this->wiki->Link($this->wiki->tag,'filemanager','Gestion des fichiers');
      			$fmFootTable =	'<tfoot>'."\n".
      			'<tr>'."\n".
      			'<td colspan="6">'.$url.'</td>'."\n";
			$url = $this->wiki->Link($this->wiki->tag,'filemanager&do=emptytrash','Vider la poubelle');
      			$fmFootTable.=	'<td>'.$url.'</td>'."\n".
      			'</tr>'."\n".
      			'</tfoot>'."\n";
		}else{
			//pied du tableau
         	$url = '<a href="'.$this->wiki->href('filemanager',$this->wiki->GetPageTag(),'do=trash').'" title="Poubelle">'.$this->attachConfig['fmTrash_symbole']."</a>";
      		$fmFootTable =	'<tfoot>'."\n".
      				'<tr>'."\n".
      				'<td colspan="6">'.$url.'</td>'."\n".
      				'</tr>'."\n".
      				'</tfoot>'."\n";
		}
		//entete du tableau
		$fmHeadTable = '<thead>'."\n".
					'<tr>'."\n".
					'<td>&nbsp;</td>'."\n".
					'<td>Nom du fichier</td>'."\n".
					'<td>Nom r&eacute;el du fichier</td>'."\n".
					'<td>Taille</td>'."\n".
					'<td>R&eacute;vision de la page</td>'."\n".
					'<td>R&eacute;vison du fichier</td>'."\n";
		if($trash){
         		$fmHeadTable.= '<td>Suppression</td>'."\n";
		}
		$fmHeadTable.= '</tr>'."\n".
				'</thead>'."\n";
		//corps du tableau
		$files = $this->fmGetFiles($trash);
  		$files = $this->sortByNameRevFile($files);

		$fmBodyTable =	'<tbody>'."\n";
		$i = 0;
		foreach ($files as $file){
			$i++;
			$color= ($i%2?"tableFMCol1":"tableFMCol2");
			//lien de suppression
			if ($trash){
				$url = $this->wiki->href('filemanager',$this->wiki->GetPageTag(),'do=erase&file='.$file['realname']);
			}else{
				$url = $this->wiki->href('filemanager',$this->wiki->GetPageTag(),'do=del&file='.$file['realname']);
			}
			$dellink = '<a href="'.$url.'" title="Supprimer">'.$this->attachConfig['fmDelete_symbole']."</a>";
			//lien de restauration
			$restlink = '';
			if ($trash){
				$url = $this->wiki->href('filemanager',$this->wiki->GetPageTag(),'do=restore&file='.$file['realname']);
				$restlink = '<a href="'.$url.'" title="Restaurer">'.$this->attachConfig['fmRestore_symbole']."</a>";
			}

			//lien pour downloader le fichier
			$url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=".$file['realname']);
			$dlLink = '<a href="'.$url.'">'.$file['name'].'.'.$file['ext']."</a>";
			$fmBodyTable .= 	'<tr class="'.$color.'">'."\n".
				'<td>'.$dellink.' '.$restlink.'</td>'."\n".
				'<td>'.$dlLink.'</td>'."\n".
				'<td>'.$file['realname'].'</td>'."\n".
				'<td>'.$file['size'].'</td>'."\n".
				'<td>'.$this->parseDate($file['datepage']).'</td>'."\n".
				'<td>'.$this->parseDate($file['dateupload']).'</td>'."\n";
			if($trash){
         		$fmBodyTable.= '<td>'.$this->parseDate($file['trashdate']).'</td>'."\n";
			}
			$fmBodyTable .= 	'		</tr>'."\n";
		}
		$fmBodyTable .= '	</tbody>'."\n";
		//pied de la page
		$fmFooterPage = "---\n-----\n[[".$this->wiki->tag." Retour � la page ".$this->wiki->tag."]]\n";
		//affichage
		echo $fmTitlePage."\n";
		echo '<table class="tableFM" border="0" cellspacing="0">'."\n".$fmHeadTable.$fmFootTable.$fmBodyTable.'</table>'."\n";
		echo $this->wiki->Format($fmFooterPage);
	}
	/**
	* Renvoie la liste des fichiers
	*/
	function fmGetFiles($trash=false){
		$path = $this->GetUploadPath();
		if($this->isSafeMode){
			$filePattern = '^'.$this->wiki->GetPageTag().'_.*_\d{14}_\d{14}\..*';
		}else{
			$filePattern = '^.*_\d{14}_\d{14}\..*';
		}
		if($trash){
			$filePattern .= 'trash\d{14}';
		}else{
			$filePattern .= '[^(trash\d{14})]';
		}
		return $this->searchFiles('`'.$filePattern.'$`', $path);
	}
	/**
	* Vide la poubelle
	*/
	function fmEmptyTrash(){
		$files = $this->fmGetFiles(true);
		foreach ($files as $file){
			$filename = $file['path'].'/'.$file['realname'];
			if(file_exists($filename)){
				unlink($filename);
			}
		}
	}
	/**
	* Effacement d'un fichier dans la poubelle
	*/
	function fmErase(){
		$path = $this->GetUploadPath();
		$filename = $path.'/'.($_GET['file']?$_GET['file']:'');
		if (file_exists($filename)){
			unlink($filename);
		}
	}
	/**
	* Met le fichier a la poubelle
	*/
	function fmDelete(){
		$path = $this->GetUploadPath();
		$filename = $path.'/'.($_GET['file']?$_GET['file']:'');
		if (file_exists($filename)){
			$trash = $filename.'trash'.$this->getDate();
			rename($filename, $trash);
		}
	}
	/**
	* Restauration d'un fichier mis a la poubelle
	*/
	function fmRestore(){
		$path = $this->GetUploadPath();
		$filename = $path.'/'.($_GET['file']?$_GET['file']:'');
		if (file_exists($filename)){
			$restFile = preg_replace('`^(.*\..*)trash\d{14}$`', '$1', $filename);
			rename($filename, $restFile);
		}
	}
	/**
	* Tri tu tableau liste des fichiers par nom puis par date de revision(upload) du fichier, ordre croissant
	*/
	function sortByNameRevFile($files){
		if (!function_exists('ByNameByRevFile')){
			function ByNameByRevFile($f1,$f2){
				$f1Name = $f1['name'].'.'.$f1['ext'];
				$f2Name = $f2['name'].'.'.$f2['ext'];
				$res = strcasecmp($f1Name, $f2Name);
				if($res==0){
					//si meme nom => compare la revision du fichier
					$res = strcasecmp($f1['dateupload'], $f2['dateupload']);
				}
				return $res;
			}
		}
		usort($files,'ByNameByRevFile');
		return $files;
	}
	/**
	* Affiche le fichier freemind dans une boite.
	*/
	function showAsMindmap($fullFilename){//pour freemind
	$haut=$this->haut;
	$large=$this->large;
	if (!$haut) $haut = "650";
       	if (!$large) $large = "100%";
	$urlmst = $this->wiki->config[url_site];
	$mindmap_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");

		$output =
		"<script type=\"text/javascript\" src=\"".$urlmst."flashobject.js\"></script><br />\n".
		"<div id=\"flashcontent\">Flash plugin or Javascript are turned off. Activate both  and reload to view the mindmap</div>\n".
	
		"<script type=\"text/javascript\">\n".
		"// <![CDATA[\n".
		"function getMap(map){
		  var result=map;
		  var loc=document.location+'';
		  if(loc.indexOf(\".mm_\")>0 && loc.indexOf(\"?\")>0){
			result=loc.substring(loc.indexOf(\"?\")+1);
		  }
		  return result;
		}\n".
		"var fo = new FlashObject(\"".$urlmst."visorFreemind.swf\", \"visorFreeMind\", \"$large\", \"$haut\", 6, \"#9999ff\");\n".
		"fo.addParam(\"quality\", \"high\");\n".
		"fo.addParam(\"bgcolor\", \"#ffffff\");\n".
		"fo.addVariable(\"openUrl\", \"_blank\");\n".
		"fo.addVariable(\"initLoadFile\", \"".$urlmst."$fullFilename\");\n".
		"fo.addVariable(\"startCollapsedToLevel\",\"5\");\n".
		"fo.write(\"flashcontent\");\n".
		"// ]]></script>\n".
		"<br />\n".
		"<span class=\"floatr\"><a href=\"$mindmap_url\">T&eacute;l&eacute;charger ce r&eacute;seau de concept</a> :: Utiliser <a href=\"http://freemind.sourceforge.net/\">Freemind</a> pour l'&eacute;diter. Plein &eacute;cran: cliquer sur Imprimer ci-dessous.\n";
	
		print($output);
		$this->showUpdateLink();
		echo "</span><div style=\"clear:both;\"></div>";
	}

	/**
	* Affiche le fichier geogebra dans une boite.
	*/
	function showAsGeogebra($fullFilename){//pour geogebra 3.2
	$haut=$this->haut;
	$large=$this->large;
	if (!$haut) $haut = "450";
       	if (!$large) $large = "100%";
	$urlmst = $this->wiki->config[url_site];
	$geogebra_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
	
		$output =
		//Pour activer l'applet pour geogebra 4, remplacer geogebra/geogebra.jar par geogebra4/geogebra.jar dans la ligne ci-dessous, ajouter le r�pertoire geogebra4 � la racine de votre wiki (visionneurs).
		"<applet code=\"geogebra.GeoGebraApplet\" archive=\"".$urlmst."geogebra/geogebra.jar\" width=\"$large\" height=\"$haut\">\n".	
		"	<param name=\"filename\" value=\"$geogebra_url\" />\n".

			"<param name=\"java_arguments\" value=\"-Xmx1000m\">\n".
			"<param name=\"framePossible\" value=\"true\"/>\n".
			"<param name=\"showResetIcon\" value=\"true\"/>\n".
			"<param name=\"showAnimationButton\" value=\"true\"/>\n".
			"<param name=\"enableRightClick\" value=\"true\"/>\n".
			"<param name=\"enableLabelDrags\" value=\"true\"/>\n".
			"<param name=\"showMenuBar\" value=\"false\"/>\n".
			"<param name=\"showToolBar\" value=\"false\"/>\n".
			"<param name=\"showToolBarHelp\" value=\"false\"/>\n".
			"<param name=\"showAlgebraInput\" value=\"false\"/>\n".
		"</applet>\n".

		"<br />\n".
		"<span class=\"floatr\"><a href=\"$geogebra_url\">T&eacute;l&eacute;charger ce fichier GeoGebra</a> :: Utiliser <a href=\"http://www.geogebra.at/\">GeoGebra</a> pour l'&eacute;diter. Plein &eacute;cran: cliquer sur Imprimer ci-dessous.\n";
	
		print($output);
		$this->showUpdateLink();
		echo "</span><div style=\"clear:both;\"></div>";
	}
	/**
	* Affiche le fichier geonext dans une boite.
	*/
	function showAsGeonext($fullFilename){//pour geonext
	$haut=$this->haut;
	$large=$this->large;
	if (!$haut) $haut = "450";
       	if (!$large) $large = "100%";
	$urlmst = $this->wiki->config[url_site];
	$geonext_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
	$contents = file_get_contents("$urlmst$fullFilename"); 	
		$output =
		"<applet id=\"umf_rect\" codebase=\"$urlmst\" code=\"geonext.Geonext.class\" archive=\"".$urlmst."geonext.jar\" width=\"$large\" height=\"$haut\">\n".
		"	<param name=\"geonext\" value=\"$contents\" />\n".
		"	<param name=\"scriptable\" value=\"true\" />\n".
		"	<param name=\"MAYSCRIPT\" value=\"true\" />\n".
	
		"</applet>\n".


		"<br />\n".
		"<span class=\"floatr\"><a href=\"$geonext_url\">T&eacute;l&eacute;charger ce fichier GeoNext</a> :: Utiliser <a href=\"http://geonext.uni-bayreuth.de/?LANG=fr/\">GeoNext</a> pour l'&eacute;diter. Plein &eacute;cran: cliquer sur Imprimer ci-dessous.\n";
	
		print($output);
		$this->showUpdateLink();
		echo "</span><div style=\"clear:both;\"></div>";
	}

 	/**
	* Affiche le fichier geolabo dans une boite.
	*/
	function showAsGeolabo($fullFilename){//pour geolabo
	$haut=$this->haut;
	$large=$this->large;
	if (!$haut) $haut = "450";
       	if (!$large) $large = "100%";
	$urlmst = $this->wiki->config[url_site];
	$geolabo_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
		$output =
				
		"<applet code=\"geolabo.geoweb.class\" codebase=\"$urlmst\" archive=\"".$urlmst."geoweb.jar\" width=\"$large\" height=\"$haut\">\n".
			
		"	<param name=\"figure\" value=\"$fullFilename\" />\n".
		"	<param name=\"framePossible\" value=\"false\" />\n".
	
		"</applet>\n".

		"<br />\n".
		"<span class=\"floatr\"><a href=\"$geolabo_url\">T&eacute;l&eacute;charger ce fichier Geolabo</a> :: Utiliser <a href=\"http://www.bibmath.net/geolabo/index.php3\">Geolabo</a> pour l'&eacute;diter. Plein &eacute;cran: cliquer sur Imprimer ci-dessous.\n";
	
		print($output);
		$this->showUpdateLink();
		echo "</span><div style=\"clear:both;\"></div>";
	}
	/**
	* Affiche le fichier flash dans une boite.
	*/
	function showAsFlash($fullFilename){//pour animation flash
	$haut=$this->haut;
	$large=$this->large;
	$size = getimagesize("$fullFilename");
       	if (!$haut) $haut = $size[0];
       	if (!$large) $large = $size[1];
	$flash_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
	
		$output =

		"<object type=\"application/x-shockwave-flash\" width=\"$haut\" height=\"$large\" data=\"$fullFilename\">
       		<param name=\"movie\" value=\"$fullFilename\" />
       		<param name=\"quality\" value=\"high\" />
       		<param name=\"LOOP\" value=\"false\" />
       		<param name=\"menu\" value=\"false\" />\n".
		"</object>\n".	

		"<br />\n".
		"<span class=\"floatr\"><a href=\"$flash_url\">T&eacute;l&eacute;charger ce fichier Flash</a>  Plein &eacute;cran: cliquer sur Imprimer ci-dessous.\n";
	
		print($output);
		$this->showUpdateLink();
		echo "</span>";
	}
	/**
	* Affiche le fichier mp3 dans un player flash.
	*/
	function showAsMp3($fullFilename){
	$titre=$this->desc;
	$mp3_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
	$urlmst = $this->wiki->config[url_site];
	if (!$this->classes) {$laclass="nul";} else {$laclass=$this->classes;}
		$output =
		
		"<div class=\"$laclass\"><object type=\"application/x-shockwave-flash\" data=\"".$urlmst."player_mp3_maxi.swf\" width=\"250\" height=\"35\">
		<param name=\"movie\" value=\"".$urlmst."player_mp3_maxi.swf\" />
		<param name=\"bgcolor\" value=\"#ffffff\" />
		<param name=\"FlashVars\" value=\"mp3=$urlmst$fullFilename&amp;showstop=1&amp;showinfo=1&amp;showvolume=1&amp;showloading=always\" />
		</object>\n".
	
		"<br />\n".
		"<a href=\"$mp3_url\">T&eacute;l&eacute;charger $this->desc</a>\n";
		
		print($output);
		$this->showUpdateLink();
		echo "</div>";
	}

	/**
	* Affiche le fichier mp3 dans un mini player flash.
	*/
	function showAsMp3mini($fullFilename){
	$titre=$this->desc;
	$mp3_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
	$urlmst = $this->wiki->config[url_site];
	if (!$this->classes) {$laclass="nul";} else {$laclass=$this->classes;}
		$output =
		
		"<div class=\"$laclass\"><object type=\"application/x-shockwave-flash\" data=\"".$urlmst."emff_standard.swf?src=$urlmst$fullFilename&amp;song_title=$titre\" width=\"130\" height=\"50\"><param name=\"quality\" value=\"high\" /><param name=\"movie\" value=\"".$urlmst."emff_standard.swf?src=$urlmst$fullFilename&amp;song_title=$titre\" /> </object>\n".	
		"<br />\n".
		"<a href=\"$mp3_url\">T�l�charger $this->desc</a>\n";
		
		print($output);
		$this->showUpdateLink();
		echo "</div>";
	}
	/**
	* Affiche le fichier csv dans un tableau.
	*/
	function showAsCsv($fullFilename){
	require_once('handlers/page/handlecsvdata.php');
	$csv_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
	// ***Get the params ***
	$header= 'on';
	$separator = ",";
	$filename = $fullFilename;
	//$tableclass = $vars['tableclass'];
	if ($this->separateur) $separator = $this->separateur;
	if ($this->entete) $header = $this->entete;
	if (!$this->classes) {$laclass="nul";} else {$laclass=$this->classes;}
	echo "<div class=\"$laclass\">";
	// *** Get the data and print the table ***
	if (is_array($table = GetCsvData($filename, $separator, $tableclass))) PrintCsvTable($table, $header);

	
	echo "<a href=\"$csv_url\">T&eacute;l&eacute;charger $this->desc</a>";
	$this->showUpdateLink();
	echo "</div><div style=\"clear:both;\"></div>";
	}

	
	/**
	* Affiche le fichier flv dans un player flash.
	*/

	function showAsFlv($fullFilename){
	$haut=$this->haut;
	$large=$this->large;
	$urlmst = $this->wiki->config[url_site];
	if (!$haut) $haut = "260";
       	if (!$large) $large = "340";
	$titre=$this->desc;
	$titre=enlever_accents($titre);
	$flv_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
	if (!$this->classes) {$laclass="nul";} else {$laclass=$this->classes;}
		$output =
		"<div class=\"$laclass\">\n".
		"<object type=\"application/x-shockwave-flash\" width=\"$large\" height=\"$haut\" data=\"".$urlmst."player_flv.swf?flv=$urlmst$fullFilename&amp;width=$large&amp;height=$haut&amp;bgcolor1=cccccc&amp;bgcolor2=cccccc&amp;buttoncolor=999999&amp;buttonovercolor=66FF33&amp;slidercolor1=cccccc&amp;slidercolor2=999999&amp;sliderovercolor=666666&amp;showvolume=1&amp;srt=1&amp;textcolor=0&amp;showstop=1&amp;title=$titre&amp;startimage=preview.jpg\" />\n".
		"<param name=\"movie\" value=\"".$urlmst."player_flv.swf?flv=$urlmst$fullFilename&amp;width=$large&amp;height=$haut&amp;bgcolor1=cccccc&amp;bgcolor2=cccccc&amp;buttoncolor=999999&amp;buttonovercolor=66FF33&amp;slidercolor1=cccccc&amp;slidercolor2=999999&amp;sliderovercolor=666666&amp;showvolume=1&amp;srt=1&amp;textcolor=0&amp;showstop=1&amp;title=$titre&amp;startimage=preview.jpg\" />\n".
		"<param name=\"wmode\" value=\"transparent\" />\n".
		"</object>\n".
		"<br />\n".
		"<a href=\"$flv_url\" class=\"handout_video\">T&eacute;l&eacute;charger $this->desc</a>\n";
	
		print($output);
		$this->showUpdateLink();
		echo "</div>";
	}

	/**
	* Affiche le fichier obj dans l'applet jmath3d
	*/
	function showAsObj($fullFilename){
	$haut=$this->haut;
	$large=$this->large;
	if (!$haut) $haut = "560";
       	if (!$large) $large = "790";
	$titre=$this->desc;
	$titre = str_replace(' ', '', $titre); 
	$urlmst = $this->wiki->config[url_site];
	$obj_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
	if (!$this->classes) {$laclass="nul";} else {$laclass=$this->classes;}
		$output =
		"<script type=\"text/javascript\">
		//<![CDATA[
		var appletid1 = (navigator.userAgent.indexOf(\"MSIE\") >= 0) ? \"appletIE1$titre\" : \"applet1$titre\";
		// Trouve le num&eacute;ro de la face s&eacute;lectionn&eacute;e.
		var f;
		function quelleFace() {
		f = document.getElementById('f');
		return f.numface.selectedIndex + 1;
		}
		
		// Cette fonction donne un code de couleur de type html (#rrvvbb) � la face s&eacute;lectionn&eacute;e
		function changeCouleur1(c) {
		var n = quelleFace();
		var alpha = f.opacite.selectedIndex * .2;
		if(alpha == 0) {   // On ne peut pas mettre une couleur tant que la
		alpha = 1;       // face est compl&egrave;tement transparente.
		f.opacite.selectedIndex = 5;
		}
		document.getElementById(appletid1).setFaceColor(n, c, alpha);
		}
		
		// M�me chose mais la couleur est communiqu&eacute;e par ses 3 composantes Rouge Vert Bleu s&eacute;par&eacute;es.
		function changeCouleur2(r, v, b) {
		var n = quelleFace();
		var alpha = f.opacite.selectedIndex * .2;
		if(alpha == 0) {
		alpha = 1;
		f.opacite.selectedIndex = 5;
		}
		document.getElementById(appletid1).setFaceColor(n, r, v, b, alpha);
		}
		
		// Change juste l'opacit&eacute; de la face s&eacute;lectionn&eacute;e.
		function changeAlpha() {
		var n = quelleFace();
		var alpha = f.opacite.selectedIndex * .2;
		var codeCouleur = document.getElementById(appletid1).getFaceColor(n).toString(16);
		document.getElementById(appletid1).setFaceColor(n, codeCouleur, alpha);
		}
		
		// Fonction utilis&eacute;e pour affichaer l'opacit&eacute; de la face s&eacute;lectionn&eacute;e.
		function majAlpha() {
		var n = quelleFace();
		var alpha = document.getElementById(appletid1).getFaceAlpha(n);   // entre 0 et 1
		var indice = Math.round(alpha * 5);  // entier de 0 � 5.
		f.opacite.selectedIndex = indice;
		}
		function changeVue(n) {
		// Cette commande demande � l'applet de ne pas rafra�chir
		// l'image imm�diatement. Utile quand on a plusieurs
		// modifications � faire.
		document.getElementById(appletid1).dontRepaint;
		switch(n) {
		case 0 :
			document.getElementById(appletid1).setLineWidth(1);
			document.getElementById(appletid1).paintZBuffer(false);
			break;
		case 1 :
			document.getElementById(appletid1).setLineWidth(2);
			document.getElementById(appletid1).paintZBuffer(false);
			break;
		case 2 :
			document.getElementById(appletid1).setLineWidth(5);
			document.getElementById(appletid1).paintZBuffer(false);
			break;
		case 3 :
			document.getElementById(appletid1).setLineWidth(2, .5);
			document.getElementById(appletid1).paintZBuffer(false);
			break;
		case 4 :
			document.getElementById(appletid1).setLineWidth(2, 0);
			document.getElementById(appletid1).paintZBuffer(false);
			break;
		case 5 :
			document.getElementById(appletid1).setLineWidth(1, 0);
			document.getElementById(appletid1).paintZBuffer(true);
			break;
		case 6 :
			document.getElementById(appletid1).setLineWidth(0);
			document.getElementById(appletid1).paintZBuffer(true);
			break;
		}
		// Cette commande signale � l'applet qu'on a termin� les modifications
		// et qu'elle peut rafra�chir l'image.
		document.getElementById(appletid1).repaint();
		}
		// Affiche les donn&eacute;es g&eacute;om&eacute;triques de la face.
		function afficheStats() {
		np = document.getElementById(appletid1).nbPoints();
		ns = document.getElementById(appletid1).nbLines();
		nf = document.getElementById(appletid1).nbFaces();
		alert(np+\" points\\n\"+ns+\" segments\\n\"+nf+\" faces\");
		}
		// Affiche les donn&eacute;es g&eacute;om&eacute;triques de la face.
		function afficheStats2() {
		np = document.getElementById(appletid1).nbPoints();
		ns = document.getElementById(appletid1).nbLines();
		nf = document.getElementById(appletid1).nbFaces();
		return nf;
		}
		//]]>
		</script>".
		"<form id=\"f\" method=\"get\" action=\"\" onsubmit=\"return false\">".
		"<div class=\"$laclass\">\n".
		"<!-- Code d'insertion de l'applet JMath3D -->
    		<!-- applet object en xhtml, voir http://ww2.cs.fsu.edu/~steele/XHTML/appletObject.html -->

    		<!--[if !IE]>-->
		<object id=\"applet1$titre\"
			classid=\"java:JMath3D.class\"
			type=\"application/x-java-applet\"
			archive=\"".$urlmst."JMath3D.jar\"
			width=\"$large\" height=\"$haut\">
			<param name=\"archive\" value=\"".$urlmst."JMath3D.jar\" />
			<param name=\"patron\" value=\"0.5 curseur\" />
			<param name=\"fichier\"       value=\"$obj_url\" />
			<param name=\"epaisseur\"     value=\"2\" />
			<param name=\"animation\" value=\"0.02 0 .01\" />
			<param name=\"nomspoints\"    value=\"auto\" />
			<param name=\"echelle\" value=\".7\" />
		<!--<![endif]-->
		<object classid=\"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93\"
			id=\"appletIE1$titre\" width=\"$large\" height=\"$haut\" >
			<param name=\"code\"          value=\"JMath3D\" />
			<param name=\"patron\" value=\"0.5 curseur\" />
			<param name=\"archive\"       value=\"".$urlmst."JMath3D.jar\" />
			<param name=\"fichier\"       value=\"$obj_url\" />
			<param name=\"epaisseur\"     value=\"2\" />
			<param name=\"animation\" value=\"0.02 0 .01\" />
			<param name=\"nomspoints\"    value=\"auto\" />
			<param name=\"echelle\" value=\".7\" />
		</object> 
		<!--[if !IE]>-->
		</object>
		<!--<![endif]-->
		<!-- Fin du code de l'applet -->".
		
		"<br />\n".
 		"<div style=\"text-align: left; width: 790px; background-color: #eee; border: #aaa 1px solid; padding: 15px;\"><br /><input type=\"button\" value=\"Arr&ecirc;ter\" onclick=\"document.getElementById(appletid1).animationStop()\" />&nbsp;<input type=\"button\" value=\"animer et faire tourner\" onclick=\"document.getElementById(appletid1).animationStart(0.01, 0, 0.005)\" />&nbsp;<input type=\"button\" value=\"masquer le curseur\" onclick=\"document.getElementById(appletid1).showNetSlider(false)\" />&nbsp;".
	  	"<input type=\"button\" value=\"afficher le curseur\" onclick=\"document.getElementById(appletid1).showNetSlider(true)\" /><br /><br />".
		"<input type=\"button\" value=\"afficher les donn&eacute;es g&eacute;om&eacute;triques\" onclick=\"afficheStats()\" /><br /><br />".
		"Taille de police : 
	 	 <select onchange=\"document.getElementById(appletid1).setFontSize(this[this.selectedIndex].text)\">
			<option>10</option>
			<option selected=\"selected\">12</option>
			<option>13</option>
			<option>14</option>
			<option>15</option>
			<option>16</option>
			<option>18</option>
			<option>20</option>
			<option>22</option>
			<option>24</option>
			<option>26</option>
			<option>28</option>
			<option>32</option>
	 	 </select>&nbsp;&nbsp;<select onchange=\"changeVue(this.selectedIndex)\">
			<option>traits fins</option>
	
			<option selected=\"selected\">traits moyens</option>
			<option>traits �pais</option>
			<option>traits visibles moyens, traits cach�s tr�s fins</option>
			<option>sans traits cach�s</option>
			<option>zbuffer avec traits visibles fins</option>
			<option>zbuffer seul</option>
	
		</select><br /><br />".
		"Choix de la couleur et de la transparence des faces:<br />
		<table style=\"border-collapse:collapse; width:650px; height:50px\">
		<tr>
		<td style=\"border:solid 1px black; background-color:#ffffff\">Face <select name=\"numface\" onchange=\"majAlpha()\">
			<option>1</option>
			<option>2</option>
			<option>3</option>
			<option>4</option>
			<option>5</option>
			<option>6</option>
			<option>7</option>
			<option>8</option>
			<option>9</option>
			<option>10</option>
			<option>11</option>
			<option>12</option>
	  	</select></td>
		<td style=\"border:solid 1px black; width:50px;background-color:#d0d0d0\" onclick=\"changeCouleur1('d0d0d0')\">&nbsp;</td>
		<td style=\"border:solid 1px black; width:50px;background-color:#40d0e0\" onclick=\"changeCouleur2(64, 208, 224)\">&nbsp;</td>
		<td style=\"border:solid 1px black; width:50px;background-color:#ffffff\" onclick=\"changeCouleur1('#ffffff')\">&nbsp;</td>
		<td style=\"border:solid 1px black; width:50px;background-color:#ffff80\" onclick=\"changeCouleur2(255,255,128)\">&nbsp;</td>
		<td style=\"border:solid 1px black; width:50px;background-color:#ff0000\" onclick=\"changeCouleur2(255,0,0)\">&nbsp;</td>
		<td style=\"border:solid 1px black; background-color:#ffffff\">&nbsp;&nbsp;opacit&eacute; :
		<select name=\"opacite\" onchange=\"changeAlpha()\">
			<option>0% (transparente)</option>
			<option>20%</option>
			<option>40%</option>
			<option>60%</option>
			<option>80%</option>
			<option selected=\"selected\">100% (opaque)</option>
		</select></td>
		</tr>
		</table>".

		"</div></form>".
	
		"<br />\n".
		"<a href=\"$obj_url\">T&eacute;l&eacute;charger $this->desc</a>\n";
	
		print($output);
		$this->showUpdateLink();
		echo "</div>";
	}

	/**
	* Affiche le fichier obj dans l'applet jmath3d
	*/
	function showAsObjMini($fullFilename){
	
	$haut=$this->haut;
	$large=$this->large;
	if (!$haut) $haut = "500";
       	if (!$large) $large = "500";
	$titre=$this->desc;
	$titre = str_replace(' ', '', $titre); 
	$obj_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
	if (!$this->classes) {$laclass="nul";} else {$laclass=$this->classes;}
		$output =
		
		
		"<div class=\"$laclass\">\n".
		"<!-- Code d'insertion de l'applet JMath3D -->
    		<!-- applet object en xhtml, voir http://ww2.cs.fsu.edu/~steele/XHTML/appletObject.html -->

    		<!--[if !IE]>-->
		<object id=\"applet1$titre\"
			classid=\"java:JMath3D.class\"
			type=\"application/x-java-applet\"
			archive=\"".$urlmst."JMath3D.jar\"
			width=\"$large\" height=\"$haut\">
			<param name=\"archive\" value=\"".$urlmst."JMath3D.jar\" />
			<param name=\"patron\" value=\"0.5 curseur\" />
			<param name=\"fichier\"       value=\"$obj_url\" />
			<param name=\"epaisseur\"     value=\"2\" />
			<param name=\"animation\" value=\"0.02 0 .01\" />
			<param name=\"nomspoints\"    value=\"auto\" />
			<param name=\"echelle\" value=\".5\" />
		<!--<![endif]-->
		<object classid=\"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93\"
			id=\"appletIE1$titre\" width=\"$large\" height=\"$haut\" >
			<param name=\"code\"          value=\"JMath3D\" />
			<param name=\"patron\" value=\"0.5 curseur\" />
			<param name=\"archive\"       value=\"".$urlmst."JMath3D.jar\" />
			<param name=\"fichier\"       value=\"$obj_url\" />
			<param name=\"epaisseur\"     value=\"2\" />
			<param name=\"animation\" value=\"0.02 0 .01\" />
			<param name=\"nomspoints\"    value=\"auto\" />
			<param name=\"echelle\" value=\".5\" />
		</object> 
		<!--[if !IE]>-->
		</object>
		<!--<![endif]-->
		<!-- Fin du code de l'applet -->".
	
		"<br />\n".
		"<a href=\"$obj_url\">T&eacute;l&eacute;charger $this->desc</a>\n";
	
		print($output);
		$this->showUpdateLink();
		echo "</div>";
	}
	function showAsSqueak($fullFilename){
	$haut=$this->haut;
	$large=$this->large;
	if (!$haut) $haut = "600";
       	if (!$large) $large = "800";
	$titre=$this->desc;
	$squeak_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
		$output =
		
		"<div class=\"$this->classes\">\n".
		"<embed type=\"application/x-squeak-source\" src=\"$squeak_url\" width=\"$large\" height=\"$haut\"></embed>".
		"<br />\n".

		"<span class=\"floatr\"><a href=\"$squeak_url\">T�l�charger ce fichier Squeak</a>.\n";
		print($output);
		$this->showUpdateLink();
		echo "</div><div style=\"clear:both;\"></div>";
	}

	function showAsScratch($fullFilename){
	$haut=$this->haut;
	$large=$this->large;
	if (!$haut) $haut = "400";
       	if (!$large) $large = "500";
	$titre=$this->desc;
	$urlmst = $this->wiki->config[url_site];
	$scratch_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
		$output =
		
		"<div class=\"$this->classes\">\n".
		"<applet id=\"ProjectApplet$titre\" style=\"display:block\" code=\"ScratchApplet\" codebase=\"$urlmst\" archive=\"".$urlmst."ScratchApplet.jar\" height=\"$haut\" width=\"$large\"><param name=\"project\" value=\"$fullFilename\"></applet>".
		
		"<br />\n".

		"<span class=\"floatr\"><a href=\"$scratch_url\">T�l�charger ce fichier Scratch</a>.\n";
		print($output);
		$this->showUpdateLink();
		echo "</span></div><br /><br />";
	}

	function showAsIeP($fullFilename){
	$haut=$this->haut;
	$large=$this->large;
	if (!$haut) $haut = "500";
       	if (!$large) $large = "100%";
	$titre=$this->desc;
	$urlmst = $this->wiki->config[url_site];
	$iep_url = $this->wiki->href("download",$this->wiki->GetPageTag(),"file=$this->file");
		$output =
		
		"<div class=\"$this->classes\">\n".
		"<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" id=\"instrumenpoche\" width=\"100%\" height=\"600\" align=\"middle\">
		
		<param name=\"allowScriptAccess\" value=\"sameDomain\" />
		<param name=\"movie\" value=\"".$urlmst."Animation.swf?$fullFilename\" />
		
		<param name=\"quality\" value=\"high\" />
		<param name=\"bgcolor\" value=\"#ffffff\" />
		<embed src=\"".$urlmst."Animation.swf?anim=$fullFilename\" loop=\"false\" quality=\"high\" bgcolor=\"#ffffff\" width=\"800\" height=\"600\" swLiveConnect=true id=\"instrumenpoche\" name=\"instrumenpoche\" align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
		</object>".
		
		"<br />\n".

		"<span class=\"floatr\"><a href=\"$scratch_url\">T�l�charger ce fichier IeP</a>.\n";
		print($output);
		$this->showUpdateLink();
		echo "</span></div><br /><br />";
	}


}

?>
