<h2>Senha redefinida</h2>

<p>Prezado(a) <?php echo $user['nome']?>.</p>
<p>Foi feita uma solicitação de nova senha. Caso você não tenha feito esta solicitação, redefina sua senha por
    segurança <a href="<?php echo site_url('inscricao/login')?>">clicando aqui</a>. <br/>
   </p>

<p>Sua nova senha é: <strong><?php echo $newPassword?></strong></p>

<p>Caso haja alguma dúvida, escreva para <a href="mailto:<?php echo $emailRespond?>"><?php echo $emailRespond?></a>
    .</p>

<p>
    Att, <br/>
    Comissão Organizadora
</p>
