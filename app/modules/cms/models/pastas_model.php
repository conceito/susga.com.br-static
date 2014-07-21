<?php

/**
 *
 * @version $Id$
 * @copyright 2010
 */
class Pastas_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function lista_conteudos($v, $tipo = 'conteudo', $modulo = array()) {
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
        if (strlen(trim($this->input->post('dt1'))) > 0) {
            $dt1 = formaSQL($this->input->post('dt1'));
        }
        else {
            $dt1 = $v['dt1'];
        }
        if (strlen(trim($this->input->post('dt2'))) > 0) {
            $dt2 = formaSQL($this->input->post('dt2'));
        }
        else {
            $dt2 = $v['dt2'];
        }
        // echo '<pre>';
        // var_dump($v['co']);
        // exit;
        // -- SQL básica com paginação -- //
        if ($dt1 != '' && $dt2 == '') {
            $this->db->where('dt_ini', $dt1);
        } else if ($dt1 != '' && $dt2 != '') {
            $this->db->where('dt_ini >=', $dt1);
            $this->db->where('dt_ini <=', $dt2);
        }
        if ($stt != '')
            $this->db->where('status', $stt);
        if ($b != '') {
            $this->db->like('titulo', $b);
            $this->db->or_like('txt', $b);
        }
        $this->db->limit($pp, $offset);
        // ordenação
        if ($v['co'] == 0 || $v['co'] == 2)
            $this->db->order_by('ordem', 'titulo');
        else
            $this->db->order_by('dt_ini desc'); // álbuns fotos
            // tipo de pasta
            $this->db->where('tipo', $v['co']);
        // grupo ou pasta
        if ($tipo == 'grupo') {
            $this->db->where('grupo', 0); // busca grupos
        } else {
            if ($g == 0){
                $this->db->where('grupo !=', 0); // todos conteudos
            }                
            else{
                $this->db->where('grupo', $g); // conteudos do grupo
            }
        }
        $this->db->where('lang', get_lang());

        $sql = $this->db->get('cms_pastas');
        // -- pega o Total de registros -- //
        if ($dt1 != '' && $dt2 == '') {
            $this->db->where('dt_ini', $dt1);
        } else if ($dt1 != '' && $dt2 != '') {
            $this->db->where('dt_ini >=', $dt1);
            $this->db->where('dt_ini <=', $dt2);
        }
        if ($stt != '')
            $this->db->where('status', $stt);
        if ($b != '') {
            $this->db->like('titulo', $b);
            $this->db->or_like('txt', $b);
        }
        $this->db->where('tipo', $v['co']);
        if ($tipo == 'grupo') {
            $this->db->where('grupo', 0); // busca grupos
        } else {
            if ($g == 0){
                $this->db->where('grupo !=', 0); // todos conteudos
            }              
            else{
                $this->db->where('grupo', $g); // conteudos do grupo
            }
                



        }
        $this->db->where('lang', get_lang());
        $sql_ttl = $this->db->get('cms_pastas');
        $ttl_rows = $sql_ttl->num_rows();
        // paginação
        $this->load->library('pagination');
        $config['base_url'] = cms_url('cms/pastas/index/co:' . $v['co'] . '/pp:' . $pp . '/g:' . $g . '/dt1:' . $v['dt1'] . '/dt2:' . $v['dt2'] . '/b:' . $b . '/stt:' . $stt . '/');
        $config['total_rows'] = $ttl_rows;
        $config['per_page'] = $pp;
        $config['uri_segment'] = 11; // segmentos + 1
        $config['num_links'] = 4; // quantas páginas são mstradas antes de depois na paginação
        $config['num_tag_open'] = '<span class="pagnation_number">';
        $config['num_tag_close'] = '</span>';
        $config['cur_tag_open'] = '<span class="pagnation_current">';
        $config['cur_tag_close'] = '</span>';
        $this->pagination->initialize($config);
//         echo '<pre>';
//         var_dump($sql->result_array());
//         exit;
        $saida = array('ttl_rows' => $ttl_rows,
            'rows' => $this->parse_lista_conteudos($sql->result_array(), $modulo));

