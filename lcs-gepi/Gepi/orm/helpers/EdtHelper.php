<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * Classe de helpers sur les edt
 */
class EdtHelper {

   public static $semaine_declaration = array("dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi");

 /**
   * Renvoi vrai ou faux selon que l'�tablissement est ouvert � date et l'heure indiqu�e
   *
   * @param      mixed $dt
   * @return boolean
   */
    public static function isEtablissementOuvert($dt){
            if (!EdtHelper::isHoraireOuvert($dt) || !EdtHelper::isJourneeOuverte($dt)) {
                return false;
            } else {
                return true;
            }
    }


  /**
   * Renvoi vrai ou faux selon que l'�tablissement est ouvert � date (jour) indiqu�e
   *
   * @param      dateTime $dt
   * @return boolean false/true
   */
    public static function isJourneeOuverte($dt){
            $jour_semaine = EdtHelper::$semaine_declaration[$dt->format("w")];
	    $horaire_tab = EdtHorairesEtablissementPeer::retrieveAllEdtHorairesEtablissementArrayCopy();
            if (!isset($horaire_tab[$jour_semaine])) {
                //etab ferm�
                return false;
            }

            //est-ce une p�riode ouverte
            $edt_periode_courante = EdtCalendrierPeriodePeer::retrieveEdtCalendrierPeriodeActuelle($dt);
            if ($edt_periode_courante != null
                    && ($edt_periode_courante->getEtabfermeCalendrier() == 0 || $edt_periode_courante->getEtabvacancesCalendrier() == 1)) {
                //etab ferm�
                return false;
            }

            return true;
    }

 /**
   * Renvoi vrai ou faux selon que l'�tablissement est ouvert � cette horaire (sans se pr�ocupper des vacances)
   *
   * @param      mixed $dt
   * @return boolean
   */
    public static function isHoraireOuvert($dt){
            $jour_semaine = EdtHelper::$semaine_declaration[$dt->format("w")];
	    $horaire_tab = EdtHorairesEtablissementPeer::retrieveAllEdtHorairesEtablissementArrayCopy();
            if (isset($horaire_tab[$jour_semaine])) {
                $horaire = $horaire_tab[$jour_semaine];
            } else {
                return false;
            }
            if ($dt->format('Hi') >= $horaire->getFermetureHoraireEtablissement('Hi')
                    ||	$dt->format('Hi') < $horaire->getOuvertureHoraireEtablissement('Hi')) {
                //etab ferm�
                return false;
            }

            return true;
    }
}
?>