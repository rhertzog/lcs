<?php
if (empty($vars['f'])) {echo "Pas de crit&egrave;re f!";}

else {
	$txt=$vars['f'];
	if ($vars['class']=="") { $laclass="nul";} else  {$laclass="attach_".$vars['class'];}
	$urlmst = $this->config[url_site];

	if (!function_exists("tex"))

	{
	/**
	* Filtre pour g�n�rer un QRCode � partir d'un texte
	*/
		function tex ( $txt, $dim=120 )
		{
			// S'il existe pas on cr�e le r�pertoire qui va accueillir nos QRCodes
			if ( !is_dir("cache-tex/" ) )
			{                                     
				if ( !mkdir ( "cache-tex/", 0775 ) )
				{ 
					return "impossible de creer le repertoire" ;
				}
		}
			
			// l'url du service web de Google qui va bien
			$url = 'http://chart.apis.google.com/chart';
			// On colle dans un tableau les arguments pour la requÃ¨te
			$args = array(	"cht"=>"tx",
						
							"chl"=>rawurldecode(urlencode($txt)) 
							);
			// On cr�e le context pour la requÃ¨te
			$context = stream_context_create( array( 
				'http' => array( 
					'method' => 'POST',
					'content' => http_build_query($args)
					)
				)
			);
			
			// Ici on va g�n�rer un nom et un chemin pour notre fichier final
		$hash    = md5(serialize($txt));
		$fichier = "cache-tex/qrcode-$hash.png";

			// Si notre fichier n'existe pas on requÃ¨te le service et on cr�e le fichier
			if ( !is_file( $fichier ) ){
				file_put_contents( $fichier, file_get_contents( $url, false, $context ) );
			}
			
			// On retourne un tag image avec la source qui va bien
			//return "<img src=\"$fichier\" alt='qrcode' width='$dim' height='$dim' class='middle' />";
			return "$fichier";
		}

	}

	$fichier= tex($txt);
	echo "<div class='$laclass'>";
	echo "<img src=\"".$urlmst."$fichier\" alt='tex' class='middle' />";
	echo "</div>";
}
?> 