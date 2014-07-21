
<?php echo form_open('contato/envia', array('class' => 'form-horizontal', 'id' => 'myform'));?>

  
  <fieldset>
  <legend>Login</legend>
  
      <div class="control-group">
        <label class="control-label" for="inputEmail">Email</label>
        <div class="controls">
          <input type="text" id="inputEmail" name="inputEmail" placeholder="Email" value="<?php echo set_value('inputEmail');?>">
          <?php echo form_error('inputEmail');?>
        </div>
      </div>
      
      
      <div class="control-group">
        <label class="control-label" for="inputPassword">Password</label>
        <div class="controls">
          <input type="password" id="inputPassword" name="inputPassword" placeholder="Password" value="<?php echo set_value('inputPassword');?>">
          <?php echo form_error('inputPassword');?>
        </div>
      </div>
      
      
      <div class="control-group">
        <div class="controls">
          <label class="checkbox">
            <input type="checkbox"> Remember me
          </label>
          <button type="submit" class="btn">Sign in</button>
        </div>
      </div>
  
  </fieldset>
  
  <fieldset>
  <legend>Vários campos</legend>
  
        <label class="checkbox">
        <input type="checkbox" value="">
        Option one is this and that—be sure to include why it's great
        </label>
        
        <label class="radio">
        <input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked>
        Option one is this and that—be sure to include why it's great
        </label>
        <label class="radio">
        <input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
        Option two can be something else and selecting it will deselect option one
        </label>
        
        <div class="control-group">
        <label class="control-label" for="inputEmail">Append</label>
        <div class="controls">
              <label class="checkbox inline">
              <input type="checkbox" id="inlineCheckbox1" value="option1"> 1
            </label>
            <label class="checkbox inline">
              <input type="checkbox" id="inlineCheckbox2" value="option2"> 2
            </label>
            <label class="checkbox inline">
              <input type="checkbox" id="inlineCheckbox3" value="option3"> 3
            </label>
        </div>
      </div>
        
        
        
        
        <div class="control-group">
        <label class="control-label" for="prependedInput">Prepend</label>
        <div class="controls">
          <div class="input-prepend">
          <span class="add-on">@</span><input class="input-medium" id="prependedInput" size="16" type="text" placeholder="Username" value="<?php echo set_value('prependedInput');?>">
			  <?php echo form_error('prependedInput');?>
              <span class="help-inline">Inline help text</span>
        </div>
        </div>
      </div>
      
      <div class="control-group">
        <label class="control-label" for="inputEmail">Append</label>
        <div class="controls">
          <div class="input-append">
          <input class="input-medium" id="appendedInput" size="16" type="text"><span class="add-on">.00</span>
          <span class="help-block">A longer block of help text that breaks onto a new line and may extend beyond one line.</span>
        </div>
        </div>
      </div>
      
      <div class="control-group">
        <label class="control-label" for="inputEmail">appended Prepended Input</label>
        <div class="controls">
          <div class="input-prepend input-append">
              <span class="add-on">$</span><input class="input-medium" id="appendedPrependedInput" size="16" type="text"><span class="add-on">.00</span>
            </div>
        </div>
      </div>
      
      
      
      
      <div class="control-group">
        <label class="control-label" for="inputEmail">appended Input Buttons</label>
        <div class="controls">
          <div class="input-append">
            <input class="input-medium" id="appendedInputButtons" size="16" type="text"><button class="btn" type="button">Search</button><button class="btn" type="button">Options</button>
          </div>
        </div>
      </div>
      
      <div class="control-group">
        <label class="control-label" for="inputEmail">Input sizes</label>
        <div class="controls">
        	<input class="input-mini" type="text" placeholder=".input-mini" value="<?php echo set_value('input');?>">        	<?php echo form_error('input');?>
        </div>
        <div class="controls">
        <input class="input-small" type="text" placeholder=".input-small">
        </div>
        <div class="controls">
        <input class="input-medium" type="text" placeholder=".input-medium">
        </div>
        <div class="controls">
        <input class="input-large" type="text" placeholder=".input-large">
        </div>
        <div class="controls">
        <input class="input-xlarge" type="text" placeholder=".input-xlarge">
        </div>
        <div class="controls">
        <input class="input-xxlarge" type="text" placeholder=".input-xxlarge">
        </div>
      </div>
      
      
      <div class="control-group">
        <label class="control-label" for="inputEmail">select</label>
        <div class="controls">
          <select class="input-xlarge">
          	<option value=""></option>
          </select>
        </div>
      </div>
      
      
      
      <div class="control-group">
        <label class="control-label" for="inputEmail">textarea</label>
        <div class="controls">
          <textarea rows="3" class="input-xlarge"></textarea>
        </div>
      </div>
      
      
        
        
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Save changes</button>
          <button type="button" class="btn">Cancel</button>
        </div>
         
        
  
  </fieldset>
  
  
<?php echo form_close();?>