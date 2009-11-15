<?php

/**
 * CAS actions.
 *
 * @package    CAS-symfony
 * @subpackage CAS
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class CASActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  
public function getLogin() {
 
  $idpers=0;
  $login="";
  $result= array();
  $result2= array();

  $HOSTAUTH = exec('cat /var/www/lcs/includes/config.inc.php | grep "\$HOSTAUTH=" | cut -d"=" -f2 ');
  $HOSTAUTH = str_replace(array('"',';'),'',$HOSTAUTH);
  $DBAUTH = exec('cat /var/www/lcs/includes/config.inc.php | grep "\$DBAUTH=" | cut -d"=" -f2 ');
  $DBAUTH = str_replace(array('"',';'),'',$DBAUTH);
  $USERAUTH = exec('cat /var/www/lcs/includes/config.inc.php | grep "\$USERAUTH=" | cut -d"=" -f2 ');
  $USERAUTH = str_replace(array('"',';'),'',$USERAUTH);
  $PASSAUTH = exec('cat /var/www/lcs/includes/config.inc.php | grep "\$PASSAUTH=" | cut -d"=" -f2 ');
  $PASSAUTH = str_replace(array('"',';'),'',$PASSAUTH);
  

  $dbh = new PDO('mysql:host='.$HOSTAUTH.';dbname='.$DBAUTH, $USERAUTH, $PASSAUTH);
  if (! empty($_COOKIE["LCSAuth"])) {
                $sess=$_COOKIE["LCSAuth"];
                foreach($dbh->query("SELECT remote_ip, idpers FROM sessions WHERE sess='$sess'") as $row) {
                        $result[] = $row;
                }
                if ($result[0]) {
                        $ip_session = $result[0]['remote_ip'];
                        if ($_SERVER['REMOTE_ADDR'] == $ip_session) {
                                $idpers = $result[0]['idpers'];
                                foreach($dbh->query("SELECT login FROM personne WHERE id=$idpers") as $row2) {
                                        $result2[] = $row2;
                                }
                                if ($result2[0])
                                        $login=$result2[0]['login'];
                        }
                }
        }

 return $login;
}	

  private function authenticate() {
	
	$ldap_server = $this->getUser()->getAttribute('ldap_server');
	$ldap_port = $this->getUser()->getAttribute('ldap_port');
	$dn = $this->getUser()->getAttribute('dn');
	
	$ldap_group_attr = array ( "cn", "gecos" );
        $ds = ldap_connect ( $ldap_server, $ldap_port );
        if ( $ds ) {
                $r = ldap_bind ( $ds ); // Bind admin
                if ($r) {
                        $filter = "cn=$username";
                        $result = ldap_list( $ds, $dn["people"], $filter, $ldap_group_attr );
                        if ($result) 
                                $info = ldap_get_entries( $ds, $result );

                 }
        }

	return $this->renderText(var_dump($info));
 }

  private function generate_tgt_cookie($user) {
    		
    	if (empty($user))
		return;
	
	
	$trouve_tgt = Doctrine::getTable('CasTgt')->createQuery('e')
					       ->where('e.username = ?',array($user) )
    						->andWhere('e.client_hostname = ?', array($_SERVER['REMOTE_ADDR']))
						->execute();
	$this->probe = $trouve_tgt->getFirst();

	if ($this->probe) {
		setcookie('tgt-cas', $this->probe->ticket, 0, "/", "",0);
		$this->getUser()->setAttribute('tgt',$this->probe->ticket);
		$this->getUser()->setAttribute('tgt_id',$this->probe->id);

	}
	else {
	
		$random_string = md5(date('Y-m-d H:i:s'));
		$tgt = new CasTgt();
    		$tgt->setUserName($user);
    		$tgt->ticket = "TGC-".$random_string;
    		$tgt->client_hostname = $_SERVER['REMOTE_ADDR'];
    		$tgt->save();
    		
		setcookie('tgt-cas', $tgt->ticket, 0, "/", "",0);
		$this->getUser()->setAttribute('tgt',$tgt->ticket); 
		$this->getUser()->setAttribute('tgt_id',$tgt->id);   	
	}
	
	

    return $this->getUser()->getAttribute('tgt');
 }
  
private function generate_service_ticket($service,$user) {
    if (isset($service)) {
    	$st = new CasSt();
    	$random_string = md5(date('Y-m-d H:i:s'));	
    	$st->ticket = "ST-".$random_string;
	$st->type='ServiceTicket';
    	$st->username = $user;
    	$st->service = $service;
    	$st->client_hostname = $_SERVER['REMOTE_ADDR'];
    	$st->tgt_id = $this->getUser()->getAttribute('tgt_id');
    	$st->save();
    	return $st->ticket;	
    } else return null;	
 }


public function executeServiceValidate(sfWebRequest $request) {
	//retourne la reponse du cas
	$ticket = $request->getParameter('ticket');
	$service = $request->getParameter('service');
	$this->user = $this->getLogin();
	
	$trouve_st = Doctrine::getTable('CasSt')->createQuery('e')
					        ->where('e.ticket = ?',array($ticket))
    						->andWhere('e.client_hostname = ?', array($_SERVER['REMOTE_ADDR']))
						->execute();
	$this->probe = $trouve_st->getFirst();
	
	sfContext::getInstance()->getConfiguration()->loadHelpers(array('Partial'));
	if ($this->probe) 
	{
		if ($this->probe->consumed) {
			$maj = get_partial('serviceValidateError',array('message' => 'Ticket is consumed'));
			return $this->renderText($maj);
		}
		$user = $this->probe->getUserName();
		$this->probe->consumed = date('Y-m-d H:m:s');
		$this->probe->save();
		$maj = get_partial('serviceValidateSuccess',array('user' => $user));
	}

	else
		$maj = get_partial('serviceValidateError',array('message' => 'Invalid ticket'));
        
	return $this->renderText($maj);
}
public function executeProxyValidate(sfWebRequest $request) {
	//retourne la reponse du cas
	$ticket = $request->getParameter('ticket');
	$service = $request->getParameter('service');

	$trouve_st = Doctrine::getTable('CasSt')->createQuery('e')
					        ->where('e.ticket = ?', array($ticket))
						->execute();
	$this->probe = $trouve_st->getFirst();
	
	sfContext::getInstance()->getConfiguration()->loadHelpers(array('Partial'));
	

	if ($this->probe) 
	{
		$this->probe->consumed = date('Y-m-d H:m:s');
		$this->probe->save();
		$user = $this->probe->getUserName();
		$maj = get_partial('proxyValidateSuccess',array('user' => $user));
	}

	else
		$maj = get_partial('proxyValidateError',array('message' => 'Invalid ticket '.$ticket));

        return $this->renderText($maj);
}

  public function executeLogin(sfWebRequest $request)
  {
	
	//user_LCS

	$this->usr = $this->getLogin();
	if (empty($this->usr)) {
		$this->getResponse()->setCookie('tgt-cas', null, 0, "/", "",0);
    		$this->getUser()->setAttribute('tgt',null);

		return;
	}
	
	$service = $request->getParameter('service');
	
	$tgt = $this->generate_tgt_cookie($this->usr);
	//return $this->renderText($this->usr." ".$tgt);
	if (isset($service)) {
		$this->ServiceTicket = $this->generate_service_ticket($service,$this->usr);
		$this->url=$service.'?ticket='.$this->ServiceTicket;
		
	}

	return;
	
  }
}
