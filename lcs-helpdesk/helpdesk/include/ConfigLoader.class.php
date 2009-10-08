<?php
/**
 * Classe d'accès à la configuration de l'établissement
 * @property-read string $tokenEtab token de l'établissement ou se trouve le serveur
 * @property-read string $urlHelpDesk url de la plateforme de support
 * @property-read int isProductionServer indique si le serveur est un serveur de prod
 */
class ConfigLoader {
    private $etabConfFile = '/etc/gestEtab/etab.conf';

    private $datas = array();

    public function __construct() {
        //if(file_exists($this->etabConfFile)) {
            $cmd = "cat $this->etabConfFile";
	    $conf = exec($cmd,$lines,$valeur);
            if($valeur !=0)
		throw new Exception('Fichier '.$this->etabConfFile.' introuvable ou inaccessible');
   
	    //$lines = explode("\n", $conf);
            //die(implode("\n",$lines));
	    foreach($lines as $line) {
                if(strpos($line, '#') === 0)
                continue;

                $temp = explode("=", $line);
                if(count($temp) == 2)
                $this->datas[trim($temp[0])] = trim($temp[1]);
            }
        //}
        //else {
        //    throw new Exception('Fichier '.$this->etabConfFile.' introuvable');
        // }
    }

    public function __get($name) {
        if(array_key_exists($name, $this->datas)) {
            return $this->datas[$name];
        }
        else {
            throw new Exception(sprintf('Champ de configuration "%s" introuvable', $name));
        }
    }
}
?>
