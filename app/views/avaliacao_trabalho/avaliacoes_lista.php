

<div class="row">

	<div class="col-md-10 col-md-push-1">

		<h1>Listagem de avaliações</h1>

		<?php if ($this->msg): ?>
			<div class="alert alert-<?php echo ($this->msg_type == 'error') ? 'danger' : $this->msg_type ?>">
				<?php echo $this->msg ?>
			</div>
		<?php endif; ?>

		<table class="table table-striped table-avaliacao">
			<thead>


			<tr>
				<th></th>
				<th class="">Título</th>
				<th>Status</th>
				<th>Data</th>
				<th>Ações</th>
			</tr>
			</thead>
			<tbody>




		<?php
		if(isset($evaluations) && $evaluations):

			foreach($evaluations as $ava):
			?>
				<tr class="avaliacao-item">
					<td>
						<?php if($ava['status'] == 1){?>
							<i class="glyphicon glyphicon-ok"></i>
						<?php }?>
					</td>
					<td class="title <?php echo ($ava['status'] == 0) ? 'status-canceled' : ''?>"><?php echo $ava['job']['titulo']?></td>
					<td class="status <?php echo ($ava['status'] == 0) ? 'status-canceled' : ''?>"><?php echo
						$ava['valor_label']?></td>
					<td class="date"><?php echo $ava['form_dt_ini']?></td>
					<td class="actions">
						<?php if($ava['status'] == 2){?>
						<a href="<?php echo site_url('avaliacao/trabalho/'.$ava['id'])?>" class="btn btn-primary">
							Avaliar </a>
						<?php } else if($ava['status'] == 0) {?>
							cancelado
						<?php } else {?>
							-
						<?php }?>

					</td>
				</tr>


				<?php
				endforeach;
				?>
			</tbody>
			</table>

		<?php
		else:
		?>
		<p>Não existem avaliações pendentes.</p>
		<?php
		endif;
		?>


	</div>

</div>