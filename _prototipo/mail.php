<?php
/*
|==============================================================================
|		MAIL
|------------------------------------------------------------------------------
*/
require_once("config.php");
$conf = require('inc/email.php');

$type = (isset($_GET['t'])) ? $_GET['t'] : 'contato';

/**
 * clean session
 */
$_SESSION['post'] = null;
$_SESSION['post'] = $_POST;
/**
 * Init vars
 */
$formName  = '';
$formEmail = '';

///////////// TIPO DE FORMULÁRIO /////////////////////

if ($type == 'contato'):

	// override to.email, or leave empty
	$toMail = '';

	/**
	 * POST data
	 */
	$subject   = 'Contato pelo site';
	$formName  = trim($_POST['nome']);
	$formEmail = trim($_POST['email']);
	$msg = trim($_POST['mensagem']);

	/**
	 * Validation
	 */
	if (strlen($formName) == 0)
	{
		$_SESSION['error']['msg'] = 'Preencha o nome.';
		$_SESSION['error']['id']  = 1;
		redirect($type);
	}
	else if (!filter_var($formEmail, FILTER_VALIDATE_EMAIL))
	{
		$_SESSION['error']['msg'] = 'Preencha o e-mail.';
		$_SESSION['error']['id']  = 1;
		redirect($type);
	}
	else if (strlen($msg) == 0)
	{
		$_SESSION['error']['msg'] = 'Deixe sua mensagem.';
		$_SESSION['error']['id']  = 1;
		redirect($type);
	}

	$body = '<b>Nome: </b>' . $formName . '';
	$body .= '<br>' . PHP_EOL;
	$body .= '<b>E-mail: </b>' . $formEmail;
	$body .= '<br>' . PHP_EOL;
	$body .= $msg;

	$html = load_email_template($body);


endif;
// contato

///////////////////// PHP MAILER ////////////////////////////////////
require_once('inc/mailer5/class.phpmailer.php');

$mail            = new PHPMailer();
$mail->SMTPDebug = false;
$mail->WordWrap  = 50;

$mail->Subject  = utf8_decode($subject);
$mail->From     = (strlen($conf['from']['email'])) ? $conf['from']['email'] : $formEmail;
$mail->FromName = utf8_decode((strlen($conf['from']['name'])) ? $conf['from']['name'] : $formName);

if (isset($conf) && strlen($conf['host']))
{
	$mail->SMTPAuth = true;
	$mail->IsSMTP();
	$mail->SMTPSecure = $conf['encr'];
	$mail->Host       = $conf['host'];
	$mail->Username   = $conf['user'];
	$mail->Password   = $conf['pass'];
	$mail->Sender     = $conf['user'];
	$mail->Port       = $conf['port'];
}

$mail->Body    = utf8_decode($html);
$mail->AltBody = strip_tags(utf8_decode($html));

$mail->AddAddress((strlen($conf['to']['email']) && strlen($toMail) == 0) ? $conf['to']['email'] : $toMail, utf8_decode((strlen($conf['to']['name'])) ? $conf['to']['name'] : $titleSite));

$mail->AddReplyTo($formEmail, utf8_decode($formName));

if (isset($conf['copies']) & !empty($conf['copies']))
{
	foreach ($conf['copies'] as $copy)
	{
		$mail->AddCC($copy);
	}
}

if (!$mail->Send())
{
	$_SESSION['error']['msg'] = $mail->ErrorInfo;
	$_SESSION['error']['id']  = 2;
}
else
{
	$mail->ClearAddresses();

	$_SESSION['error']['msg'] = 'Obrigado pelo contato. Responderemos em breve.';
	$_SESSION['error']['id']  = 0;
}

redirect($type);