<?php
if(!defined('SACoche')) {exit('Ce fichier ne peut être appelé directement !');}

/**
 * Ce fichier n'est présent que sur l'installation SACoche du département du Rhône.
 * 
 * Il est chargé par les autres fichiers Laclasse-*.php de ce dossier /webservices/.
 * 
 * @author Thomas Crespin <thomas.crespin@sesamath.net>
 */

class Laclasse
{

  /**
   * Constantes pour configurer le service
   * @author Pierre-Gilles Levallois <pgl@erasme.org>
   */
  const ANNUAIRE_API_ETAB  = 'http://www.dev.laclasse.com/api/app/etablissements/';
  const ANNUAIRE_API_USER  = 'http://www.dev.laclasse.com/api/app/users/';       // inutilisé
  const ANNUAIRE_API_APPLI = 'http://www.dev.laclasse.com/api/app/application/'; // inutilisé
  const ANNUAIRE_APP_ID    = 'LPC';
  const ANNUAIRE_API_KEY   = '20gRnRX4o9Mf1KwR3jwLSB6Nf/0dMYIjKinwzCkafoA=';

  /**
   * Méthode pour générer l'URL à appeler afin de récupérer les informations de l'annuaire.
   * @author Pierre-Gilles Levallois <pgl@erasme.org>
   *
   * @param string $url
   * @param string $params
   * @return array
   */
  private static function generer_url_appel($url, $params)
  {
    $canonical_string = $url.'?';
    $query_string = '';
    // 1. trier les paramètres
    ksort($params);
    // 2. construction de la canonical string
    foreach ($params as $k => $v)
    {
      $query_string .= $k.'='.urlencode($v).'&';
    }
    $query_string = trim($query_string,'&');
    $canonical_string .= $query_string;
    // 3. ajout du timestamp
    $timestamp = date("Y-m-d\TH:i:s");
    $canonical_string .= ';'.$timestamp;
    // 4. Ajout de l'identifiant d'application (connu de l'annuaire, et qui lui permet de comprendre la signature)
    $app_id = Laclasse::ANNUAIRE_APP_ID;
    $canonical_string .= ';'.$app_id; 
    // 5. Calcul de la signature : sha1 et Encodage Base64
    $signature = 'signature='.urlencode(base64_encode(hash_hmac('sha1', $canonical_string, Laclasse::ANNUAIRE_API_KEY, TRUE)));
    // Retour de la requete constituée
    $req = $url . '?' .  $query_string . ';app_id=' . $app_id . ';timestamp=' . urlencode($timestamp) . ';' . $signature;
    return $req;
  }

  /**
   * Méthode pour envoyer une requête à l'annuaire
   * Retourne un tableau avec les infos ou une chaine d'erreur ou stoppe (exit).
   *
   * Pour [api] valeurs [ profs | eleves | parents ] prévues mais pas présentes.
   * Pour [api] valeurs [ classes | groupes ] n'apportent aucune informations intéressante supplémentaire.
   *
   * @param string $uai
   * @param string $api   '' | matieres | users
   * @param bool   $exit_if_error
   * @param bool   $with_details   Retourne la même chose sauf pour [api=''] qui ne renvoie que les infos sur l'établissement si [with_details=FALSE]
   * @return array
   */

  public static function get_info_from_annuaire($uai,$api,$exit_if_error,$with_details)
  {
    $annuaire_adresse   = ($api) ? Laclasse::ANNUAIRE_API_ETAB.$uai.'/'.$api : Laclasse::ANNUAIRE_API_ETAB.$uai ;
    $annuaire_tab_param = ($with_details) ? array('expand' => 'true') : array() ;

    $json_reponse = cURL::get_contents( Laclasse::generer_url_appel( $annuaire_adresse , $annuaire_tab_param ) );

    if(substr($json_reponse,0,6)=='Erreur')
    {
      // On récupère par exemple 
      // "Erreur : The requested URL returned error: 401 Unauthorized" si UAI étranger
      // "Erreur : The requested URL returned error: 404 Not Found"    si UAI inconnu
      if($exit_if_error)
      {
        exit( json_encode( array( 'error' => $uai.' '.$api.' - '.$json_reponse ) ) );
      }
      else
      {
        return $uai.' '.$api.' - '.$json_reponse;
      }
    }

    $tab_reponse = json_decode($json_reponse,TRUE);

    if($tab_reponse===NULL)
    {
      if($exit_if_error)
      {
        exit( json_encode( array( 'error' => $uai.' '.$api.' - Chaîne JSON incorrecte : '.$json_reponse ) ) );
      }
      else
      {
        return $uai.' '.$api.' - Chaîne JSON incorrecte : '.$json_reponse;
      }
    }
    elseif(isset($tab_reponse['error']))
    {
      // On récupère par exemple {"error":"Non authentifié"} si API non reconnue ou clef d'API incorrecte
      if($exit_if_error)
      {
        exit($json_reponse);
      }
      else
      {
        return $uai.' '.$api.' - Erreur réponse annuaire : '.$json_reponse;
      }
    }

    return $tab_reponse;
  }

}
?>
