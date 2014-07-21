<?php
/** ========================================================================
 *    template to show the answers of one query
 * ------------------------------------------------------------------------
 */
if ($answers):

    ?>
    <div class="step-group">
        <div class="step-bar bar">
            <span class="title"><?php echo $query['titulo'] ?></span>
        </div>

        <ul class="answers-list">
        <?php



        $stepCount = 1;
        foreach ($answers as $a):
            ?>
            <li>
                <div class="small">
                    <?php echo $a['data']?>
                </div>
                <?php echo $a['valor']?></li>
            <?php
            $stepCount++;
        endforeach;// steps
        ?>
        </ul>

        <?php echo $this->pagination->create_links();?>

    </div><!-- step-group -->
<?php
endif;
?>



<?php
# modelo
if (true == false):
    ?>
    <!-- grupo de passos  -->
    <div class="step-group">
        <div class="step-bar bar"><span class="type">Passo #1 -</span> <span class="title">Quanto aos seus primeiros atendimentos no consultório do cirurgião</span>
            <a href="#" class="btn">editar</a>
        </div>

        <div class="queries-group">

            <div class="query-bar bar">
                <span class="type">?</span> <span class="title">Atendimento ao telefone para marcação da consulta (rapidez, presteza, educação, etc)</span>
                <a href="#" class="btn">editar</a>
            </div>
            <!-- query-bar -->

            <div class="query-bar bar">
                <span class="type">?</span> <span class="title">Atendimento ao telefone para marcação da consulta (rapidez, presteza, educação, etc)</span>
                <a href="#" class="btn">editar</a>
            </div>
            <!-- query-bar -->

        </div>
        <!-- queries-group -->

        <div class="group-group">
            <div class="group-bar bar"><span class="type">Grupo #1 -</span> <span class="title">Quanto aos seus primeiros atendimentos no consultório do cirurgião (Dr. Alexandre Siciliano)</span>
                <a href="#" class="btn">editar</a>
            </div>
            <div class="queries-group">

                <div class="query-bar bar">
                    <span class="type">?</span> <span class="title">Atendimento ao telefone para marcação da consulta (rapidez, presteza, educação, etc)</span>
                    <a href="#" class="btn">editar</a>
                </div>
                <!-- query-bar -->

                <div class="query-bar bar">
                    <span class="type">?</span> <span class="title">Atendimento ao telefone para marcação da consulta (rapidez, presteza, educação, etc)</span>
                    <a href="#" class="btn">editar</a>
                </div>
                <!-- query-bar -->

            </div>
            <!-- queries-group -->

        </div>
        <!-- group-group -->



<?php endif; ?>