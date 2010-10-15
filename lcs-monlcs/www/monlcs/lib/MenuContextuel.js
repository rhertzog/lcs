/**********************************************************************************************************************************************************/
/**********************************************************************************************************************************************************/
/******************************************************* OB1 - www.warmj.com - 2006 */
/**********************************************************************************************************************************************************/
/**********************************************************************************************************************************************************/
var MCenable=true;
var MCie5=document.all&&document.getElementById;
var MCns6=document.getElementById&&!document.all;
var menuobj=null;
/**********************************************************************************************************************************************************/
/******************************************************* Coordonnées de la souris */
/******************************************************* A ne pas changer */
/**********************************************************************************************************************************************************/
/**********************************************************************************************************************************************************/
/* Horizontale */
function ReturnXdoc(e)
{
	var event=typeof window.event!='undefined'?window.event:e;
	var Xfen, Xdoc, scrollL;
	if(document.documentElement.scrollLeft!=0) scrollL=document.documentElement.scrollLeft;
	else if(document.body.scrollLeft!=0) scrollL=document.body.scrollLeft;
	else scrollL=0;	
	Xfen=event.clientX ;
	return(Xfen+scrollL);
	
}
/* Verticale */
function ReturnYdoc(e)
{
	var event=typeof window.event!='undefined'?window.event:e;
	var Yfen, Ydoc, scrollT;
	if(document.documentElement.scrollTop!=0) scrollT=document.documentElement.scrollTop;
	else if(document.body.scrollTop!=0) scrollT=document.body.scrollTop;
	else scrollT=0;
	Yfen=event.clientY || 50;
	return Yfen+scrollT;	
}
/**********************************************************************************************************************************************************/
/******************************************************* Function d'affichage */
/******************************************************* A ne pas changer */
/**********************************************************************************************************************************************************/
var MCdisplayURL=true;	/* Variable globale autorisant l'affichage de l'argument "title" dans la barre de status du navigateur */
var MenuContextuelXdoc=null;		/* Variable globale pour afficher les menus secondaire */
var MenuContextuelYdoc=null;
/**********************************************************************************************************************************************************/
/* Affichage du div lorsque l'on apuie sur le bouton droit de la souris */
function showmenuie5(e)
{
	if(!MCenable) { hidemenuie5(e);  return false; }
	if(!menuobj) { hidemenuie5(e); }
	CloseMenuItems();
	MenuContextuelXdoc=ReturnXdoc(e);
	MenuContextuelYdoc=ReturnYdoc(e);
	menuobj.style.left=MenuContextuelXdoc+'px';
	menuobj.style.top=MenuContextuelYdoc+'px';
	MenuContextuelTemp();
	menuobj.style.visibility="visible";	
	window.status='';
	return false;
}

