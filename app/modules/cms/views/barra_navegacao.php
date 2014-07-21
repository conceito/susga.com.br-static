<?php 

$baseurl = base_url();
$pagination = $this->pagination->create_links();

// se for co: inclui no 4º seg - co:0000/tip:0000
$seg4 = (substr($this->uri->segment(4), 0, 2)=='co') ? '/'.$this->uri->segment(4) : '';
$uri3 = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3).$seg4;

if(count($desabilitar) > 0){
	foreach($desabilitar as $id){
		$$id = ' disabled="disabled"';
	}
}
if($aba_avancada_aberta){
	echo '<script type="text/javascript">$(function(){$("#barra-filtros").show();});</script>';	
}
?>
 <!--barra de navegação básica-->
      <div id="barra-navegacao">
      <?php echo (strlen($pagination)) ? 'Páginas: '.$pagination . ' | ': '';?>Total de <?php echo $ttl_rows;?> registros | <!--<select name="porpag" class="porpag" id="porpag">
       <?php //echo $pp;?>
      </select>        
      por página | -->
      <form action="<?php echo cms_url(uri_string());?>" method="post" name="busca" class="form-busca"><input name="q" value="<?php echo $b;?>" type="text" class="input-busca" /><input name="" type="image" src="<?php echo cms_img();?>bot-form-ok.gif" alt="ok" class="ok" />                                                       
      <a href="#" style="margin-left:10px;" class="bot-maisfiltros"><span class="courier">&gt;</span> mais filtros</a>      
      <a href="#" class="bot-exportar" style="margin-left:10px;"><span class="courier">&gt;</span> exportar</a>
      </div>
      <!--barra de navegação básica fim-->
      
      <!--barra de filtros avançados-->
      <div id="barra-filtros">
      <div class="perciana">
      <a href="#" class="fechar-filtros"><img src="<?php echo cms_img();?>bot-fecha-div.gif" width="12" height="11" alt="fechar" /></a>


		<a href="<?php echo cms_url($uri3);?>" style="margin-left:10px;"><span class="courier">[&gt;]</span> limpar filtros</a> &nbsp;&nbsp; | 
      <label>DT1 &nbsp; <input name="dt1" type="text" value="<?php if(isset($dt1))echo $dt1;?>" class="input-data"  /></label>
      <label>DT2 &nbsp; <input name="dt2" type="text" value="<?php if(isset($dt2))echo $dt2;?>" class="input-data"  /></label>
      <?php echo $combogrupos;?>
      
     &nbsp;&nbsp;&nbsp;
      <label><?php echo form_radio('ativo', 1, $ativos);?> ativos</label>
       <label><?php echo form_radio('ativo', '0', $inativos);?> inativos</label>
        <label><?php echo form_radio('ativo', '2', $editando);?> editando</label>
         
       <input name="" type="image" src="<?php echo cms_img();?>bot-form-pesquisar.gif" alt="pesquisar" class="bot-pesquisar" />
       
      </form>
      </div> 
      </div>
      <!--barra de filtros avançados fim-->
      
      <!-- barra de filtros de exportação -->
      <div id="barra-exportacao">
      <div class="perciana">
      		<a href="#" class="fechar-filtros"><img src="<?php echo cms_img();?>bot-fecha-div.gif" width="12" height="11" alt="fechar" /></a>
            
            <form action="<?php echo cms_url("cms/cmsutils/exportacao".$seg4);?>" method="post" name="exporta" class="form-busca " target="_blank">
            
                <label>DT1 &nbsp; <input name="dt1" type="text" value="<?php if(isset($dt1))echo $dt1;?>" class="input-data"  /></label>
                <label>DT2 &nbsp; <input name="dt2" type="text" value="<?php if(isset($dt2))echo $dt2;?>" class="input-data"  /></label>
                <?php echo $combogrupos;?>
                &nbsp;&nbsp;&nbsp;
                <label><?php echo form_radio('ativo', 1, $ativos);?> ativos</label>
                <label><?php echo form_radio('ativo', '0', $inativos);?> inativos</label>
                <label><?php echo form_radio('ativo', '2', $editando);?> editando</label>
         
       <input name="" type="image" src="<?php echo cms_img();?>bot-exportar.gif" alt="pesquisar" class="bot-pesquisar" />
            
            </form>
            <br />
			
            <?php if(strlen($this->cms_libs->linkPlanilha($co))>0):?>
            <fieldset class="fieldset"><legend for="">Planilhas personalizadas</legend>

                <?php
                echo $this->cms_libs->linkPlanilha($co);
                ?>

            </fieldset>
            <?php endif;?>
      </div>
      </div>
      <!-- barra de filtros de exportação fim -->