        return $saida;
    }

    /**
     * Prepara pesquisa de item de menu
     *
     * @param mixed $array
     * @return
     */
    function parse_lista_conteudos($array, $modulo = array()) {
        if (count($array) == 0)
            return false;
        // percorre array
        

        $saida = array();
        foreach ($array as $row) {

            // init
            $row['level'] = 0;

            if ($row['status'] == 1)
                $row['status'] = 'ativo';
            else if ($row['status'] == 0)
                $row['status'] = 'inativo';
            else if ($row['status'] == 2)
                $row['status'] = 'editando';

            if ($row['grupo'] == 0) {
                $row['grupo'] = 'Grupo';

                // Obtêm cores do crupo
                // trata as cores
                $cores = $this->get_grupo_cores($row['cor']);
                $row['grupoCor1'] = $cores['cor1'];
                $row['grupoCor2'] = $cores['cor2'];

            } else {
              

                $this->db->where('id', $row['grupo']);
                $this->db->select('titulo, cor');
                $sql = $this->db->get('cms_pastas');
                $item = $sql->row_array();
                $row['grupo'] = $item['titulo'];
                // cores do grupo
                $cores = (strlen($item['cor']) > 6) ? explode('|', $item['cor']) : array("", "");
                $row['grupoCor1'] = $cores[0];
                $row['grupoCor2'] = $cores[1];
            }
            // padrões - detalhes menores
            if ($row['tipo'] != 2) {
                $row['padroes'] = 'Max: ' . $row['max_w'] . 'x' . $row['max_h'];
                $row['padroes'] .= ' <br/>Med: ' . $row['med_w'] . 'x' . $row['med_h'] . '';
                $row['padroes'] .= ' <br/>Min: ' . $row['mini_w'] . 'x' . $row['mini_h'] . '';
            } else {
                $row['padroes'] = '-';
            }

//            mybug($array);


            // busca a quantidade de imagens ou arquivos
            $this->db->where('pasta', $row['id']);
            $sql = $this->db->get('cms_arquivos');
            $quant = $sql->num_rows();
            $row['quantidade'] = $quant;
            // palavra chave para layout
            if ($row['tipo'] == 0)
                $row['termo'] = 'imagem';
            if ($row['tipo'] == 1)
                $row['termo'] = 'album';
            if ($row['tipo'] == 2)
                $row['termo'] = 'arquivo';
            // coloca no array

            $saida[] = $row;
        }

        return $saida;
    }


    /**
     * Entra com a string das cores concatenadas e retorna um array
     *
     * @param string $tagdecor
     * @param string $tipo Para fazer alguma distinção no futuro
     * @return array Array com as duas cores array('grupoCor1' => #cor, 'grupoCor2' => #cor)
     */
    function get_grupo_cores($tagdecor, $tipo = 'conteudo') {
        $tagdecor = trim($tagdecor);
        $cores = (strlen($tagdecor) > 6) ? explode('|', $tagdecor) : array("", "");
        $row['cor1'] = $cores[0];
        $row['cor2'] = $cores[1];

        return $row;
    }

    function conteudo_salva($var) {
        // - salva os dados do menu principal Raiz
        $grupo = $this->input->post('grupos');
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $data = trim($this->input->post('dt1'));
        $status = $this->input->post('status');
        $tipo = $this->input->post('tipo');
        $txt = trim($this->input->post('txt'));
        $mini_w = trim($this->input->post('mini_w'));
        $mini_h = trim($this->input->post('mini_h'));
        $med_w = trim($this->input->post('med_w'));
        $med_h = trim($this->input->post('med_h'));
        $max_w = trim($this->input->post('max_w'));
        $max_h = trim($this->input->post('max_h'));

        $dados['titulo'] = $titulo;
        $dados['grupo'] = $grupo;
        $dados['dt_ini'] = formaSQL($data);
        $dados['mini_w'] = $mini_w;
        $dados['mini_h'] = $mini_h;
        $dados['med_w'] = $med_w;
        $dados['med_h'] = $med_h;
        $dados['max_w'] = $max_w;
        $dados['max_h'] = $max_h;
        $dados['status'] = $status;
        $dados['txt'] = $txt;

//         echo '<pre>';
//         var_dump($dados);
//         exit;
        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');
            $dados['lang'] = get_lang();
            $dados['tipo'] = $var['co']; // tipo só quando cria
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados, 'cms_pastas');

            $sql = $this->db->insert('cms_pastas', $dados);
            $esteid = $this->db->insert_id();
            // -- >> LOG << -- //
            $oque = "Nova Pasta: <a href=\"" . cms_url('cms/pastas/edita/co:' . $var['co'] . '/id:' . $esteid) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {

            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_pastas', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Pasta: <a href=\"" . cms_url('cms/pastas/edita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
            $esteid = $var['id'];
        }

        return $esteid;
    }

    /**
     * Pega os dados na Library e parseia os dados
     *
     * @param mixed $var
     * @return
     */
    function conteudo_dados($var) {

        $this->db->where('id', $var['id']);
        $sql = $this->db->get('cms_pastas');
        if ($sql->num_rows() == 0)
                return false;
        $dd = $sql->row_array();

        if (!$dd)
            return false;
        // percorre array
        $saida = array();
        foreach ($dd as $chv => $vlr) {
            // data
            if ($chv == 'dt_ini')
                $saida['dt1'] = formaPadrao($vlr);
            // quantidade de imagens na galeria
            if ($chv == 'id') {
                $this->db->where('pasta', $vlr);
                $sql = $this->db->get('cms_arquivos');
                $saida['quantGal'] = $sql->num_rows();
            }
            // pega cores
            if ($chv == 'cor') {
                $cores = (strlen($vlr) > 6) ? explode('|', $vlr) : array("", "");
                $saida['cor1'] = $cores[0];
                $saida['cor2'] = $cores[1];
            }



            // coloca no array
            $saida[$chv] = $vlr;
        }
//         echo '<pre>';
//         var_dump($saida);
//         exit;
        return $saida;
    }

    /**
     * Retorna um array multi com todos os dados dos arquivos
     *
     * @param mixed $var
     * @param mixed $saida
     * @return
     * */
    function arquivos_dados($var, $campo = 'pasta') {

        if (is_array($var)) {
            $id = $var['id'];
        } else {
            $id = $var;
        }
        
        $this->db->where($campo, $id);
        $this->db->order_by('ordem');
        $sql = $this->db->get('cms_arquivos');
        
        if ($sql->num_rows() == 0) {
            return false;
        }
        
        $lista = $sql->result_array();
        
        // parseia
        $saida = array();
        foreach ($lista as $row) {
            if ($row['img'] == 2) {
                $row['ext'] = 'flv';
            }
            $saida[] = $row;
        }

        return $saida;
    }

    function grupo_salva($var) {
        // - salva os dados do menu principal Raiz
        $titulo = trim($this->input->post('titulo'));
        $nick = trim($this->input->post('nick'));
        $txt = trim($this->input->post('txt'));
        $cor1 = trim($this->input->post('cor1'));
        $cor2 = trim($this->input->post('cor2'));

        $dados['titulo'] = $titulo;
        $dados['tipo'] = $var['co'];
        $dados['txt'] = $txt;
        $dados['cor'] = $cor1 . '|' . $cor2;
        $dados['grupo'] = 0;

        
        // --  NOVO ITEM  -- //
        if ($var['id'] == '') {
            $dados['autor'] = $this->phpsess->get('admin_id', 'cms');            
            $dados['lang'] = get_lang();
            $dados['status'] = 1;            
            $dados['dt_ini'] = date("Y-m-d");
            $dados['nick'] = $this->cms_libs->confirma_apelido($var['id'], $nick, $dados, 'cms_pastas');

            $sql = $this->db->insert('cms_pastas', $dados);
            // -- >> LOG << -- //
            $oque = "Novo Grupo de Pasta: <a href=\"" . cms_url('cms/pastas/grupoEdita/co:' . $var['co'] . '/id:' . $this->db->insert_id()) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }
        // --  ATUALIZANDO ITEM  -- //
        else {
            $this->db->where('id', $var['id']);
            $sql = $this->db->update('cms_pastas', $dados);
            // -- >> LOG << -- //
            $oque = "Atualizou Grupo de Pasta: <a href=\"" . cms_url('cms/pastas/grupoEdita/co:' . $var['co'] . '/id:' . $var['id']) . "\">" . $titulo . "</a>";
            $this->cms_libs->faz_log_atividade($oque);
        }

        return $sql;
    }

    /**
     * Retorna o nome (label) das pasrtes do sistema para cada tipo de pasta
     *
     * @param mixed $tipo
     * @param mixed $lugar
     * @return
     * */
    function labels($tipo, $lugar) {


//        echo $tipo;
//        exit;
// ajustes para CI2
        if($tipo == 13){
            $tipo = 1;
        } 




        if ($lugar == 'title') {
            if ($tipo == 0)
                $r = 'Lista de Pastas de Imagens';
            if ($tipo == 1)
                $r = 'Lista de Álbuns';
            if ($tipo == 2)
                $r = 'Lista de Pastas de Arquivos';
        } else if ($lugar == 'novo') {
            if ($tipo == 0)
                $r = 'Nova Pasta de Imagens';
            if ($tipo == 1)
                $r = 'Novo Álbum';
            if ($tipo == 2)
                $r = 'Nova Pasta de Arquivos';
        } else if ($lugar == 'tipo') {
            if ($tipo == 0)
                $r = 'Imagens';
            if ($tipo == 1)
                $r = 'Álbuns';
            if ($tipo == 2)
                $r = 'Arquivos';
        } else if ($lugar == 'edita') {
            if ($tipo == 0)
                $r = 'Editando Pasta de Imagens';
            if ($tipo == 1)
                $r = 'Editando Álbum';
            if ($tipo == 2)
                $r = 'Editando Pasta de Arquivos';
        } else if ($lugar == 'abaArqs') {
            if ($tipo == 0)
                $r = 'Imagens';
            if ($tipo == 1)
                $r = 'Fotos';
            if ($tipo == 2)
                $r = 'Arquivos';
        }



        return $r;
    }

    function combo_pastas($tipo = 0, $ids = '', $name = '', $selecione = false) {
        if (!is_array($ids)) { // se não for array, processa para validar no combobox
            $ids = explode('|', $ids);
        }
        // nome do campo, mantem compatibilidade
        if ($name == '') {
            $name = 'pastas_' . $tipo;
        }

        // popula array
        $this->db->where('tipo', $tipo);
        $this->db->where('grupo !=', 0);
        $this->db->where('status', 1);
        $sql = $this->db->get('cms_pastas');

        $menus = array();
        // verifica se deve adicionar o primeiro option vazio
        if ($selecione)
            $menus[] = 'Escolha...';
        foreach ($sql->result_array() as $p) {
            $menus[$p['id']] = $p['titulo'];
        }
        // echo '<pre>';
        // print_r($ids);
        // exit;
        $cb = $this->cms_libs->cb($ids, $menus, $name, false);
        return $cb;
    }

}

?>