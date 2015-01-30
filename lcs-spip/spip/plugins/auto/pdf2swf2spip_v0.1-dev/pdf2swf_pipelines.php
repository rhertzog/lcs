<?php
function pdf2swf_affiche_droite($flux){
	$id_article = $flux['args']['id_article'];
	if ($flux['args']['exec']=='articles' AND $id_article > 0) {
		$icone = icone_horizontale(_T("pdfswf:importer_fichier"), "#", "", _DIR_PLUGIN_PDF2SWF."images/pdf2swf-24.png", false, "onclick='$(\"#boite_pdf2swf\").slideToggle(\"fast\");return false;'");
		$out = recuperer_fond('formulaires/pdf2swf',array('id_article'=>$id_article,'icone'=>$icone));
		$flux['data'].= $out;
	}
	return $flux;
}
// css prive
	function pdf2swft_header_prive($flux) {
		$flux.= "\n".'<script type="text/javascript" src="'._DIR_PLUGIN_PDF2SWF.'lib/swfobject.js" />'."\n";
 		return $flux;
	}

?>
