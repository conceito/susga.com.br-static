<script type="text/javascript">
	var widget_img_choose = {
		"meta_key": "<?php echo $metas['meta_key']?>"
	};
</script>
<script type="text/javascript" src="<?php echo base_url() . app_folder();?>ci_itens/js/widget-img-choose.js"></script>
<style>
	#img-choose .selected{
		border: rgb(255, 214, 0) solid 2px;
		box-shadow: 0 0 10px rgba(0,0,0,0.2);
		display: inline-block;
	}
	.img-scroll-pane{
		overflow: auto;
		border: rgb(243, 243, 243) solid 1px;
		box-shadow: 0 0 10px rgba(0,0,0,0.1) inset;
		height: 215px;
		text-align: center;
		padding: 10px;		
	}
</style>
<label for="status" class="lb-full">Escolha a imagem</label>
    <div class="form-opcoes img-scroll-pane">        
        <ul id="img-choose" class="unstyled">
        	<?php foreach($images as $img): ?>
			<li>
				<a href="#" class="<?php echo ($selected == $img['full_path']) ? 'selected' : '' ?>" data-value="<?php echo $img['full_path'] ?>"> <img src="<?php echo $img['full_path'] ?>" ></a>
			</li>
        	<?php endforeach; ?>
        </ul>
        <input type="hidden" id="set_<?php echo $metas['meta_key']?>" name="<?php echo $metas['meta_key']?>" value="<?php echo $selected?>">
    </div>
<div class="help-block">Última atualização em </div>