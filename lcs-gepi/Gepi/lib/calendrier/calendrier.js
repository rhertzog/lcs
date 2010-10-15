/**
 ---------------------------------------------------------------------------------------------
 * METHODES JAVASCRIPT
 * Calendrier
 * calendrier.js
 ---------------------------------------------------------------------------------------------
 */

/**
 * Reload la fen�tre avec les nouveaux mois et ann�e choisis
 *
 * @param   object      frm     L'object document du formulaire
 */
function reload(frm){
    var mois = frm.elements['mois'];
    var annee = frm.elements['annee'];
    //Debug du mois et ann�e
    var index1 = mois.options[mois.selectedIndex].value;
    var index2 = annee.options[annee.selectedIndex].value;
    //Envoi du formulaire
    frm.submit();
}

/**
 * Ajoute un z�ro devant le jour et le mois s'ils sont plus petit que 10
 *
 * @param   integer     jour        Le num�ro du jour dans le mois
 * @param   integer     mois        Le num�ro du mois
 */
function checkNum(jour, mois){
    tab = new Array();
    tab[0] = jour;
    tab[1] = mois;
    if (this.checkzero){
        if (jour < 10){
            tab[0] = "0" + jour;
        }
        if (mois < 10){
            tab[1] = "0" + mois;
        }
    }
    return tab;
}

/**
 * Cr�� la string de retour
 *
 * C'est ici que la string est cr��. C'est �galement ici que le champ du formulaire
 * de la page d'appel re�oit la valeur. La fen�tre s'auto-fermera ensuite toute
 * seule comme une grande.
 * Paisible est l'�tudiant qui comme la rivi�re peut suivre son cours sans quitter son lit...
 *
 * @param   integer     jour        Le num�ro du jour dans le mois
 */
function submitDate(jour){
    tab = this.checkNum(jour, this.moisc);
    jour = tab[0];
    mois = tab[1];
    if (this.ordre[0] && this.ordre[0] == "M"){
        if (this.ordre[1] && this.ordre[1] == "A"){
            val = mois + this.format + this.anneec + this.format + jour;
        }else{
            val = mois + this.format + jour + this.format + this.anneec;
        }
    }else if (this.ordre[0] && this.ordre[0] == "J"){
        if (this.ordre[1] == "A"){
            val = jour + this.format + this.anneec + this.format + mois;
        }else{
            val = jour + this.format + mois + this.format + this.anneec;
        }
    }else{
        if (this.ordre[1] && this.ordre[1] == "J"){
            val = this.anneec + this.format + jour + this.format + mois;
        }else{
            val = this.anneec + this.format + mois + this.format + jour;
        }
    }
    //On agit selon qu'on est dans une popup ou non
    this.finOperation(val);
}