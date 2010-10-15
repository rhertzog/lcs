
var please_wait=false;function afficher_masquer_images_action(why)
{if(why=='show')
{$('form q').show();}
else if(why=='hide')
{$('form q').hide();}}
function format_liens(element)
{$(element).find("a.lien_ext").attr("target","_blank");$(element).find("a.lien_ext").css({"padding-right":"14px","background":"url(./_img/popup2.gif) no-repeat right"});$(element).find("a.pop_up").css({"padding-right":"18px","background":"url(./_img/popup1.gif) no-repeat right"});$(element).find("a.lien_mail").css({"padding-left":"15px","background":"url(./_img/mail.gif) no-repeat left"});}
function infobulle()
{$('img[title]').tooltip({showURL:false});$('th[title]').tooltip({showURL:false});$('td[title]').tooltip({showURL:false});$('a[title]').tooltip({showURL:false});$('q[title]').tooltip({showURL:false});}
function analyse_mdp(mdp)
{mdp.replace(/^\s+/g,'').replace(/\s+$/g,'');mdp=mdp.substring(0,20);var nb_min=0;var nb_maj=0;var nb_num=0;var nb_spe=0;var longueur=mdp.length;for(i=0;i<longueur;i++)
{var car=mdp.charAt(i);if((/[a-z]/).test(car)){nb_min++;}
else if((/[A-Z]/).test(car)){nb_maj++;}
else if((/[0-9]/).test(car)){nb_num++;}
else{nb_spe++;}}
var coef=Math.min(nb_min,2)+Math.min(nb_maj,2)+Math.min(nb_num,2)+Math.min(nb_spe*2,6);if(longueur>7)
{coef+=Math.floor((longueur-5)/3);}
coef=Math.min(coef,12);var rouge=255-16*Math.max(0,coef-6);var vert=159+16*Math.min(6,coef);var bleu=159;$('#robustesse').css('background-color','rgb('+rouge+','+vert+','+bleu+')').children('span').html(coef);}
function maj_clock(evolution)
{DUREE_RESTANTE=(evolution==-1)?DUREE_RESTANTE-1:DUREE_AUTORISEE;if(DUREE_RESTANTE>5)
{$("#clock").html('<img alt="" src="./_img/clock_fixe.png" /> '+DUREE_RESTANTE+' min');if((evolution==-1)&&(DUREE_RESTANTE%10==0))
{conserver_session_active();}}
else
{setVolume(100);play("bip");$("#clock").html('<img alt="" src="./_img/clock_anim.gif" /> '+DUREE_RESTANTE+' min');if(DUREE_RESTANTE==0)
{$('#deconnecter').click();}}}
function conserver_session_active()
{$.ajax
({type:'GET',url:'ajax.php?page=conserver_session_active',data:'',dataType:"html",error:function(msg,string)
{alert('Avertissement : échec lors de la connexion au serveur !\nLe travail en cours pourrait ne pas pouvoir être sauvegardé...');},success:function(responseHTML)
{if(responseHTML!='ok')
{alert(responseHTML);}}});}
var myListener=new Object();myListener.onInit=function()
{this.position=0;};myListener.onUpdate=function()
{info_playing=this.isPlaying;info_url=this.url;info_volume=this.volume;info_position=this.position;info_duration=this.duration;info_bytes=this.bytesLoaded+"/"+this.bytesTotal+" ("+this.bytesPercent+"%)";var isPlaying=(this.isPlaying=="true");};function getFlashObject()
{return document.getElementById("myFlash");}
function play(file)
{if(myListener.position==0)
{getFlashObject().SetVariable("method:setUrl","./_mp3/"+file+".mp3");}
getFlashObject().SetVariable("method:play","");getFlashObject().SetVariable("enabled","true");}
function pause()
{getFlashObject().SetVariable("method:pause","");}
function stop()
{getFlashObject().SetVariable("method:stop","");}
function setPosition(position)
{getFlashObject().SetVariable("method:setPosition",position);}
function setVolume(volume)
{getFlashObject().SetVariable("method:setVolume",volume);}
function arrondir_coins(element,taille)
{if(document.body.style['BorderRadius']!==undefined){style='border-radius';}
else if(document.body.style['borderRadius']!==undefined){style='border-radius';}
else if(document.body.style['MozBorderRadius']!==undefined){style='-moz-border-radius';}
else if(document.body.style['WebkitBorderRadius']!==undefined){style='-webkit-border-radius';}
else if(document.body.style['KhtmlBorderRadius']!==undefined){style='-khtml-border-radius';}
else if(document.body.style['OBorderRadius']!==undefined){style='-o-border-radius';}
else{style=false;}
if(style!==false)
{$(element).css(style,taille);}}
$(document).ready
(function()
{format_liens('body');infobulle();$("#menu a").each
(function()
{classe=$(this).attr("class");if(classe)
{$(this).css({'background-image':'url(./_img/menu/'+classe+'.png)','background-repeat':'no-repeat','background-position':'1px 1px'});}});var test_over=false;$('#menu li').mouseover(function(){test_over=true;});$('#menu li').mouseout(function(){test_over=false;});function page_transparente()
{$("body").everyTime
('5ds',function()
{if(test_over)
{$('#cadre_bas').fadeTo('normal',0.2);}
else
{$('#cadre_bas').fadeTo('fast',1);}});}
page_transparente();$('#zone_compet li span').siblings('ul').hide('fast');$('#zone_compet li span').live
('click',function()
{$(this).siblings('ul').toggle();});$('#zone_paliers li span').siblings('ul').hide('fast');$('#zone_paliers li span').live
('click',function()
{$(this).siblings('ul').toggle();});$('#zone_socle li span').siblings('ul').hide('fast');$('#zone_socle li span').click
(function()
{$(this).siblings('ul').toggle();});$('#zone_eleve li span').siblings('ul').hide('fast');$('#zone_eleve li span').click
(function()
{$(this).siblings('ul').toggle();});$('#deconnecter').click
(function()
{var profil=$(this).val();window.document.location.href='./index.php?'+profil;});$('a.toggle').click
(function()
{$("div.toggle").toggle("slow");return false;});$('img.toggle').live
('click',function()
{id=$(this).parent().attr('lang');$('#'+id).toggle('fast');src=$(this).attr('src');if(src.indexOf("plus")>0)
{$(this).attr('src',src.replace('plus','moins'));}
else
{$(this).attr('src',src.replace('moins','plus'));}
return false;});$('a.pop_up').live
('click',function()
{adresse=$(this).attr("href");if(window.name!='popup')
{var largeur=Math.max(1000,screen.width-600);var hauteur=screen.height*1;var gauche=0;var haut=0;window.moveTo(gauche,haut);window.resizeTo(largeur,hauteur);}
var largeur=600;var hauteur=screen.height*1;var gauche=screen.width-largeur;var haut=0;w=window.open(adresse,'popup',"toolbar=no,location=no,menubar=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width="+largeur+",height="+hauteur+",top="+haut+",left="+gauche);w.focus();return false;});if(PAGE.substring(0,6)!='public')
{$("body").everyTime
('60s',function()
{maj_clock(-1);});}
$('<div id="calque"></div>').appendTo(document.body).hide();var leave_erreur=false;$('q.date_calendrier').live
('click',function(e)
{champ=$(this).prev().attr("id");date_fr=$(this).prev().attr("value");tab_date=date_fr.split('/');if(tab_date.length==3)
{jour=tab_date[0];mois=tab_date[1];annee=tab_date[2];get_data='j='+jour+'&m='+mois+'&a='+annee;}
else
{get_data='';}
posX=e.pageX-5;posY=e.pageY-5;$("#calque").css('left',posX+'px');$("#calque").css('top',posY+'px');$("#calque").html('<label id="ajax_alerte_calque" for="nada" class="loader">Chargement en cours...</label>').show();$.ajax
({type:'GET',url:'ajax.php?page=date_calendrier',data:get_data,dataType:"html",error:function(msg,string)
{$('#ajax_alerte_calque').removeAttr("class").addClass("alerte").html("Echec de la connexion !");leave_erreur=true;},success:function(responseHTML)
{if(responseHTML.substring(0,4)=='<h5>')
{$('#calque').html(responseHTML);leave_erreur=false;}
else
{$('#ajax_alerte_calque').removeAttr("class").addClass("alerte").html(responseHTML);leave_erreur=true;}}});});$('q.demander_add').live
('click',function(e)
{infos=$(this).attr("lang");tab_infos=infos.split('_');if(tab_infos.length==4)
{matiere_id=tab_infos[1];item_id=tab_infos[2];score=(tab_infos[3]!='')?tab_infos[3]:-1;get_data='matiere_id='+matiere_id+'&item_id='+item_id+'&score='+score;}
else
{return false;}
posX=e.pageX-5;posY=e.pageY-5;$("#calque").css('left',posX+'px');$("#calque").css('top',posY+'px');$("#calque").html('<label id="ajax_alerte_calque" for="nada" class="loader">Chargement en cours...</label>').show();$.ajax
({type:'GET',url:'ajax.php?page=eleve_eval_demande_ajout',data:get_data,dataType:"html",error:function(msg,string)
{$('#ajax_alerte_calque').removeAttr("class").addClass("alerte").html("Echec de la connexion !");leave_erreur=true;},success:function(responseHTML)
{if(responseHTML.substring(0,5)=='<form')
{maj_clock(1);$('#calque').html(responseHTML);leave_erreur=true;}
else
{$('#ajax_alerte_calque').removeAttr("class").addClass("alerte").html(responseHTML);leave_erreur=true;}}});});$("#calque").mouseleave
(function()
{if(leave_erreur)
{$("#calque").html('&nbsp;').hide();}});$("#form_calque #fermer_calque").live
('click',function()
{$("#calque").html('&nbsp;').hide();return false;});$("#form_calque a.actu").live
('click',function()
{retour=$(this).attr("href");retour=retour.replace(/\-/g,"/");$("#"+champ).val(retour).focus();$("#calque").html('&nbsp;').hide();return false;});function reload_calendrier(mois,annee)
{$.ajax
({type:'GET',url:'ajax.php?page=date_calendrier',data:'m='+mois+'&a='+annee,dataType:"html",success:function(responseHTML)
{if(responseHTML.substring(0,4)=='<h5>')
{$('#calque').html(responseHTML);}}});}
$("#form_calque select.actu").live
('change',function()
{m=$("#m option:selected").val();a=$("#a option:selected").val();reload_calendrier(m,a);});$("#form_calque input.actu").live
('click',function()
{tab=$(this).attr("lang").split('_');m=tab[0];a=tab[1];reload_calendrier(m,a);return false;});});