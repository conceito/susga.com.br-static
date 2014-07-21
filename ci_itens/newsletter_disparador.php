#! /usr/bin/php -q
<?php 
ini_set("allow_url_fopen", 0);
///////////////////////////////  Script de envio de emails em massa - CMS3 v 3.10 ///////////////////////////////////////
// Por Bruno Barros - bruno@brunobarros - 2010

// includes necessários
include("config.php");
include_once('mailer/class.phpmailer.php'); 

//// --- >>> Dados fixos  |||||| 1º |||||||
// Pega os dados dos SMTPs remetente
	$sql_remetente = mysql_query("select valor from cms_config WHERE id >= 8 AND id <= 16 ORDER BY id");

	$remetente = array();
	while($a_remetente = mysql_fetch_array($sql_remetente)){
		$remetente[] = $a_remetente['valor'];
	}

	
	$nome_remetente = utf8_decode($TituloSite);
	$email_remetente = trim($remetente[0]);
	$host_smtp = trim($remetente[1]);
	$senha_remetente = trim($remetente[2]);
	$email_receb_erros = $remetente[3];  // << -- retorna os erros para este end
	// adicionais
	$email_remetente2 = trim($remetente[4]);
	$host_smtp2 = trim($remetente[5]);
	$senha_remetente2 = trim($remetente[6]);
	// verifica se fará o envio dobrado
	$dobra_envio = (strlen($email_remetente2) > 7) ? true : false;
	
	
// Quantidade de emails que são enviados por hora	
	$envio_por_hora = $remetente[7];
// Texto padrão de descadastro
	$texto_rodape_descadastro = $remetente[8];
//// --- >>> ------------------------------------------------  <<< --- ////

//  ----------------------|||||| 2º |||||||
// pega a mensagem ativa no sistema. Só pode haver uma! Verifica as prioridades nos agendamentos
// status 0 = enviando... , 1 = na fila de envio , 2 = terminado
$sql_agendamento = mysql_query("select * from cms_news_age where data <= '".date("Y-m-d")."' and status < '2'  ORDER BY data, id limit 1");

// verifica se algum agendamento cumpre este requisito
$num_agendamento = mysql_num_rows($sql_agendamento);
if($num_agendamento == 0){
	// limpa tabela de ligação dos agendamentos e usuários
	mysql_query("update cms_news_age SET status='2' WHERE data <= '".date("Y-m-d")."'");// define todas como terminado
	mysql_query("truncate table cms_news_age_users");
	die('Não existe mensagem ativa!');
}
// Array com dados da mensagem
$a_agendamento = mysql_fetch_assoc($sql_agendamento);

//  ----------------------|||||| 3º |||||||
//// Verifica se existe algum e-mail para ser enviado, senão desabilita este agendamento
$fila_agendamento = mysql_query("select id from cms_news_age_users where age_id='".$a_agendamento['id']."'");
if(mysql_num_rows($fila_agendamento) == 0){
	mysql_query("update cms_news_age SET status='2' where id='".$a_agendamento['id']."'");
}

//  ----------------------|||||| 4º |||||||
//// Dados da mensagem que não vão mudar nas interações.
$sql_mensagem = mysql_query("select * from cms_conteudo where id='".$a_agendamento['mens_id']."'");
// verifica se a mensagem existe MESMO
if(mysql_num_rows($sql_mensagem) == 0) die('Mensagem não existe');
$a_mensagem = mysql_fetch_assoc($sql_mensagem);

	$id_agendamento = $a_agendamento['id'];
	$id_men = $a_mensagem['id'];
	$assunto = utf8_decode($a_mensagem['titulo']);
	$corpo_html = utf8_decode($a_mensagem['txt']);
	$corpo_text = utf8_decode($a_mensagem['resumo']);
	
///////////  dados estatísticos do envio ////////
// Zera variavel que conta quantas interações ouveram
	$total_de_linhas = 0;
	$total_de_erros = 0;
	

	
	
	
// --- >> primeiro disparo << --- //
disparador();

////// Verifica se vai reenviar com o e-mail alternativo
////// já fez o primeiro envio. Altera as infos do remetente e desativa o envio dobrado, senão entra em loop
if($dobra_envio){
	
	$email_remetente = $email_remetente2;
	$host_smtp = $host_smtp2;
	$senha_remetente = $senha_remetente2;
	$dobra_envio = false; // desliga na segunda vez
		
	disparador();	
}


