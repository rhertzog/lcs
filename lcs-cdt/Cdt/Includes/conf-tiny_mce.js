// Creates a new plugin class and a custom listbox
tinymce.create('tinymce.plugins.ExamplePlugin', {
    createControl: function(n, cm) {
        switch (n) {
            case 'mylistbox':
                var mlb = cm.createListBox('mylistbox', {
                     title : 'INSERER',
                     onselect : function(v) {
                         if ( v == 'val1' ) image_popup();
                         else if ( v == 'val2' ) joint_popup();				                         
                         else if  (v == 'val3' ) lien_popup();
                         else if ( v== 'val4' ) form_popup();
                     }
                });

                // Add some values to the list box
                mlb.add('une image', 'val1');
                mlb.add('une piece jointe', 'val2');
                mlb.add('un lien', 'val3');
                mlb.add('une expr math', 'val4');

                // Return the new listbox instance
                return mlb;            
        		}

        return null;
    }
});

// Register plugin with a short name
tinymce.PluginManager.add('example', tinymce.plugins.ExamplePlugin);

// Initialize TinyMCE with the new plugin and listbox



tinyMCE.init({
    //  plugins : '-example',- tells TinyMCE to skip the loading of the plugin
    mode : "textareas",
	theme : "advanced",
	width: "560",
	height: "120",
	editor_selector : 'mceAdvanced',
	skin : "o2k7",
	skin_variant : "silver",
	theme_advanced_disable : "hr,visualaid,removeformat,separator, cleanup,help ",
	plugins : "example,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,media,emotions,searchreplace,print,contextmenu,paste,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	theme_advanced_buttons1 : "mylistbox,bold,italic,underline,|,sub,sup,|,charmap,emotions,hr,|,justifyleft,justifycenter,justifyright,justifyfull,fontselect,|,fontsizeselect",
	theme_advanced_buttons2 : "bullist,numlist,|,undo,redo,|,copy,paste,|,search,|,forecolor,backcolor,|,tablecontrols,|,fullscreen",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "center",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	template_external_list_url : "example_template_list.js",
	language : "fr",
	file_browser_callback : "myFileBrowser",
	setup : function(ed) {
        // Add a custom button
        ed.addButton('mybutton', {
            title : 'Ins&#233;rer',
            image : 'jscripts/tiny_mce/plugins/example/img/example.gif',
            onclick : function() {image_popup();}          
        });       
    }
});

tinyMCE.init({
    
    mode : "textareas",
	theme : "advanced",
	editor_selector : 'MYmceAdvanced',
	theme_advanced_disable : "hr,visualaid,removeformat,separator, cleanup,help ",
	theme_advanced_buttons1 : "forecolor,backcolor,bold,italic,underline,bullist,numlist",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "bottom",
	theme_advanced_toolbar_align : "center",
	extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
		language : "fr"
	
});