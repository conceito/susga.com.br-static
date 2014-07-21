<?php

class Contato_m extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Verifica se a string inicia com [debug]
     *
     * @param string $str
     * @return boolean
     */
    function debugMode($str = '')
    {

        if (strlen($str) == 0)
        {
            $d = false;
        }
        else if (strtolower(substr($str, 0, 7)) == '[debug]')
        {
            $d = true;
        }
        else
        {
            $d = false;
        }

        return $d;
    }

    function envia_contato()
    {

        $nome     = $this->input->post('nome');
        $email    = $this->input->post('email');
        $tel      = $this->input->post('tel');
        $assunto  = 'Mensagem pelo site';
        $mensagem = $this->input->post('mensagem');

        /*
         * monta html
         */
        $body = "<h2>{$assunto}</h2>
        <table border=\"0\">
                <tr>
                        <th align=\"right\">Nome</th>
                        <td>{$nome}</td>
                </tr>
                <tr>
                        <th align=\"right\">E-mail</th>
                        <td>{$email}</td>
                </tr>
                <tr>
                        <th align=\"right\">Telefone</th>
                        <td>{$tel}</td>
                </tr>
                <tr>
                        <th align=\"right\">Mensagem</th>
                        <td>{$mensagem}</td>
                </tr>
        </table>";


        $html = $this->emailTemplate($body);

        /*
         * instancia library
         */
        $this->load->library('e_mail');

        if ($this->debug)
        {
            $emailDes = $this->config->item('email_debug');
            $assunto  = '[debug] ' . $assunto;
        }
        else
        {
            $emailDes = $this->config->item('email1');
        }

        $nomeDes  = $this->config->item('title');
        $menHTML  = $html;
        $menTXT   = strip_tags($html);
        $emailRem = $email;
        $nomeRem  = $nome;

        $ret = $this->e_mail->envia($emailDes, $nomeDes, $assunto, $menHTML, $menTXT, $emailRem, $nomeRem);

        return $ret;
    }

    function envia_curriculo()
    {

        $nome     = $this->input->post('nome');
        $email    = $this->input->post('email');
        $tel      = $this->input->post('tel');
        $mensagem = $this->input->post('obs');
        $assunto  = 'Trabalhe Conosco';

        $validou = $this->validaArq($_FILES['arquivo']);

        // se não validou
        if (!$validou)
        {
            return false;
            exit;
        }

        // arquivo
        $name          = $_FILES['arquivo']['name'];
        $tmp           = $_FILES['arquivo']['tmp_name'];
        $anexo['file'] = $tmp;
        $anexo['name'] = $name;

        //        echo "<pre>";
        //        var_dump($_FILES['arquivo']);
        //        exit;

        /*
         * monta html
         */
        $html = "Nome: " . $nome . "<br>" . PHP_EOL;
        $html .= "E-mail: " . $email . "<br>" . PHP_EOL;
        $html .= "Telefone: " . $tel . "<br>" . PHP_EOL;
        $html .= "Observações: " . nl2br($mensagem) . "<br>" . PHP_EOL;

        /*
         * instancia library
         */
        $this->load->library('e_mail');

        $emailDes = $this->config->item('email1');
        $nomeDes  = $this->config->item('title');
        $menHTML  = $html;
        $menTXT   = $html;
        $emailRem = $email;
        $nomeRem  = $nome;

        $ret = $this->e_mail->envia($emailDes, $nomeDes, $assunto, $menHTML, $menTXT, $emailRem, $nomeRem, $anexo);

        return $ret;
    }


    /**
     * Monta templade de e-mail
     *
     * @param string $body
     * @return string
     */
    public function emailTemplate($body = '')
    {
        $view['body'] = $body;
        return $this->load->view('template/email', $view, true);

    }

}