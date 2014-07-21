<!--barra de botões abaixo do título-->
      <div id="barra-botoes">
      <div class="floater">
      <ul>
        <li class="esq">
          <?php echo $this->options_menu->make(); ?>

        <?php if(isset($item['check'])) echo '<a href="#" class="btn check-all">
		<i class="icon-ok-circle"></i> Marcar tudo</a>';?>
        <?php if(isset($item['check'])) echo '<a href="#" class="btn check-invert">
		<i class="icon-remove-circle"></i> Inverter</a>';?>
        
      <?php if(isset($item['imprimir'])) echo '<a href="'.cms_url($item['imprimir']).'" class="btn btn-info imprimir-lote" target="_blank">
    <i class="icon-print icon-white"></i> Imprimir</a>';?>

    <?php if(isset($item['apagar']) && $act['a']==1) echo '<a href="'.cms_url($item['apagar']).'" class="btn btn-danger apagar-lote">
	  <i class="icon-trash icon-white"></i> Apagar</a></li>';?>
       &nbsp;
      
        <li class="dir">
        <?php if(isset($item['voltar'])) echo '<a href="'.cms_url($item['voltar']).'" class="btn url-back">
		<i class="icon-chevron-left"></i> Voltar</a>';?>
        
        <?php if(isset($item['duplicar'])) echo '<a href="'.cms_url($item['duplicar']).'" class="btn">
		<i class="icon-share"></i> Duplicar</a>';?>

    
        
        <?php if(isset($item['restaurar'])) echo '<a href="'.cms_url($item['restaurar']).'" class="btn btn-danger url-back">
		<i class="icon-repeat icon-white"></i> Restaurar padrões</a>';?>
        
        <?php if(isset($item['limpar']) && $act['c']==1) echo '<a href="'.cms_url($item['limpar']).'" class="btn btn-danger">
		<i class="icon-ban-circle icon-white"></i> Limpar</a>';?>
        
              
        <?php if(isset($item['calendar'])) echo '<a href="'.cms_url($item['calendar']).'" class="btn btn-info">
		<i class="icon-calendar icon-white"></i> Calendário</a>';?>
        
        <?php if(isset($item['pedidos'])) echo '<a href="'.cms_url($item['pedidos']).'" class="btn btn-info">
		<i class="icon-th-list icon-white"></i> Ver pedidos</a>';?>
        
        <?php if(isset($item['agendar']) && $act['c']==1) echo '<a href="'.cms_url($item['agendar']).'" class="btn btn-success bot-agendarnews"><i class="icon-envelope icon-white"></i> Agendar envio</a>';?>
     
      <?php if(isset($item['novoGrupo']) && $act['c']==1) echo '<a href="'.cms_url($item['novoGrupo']).'" class="btn btn-success">
	  <i class="icon-plus icon-white"></i>  Novo Grupo</a>';?>
      
      <?php if(isset($item['novaTag']) && $act['c']==1) echo '<a href="'.cms_url($item['novaTag']).'" class="btn btn-success">
	  <i class="icon-plus icon-white"></i> Nova Tag</a>';?>
      
	 <?php if(isset($item['novo']) && $act['c']==1) echo '<a href="'.cms_url($item['novo']).'" class="btn btn-success">
	 <i class="icon-plus icon-white"></i> Novo</a>';?>
     <?php if(isset($item['salvar']) && $act['c']==1) echo '<a href="'.cms_url($item['salvar']).'" class="btn btn-success btt-salva">
	 <i class="icon-ok icon-white"></i> Salvar</a>';?>
     <?php if(isset($item['continuar']) && $act['c']==1) echo '<a href="'.cms_url($item['continuar']).'" class="btn btn-success btt-salva">
	 <i class="icon-ok icon-white"></i> Salvar e continuar</a></li>';?>
     <?php if(isset($item['importar']) && $act['c']==1) echo '<a href="'.cms_url($item['importar']).'" class="btn btn-success btt-salva">
	 <i class="icon-download icon-white"></i> Fazer importação</a></li>';?>


    
   	  </ul>
      
      
      
      </div>      
      </div>
      <!--barra de botões abaixo do título fim-->