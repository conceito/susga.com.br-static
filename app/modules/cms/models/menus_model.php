<?php
class Menus_model extends CI_Model {
    
    public $moduloMenus;
    public $modulosNaoPermitidos = array(29);

    function __construct()
    {
        parent::__construct();
        $this->moduloMenus = 37;
    }

     /**
    * Lista os conteudos dos grupos e conteudos
    *
    * @param mixed $v array com dados via URI
    * @param string $tipo grupo ou conteudo
    * @param array $modulo array dados do módulo
    * @return
    */
    function lista_conteudos($v, $tipo = 'conteudo', $modulo = array())
    {
        // -- trata as variaveis --//
        $pps = $this->config->item('pagination_limits');        
        $pp = ($v['pp'] == '') ? $pps[0] : $v['pp']; // por página
                
        $pag = $this->uri->to_array('pag');
        if($pag['pag'] == ''){
            $offset = 0;
        } else {
            $offset = ($pag['pag']-1) * $pp;
        }
        
        $b = $v['b'];
        // se foi feita uma busca
        if (strlen(trim($this->input->post('q'))) > 0) {
            $b = $this->cms_libs->limpa_caracteres(trim($this->input->post('q')));
            $b = ($b == 'busca') ? '' : $b; // prevenir contra falsa busca
            $offset = 0;
        }
        // se foi feita bsca avançada ------------------------
        if (strlen(trim($this->input->post('ativo'))) > 0) {
            $stt = $this->input->post('ativo');
            $offset = 0;
        } else {
            $stt = $v['stt'];
        }
        // se foi feita seleção com grupos
        if (strlen(trim($this->input->post('grupos'))) > 0) {
            $g = $this->input->post('grupos');
        } else {
            $g = ($v['g'] == '') ? 0 : $v['g'];
        }
        // pelas datas
        if (strlen(trim($this->input->post('dt1'))) > 0)$dt1 = formaSQL($this->input->post('dt1'));
        else $dt1 = $v['dt1'];
        if (strlen(trim($this->input->post('dt2'))) > 0)$dt2 = formaSQL($this->input->post('dt2'));
        else $dt2 = $v['dt2'];
        // echo '<pre>';
        // var_dump($v['co']);
        // exit;
        // -- SQL básica com paginação -- //
        if ($dt1 != '' && $dt2 == ''){$this->db->where('dt_ini', $dt1);}
        else if ($dt1 != '' && $dt2 != '') {
            $this->db->where('dt_ini >=', $dt1);
            $this->db->where('dt_ini <=', $dt2);
        }
        if ($stt != '')$this->db->where('status', $stt);
        if ($b != ''){
			$this->db->like('titulo', $b);$this->db->or_like('resumo', $b);
		}
        $this->db->limit($pp, $offset);
        // ordenação
        if (isset($modulo['ordenavel']) && $modulo['ordenavel'])$this->db->order_by('ordem');
        else $this->db->order_by('dt_ini', 'titulo');
        $this->db->where('modulo_id', $v['co']);
        if ($tipo == 'grupo') {
            $this->db->where('grupo', 0); // busca grupos
        } else {
            if ($g == 0)$this->db->where('grupo !=', 0); // todos conteudos
            else $this->db->where('grupo', $g); // conteudos do grupo
        }
        $this->db->where('lang', get_lang());

        $sql = $this->db->get('cms_conteudo');
        // -- pega o Total de registros --------------------------------------------------- //
        // -- pega o Total de registros -- //
        if ($dt1 != '' && $dt2 == ''){$this->db->where('dt_ini', $dt1);}
        else if ($dt1 != '' && $dt2 != '') {
            $this->db->where('dt_ini >=', $dt1);
            $this->db->where('dt_ini <=', $dt2);
        }
        if ($stt != '')$this->db->where('status', $stt);
        if ($b != ''){
			$this->db->like('titulo', $b);$this->db->or_like('resumo', $b);
		}
        $this->db->where('modulo_id', $v['co']);
        if ($tipo == 'grupo') {
            $this->db->where('grupo', 0); // busca grupos
        } else {
            if ($g == 0)$this->db->where('grupo !=', 0); // todos conteudos
            else $this->db->where('grupo', $g); // conteudos do grupo
        }
        $this->db->where('lang', get_lang());
        $sql_ttl = $this->db->get('cms_conteudo');
        $ttl_rows = $sql_ttl->num_rows();
        // paginação
        $this->load->library('pagination');
//        $config['base_url'] = cms_url('cms/menus/index/co:' . $v['co'] . '/pp:' . $pp . '/g:' . $g . '/dt1:' . $v['dt1'] . '/dt2:' . $v['dt2'] . '/b:' . $b . '/stt:' . $stt . '/');
//        $config['total_rows'] = $ttl_rows;
//        $config['per_page'] = $pp;
//        $config['uri_segment'] = 11; // segmentos + 1
//        $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
//        $config['num_tag_open'] = '<span class="pagnation_number">';
//        $config['num_tag_close'] = '</span>';
//        $config['cur_tag_open'] = '<span class="pagnation_current">';
//        $config['cur_tag_close'] = '</span>';
//        $this->pagination->initialize($config);
       
        $saida = array('ttl_rows' => $ttl_rows,
            'rows' => $this->parse_lista_conteudos($sql->result_array(), $modulo));

        return $saida;
    }

