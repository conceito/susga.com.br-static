

<div class="dragbox painel-mensagens <?php echo $vars['status'];?>" id="<?php echo $vars['id'];?>" >  
        <h2><img src="<?php echo cms_img();?>h2-mensagens.gif" width="22" height="22" alt=" " />O que deseja fazer? </h2>  
        <div class="dragbox-content" <?php echo ($vars['status']=='hidden')?'style="display:none;"':'';?>>
        
            
        <ul class="painel-icones">        
            
            <li><a href="<?php echo cms_url('cms/administracao/adminNovo');?>">
            <img src="<?php echo cms_img();?>ico-adduser.gif" width="32" height="32" alt="novo admin"><br/>Criar Admin</a></li>
            <li><a href="<?php echo cms_url('cms/administracao/admins');?>">
            <img src="<?php echo cms_img();?>ico-editaradmin.gif" width="32" height="32" alt="editar"><br/>Editar Admins</a></li>
            <li><a href="<?php echo cms_url('cms/paginas/novo/co:7');?>">
            <img src="<?php echo cms_img();?>ico-addnews.gif" width="32" height="32" alt="nova noticia"><br/>Nova Notícia</a></li>
            <li><a href="<?php echo cms_url('cms/paginas/index/co:7');?>">
            <img src="<?php echo cms_img();?>ico-editnews.gif" width="34" height="32" alt="editar"><br/>Editar Notícias</a></li>
            <li><a href="<?php echo cms_url('cms/pastas/novo/co:1');?>">
            <img src="<?php echo cms_img();?>ico-newgalery.gif" width="35" height="32" alt="nova"><br/>Nova Galeria</a></li>
            <li><a href="<?php echo cms_url('cms/pastas/index/co:1');?>">
            <img src="<?php echo cms_img();?>ico-editgalery.gif" width="37" height="34" alt="aditar"><br/>Editar Galerias</a></li>
            <li><a href="<?php echo cms_url('cms/enquete/novo');?>">
            <img src="<?php echo cms_img();?>ico-addenquete.gif" width="35" height="32" alt="criar"><br/>Criar Enquete</a></li>
            <li><a href="<?php echo cms_url('cms/enquete/index');?>">
            <img src="<?php echo cms_img();?>ico-editenquete.gif" width="36" height="34" alt="editar"><br/>Editar Enquetes</a></li>
        </ul>
        <div class="clear"></div>
        
         </div>
     </div>