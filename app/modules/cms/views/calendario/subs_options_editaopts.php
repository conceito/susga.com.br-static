<?php echo validation_errors(); ?>
<div ng-controller="OptionsController">
    

<div class="panel-left-lg clearfix">

    <ul ui-sortable="sortableOptions" ng-model="opts" class="sortable-items"> 

    
        <li class="control-group box type-{{ opt.tipo }}"  ng-repeat="opt in opts">
            <a href="" ng-click="remove(opt)" class="close">&times;</a>
            
            <label for="titulo" class="-lb-full">
                <span ng-if="opt.tipo == 'header'">Grupo:</span>
                <span ng-if="opt.tipo == 'option'">Opção:</span> {{ opt.titulo }}</label>
            <input name="titulo" type="text" class="input-longo" ng-model="opt.titulo" />

            <label for="resumo" class="-lb-full">Descrição (opcional)</label>
            <input name="resumo" type="text" class="input-longo" ng-model="opt.resumo" />
    
            <label for="valor" class="-lb-full" ng-if="opt.tipo == 'option'">Valor (opcional)</label>
            <input name="valor" type="text" class="input-curto" ng-model="opt.valor" ng-if="opt.tipo == 'option'" />

            <label style="display: inline-block;margin-left: 10px;">
            <input type="checkbox" ng-model="opt.status" ng-checked="opt.status" value="1" style="float: left; display: inline-block; margin: 1px 5px 0 0;"> Ativo</label>
        
        
        </li><!-- .control-group -->
    </ul>
    
    
</div><!-- .panel-left -->


<div class="panel-right-sm clearfix">

    
    <div class="control-group box">
        <br>
        <div class="btn-group btn-group-vertical">
            <a ng-click="addHeader()" class="btn btn-warning" href="" style="text-align: left;"> <i class="icon-plus icon-white"></i> Adicionar Grupo </a>
            <a ng-click="addOption()" class="btn btn-warning" href="" style="text-align: left;"> <i class="icon-plus icon-white"></i> Adicionar Opção </a>
        </div>
        <br>
        <div class="" ng-if="changed">
            <br>
            <a ng-click="update()" class="btn btn-primary" href="" style="text-align: left;"> <i class="icon-ok-circle icon-white"></i> Manter alterações </a>
        </div>
        
    </div>

    
</div><!-- .panel-right -->


   
</div><!-- OptionsController -->