<div class="col-1">

    <h1>CONTATO</h1>


    <div class="form-horizontal">

        <div class="control-group">
            <label class="control-label" for="">&nbsp;</label>

            <div class="controls">
                <p>* Campos obrigatórios.</p>
            </div>
        </div>
    </div>


    <?php if ($this->msg): ?>
        <div class="alert alert-<?php echo $this->msg_type ?>">
            <?php echo $this->msg ?>
        </div>
    <?php endif; ?>


    <form action="<?php echo site_url('contato/envia'); ?>" class="form-horizontal" method="post" id="frm_contato">

        <div class="control-group">
            <label class="control-label" for="field_nome">* Nome</label>

            <div class="controls">
                <input type="text" id="field_nome" name="nome" class="input-xlarge" required value="<?php echo
                set_value('nome')?>">
                <?php echo form_error('nome') ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="field_email">* E-mail</label>

            <div class="controls">
                <input type="email" id="field_email" name="email" class="input-xlarge" required value="<?php echo
                set_value('email')?>">
                <?php echo form_error('email') ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="field_tel">Telefone</label>

            <div class="controls">
                <input type="text" id="field_tel" name="tel" class="input-medium" placeholder="(xx) xxxx-xxxx"
                       value="<?php echo set_value('tel') ?>">
                <?php echo form_error('tel') ?>
            </div>
        </div>


        <div class="control-group">
            <label class="control-label" for="field_mensagem">* Mensagem</label>

            <div class="controls">
                <textarea name="mensagem" id="field_mensagem" cols="30" rows="4" class="input-xxlarge"
                          require><?php echo
                    set_value('mensagem')?></textarea>
                <?php echo form_error('mensagem') ?>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="">&nbsp;</label>

            <div class="controls">
                <button type="submit" class="btn btn-primary">ENVIAR MENSAGEM</button>
            </div>
        </div>

    </form>


</div>