/* Effacer le menu lorsque l'on click en dehors du div */
function hidemenuie5(e)
{
	if(menuobj) menuobj.style.visibility="hidden";
	CloseMenuItems();
	window.status='';
}
/* effets lorsqu'on passe sur un lien du menu */
function highlightie5(e,back,color)
{
	var firingobj=MCie5?event.srcElement:e.target;
	if (firingobj.className=="menuitems"||MCns6&&firingobj.parentNode.className=="menuitems"
		||firingobj.className=="MenuBis"||MCns6&&firingobj.parentNode.className=="MenuBis")
	{
		if (MCns6&&firingobj.parentNode.className=="menuitems") firingobj=firingobj.parentNode;
		firingobj.style.backgroundColor=back;
		firingobj.style.color=color;
		/* affichage dans la barre du navigateur de l'argument "title"      si MCdisplayURL = true;  */
		if (MCdisplayURL) 
		{
			if(firingobj.getAttribute("title")) window.status=firingobj.getAttribute("title");
			else window.status='';
		}
		/* affichage du menu secondaire */
		if(firingobj.getAttribute("MenuContextuel") && menuobj.style.visibility=="visible") OpenMenuItems(firingobj.getAttribute("MenuContextuel"),e);
		else CloseMenuItems();
	}
}
/* effets lorsqu'on sort d'un lien du menu */
function lowlightie5(e,back,color)
{
	var firingobj=MCie5?event.srcElement:e.target
	if (firingobj.className=="menuitems"||(MCns6&&firingobj.parentNode.className=="menuitems")
		||firingobj.className=="MenuBis"||(MCns6&&firingobj.parentNode.className=="MenuBis"))
	{
		if (MCns6&&firingobj.parentNode.className=="menuitems") firingobj=firingobj.parentNode;
		firingobj.style.backgroundColor=back;
		firingobj.style.color=color;
		window.status='';
	}
}
/* effets lorsqu'on clique sur un lien du menu */
function jumptoie5(e)
{
	return false;
}
/* Détection des événements */
if (MCie5||MCns6)
{
	document.oncontextmenu=showmenuie5; /* Détecte le click droit : affiche le menu contextuel */
	document.onclick=hidemenuie5; 		/* Détecte le click gauche : efface le menu contextuel */
}
/**********************************************************************************************************************************************************/
/**********************************************************************************************************************************************************/
/**********************************************************************************************************************************************************/
/*************************************************** Ouverture des menu secondaires **************************************************************************/
/**********************************************************************************************************************************************************/
/**********************************************************************************************************************************************************/
var menuBisobj=null; /* Objet représentant le DIV du menu secondaire */
/**********************************************************************************************************************************************************/
function OpenMenuItems(id,e)
{	
	if(menuBisobj==document.getElementById(id)) return;
	if(menuBisobj) menuBisobj.style.visibility='hidden';
	menuBisobj=document.getElementById(id);
	menuBisobj.style.left=(MenuContextuelXdoc+150)+'px'; /* 150 représente la taille du premier DIV */
	menuBisobj.style.top=(ReturnYdoc(e)-20)+'px';
	menuBisobj.style.visibility="visible";
}
function CloseMenuItems()
{
	if(menuBisobj)	menuBisobj.style.visibility='hidden';
	menuBisobj=null;
}
/**********************************************************************************************************************************************************/
/**********************************************************************************************************************************************************/
/**********************************************************************************************************************************************************/
/******************************************************* Affichage des menu en Javascript */
/**********************************************************************************************************************************************************/
/**********************************************************************************************************************************************************/
var StringMenuItemsJSTop=''; var StringMenuItemsJSBottom='';
/**********************************************************************************************************************************************************/
/* Affichage du menu temporaire */
function MenuContextuelTemp()
{
	if(document.getElementById("menuitemsJSTop"))
	{
		SetDiv('menuitemsJSTop',StringMenuItemsJSTop);
		if(StringMenuItemsJSTop) document.getElementById("menuitemsJSTop").style.display='block';
		else document.getElementById("menuitemsJSTop").style.display='none';
	}
	if(document.getElementById("menuitemsJSBottom"))
	{
		SetDiv('menuitemsJSBottom',StringMenuItemsJSBottom);
		if(StringMenuItemsJSBottom) document.getElementById("menuitemsJSBottom").style.display='block';
		else document.getElementById("menuitemsJSBottom").style.display='none';
	}
}
/******************************************************* CreatMenuContextuel */
/* Creation d'un lien du menu */
function CreatMenuContextuel(js,title,name,img)
{
	return '<div class="menuitems" onclick="'+js+'" title="'+title+'"><img src="'+img+'" align="absmiddle" style="padding:0 3px;"/>'+name+'</div>';
}
/******************************************************* Top */
/* Exemple pour afficher des liens en haut du menu */
function DelMenuContextuelTop()
{
	StringMenuItemsJSTop='';
}
function AddMenuContextuelTop(url,title)
{
	StringMenuItemsJSTop=CreatMenuContextuel('window.location=\''+url+'\';',title,title,'puce2.gif');
	StringMenuItemsJSTop+='<hr/>';
}
/******************************************************* Bottom */
/* Exemple pour afficher des liens en bas du menu */
function DelMenuContextuelBottom()
{
	StringMenuItemsJSBottom='';
}
function AddMenuContextuelBottom(url,title)
{
	StringMenuItemsJSBottom='<hr/>';
	StringMenuItemsJSBottom+=CreatMenuContextuel('window.location=\''+url+'\';',title,title,'puce3.gif');
}
/**********************************************************************************************************************************************************/
/******************************************************* Affichage d'un code HTML dans un DIV */
/******************************************************* A ne pas changer */
/**********************************************************************************************************************************************************/
/* Sert seulement pour l'affichage du menu en JavaScript */
var SDns4=(document.layers)?true:false;         	//NS 4
var SDie4=(document.all)?true:false;         		//IE 4
var SDdom=(document.getElementById)?true:false;   	//NS 6 ou IE 5
function SetDiv(ID,Content)
{
  if (SDdom) { document.getElementById(ID).innerHTML=Content; return; }
  if (SDie4) { document.all[ID].innerHTML=Content; return; }
  if (SDns4) { with (eval('document.'+ID+'.document')) { open(); write(Content); close(); } return; }
}
/**********************************************************************************************************************************************************/
/**********************************************************************************************************************************************************/