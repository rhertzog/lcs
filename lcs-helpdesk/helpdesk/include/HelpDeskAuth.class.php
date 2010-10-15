<?php

class Proxy {

	//private $config;

	public function __construct() {
		$this->cookie = array();
	      	$this->ch = curl_init();
        	curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
		curl_setopt ($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->ch,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($this->ch, CURLOPT_CRLF, true);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
	}

	public function __destruct() {
	      if ($this->ch)
			curl_close($this->ch);
	}

	public function getHeaders() {
	      if ($this->ch)
			return $this->headers;
	}
	public function getCookie() {
			return json_encode($this->cookie);
	}
	public function getContentType() {
			return $this->ContentType;
	}

	public function addPostData($nom_champ, $valeur)
    	{   
        	if (!isset($this->_post[$nom_champ]) && !is_array($valeur)) {
            		$this->_post[$nom_champ] = $valeur;
            		return TRUE;
        	} else {
            		return FALSE;
        	}
    	}

	public function addPostFile($nom_champ, $fichier)
    	{
        	if (is_file($fichier)) {
            		$this->_post[$nom_champ] = '@' . realpath($fichier);
        	} else {
            	throw new Exception("Le fichier '$fichier' n'existe pas ou n'est pas un fichier régulier");
        	}
    	}


	public function process($url, $data = null, $verb = 'GET') {
		try {

			curl_setopt ($this->ch, CURLOPT_URL, $url);
			$cookie = "symfony=".$_SESSION['symfony'];
			curl_setopt($this->ch, CURLOPT_COOKIE, $cookie);
			curl_setopt($this->ch, CURLOPT_HEADER, true);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
			  if ($verb == 'POST') {
                                curl_setopt($this->ch, CURLOPT_POST, 1 );
                                if( is_array($data) ) {
                                        $postData = implode('&', $data);
                                        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postData );
                                } else {
                                        $postData = $this->_post;
                                        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postData );
	
				}
                        }

			$response = curl_exec($this->ch);
			$this->error = curl_error($this->ch);
			$test = explode("\n\r\n",$response);
			$this->headers = explode("\n",trim($test[0]));
                	$this->response = $test[1];
			//die(implode('<br />',$this->headers)." : ".htmlentities($this->response));
			foreach($this->headers as $ligne) {
                        	if (eregi('Set-Cookie',$ligne)) {
                                	$infos = explode(':',$ligne);
                                	$cook1 = explode(';',$infos[1]);
                                	$infosCook = explode('=',$cook1[0]);
                                        $_SESSION['symfony'] = $infosCook[1];
                        	}

                        	if (eregi('Content-type',$ligne)) {
					$this->ContentType = $ligne;
				}
                	}
	

		} 
		catch (Exception $e) {
    			$this->response = "[$this->error] $e->getMessage() ";
			return $this->response;
		}
		return $this->response;
	}

}
	
class HelpDeskAuth {

	private $config;
	private $urlAUTH;

	public function __construct($array_user) {
		
		$this->config = new ConfigLoader();
		$this->ticketID = $this->config->tokenEtab;
		$this->urlHD = $this->config->urlHelpDesk;
		$this->urlAPI = $this->urlHD."LcsAPI";
		$this->array_user = $array_user;	
		$this->proxy = new Proxy();

	}

	public function getToken() {
		return $this->ticketID;
	}
	
	public function getMessage() {
		return $this->message;
	}
	public function getProxy() {
		return $this->proxy;
	}
	public function setMessage($msg) {
		$this->message = $msg;
	}
	
	public function setUser($user) {
		$this->user = $user;
	}
	
	public function getResponse() {
		return $this->response;
	}

	public function authenticate() {

		try {
    			$this->proxy = new Proxy();
			//$user_mail = $this->array_user['email'];
			$user_mail = $this->array_user['prenom'].'.'.$this->array_user['nom'].'@ac-caen.fr';
			$this->urlAUTH = $this->urlAPI."/auth?token=$this->ticketID&user=".$user_mail;
			$this->response = $this->proxy->process($this->urlAUTH);
			//die($this->response);
		} 
		catch (Exception $e) {
    			$this->message = $e->getMessage();
			return false;
		}
		
		if ($this->response == '<') {
			$this->message =htmlspecialchars(" [Reseau] La plateforme est injoignable pour le moment! Veuillez nous excuser.");
                        return false;
		}
		$heads = $this->proxy->getHeaders();
		try {
                    if (eregi('200 OK', $heads[0])) {
			@$response  = new SimpleXMLElement($this->response);
			$auth_status =  (string) $response->auth;
			if ($auth_status == 'AuthFailure') {
				$this->message =htmlspecialchars(" [$auth_status] Desole! Vous n\'avez pas acces a ce service");
				return false;
			} else {
				$this->message =htmlspecialchars(" [$auth_status] Felicitations. Vous etes bien authentifie !");
				return true;
			}
		    } else {	
				$this->message = htmlspecialchars(" [Reseau] ".trim( (string) $heads[0]) );
				return false;
			}
	
		}
		catch (Exception $e) {
    			$this->message = $e->getMessage();
			return false;
		}


	}    
}

?>
