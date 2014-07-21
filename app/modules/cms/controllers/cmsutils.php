<?php

/**
 * Controller que carrega várias funções úteis a todo CMS, inclusive chamadas via AJAX
 *
 * @version 3
 * @copyright 2009
 */
class Cmsutils extends Cms_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array('cms/cmsutils_model'));
    }
    
    // -----------------------------------------------------------------------
    /**
     * Recebe URI para retornar, ID revisão e original.
     * Troca todo conteúdo pelos IDs.
     */
    public function revisionToPublic(){
        
        $uri = $this->uri->to_array(array('back', 'rev', 'ori'));
        
        // recompõe uri de edição do conteúdo
        $suri = str_replace('=', ':', $uri['back']);
        $suri = str_replace('_', '/', $suri);
        
        // salva post de revisão
        $return = $this->db->where('id', $uri['rev'])->get('cms_conteudo');
        $revisao = $return->row_array();
        unset($revisao['id']);
        $rev_tipo = $revisao['tipo'];
        
        // salva post original
        $return2 = $this->db->where('id', $uri['ori'])->get('cms_conteudo');
        $original = $return2->row_array();
        unset($original['id']);
        $ori_tipo = $original['tipo'];
        
        // salva revisão com ID do original
        $revisao['tipo'] = $ori_tipo;
        $this->db->where('id', $uri['ori']);
        $this->db->update('cms_conteudo', $revisao);
        
        // salva original com ID da revisão
        $original['tipo'] = $rev_tipo;
        $this->db->where('id', $uri['rev']);
        $this->db->update('cms_conteudo', $original);
        
//        mybug($suri);
        redirect($suri . '/id:' . $uri['ori']);
        
    }


    // -----------------------------------------------------------------------

    /**
     * Recebe o ID do conteúdo e gera <option></option> dos arquivos para popular combo
     * Esta função é chamada via AJAX
     * $param string $type (arq, img)
     * @param int $ID
     * @param string $tb 
     */
    function extraGetDadosDyn($type, $ID, $tb) {
        // pega dados do conteúdo
        $this->db->where('id', $ID);
        $this->db->select('tipo, galeria');
        $sql = $this->db->get($tb);
        $conteudo = $sql->row_array();
        // se for arquivos
        if ($type == 'arq') {

            // dados do módulo
            $this->db->where('id', $conteudo['tipo']);
            $this->db->select('pasta_img, pasta_arq');
            $sql2 = $this->db->get('cms_modulos');
            $arq = $sql2->row_array();

            // gera saida de arquivos
            $this->db->where('pasta', $arq['pasta_arq']);
            $this->db->order_by('dt_ini desc');
            $sql3 = $this->db->get('cms_arquivos');
            $arqs = $sql3->result_array();
        } else if ($type == 'img') {

            $arqs = $this->cms_libs->arquivos_concat_dados($conteudo['galeria']);
        }


        $saida = '<option value="">...</option>';
        foreach ($arqs as $a) {

            $nome = $a['nome'];
            $saida .= '<option value="' . $nome . '">' . $nome . '</option>';
        }

        echo $saida;
    }

    // -----------------------------------------------------------------------
  
    /**
     * Gera o combo box via AJAX de relacionamentos entre arquivo e conteúdos.
     * @param type $modulo_id
     * @param type $tabela 
     */
    function comboConteudoFromModulo($modulo_id, $tabela = 'cms_conteudo'){               
       
        echo $this->cmsutils_model->getComboConteudoFromModulo($modulo_id, '', $tabela);

    }

    function comboCidade($uf) {
        $cb = $this->cms_libs->combo_cidades($uf);
        echo $cb;
    }

    function comboGrupos($co, $ids = '') {
        echo $this->cms_libs->combo_grupos($co, $ids, false, '', 'cms_conteudo');
    }
    
    // -----------------------------------------------------------------------
    /**
     * Recebe requisição AJAX para retornar array de grupos
     */
    public function get_groups_by_json($modulo_id = false){
        
        $term  = $this->input->get('term');
//        $limit = $this->input->get('pagesize');
        
        $this->db->where("(titulo LIKE '%$term%')");
        
        $this->db->where('modulo_id', $modulo_id);
        $this->db->where('tipo', 'conteudo');
        $this->db->select('id, titulo');
        $this->db->where('grupo', 0);
        $this->db->where('lang', get_lang());
//        $this->db->where('status', 1);
        
        $return = $this->db->get('cms_conteudo');
        
        if($return->num_rows() == 0){
            return '';
        }
        
        $retorno = array();
        
        foreach ($return->result_array() as $row){
            
            $retorno[] = array('id' => $row['id'], 'label' => $row['titulo'], 'value' => $row['titulo']);
            
        }        
        
        echo json_encode($retorno);
        
    }

    function enviaSisMens() {
        $imp = ($this->input->post('imp') == 1) ? 1 : 0;
        $assunto = trim($this->input->post('assunto'));
        $mensagem = trim($this->input->post('mensagem'));
        $admin_id = $this->phpsess->get('admin_id', 'cms');
        // valida os campos
        if (strtolower($assunto) == 'assunto' || strtolower($mensagem) == 'mensagem') {
            echo 'Preencha os campos!';
            exit;
        } else if (strlen($assunto) < 4 || strlen($mensagem) < 4) {
            echo 'Preencha os campos!';
            exit;
        }
        // verifica o limite de mensagens por dia
        $this->db->where('admin_id', $admin_id);
        $this->db->where('data', date("Y-m-d"));
        $men = $this->db->get('cms_sis_mens');
        $ttl = $men->num_rows();
        $limite = 5; // --
        if ($ttl >= $limite) {
            $saida = 'Desculpe, mas você atingiu o limite de ' . $limite . ' mensagens por dia.';
        } else {
            $dados['admin_id'] = $admin_id;
            $dados['data'] = date("Y-m-d");
            $dados['hora'] = date("H:i:s");
            $dados['assunto'] = $assunto;
            $dados['txt'] = $mensagem;
            $dados['imp'] = $imp;

            $sql = $this->db->insert('cms_sis_mens', $dados);
            if ($sql) {
                $saida = 'Sua mensagem foi enviada. Hoje restam ' . (($limite - $ttl) - 1) . '.';
            } else {
                $saida = 'Erro ao salvar mensagem!';
            }
        }

        echo $saida;
    }

    /**
     * Marca com olida as mensagens do sistema
     */
    function mensLida($id_mens) {
        $admin_id = $this->phpsess->get('admin_id', 'cms');
        $dados['admin_id'] = $admin_id;
        $dados['mens_id'] = $id_mens;
        $dados['data'] = date('Y-m-d');
        $dados['hora'] = date('H:i:s');
        $ret = $this->db->insert('cms_sis_menslig', $dados);
        echo $ret;
    }

    /**
     * Acesso via AJAX para método que está na library cms_libs
     */
    function atualizaGaleria($id_galeria, $array, $tabela = 'cms_conteudo') {
        $this->cms_libs->atualiza_galeria($id_galeria, $array, $tabela);
        echo true;
    }

    function removeFavorito($id) {
        $this->db->where('id', $id);
        $sql = $this->db->delete('cms_favoritos');

        echo $sql;
    }

    /**
     * Recebe os IDs concatenados com '-' e a tabela. Apaga definitivamente
     *
     * @param mixed $idsFotos
     * @param string $tb
     * @return
     */
    function apagaArquivos($idsFotos, $tb = '') {
        // -- recebe variaveis -- //
        $tbl = 'cms_arquivos';

        $var = trim($idsFotos, '-');
        $partes = explode(':', $var);
        $id_cont = $partes[0];
        $lista = explode('-', $partes[1]); // array de ids
        $this->load->library('cms_libs');
        // percorre e apaga
        foreach ($lista as $id_foto) {
            // pega o nome dos arquivos
            $this->db->where('id', $id_foto);
            $sql = $this->db->get('cms_arquivos');
            // apaga o arquivo físico e BD
            $this->cms_libs->deleta_arquivo($sql->row_array());
        }

        echo 'apagaArquivos|' . implode('-', $lista);
    }

    /**
     * Recebe os IDs concatenados com '-' e a tabela
     *
     * @param mixed $idsFotos
     * @param string $tb
     * @return
     */
    function removeFotos($idsFotos, $tb = '') {
        // -- recebe variaveis -- //
        if ($tb == '') {
            $tbl = 'cms_conteudo';
        }
        else {
            $tbl = $tb;
        }

        $var = trim($idsFotos, '-');
        $partes = explode(':', $var);
        $id_cont = $partes[0];
        $lista = explode('-', $partes[1]); // array de ids

        // pega os IDs da galeria
        $this->db->where('id', $id_cont);
        $this->db->select('galeria');
        $sql = $this->db->get($tbl);
        $row = $sql->row_array();
        $galeria = explode('|', $row['galeria']);
        
        // percorre todos os itens e compara para ver se existe... e remove
        $nova_gal = array();
        foreach ($galeria as $ft) {
            if (array_search($ft, $lista) === false) {
                $nova_gal[] = $ft; // ficou
            }
        }

        $lista2 = implode('|', $nova_gal);
        // atualiza  o conteudo
        $this->db->where('id', $id_cont);
        $this->db->update($tbl, array('galeria' => $lista2));

        echo 'removeFotos|' . implode('-', $lista);
    }

    /**
     * Remove a ligação do arquivo com o conteúdo.
     * ! Não apaga o arquivo!
     * @param int $idArquivo
     */
    function removeLigacaoArquivo($idArquivo){

        $dados['conteudo_id'] = 0;
        $this->db->where('id', $idArquivo);
        $sql = $this->db->update('cms_arquivos', $dados);
        
        echo $sql;
    }

    function ordenaFotos($idsFotos, $tb = '') {
        // -- recebe variaveis -- //
        if ($tb == '') { 
            $tbl = 'cms_conteudo';
        }
        else {
            $tbl = $tb;
        }

        $var = trim($idsFotos, '-');
        $partes = explode(':', $var);
        $id_cont = $partes[0]; // ID
        $lista = explode('-', $partes[1]); // array IDs
        $lista2 = implode('|', $lista); // string n|n|N|n|n
        //
        // modelo de ordenação na ordenação dos ID em um campo
        if ($tbl == 'cms_conteudo') {
            $this->db->where('id', $id_cont);
            $this->db->update($tbl, array('galeria' => $lista2));
        } 
        // modelo de ordenação rearrumando o campo 'ordem'
        else {
            
            if($tbl == 'cms_pastas'){
                $tbl = 'cms_arquivos';
            }
            
            $cont = 1;
            foreach ($lista as $id) {
                $this->db->where('id', $id);
                $this->db->update($tbl, array('ordem' => $cont));
                $cont++;
            }
        }

        echo $id_cont . '|' . $lista2 . '|' . $tbl;
    }

    /**
     * Apaga o registro via AJAX e redireciona para a url de retorno
     *
     * @param mixed $id
     * @param string $tb
     * @return
     */
    function apagaUm($id, $tb = '') {
        // -
        // -- carrega classes -- //
        // -
        // -- recebe variaveis -- //
        if ($tb == ''

            )$tbl = 'cms_conteudo';
        else
            $tbl = $tb;
        // -
        $var = trim($id);
        // -
        // -- processa informações -- //
        $this->db->where('id', $var);
        $sql = $this->db->delete($tbl);

        echo 'apagaUm|' . $id; // retorna os IDs
        // -
        // -- chama as views -- //
        // -
        // -- descarrega no template -- //
    }

    /**
     * Reordena os itens que estão na página.
     * Atenção: itens fora do DOM não serão reordenados
     *
     * @param mixed $ids
     * @param string $tb
     * @return string
     */
    function reordenar($ids, $tb = '') {
        // -
        // -- carrega classes -- //
        // -
        // -- recebe variaveis -- //
        if ($tb == '') $tbl = 'cms_conteudo';
        else
            $tbl = $tb;
        // -
        $var = trim($ids, '-');
        $lista = explode('-', $var);

        $ids = '';
        $i = 0; // init
        // -
        // -- processa informações -- //
        foreach ($lista as $id) {
            $dados['ordem'] = $i + 1;
            $this->db->where('id', $id);
            $sql = $this->db->update($tbl, $dados);
            $ids .= $id . '-';
            $i++;
        }
        echo 'reordenar|' . trim($ids, '-'); // retorna os IDs
        // -
        // -- chama as views -- //
        // -
        // -- descarrega no template -- //
    }

    /**
     * Reordena os itens de menu.
     *
     * @param mixed $ids 13:17.0.0-18.1.17-
     * @param string $tb
     * @return string
     */
    function ordenaMenus($ids, $tb = 'cms_conteudo') {
       
        // remove o hifen do final
        $var = trim($ids, '-');
        // separa o ID do menu
        $dados = explode(':', $var);
        $menu_id = $dados[0];
        $strMenus = $dados[1];// 17.0.0-18.1.17 idMenu.depth.idPai
        // separa os grupos de cada item de menu
        $menuDados = explode('-', $strMenus);
        $ids = '';
        $i = 0; // init
        $nivelPonteiro = 0;
        $paiPonteiro = 0;
        // ----------------------------------------------------
        // -- processa informações -- //
        for ($x = 0; $x < count($menuDados); $x++) {

            $itemDados = explode('.', $menuDados[$x]);
            $item_id = $itemDados[0];
            $item_depth = $itemDados[1];
            $item_pai = $itemDados[2];

            // verifica se tem um pai ou se é de primeiro nível == 0            
            if($item_depth > 0){
                
                // se o ponteiro for apenas um nível acima
//                if($item_depth > $nivelPonteiro){
//
//                    $paiPonteiro = $item_pai;
//
//                    // ajusta o novo ponteiro
//                    $nivelPonteiro = $item_depth;
//
//                }
//                // se o ponteiro for igual o ponteiro anterior preserva o mesmo pai
//                else if($item_depth == $nivelPonteiro){
//
//                    $paiPonteiro = $paiPonteiro;
//
//                }
                
                
                $data['rel'] = $item_pai;
                
                
            } else {
                $data['rel'] = 0;
                $nivelPonteiro = 0;
                $paiPonteiro = 0;
            }

            // ordem sequencial
            $data['ordem'] = $x;

            $this->db->where('id', $item_id);
            $sql = $this->db->update($tb, $data);
            $ids .= $item_id . '-';
            
        }

        echo 'ordenaMenus|' . trim($ids, '-'); // retorna os IDs

//        echo 'ordenaMenus|'.$strMenus;
    }

    /**
     * Apaga em lote. Recebe a tabela via URL e IDs via POST
     *
     * @return
     */
    function apagarLote($ids, $tb = '') {
        // -
        // -- carrega classes -- //
        // -
        // -- recebe variaveis -- //
        if ($tb == ''

            )$tbl = 'cms_conteudo';
        else
            $tbl = $tb;
        // -
        $var = trim($ids, '-');
        $lista = explode('-', $var);

        $ids = '';
        // -
        // -- processa informações -- //
        $oque = '';
        foreach ($lista as $id) {
            // se for 'cms_pastas' verifica se estão vazias
            if ($tbl == 'cms_pastas') {
                $this->db->where('pasta', $id);
                $sql = $this->db->get('cms_arquivos');
                if ($sql->num_rows() == 0) {
                    $dados = $this->cms_libs->conteudo_dados($id, $tbl);
                    $oque .= "Apagou Pasta: <b>" . $dados['titulo'] . "</b><br/>";

                    $this->db->where('id', $id);
                    $sql = $this->db->delete($tbl);
                    $ids .= $id . '-';
                }
            } else if ($tbl == 'cms_enquete_per') {
                $dados = $this->cms_libs->conteudo_dados($id, $tbl);
                $oque .= "Apagou Enquete: <b>" . $dados['titulo'] . "</b><br/>";
                $sql = $this->_elimina_enquete($id);
                $ids .= $id . '-';
            } else {
                $dados = $this->cms_libs->conteudo_dados($id, $tbl);
                $oque .= "Apagou Conteúdo: <b>" . $dados['titulo'] . "</b><br/>";

                $this->db->where('id', $id);
                $sql = $this->db->delete($tbl);
                $ids .= $id . '-';
            }
        }
        // -- >> LOG << -- //
        $this->cms_libs->faz_log_atividade($oque);
        echo 'apagarLote|' . trim($ids, '-'); // retorna os IDs
        // -
        // -- chama as views -- //
        // -
        // -- descarrega no template -- //
    }

    /**
     * Requisição via AJAX para apagar UM item
     *
     * @param mixed $id
     * @param string $tb
     * @return
     */
    function apagaItem($id, $tb = '') {
        if ($tb == '') { 
            $tbl = 'cms_conteudo';
        }
        else {
            $tbl = $tb;
        }
        // se for 'cms_pastas' verifica se estão vazias
        if ($tbl == 'cms_pastas') {
            $this->db->where('pasta', $id);
            $sqll = $this->db->get('cms_arquivos');
            if ($sqll->num_rows() == 0) {
                $dados = $this->cms_libs->conteudo_dados($id, $tbl);
                $oque = "Apagou Pasta: <b>" . $dados['titulo'] . "</b>";
                $this->db->where('id', $id);
                $sql = $this->db->delete($tbl);
            } else {
                $sql = false;
            }
        } else if ($tbl == 'cms_enquete_per') {
            $dados = $this->cms_libs->conteudo_dados($id, $tbl);
            $oque = "Apagou Enquete: <b>" . $dados['titulo'] . "</b>";
            $sql = $this->_elimina_enquete($id);
        } else if ($tbl == 'cms_admin') {
            $this->load->model('cms/admin_model');
            $dados = $this->admin_model->dados_administrador($id);
            $oque = "Apagou Admin: <b>" . $dados['nome'] . "</b>";
            $this->db->where('id', $id);
            $sql = $this->db->delete($tbl);
        } else if ($tbl == 'cms_usuarios') {
            $dados = $this->cms_libs->conteudo_dados($id, $tbl);
            $tipo = ($dados['grupo'] == 0) ? 'Grupo' : 'Usuário';
            $oque = "Apagou " . $tipo . ": <b>" . $dados['nome'] . "</b>";
            $this->db->where('id', $id);
            $sql = $this->db->delete($tbl);
        } else if ($tbl == 'cms_comentarios') {
            $this->db->where('id', $id);
            $this->db->select('conteudo_id');
            $sql3 = $this->db->get($tbl);
            $cont_id = $sql3->row_array();
            //-
            $this->db->where('id', $cont_id['conteudo_id']);
            $this->db->select('titulo');
            $sql2 = $this->db->get('cms_conteudo');
            $dados = $sql2->row_array();
            //-
            $oque = "Apagou comentário em: <b>" . $dados['titulo'] . "</b>";
            $this->db->where('id', $id);
            $sql = $this->db->delete($tbl);
        } else if ($tbl == 'cms_inscritos') {
            $this->db->where('id', $id);
            $this->db->select('conteudo_id');
            $sql3 = $this->db->get($tbl);
            $cont_id = $sql3->row_array();
            //-
            $this->db->where('id', $cont_id['conteudo_id']);
            $this->db->select('titulo');
            $sql2 = $this->db->get('cms_conteudo');
            $dados = $sql2->row_array();
            //-
            $oque = "Apagou inscrição em: <b>" . $dados['titulo'] . "</b>";
            $this->db->where('id', $id);
            $sql = $this->db->delete($tbl);
            
        } else if ($tbl == 'cms_news_age') {
            
            $dados = $this->cms_libs->conteudo_dados($id, $tbl);
            // poderia apagar os registros de usuários na fila
            $oque = "Apagou Agendamento: <b>" . $dados['titulo'] . "</b>";
            $this->db->where('id', $id);
            $sql = $this->db->delete($tbl);
            
        } else {
            
            $dados = $this->cms_libs->conteudo_dados($id, $tbl);
            $tipo = ($dados['grupo'] == 0) ? 'Grupo' : 'Conteúdo';
            $oque = "Apagou " . $tipo . ": <b>" . $dados['titulo'] . "</b>";
            $this->db->where('id', $id);
            $sql = $this->db->delete($tbl);
            
            // alguns conteúdos tem registros em tabelas complementares
            
            if(in_array($dados['modulo_id'], $this->config->item('modulo_loja')) !== FALSE){
                // módulo loja. remove opções, e cms_produtos
                $this->_elimina_produtos($id);
            }
            
        }
        // -- >> LOG << -- //
        $this->cms_libs->faz_log_atividade($oque);

        echo $sql;
    }
    
    // -------------------------------------------------------------------------
    /**
     * Elimina o rastro de registros de um produto
     * @param int $id
     */
    function _elimina_produtos($id){
        // remove votos
        $this->db->where('conteudo_id', $id);
        $this->db->delete('cms_produtos');
        // pega os valores das opções... se existirem
        // busca as opções
        $opcoes = $this->db->where('rel', $id)
                ->where('tipo', 'prod_opcao')
                ->select('id, rel, grupo')
                ->get('cms_conteudo');
        // percorre as opções
        if($opcoes->num_rows() > 0){
            foreach($opcoes->result_array() as $row){
                // remove os valores desta opção
                $this->db->where('grupo', $row['id']);
                $this->db->where('tipo', 'prod_opcao');
                $this->db->delete('cms_conteudo');
                // remove a própria opção
                $this->db->where('id', $row['id']);
                $this->db->where('tipo', 'prod_opcao');
                $this->db->delete('cms_conteudo');
            }
        }
        
    }

    // -------------------------------------------------------------------------
    /**
     * Remove dados sobre a enquete
     * @param type $id
     * @return type
     */
    function _elimina_enquete($id) {

        // remove votos
        $this->db->where('pergunta', $id);
        $this->db->delete('cms_enquete_res');
        // remove opcoes
        $this->db->where('pergunta', $id);
        $this->db->delete('cms_enquete_opc');
        // remove a dita
        $this->db->where('id', $id);
        $sql = $this->db->delete('cms_enquete_per');
        return $sql;
    }

    /**
     * Requisição via AJAX para alterar o Status
     *
     * @param mixed $id
     * @param mixed $tb
     * @param mixed $stt
     * @return
     */
    function alteraStatus($id, $tb = '', $stt = 1) {
        if ($tb == '')
            $tbl = 'cms_conteudo';
        else
            $tbl = $tb;
        // --
        if ($stt == 1)
            $dados['status'] = 0;
        else if ($stt == 0)
            $dados['status'] = 2;
        else if ($stt == 2)
            $dados['status'] = 1;
        $this->db->where('id', $id);
        $sql = $this->db->update($tbl, $dados);
        // -
        // -- chama as views -- //
        // -
        // -- descarrega no template -- //
        echo $sql;
    }

    /**
     * Requisição via AJAX para alterar o Destaque
     *
     * @param mixed $id
     * @param mixed $tb
     * @param mixed $stt
     * @return
     */
    function alteraDestaque($id, $tb = '', $stt = 1) {
        if ($tb == '')$tbl = 'cms_conteudo';
        else
            $tbl = $tb;
        // --
        if ($stt == 1)$dados['destaque'] = 0;
        else if ($stt == 0)$dados['destaque'] = 1;
        $this->db->where('id', $id);
        $sql = $this->db->update($tbl, $dados);
        // -
        // -- chama as views -- //
        // -
        // -- descarrega no template -- //
        echo $sql;
    }

    /**
     * Altera a língua na sessão e redireciona para a página anterior
     *
     * @param string $lang
     * @param string $uri
     */
    function mudaLang($lang, $uri) {
        $uri = str_replace('_', '/', trim($uri, '_'));
        $this->phpsess->save('lang', $lang, 'cms');
        redirect($uri);
    }


    /**
     * Recebe os dados via URI para
     */
    function exporta() {
        // -
        // -- recebe variaveis -- //
        $var = $this->uri->to_array(3, array('co', 'id', 't'));
        $stt = ($var['t'] == 'todos') ? '' : 1;
        $this->load->library(array('table'));
        // pega os dados deste item
        $saida = $this->calendario_model->conteudo_dados($var);
        $curso = $saida['nick'];
        $dt = str_replace('/', '_', $saida['dt1']);

        $inscritos = $this->calendario_model->inscritos_dados($var['id'], $stt);
//		 echo '<pre>';
//         var_dump($inscritos);
//         exit;
        // cabeças
        $data = array(
            array('Nome', 'E-mail', 'Telefone 1', 'Telefone 2', 'Status')
        );
        $data[] = array($saida['titulo'], $saida['dt_ini'], '', '', '');

        if (!$inscritos) {
            
        } else {
            // corpo
            foreach ($inscritos as $row) {
                $data[] = array($row['user']['nome'], $row['user']['email'], $row['user']['tel1'], $row['user']['tel2'], $row['status']);
            }
        }


        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=" . $curso . '_' . $dt . ".xls");
        echo $this->table->generate($data);
    }

    /**
     * Recebe dados básicos para gerar planilha 
     * e gera os links para geração da planilha em lotes
     */
    function exportacao($_var = '') {
        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Exportação' ;
        $this->tabela = 'cms_conteudo';
        $this->var = $this->uri->to_array(array('co', 'id', 'offset', 'tip', 'imgs', 'arqs', 'campos', 'extra', 'dt1', 'dt2', 'stt', 'grupo'));
        $this->_var = $_var;
        $porPag = 1000;
        /*
         * ASSETS
         */
        $this->jquery = array();
        $this->cmsJS = array('padrao-modal');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */
        $this->botoes = array();


        // @todo: se for tabela diferente cms_conteudo, enquete deve retornar false
        // carrega bibliotecas
//        $this->load->library(array('layout_cms'));
        // recebe variáveis
        
        $tipo = $this->var['co'];
        $offset = ($this->var['offset']=="") ? 0 : $this->var['offset'];
        $dt1 = ($this->var['dt1'] == '') ? $this->input->post('dt1') : $this->var['dt1'];
        $dt2 = ($this->var['dt2'] == '') ? $this->input->post('dt2') : $this->var['dt2'];
        $ativo = ($this->var['stt'] == '') ? $this->input->post('ativo') : $this->var['stt'];
        $grupo = ($this->var['grupo'] == '') ? $this->input->post('grupos') : $this->var['grupo'];
        $camposPersona = $this->var['campos'];
        $camExtra = $this->var['extra'];



        // dados do módulo
//        $this->db->where('id', $tipo);
//        $this->db->select('tabela');
//        $sql1 = $this->db->get('cms_modulos');
//        $mod = $sql1->row_array();
        $tb = $modulo['tabela'];

        
        // faz pesquisa de tudo
        if ($dt1 != '' && $dt2 == ''){$this->db->where('dt_ini', formaSQL($dt1));}
        else if ($dt1 != '' && $dt2 != '') {
            $this->db->where('dt_ini >=', formaSQL($dt1));
            $this->db->where('dt_ini <=', formaSQL($dt2));
        }
        if ($ativo != '')$this->db->where('status', $ativo);
        if($tb == 'cms_conteudo')$this->db->where('tipo', $tipo);
        $this->db->select('id');
        if($grupo != ''){
            $this->db->where('grupo', $grupo);
        } else {
            $this->db->where('grupo !=', 0);
        }
            
         $this->db->where('lang', get_lang());
        $sql = $this->db->get($tb);
        $quantTotal = $sql->num_rows();
        // quantas páginas
        $pags = ceil($quantTotal / $porPag);
        // concatena dados do corpo
 
// echo '<pre>';
//var_dump($this->db->last_query());
//exit;
        $dados = array('corpo' => false);
        for($x = 0; $x < $pags; $x++){

            $dados['corpo'][] = array(
                'tipo' => $tipo,
                'porPag' => $porPag,
                'offset' => $x * $porPag,
                'dt1' => formaSQL($dt1),
                'dt2' => formaSQL($dt2),
                'stt' => $ativo,
                'grupo' => $grupo,
                'tb' => $tb,
                'campos' => $camposPersona,
                'extra' => $camExtra,
                'total' => $quantTotal
            );
           
        }
 


        $this->corpo = $this->load->view('cms/exportacao_listaLinks', $dados, true);
        $this->modalRender();
        
//        $tmp['tabela'] = 'cms_conteudo';
//        $tmp['title'] = 'Exportação';
//        $tmp['scripts'] = $this->head_model->scripts($scripts, 'js');
//        $tmp['scripts'] .= $this->head_model->scripts($scriptsCms, 'ci_itens/js');
//        $tmp['estilos'] = $this->head_model->estilos($estilos, 'ci_itens/css');
//        $tmp['menu'] = $this->layout_cms->menu_modal($botoes);
//        $tmp['resposta'] = $this->layout_cms->modal_resposta($var, $_var);
//        // -
//        // -- descarrega no template -- //
//        $this->load->view('cms/template_modal', $tmp);


    }
    /**
     * Gera a planilha com os dados passados via URI
     */
    function exportaPlanilha()
    {
        // carrega bibliotecas
        $this->load->library(array('table'));
        // recebe variáveis
        $var = $this->uri->to_array(array('co', 'dt1', 'dt2', 'stt', 'g', 'p', 'o', 'tb', 'campos', 'extra'));
        $tipo = $var['co'];
        $porPag = $var['p'];
        $offset = $var['o'];
        $tb = $var['tb'];
        $dt1 = $var['dt1'];
        $dt2 = $var['dt2'];
        $grupo = $var['g'];
        $ativo = $var['stt'];
        $camposPerso = $var['campos'];
        $camExtra = $var['extra'];
        
        // trata variáveis
        if($tb == 'cms_conteudo'){
            $campos = array('id','dt_ini', 'grupo', 'titulo', 'resumo', 'status');
        } else if($tb == 'cms_usuarios'){
            $campos = array('id', 'dt_ini', 'grupo', 'nome', 'email', 'nasc', 'tel1', 'tel2', 'obs');
        }
        // se houver campos personalizados sobrepoe
        if($camposPerso != ''){
            $campos = explode('-', $camposPerso);
        }
        
        
        
        
        // pesquisa dados do corpo
        $this->db->limit($porPag, $offset);
        if ($dt1 != '0000-00-00' && $dt2 == '0000-00-00'){$this->db->where('dt_ini', $dt1);}
        else if ($dt1 != '0000-00-00' && $dt2 != '0000-00-00') {
            $this->db->where('dt_ini >=', $dt1);
            $this->db->where('dt_ini <=', $dt2);
        }
        if ($ativo != '')$this->db->where('status', $ativo);
        if($tb == 'cms_conteudo')$this->db->where('tipo', $tipo);
        if($grupo != ''){
            $this->db->where('grupo', $grupo);
        } else {
            $this->db->where('grupo !=', 0);
        }
         $this->db->where('lang', get_lang());
        $this->db->order_by('dt_ini desc');
        $this->db->select(implode(',', $campos));
        $sql = $this->db->get($tb);
        $corpo = $sql->result_array();
        

        // se houver campos Extra adiciona
        if($camExtra != ''){
            $camExtra = explode('-', $camExtra);
            $campos = array_merge($campos, $camExtra);
        }

        // cabeça da planilha
        if($tb == 'cms_usuarios')
        {
            $campos[] = 'nome cracha';
            $campos[] = 'subgrupo';
        }

        $data = array($campos);



        // percorre e parseia dados da tabela contando array final
        foreach($corpo as $c){
            // parseia dados
            $l = array();
            $idRegistro = $c['id'];
            foreach($c as $r => $v){

                if($r=='dt_ini' || $r=='dt_fim' || $r=='nasc'){
                    $v = formaPadrao($v);
                } 
                else if($r == 'tel1' || $r == 'tel2'){
                    $v = tel_input($v);
                }
                else if($r == 'status'){
                    if($v == 1)$v = 'ativo';
                    else if($v == 0)$v = 'inativo';
                    else if($v == 2)$v = 'editando';
                }
                else if($r == 'grupo'){
                    $dd = $this->cms_libs->conteudo_dados($v, $tb, 'grupo');
                    $v = ($tb=='cms_conteudo') ? utf8_decode($dd['titulo']) : utf8_decode($dd['nome']);
                }
                else {
                    $v = utf8_decode($v);
                }

                
                $l[$r] = $v;
            }

            // add meta data
            if($tb == 'cms_usuarios')
            {
                $this->load->library('cms_metadados');
                $metas = $this->cms_metadados->getAllByUser($idRegistro);
                $l['nome cracha'] = utf8_decode(get_meta($metas, 'nome_cracha', null, true));
                $l['subgrupo'] = utf8_decode(get_meta($metas, 'tipo_usuario', null, true));
            }

            // se existe campos extra adiciona aqui
            if(is_array($camExtra)){
                $this->load->library(array('site_utils'));
                $regdados = $this->site_utils->_get_conteudo($idRegistro);
                $dadosModulo = $this->site_utils->_get_modulo($regdados['tipo']);


                foreach($camExtra as $ex){
                    
                    $extra = $this->site_utils->getCampoExtra($dadosModulo['extra'], $regdados['extra'], $ex);
                    $l[$ex] = utf8_decode($extra);
                    
                }

            }
            $data[] = $l;
        }





        //////// geração
        header("Content-type: application/x-msdownload; charset=ISO-8859-1");
        header("Content-Disposition: attachment; filename=Planilha_" . date("d-m-Y_H-i-s") . ".xls");
        echo $this->table->generate($data);
    }

    /**
     * Gera link de planilha persinalizada
     * Com campos EXTRA
     */
    function planilhaA1($_var = '') {
        // carrega bibliotecas
        $this->load->library(array('layout_cms'));
        // recebe variáveis
        $porPag = 3;
        $var = $this->uri->to_array(array('co', 'tip', 'imgs', 'arqs'));
        $tipo = $var['co'];
        $offset = 0;
        $dt1 = $this->input->post('dt1');
        $dt2 = $this->input->post('dt2');
        $ativo = $this->input->post('ativo');
        $grupo = $this->input->post('grupos');
        $camposPersona = 'titulo-status';
        $camExtra = 'raca-maisopcoes';

        $scripts = array();
        $scriptsCms = array('padrao-modal');
        $estilos = array('nyroModal');
        $botoes = array();

        // dados do módulo
        $this->db->where('id', $tipo);
        $this->db->select('tabela');
        $sql1 = $this->db->get('cms_modulos');
        $mod = $sql1->row_array();
        $tb = $mod['tabela'];



        // faz pesquisa de tudo
        if($tb == 'cms_conteudo')$this->db->where('tipo', $tipo);
        $this->db->select('id');
        $this->db->where('grupo !=', 0);
         $this->db->where('lang', get_lang());
        $sql = $this->db->get($tb);
        $quantTotal = $sql->num_rows();
        // quantas páginas
        $pags = ceil($quantTotal / $porPag);
        // concatena dados do corpo

        for($x = 0; $x < $pags; $x++){

            $dados['corpo'][] = array(
                'tipo' => $tipo,
                'porPag' => $porPag,
                'offset' => $x * $porPag,
                'dt1' => $dt1,
                'dt2' => $dt2,
                'att' => $ativo,
                'grupo' => $grupo,
                'tb' => $tb,
                'campos' => $camposPersona,
                'extra' => $camExtra
            );

        }



        $tmp['corpo'] = $this->load->view('cms/exportacao_listaLinks', $dados, true);

        $tmp['tabela'] = 'cms_conteudo';
        $tmp['title'] = 'Exportação';
        $tmp['scripts'] = $this->head_model->scripts($scripts, 'js');
        $tmp['scripts'] .= $this->head_model->scripts($scriptsCms, 'ci_itens/js');
        $tmp['estilos'] = $this->head_model->estilos($estilos, 'ci_itens/css');
        $tmp['menu'] = $this->layout_cms->menu_modal($botoes);
        $tmp['resposta'] = $this->layout_cms->modal_resposta($var, $_var);
        // -
        // -- descarrega no template -- //
        $this->load->view('cms/template_modal', $tmp);

//        echo '<pre>';
//        var_dump($_POST);
//        exit;
    }


    /**
     * Abre formulário para entrada dos dados para extração dos e-mails.
     * @param <type> $_var
     */
    function extrairEmails($_var = ''){


        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Extraindo E-mails ';
        $this->tabela = 'cms_conteudo';
        $this->var = $this->uri->to_array(array('tip', 'imgs', 'arqs'));
        $this->_var = $_var;
        /*
         * ASSETS
         */
        $this->jquery = array('jquery.nyroModal');
        $this->cmsJS = array('padrao-modal', 'nyroModal_init');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */
        $this->botoes = array();

        $this->corpo = $this->load->view('cms/usuarios/extrai_form', '', true);

        $this->modalRender();
    }

    function extraindoEmails()
    {

        $url = $this->input->post('url');
        $texto = $this->input->post('texto');

        

        if ($url || $texto) {
            // verifica o url
            if (strlen(trim($url)) > 10) {
                $dadosPag['resultado'] = $this->cms_libs->extrai_emails_do_texto($url, 'url');
            }
            // verifica o texto
            else if (strlen(trim($texto)) > 10) {
                $dadosPag['resultado'] = $this->cms_libs->extrai_emails_do_texto($texto);
            }
            // retiorna erro
            else {
                redirect("cms/cmsutils/extrairEmails/erro");
            }
        } else {
            
            redirect("cms/cmsutils/extrairEmails/erro");

        }


        /*
         * VARIÁVEIS
         */
        $modulo = $this->modulo; // infos do módulo
        $this->title = 'Extraindo E-mails ';
        $this->tabela = 'cms_conteudo';
        $this->var = $this->uri->to_array(array('tip', 'imgs', 'arqs'));
        
        /*
         * ASSETS
         */
        $this->jquery = array('jquery.nyroModal');
        $this->cmsJS = array('padrao-modal', 'nyroModal_init');
        $this->css = array('nyroModal');

        /*
         * OPÇÕES
         */
        $this->botoes = array('Fazer nova extração' => 'cms/cmsutils/extrairEmails');

        $this->corpo = $this->load->view('cms/usuarios/extrai_resultado', $dadosPag, true);

        $this->modalRender();


    }

}

?>