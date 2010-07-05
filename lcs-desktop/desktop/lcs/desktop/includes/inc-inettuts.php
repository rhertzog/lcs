    <div id="columns">
                	<?php
						include("/var/www/lcs/desktop/action/rsslib.php");
					?>
        
        <ul id="column1" class="column">
            <li class="no_widget color-black" id="intro">
                <div class="widget-head">
                    <h3><strong><i>i</i></strong>Lcs</h3>
                </div>
                <div class="widget-content rsslib">
                    <p>iLcs vous permet de suivre vos flux rss sur une seule page</p>
<!--                    <p><span id="btn_add_widget">Ajouter un widget</span></p> -->
                    <p>
                    <form>
                    <fieldset>
                    <ul>
                    <li>
                    <label for="url_load_flux">Ajouter un flux rss</label>
                    <input type="url" id="url_load_flux" value="http://www.scriptol.com/rss.xml" />
                    <span class="button" id="btn_load_flux">OK</span>
                    </li>
                    </ul>
                    </fieldset>
                    </form>
                    </p>
                    <p><span id="btn_save_ilcs">Enregistrer cette configuration</span></p>
                </div>
            </li>
           <li class="widget color-red">  
                <div class="widget-head">
                    <h3>Widget 2 title</h3>
                </div>
                <div class="widget-content rsslib">
                	<?php
						echo RSS_Display("http://www.xul.fr/rss.xml", 3,true,true);
					?>
                </div>

            </li>
            <li class="widget color-red">  
                <div class="widget-head">
                    <h3>Widget 3 title</h3>
                </div>
                <div class="widget-content rsslib">
                	<?php
//						echo RSS_Display("http://www.ac-caen.fr", 3);
					?>
                </div>

            </li>
        </ul>

        <ul id="column2" class="column">
            <li class="widget color-blue">  
                <div class="widget-head">
                    <h3>Education.gouv - Toute l'actualit&eacute;.</h3>
                </div>
                <div class="widget-content rsslib">
                	<?php
						echo RSS_Display("http://www.education.gouv.fr/rid4/toute-l-actualite.rss?xtdate=20100626",3,1,1);
					?>
                </div>
            </li>
            <li class="widget color-yellow">  
                <div class="widget-head">
                    <h3>Widget title</h3>
                </div>
                <div class="widget-content rsslib">
                    <p>
                	<?php
						echo RSS_Display("http://lcs.pmcurie.lyc50.ac-caen.fr/spip/?page=backend",3,1,1);
					?>
                    </p>
                </div>
            </li>
        </ul>
        
        <ul id="column3" class="column">
            <li class="widget color-orange">  
                <div class="widget-head">
                    <h3>Widget title</h3>
                </div>
                <div class="widget-content rsslib">
                    <p>
                    <?php
						echo RSS_Display("http://tispip.etab.ac-caen.fr/?page=backend",3,1,1);
                    ?>
                    </p>
                </div>
            </li>
            <li class="widget color-white">  
                <div class="widget-head">
                    <h3>Widget title</h3>
                </div>
                <div class="widget-content rsslib">
                    <p>
                	<?php
						echo RSS_Display("http://pgm.discip.ac-caen.fr/?page=backend",3,1,1);
					?>
                    </p>
                </div>
            </li>
            
        </ul>
<script>
$('a.link_out, span.rssdesc a').attr('target', '_blank');

