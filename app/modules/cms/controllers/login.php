<?php

/**
 * Controla a exibição da tela de login e validação da mesma
 *
 * @version $Id$
 * @copyright 2009
 */
class Login extends MX_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->helper('cms/cmshelper');
        $this->load->helper('cookie');
    }

    function index()
    {
        
        
        
        // -- Nome da página -- //
        $title = 'Faça seu Login';

        // -
        // -- carrega classes -- //
        $this->load->model(array('cms/painel_model'));
        $this->load->helper('cms/cmshelper');
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array('r');
        $r = $var['r'];
        
        // -
        // -- processa informações -- //
        if ($r == 'nao-logado')$resposta = 'Você deve fazer o seu login.';
        else if ($r == 'invalido')$resposta = 'Login ou Senha não conferem!';
        else if ($r == 'desconectado')$resposta = 'Você foi desconectado.';
        else if ($r == 'nao-admin')$resposta = 'Este email não é de um adminnistrador válido.';
        else if ($r == 'sim-admin')$resposta = 'Seu <i>login</i> e <i>senha</i> foram envidos para sua caixa de email.';
        else $resposta = '';
        // -
        // -- chama as views -- //
        $tmp['title'] = $title;
        $tmp['resposta'] = $resposta;
        
        // -
        // -- descarrega no template -- //
        $this->load->view('cms/template_login', $tmp);
    }

    /**
     * faz o processamento dos dados de login e retorna
     *
     * @return
     */
    function fazLogin()
    {
        // -- Nome da página -- //
        $nomePag = '';
      
        // -- carrega classes -- //
        $this->load->library('form_validation');
        $this->load->model(array('sessao_model'));

        // -
        // -- processa informações -- //
        $this->form_validation->set_rules('adminlogin', 'seu login', 'trim|required|min_length[5]');
        $this->form_validation->set_rules('adminsenha', 'sua senha', 'trim|required|min_length[5]');
        $this->form_validation->set_rules('salvar_cookie', 'salvar_cookie', 'trim');
        
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        if ($this->form_validation->run() == false) {
            $this->index('r:invalido'); //
        }
        // válido
        else {
            $ret = $this->sessao_model->valida_usuario(); //
            if ($ret) {
                redirect('cms');
            } else {
                redirect('cms/login/index/r:invalido'); // falhou
            }
        }
    }

    function logout()
    {
        $this->phpsess->clear(null, 'cms');
        $this->phpsess->destroy();
        // remove cookie se existir
        delete_cookie('cmsadminlogin');
        redirect('cms/login/index/r:desconectado');
    }

    /**
     * durante a digitação verifica se o email admin existe
     *
     * @return
     */
    function validaEmailAdmin()
    {
        $email = $this->input->post('email');
        
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->db->where('email', $email);
            $this->db->where('status', 1);
            $sql = $this->db->get('cms_admin');
            if ($sql->num_rows() == 0) {
                echo 'nao';
            } else {
                echo 'sim';
            }
        } else {
            echo '';
        }
    }

    /**
     * Lembra senha do usuário.
     *
     * @return bool
     */
    function lembraSenha()
    {
        $email = trim($this->input->post('adminemail'));
        // verifica se existe
        $this->db->where('email', $email);
        $this->db->where('status', 1);
        $sql = $this->db->get('cms_admin');
        // envia email
        if ($sql->num_rows() == 1) {
            $row = $sql->row_array();

            $this->load->helper('string');
            $novaSenha = random_string('alnum', 6);

            $this->db->where('email', $email);
            $this->db->update('cms_admin', array('senha' => md5($novaSenha)));
            // monta mensagem
            $tit = $this->config->item('title');
            $tracos = str_pad('-', strlen($tit), '-');
            $login = $row['login'];
            $senha = $novaSenha;
            $url_admin = cms_url('cms/login');
            $menHTML = "
<h2>$tit<br />
$tracos</h2>
<br /><br />
Esta mensagem foi enviada para lembrar sua senha, caso não tenha feito esta solicitação, entre em contato com o webmaster.
<br /><br />
Seu login: <b>$login</b>
<br />
Sua senha: <b>$senha</b>
<br /><br />
* Ao fazer seu login, você poderá alterar seus dados.
<br /><br />
Fazer login -> $url_admin
<br /><br />";

            $this->load->library('e_mail');
            // debug
            // echo '<pre>';
            // print_r($mensagem_html);
            // exit;
            $assunto = 'Lembrete de senha';
            $emailRem = $this->config->item('email1');
            $nomeRem = $this->config->item('title');


			$ret = $this->e_mail->envia($email, $row['nome'], $assunto, $menHTML, $menHTML, $emailRem, $nomeRem);
            if ($ret) {
                redirect('cms/login/index/r:sim-admin', 'refresh');
            } else {
                redirect('cms/login/index/r:nao-admin', 'refresh');
            }
        }
        // não passou
        else {
            redirect('cms/login/index/r:nao-admin', 'refresh');
        }
    }
}

?>