<div class="control-group box box-revision">

    <label for="status" class="lb-full"><i class="icon-chevron-down"></i> Versões anteriores (<?php echo $total;?>)</label>
    
    <div class="box-content">
    	
        <?php if($revs): ?>
        
        <ul class="unstyled">
        	
            <?php foreach($revs as $key => $row):?>
            
            <li><a href="<?php echo cms_url($uri.'id:'.$row['id']);?>">Revisão #<?php echo $key+1;?> (<?php echo datetime_br($row['atualizado']);?>)</a></li>
            
            <?php endforeach;?>
            
        </ul> 
        <div class="help-block">Clique nas revisões para ver as versões anteriores deste conteúdo.</div>              
        
        <?php else:?>
        <p>Não existem versões salvas deste conteúdo.</p>
        <?php endif;?>
        
    </div>

</div><!-- .control-group -->   