<?php echo validation_errors(); ?>

<div class="panel-left clearfix">

    <?php ////// REVISÕES BAR ///////
    echo $this->cms_libs->output_revision_options_bar($row);
    ?>

    <div class="ai-page clearfix">

        <div class="ai">
            <label for="grupos" class="lb-full">Categoria</label>
            <?php echo (!$grupos) ? 'Não existem.<br />' : $grupos; ?>
        </div>

        <div class="page">


        </div>

    </div>
    <!-- .ai-page -->

    <div class="control-group -box">

        <label class="lb-full">Eixo temático</label>
        <?php echo $row['eixo_tematico'];?>

    </div>
    <!-- .control-group -->

    <br/>

    <div class="control-group -box">

        <label class="lb-full">Modalidade do trabalho</label>
        <?php echo $row['modalidade'];?>

    </div>
    <!-- .control-group -->


    <div class="control-group -box">

        <label for="titulo" class="lb-full"><b class="obr">[!]</b> Título</label>

        <textarea name="titulo" class="textarea-tags basic-editor" id="titulo" ><?php echo set_value('titulo',
                $row['titulo']); ?></textarea>

    </div>
    <!-- .control-group -->

<!--    <input name="nick_edita" id="" type="text" class="input-apelido" title="Endereço amigável"-->
<!--           placeholder="Endereço amigável" value="--><?php //echo set_value('nick', $row['nick']); ?><!--" readonly="readonly"/>-->
<!---->
<!---->
<!--    <br/>-->


    <br/>

    <div class="control-group -box">

        <label for="subtitulo" class="lb-full">Subtítulo</label>
        <textarea name="subtitulo" class="textarea-tags basic-editor" id="subtitulo" data-editor-limit="130"><?php echo
            set_value('subtitulo',
                get_meta($metas, 'subtitulo', null, true));?></textarea>

    </div>
    <!-- .control-group -->


    <div class="control-group -box">

        <label for="resumo" class="lb-full">Resumo</label>
        <textarea name="resumo" class="textarea-curto basic-editor"
                  id="resumo"><?php echo set_value('resumo', $row['resumo']); ?></textarea>

    </div>
    <!-- .control-group -->

    <div class="control-group -box">

        <label for="tags" class="lb-full">Palavras-chave</label>
        <textarea name="tags" class="textarea-tags" id="tags" data-characters-limit="75"><?php echo set_value('tags',
                $row['tags']);?></textarea>

    </div>
    <!-- .control-group -->


    <div class="control-group">

        <label for="txt" class="lb-full">Proposta <?php if ($multicontent): ?>| <a href="#"
                                                                                             onclick="javascript:$('#tabs').tabs('select',1); return false;">Mais
                conteúdos</a><?php endif; ?></label>

        <div class="content-ctrl">
            <a href="<?php echo cms_url($linkAddImage); ?>" class="ico-img nyroModal" target="_blank">Subir imagem</a>
            <!--<a href="<?php echo cms_url($linkAddArq); ?>" class="bot-verde nyroModal" target="_blank"><span><b class="ico-mais">arquivo</b></span></a>-->
        </div>
        <textarea name="txt" class="textarea-longo" id="txt"
                  style="width:100%"><?php echo set_value('txt', $row['txt']); ?></textarea>

    </div>
    <!-- .control-group -->



    <?php if ($rel != false)
    {
        ?>
        <div class="control-group">

            <label for="rel" class="lb-full">Relacionado à</label><?php echo (!$rel) ? 'Não existem.<br />' : $rel; ?>

        </div><!-- .control-group -->
    <?php } ?>

    <?php
    if ($swfUplForm)
    {

        echo '<div class="control-group box"><div class="attached-box">';
        echo $swfUplForm;
        echo '</div></div>';

    }
    ?>
    <br/>

    <?php ////// REVISÕES ///////
    echo $this->cms_libs->output_revisions($row);
    ?>


