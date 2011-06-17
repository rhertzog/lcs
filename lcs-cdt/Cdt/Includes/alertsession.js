var debut=new Date()

function jouer_musique(n) {
	document.getElementById("musique").innerHTML = '<p><object type="audio/mpeg" width="0" height="0" data="../Includes/'+ n +'.mp3"><param name="filename" value="../Includes/'+ n + '.mp3" /><param name="autostart" value="true" /><param name="loop" value="false" /><param name="volume" value="100% /></object></p>';
	
}

function stopper_musique() {
	document.getElementById("musique").innerHTML = '';
	
}



function avertissement(){
  var maintenant=new Date();
  var depuis=(maintenant-debut)/1000;
  if (depuis>duree-300 && depuis<duree) {
   
    la=new Date();
    var reste = Math.floor(duree - depuis);
    var hr=la.getHours();
    var mi=la.getMinutes();
    var se=la.getSeconds();
    var ilest = hr + " h " + mi + " mn " + se + " s ";
    jouer_musique(44);
    alert("A "+ ilest + ", il vous reste  "+ reste +" secondes  pour enregistrer votre travail, avant que votre session expire !");
    }
  setTimeout("avertissement()",60000);
}
function finsession(){
  var maintenant2=new Date();
  var depuis2=(maintenant2-debut)/1000;
  if (depuis2>duree) {
  		jouer_musique(31);
       alert(" Votre session a expir\351. \n Les donn\351es non enregistr\351es seront perdues !");
       stopper_musique();
    }
  
}

