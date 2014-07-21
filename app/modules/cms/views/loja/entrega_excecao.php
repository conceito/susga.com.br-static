<script type="text/javascript">
function nonWorkingDates(date){
	//alert(closedDays);
	var day = date.getDay(), Sunday = 0, Monday = 1, Tuesday = 2, Wednesday = 3, Thursday = 4, Friday = 5, Saturday = 6;
	var closedDates = <?php echo $row['nonWorkingDates'];?>; 
	//[[7, 29, 2009], [8, 25, 2010]];
	var closedDays = <?php echo $row['nonWorkingDays'];?>;//[[Monday], [Sunday]];
	
	for (var i = 0; i < closedDays.length; i++) {
		if (day == closedDays[i][0]) {
			return [false];
		}

	}

	for (i = 0; i < closedDates.length; i++) {
		if (date.getMonth() == closedDates[i][0] - 1 &&
		date.getDate() == closedDates[i][1] &&
		date.getFullYear() == closedDates[i][2]) {
			return [false];
		}
	}

	return [true];
}
</script>
    
<div id="datapicker"></div>


<div class="new-closed-dates">
    <label for="dates_for_all" class="checkbox"><input type="checkbox" name="dates_for_all" id="dates_for_all" value="1"> Adicionar novas datas para todas as regiões</label>
 

</div><!-- .new-closed-dates -->

<div class="closed-dates">
	<label class="lb-full">Datas que não estarão disponíveis para entrega</label>
    <?php
    ///////////// datas exceção ////////////////
	$datas_str = trim($row['txtmulti'], ',');
	$dates = explode(',', $datas_str);
	
	if(count($dates) > 0 && $dates[0] != ''){
		foreach($dates as $c => $dt):
		
		$d = formaPadrao($dt);
		?>
        <div class="tag-item"> 
            <span class="tag-label"><?php echo $d;?></span> 
            <a href="#" title="remover" class="del">x</a> 
            <input name="nondates[]" type="hidden" value="<?php echo $d;?>">
        </div>
        <?php
		endforeach;
	}
	?>
    
	
	
    
    
</div><!-- .closed-dates -->