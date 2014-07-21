
<div class="voce-esta-aqui">Pasta de destino: <strong><?php echo $pasta['titulo'];?></strong>.  Arquivos aceitos: <strong><?php echo $ext;?></strong> com no máximo <strong><?php echo $pesomax;?></strong></div>
<br />



    <!-- The file upload form used as target for the file upload widget -->
    <form id="fileupload" action="<?php echo cms_url('cms/upload/processajQupload/co:'.$co.'/id:'.$id.'/pasta:'.$pasta['id'].'/onde:'.$onde);?>" method="POST" enctype="multipart/form-data">
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="span3">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>Adicionar...</span>
                    <input type="file" name="userfile" multiple>
                </span>
                <!--<button type="submit" class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Iniciar</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>Cancel upload</span>
                </button>-->
                <button type="button" class="btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span>Apagar</span>
                </button>
                <input type="checkbox" class="toggle">
            </div>
            <div class="span5">
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active fade">
                    <div class="bar" style="width:0%;"></div>
                </div>
            </div>
        </div>
		<a href="#" class="closeModalandRefresh">Terminei meus uploads. Voltar para galeria de imagens.</a>
        <!-- The loading indicator is shown during image processing -->
        <div class="fileupload-loading"></div>
        <br>
		
		<div class="dropable"><strong>Solte os arquivos aqui</strong><br /> para fazer o upload.</div>
        <!-- The table listing the files available for upload/download -->
        
        	<table class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
        <a href="#" class="closeModalandRefresh">Terminei meus uploads. Voltar para galeria de imagens.</a>
    </form>
  
	
    



<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>{%=locale.fileupload.start%}</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>{%=locale.fileupload.cancel%}</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <!--<a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}">--> 
				<img src="{%=file.thumbnail_url%}">
				<!--</a>-->
            {% } %}</td>
            <td class="name">
                <!--<a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">-->
				{%=file.name%}
				<!--</a>-->
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
                <i class="icon-trash icon-white"></i>
                <span>{%=locale.fileupload.destroy%}</span>
            </button>
            <input type="checkbox" name="delete" value="1">
        </td>
    </tr>
{% } %}
</script>


<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS and Bootstrap Image Gallery are not required, but included for the demo -->
<script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/bootstrap.min.js"></script>
<script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/bootstrap-image-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/jquery.fileupload.js"></script>
<!-- The File Upload image processing plugin -->
<script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/jquery.fileupload-ip.js"></script>
<!-- The File Upload user interface plugin -->
<script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/jquery.fileupload-ui.js"></script>
<!-- The localization script -->
<script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/locale.js"></script>
<!-- The main application script -->
<script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/main.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="<?php echo base_url() . app_folder();?>ci_itens/js/jQupload/cors/jquery.xdr-transport.js"></script><![endif]-->