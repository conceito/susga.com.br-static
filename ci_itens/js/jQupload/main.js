/*
 * jQuery File Upload Plugin JS Example 6.5
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */

$(function () {
    'use strict';

    // remove botões antes de iniciar
    $('button.delete, input.toggle').hide();

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        autoUpload: true,
        prependFiles: true
    }).bind('fileuploadstart', function(){ // Evento ao iniciar os uploads
        $('.dropable').slideUp();
        $('button.delete, input.toggle').show();
        $('.closeModalandRefresh').css({display: 'block'});
    }).bind('fileuploadstop', function(){ // Eventos ao terminar uploads

    });;

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    if (window.location.hostname === 'localhost') {
        //Load files
        // Upload server status check for browsers with CORS support:
        if ($.ajaxSettings.xhr().withCredentials !== undefined) {
            $.ajax({
                url: V['site_url']+'cms/upload/processajQupload/co:0/id:4/pasta:4/onde:pasta',
                dataType: 'json',

                success : function(data) {

                    var fu = $('#fileupload').data('fileupload'),
                    template;
                    fu._adjustMaxNumberOfFiles(-data.length);
                    template = fu._renderDownload(data)
                    .appendTo($('#fileupload .files'));

                    // Force reflow:
                    fu._reflow = fu._transition && template.length &&
                    template[0].offsetWidth;
                    template.addClass('in');
                    $('#loading').remove();
                }


            }).fail(function () {
//                $('<span class="alert alert-error"/>')
//                .text('Upload server currently unavailable - ' +
//                    new Date())
//                .appendTo('#fileupload');
                console.log('erro localhost server');
            });
        }
    } else {
        // Load existing files:
        $('#fileupload').each(function () {
            var that = this;
            $.getJSON(this.action, function (result) {
                if (result && result.length) {
                    $(that).fileupload('option', 'done')
                        .call(that, null, {result: result});
                }
            });
        });
    }

    // Open download dialogs via iframes,
    // to prevent aborting current uploads:
    $('#fileupload .files a:not([target^=_blank])').live('click', function (e) {
        e.preventDefault();
        $('<iframe style="display:none;"></iframe>')
        .prop('src', this.href)
        .appendTo('body');
    });

  
   

});
