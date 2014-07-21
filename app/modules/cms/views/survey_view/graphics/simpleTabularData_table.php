<table class="table table-condensed table-hover">
    <thead>
    <tr>
        <th class="options">opções</th>
        <th class="percentuals">%</th>
        <th class="percentuals-bar"></th>
    </tr>
    </thead>
    <tbody>

    <?php
    $finalPoints = 0;
    if (isset($result) && $result):
        foreach ($result as $r):

            ?>
            <tr>
                <td class="options"><?php echo $r['label']?></td>
                <td class="percentuals"><?php echo $r['total_perc']?>%</td>
                <td>
                    <div class="percent-bar" style="width:<?php echo $r['total_perc']?>%">
                        <div class="ttl"><?php echo $r['total_answer']?></div>
                        <img src="<?php echo cms_img() ?>color-blue.gif" alt="">
                    </div>
                </td>
            </tr>
        <?php
        endforeach;
    endif;
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td class="">total</td>

        <td class="percentuals">100%</td>
        <td></td>
    </tr>
    </tfoot>
</table>