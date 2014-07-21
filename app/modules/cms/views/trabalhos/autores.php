<?php echo validation_errors(); ?>

<div ng-controller="AutoresController">





    <div class="panel-left-lg clearfix">

        <ul ui-sortable="sortableOptions" ng-model="opts" class="sortable-items">


            <li class="control-group box "  ng-repeat="opt in opts">
                <a href="" ng-click="remove(opt)" class="close">&times;</a>

                <label for="titulo" class="-lb-full handle">Nome
                    <span ng-if="opt.user_id">| <a
                        href="{{base_url}}cms/usuarios/edita/co:25/id:{{opt.user_id}}">mais</a></span>
                </label>
                <input name="nome" type="text" class="input-longo" ng-model="opt.nome" />

                <label for="resumo" class="-lb-full">Currículo</label>

<!--                <text-angular-toolbar name="toolbar1">-->
<!--                    ecemplo-->
<!--                </text-angular-toolbar>-->

                <div text-angular ng-model="opt.curriculo" ta-toolbar="[['bold','italics'],['html']]"></div>

<!--                <textarea name="curriculo" class="textarea-tags " rows="4" ng-model="opt.curriculo"></textarea>-->


                <label style="display: inline-block;margin-left: 10px;">
                    <input type="checkbox" ng-model="opt.status" ng-checked="opt.status" value="1" style="float: left; display: inline-block; margin: 1px 5px 0 0;"> Ativo</label>


            </li><!-- .control-group -->
        </ul>


    </div><!-- .panel-left -->


    <div class="panel-right-sm clearfix">


        <div class="control-group box">
            <br>
            <div class="btn-group btn-group-vertical">
                <a ng-click="addAuthor()" class="btn btn-warning" href="" style="text-align: left;"> <i class="icon-plus icon-white"></i> Adicionar Autor </a>

            </div>
            <br>
            <div class="" ng-if="changed">
                <br>
                <a ng-click="update()" class="btn btn-primary" href="" style="text-align: left;"> <i class="icon-ok-circle icon-white"></i> Manter alterações </a>
            </div>

        </div>


    </div><!-- .panel-right -->



</div><!-- OptionsController -->
              