<?php session_start();
///////////////////////////////  Script de envio de emails em massa - Sistema em Batch ///////////////////////////////////////
// Por Bruno Barros - bruno@brunobarros.com - 2011

// includes necessários
include("config.php");
include_once('mailer/language/phpmailer.lang-br.php');
include_once('mailer/class.phpmailer.php'); 


// dados recebidos via GET
$id_mensagem = $_REQUEST['idNews'];
$idAge = $_REQUEST['idAge'];
$co = $_REQUEST['co'];// módulo news

// pega dados de configuração e coloca no array $config
$config = array();
$sql_config = mysql_query("select * from cms_config");
while($c = mysql_fetch_assoc($sql_config)){
	
	if($c['campo'] != ''){
		$config[$c['campo']] = $c['valor'];
	}
	
}

// define o tempo de espera e a quantidade por loop
$porhora = explode('/', $config['porhora']);
$esperaEmSeg = (int)$porhora[1];
$envio_por_hora = $porhora[0]; 

?>
<script type="text/javascript">

	function vTimer() {
		window.setTimeout("vEnviarNews()", <?php echo $esperaEmSeg * 1000;?>);
	}	

	function vEnviarNews() {
		window.location="<?php echo $_SERVER['PHP_SELF']?>?idNews=<?php echo $id_mensagem;?>&idAge=<?php echo $idAge;?>";
		//document.forms['frm-busca'].submit();
	}

</script>


<?php
ini_set("allow_url_fopen", 0);
@set_time_limit(0); // Remove the time limit for command-line usage;

// -- inicializa SESSION ---  //
//$ttl_envios = (isset($_SESSION['ttl_envios'])) ? $_SESSION['ttl_envios'] : 0;
$loop = (isset($_SESSION['loop'])) ? $_SESSION['loop'] : 1;

// -- cancelamento do envio ---  //
if($_REQUEST['op'] && $_REQUEST['op'] == 'cancelar'){
	terminate();
}





ob_start();

$loop++; // toda vez que a página é lida



// variáveis que chegam via POST ou GET
// Se existe o ID da mensagem desejada...
if(isset($id_mensagem)) {
	$sql_mensagem = mysql_query("select * from cms_conteudo where id='$id_mensagem'");
} 
// Senão pega a mensagem ativa no sistema. Só pode haver uma!
else {
	die("Mensagem não foi encontrada.");
}


// verifica se alguma mensagem cumpre este requisito
$num_mensagem = mysql_num_rows($sql_mensagem);
if($num_mensagem == 0) die('Não existe mensagem ativa!');
// Array com dados da mensagem
$a_mensagem = mysql_fetch_array($sql_mensagem);

//// Dados da mensagem que não vão mudar nas interações.
	$id_men = $a_mensagem['id'];
	$assunto = utf8_decode($a_mensagem['titulo']);
							// $formato = $a_mensagem['formato']; // html ou text
	$corpo_html = $a_mensagem['txt'];
	$corpo_text = $a_mensagem['resumo'];
	$remover = 1; // 0 ou 1
	$instrucao = utf8_decode($config['optout']); // texto de rodape
							// $id_do_remetente = $a_mensagem['remetente'];

	
	$nome_remetente = utf8_decode($a_mensagem['tags']);
	$email_resposta = $a_mensagem['extra'];
	// smtp 1
	$email_remetente = trim($config['email1']);	
	$host_smtp = trim($config['host1']);
	$senha_remetente = trim($config['senha1']);
	$email_receb_erros = $config['erros'];  // << -- retorna os erros para este end
	// adicionais smtp 2
	$porta = trim($config['email2']);
	$secure = trim($config['host2']);
	//$senha_remetente2 = trim($config['senha2']);
	



/// Estilos usados na mensagem HTML
$estilos = '';
	
