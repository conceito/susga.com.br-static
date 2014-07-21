<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Classe que carregará todas as funções comuns do CMS, como links para deletar, mover, abrir janelas...
 * Relativos as peças da interface
 * Esta classe substituirá a library Opcoes.php do antigo CMS
 *
 * @version 1.0
 * @copyright 2009
 */

class Layout_cms {
    
    public $idioma = array(); // armazena dados do idioma ativo


    function Layout_cms()
    {
        $this->ci = &get_instance();
        // resgata variaveis do admin
        $this->lang = $this->ci->phpsess->get('lang', 'cms');
        $this->admin_id = $this->ci->phpsess->get('admin_id', 'cms');
        $this->admin_nick = $this->ci->phpsess->get('admin_nick', 'cms');
        $this->admin_mod = $this->ci->phpsess->get('admin_mod', 'cms');
        $this->admin_act = $this->ci->phpsess->get('admin_act', 'cms');
        $this->admin_tipo = $this->ci->phpsess->get('admin_tipo', 'cms');
    }

    /**
     * HEAD do CMS. nick, data, linguas
     * Também controla exibição de alertas durante execução
     *
     * @return
     */
    function head($_var = '', $var = array())
    {
        // captura possíveis variáveis enviadas pela URL ao controller
        // e transforma em array array('tip' => 'erro', 'op' => 'alguma')
        // se for vazio retorna FALSE
        $var2 = $this->ci->uri->dash_to_array($_var);

        // verifica se site está no AR ou manutenção e notifica
        $tmp['chave'] = $this->_chave_geral();
        
        // pega as mensagens não lidas
        $this->ci->load->model(array('cms/painel_model'));
//        $tmp['naoLidas'] = $this->ci->painel_model->mensagens_nao_lidas();
//        $tmp['lnkNaoLidas'] = ($tmp['naoLidas'] > 0) ? 't:0' : '';

        
        if (isset($var2['tip']) && strlen($var2['tip']) > 1)$tmp['tip'] = $var2['tip']; // alertas
        if (isset($var['tip']) && strlen($var['tip']) > 1)$tmp['tip'] = $var['tip']; // alertas
        $tmp['tab'] = (isset($var['tab'])) ? $var['tab'] : '';
        $tmp['nick'] = $this->admin_nick;
        $tmp['dataext'] = dataPorExtenso();        
        $tmp['langOpts'] = $this->_lang_opcoes();
        $tmp['lang'] = ($this->_lang_opcoes() == false) ? false : $this->idioma['titulo'];
        $tmp['nomeempresa'] = $this->ci->config->item('title');
        $tmp['linkSite'] = cms_url();
        $saida = $this->ci->load->view('cms/head', $tmp, true);
        return $saida;
    }
    function _chave_geral()
    {
        $this->ci->load->model(array('cms/admin_model'));
        $con = $this->ci->admin_model->config_dados();
        $chave = $con[5]['valor'];
        $redirecionamento = $con[6]['valor'];

        if ($chave == 1) { // no ar
            return false;
        } else if ($chave == 0 && strlen($redirecionamento) == 1) { // manutenção
            return 0;
        } else { // redirecionando...
            return $redirecionamento;
        }
    }


    /**
     * Monta o título da página com icone
     * @param mixed $title
     * @param string $css
     * @param array $modulo
     * @return string
     */
    function titulo($titulo, $this_dados, $css = 'novo', $modulo = NULL)
    {
        $uri = $this->ci->uri->segment_array();
                
        if(isset($uri[3]) && $uri[3] == 'duplicar'){
            $tmp['title'] = $titulo;
        } else {
            $tmp['title'] = (isset($this_dados['row']['titulo'])) ? $this_dados['row']['titulo'] : $titulo;
            
            // verifica se é revisão
            if(isset($this_dados['row']['tipo']) && strstr($this_dados['row']['tipo'], 'revision') !== FALSE){
                $tmp['title'] .= ' &rarr; Revisão #'.  revision_num($this_dados['row']['tipo']) .' '. datetime_br($this_dados['row']['atualizado']);
            }
        }
        
        
        $tmp['css'] = $css;
        $tmp['modulo'] = $modulo;
        
        if(isset($this_dados['row']['full_uri']) && $this_dados['row']['full_uri'] != ''){
            $tmp['front_uri'] = $this_dados['row']['full_uri'];
        } else {
            $tmp['front_uri'] = '';
        }
        
        
        $saida = $this->ci->load->view('cms/titulo', $tmp, true);
        return $saida;
    }
    

