<div id="adminbar">

	<div class="cms">
    	<a href="<?php echo cms_url('cms'); ?>">Administração</a>
    </div>
    
    <div class="modules">
    	<a href="#"><i class="plus">+</i>Novo</a>
        <ul class="unstyled mod-list">
        	<?php if($modulos):
				foreach($modulos as $m):
			?>
        	<li><a href="<?php echo cms_url($m['novo']);?>"><?php echo $m['label'];?></a></li>
            <?php 
				endforeach;
			endif; ?>
            
        </ul>
    </div>
    
    <div class="options">
    	<label><input type="checkbox" name="show_edit_icons" value="1" checked="checked" /> Exibir/esconder ícones de edição</label>
    </div>
    
    <div class="user">
    	<ul class="unstyled">
        	<li><a href="<?php echo cms_url('cms/administracao/adminEdita/co:1/id:'.$user['id']);?>">Meus dados</a></li>
            <li><a href="<?php echo cms_url('cms/login/logOut');?>">Sair</a></li>
        </ul>
    </div>

</div><!-- #adminbar -->