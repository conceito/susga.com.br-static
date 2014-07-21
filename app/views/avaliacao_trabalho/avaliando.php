<div ng-controller="AvaliacaoController">


	<div class="row">

		<div class="col-xs-12 col-lg-10 col-lg-push-1">
			<h1><a href="<?php echo site_url('avaliacao/index')?>" class="back-link">&lt;</a> Avaliando trabalho</h1>

			<p><b>Caro avaliador, <br/> após avaliar o trabalho (painel esquerdo), preencha o formulário (painel
				direito) de forma completa para validar sua avaliação.</b></p>

			<hr/>

			<br/>
			<br/>

		</div>

	</div>

	<div class="row">

		<div class="col-xs-7 col-lg-6 col-lg-push-1">

			<div class="panel-job">

				<div class="job-label">Eixo temático</div>
				<div class="job-content">
					<?php echo $evaluation['job']['eixo_tematico'] ?>
				</div>

				<div class="job-label">Modalidade</div>
				<div class="job-content">
					<?php echo $evaluation['job']['modalidade'] ?>
				</div>

				<div class="job-label">Título</div>
				<div class="job-content">
					<?php echo $evaluation['job']['titulo'] ?>
				</div>

				<div class="job-label">Subtítulo</div>
				<div class="job-content">
					<?php echo $evaluation['job']['subtitulo'] ?>
				</div>

				<div class="job-label">Resumo</div>
				<div class="job-content">
					<?php echo $evaluation['job']['resumo'] ?>
				</div>

				<div class="job-label">Palavras-chave</div>
				<div class="job-content">
					<?php echo $evaluation['job']['tags'] ?>
				</div>

				<div class="job-label">Proposta</div>
				<div class="job-content">
					<?php echo $evaluation['job']['txt'] ?>
				</div>

			</div>


		</div>

		<div class="col-xs-5 col-lg-4  col-lg-push-1" >


			<div class="panel-form">

				<div class="form-title">Ficha de avaliação</div>
				<form action="" name="f">


					<label class="form-label" for="">Marque os itens para confirmar sua aprovação.</label>

					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q1"> O trabalho apresenta adequação do título ao
							trabalho
							proposto
						</label>
					</div>
					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q2"> O trabalho apresenta adequação do trabalho ao
							eixo temático
						</label>
					</div>
					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q3"> O trabalho apresenta adequação do trabalho à
							modalidade proposta
						</label>
					</div>
					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q4"> O trabalho apresenta coerência e articulação
							de ideias no texto
						</label>
					</div>
					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q5"> O trabalho apresenta contribuição para as reflexões contemporâneas
							da abordagem
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q6"> O trabalho apresenta adequação do método aos
							objetivos propostos
						</label>
					</div>
					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q7"> O trabalho apresenta clareza na apresentação dos resultados e sua
							discussão (se for o caso)
						</label>
					</div>
					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q8"> O trabalho apresenta referências
							bibliográficas relevantes e
							compatíveis com o assunto desenvolvido
						</label>
					</div>

					<label class="form-label" for="">Conforme a modalidade do trabalho confirme se a proposta contempla
						os itens necessários a sua modalidade:</label>

					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q9">MR e TL: introdução, relevância,
							conceitos principais, objetivos,
							síntese do estudo, aspectos em discussão e bibliografia
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q10">MC: introdução, relevância, conceitos principais, objetivos, síntese do estudo, metodologia (teórico ou teórico-vivencial), aspectos em discussão e bibliografia
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q11">WS: introdução, objetivos, metodologia,
							recursos e bibliografia
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q12">EG: contexto, justificativa, público-alvo, objetivos, metodologia, resultados, conclusões, sugestões
						</label>
					</div>

					<div class="checkbox">
						<label>
							<input type="checkbox" ng-model="aval.q13">PO: apresentação do tema, objetivos, metodologia e resultados (quando houver), bibliografia
						</label>
					</div>

					<div class="form-group">
						<label class="form-label" for="field_comments">Comentários do avaliador </label>
						<textarea  ng-model="aval.q14" id="field_comments" rows="4" class="form-control"></textarea>
					</div>

					<div class="form-group">
						<label class="form-label" for="field_status">O trabalho está:</label>
						<select  ng-model="aval.q15" id="field_status" class="form-control" required>
							<option value="">-- selecione --</option>
							<option value="10">Aprovado</option>
							<option value="5">Aprovado com solicitação de correções</option>
							<option value="0">Reprovado</option>
						</select>
					</div>

					<div class="form-group">
						<button type="submit" ng-click="sendEvaluation()" class="btn btn-primary btn-block "
						        ng-disabled="f.$invalid">ENVIAR
							AVALIAÇÃO</button>
					</div>


				</form>

			</div>


		</div>

	</div>




	<div id="avalStatus" class="modal fade">
		<div class="modal-dialog ">
			<div class="modal-content">
				<div class="modal-header">
<!--					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>-->
					<h4 class="modal-title">Obrigado!</h4>
				</div>
				<div class="modal-body">

					<p>Sua avaliação foi enviada com sucesso.</p>

					<p><a href="<?php echo site_url('avaliacao/index')?>" class="btn btn-primary">Voltar para
							suas avaliações</a></p>

				</div>

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


</div>
<!--AvaliacaoController-->