    /**
     * Lista os itens do menu. Faz busca recurssiva em cada um e acrescenta no array
     * colocando o nível de profundidade: 0, 1, 2
     * @param array $vars
     * @return array
     */
    function getMenusDados($vars){

        $this->db->where('modulo_id', $vars['co']);
        $this->db->where('grupo', $vars['id']); 
        $this->db->where('rel', 0);// nivel 0
        $this->db->where('lang', get_lang());
        $this->db->order_by('ordem');
        $this->db->select('id, nick, titulo, ordem, rel, resumo, txt, tags');
        $sql = $this->db->get('cms_conteudo');

        

        // variavel incrementada pelas ecurssões
        $this->menusLista = array();


        if($sql->num_rows()){

            // percorre as páginas de nível 0 e insere os níveis
            foreach($sql->result_array() as $row){

                $row['nivel'] = 0;
                $this->menusLista[] = $row;

                $this->getMenusRecurssivos($vars, $row, 0);
            }

        } else {
            return false;
        }

        return $this->menusLista;

//            mybug($this->menusLista);
    }
    
    function getMenusRecurssivos($menuDados, $menuPai, $nivel = 0){
        
        $this->db->where('modulo_id', $menuDados['co']);
        $this->db->where('grupo', $menuDados['id']);
        $this->db->where('rel', $menuPai['id']);// nivel 0
        $this->db->where('lang', get_lang());
        $this->db->order_by('ordem');
        $this->db->select('id, nick, titulo, ordem, rel, resumo, txt, tags');
        $sql = $this->db->get('cms_conteudo');
        
        if($sql->num_rows()){
            $nivel++;
            // percorre as páginas de nível 0 e insere os níveis
            foreach($sql->result_array() as $row){

                $row['nivel'] = $nivel;
                $this->menusLista[] = $row;

                $this->getMenusRecurssivos($menuDados, $row, $nivel);
                
            }

        } else {
            return false;
        }
        
    }


    /**
    * Prepara pesquisa de item de menu
    *
    * @param mixed $array
    * @return
    */
    function parse_lista_conteudos($array, $modulo = array())
    {
        if (count($array) == 0) return false;
        // percorre array
        $saida = array();
        foreach($array as $row) {
            if ($row['status'] == 1)$row['status'] = 'ativo';
            else if ($row['status'] == 0)$row['status'] = 'inativo';
            else if ($row['status'] == 2)$row['status'] = 'editando';

            if ($row['grupo'] == 0) {
                
                $row['grupo'] = 'Grupo';
                $row['ttlItens'] =  $this->getNumItensMenu($row['id']);

            } else {
                $this->db->where('id', $row['grupo']);
                $this->db->select('titulo, tags');
                $sql = $this->db->get('cms_conteudo');
                if($sql->num_rows()>0){
	                $item = $sql->row_array();
	                $row['grupo'] = $item['titulo'];
	                $cores = (strlen($item['tags']) > 6) ? explode('|', $item['tags']) : array("", "");
	                $row['grupoCor1'] = $cores[0];
	                $row['grupoCor2'] = $cores[1];
				} else {
					$row['grupo'] = 'desconhecido';
					$row['grupoCor1'] = '';
	                $row['grupoCor2'] = '';
				}


            }
            
            // coloca no array
            // $saida[] = array('id' => $row['id'],
            // 'titulo' => $row['titulo'],
            // 'tipo' => $tipo,
            // 'status' => $att);
            $saida[] = $row;
        }
//        mybug($saida);
        return $saida;
    }

