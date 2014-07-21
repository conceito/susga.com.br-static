<?php echo validation_errors(); ?>


<div class="panel-left clearfix">
	
	
	<label for="razao" class="lb-full">Razão Social</label>
	<input name="razao" id="razao" type="text" class="input-longo" value="<?php echo set_value('razao', form_prep($row['razao']));?>" />
	
	<br />
	
	
	<label for="fantasia" class="lb-full">Nome fantasia</label>
	<input name="fantasia" id="fantasia" type="text" class="input-longo" value="<?php echo set_value('fantasia', $row['fantasia']);?>" />
	
	<br />
	
	<label for="cnpj" class="lb-full">CNPJ</label>
	<input name="cnpj" id="cnpj" type="text" class="input-longo" value="<?php echo set_value('cnpj', $row['cnpj']);?>" />
	
	<br />	
	
	<div class="subpanel-50-50">
	<label for="insc_estadual" class="lb-full">Insc. Estadual</label>
	<input name="insc_estadual" id="insc_estadual" type="text" class="input-curto" value="<?php echo set_value('insc_estadual', $row['insc_estadual']);?>" />
	</div><!-- .subpanel-50-50 -->
	
	<div class="subpanel-50-50">
	<label for="insc_municipal" class="lb-full">Insc. Municipal</label>
	<input name="insc_municipal" id="insc_municipal" type="text" class="input-curto" value="<?php echo set_value('insc_municipal', $row['insc_municipal']);?>" />
	</div><!-- .subpanel-50-50 -->	
	
	
	
	
	
	
</div><!-- .panel-left -->


<div class="panel-right clearfix">
	
	
	<label for="profissao" class="lb-full">Profissão</label>
	<input name="profissao" id="profissao" type="text" class="input-longo" value="<?php echo set_value('profissao', $row['profissao']);?>" />
	
	<br />
	
	<label for="atividade" class="lb-full">Atividade</label>
	<input name="atividade" id="atividade" type="text" class="input-longo" value="<?php echo set_value('atividade', $row['atividade']);?>" />
	
	<br />
	
	<label for="cargo" class="lb-full">Cargo</label>
	<input name="cargo" id="cargo" type="text" class="input-longo" value="<?php echo set_value('cargo', $row['cargo']);?>" />
	
	<br />
	

	
	
	
	
</div><!-- .panel-right -->










       


