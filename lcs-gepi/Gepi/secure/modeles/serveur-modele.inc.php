<?php
# Une fois renseign�, pensez � renommer ce fichier en serveur.inc.php dans le r�pertoire secure

/*$serveur = array(
          'application' => array(
                      'domain' => 'nom du domaine du demandeur',
                      'RNE'    => '',
                      'api_key'=> 'exempledecodesecretcomplexe345ERDFbgftr570lk',
                      'nonce'  => 'enreserve',
                      'ip'     => '000.000.000.000',
                      'auth'   => array('all')
          )
);*/
# Pour utiliser le serveur de ressource de GEPI, chaque application ext�rieure doit disposer d'un compte dans
# ce fichier en respectant la syntaxe du tableau pr�c�dent.
# application est le nom de l'application (un ENT, ...), le client devra pr�ciser ce nom exact
# domain est le nom du domaine du client
# RNE est le num�ro de l'�tablissement (sert uniquement pour le multisite)
# api_key est la cl� unique de cette application
# nonce ne doit pas �tre modifi�
# ip est l'adresse IP du client
# auth est un tableau de la liste des m�thodes autoris�es pour cet utilisateur (all = toutes les m�thodes sont autoris�es).
?>