    /**
     * 
     * @param <type> $var
     * @return <type>
     */
     function conteudo_salva($var)
    {
        // - salva os dados do menu principal Raiz
        $grupo = 0;
        $rel = 0;
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $data = trim($this->input->post('dt1'));
        $status = $this->input->post('status');
        $resumo = trim($this->input->post('resumo'));
        $tags = '';
        $txt = '';

        $dados['titulo'] = $titulo;
        $dados['resumo'] = $resumo;

        $dados['grupo'] = $grupo;
        $dados['modulo_id'] = $var['co'];
        $dados['tags'] = $tags;
        $dados['status'] = $status;
        $dados['txt'] = campo_texto_utf8($txt);
        $dados['rel'] = ($rel == '') ? 0 : $rel;

//        mybug($dados);

        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['hr_ini'] = date("H:i:s");
            $dados['lang'] = get_lang();
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['dt_ini'] = date("Y-m-d");

            $sql = $this->db->insert('cms_conteudo', $dados);
            $esteid = $this->db->insert_id();
            // -- >> LOG << -- //
            $oque = "Novo Menu: <a href=\"" . cms_url('cms/menus/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {
            $dados['dt_ini'] = formaSQL($data);

            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Menu: <a href=\"" . cms_url('cms/menus/edita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
            $esteid = $var['id'];
        }

        return $esteid;
    }

    /**
     * Salva os menus principais
     * @param <type> $var
     * @return <type>
     */
    function grupo_salva($var)
    {
        // - salva os dados do menu principal Raiz
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $resumo = trim($this->input->post('resumo'));
        $cor1 = trim($this->input->post('cor1'));
        $cor2 = trim($this->input->post('cor2'));

        $dados['titulo'] = $titulo;
        $dados['resumo'] = $resumo;
        $dados['img'] = $cor1 . '|' . $cor2;
      
        
        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['dt_ini'] = date("Y-m-d");
            $dados['hr_ini'] = date("H:i:s");
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados);
            $dados['grupo'] = 0;
            $dados['lang'] = get_lang();
            $dados['status'] = 1;
            $dados['modulo_id'] = $var['co'];

            $sql = $this->db->insert('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Novo Menu: <a href=\"" . cms_url('cms/menus/edita/co:' . $var['co'] . '/id:' . $this->db->insert_id()) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {
            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_conteudo', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Menu: <a href=\"" . cms_url('cms/menus/edita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }

        return $sql;
    }


    /**
     * retorna um array com dados principais dos conteúdo
     * preenche o combo box para inserção no menu
     * @param <type> $modulo_id
     */
    function getConteudosFromModulo($modulo_id, $modulo_tabela){

        $this->db->where('modulo_id', $modulo_id);
        $this->db->where('lang', get_lang());
        $this->db->where('status', 1);
        $this->db->order_by('dt_ini desc');
        $this->db->where('grupo !=', 0);
        $this->db->select('id, nick, titulo');
        $this->db->limit(100); // limita para não travar
        $sql = $this->db->get($modulo_tabela);

        if($sql->num_rows() == 0){
            return FALSE;
        }
        
        return $sql->result_array();


    }

    /**
     * Insere um item vazio no menu.
     * @param int $grupo_id
     * @return array
     */
    function insereItemBlankDeMenu($grupo_id){

        //
        //pega a quantidade de itens neste menu para continuar a contagem de ordenação

        $total = $this->getNumItensMenu($grupo_id);
        //
        // percorre ids e salva no BD, retornando os dados dos itens de menu
        $novosIds = array();



            $dados['titulo'] = '- vazio - ';
            $dados['nick'] = 'http://www.';
            $dados['rel'] = 0;// nivel 0 = raiz
//            $dados['visitas'] = 0;
            $dados['txt'] = '- vazio -';
            $dados['ordem'] = $total++;

            $dados['modulo_id'] = $this->moduloMenus;
            $dados['grupo'] = $grupo_id;
            $dados['dt_ini'] = date("Y-m-d");
            $dados['status'] = 0;// item vazio inicia inativo
            $dados['lang'] = get_lang();
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');

            $this->db->insert('cms_conteudo', $dados);
            $novosIds[] = array(
                'id' => $this->db->insert_id(),
                'rotulo' => '- vazio - ',
                'url' => ''
            );



        return $novosIds;

    }