</div><!-- .panel-left -->


<div class="panel-right clearfix">


    <div class="control-group box">

        <label for="status" class="lb-full">Status</label>

        <div class="form-opcoes group-buttons">
            <?php echo inputs_status($row['status']); ?>

        </div>
        <div class="help-block">Última atualização em <?php echo datetime_br($row['atualizado']); ?></div>
    </div>
    <!-- .control-group -->

    <div class="control-group box">

        <label for="from" class="lb-full">Data de recebimento</label>
        <input name="dt1" id="from" type="text" class="input-curto"
               value="<?php echo set_value('dt1', $row['dt1']); ?>"/>

    </div>
    <!-- .control-group -->

    <div class="control-group box">

        <label for="" class="lb-full">Autor(es)</label>
        <a href="#" onclick="javascript:$('#tabs').tabs('select',1); return false;">Administrar autor(es)</a>

    </div>
    <!-- .control-group -->




    <div class="control-group box" ng-controller="AvaliadoresController">

        <label for="" class="lb-full">Avaliações</label>
        <a href="" ng-click="toggleAvaliadorDropdown = !toggleAvaliadorDropdown">Adicionar avaliador</a>

        <div class="avals-dropdown" ng-show="toggleAvaliadorDropdown">

            <div class="input-append">
<!--	            label group by group for (key, value) in object-->
                <select ng-model="avaliador" ng-options="avaliador.nome for (idx, avaliador) in avaliadores">
	                <option value="">Escolha</option>
                </select>
                <a class="btn" href="" ng-click="sendAvaliadorInvite()">Adicionar</a>
            </div>



        </div>

        <label for="">Avaliações em andamento</label>

	    <p ng-if="!avaliacoes.awaiting.length">Não há.</p>

        <table class="table table-condensed">
            <tr ng-repeat="ava in avaliacoes.awaiting" ng-show="ava.status != 0">
                <td><span class="author-name">{{ ava.usuario_nome }}</span></td>
	            <td>{{ ava.form_dt_ini }}</td>
                <td><a href="" ng-click="removeAvaliacao(ava, 'awaiting')" title="remover avaliação"><i
			                class="icon-remove"></i></a></td>
            </tr>

        </table>



        <label for="">Avaliações finalizadas</label>

	    <p ng-if="!avaliacoes.finished.length">Não há.</p>

        <table class="table table-condensed">
            <tr ng-repeat="ava in avaliacoes.finished" ng-show="ava.status != 0">
                <td><span class="author-name">{{ ava.usuario_nome }}</span></td>

                <td><span class="label label-success" ng-class="{'label-success': ava.valor == 10,
                'label-important': ava.valor == 5, 'label-warning': ava.valor == 0
                }" title="{{ ava.valor_label }}">{{ ava.valor_label }}</span></td>



	            <td><a href="" title="ver avaliação" ng-click="openAvaliacao(ava)"><i
				            class="icon-file"></i></a></td>
	            <td class="">
		            <div class="dropdown pull-right">
			            <a title="opções" class="dropdown-toggle" id="dLabel" role="button" data-toggle="dropdown"
			               href="#">
				           <i class="icon-cog"></i>
			            </a>
			            <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dLabel">
				            <li><a href="" ng-click="removeAvaliacao(ava, 'finished')">remover avaliação</a></li>
			            </ul>
		            </div>
	            </td>
            </tr>

        </table>

    </div>
    <!-- .control-group -->


	<div class="control-group box">

		<label for="" class="lb-full">Notificar responsável</label>
		<p>Enviar e-mail para o responsável sobre status do trabalho.</p>
		<a href="<?php echo cms_url('cms/usuarios/mensagemForm/id:' . $row['autor'])?>" class="btn nyroModal"
		   target="_blank">Enviar mensagem</a>

	</div>
	<!-- .control-group -->



</div><!-- .panel-right -->

        
              