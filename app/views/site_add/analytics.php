<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<style type="text/css">
			html, body {
			font-family: Arial, "sans-serif";
			color: #555;
			}
			a {
			 	text-decoration: none;
				padding: 4px 7px;
			}
			table {
				width: 100%;
				border: 1px solid #E1E1E1;
			}
			table, tr, td {
				border-spacing: 0;
				border: none;
				font-size: 12px;
			}
			thead, a {
				background: #5c7fa2;
				color: white;
			}
			th {
				padding: 10px;
				font-size: 14px;
				text-align: left;
				border-left: 1px solid silver;
			
			}
			tbody td {
				background-color: #f8f8f8;
				padding: 5px 10px;
				border-bottom: 1px solid #f1f1f1;
			
			}
			tfoot tr{
				height: 30px;
			}
		</style>
	</head>
	<body>

<!-- tableau pour les villes -->		
		<?php if(isset($cities) && is_array($cities)): ?>
			<h4>
				Visites par ville du <?php echo $cities['summary']->startDate;?>
				au <?php echo $cities['summary']->endDate;?>
			</h4>
		    <table>

		    	<thead>
		    		<tr>
		    			<th>Villes (<?php echo $cities['summary']->totalResults;?>)</th>
		    			<th>Visites (<?php echo $cities['summary']->metrics->visits;?>)</th>
		    			<th>Pages vues (<?php echo $cities['summary']->metrics->pageviews;?>)</th>
		    		</tr>
		    	</thead>
		    	
		    	<tbody>
		    	<?php foreach ($cities as $key => $city): if ($key != 'summary'):?>
		    		<tr>
		    			<td><?php echo $key;?></td>
		    			<td><?php echo $city->visits;?></td>
		    			<td><?php echo $city->pageviews;?></td>
		    		</tr>
		    	<?php endif; endforeach; ?>
		    	</tbody>
		    		
		    	<?php if (isset($pagination)): ?>
		    	    <tfoot>
		    	    	<tr>
		    	    		<td colspan="3"><?php echo $pagination;?></td>
		    	    	</tr>
		    	    </tfoot>
		    	<?php endif;?>
		    
		    </table>
		<?php endif; ?>
		
<!-- tableau pour les site référents -->
		<?php if(isset($referrers) && is_array($referrers)): ?>
			<h4>
		    		Sites référents du <?php echo $referrers['summary']->startDate;?>
		    		au <?php echo $referrers['summary']->endDate;?>
			</h4>
		    <table>

		    	<thead>
		    		<tr>
		    			<th>Sites référents (<?php echo $referrers['summary']->totalResults;?>)</th>
		    			<th>Visites (<?php echo $referrers['summary']->metrics->visits;?>)</th>
		    			<th>Pages vues (<?php echo $referrers['summary']->metrics->pageviews;?>)</th>
		    		</tr>
		    	</thead>
		    	
		    	<tbody>
		    	<?php foreach ($referrers as $key => $ref): if ($key != 'summary'):?>
		    		<tr>
		    			<td><?php echo $key;?></td>
		    			<td><?php echo $ref->visits;?></td>
		    			<td><?php echo $ref->pageviews;?></td>
		    		</tr>
		    	<?php endif; endforeach; ?>
		    	</tbody>
		    
		    </table>
		<?php endif; ?>
		
<!-- Listes pour les comptes -->
		<?php if(isset($accounts)): ?>
		<ol>
			<?php foreach ($accounts as $name => $value):?>
				<?php if ($name == 'segments'):?>
					<li><h4><?php echo $name;?></h4>
						<ul>
							<?php foreach ($value as $segid => $segname):?>
								<li><?php echo $segid;?> : <?php echo $segname;?></li>
							<?php endforeach;?>
						</ul>
					</li>

				<?php else: ?>
					<li><h4><?php echo anchor('labs/ga_test/accounts'.'/'.$value->profileId, $name);?></h4>
						<ul>
							<li>titre: <?php echo $value->title;?></li>
							<li>ID table: <?php echo $value->tableId;?></li>
							<li>ID du compte: <?php echo $value->accountId;?></li>
							<li> Nome du compte: <?php echo $value->accountName;?></li>
							<li>ID de profil: <?php echo $value->profileId;?></li>
							<li>Tracker: <?php echo $value->webPropertyId;?></li>
						</ul>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ol>
		<?php endif; ?>

	</body>
</html>