////............../////............./////............./////............./////............./////

	
	/*// globais da mensagem
	global $id_men, $assunto, $formato, $corpo_html, $corpo_text, $remover, $instrucao, $id_do_remetente, $eh_envio_teste, $idAge;
	// globais do remetente
	global $email_resposta, $nome_remetente, $email_remetente, $host_smtp, $senha_remetente, $dobra_envio, $EmailSite, $TituloSite, $email_receb_erros;
	// outros
	global $envio_por_hora, $estilos, $UrlSite;*/
	
	// Instancia o phpMailer e coloca as informações que não mudarão nas interações.
	$mail = new PHPMailer();
	
	// autenticação 
	$mail->Subject = $assunto;
	$mail->From     = $email_remetente;
	$mail->FromName = $nome_remetente;
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host = $host_smtp; // SMTP server
	$mail->SMTPAuth = true;     // turn on SMTP authentication
	$mail->Username = $email_remetente;  // SMTP username
	$mail->Password = $senha_remetente; // SMTP password
	$mail->AddReplyTo($email_resposta, $nome_remetente);
	//$mail->Host     = "67.228.20.248"; //////////////////
	// informando a quem devemos responder. o mail inserido no formulario	
	$mail->Sender = $email_receb_erros;// <<-- receberá os erros
	$mail->SMTPSecure = $secure;
	$mail->Port = $porta;
	
	
	//  pega todos que estão agendados, podem receber News e estão ativos
	$sql_agendados = mysql_query("select * from cms_news_age_users where age_id='$idAge' limit $envio_por_hora");
	
	
	
	
	$num_agendados = mysql_num_rows($sql_agendados);
	if($num_agendados == 0){
		terminate();
	}
	
	// Zera variavel que conta quantas interações houveram
	$total_de_linhas = 0;
	$total_de_erros = 0;
	
	// Inicia as interações
	while($agenda = mysql_fetch_assoc($sql_agendados)){
			
		// dados do usuário disponíves para usar
		$sql_user = mysql_query("select * from cms_usuarios where id='".$agenda['user_id']."'");
		$user = mysql_fetch_assoc($sql_user);
		
		$id_user = $user['id'];
		$nome_user = utf8_decode($user['nome']);
		$email_user = trim($user['email']);
		
		
		
		
		//// Monta corpo da mensagem ----------------------------
		
			
		$img_de_leitura = "<img src=\"".$UrlSite."ci_itens/newsletter_view.php?i=$id_men-$id_user-v\" alt=\" \" width=\"0\" height=\"0\" style=\"display:none;\" />";
	
	
		
		//  [USER] pela ID so usuario
		$corpo_alt_html = str_replace("[USER]", $id_user, $corpo_html);
		//  [IDMEN] pela ID da mensagem. Para evitar que links sejam atribuidos indevidamente a outra mensagem.
		$corpo_alt_html = str_replace("[IDMEN]", $id_men, $corpo_alt_html);
		// tags especiais
		$corpo_alt_html = str_replace("[NOME]", $nome_user, $corpo_alt_html);
		$corpo_alt_html = str_replace("[EMAIL]", $email_user, $corpo_alt_html);
                // no texto puro
                $corpo_text = str_replace("[USER]", $id_user, $corpo_text);
                $corpo_text = str_replace("[IDMEN]", $id_men, $corpo_text);
                $corpo_text = str_replace("[NOME]", $nome_user, $corpo_text);
		$corpo_text = str_replace("[EMAIL]", $email_user, $corpo_text);
		
		$mensagem_html = $estilos . $corpo_alt_html . $img_de_leitura;
		$mensagem_text = $corpo_text;
		
	
			
		
		//// Monta rodapé da mensagem ---------------------------
		  // Se rodapé estiver ativo
		if($remover == 1){	
			
			// Links de descadastramento
			$link_remover = $UrlSite . $index ."/cms/newsopc/remover/" . $id_user . '/'.$id_men;
			// Links para ver online
			$link_ver = $UrlSite . $index ."/cms/newsopc/index/" . $id_user . '/'.$id_men;
			
			// --- HTML ---
			
				
			$mensagem_topo = '<br /><br /><blockquote><font size="2" color="#666666">';
			$mensagem_topo .= "Caso n&atilde;o esteja visualizando corretamente <a href=\"$link_ver\" style=\"color:#678FCD\">clique neste link</a>.";
			$mensagem_topo .= '</font></blockquote>';
			
			$mensagem_rodape = '<br /><br /><blockquote><font size="2" color="#666666">';
			$mensagem_rodape .= '<br /><br />' . $instrucao . '';
			$mensagem_rodape .= "<br /> <a href=\"$link_remover\" style=\"color:#678FCD\">Remover e-mail</a>";
			$mensagem_rodape .= '</font></blockquote>';
			
			// --- TEXT ---		
				
			$mensagem_topo_txt =	'\n\r Caso n&atilde;o esteja visualizando corretamente clique no link abaixo:\n';
			$mensagem_topo_txt .= $link_ver;
			$mensagem_rodape_txt = '\n\r' . $instrucao;
			$mensagem_rodape_txt .= "\n Remover e-mail: \n $link_remover";
					
			
			
		}
		  // Não tem rodapé 
		else {
			$mensagem_rodape = '';
			$mensagem_rodape_txt = '';
		}
		
		//// Combina as partes ----------------------------------
		$mensagem_final_html = $mensagem_topo . $mensagem_html . $mensagem_rodape;
		$mensagem_final_text = $mensagem_topo_txt . $mensagem_text . $mensagem_rodape_txt;
		
		
		//// Finaliza instruções do phpmailer -------------------
		$mail->Body    = $mensagem_final_html;
		$mail->AltBody = $mensagem_final_text;
		$mail->AddAddress($email_user, $nome_user);
		
		//// Envia e pega resultado -----------------------------
		
		for ($i = 0; $i < 10; $i++) {
			$send = $mail->Send();
			if ($send) break;
		}
		
		  // Não enviou!!!
		if(!$send){
			
			// faz estatística se for envio real
			salva_stats($id_men, $id_user, 4, '1');
			
			
			echo "<br /> Erro ao enviar para: " . $email_user . ' ( ' . $mail->ErrorInfo . ' )';
			$total_de_erros++;
		} 
		  // Enviou com sucesso!!!
		else {
		
			 // só faz estatística se for envio real
			salva_stats($id_men, $id_user, 3, '1');
			
			
			echo "<br /> Enviado para: " . $email_user;
			// Acrescenta uma interação
			$total_de_linhas++; // para script original
			$_SESSION['ttl_envios'] = $_SESSION['ttl_envios'] + 1; // para sistema batch
		}
		
		// Limpa o endereço e anexos para próximo loop -----------
		$mail->ClearAddresses();
		// remove usuário do agendamento
		mysql_query("delete from cms_news_age_users where user_id='$id_user'");
		
		//$mail->ClearAttachments();	
		
		
	}// while
	
	$mail->SmtpClose();
	unset($mail); 
	
	////............../////............./////............./////............./////............./////
	// DEBUG
	//echo "<br /><br /> A mensagem de assunto: \"$assunto\" foi enviada para <strong>$total_de_linhas</strong> e-mails.<br />";
	//echo "Erros de envio: <strong>$total_de_erros</strong> e-mails.<br />";
	
	
	
	




