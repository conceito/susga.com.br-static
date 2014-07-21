<!--enquete-->
<div id="enquete">
    <div class="titulo">Enquete</div>
    <div class="corpo">
        <div class="pergunta"><?=$pergunta?></div>
        <form method="post" id="formenquete" action="">
        <input type="hidden" name="idEnquete" value="<?=$id?>" />
        <div class="opcoes">
        	<?php
			$i = 0;
            foreach($opcoes as $op):
			?>
            <div class="opcline">
            <input name="opc" type="radio" value="<?=$op['id']?>" id="opc<?=$i?>" /> <label for="opc<?=$i?>"><?=$op['opcao']?></label>
            </div>
            <br />            
            <?php
			$i++;
            endforeach;
			?>
        </div>
        <div class="resposta"></div>
        <div class="botoes">
            <a href="#" class="botVotar">&nbsp;&nbsp; OK &nbsp;&nbsp;</a>
          <a href="#" class="botResultado">resultados</a>
        </div>
        </form>
    </div>
</div><!--enquete FIM -->