<h1>LOGIN</h1>

<div id="page" class="outra-classe">

	<h1 class="page-title">Identificação de avaliador</h1>

	<div class="form-horizontal">

		<div class="form-group">
			<div class="col-sm-8 col-sm-push-2">
				<p>Todos os campos são obrigatórios.</p>
			</div>
		</div>
	</div>



	<?php if ($this->msg): ?>
		<div class="alert alert-<?php echo ($this->msg_type == 'error') ? 'danger' : $this->msg_type ?>">
			<?php echo $this->msg ?>
		</div>
	<?php endif; ?>





	<form action="<?php echo site_url('avaliacao/post_login'); ?>" class="form-horizontal -form-validate" method="post"
	      id="frm_inscricao">

		<fieldset>
			<br/>

			<div class="form-group <?php err('email') ?>">
				<label class="col-sm-3 control-label" for="field_email">E-mail</label>

				<div class="col-sm-8">
					<input type="email" id="field_email" name="email" class="form-control" required value="<?php echo
					set_value('email')?>">
					<?php echo form_error('email') ?>
				</div>
			</div>

			<div class="form-group <?php err('senha') ?>">
				<label class="col-sm-3 control-label" for="field_senha">Senha</label>

				<div class="col-sm-4">
					<input type="password" id="field_senha" name="senha" class="form-control" value="" required pattern="\S{6,}">

					<?php echo form_error('senha') ?>
				</div>
			</div>

			<div class="form-group ">
				<label class="col-sm-3 control-label" for="field_senha"></label>

				<div class="col-sm-4">
					<a href="#" data-toggle="modal" data-target="#modalPassword">Redefinir senha</a>
				</div>
			</div>

		</fieldset>


		<div class="form-group">
			<div class="col-sm-8 col-sm-push-3">
				<button type="submit" class="btn btn-success">ENTRAR</button>
			</div>
		</div>

	</form>


</div>




<div id="modalPassword" class="modal fade">
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Redefinir senha</h4>
			</div>
			<div class="modal-body">

				<p>Por questões de segurança uma nova senha será gerada e enviada para o email cadastrado.</p>

				<form action="" method="post" class="clearfix form-horizontal" id="renew-password">

					<div class="alert alert-warning hide">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						<strong class="text"></strong>
					</div>

					<div class="form-group ">
						<label class="col-sm-2 control-label" for="field_renew_email">E-mail</label>

						<div class="col-sm-8">
							<input type="email" id="field_renew_email" name="renew_email" class="form-control" required
							       value="">
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-8 col-sm-push-2">
							<button type="submit" class="btn btn-success">GERAR SENHA</button>
						</div>
					</div>
				</form>


			</div>

		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->