    /**
     * Monta o meu do CMS
     *
     * @return
     */
    function menu($var = array())
    {
        $this->ci->load->library('cms_libs');

        $tmp['id'] = $this->admin_id;
        $tmp['mod'] = $this->ci->cms_libs->str_to_array($this->admin_mod, '|');
        $tmp['act'] = $this->ci->cms_libs->str_to_array($this->admin_act, '|');
        $tmp['menus'] = $this->_carrega_menu(false, $var);
        
        $saida = $this->ci->load->view('cms/menu_cms', $tmp, true);
        return $saida;
    }

    function barra_botoes($lista)
    {
        /*
         * processa para verificar se o link é nativo deste controller, ou não
         */
        $controllerString = $this->ci->uri->segment(1).'/'.$this->ci->uri->segment(2).'/';

        $novaLista = array();
        foreach ($lista as $c=>$v){

            $partes = explode('/', $v);
            if($partes[0] == 'cms'){// reescreve o controller nativo
                $novaLista[$c] = $v;
            } else {
                $novaLista[$c] = $controllerString.$v;
            }

        }
        
        $tmp['act'] = $this->ci->cms_libs->gera_array_acoes();
        $tmp['item'] = $novaLista;
        $saida = $this->ci->load->view('cms/barra_botoes', $tmp, true);
        return $saida;
    }

    function barra_navegacao($vars, $ttl_rows, $desabilitar = array(), $onde = 'cms_conteudo')
    {
        
        // - captura as possíveis variaveis via POST - //
        $input_ativo = $this->ci->input->post('ativo');
        $input_dt1 = $this->ci->input->post('dt1');
        $input_dt2 = $this->ci->input->post('dt2');
        $input_busca = $this->ci->input->post('q');
        $input_grupos = ($this->ci->input->post('grupos') == 0)?'':$this->ci->input->post('grupos');
        // -
        $tmp['co'] = (isset($vars['co'])) ? $vars['co'] : '';
        $tmp['desabilitar'] = $desabilitar;
        $tmp['ttl_rows'] = $ttl_rows;
        $tmp['pp'] = $this->_opcoes_paginacao($vars['pp']);
        // valor do campo busca
        if (strlen(trim($input_busca)) > 0) {
            $b = trim($input_busca);
        } else {
            $b = $vars['b'];
        }
        $tmp['b'] = ($b == '') ? 'busca' : $b;
        // controla o estad os dos radios status
        $tmp['ativos'] = ($vars['stt'] === '1' OR $input_ativo === '1')?true:false;
        $tmp['inativos'] = ($vars['stt'] === '0' OR $input_ativo === '0')?true:false;
        $tmp['editando'] = ($vars['stt'] === '2' OR $input_ativo === '2')?true:false;
        // remove link para exportar conteúdo
        $tmp['export'] = (isset($desabilitar['export'])) ? false : true;
        // prenche o campo DT
        $tmp['dt1'] = (strlen($input_dt1) == 10) ? $input_dt1 : '';
        $tmp['dt2'] = (strlen($input_dt2) == 10) ? $input_dt2 : '';
        // se foi enviada alguma variavel de filtro mantem a aba aberta
        if (strlen($input_ativo) > 0 OR strlen($input_dt1) > 0 OR strlen($input_dt2) > 0 OR strlen($input_grupos) > 0 OR strlen($vars['stt']) > 0) {
            $tmp['aba_avancada_aberta'] = true;
        } else {
            $tmp['aba_avancada_aberta'] = false;
        }
        
        // monta combo de grupos
        $co = (isset($vars['co'])) ? $vars['co'] : '';
        $tmp['combogrupos'] = $this->ci->cms_libs->combo_grupos($co, '', false, $desabilitar, $onde);
        // echo '<pre>';
        // var_dump($tmp['dt1']);
        // exit;
        // -

        $saida = $this->ci->load->view('cms/barra_navegacao2', $tmp, true);
        return $saida;
    }

