<?php
/**
 * Controla a seção, e dados do usuário logado.
 * Envio de senha.
 * Tipos de admin:
 *    $tipo = '0' // GOD
 *    $tipo = '1' // superadmin
 *    $tipo = '2' // admin
 *    $tipo = '3' // usuário
 *
 * @package Model
 * @author Bruno Barros
 * @copyright Copyright (c) 2009
 * @version 2.0
 * @access public
 **/
class Sessao_model extends CI_Model {

    function  __construct()
    {
        parent::__construct();
    }


    /**
     * Verifica se está logado e retorna TRUE ou FALSE.
     *
     * @return bool
     **/
    function esta_logado()
    {
        if ($this->phpsess->get('logado', 'cms') == true) {
            $logado = true;
        } else {
            $logado = false;
        }
        return $logado;
    }


    /**
     * Valida o admin.
     * Popula a SESSION com os dados do usuário.
     *
     * @param $_POST['seulogin']
     * @param $_POST['suasenha']
     * @return bool
     **/
    function valida_usuario($user = NULL, $pass = NULL)
    {
        $l = ($user === NULL) ? $this->input->post('adminlogin') : $user;        
        $s =  ($pass === NULL) ? md5($this->input->post('adminsenha')) : $pass;
        $salvar_cookie = $this->input->post('salvar_cookie');

        $this->db->where('login', $l);
        $this->db->where('senha', $s);
        $this->db->where('status', 1);
        $this->db->limit(1);
        $sql = $this->db->get('cms_admin');

        if ($sql->num_rows() == 1) {
            // Destroy old session
            $this->phpsess->clear(NULL, 'cms');

            $reg = $sql->row_array();

            $this->phpsess->save('logado', true, 'cms');
            $this->phpsess->save('lang', 'pt', 'cms'); // default
            $this->phpsess->save('admin_id', $reg['id'], 'cms');
            $this->phpsess->save('admin_tipo', $reg['tipo'], 'cms');
            $this->phpsess->save('admin_nick', $reg['nick'], 'cms');
            $this->phpsess->save('admin_mod', $reg['mod'], 'cms');
            $this->phpsess->save('admin_act', $reg['acoes'], 'cms');

            // já que logou salva os dados deste login
            $dados['ultima_dt'] = date("Y-m-d");
            $dados['visitas'] = $reg['visitas'] + 1;
            $this->db->where('id', $reg['id']);
            $this->db->update('cms_admin', $dados);
            
            // Se for salvar em cookie
            
            if($salvar_cookie == 1){
                
                $this->input->set_cookie(array(
                    'name'   => 'cmsadminlogin',
                    'value'  => 'logado=1,admin_id='.$reg['id'],
                    'expire' => time()+2629743,// uma mês 
                    'path'   => '/',
                    'domain' => '',
                    'prefix' => '',
                    'secure' => false
                ));
               
            }

            return TRUE;
            
        } else {
            $this->phpsess->save('logado', false, 'cms');
            
            // remove cookie se existir
            delete_cookie('cmsadminlogin');
            
            return FALSE;
        }
    }
    
    // -------------------------------------------------------------------------
    /**
     * Pega dados no cookie, valida sessão do admin, redireciona para pagina
     */
    public function do_cookie_login(){
        
        $cook = $this->input->cookie('cmsadminlogin');
        
        if($cook){
            $sep = explode(',', $cook);            
            
            if(trim($sep[0]) == 'logado=1'){
                $admin = explode('=', $sep[1]);
                $admin_id = $admin[1];
                
                $this->db->where('id', $admin_id);
                $this->db->where('status', 1);
                $this->db->limit(1);
                $sql = $this->db->get('cms_admin');
                
                // se não existir, possivelmente foi desativado
                if($sql->num_rows() == 0){
                    redirect('cms/login/index', 'refresh');
                }
                
                $admin = $sql->row_array();
                
                $ret = $this->valida_usuario($admin['login'], $admin['senha']);
                
                if($ret){
                    redirect(current_url());
                } else {
                    redirect('cms/login/index', 'refresh');
                }
                
                
            } else {
                redirect('cms/login/index', 'refresh');
            }
            
        } else {
            redirect('cms/login/index', 'refresh');
        }
        
    }


    // -------------------------------------------------------------------------
    /**
     * Verifica se existe cookie ativo e faz cookie login
     */
    public function check_cookie_login(){
        
        $cook = $this->input->cookie('cmsadminlogin');
        $already = $this->esta_logado();
        
        if($cook && !$already){
            $sep = explode(',', $cook);            
            
            if(trim($sep[0]) == 'logado=1'){                
                $this->do_cookie_login();
            }
        }
    }

    /**
     * Pega as informações do usuário admin e retorna um array associativo
     *
     * @param integer $id
     * @return array
     **/
    function user_infos($id = '')
    {
        if ($id == '') return false;

        if ($id == 0) return array('nome' => 'administrador');

        $this->db->where('id', $id);
        $sql = $this->db->get('cms_admin');
        return $sql->row_array();
    }


    /**
     * Pesquisa e retorna o tipo em forma de string.
     *
     * @param string $tipo
     * @return string
     **/
    function tipo_user($tipo = '')
    {
        if ($tipo == '') {
            $i = $this->phpsess->get('admin_tipo', 'cms');
        } else {
            $i = $tipo;
        }
        return $this->array_tipo_user[$i];
    }


    /**
     * Verifica a variavel na session. Se não for TRUE redireciona para login
     *
     * @return bool
     **/
    function controle_de_sessao()
    {
        // verifica na session se está logado
        $logado = $this->phpsess->get('logado', 'cms');
        // verifica cookie
        $cook = $this->input->cookie('cmsadminlogin');
        
        // redireciona
        if (!$logado && !$cook) {
            redirect('cms/login/index', 'refresh');
        } else if(!$logado && $cook){
            $this->do_cookie_login();
        }
        return $logado;
    }






    /**
     * Monta combo box dos tipos de dmins.
	 * Só mostra os tipos subordinados.
     *
     * @param integer $id
     * @return bool
     **/
    function combo_admins($id = '')
    {
        $selected = array($id);

        $indice_inicio = $this->phpsess->get('admin_tipo', 'cms');
        $users = array();
        for($x = 0; $x < count($this->array_tipo_user); $x++) {
            if ($x >= $indice_inicio) {
                $users[$x] = $this->array_tipo_user[$x];
            }
        }

        $this->load->helper('form');
        $drop = form_dropdown('tipo', $users, $selected, 'class="texto" id="tipo"');
        return $drop;
    }
}

?>