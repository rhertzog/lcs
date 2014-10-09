/*
---------------------------------------------------------
Ajout� par le lyc�e laetitia Bonaparte 2008 pour le LCS
---------------------------------------------------------
*/


	function getXhr(){
		var xhr = null; 
		if(window.XMLHttpRequest) // Firefox et autres
			xhr = new XMLHttpRequest();
	
		else if(window.ActiveXObject){ // Internet Explorer 
			try {
			        xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
			    xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
		else { // XMLHttpRequest non support� par le navigateur 
			alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest..."); 
			xhr = false; 
		} 
		
		return xhr;
	}
	
	
	// M�thode qui sera appel�e sur le click du bouton
	function go(varscript,idchoix){
		var xhr = getXhr();
		// On d�fini ce qu'on va faire quand on aura la r�ponse
		xhr.onreadystatechange = function(){
		// On ne fait quelque chose que si on a tout re�u et que le serveur est ok
			if(xhr.readyState == 4 && xhr.status == 200){
				leselect = xhr.responseText;
				// On se sert de innerHTML pour rajouter les options a la liste
				document.getElementById('scategorie').innerHTML = leselect;
			}
		}	
		
		// Ici on va voir comment faire du post
		xhr.open("POST","lcs_scripts/php/"+varscript,true);
		// ne pas oublier �a pour le post
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		// ne pas oublier de poster les arguments
		sel = document.getElementById('categorie');
		idcat = sel.options[sel.selectedIndex].value;
		
		//idchoix est utilis� pour la gestion des droits. Il correspond � la valeur du choix s�lectionn� dans la premi�re liste d�roulante.
		//Cette valeur est n�cessaire pour la gestion des droits car elle permet de savoir si les droits que l'utilisateur souhaite modifier s'applique � un groupe (donc ajout de @ devant le nom du groupe) ou � un utilisateur.
		//idchoix a la valeur 0 lorsque le script est appel� pour la gestion des groupes et non la gestion des droits.
		if(idchoix == "0"){
			xhr.send("idCat="+idcat);
		}
		else{
			xhr.send("idCat="+idcat+"&idchoice="+idchoix);
		}
						
	}

        // M�thode qui sera appel�e sur le click du bouton
	function choixtype(){		
		var xhr = getXhr();
		//alert("test1");
		// On d�fini ce qu'on va faire quand on aura la r�ponse
		xhr.onreadystatechange = function(){
		// On ne fait quelque chose que si on a tout re�u et que le serveur est ok
			//alert("mess :"+xhr.readyState);
			if(xhr.readyState == 4 && xhr.status == 200){
				leselect = xhr.responseText;
				// On se sert de innerHTML pour rajouter les options a la liste
				document.getElementById('stype').innerHTML = leselect;
				//alert(leselect);
			}
		}
		
		// Ici on va voir comment faire du post
		xhr.open("POST","lcs_scripts/php/sousType.php",true);
		//alert("test2");
		// ne pas oublier �a pour le post
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		// ne pas oublier de poster les arguments
		sel = document.getElementById('type');		
		idtype = sel.options[sel.selectedIndex].value;		
		//alert("test3:"+idtype);	
		//pour que la troisi�me liste d�roulante disparaisse quand la valeur de la premi�re est 0, 2 ou 3
		if((idtype == "0")||(idtype == "2")||(idtype == "3")) {
			var xhrbis = getXhr();
			xhrbis.onreadystatechange = function(){
				if(xhrbis.readyState == 4 && xhrbis.status == 200){
					leselect = xhrbis.responseText;
					document.getElementById('scategorie').innerHTML = leselect;
				}
			}

			xhrbis.open("POST","lcs_scripts/php/sousCategorie.php",true);
			xhrbis.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			idcat = "0";
			xhrbis.send("idCat="+idcat);
		}
		
		xhr.send("idType="+idtype);

	}

	function ajoutmembres(id,memb)
	{
		
		//id = ensemble des login selectionn� dans la liste deroulante
		//memb = liste des membres d�j� dans le groupe
		

		//si l'utilisateur ne selectionne rien, on envoi un message d'erreur
		if(id == "") {
			alert("Veuillez s�lectionner au moins un utilisateur.");
			return false; //on sort de la fonction
		}
		
		//on extrait chaque login de "id"
		
		
		liste = "";
		
		tabmemb = id.split("|");
		
			for (i=0; i < tabmemb.length - 1 ;i++) {
				var maReg = new RegExp(tabmemb[i], "gi");
				var contenu = memb.toString();
						
				if( contenu.search(maReg) == -1 ) {
					document.forms.membres.group_new_members.value += tabmemb[i] + "\n";
				}
				else {
					liste += tabmemb[i] + "\n"; 	
				}

			}
		
			if (liste != "") {
				alert("le ou les utilisateurs suivants n'ont pas �t� ajout�s car ils appartiennent d�j� au groupe: \n"+liste);
			}
	}

        function aclchoix(idchoix){
		var xhr = getXhr();
		// On d�fini ce qu'on va faire quand on aura la r�ponse
		xhr.onreadystatechange = function(){
			// On ne fait quelque chose que si on a tout re�u et que le serveur est ok
			if(xhr.readyState == 4 && xhr.status == 200){
				leselect = xhr.responseText;
				// On se sert de innerHTML pour rajouter les options a la liste
				document.getElementById('stype').innerHTML = leselect;
			}
		}

		// Ici on va voir comment faire du post
		xhr.open("POST","lcs_scripts/php/choix.php",true);
		// ne pas oublier �a pour le post
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		// ne pas oublier de poster les arguments
		sel = document.getElementById('choix');
                
		if(idchoix != "1") {
			var xhrbis = getXhr();
			xhrbis.onreadystatechange = function(){
				if(xhrbis.readyState == 4 && xhrbis.status == 200){
					leselect = xhrbis.responseText;
					document.getElementById('scategorie').innerHTML = leselect;
				}
			}

		        xhrbis.open("POST","lcs_scripts/php/sousChoix.php",true);
			xhrbis.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			idcat = "0";
			xhrbis.send("idCat="+idcat);
		}
		
		xhr.send("idchoix="+idchoix);
	
	}

		function traitement(id,memb,typeacl,idchoix,sig){
					liste = "";
					
					if(( memb == "*")||( memb == "+")){
						switch(typeacl) {
							case "read_acl":
											document.acls.read_acl.value = "";
											break;
							case "write_acl":
											document.acls.write_acl.value = "";
											break;
							case "comment_acl":
											document.acls.comment_acl.value = "";
											break;												
						}
					}
				
					tab = id.split("|");
					
					for (i=0; i < tab.length - 1 ;i++) {
						var maReg = new RegExp(tab[i], "gi");
						var contenu = memb.toString();

						if( contenu.search(maReg) == -1 ) {
							if ((idchoix == "3")||(idchoix == "2")||(idchoix == "6")){
								val = "\n" + sig+"@"+tab[i];
							}
							else{
								val = "\n" + sig+tab[i];
							}
							
							switch(typeacl) {
								case "read_acl":
												document.acls.read_acl.value += val;
												break;
								case "write_acl":
												document.acls.write_acl.value += val;
												break;
								case "comment_acl":
												document.acls.comment_acl.value += val;
												break;												
							}
						}
						else{
							liste += "\n" + tab[i];
						}
					}

					if (liste != "") {
					        return liste;
					}
					else {
						return 0;
					}
	
		}	
		
		
		function traitement2(id,memb,typeacl,idchoix,sig){
			if(idchoix == "7"){
				symb = "Eleves";
			}
			else if(idchoix == "8"){
				symb = "Profs";
			}
			else{//idchoix=9
				symb = "Administratifs";
			}
			
            if(( memb == "*")||( memb == "+")){
				switch(typeacl) {
					case "read_acl":
									document.acls.read_acl.value = "";
									break;
					case "write_acl":
									document.acls.write_acl.value = "";
									break;
					case "comment_acl":
									document.acls.comment_acl.value = "";
									break;												
				}
			}
			
			var maReg = new RegExp(symb, "gi");
			var contenu = memb.toString();

			if( contenu.search(maReg) == -1 ) {
				switch(typeacl) {
					case "read_acl":
									document.acls.read_acl.value += "\n" + sig + "@" + symb;
									break;
					case "write_acl":
									document.acls.write_acl.value += "\n" + sig + "@" + symb;
									break;
					case "comment_acl":
									document.acls.comment_acl.value += "\n" + sig + "@" + symb;
									break;												
				}
				
				res = "0";
			}
			else{
				res = symb;
			}

			return res;
		}
		
		
        function modifdroits(id,memblect,membecr,membcom,lecture,ecriture,commentaire,idchoix) {
	                //id = ensemble des login selectionn� dans la liste deroulante
			//memblect = liste des membres dans la case "lecture"
			//membecr = liste des membres dans la case "�criture"
			//membcom = liste des membres dans la case "commentaire"
	                //lecture = permet de savoir si un des 2 boutons radio concernant la lecture a �t� coch�
			//	    3 valeurs possibles : "aut" pour "autoriser", "ref" pour "refuser" et "0" si ce n'est pas coch�
			//ecriture = idem mais pour l'ecriture
			//commentaire = idem pour les commentaires
			//idchoix = num�ro de la cat�gorie choisie dans la premi�re liste d�roulante
	
	switch(idchoix){ 
		case "1" : //passe � 6
		case "2" : //passe � 6
		case "3" : //passe � 6
		case "4" : //passe � 6
		case "5" : //passe � 6
		case "6" :
		
			switch(lecture){
				case "aut": 
					typeacl = "read_acl";
					sig = "";
					
					res = traitement(id,memblect,typeacl,idchoix,sig);
					
					if (res != "0") {
					        alert("le ou les utilisateurs suivants n'ont pas �t� ajout�s pour les droits de lecture car ils poss�dent d�j� ces droits: \n"+res);
					}
										
					break;
					
				case "ref": 
					typeacl = "read_acl";
					sig = "!";
					
					res = traitement(id,memblect,typeacl,idchoix,sig);

					if (res != "0") {
						alert("le ou les utilisateurs suivants n'ont pas �t� ajout�s pour les droits de lecture car ils poss�dent d�j� ces droits: \n"+res);
					}

					break;
				
				case "0": break;
			}

			switch(ecriture){
				case "aut": 
					typeacl = "write_acl";
					sig = "";
					
					res = traitement(id,memblect,typeacl,idchoix,sig);

					if (res != "0") {
						alert("le ou les utilisateurs suivants n'ont pas �t� ajout�s pour les droits d'�criture car ils poss�dent d�j� ces droits: \n"+res);
					}

					break;
				
				case "ref": 
					typeacl = "write_acl";
					sig = "!";
					
					res = traitement(id,memblect,typeacl,idchoix,sig);

					if (res != "0") {
						alert("le ou les utilisateurs suivants n'ont pas �t� ajout�s pour les droits d'�criture car ils poss�dent d�j� ces droits: \n"+res);
					}

					break;

				case "0": break;
			}

			switch(commentaire){
				case "aut": 
					typeacl = "comment_acl";
					sig = "";
					
					res = traitement(id,memblect,typeacl,idchoix,sig);

					if (res != "0") {
						alert("le ou les utilisateurs suivants n'ont pas �t� ajout�s pour les droits de commentaire car ils poss�dent d�j� ces droits: \n"+res);
					}

					break;

				case "ref":
					typeacl = "comment_acl";
					sig = "!";
					
					res = traitement(id,memblect,typeacl,idchoix,sig);

					if (res != "0") {
						alert("le ou les utilisateurs suivants n'ont pas �t� ajout�s pour les droits de commentaire car ils poss�dent d�j� ces droits: \n"+res);
					}

					break;
				
				case "0": break;
			}
		
			break;
		
		case "7" : //tous les �l�ves : @Eleves
			   //passe � 8

		case "8" : //tous les profs : @Profs
			  //passe � 9

		case "9" : //tout le personnel administratif
                        switch(lecture){	
				case "aut" :
					typeacl = "read_acl";
					sig = "";
					
					res = traitement2(id,memblect,typeacl,idchoix,sig);
					
					if (res != "0") {
					        alert("Tous les "+symb+" ont d�j� des droits configur�s pour la lecture.");
					}
					
					break;

				case "ref" :
					typeacl = "read_acl";
					sig = "!";
					
					res = traitement2(id,memblect,typeacl,idchoix,sig);
					
					if (res != "0") {
					        alert("Tous les "+symb+" ont d�j� des droits configur�s pour la lecture.");
					}
					
					break;

				case "0" :
					break;
			}

			switch(ecriture){
				case "aut" :
					typeacl = "write_acl";
					sig = "";
					
					res = traitement2(id,memblect,typeacl,idchoix,sig);
					
					if (res != "0") {
					        alert("Tous les "+symb+" ont d�j� des droits configur�s pour l'�criture.");
					}

					break;

				 case "ref" :
					typeacl = "write_acl";
					sig = "!";
					
					res = traitement2(id,memblect,typeacl,idchoix,sig);
					
					if (res != "0") {
					        alert("Tous les "+symb+" ont d�j� des droits configur�s pour l'�criture.");
					}

					break;

				case "0" :
					break;
			}

			switch(commentaire){
                		case "aut" :
					typeacl = "comment_acl";
					sig = "";
					
					res = traitement2(id,memblect,typeacl,idchoix,sig);
					
					if (res != "0") {
					        alert("Tous les "+symb+" ont d�j� des droits configur�s pour les commentaires.");
					}

					break;

				case "ref" :
					typeacl = "comment_acl";
					sig = "!";
					
					res = traitement2(id,memblect,typeacl,idchoix,sig);
					
					if (res != "0") {
					        alert("Tous les "+symb+" ont d�j� des droits configur�s pour les commentaires.");
					}

					break;

				case "0" :
					break;
			}

			break;

		case "10" : //tous les utilisateurs authentifi�s
			   //passe � 11

		case "11" : //tous les utilisateurs m�me non authentifi�s
			switch(lecture){
				case "aut" :

					if(idchoix == "10"){
						symb = "+";
					}
					else{//idchoix=11
						symb = "*";
					}
					
					var maReg = new RegExp("\\"+symb, "gi");
					var contenulect = memblect.toString();

					if( contenulect.search(maReg) == -1 ) {
							document.acls.read_acl.value += "\n"+symb;
					}
					else{
						alert("Tous les utilisateurs ont d�j� des droits configur�s pour la lecture.");
					}
					
					break;

				case "ref" : 
					
					if(idchoix == "10"){
						symb = "+";
					}
					else{//idchoix=11
						symb = "*";
					}
					
					var maReg = new RegExp("\\"+symb, "gi");
					var contenulect = memblect.toString();

					if( contenulect.search(maReg) == -1 ) {
						document.acls.read_acl.value += "\n!"+symb;
					}
					else{
						alert("Tous les utilisateurs ont d�j� des droits configur�s pour la lecture.");
					}
					
					break;

				case "0" :
					break;
			}
			
			switch(ecriture){
				case "aut" : 
					
					if(idchoix == "10"){
						symb = "+";
					}
					else{//idchoix=11
						symb = "*";
					}
					
					var maReg = new RegExp("\\"+symb, "gi");
					var contenuecr = membecr.toString();

					if( contenuecr.search(maReg) == -1 ) {
						document.acls.write_acl.value += "\n"+symb;
					}
					else{
						alert("Tous les utilisateurs ont d�j� des droits configur�s pour l'�criture.");
					}
					
					break;
				
				case "ref" : 
					
					if(idchoix == "10"){
						symb = "+";
					}
					else{//idchoix=11
						symb = "*";
					}
					
					var maReg = new RegExp("\\"+symb, "gi");
					var contenuecr = membecr.toString();

					if( contenuecr.search(maReg) == -1 ) {
						document.acls.write_acl.value += "\n!"+symb;
					}
					else{
						alert("Tous les utilisateurs ont d�j� des droits configur�s pour l'�criture.");
					}
					
					break;

				case "0" :
					break;
			}

			switch(commentaire){
				case "aut" : 
					
					if(idchoix == "10"){
						symb = "+";
					}
					else{//idchoix=11
						symb = "*";
					}
					
					var maReg = new RegExp("\\"+symb, "gi");
					var contenucom = membcom.toString();

					if( contenucom.search(maReg) == -1 ) {
						document.acls.comment_acl.value += "\n"+symb;
					}
					else{
						alert("Tous les utilisateurs ont d�j� des droits configur�s pour l'ajout de commentaires.");
					}
					
					break;

				case "ref" : 
					
					if(idchoix == "10"){
						symb = "+";
					}
					else{//idchoix=11
						symb = "*";
					}
					
					var maReg = new RegExp("\\"+symb, "gi");
					var contenucom = membcom.toString();

					if( contenucom.search(maReg) == -1 ) {
						document.acls.comment_acl.value += "\n!"+symb;
					}
					else{
						alert("Tous les utilisateurs ont d�j� des droits configur�s pour l'ajout de commentaires.");
					}
					
					break;

				case "0" :
					break;
			}
			
			break;
	
	}

			
}	
