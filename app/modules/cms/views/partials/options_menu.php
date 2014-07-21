<?php 
if(isset($items) && $items):
?>
<div class="btn-group -pull-right">
    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
        <i class="icon-cog"></i>
        <span class="caret"></span>
    </a>
    <ul class="dropdown-menu -pull-right">

        <?php 
        foreach ($items as $i):
        ?>
        <li><a href="<?php echo $i['url'] ?>"><?php echo $i['icon'] ?> <?php echo $i['label'] ?></a></li>
        <?php endforeach; ?>
    
    </ul>
</div>

<?php endif; ?>