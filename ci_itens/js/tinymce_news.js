$(document).ready(function() {
    $('.tynimce').tinymce({
        // Location of TinyMCE script
        script_url : V['base_url']+'libs/tiny_mce356/tiny_mce.js',
        language : "pt", // change language here
        convert_urls : false,// true = relativos , false = path absolut
        spellchecker_languages : "+Português=pt,English=en,Espanhol=es",
        relative_urls : false,
        remove_script_host : false,
        width : "100%",


        // General options
        theme : "advanced",
        plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,spellchecker",

        // Theme options
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,image,link,unlink,anchor,media,cleanup,|,pastetext,pasteword",
        theme_advanced_buttons2 : "formatselect,|,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,forecolor",
        theme_advanced_buttons3 : "undo,redo,|,cite,abbr,acronym,attribs,|,template,pagebreak,|,fullscreen,|,code,|,preview",
        theme_advanced_buttons4 : "tablecontrols,|,sub,sup",
		
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
        theme_advanced_resize_horizontal : false,
        paste_auto_cleanup_on_paste : true,
        paste_block_drop : true, // true = não permite arrastar para conteúdo

        // Example content CSS (should be your site CSS)
        //content_css : V['base_url']+"assets/css/para-cms.css",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : V['base_url']+"ci_itens/tinymce_templates.php",
        external_link_list_url : V['base_url']+"ci_itens/tinymce_newslink_list.php?id="+V['item_id'],
        external_image_list_url : V['base_url']+"ci_itens/tinymce_image_list.php?id="+V['item_id']+"&tb="+V['tb'],
        media_external_list_url : V['base_url']+"ci_itens/tinymce_media_list.php",

        // Replace values for the template plugin
        template_replace_values : {
            username : "Some User",
            staffid : "991234"
        }
    });
});