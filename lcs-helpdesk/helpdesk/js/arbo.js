/*
AUTEUR MrT
CRDP Basse Normandie
Septembre 2008
 */
	
var repImages = '/helpdesk/images/';
imagePlus = 'dhtmlgoodies_plus.gif';
imageMinus = 'dhtmlgoodies_minus.gif';
imageRien = 'dhtmlgoodies_rien.gif';

choix_niveau = '?';
	
function HideAll() {
    var liste = document.getElementsByClassName('img_tree');
    for (var i = 0; i < liste.length; i++) {
        if ($('folder'+parseInt(i)))
            HideShow($('folder'+parseInt(i)));
    }
}
	
function HideShow(d) {
    var plus = new RegExp(imagePlus, "i");
    var minus = new RegExp(imageMinus, "i");
		
    if (minus.exec($(d).src) != null) {
        var l = $(d).parent.childNodes;
	if (l.length <= 4)
        	$(d).src = repImages+imageMinus;
	else
        	$(d).src = repImages+imagePlus;

	for (var i=4; i<l.length; i++) {
            if (l[i].innerHTML)
                l[i].style.display = 'none';
        }
        return;
    }
		
    if (plus.exec($(d).src) != null) {
        $(d).src = repImages+imageMinus;
        var l = $(d).parent.childNodes;
        for (var i=4; i<l.length; i++) {
            if (l[i].innerHTML)
                l[i].style.display = 'block';
        }
        return;
    }
		
}

function closeTree(container)
{
    var listes = $(container).getElementsByTagName('ul');
    while(listes.length > 0)
    {
        listes[0].remove();
    }

    $(container).removeClassName('tree_open');
}

function clickExpand(container, url)
{
    if($(container).hasClassName('tree_open'))
    {
        closeTree(container);
    }
    else
    {
        new Ajax.Request(url, {
            asynchronous:true,
            evalScripts:false,
            onComplete:function(request, json){
                link_tree(container, request)
            }
        });
    }
}

function link_tree(container, ajax) {
    if($(container).hasClassName('tree_open'))
    {
        closeTree(container);
    }
    
    $(container).addClassName('tree_open');
    json = ajax.responseJSON;
    for (var i = 0; i < json.length; i++)
    {
        var it = json[i];

        var elem = '<ul id="menu_'+it.id+'" class="folder">';
        if(it.has_subLink == 0)
            elem += '<img src="'+repImages+imageMinus+'" />';
        else
            elem += '<img src="'+repImages+imagePlus+'" onclick="clickExpand(\'menu_'+it.id+'\',\''+it.url_Sub+'\'); return false;" />';

        elem += '<a href="'+it.url+'">'+it.title+'</a>';
        elem += '</ul>';

        var liste = document.getElementById(container);
        liste.innerHTML = liste.innerHTML + elem;
    }
}

			function init_tree(receiver) {
                		var elem;
                                var liste = document.getElementsByClassName('folder');
                                for (var i = 0; i < liste.length; i++) {
						elem ='';
						if (liste[i].id != 'root') {
							var cat = liste[i].innerHTML;
                                        		elem += '<img class ="img_tree" id=folder'+parseInt(i)+' src="'+repImages+imageMinus+'" />';
                                        		elem += '<img class = "img_tree" src="'+repImages+'dhtmlgoodies_'+liste[i].className+'.gif" />';
                                        		elem +='&nbsp;';
							liste[i].innerHTML = elem + cat;
						
							if ($('folder'+parseInt(i))) {
                                                		var dossier = $('folder'+parseInt(i));
                                                		dossier.parent = liste[i];
                                                		dossier.onclick = function() {              
                                                        		HideShow(this)
                                                		}
							}
						}
                                }//for

                                var liste2 = document.getElementsByClassName('cat_tree');
                                	for (var i = 0; i < liste2.length; i++) {

						liste2[i].onclick = function() {
							//alert(this.innerHTML);
							if (receiver) {
								receiver.innerHTML = this.innerHTML;
								$('category_id').value = this.id;
								Ext.getCmp('winCategories').close();
							}
							return false;
						}
					}


			}//function