    function menu_modal($botoes = array())
    {
        $tmp['menu'] = $botoes;
        $saida = $this->ci->load->view('cms/modal_menu', $tmp, true);
        return $saida;
    }

    function modal_resposta($var, $_var)
    {
        $tip = $var['tip'];
        $var2 = $this->ci->uri->dash_to_array($_var);
        if ($tip != '') {
            if ($tip == 'ok')$html = '<div class="resposta-ok">OK, os arquivos subiram com sucesso!</div>';
            if ($tip == 'erro')$html = '<div class="resposta-erro">Houve um erro ao subir arquivos.<br />' . $var['erro'] . '</div>';
            if ($tip == 'opok')$html = '<div class="resposta-ok">OK, o efeito foi aplicado com sucesso!</div>';
            if ($tip == 'faltaCampos')$html = '<div class="resposta-erro">Você deve preencher todos os campos.</div>';
            if ($tip == 'envioOk')$html = '<div class="resposta-ok">OK, mensagem enviada com sucesso!</div>';
            if ($tip == 'envioErro')$html = '<div class="resposta-erro">Ops! Houve um erro ao enviar mensagem.</div>';
        } else {
            $html = '';
        }
        // mostra de thumbs id-id-id
        if (strlen($var['imgs']) > 0 && $tip == 'ok') {
            // $html .= $var['imgs'];
            $dados['listaImgs'] = $this->_dados_arquivos($var['imgs']);
            $html .= $this->ci->load->view('cms/uploads/thumb_uploaded', $dados, true);
        }
        // mostra de thumbs id-id-id
        if (strlen($var['arqs']) > 0 && $tip == 'ok') {
            $dados['listaArqs'] = $this->_dados_arquivos($var['arqs']);
            $html .= $this->ci->load->view('cms/uploads/arqvs_uploaded', $dados, true);
        }

        return $html;
    }
    // //////////////////////////////////////////////////////////////////////////////
    // ///// 	FUNÇÕES PRIVADAS 	////////////////////////////////////////////////
    function _carrega_menu($soraiz, $vars = array())
    {
        $saida = array();
        // -- pegar os menus principais RAIZ
        $this->ci->db->where('grupo', 0);
        $this->ci->db->where('status', 1);
        if ($this->admin_tipo == 1) {
            $this->ci->db->where('tipo', 1);
        } // para superadmin vê tudos menos tipo ==0
        else if ($this->admin_tipo == 2) { // para Segmentados escolhe os módulos
            $this->ci->db->where('id', 0); // hack do AR
            $listMods = explode('|', $this->admin_mod);
            foreach($listMods as $idmod) {
                $this->ci->db->or_where('id', $idmod);
            }
        }
        $this->ci->db->order_by('ordem');
        $sql = $this->ci->db->get('cms_modulos');
        // percorre os manus de raiz
        foreach($sql->result_array() as $raiz) {
            // dados dos menus
            $label = $raiz['label'];
            $id = $raiz['id'];
            $uri = $raiz['uri'];
            $tabela = $raiz['tabela'];
            
            // destaca menu ativo
            $controller = $this->ci->uri->segment(2);
            
            // primeiro as exceções
            $ativ = 0;
            // administração
            if($controller == 'administracao' && $id == 1){
                $ativ = 1;
            } 
            // ger. de arquivos
            else if($controller == 'pastas' && ($vars['co'] == 0 || $vars['co'] == 2) && $id == 4){
                $ativ = 1;
            } 
            // albuns de fotos
            else if($controller == 'pastas' && $vars['co'] == 1 && $id == 13){
                $ativ = 1;
            } else if($controller != 'pastas' && $controller != 'administracao'){
                if (isset($vars['co']) && $vars['co'] == $id) {
                    $ativ = 1;
                }
            }
            
            
            // echo '<pre>';
            // var_dump($vars['co']);
            // exit;
            $item0 = array('label' => $label,
                'id' => $id,
                'uri' => $uri,
                'ativo' => $ativ,
                'tabela' => $tabela);
            // se permitir
            if (! $soraiz) {
                // percorre os submenus
                $submenus = array(); // init
                $this->ci->db->where('status', 1);
                $this->ci->db->where('grupo', $id);
                if ($this->admin_tipo > 0) {
                    $this->ci->db->where('tipo', 1);
                } // para superadmin vê tudos menos tipo ==0
                $this->ci->db->order_by('ordem');
                $sql = $this->ci->db->get('cms_modulos');
                // percorre o segundo nível e ponta array
                foreach($sql->result_array() as $sub) {
                    // dados dos menus
                    $label2 = $sub['label'];
                    $uri = $sub['uri'];
                    $submenus[] = array('label' => $label2, 'uri' => $uri);
                }
            } else {
                $submenus = false;
            }
            // coloca no array
            $saida[] = array('raiz' => $item0, 'submenus' => $submenus);
        }

        return $saida;
    }
    