$('#btn_load_flux').click(function(){
//	alert('url= '+$('#url_load_flux').val());
 	var rss_url=$('#url_load_flux').val();
	$.ajax({
	async : false,
		type: "POST",
		url: "desktop/includes/inc-new_widget.php",
		cache: false,
		data: ({url: rss_url,
			size : 5}),
		dataType: "text",
		success: function(msg){
			$('#intro').after(msg);
			alert($('li.new-widget').length+'_'+$('li.new-widget').find('.widget-content a.orange').text());
			var title=$('li.new-widget').find('.widget-content a.orange').text();
			$('li.new-widget').find('div.widget-head h3').html(title.length>20 ?  title.substr(0,30)+'...' :  title);
			$('li.new-widget').find('div.widget-head div.edit-box ul li input.e_url').val(rss_url);
//			alert(msg);
		},
		error: function(){
		},
		complete : function(data, status) {
			$('.widget-head').each(function(){$(this).find('a').remove()});$('.edit-box').remove();iNettuts.getWidgetSettings();	
			iNettuts.init();
			tittle='';
		}
	});
});
$('#btn_save_ilcs').click(function(){
//	alert('toto');
		var s_xml="" ;
		s_xml+='<response>\n';
	$('ul.column').each(function(){
		r_col = parseInt($(this).index()+1);
		s_xml+='\t<column>'+r_col+'\n';
//		alert(r_col);
		$(this).find('li.widget').not('.no_widget').each(function(){
			r_item = r_col+'_'+parseInt($(this).index()+1);
			s_xml += '\t\t<widget>'+r_item+'\n';
			s_title = $(this).find('input.e_titre').val();
			s_url = $(this).find('input.e_url').val();
//			alert('widget_'+r_item+' titre:'+s_title+' url:'+s_url);
			s_xml += '\t\t\t<titre>'+s_title+'</titre>'+'\n';
			s_xml += '\t\t\t<url>'+s_url+'</url>'+'\n';
			s_xml += '\t\t</widget>'+'\n';
		});
		s_xml +='\t</column>'+'\n';
	});
		s_xml+='</response>'+'\n';
		alert(s_xml);
			$.ajax({
				type: "POST",
				url: "desktop/action/save_xml.php",
				cache: false,
				data: ({file: 'iLcs_admin',
						user: 'admin',
						groups : 'n',
						data : s_xml}),
				dataType: "text",
				success: function(msg){
					alert(msg);
				},
				error: function(){
				},
				complete : function(data, status) {
				}
			});	
});
	$(function() {
		$(".column").sortable({
			connectWith: '.column',
			items: 'li.widget',
            handle: '.widget-head',
            placeholder: 'widget-placeholder',
            forcePlaceholderSize: true,
            start: function (e,ui) {
                $(ui.helper).addClass('dragging');
            },
            stop: function (e,ui) {
                $(ui.item).css({width:''}).removeClass('dragging');
//                $(settings.columns).sortable('enable');
				$('li.widget').each(function(){
					var idItem=parseInt($(this).parent('ul').index()+1)+'_'+parseInt($(this).index()+1);
					$(this).attr('id', idItem);
				});
            }
		});

		$(".widget").addClass("ui-widget ui-widget-content ui-helper-clearfix")
			.find(".widget-head")
				.addClass("ui-widget-head ui-corner-all")
//				.prepend('<span class="ui-icon ui-icon-minusthick"></span>')
				.end()
			.find(".widget-content");

		$(".widget-head .ui-icon").click(function() {
			$(this).toggleClass("ui-icon-minusthick").toggleClass("ui-icon-plusthick");
			$(this).parents(".xidegt:first").find(".widget-content").toggle();
		});

		$(".column").disableSelection();
	});
$('#showWidgetsContent').toggle(function(){
	$(this).text('Tout montrer');
	$('.widget .widget-content').each(function(){$(this).hide();});
},
function(){
	$('.widget .widget-content').each(function(){$(this).show();});
	$(this).text('Tout cacher');
});
		function load_widget( suser ) { 
			
			$.ajax({
				type: "GET",
				url: "desktop/xml/admin/lcs_buro_" + $("#login").val() +".xml", // on cherche dans le bon rep
				cache: false, 
				dataType: "xml", 
				complete : function(data, status) {
					var resp = data.responseXML;
					// Traitement du xml
					$(resp).find('response').each(function(){
						$(resp).find('column').each(function(){
							var e_col = $(this).text();			
							$(this).find('widget').each(function(){
								e_url = $(this).find('url').text() ;
								
								$('#column'+e_col).append($.load('desktop/includes/inc-new_widget.php'));
							});
						});
					});
		
				}
			});
					
		}
		

</script>
       
</div>