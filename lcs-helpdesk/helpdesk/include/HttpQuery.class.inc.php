<?php
class HTTPQuery
{
    /**
     * Tableau : les données POST qui seront envoyées (nom du champ => valeur)
    **/
    protected $_post;

    /**
     * Tableau des options cURL définies par l'utilisateur (option => valeur)
     **/
    protected $_options;

    /**
     * La ressource cURL
     **/
    protected $_ch;

    /**
     * Constructeur
     * @param url URL à laquelle la requête sera envoyée
     * @throws Exception si l'extension cURL n'est pas active
     **/
    public function __construct($url)
    {
        if (!extension_loaded('curl')) {
            throw new Exception("L'extension curl n'est pas disponible");
        }
        $this->_ch = curl_init($url);
        //$this->_options = array();
	curl_setopt($this->ch,CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch,CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->ch,CURLOPT_SSL_VERIFYHOST, 0);
    }

    /**
     * Obtenir la valeur des options cURL avec la syntaxe $ojet->CURLOPT_X définie par l'utilisateur
     * @param nom le nom de l'option cURL
     * @return NULL si l'option n'a pas été définie sinon sa valeur
     **/
    public function __get($nom)
    {
        $resultat = NULL;
        if (defined($nom)) {
            $valeur = constant($nom);
            if (isset($this->_options[$valeur])) {
                $resultat = $this->_options[$valeur];
            }
        }
        return $resultat;
    }

    /**
     * Fixer les valeurs des options cURL avec la syntaxe $objet->CURLOPT_X = Y
     * @param nom    le nom de l'option cURL (constantes CURLOPT_*)
     * @param valeur la nouvelle valeur de l'option (écrase la précédente)
     * @throws Exception si l'option "nom" n'est pas valide (inexistante ou ne commençant pas par CURLOPT_) ou est
     *                   protégée de façon à ce que vous passiez par les méthodes déléguées à la fonctionnalité ciblée
     **/
    public function __set($nom, $valeur)
    {
        if (defined($nom) && preg_match('/^CURLOPT_(?!POSTFIELDS)/', $nom)) {
            $this->_options[constant($nom)] = $valeur;
        } else {
            throw new Exception("Option '$nom' invalide ou protégée");
        }
    }

    /**
     * Prendre connaissance de la définition d'une option cURL par l'utilisateur
     * @param nom le nom de l'option cURL
     * @return un booléen indiquant si cette option a été définie
     **/
    public function __isset($nom)
    {
        return (defined($nom) && isset($this->_options[constant($nom)]));
    }

    /**
     * Détruire la définition d'une option cURL
     * @param nom le nom de l'option cURL à détruire
     **/
    public function __unset($nom)
    {
        if (defined($nom) && isset($this->_options[constant($nom)])) {
            unset($this->_options[constant($nom)]);
        }
    }

    /**
     * Description de l'objet
     * @return une chaîne de caractères décrivant l'objet
     **/
    public function __toString()
    {
        return sprintf("%s (%s)", __CLASS__, curl_getinfo($this->_ch, CURLINFO_EFFECTIVE_URL));
    }

    /**
     * Fixer la durée maximale d'exécution de la requête
     * @param timeout cette durée exprimée en secondes
     **/
    public function setTimeout($timeout)
    {
        $timeout = intval($timeout);
        if ($timeout > 0) {
            $this->CURLOPT_TIMEOUT = $timeout;
            $this->CURLOPT_CONNECTTIMEOUT = $timeout;
        }
    }

    /**
     * Ajouter des données textuelles aux données POST à envoyer
     * @param nom_champ le nom du champ (permet d'exploiter les données côté serveur - $_POST)
     * @param valeur    les données correspondantes à envoyer
     * @return un booléen indiquant que les données ont été prises en compte
     **/
    public function addPostData($nom_champ, $valeur)
    {
        if (!isset($this->_post[$nom_champ]) && !is_array($valeur)) {
            $this->_post[$nom_champ] = $valeur;
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Ajouter un fichier aux données POST à envoyer (upload de fichiers)
     * @param nom_champ le nom du champ (permet d'exploiter le fichier à sa réception - $_FILES)
     * @param fichier   le fichier à envoyer
     * @throws Exception si le fichier indiqué est inexistant ou n'est pas un fichier régulier
     **/
    public function addPostFile($nom_champ, $fichier)
    {
        if (is_file($fichier)) {
            $this->_post[$nom_champ] = '@' . realpath($fichier);
        } else {
            throw new Exception("Le fichier '$fichier' n'existe pas ou n'est pas un fichier régulier");
        }
    }

    /**
     * Exécuter la requête
     * @param fichier_sortie, renseigné le contenu de la page distante est écrit dans le fichier indiqué
     * @return le contenu de la page distante ou alors TRUE si le paramètre fichier_sortie a été utilisé
     * @throws Exception en cas d'erreur liée à cURL ou à l'écriture du fichier
     **/
    public function doRequest($fichier_sortie = FALSE)
    {

	$cookie = "symfony=".$_SESSION['symfony'];
	//var_dump($_SESSION);
    	curl_setopt($this->_ch, CURLOPT_COOKIE, $cookie);



        if ($this->_options) {
            if (function_exists('curl_setopt_array')) {
                curl_setopt_array($this->_ch, $this->_options);
            } else {
                foreach ($this->_options as $option => $valeur) {
                    curl_setopt($this->_ch, $option, $valeur);
                }
            }
        }


        if ($fichier_sortie) {
            @ $fp = fopen($fichier_sortie, 'w');
            if (!$fp) {
                throw new Exception("Impossible d'ouvrir en écriture le fichier '$fichier_sortie'");
            }
            curl_setopt($this->_ch, CURLOPT_FILE, $fp);
        } else {
            curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, TRUE);
        }
        if ($this->_post) {
            curl_setopt($this->_ch, CURLOPT_POST, TRUE);
            curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $this->_post);
        }
        $ret = curl_exec($this->_ch);
        if ($fichier_sortie) {
            fclose($fp);
        }
        if ($ret === FALSE) {
            throw new Exception("Une erreur est survenue : '" . curl_error($this->_ch) . "'");
        }
        return $ret;
    }

    /**
     * Destructeur
     **/
    public function __destruct()
    {
        unset($this->_options);
        unset($this->_post);
        curl_close($this->_ch);
    }
}
?>
