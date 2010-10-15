<?php
/*
 * $Id: Controleur.php 4310 2010-04-15 10:13:51Z crob $
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Didier Blanqui
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
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
 * Une classe implantant un controleur
 * Code adapt� du controleur de Philippe Rigaux:
 * http://www.lamsade.dauphine.fr/rigaux/mysqlphp
 */
//require_once('tbs_class_php5.php');
require_once('Class.Vue.php');
abstract class Controleur {
    // Objets utilitaires
    protected $vue; // Composant "vue" pour produire les pages HTML

    /**
     * Constucteur: initialise les objets utilitaires
     */

    function __construct ()
    {
        /*
     * Le contr�leur initialise plusieurs objets utilitaires:
    * une instance du moteur de templates pour g�rer la vue
     */
        // Instanciation du moteur de templates
       // $this->vue = new clsTinyButStrong ;
    $this->vue = new ClassVue() ;

    }
}