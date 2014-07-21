<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 * @version $Id$
 * @copyright 2010
 */
class E_mail {

    function E_mail() {
        $this->ci = &get_instance();
    }

    /**
     * Envia email com o PHPmailer
     *
     * @return
     */
    function envia($emailDes = '', $nomeDes = '', $assunto = '', $menHTML = '', $menTXT = '', $emailRem = '', $nomeRem = '', $anexo = null) {

        require_once FCPATH . app_folder() . 'libs/mailer527/class.phpmailer.php';


        $mail = new PHPMailer();
        // Principal settings
        if ($this->ci->config->item('smtp_host') != '') {
            $mail->IsSMTP();
            $mail->Mailer = "smtp";
            $mail->Host = $this->ci->config->item('smtp_host');
            $mail->SMTPAuth = true;
            $mail->Password = $this->ci->config->item('smtp_pass');
            $mail->Username = $this->ci->config->item('smtp_user');
            $mail->SMTPSecure = $this->ci->config->item('smtp_encr');
            $mail->Port = $this->ci->config->item('smtp_port');
        }


        $mail->From = $emailRem;
        $mail->FromName = utf8_decode($nomeRem);
        $mail->AddAddress($emailDes, utf8_decode($nomeDes)); //You can add more emails
        $mail->IsHTML(true); // send as HTML
        $mail->Sender = $this->ci->config->item('smtp_erro'); // <<-- receberá os erros
        // Embed images
        // $mail->AddEmbeddedImage('img/mailings/logo.gif', "logo_header");
        $mail->Subject = utf8_decode($assunto);
        $mail->Body = utf8_decode($menHTML);
        $mail->AltBody = utf8_decode($menTXT);

        if($anexo != null){
            if(is_array($anexo)){
                $arq = $anexo['file'];
                $name = utf8_decode($anexo['name']);
            } else {
                $arq = $anexo;
                $name = '';
            }
            $mail->AddAttachment($arq, $name);
        }

        for ($i = 0; $i < 10; $i++) {
            $send = $mail->Send();
            if ($send)
                break;
        }
        if (!$send) {
            // echo "Message was not sent <br>";
            // echo "Mailer Error: " . $mail->ErrorInfo;
            // exit;
            return false;
        } else {
            return true;
        }
    }

}