////............../////............./////............./////............./////............./////
function disparador(){
	
	// globais da mensagem
	global $id_agendamento, $id_men, $assunto, $corpo_html, $corpo_text, $texto_rodape_descadastro;
	// globais do remetente
	global $nome_remetente, $email_remetente, $host_smtp, $senha_remetente, $dobra_envio, $EmailSite, $TituloSite, $email_receb_erros;
	// outros
	global $envio_por_hora, $UrlSite, $total_de_linhas, $total_de_erros;
	
	
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
	$mail->AddReplyTo($EmailSite, $TituloSite);// informando a quem devemos responder. o mail inserido no formulario	
	$mail->Sender = $email_receb_erros;// <<-- receberá os erros
	

	// pega todos que estão agendados
	$sql_agendados = mysql_query("select * from cms_news_age_users where age_id='".$id_agendamento."' limit $envio_por_hora");
	$ttl_agendados = mysql_num_rows($sql_agendados);

		
	// Inicia as interações
	while($user = mysql_fetch_assoc($sql_agendados)){
			
		// dados do usuário disponíves para usar
		$userdata = mysql_query("SELECT id, nome, email, email2 FROM cms_usuarios WHERE id = '".$user['user_id']."'");
		$a_userdata = mysql_fetch_assoc($userdata);
		$id_user = $a_userdata['id'];
		$nome_user = $a_userdata['nome'];
		$email_user = trim($a_userdata['email']);
		
/*		echo '<pre>';
var_dump($a_mensagem);
exit;*/
		
		// Retira este usuário do agendamento
		mysql_query("delete from cms_news_age_users WHERE age_id='".$id_agendamento."' AND user_id='$id_user'");
		
		
		
		//// Monta corpo da mensagem ----------------------------
		
			// --- HTML ---			
			// imagem para captura de visualização do email
			$img_de_leitura = "<img src=\"".$UrlSite."ci_itens/newsletter_view.php?i=$id_men-$id_user\" alt=\" \" width=\"0\" height=\"0\" />";		
			
				
			//  [USER] pela ID so usuario
			$corpo_alt_html = str_replace("[USER]", $id_user, $corpo_html);
			//  [IDMEN] pela ID da mensagem. Para evitar que links sejam atribuidos indevidamente a outra mensagem.
			$corpo_alt_html = str_replace("[IDMEN]", $id_men, $corpo_alt_html);
			
			$mensagem_html = $corpo_alt_html . $img_de_leitura;			
				
			
			// --- TEXT ---			
			$mensagem_text = nl2br($corpo_text);
			
		
		//// Monta rodapé da mensagem ---------------------------
			
		// Links de descadastramento
		$link_remover = $UrlSite . "index.php/cms/newsopc/remover/" . $id_user;
		// Links para ver online
		$link_ver = $UrlSite . "index.php/cms/newsopc/index/". $id_men;
		
		// --- HTML ---			
		$mensagem_rodape_html = '<br /><br /><blockquote><font size="2" color="#666666">';
		$mensagem_rodape_html .= "Caso não esteja visualizando corretamente <a href=\"$link_ver\" style=\"color:#678FCD\" target=\"_blank\">clique neste link</a>.";
		$mensagem_rodape_html .= '<br /><br />' . $texto_rodape_descadastro . '';
		$mensagem_rodape_html .= "<br /> <a href=\"$link_remover\" style=\"color:#678FCD\" target=\"_blank\">Remover e-mail</a>";
		$mensagem_rodape_html .= '</font></blockquote>';
		
		// --- TEXT ---	
		$mensagem_rodape_txt =	'\n\r Caso não esteja visualizando corretamente clique no link abaixo:\n';
		$mensagem_rodape_txt .= $link_ver;
		$mensagem_rodape_txt .= '\n\r' . $texto_rodape_descadastro;
		$mensagem_rodape_txt .= "\n Remover e-mail: \n $link_remover";
				
			
		
		//// Combina as partes ----------------------------------
		$mensagem_final_html = $mensagem_html . utf8_decode($mensagem_rodape_html);
		$mensagem_final_text = $mensagem_text . utf8_decode($mensagem_rodape_txt);
		
		
		//// Finaliza instruções do phpmailer -------------------
		$mail->Body    = $mensagem_final_html;
		$mail->AltBody = $mensagem_final_text;
		$mail->AddAddress($email_user, $nome_user);
		
		//// Envia e pega resultado -----------------------------
		  // Não enviou!!!
		if(!$mail->Send()){
			
			salva_stats($id_men, $id_user, 4, '');			
			
			echo "<br /> Erro ao enviar para: " . $email_user;
			$total_de_erros++;
		} 
		  // Enviou com sucesso!!!
		else {
		
			salva_stats($id_men, $id_user, 3, '');			
			
			echo "<br /> Enviado para: " . $email_user;
			// Acrescenta uma interação
			$total_de_linhas++;
		}
		
		// Limpa o endereço e anexos para próximo loop -----------
		$mail->ClearAddresses();
		//$mail->ClearAttachments();	
		
		
	}// while
	
	
	

}// fim função disparador
// Depois das interações

////............../////............./////............./////............./////............./////
	// DEBUG
	echo "<br /><br /> A mensagem de assunto: \"$assunto\" foi enviada para <strong>$total_de_linhas</strong> e-mails.<br />";
	echo "Erros de envio: <strong>$total_de_erros</strong> e-mails.<br />";
	
	// Faz um log do envio
	
	$log = "Assunto: $assunto, para $total_de_linhas usu&aacute;rios. (Erros: $total_de_erros)";
	// se enviou, faz log
	if($total_de_linhas > 0) faz_log($log);





function faz_log($log){
	$d = date("Y-m-d");
	$h = date("H:i:s");
	$log = utf8_encode($log);
	//$log = utf8_decode($log);
	mysql_query("insert into cms_news_log (data, hora, log) values ('$d', '$h', '$log')");
}

// salva estatística
	// s = enviado
	// c = click
	// v = view
	// e = erro
	// r = removido
function salva_stats($id_m, $id_u, $acao, $link = ''){

	$d = date("Y-m-d");
	$h = date("H:i:s");
	mysql_query("insert into cms_news_stats (mens_id, user_id, data, hora, acao, link) values ('$id_m', '$id_u', '$d', '$h', '$acao', '$link')");
	
}
?>