/*
 * Script from NETTUTS.com [by James Padolsey]
 * @requires jQuery($), jQuery UI & sortable/draggable UI modules
 */

var iNettuts = {
    
    jQuery : $,
    
    settings : {
        columns : '.column',
        widgetSelector: '.widget',
        handleSelector: '.widget-head',
        contentSelector: '.widget-content',
        widgetDefault : {
            movable: true,
            removable: true,
            collapsible: true,
            editable: true,
            colorClasses : ['color-yellow', 'color-red', 'color-blue', 'color-white', 'color-orange', 'color-green']
        },
        widgetIndividual : {
            intro : {
                movable: false,
                removable: false,
                collapsible: false,
                editable: false
            }
        }
    },

    init : function () {
        this.attachStylesheet('inettuts.js.css');
        this.addWidgetControls();
        this.makeSortable();
    },
    
    getWidgetSettings : function (id) {
        var $ = this.jQuery,
            settings = this.settings;
        return (id&&settings.widgetIndividual[id]) ? $.extend({},settings.widgetDefault,settings.widgetIndividual[id]) : settings.widgetDefault;
    },
    
    addWidgetControls : function () {
        var iNettuts = this,
            $ = this.jQuery,
            settings = this.settings;
            
        $(settings.widgetSelector, $(settings.columns)).each(function () {
            var thisWidgetSettings = iNettuts.getWidgetSettings(this.id);
            if (thisWidgetSettings.removable) {
                $('<a href="#" class="remove">SUPPRIMER</a>').mousedown(function (e) {
                    e.stopPropagation();    
                }).click(function () {
                    if(confirm('Vous allez supprimer ce widget, ok ?')) {
                        $(this).parents(settings.widgetSelector).animate({
                            opacity: 0    
                        },function () {
                            $(this).wrap('<div/>').parent().slideUp(function () {
                                $(this).remove();
                            });
                        });
                    }
                    return false;
                }).appendTo($(settings.handleSelector, this));
            }
            
            if (thisWidgetSettings.editable) {
                $('<a href="#" class="edit">EDITER</a>').mousedown(function (e) {
                    e.stopPropagation();    
                }).toggle(function () {
                    $(this).css({backgroundPosition: '-66px 0', width: '55px'})
                        .parents(settings.widgetSelector)
                            .find('.edit-box').show().find('input').focus();
                    return false;
                },function () {
                    $(this).css({backgroundPosition: '', width: ''})
                        .parents(settings.widgetSelector)
                            .find('.edit-box').hide();
                    return false;
                }).appendTo($(settings.handleSelector,this));
//                alert($(this).attr('id')+'\n'+parseInt($(this).parent('ul').index()+1)+'_'+parseInt($(this).not('#intro').index()+1));
                    var editContent = '<ul>';
                    var idForm = parseInt($(this).parent('ul').index()+1)+'_'+parseInt($(this).not('#intro').index()+1);
                    editContent += '<li class="item"><label for="titre_'+idForm+'">Modifier le titre ?</label><input name="titre_'+idForm+'" class="e_titre" id="titre_'+idForm+'" value="' + $('h3',this).text() + '"/></li>';
                    editContent += '<li class="item"><label for="url_'+idForm+'">Modifier l\'url ?</label><input name="url_'+idForm+'" class="e_url" id="url_'+idForm+'" value=""/>';
                    editContent += '<span class="btn_url_save"> OK </span></li>';
                        var colorList = '<li class="item"><label>Couleurs disponibles :</label><ul class="colors">';
                        $(thisWidgetSettings.colorClasses).each(function () {
                            colorList += '<li class="' + this + '"/>';
                        });
                        editContent += colorList + '</ul>';
                    editContent += '</ul>';
                $('<div class="edit-box" style="display:none;"/>')
                    .append(editContent)
                    .insertAfter($(settings.handleSelector,this));
            }
            
            if (thisWidgetSettings.collapsible) {
                $('<a href="#" class="collapse">COLLAPSE</a>').mousedown(function (e) {
                    e.stopPropagation();    
                }).toggle(function () {
                    $(this).css({backgroundPosition: '-38px 0'})
                        .parents(settings.widgetSelector)
                            .find(settings.contentSelector).hide();
                    return false;
                },function () {
                    $(this).css({backgroundPosition: ''})
                        .parents(settings.widgetSelector)
                            .find(settings.contentSelector).show();
                    return false;
                }).prependTo($(settings.handleSelector,this));
            }
        });
        
        $('.edit-box').each(function () {
            var t_edit = this;
            $('input.e_titre',this).keyup(function () {
                $(this).parents(settings.widgetSelector).find('h3').text( $(this).val().length>30 ? $(this).val().substr(0,30)+'...' : $(this).val() );
            });
            $('input.e_url',this).keyup(function () {
                $(this).parent('li').find('span.btn_url_save').show();
            });
            $('ul.colors li',this).click(function () {
                
                var colorStylePattern = /\bcolor-[\w]{1,}\b/,
                    thisWidgetColorClass = $(this).parents(settings.widgetSelector).attr('class').match(colorStylePattern)
                if (thisWidgetColorClass) {
                    $(this).parents(settings.widgetSelector)
                        .removeClass(thisWidgetColorClass[0])
                        .addClass($(this).attr('class').match(colorStylePattern)[0]);
                }
                return false;
            });
            $('span.btn_url_save',this).click(function () {
            var t_url =$(t_edit).find('input.e_url').val();
            var t_titre =$(t_edit).find('input.e_titre');
			$.ajax({
				type: "POST",
				url: "desktop/action/inettuts_rss.php",
				cache: false,
				data: ({file: t_url,
						user: 'admin',
						nb_items: 5}),
				dataType: "text",
				success: function(msg){
//					alert(msg);
				},
				error: function(){
				},
				complete : function(data, status) {
					var resp = data.responseText;
//					alert(resp);
					var rens= resp.split('|,|');
					t_titre.attr('value', rens[0]);
	            	$(t_edit).parents(settings.widgetSelector).find('h3').html(rens[0].length>30 ?  rens[0].substr(0,30)+'...' :  rens[0] );
					$(t_edit).parents('li.widget').find('div.widget-content').html(rens[4]);
					$('a.link_out, span.rssdesc a').attr('target', '_blank').css('color','orange');
					$(t_edit).hide().parents('li.widget').find('a.edit').css({backgroundPosition: '', width: ''});
				}
			});	


            });
       });
        
    },
    
    attachStylesheet : function (href) {
        var $ = this.jQuery;
        return $('<link href="' + href + '" rel="stylesheet" type="text/css" />').appendTo('head');
    }/*,
    
    makeSortable : function () {
        var iNettuts = this,
            $ = this.jQuery,
            settings = this.settings,
            $sortableItems = (function () {
                var notSortable = '';
                $(settings.widgetSelector,$(settings.columns)).each(function (i) {
                    if (!iNettuts.getWidgetSettings(this.id).movable) {
                        if(!this.id) {
                            this.id = 'widget-no-id-' + i;
                        }
                        notSortable += '#' + this.id + ',';
                    }
                });
                return $('> li:not(' + notSortable + ')', settings.columns);
            })();
        
        $sortableItems.find(settings.handleSelector).css({
            cursor: 'move'
        }).mousedown(function (e) {
            $sortableItems.css({width:''});
            $(this).parent().css({
                width: $(this).parent().width() + 'px'
            });
        }).mouseup(function () {
            if(!$(this).parent().hasClass('dragging')) {
                $(this).parent().css({width:''});
            } else {
                $(settings.columns).sortable('disable');
            }
        });

        $(settings.columns).sortable({
            items: $sortableItems,
            connectWith: $(settings.columns),
            handle: settings.handleSelector,
            placeholder: 'widget-placeholder',
            forcePlaceholderSize: true,
            revert: 300,
            delay: 100,
            opacity: 0.8,
            containment: '#inettuts',
            start: function (e,ui) {
                $(ui.helper).addClass('dragging');
            },
            stop: function (e,ui) {
                $(ui.item).css({width:''}).removeClass('dragging');
                $(settings.columns).sortable('enable');
            }
        });
    }
*/  
};

iNettuts.init();