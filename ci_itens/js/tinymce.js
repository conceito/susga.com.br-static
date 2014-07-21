$(document).ready(function() {


    $('.basic-editor').tinymce({
        // Location of TinyMCE script
        script_url: V['base_url']+'libs/tiny_mce356/tiny_mce.js',
        language: "pt", // change language here
        convert_urls: false,// true = relativos , false = path absolut
        width: "100%",
//        entity_encoding : "raw",

        // General options
//        theme : "advanced",
        plugins: "visualblocks,safari,pagebreak,paste,directionality,noneditable,nonbreaking,xhtmlxtras",

        /* removed:
         * pagebreak, forecolor, outdent,indent, pasteword
         * */
        theme_advanced_buttons1: "bold,italic,underline,|,justifyleft,justifycenter,justifyright,code",

        cleanup: true,
        init_instance_callback: function (inst) {
//            console.log($(tinyMCE.get(inst.editorId).getElement()));
            //for (editorId in tinyMCE.editors) {
            var orig_element = $(tinyMCE.get(inst.editorId).getElement());
            var editorHeight = orig_element.data('editor-height');

//            console.log('editorHeight', editorHeight);

//                console.log('editorId', editorId);
//                if (id === 'field_resumo') {
//                    console.log(numChars);
//                }
            //}
        },
        handle_event_callback: function (e, inst) {

            var orig_element = $(tinyMCE.get(inst.editorId).getElement());
            var name = orig_element.attr('name');
            var id = orig_element.attr('id');
            var numChars = orig_element.data('editor-limit');

            var body = tinyMCE.get(inst.editorId).getBody(), text = tinymce.trim(body.innerText || body.textContent);
            var left = numChars - text.length;

//            console.log('left', left);

            if (left < 0) {
                $('#'+id+'_countBox').addClass('overquota');
            } else {
                $('#'+id+'_countBox').removeClass('overquota');
            }

            $('#'+id+'_countBox').find('.countBox').text(left);
//           console.log(value);
        },
        theme_advanced_toolbar_location: "top",
        theme_advanced_toolbar_align: "left",
        theme_advanced_statusbar_location: "none",
        theme_advanced_resizing: false,
        theme_advanced_resize_horizontal: false,
//        theme_advanced_blockformats: "p,h1,h2,h3,h4",
        paste_auto_cleanup_on_paste: true,
        paste_block_drop: true, // true = não permite arrastar para conteúdo

        // Example content CSS (should be your site CSS)
        content_css : V['base_url']+"assets/cms/para-cms.css",
        class_filter: function (cls, rule) {
            // Skip classes that are inside id like #tinymce
            if (/^#.*/.test(rule))
                return false;

            // Pass though the rest
            return cls;
        }
    });
	
    // só exibe a 4ª barra de ferramentas para admin GOD
    var theme_adv_3 = (V['admin_tipo'] == 0) ? "tablecontrols,|,code,removeformat,|,preview,|,sub,sup,|,visualblocks" : '';
    var theme_adv_4 = (V['admin_tipo'] == 0) ? "tablecontrols," : '';
	
    $('.textarea-longo').tinymce({
        // Location of TinyMCE script
        script_url : V['base_url']+'libs/tiny_mce356/tiny_mce.js',
        language : "pt", // change language here
        convert_urls : false,// true = relativos , false = path absolut
        spellchecker_languages : "+Português=pt,English=en,Espanhol=es",
        width : "100%",


        // General options
        theme : "advanced",
        plugins : "autosave,visualblocks,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,nonbreaking,xhtmlxtras,template,spellchecker",

        // Theme options
//        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,image,link,unlink,anchor,media,cleanup,|,pastetext,pasteword,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,image,link,unlink,anchor,media,cleanup,|,pastetext,pasteword,|,styleselect,formatselect,|,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,forecolor,|,undo,redo,|,cite,abbr,acronym,attribs,|,template,pagebreak,|,fullscreen,|,code,|,preview",
        
        /* removed:
         * pagebreak, forecolor, outdent,indent, pasteword
         * */
        
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,link,unlink,anchor,|,image,media,|,cleanup,pastetext,fullscreen",
        theme_advanced_buttons2 : "styleselect,formatselect,|,replace,|,bullist,numlist,blockquote,|,template,|,spellchecker",
        theme_advanced_buttons3 : theme_adv_3,
//        theme_advanced_buttons3 : "undo,redo,|,cite,abbr,acronym,attribs,|,code,removeformat,|,preview,|,sub,sup,|,visualblocks",
//        theme_advanced_buttons4 : theme_adv_4,
			
        //theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_location : "external",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
        theme_advanced_resize_horizontal : true,
        theme_advanced_blockformats : "p,h2,h3,h4,h5,h6,address,pre",
        paste_auto_cleanup_on_paste : true,
        paste_block_drop : true, // true = não permite arrastar para conteúdo

        // Example content CSS (should be your site CSS)
        content_css : V['base_url']+"assets/cms/para-cms.css",
        class_filter : function(cls, rule) {
            // Skip classes that are inside id like #tinymce 
            if (/^#.*/.test(rule))
                return false;

            // Pass though the rest
            return cls;
        },
        table_styles : "Estilo básico=table;Arredondada=table table-bordered;Listrada=table table-striped;Condensada=table table-condensed",

        // Drop lists for link/image/media/template dialogs
        template_external_list_url : V['base_url']+"ci_itens/tinymce_templates.php",
        external_link_list_url : V['base_url']+"ci_itens/tinymce_link_list.php",
        external_image_list_url : V['base_url']+"ci_itens/tinymce_image_list.php?id="+V['item_id']+"&tb="+V['tb'],
        media_external_list_url : V['base_url']+"ci_itens/tinymce_media_list.php",

        // Replace values for the template plugin
        template_replace_values : {
            username : "Some User",
            staffid : "991234"
        }
    });

    // garante que area de texto ficará 100%
    setTimeout(function(){
      $('.mceEditor').css('width','100%').css('minHeight','240px');
      $('.mceLayout').css('width','100%').css('minHeight','240px');
      $('.mceIframeContainer').css('width','100%').css('minHeight','240px');
      $('#txt_ifr').css('width','100%').css('minHeight','240px');
      $('iframe[id^=txtmulti]').css('width','100%').css('minHeight','240px');
    },500)
    

});