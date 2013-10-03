<script language="JavaScript">
var memeFormulaire=(ancienFichierMaitre=="<#VAR FICHIER-MAITRE>")
var etOu=(memeFormulaire) ? ("<#THEMEPOLE1CASE2>"!="") : false; // vrai si ou
ua=navigator.userAgent.toLowerCase();
var ie=((ua.indexOf('msie') != -1) && (ua.indexOf('opera') == -1) && (ua.indexOf('webtv') == -1) && (location.href.indexOf('seenIEPage') == -1));
var Derouler=false // A priori, on ne déroule pas les restrictions.
var fichierEnCours="<#VAR FICHIER-MAITRE>";
var aChp; // définie dans Cherche
  function DivStatus( nom, numero )
	{
      var plusMoins=((nom=="a") || (nom=="b") || (nom=="c"))
	  var divID = nom + numero;
	  PcH = true;
	  if ( document.getElementById && document.getElementById( divID ) ) // Pour les navigateurs récents
		  Pdiv = document.getElementById( divID );
	  else if ( document.all && document.all[ divID ] ) // Pour les veilles versions
		  Pdiv = document.all[ divID ];
	  else if ( document.layers && document.layers[ divID ] ) // Pour les très veilles versions
		  Pdiv = document.layers[ divID ];
	  else
		  PcH = false;
	if ( PcH )
		  Pdiv.className = ( Pdiv.className == 'cachediv' ) ? (plusMoins) ? 'plusmoins' : '' : 'cachediv';
	}

function elargir(dico)
{
  if (!dico) document.forms[0].AUTOPOSTAGE.checked=false;
  document.forms[0].AUTOPOSTAGE.disabled=!dico;
}

function tube(ch)
{
  while (ch.substring(0,1)==" ")
   ch=ch.substring(1,ch.length)
  return "|"+ch+"|"
}
function ParDefaut(ch,defaut)
{
  while (ch.substring(0,1)==" ")
   ch=ch.substring(1,ch.length)
  if (ch=="")
   ch=defaut
  return ch
}

function BasculerRestrictions() {
  DivStatus( 'txt', '3' );
//  DivStatus( 'txt', '6' );
}

function ChangerOptions(nom,valeur)
{
  res=false
  if (valeur>'') {
	var selectBox = document.getElementById(nom);
    if (selectBox!=null)
	for (var i=0; i<selectBox.options.length; i++) {
	  if (selectBox.options[i].text==valeur) {
		selectBox.options[i].selected = true;
		if (i>0) res=true;
	  }
	}
  }
  return(res)
}

function effacer()
{
  document.forms[0].reset();
  prem=true;
  for (i=0;i<document.forms[0].elements.length;i++)
  with (document.forms[0].elements[i])
  {
	if (type=="text") {
      value="";
      if (prem) {nom=name; prem=false;}
    }
	if (type=="select-one") options[0].selected = true;
	if (type=="checkbox") checked=(name=="TYPEDENATURE") // on conserve FICTION et DOCUMENTAIRE
	if (name=="LANGAGE") checked=(value=="DICO")
  }
  if (!prem) document.getElementsByName(nom)[0].focus();
  // on efface les HIDDEN
  if (aChp!=undefined) for(var n=0; n<aChp.length; n++)
	document.getElementsByName(aChp[n].toUpperCase())[0].value=""
}

function InitChecked()
{
  chComment=ParDefaut("<#COMMENT>","TOUS").substr(0,4)
  chLangage=ParDefaut("<#LANGAGE>","DICO")
  chClassement=ParDefaut("<#CLASSEMENT>","<#SI RECHERCHE=THEME>PERTINENCE<#SINON RECHERCHE=THEME><#SI RECHERCHE=THESAURUS>PERTINENCE<#SINON RECHERCHE=THESAURUS>TITRE<#FINSI RECHERCHE=THESAURUS><#FINSI RECHERCHE=THEME>").substr(0,4)
  chTypeNature=ParDefaut("<#TYPEDENATURE>","Fiction|Documentaire")
  chTypeNature=tube(chTypeNature);
  chSupport=tube("<#SUPPORT1>")
  chAutopostage="<#AUTOPOSTAGE>"
  Derouler=false;
  for (i=0;i<document.forms[0].elements.length;i++)
  {
    nomI=document.forms[0].elements[i].name;
	if (nomI=="AUTOPOSTAGE")
<#SI RECHERCHE=THESAURUS>
	document.forms[0].elements[i].checked=(document.forms[0].elements[i].value==chAutopostage);
<#SINON RECHERCHE=THESAURUS>
	document.forms[0].elements[i].checked=((chAutopostage>"!") && (chAutopostage!="on"));
<#FINSI RECHERCHE=THESAURUS>
	else
	if (nomI=="LANGAGE") {
      document.forms[0].elements[i].value=chLangage
	  Radios=document.getElementsByName("LANGAGEVISIBLE")
	  if (Radios.length>0)
       for(var j=0; j<Radios.length; j++)
        Radios[j].checked=(Radios[j].value==chLangage)
    }
	else
	if (nomI=="COMMENT")
	{document.forms[0].elements[i].checked=(document.forms[0].elements[i].value.substr(0,4)==chComment)}
	else
	if (nomI=="CLASSEMENT")
	{document.forms[0].elements[i].checked=(document.forms[0].elements[i].value.substr(0,4)==chClassement)}
	else
	if (nomI=="TYPEDENATURE")
	{
	  ch="|"+document.forms[0].elements[i].value+"|"
	  document.forms[0].elements[i].checked=(chTypeNature.indexOf(ch)>=0)
	}
	else
	if (nomI=="SUPPORT1")
	{
	  ch="|"+document.forms[0].elements[i].value+"|"
	  document.forms[0].elements[i].checked=(chSupport.indexOf(ch)>=0)
	}
	else  // Date > espace
	if ((nomI.substr(0,4)=="DATE") && (document.forms[0].elements[i].value>" ")) Derouler=true
    else
	if (nomI.substr(0,5)=="DISPO")
	{
	  document.forms[0].elements[i].checked=!("<#DISPONIBLES>"=="")
	  if (document.forms[0].elements[i].checked) Derouler=true
	}
  }
  ChangerOptions('GENRES',"<#GENRES>")
  ChangerOptions('NATURES',"<#NATURES>")
  if (ChangerOptions('NIVEAUX',"<#NIVEAUX>")) Derouler=true
  if (ChangerOptions('DISCIPLINES',"<#DISCIPLINES>")) Derouler=true
  if (ChangerOptions('PUBLICS',"<#PUBLICS>")) Derouler=true
  if (!memeFormulaire) { effacer(); Derouler=false; }
  if (Derouler) BasculerRestrictions();
<#SI RECHERCHE=COLLECTION>
  document.getElementById("CONJONCTION_OU").checked=(etOu)
  document.getElementById("CONJONCTION_ET").checked=(!etOu)
  changeEtOu();
<#FINSI RECHERCHE=COLLECTION>
<#SI CONFIGURATION=SIMPLE>
  document.getElementById("CONJONCTION_OU").checked=(etOu)
  document.getElementById("CONJONCTION_ET").checked=(!etOu)
  changeEtOu();
<#FINSI CONFIGURATION=SIMPLE>
}
</script>
