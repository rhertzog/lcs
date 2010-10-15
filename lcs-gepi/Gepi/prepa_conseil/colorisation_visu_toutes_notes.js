/*
$Id: colorisation_visu_toutes_notes.js 4508 2010-05-28 06:49:44Z crob $
*/

	var cpt_couleur=1;
	//var table_body_couleur=document.getElementById('table_body_couleur');
	// Il faut �valuer table_body_couleur une fois le tableau charg�

	function add_tr_couleur() {
		var table_body_couleur=document.getElementById('table_body_couleur');

		// cr�ation des nouveaux noeuds
		var nouveauTR = document.createElement('tr');
		var nouveauTD1 = document.createElement('td');
		var nouveauTD2 = document.createElement('td');
		var nouveauTD3 = document.createElement('td');
		var nouveauTD4 = document.createElement('td');

		// raccord des noeuds
		//table_couleur.appendChild(nouveauTR);
		table_body_couleur.appendChild(nouveauTR);
		nouveauTR.appendChild(nouveauTD1);
		nouveauTR.appendChild(nouveauTD2);
		nouveauTR.appendChild(nouveauTD3);
		nouveauTR.appendChild(nouveauTD4);

		// Pour pouvoir tester l'existence de la ligne en parcourant les indices
		nouveauTR.id='tr_couleur_'+cpt_couleur;

		// Ajout de champs:
		add_field_couleur(nouveauTD1);
		add_select_couleur(nouveauTD2,'vtn_couleur_texte');
		add_select_couleur(nouveauTD3,'vtn_couleur_cellule');

		nouveauTD4.innerHTML='<a href=\'#colorisation_resultats\' onclick=\'suppr_ligne_couleur('+cpt_couleur+');return false;\'><img src=\'../images/delete16.png\' height=\'16\' width=\'16\' alt=\'Supprimer la ligne\' /></a>';

		// Pour r�-�taler les valeurs de bornes et refaire l'alternance des couleurs
		retouches_tab_couleur();

		cpt_couleur=cpt_couleur+1;
	}


	function add_field_couleur(td_conteneur) {
		// cr�ation des nouveaux noeuds
		var nouveauInput = document.createElement('input');

		nouveauInput.name = 'vtn_borne_couleur[]';
		nouveauInput.id = 'vtn_borne_couleur_'+cpt_couleur;
		nouveauInput.type = 'text';
		nouveauInput.setAttribute('value',cpt_couleur);
		nouveauInput.setAttribute('size','2');

		// raccord des noeuds
		td_conteneur.appendChild(nouveauInput);
	}

	function add_select_couleur(td_conteneur,nom) {
		// cr�ation des nouveaux noeuds
		var nouveauSelect = document.createElement('select');

		nouveauSelect.name = nom+'[]';
		nouveauSelect.id = nom+'_'+cpt_couleur;

		td_conteneur.appendChild(nouveauSelect);

		//tab_couleur=new Array('red', 'green', 'blue');

		// Pour pouvoir ne pas modifier la couleur par d�faut
		nouvelleOption=document.createElement('option');
		nouvelleOption.setAttribute('value','');
		nouvelleOption.innerHTML='---';
		nouveauSelect.appendChild(nouvelleOption);

		for(i=0;i<tab_couleur.length;i++) {
			nouvelleOption=document.createElement('option');
			nouvelleOption.setAttribute('value',tab_couleur[i]);

			nouvelleOption.setAttribute('style','background-color:'+tab_couleur[i]);

			nouvelleOption.innerHTML=tab_couleur[i];
			nouveauSelect.appendChild(nouvelleOption);
		}
	}

	var nb_suppr_couleur=0;
	function suppr_ligne_couleur(cpt_couleur) {
		//document.getElementById('tr_couleur_'+cpt_couleur).removeChild;

		// Cela merdoie... on peut r�ussir � supprimer la ligne en <thead>
		// Pour �viter cela:
		if(cpt_couleur-nb_suppr_couleur>0) {
			// Probl�me: Le cpt_couleur est fixe...
			//           ... si bien que si on supprime une ligne autre que la derni�re, la ligne de dernier rang ne correspond plus au rang cpt_couleur, mais � cpt_couleur-1
			document.getElementById('table_couleur').deleteRow(cpt_couleur-nb_suppr_couleur);
			nb_suppr_couleur++;
			retouches_tab_couleur();
		}
	}

	function retouches_tab_couleur() {
		// Couleurs altern�es des lignes
		j=0;
		for(i=0;i<=cpt_couleur;i++) {
			if(document.getElementById('tr_couleur_'+i)) {
				if(j%2==0) {
					document.getElementById('tr_couleur_'+i).className='lig1';
				}
				else {
					document.getElementById('tr_couleur_'+i).className='lig-1';
				}
				j++;
			}
		}

		// R�-�talement des bornes
		if(j>0) {
			tranche=Math.round(200/j)/10;

			j=0;
			for(i=0;i<=cpt_couleur;i++) {
				if(document.getElementById('vtn_borne_couleur_'+i)) {
					document.getElementById('vtn_borne_couleur_'+i).value=Math.round(10*tranche*(j+1))/10;
					j++;
				}
			}

			// On a des arrondis malheureux � 19.8 et 20.1
			// Pour mettre � 20 le dernier champ:
			for(i=cpt_couleur;i>=0;i--) {
				if(document.getElementById('vtn_borne_couleur_'+i)) {
					document.getElementById('vtn_borne_couleur_'+i).value=20;
					break;
				}
			}
		}
	}

	function vtn_couleurs_par_defaut() {
		tab_couleur_defaut=new Array('red','orangered','green');
		for(i=1;i<=tab_couleur_defaut.length;i++) {
			if(document.getElementById('vtn_couleur_texte_'+i)) {
				for(j=0;j<tab_couleur.length;j++) {
					if(tab_couleur[j]==tab_couleur_defaut[i-1]) {
						document.getElementById('vtn_couleur_texte_'+i).selectedIndex=j+1;
						break;
					}
				}
			}
		}
	}
