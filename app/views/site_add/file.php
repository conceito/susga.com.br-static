<form class="well" method="post" action="<?php echo site_url('file/send');?>" enctype="multipart/form-data">
<label>Arquivo</label>
<input type="file" name="arquivo[]" class="span3" placeholder="Type something…" multiple="multiple">

<label>Arquivo2</label>
<input type="file" name="arquivo2" class="span3" placeholder="Type something…">

<button type="submit" class="btn">Submit</button>
</form>