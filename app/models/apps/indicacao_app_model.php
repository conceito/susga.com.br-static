<?php

class Indicacao_app_model extends CI_Model {
    function __construct()
    {
        parent::__construct();
    }

    function envia($nomeRem, $emailRem, $nomeDes, $emailDes)
    {
        // corpo
        $dados['nomeDest'] = $nomeDes;
        $dados['nomeRem'] = $nomeRem;
        $dados['emailRem'] = $emailRem;
        $menHTML = $this->load->view('apps/indicacao_email_html', $dados, true);

        $ret = $this->email($emailRem, utf8_decode($nomeRem), utf8_decode('Indicação ' . $this->config->item('title')), utf8_decode($menHTML), utf8_decode($menHTML), $emailDes, utf8_decode($nomeDes));
        return $ret;
    }

    function email($emailRem, $nomeRem, $assunto, $menHTML, $menTXT, $emailDes, $nomeDes)
    {
         require_once FCPATH . 'libs/mailer/class.phpmailer.php';

        $mail = new PHPMailer();

        // Principal settings
        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->Host = $this->config->item('smtp_host');
        $mail->SMTPAuth = true;
        $mail->Password = $this->config->item('smtp_pass');
        $mail->Username = $this->config->item('smtp_user');

        $mail->From = $emailRem;
        $mail->FromName = $nomeRem;
        $mail->AddAddress($emailDes, $nomeDes); //You can add more emails
        $mail->IsHTML(true); // send as HTML
        $mail->Sender = $this->config->item('smtp_erro'); // <<-- receberá os erros
        // Embed images
        // $mail->AddEmbeddedImage('img/mailings/logo.gif', "logo_header");
        $mail->Subject = $assunto;
        $mail->Body = $menHTML;
        $mail->AltBody = $menTXT;

        if (!$mail->Send()) {
            // echo "Message was not sent <br>";
            // echo "Mailer Error: " . $mail->ErrorInfo;
            // exit;
            return false;
        } else {
            return true;
        }
    }
}

?>