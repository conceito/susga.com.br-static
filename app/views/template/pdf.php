<?php
/**
 * main page for PDFs exportation
 */
?>
<style type="text/css">
	<!--
	table.page_header {
		width: 100%;
		border: none;
		background-color: #ffab09;
		padding: 2mm 10mm;
	}
	table.page_footer {
		width: 100%;
		border: none;
		background-color: #ffffff;
		border-top: solid 1px #eeeeee;
		padding: 2mm 10mm;
		color: #999999;
		font-size: 11px;
		text-align: right;
	}
	table.page_header td,
	table.page_footer td{
		width: 100%;
	}
	-->
</style>

<page backtop="14mm" backbottom="14mm" backleft="10mm" backright="10mm" pagegroup="">
	<page_header>
		<table class="page_header">
			<tr>
				<td>
					<?php echo $this->config->item('title')?>
				</td>
			</tr>
		</table>
	</page_header>
	<page_footer>
		<table class="page_footer">
			<tr>
				<td>
					[[page_cu]]/[[page_nb]]
				</td>
			</tr>
		</table>
	</page_footer>

	<?php
	if(isset($pageBody)) echo $pageBody;
	?>

</page>