    /**
     * Monta as opções do combobox dos offsets de paginação
     *
     * @param mixed $pp
     * @return
     */
    function _opcoes_paginacao($pp)
    {
        $nums = $this->ci->config->item('pagination_limits');
        $saida = '';
        foreach($nums as $n) {
            if ($pp == $n) $c = 'selected';
            else $c = '';
            $saida .= '<option value="' . $n . '" ' . $c . '>' . $n . '</option>';
        }
        // echo '<pre>';
        // var_dump($saida);
        // exit;
        return $saida;
    }

    /**
     * Retorna array com dados das imagens
     *
     * @param mixed $str
     * @return
     */
    function _dados_arquivos($str)
    {
        if (strlen(trim($str)) == 0) return false;
        $this->ci->load->library('cms_libs');
        // prepara array
        $lista = explode('-', $str);
        $saida = array();
        // poercorre
        foreach($lista as $id) {
            if ($id != '') {
                $dados = $this->ci->cms_libs->arquivo_dados($id);
                $saida[] = array('id' => $id,
                    'nome' => $dados['nome'],
                    'pos' => $dados['pos'],
                    'ext' => $dados['ext'],
                    'peso' => $dados['peso']

                    );
            }
        }
        return $saida;
    }

    
    /**
     * Retorna os nós LI com opções de idioma
     *
     * @return
     */
    function _lang_opcoes()
    {
        $this->ci->db->where('grupo', 1); // lingua
        $this->ci->db->order_by('ordem');
        $this->ci->db->where('status', 1);
        $sql = $this->ci->db->get('cms_combobox');
        if ($sql->num_rows() < 2)return false;
        // percorre opções
        $saida = '';
        foreach($sql->result_array() as $row) {
            $uri = str_replace('/', '_', uri_string());
            $link = cms_url('cms/cmsutils/mudaLang/' . $row['valor'] . '/' . $uri);
            if ($row['valor'] == $this->lang) {
                $saida .= '<li>' . $row['titulo'] . '</li>';
                $this->idioma = $row;
            } else {
                $saida .= '<li><a href="' . $link . '" title="' . $row['titulo'] . '">' . $row['titulo'] . '</a></li>';
            }
        }

        return $saida;
    }
}

?>