<?php

class Contato extends Frontend_Controller
{

    /**
     * @var
     */
    public $msg_type;
    /**
     * @var
     */
    public $msg;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('contato_m', 'contato');
    }

    public function index()
    {

        $this->message();

        //        $this->setNewScript(array('jquery.validate', 'jquery.maskedinput', 'form'));

        // breadcrumb

        $this->title = 'Contato';
        $this->corpo = $this->load->view('contato', '', true);

        $this->templateRender();

    }

    public function post_contato()
    {
        // -- carrega classes -- //
        $this->load->library(array('form_validation'));

        /*
         * Validação
         */
        $this->form_validation->set_rules('nome', 'Nome', 'trim|required');
        $this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email');
        $this->form_validation->set_rules('mensagem', 'Mensagem', 'trim|required|min_length[10]');
        $this->form_validation->set_rules('tel', 'Telefone', 'trim');

        $this->form_validation->set_rules('anexo', 'Anexo', 'callback_validate_attached');

        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');

        /*
         * Não validou
         */
        if ($this->form_validation->run() == false)
        {

            // salva erro na session
            $this->phpsess->save('msg_type', 'error');
            $this->phpsess->save('msg', 'Campos incorretos.');
            $this->index();
        }
        /*
         * OK, validou
         */
        else
        {

            $ret = $this->contato->envia_contato();

            if ($ret)
            {
                $this->phpsess->save('msg_type', 'success');
                $this->phpsess->save('msg', 'Mensagem enviada com sucesso.');
            }
            else
            {
                $this->phpsess->save('msg_type', 'error');
                $this->phpsess->save('msg', 'Erro ao enviar mensagem');
            }

            redirect('contato');
        }
    }

    /**
     * validate the attached file input
     * @param $value
     * @return bool
     */
    public function validate_attached($value)
    {
        if (strlen($_FILES['anexo']['name']) > 0)
        {

            $this->load->library('cms_arquivo');
            $this->cms_arquivo->max_size     = 2097152; // 1048576 = 1Mb
            $this->cms_arquivo->permited_ext = array('doc', 'docx', 'jpg', 'pdf');

            $val = $this->cms_arquivo->valida('anexo');

            if ($val !== true)
            {
                $this->form_validation->set_message('validate_attached', $val);

                return false;
            }

        }

        return true;

    }

    /**
     * set up sessions vars
     */
    private function message()
    {
        $this->msg_type = $this->phpsess->get('msg_type');
        $this->msg      = $this->phpsess->get('msg');
        $this->phpsess->save('msg_type', null);
        $this->phpsess->save('msg', null);

    }

}