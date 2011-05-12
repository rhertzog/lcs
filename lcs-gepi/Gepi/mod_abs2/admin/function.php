<?php
/*
 *
 * $Id: function.php 6699 2011-03-25 22:04:13Z jjacquard $
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

function ajoutMotifsParDefaut() {
    $motif = new AbsenceEleveMotif();
    $motif->setNom("M�dical");
    $motif->setCommentaire("L'�l�ve est absent pour raison m�dicale");
    if (AbsenceEleveMotifQuery::create()->filterByNom($motif->getNom())->find()->isEmpty()) {
	$motif->save();
    }

    $motif = new AbsenceEleveMotif();
    $motif->setNom("Familial");
    $motif->setCommentaire("L'�l�ve est absent pour raison familiale");
    if (AbsenceEleveMotifQuery::create()->filterByNom($motif->getNom())->find()->isEmpty()) {
	$motif->save();
    }

    $motif = new AbsenceEleveMotif();
    $motif->setNom("Sportive");
    $motif->setCommentaire("L'�l�ve est absent pour cause de comp�tition sportive");
    if (AbsenceEleveMotifQuery::create()->filterByNom($motif->getNom())->find()->isEmpty()) {
	$motif->save();
    }
}

function ajoutLieuxParDefaut() {
    $lieu = new AbsenceEleveLieu();
    $lieu->setNom("Etablissement");
    $lieu->setCommentaire("L'�l�ve est dans l'enceinte de l'�tablissement");
    if (AbsenceEleveLieuQuery::create()->filterByNom($lieu->getNom())->find()->isEmpty()) {
	$lieu->save();
    }
}

function initLieuEtab(){
    $lieu_etab=AbsenceEleveLieuQuery::create()->filterByNom("Etablissement")->findOne();
    if(is_null($lieu_etab)){
       $lieu_etab= new AbsenceEleveLieu();
       $lieu_etab->setNom("Etablissement");
       $lieu_etab->setCommentaire("L'�l�ve est dans l'enceinte de l'�tablissement");
       $lieu_etab->save();
    }
    return($lieu_etab->getId());
}

function ajoutJustificationsParDefaut() {
    $justifications = new AbsenceEleveJustification();
    $justifications->setNom("Certificat m�dical");
    $justifications->setCommentaire("Une justification �tablie par une autorit� m�dicale");
    if (AbsenceEleveJustificationQuery::create()->filterByNom($justifications->getNom())->find()->isEmpty()) {
	$justifications->save();
    }

    $justifications = new AbsenceEleveJustification();
    $justifications->setNom("Courrier familial");
    $justifications->setCommentaire("Justification par courrier de la famille");
    if (AbsenceEleveJustificationQuery::create()->filterByNom($justifications->getNom())->find()->isEmpty()) {
	$justifications->save();
    }

    $justifications = new AbsenceEleveJustification();
    $justifications->setNom("Justificatif d'une administration publique");
    $justifications->setCommentaire("Justification �mise par une administration publique");
    if (AbsenceEleveJustificationQuery::create()->filterByNom($justifications->getNom())->find()->isEmpty()) {
	$justifications->save();
    }
}

function ajoutTypesParDefaut() {
    $id_lieu_etab=initLieuEtab();
    $type = new AbsenceEleveType();
    $type->setNom("Absence scolaire");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'�l�ve n'est pas pr�sent pour suivre sa scolarit�.");
	$type->setJustificationExigible(true);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_VRAI);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Retard intercours");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'�l�ve est en retard lors de l'intercours");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Retard ext�rieur");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'�l�ve est en retard lors de son arriv�e dans l'�tablissement");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_VRAI);
	$type->setRetardBulletin(AbsenceEleveType::$RETARD_BULLETIN_VRAI);
        
	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Erreur de saisie");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("Il y a probablement une erreur de saisie sur cet enregistrement. Pour �tre non comptabilis�e,
            une saisie de type 'Erreur de saisie' ne doit �tre associ�e avec aucun autre type, mais exclusivement avec le type erreur de saisie.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_NON_PRECISE);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_NON_PRECISE);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

 	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Infirmerie");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'�l�ve est � l'infirmerie.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Sortie scolaire");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'�l�ve est en sortie scolaire.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(true);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Exclusion de l'�tablissement");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'�l�ve est exclu de l'�tablissement.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Exclusion/inclusion");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'�l�ve est exclu mais pr�sent au sein de l'�tablissement.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Exclusion de cours");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'�l�ve est exclu de cours.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);
	$type->setTypeSaisie(AbsenceEleveType::$TYPE_SAISIE_DISCIPLINE);
    $type->setIdLieu($id_lieu_etab);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Dispens� (�l�ve pr�sent)");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'�l�ve est dispens� mais pr�sent physiquement lors de la s�ance.");
	$type->setJustificationExigible(true);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Dispens� (�l�ve non pr�sent)");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'�l�ve est dispens� et non pr�sent physiquement lors de la s�ance.");
	$type->setJustificationExigible(true);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("Stage");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'�l�ve est en stage a l'ext�rieur de l'�tablissement.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_FAUX);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

    $type = new AbsenceEleveType();
    $type->setNom("�l�ve pr�sent");
    if (AbsenceEleveTypeQuery::create()->filterByNom($type->getNom())->find()->isEmpty()) {
	$type->setCommentaire("L'�l�ve est pr�sent.");
	$type->setJustificationExigible(false);
	$type->setSousResponsabiliteEtablissement(AbsenceEleveType::$SOUS_RESP_ETAB_VRAI);
	$type->setManquementObligationPresence(AbsenceEleveType::$MANQU_OBLIG_PRESE_FAUX);
    $type->setIdLieu($id_lieu_etab);
    
	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("professeur");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("cpe");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("scolarite");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$statut = new AbsenceEleveTypeStatutAutorise();
	$statut->setStatut("autre");
	$type->addAbsenceEleveTypeStatutAutorise($statut);
	$statut->save();

	$type->save();
    }

}
?>