    /**
     * Insere via AJAX os itens de menu
     * Retorna os dados dos itens inseridos para montagem do frontend
     *
     * *** linhas da tabela cms_conteudo usadas para os menus
     * "titulo" => rotulo do menu
     * "nick"   => url do conteudo
     * "rel"    => ID do item de menu pai
     * "visitas"=> ID do conteudo
     * "txt"    => title
     * "resumo" => CSS
     * "tags"   => target
     * @param string $conteudo_id
     * @return array
     */
    function insereItensDeMenu($conteudo_id, $grupo_id){

        // remove hifen do final
        $ids = trim($conteudo_id, '-');
        // explode array
        $ids = explode('-', $ids);
        //
        //pega a quantidade de itens neste menu para continuar a contagem de ordenação
        
        $total = $this->getNumItensMenu($grupo_id);
        //
        // percorre ids e salva no BD, retornando os dados dos itens de menu
        $novosIds = array();

        foreach($ids as $id){

            // pesquisa dados do conteúdo
            $dd = $this->cms_libs->conteudo_dados($id);


            $dados['titulo'] = $dd['titulo'];
            $dados['nick'] = $dd['nick'];
            $dados['rel'] = 0;// nivel 0 = raiz
//            $dados['visitas'] = $dd['id'];
            $dados['txt'] = $dd['titulo'];
            $dados['ordem'] = $total++;

            $dados['modulo_id'] = $this->moduloMenus;
            $dados['grupo'] = $grupo_id;
            $dados['dt_ini'] = date("Y-m-d");
            $dados['status'] = 1;
            $dados['lang'] = get_lang();
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');

            $this->db->insert('cms_conteudo', $dados);
            $novosIds[] = array(
                'id' => $this->db->insert_id(),
                'rotulo' => $dd['titulo'],
                'url' => $dd['nick']
            );

        }

        return $novosIds;
        

    }

    /**
     * Salva dados do item de menu em uma atualização.
     * @return boolean
     */
    function salvaDadosItemMenu(){
        $id = $this->input->post('id');
        $url = $this->input->post('url');
        $rotulo = $this->input->post('rotulo');
        $title = $this->input->post('title');
        $css = $this->input->post('css');
        $target = $this->input->post('target');

//        * "titulo" => rotulo do menu
//     * "nick"   => url do conteudo
//     * "rel"    => ID do item de menu pai
//     * "visitas"=> ID do conteudo
//     * "txt"    => title
//     * "resumo" => CSS
//     * "tags"   => target

        $dados['titulo'] = $rotulo;
        $dados['nick'] = $url;
        $dados['txt'] = $title;
        $dados['resumo'] = $css;
        $dados['tags'] = $target;
        $dados['status'] = 1; // garante que estará ativo

        $this->db->where('id', $id);
        $ret = $this->db->update('cms_conteudo', $dados);

        return $ret;

    }

    /**
     * Retorna a quantidade de itens de menu
     * @param int $grupo_id
     * @return int
     */
    function getNumItensMenu($grupo_id){

        //pega a quantidade de itens neste menu para continuar a contagem de ordenação
        $this->db->where('modulo_id', $this->moduloMenus);
        $this->db->where('grupo', $grupo_id);
        $this->db->select('id');
        $this->db->where('lang', get_lang());
        $sql = $this->db->get('cms_conteudo');
        $total = $sql->num_rows();

        return $total;

    }