/********************************************************************
* Controla cada vez que a página é lida para uma sessão de disparos *
* Via JavaScript recarrega a página                                 *
********************************************************************/

	
	$_SESSION['loop'] = $loop;
	//$_SESSION['ttl_envios'] = $ttl_envios;
	$imgUrl = $UrlSite . 'ci_itens/img/';
	echo '<br /><br /><img src="'.$imgUrl.'news-loader.gif" width="220" height="19" />';
	echo '<pre>----------------------------------';
	// echo '<br />Interação Nº: <strong>' . $_SESSION['loop']. '</strong>';
	echo '<br />Total enviados até aqui: <strong>' . $_SESSION['ttl_envios']. '</strong>';
	echo '<br />Total de erros: <strong>' . $total_de_erros . '</strong>';
	echo '<br />----------------------------------</pre>';
	
	echo "<form id=\"frm-envio-cancelar\" method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "?op=cancelar \">
	<input class=\"btn rollover\" type=\"submit\" id=\"btnCancelarNewsletter\" name=\"btnEnviarNewsletter\" value=\"Cancelar envio\" />
	</form>";
	
	//usleep(10000);
	echo "<script type='text/javascript'>vTimer();</script>";
	
	




ob_end_flush();

	
	
	
/********************************************************************
* Funçõs de apoio *
********************************************************************/
function terminate(){
	global $total_de_erros, $assunto;
	echo '<pre>-------------------------------------';
	//echo '<br />Interação Nº: <strong>' . $_SESSION['loop'] . '</strong>';
	echo '<br />Total enviados até aqui: <strong>' . $_SESSION['ttl_envios'] . '</strong>';
	echo '<br />Total de erros: <strong>' . $total_de_erros . '</strong>';
	echo '<br />------------------------------------</pre>';
	$_SESSION['ttl_envios'] = NULL;
	$_SESSION['loop'] = NULL;
	//debug();
	faz_log("<b>$assunto</b> enviado para ".$_SESSION['ttl_envios']." usuários");
	
	die('+ Fim do envio +');
	
}
function faz_log($log){
	$d = date("Y-m-d");
	$h = date("H:i:s");
	$log = utf8_encode($log);
	//$log = utf8_decode($log);
	mysql_query("insert into cms_news_log (data, hora, log) values ('$d', '$h', '$log')");
}

// salva estatística
	// 1 = click em link
	// 2 = abertura de email
	// 3 = envio ok
	// 4 = erro envio
	// 5 = removido
function salva_stats($id_m, $id_u, $acao, $link = ''){

	$d = date("Y-m-d");
	$h = date("H:i:s");
	mysql_query("insert into cms_news_stats (mens_id, user_id, data, hora, acao, link) values ('$id_m', '$id_u', '$d', '$h', '$acao', '$link')");
	
}



function debug(){
	global $assunto, $email_remetente, $nome_remetente, $host_smtp, $email_remetente, $senha_remetente, $EmailSite, $TituloSite, $email_receb_erros;
	echo '<pre>-----------------------------------------------------------------';
	echo '<br />$mail->Subject = ' . $assunto;
	echo '<br />$mail->From = ' . $email_remetente;
	echo '<br />$mail->FromName = ' . $nome_remetente;
	echo '<br />$mail->Host = ' . $host_smtp;
	echo '<br />$mail->Username = ' . $email_remetente;
	echo '<br />$mail->Password = ' . $senha_remetente;	
	echo '<br />$mail->AddReplyTo('.$EmailSite.', '.$TituloSite.') ';
	echo '<br />$mail->Sender = ' . $email_receb_erros;
	echo '<br />----------------------------------------------------------------</pre>';
	
}
?>