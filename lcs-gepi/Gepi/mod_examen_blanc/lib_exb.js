/*
$Id: lib_exb.js 3746 2009-11-20 08:58:25Z crob $
*/

// fonction permettant d'augmenter/r�duire un nombre via onKeyDown
// Pour relever les keyCode: http://www.asquare.net/javascript/tests/KeyCode.html
function nombre_plus_moins(id,e){

	if(document.getElementById(id)) {
		var touche=e.keyCode;

		//if((touche == '61')||(touche == '109')) {
		if((touche == '40')||(touche == '38')) {
			var nombre=document.getElementById(id).value;

			// Touche + -> PB: Le + est �crit quand m�me
			//if (touche == '61') {
			// Touche Fl�che Haut
			if (touche == '40') {
				nombre=eval(eval(nombre)+1);
			}
			// Touche -
			//if (touche == '109') {
			// Touche Fl�che Bas
			if (touche == '38') {
				nombre=eval(eval(nombre)-1);
			}

			if(nombre<0) {nombre=0;}

			document.getElementById(id).value=nombre;
		}
		/*
		else {
			alert('Autre touche')
		}
		*/
	}
	/*
	else {
		alert('id '+id+' inexistant')
	}
	*/
}