    /**
     * Template da estrutura HTML de cada item do menu.
     * @param <type> $dados
     * @return string
     */
    function modeloItemMenu($dados){

        $saida = '';
        $saida .= '<li id="'.$dados['id'].'" class="menu-depth-0">
            <div class="title drag">'.$dados['rotulo'].'</div>
            <a href="#" class="options" title="detalhes">+</a>

            	<!-- dados do item de menu -->
                <div class="menu-dados menu-item-2">

                    <label class="lb-menu">URL:</label>
                    <input name="url" type="text" value="'.$dados['url'].'" class="input-menu" />

                    <div class="item-info-metade">
                    <label class="lb-menu">Rótulo:</label>
                    <input name="rotulo" type="text" value="'.$dados['rotulo'].'" class="input-menu" />
                    </div>

                    <div class="item-info-metade">
                    <label class="lb-menu">Title:</label>
                    <input name="title" type="text" value="'.$dados['rotulo'].'" class="input-menu" />
                    </div>

                    <div class="item-info-metade">
                    <label class="lb-menu">CSS:</label>
                    <input name="css" type="text" value="" class="input-menu" />
                    </div>

                    <div class="item-info-metade">
                    <label class="lb-menu">Target:</label>

                    <select name="target" class="input-menu">
                    	<option value="">nenhum</option>
                      <option value="_blank">_blank</option>
                      <option value="_parent">_parent</option>
                      <option value="_self">_self</option>
                      <option value="_top">_top</option>
                    </select>
                    </div>



                    <div class="md-options"><a href="#" class="opt-remover">&raquo; remover do menu</a> | <a href="#" class="opt-atualizar">&raquo; atualizar dados</a>
                    <img src="'.base_url().'ci_itens/img/Jcrop.gif" width="8" height="8" alt="loading" class="loading" /></div>

                </div><!-- .menu-dados -->

            </li>';

        return $saida;

    }

    /**
     * Gera as OPTION do combobox TARGET dos menus
     * @param <type> $value
     * @return string
     */
    function comboTarget($value = ''){

        $matriz = array('' => 'nenhum', '_blank' => '_blank', '_parent' => '_parent', '_self' => '_self', '_top' => '_top');
        $saida = '';
        foreach($matriz as $c => $v){

            $selected = '';
            if($c == $value){
                $selected = 'selected="selected"';
            }

            $saida .= '<option value="'.$c.'" '.$selected.'>'.$v.'</option>';

        }

        return $saida;

    }

    /**
     * Retorna os dados dos módulos permitidos para serem usados no menu
     * @return array
     */
    function getModulosToMenu(){

        
        foreach($this->modulosNaoPermitidos as $c=>$id){
            $this->db->or_where('id !=', $id);
            
        }
        $this->db->where('tipo', 1);
        $this->db->where('grupo', 0);
        $this->db->where('tabela', 'cms_conteudo');
        $this->db->where('status', 1);
        $this->db->order_by('ordem');
        $this->db->select('id, label');
        $sql = $this->db->get('cms_modulos');
//mybug($sql->result_array());
        return $sql->result_array();

    }

    /**
     * Monta combobox com os módulos que contribuem para o menu.
     * Ver no topo da classe.
     * @return string
     */
    function comboModulosToMenu(){

        $itens = array('' => '-- ninguém --');

        foreach($this->getModulosToMenu() as $item){

            $itens[$item['id']] = $item['label'];

        }
        
        $cb = $this->cms_libs->cb('', $itens, 'modulos', false, 'input-combo');
//        mybug($cb);
        return $cb;

    }

    /**
     * Psquisa pelos conteúdos de acordo com a 'query' enviada e retorna um array
     * 
     * @param string $q
     * @param int $grupo_id
     * @param string $modulo_tabela
     * @return array
     */
    function getResultsPesquisa($q, $grupo_id, $modulo_tabela = 'cms_conteudo'){


        $this->db->like('cms_conteudo.titulo', $q);
        $this->db->or_like('cms_conteudo.resumo', $q);
        $this->db->where('cms_conteudo.status', 1);
        $this->db->where('cms_conteudo.grupo !=', 0);
        $this->db->where('cms_conteudo.modulo_id !=', $this->moduloMenus);
        $this->db->limit(6); // limita para não travar
        $this->db->order_by('cms_conteudo.titulo');
        $this->db->select('cms_conteudo.id, cms_conteudo.nick, cms_conteudo.titulo, cms_conteudo.modulo_id, cms_conteudo.resumo, cms_modulos.label');
        $this->db->from($modulo_tabela);
        $this->db->join('cms_modulos', 'cms_modulos.id = cms_conteudo.modulo_id');
        $sql = $this->db->get();

        if($sql->num_rows() > 0){
            return $sql->result_array();
        } else {
            return 'nenhum resultado encontrado.';
        }

        

    }

}