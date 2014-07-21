<?php

/**
 * Opções de utilização da newsletter.
 *
 * @package Controller
 * @author Bruno Barros
 * @copyright Copyright (c) 2009
 * @version 2.0
 * @access public
 * */
class Newsopc extends Controller {

    function Newsopc() {
        parent::Controller();
        $this->load->library(array('cms_libs'));
        $this->load->model(array('cms/news_model', 'cms/admin_model'));
    }

    /**
     * Abre HTML com a mensagem.
     * Carrega Model: news/mensagem_model.
     *
     * @param integer $id_men
     * @return view
     * */
    function index($id_user = '', $id_mens = '') {
        if ($id_mens == '')
            return 'ID inesistente.';


        $dadosPag['row'] = $this->news_model->conteudo_dados($id_mens);

        // adiciona estatística
        $dados['mens_id'] = $id_mens;
        $dados['user_id'] = $id_user;
        $dados['data'] = date("Y-m-d");
        $dados['hora'] = date("H:i:s");
        $dados['acao'] = 1;
        $dados['link'] = 0;
        $this->db->insert('cms_news_stats', $dados);



        // view
        $this->load->view('cms/news/ver', $dadosPag);
    }

    /**
     * Abre form de descadastramento na newsletter.
     * Carrega Library: form_validation.
     *
     * @param integer $id_user
     * @param string $motivo
     * @return view
     * */
    function remover($id_user = '', $id_mens = '', $motivo = '') {
        // if($id_user == '') return 'ID inesistente.';
        $this->load->library('form_validation');

        $dadosPag['motivo'] = $motivo;
        $dadosPag['id_user'] = $id_user;
        $dadosPag['id_mens'] = $id_mens;

        $this->load->view('cms/news/remove_usuario', $dadosPag);
    }

    /**
     * Efetua o descadastramento na newsletter.
     * Carrega Helper: form.
     * Carrega Library: form_validation.
     * Carrega Model: cms/usuario_model.
     * Redireciona para Newsopc::remover().
     *
     * @param integer $id_user
     * */
    function descadastra() {
        $this->load->helper(array('form'));
        $this->load->library('form_validation');

        $id_user = $this->input->post('id_user');
        $id_mens = $this->input->post('id_mens');

//        var_dump($_POST);
//        exit;

        $this->form_validation->set_rules('seuemail', 'Seu e-mail', 'trim|required|valid_email');

        $this->form_validation->set_error_delimiters('<div class="erros">', '</div>');
        // NÃO validou
        if ($this->form_validation->run() == false) {
            $this->remover($id_user, '');
        }
        // valido
        else {

            $this->load->model('cms/usuarios_model');
            $ret = $this->usuarios_model->usuario_descadastra();

            if ($ret) {
                

                

                $this->remover($id_user, '', 'E-mail removido com sucesso!');

            } else {
                $this->remover($id_user,'', 'Erro ao descadastrar!');
            }
        }
    }

}

?>