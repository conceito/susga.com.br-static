<?php
/**
 * Biblioteca para manipulação de arquivos.
 * 
 * # faz upload para o servidor
 *   $this->cms_arquivo->send('input_name', 'url_pasta');
 * 
 * # se o input file[] for do tipo multiple, o retorno será:
 *   $ret = $this->cms_arquivo->send('input_name');
 *   $ret['input_name0'] = array('error', 'success');
 *   $ret['input_name1'] = array('error', 'success');
 *   $ret['input_name2'] = array('error', 'success');
 * 
 * # salva dados do arquivo em uma pasta
 *   $this->cms_arquivo->save($retorno_input_name); // padrão 5
 *   $this->cms_arquivo->save($retorno_input_name, 'pasta_id');
 *   $this->cms_arquivo->save($retorno_input_name, array());
 * 
 * # Envia o arquivo para o servidor e salva dados no BD.
 *   $this->cms_arquivo->send_save($input_name = NULL, $pasta_id_content_array = NULL, $upl_path = NULL)
 */
class Cms_arquivo{
    
    private $ci = NULL;
    private $this_arquivo = FALSE;   // armazena dados do arquivo
    public  $max_size     = 2097152; // 1048576 = 1Mb
    public  $permited_ext = array('doc', 'docx', 'jpg', 'gif', 'png', 'pdf', 'xls', 'xlsx', 'tif');
    public  $pasta_padrao = 5;       // confirmar no CMS
    public  $error = FALSE;          // armazena os erros
    public  $fisic_path = ''; // caminho físico do site
    public  $upl_arqs = '';
    public  $upl_imgs = '';
    public  $debug_mode = FALSE;
    private $debug = array();


//    public function spark(){        
//        $this->ci->load->spark('wideimage/11.02.19');
//        $p = fisic_path().$this->ci->config->item('upl_imgs').'/';
//        $this->ci->wideimage->load($p.'_01.jpg')->resize(50, 50)->saveToFile($p.'small.jpg');
//    }
    
    public function __construct() {
        $this->ci = &get_instance();
        $this->fisic_path = fisic_path();
        $this->upl_imgs = $this->ci->config->item('upl_imgs');
        $this->upl_arqs = $this->ci->config->item('upl_arqs');
    }
    
    public function debug(){
        return $this->debug;
    }


    // -------------------------------------------------------------------------

    /**
     * Envia o arquivo para o servidor. Não salva referência no BD.
     * Retorno:
     * Array
        (
            [$input_name] => Array
                (
                    [error] => false
                    [success] => Array
                        (
                            [file_name] => arquivo.doc
                            [file_type] => application/msword
                            [file_path] => F:/wamp/www/meucms/upl/arqs/
                            [full_path] => F:/wamp/www/meucms/upl/arqs/arquivo.doc
                            [raw_name] => arquivo
                            [orig_name] => arquivo.doc
                            [client_name] => arquivo.doc
                            [file_ext] => .doc
                            [file_size] => 40.5
                            [is_image] => 
                            [image_width] => 
                            [image_height] => 
                            [image_type] => 
                            [image_size_str] => 
                        )
                )
        )
     * 
     * @param string $input_name
     * @return array
     */
    public function send($input_name = NULL, $upl_path = NULL){
              
        
        $this->ci->load->library('upload');
        $return = array();
                    

        if($input_name === NULL){
            // upload de lista de aqruivos
            foreach($_FILES as $name => $filedata){
            
                $return[$name] = $this->upload($name, $upl_path);
                
                if($this->debug_mode){
                    $this->debug[] = array('acao' => 'ap', 'retorno' => $name);
                }

            }
        } else {
            
            // verifica se é um input multiple
            if(is_array($_FILES[$input_name]['tmp_name'])){
                
                // altera estrutura de $_FILES
                $this->prep_multiples($input_name);
                // upload de lista de aqruivos
                foreach($_FILES as $name => $filedata){

                    $return[$name] = $this->upload($name, $upl_path);

                    if($this->debug_mode){
                        $this->debug[] = array('acao' => 'ap', 'retorno' => $name);
                    }

                }
                
            } 
            // é input simples
            else{
                $return[$input_name] = $this->upload($input_name, $upl_path);
            }
            
            
            if($this->debug_mode){
                $this->debug[] = array('acao' => '$this->upload() single','retorno' => $input_name);
            }
            
        }
        
        
        return $return;
        
    }
    
    // -------------------------------------------------------------------------

    /**
     * Faz o envio de UM arquivo por vez para o servidor.
     * Para enviar vários arquivos fazer um looping pelos arquivos.
     * @param array $input_name
     * @param string $upl_path
     */
    public function upload($input_name, $upl_path = NULL){
        
        if($upl_path === NULL){
            $upl_path = $this->upl_arqs;
        }
        
        // retorno
        $data['error'] = FALSE;
        $data['success'] = FALSE;
        
        
//        $config['encrypt_name']  = TRUE;
        $config['file_name']     = substr(url_title($_FILES[$input_name]['name']),0 , -3);
        $config['upload_path']   = $this->fisic_path.$upl_path.'/';
        $config['allowed_types'] = implode('|', $this->permited_ext);
        $config['max_size']	 = round($this->max_size/1000);
        $config['remove_spaces'] = TRUE;

        
        // Initialize the new config
        $this->ci->upload->initialize($config);

        if ( ! $this->ci->upload->do_upload($input_name)){
            $data['error'] = $this->ci->upload->display_errors();
        } else {
            $data['success'] = $this->ci->upload->data();
        }
        
        if($this->debug_mode){
            $this->debug[] = array('acao' => 'uloaded','retorno' => $data);
        }
        
        
        
        return $data;
    }
    
    // -------------------------------------------------------------------------
    /**
     * Recebe o retorno do $this->send() e dados da pasta de destino e salva no BD.
     * Como dados adicionais para salvar o arquivo você tem:
     * $pasta_id_content_array = array(
            'descricao'   => string,
            'pasta_id'    => int,
            'rel'         => int,
            'tag_opt'     => 1|2|3|4|5,
            'conteudo_id' => int
        );
     * 
     * @param       array       $retorno_input_name
     * @param       int|array   $pasta_id_content_array
     * @return      boolean|int     Retorna o ID do arquivo no BD.
     */
    public function save($retorno_input_name, $pasta_id_content_array = NULL){
        
        // se não passar a pasta seta a padrão
        if ($pasta_id_content_array === NULL) {
            $dados['pasta_id'] = $this->pasta_padrao;
        } 
        // se passar um número, é usado como o ID da pasta
        elseif (is_numeric($pasta_id_content_array)) {
            $dados['pasta_id'] = $pasta_id_content_array;
        }
        // senão, deve ser um array com os dados adicionais para salvar o arquivo
        else {
            $dados = $pasta_id_content_array;
        }
        
        // se deu erro... retorna
        if($retorno_input_name['error'] !== FALSE){
            return FALSE;
        }

        
        
        // armazena os dados do arquivo
        $retorno = $retorno_input_name['success'];
        
        if($this->debug_mode){
            $this->debug[] = array('acao' => '$this->save() $retorno','retorno' => $retorno);
        }
        
        
        // prepara valores para salvar no BD
        $arq['dt_ini'] = date("Y-m-d");	
        $arq['nome'] = $retorno['file_name'];	
        $arq['descricao'] = (isset($dados['descricao'])) ? $dados['descricao'] : $retorno['client_name'];	
        $arq['img'] = 0;
        
        if((bool)$retorno['is_image']){
            $arq['width'] = $retorno['image_width'];	
            $arq['height'] = $retorno['image_height'];	
            $arq['pos'] = ($retorno['image_width'] > $retorno['image_height']) ? 'h' : 'v';	
            $arq['img'] = 1;
        }
        	
        $arq['ext'] = str_replace('.', '', $retorno['file_ext']);	
        $arq['peso'] = $retorno['file_size'] * 1000;	
        $arq['pasta'] = $dados['pasta_id'];	
        $arq['rel'] = (isset($dados['rel'])) ? $dados['rel'] : 0;	
        $arq['tag_opt'] = (isset($dados['tag_opt'])) ? $dados['tag_opt'] : 0;	
        $arq['conteudo_id'] = (isset($dados['conteudo_id'])) ? $dados['conteudo_id'] : 0;
        
        $this->ci->db->insert('cms_arquivos', $arq);        
        
        return $this->ci->db->insert_id();
         
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Envia o arquivo para o servidor e salva dados no BD.
     * @param string|NULL $input_name
     * @param mixed $pasta_id_content_array
     * @param string $upl_path
     * @return bool
     */
    public function send_save($input_name = NULL, $pasta_id_content_array = NULL, $upl_path = NULL){
        
        $saida = FALSE;
        
        // envia os arquivos
        $retorno = $this->send($input_name, $upl_path);
        // percorre o retorno para salvar dados
        foreach($retorno as $file_array){
            
            $saida = $this->save($file_array, $pasta_id_content_array); 
        }
        
//        mybug($saida);
        
        return $saida;
        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Se for input multiples, altera a estrutura de $_FILES para o looping
     * @param string $input_name
     */
    private function prep_multiples($input_name){
        // prepara variáveis
        $name     = $_FILES[$input_name]['name'];
        $type     = $_FILES[$input_name]['type'];
        $tmp_name = $_FILES[$input_name]['tmp_name'];
        $error    = $_FILES[$input_name]['error'];
        $size     = $_FILES[$input_name]['size'];
        // quantos arquivos foram enviados
        $quant = count($tmp_name);
        // temporário
        $temp_array = array();
        // transforma o array multi em bidimensional
        foreach($tmp_name as $c=>$v){
            $temp_array[$input_name.$c] = array(
                'name' => $name[$c],
                'type' => $type[$c],
                'tmp_name' => $tmp_name[$c],
                'error' => $error[$c],
                'size' => $size[$c]
            );

        }

        // atualiza $_FILES
        $_FILES = $temp_array;
//                mybug($_FILES);
        //Then we clean the memory
        unset($temp_array);
    }

    
    // -------------------------------------------------------------------------
    /**
     * VALIDA ARQUIVO SUBMETIDO
     * http://www.beesky.com/newsite/bit_byte.htm << conversão
     * @param type $files
     * @return boolean
     */
    public function valida($files = NULL) {

        if($files === NULL){
            $files = $_FILES;
        } else {
            $files = $_FILES[$files];
        }
        
        $erro = false;
        $ext1 = explode('.', $files['name']);
        $ext = strtolower($ext1[count($ext1) - 1]);


        // erro do servidor
        if ($files['error'] != 0) {
            $erro = 'Erro ao subir arquivo.';
        } else if ($files['size'] > $this->max_size) {
            $erro = 'Arquivo é maior que o permitido.';
        } else if (! in_array($ext, $this->permited_ext)) {
            $erro = 'A extensão do arquivo não é permitida.';
        }

        if ($erro === false) {
            return true;
        } else {
            return $erro;
        }
    }
    
    // -------------------------------------------------------------------------
    /**
     * Abre o arquivo para download e incrementa no BD
     * @param type $arq_id
     */
    public function download($arq_id){
        
        $this_arquivo = $this->get_arquivo($arq_id);
        
        $this->increment_download($this_arquivo);
        
        $this->ci->load->helper('download');
        
        $path = base_url().$this->upl_arqs.'/'.$this_arquivo['nome'];
        
        $data = file_get_contents($path); // Read the file's contents
       
        force_download($this_arquivo['nome'], $data);

        
    }
    
    // -------------------------------------------------------------------------
    /**
     * Recebe os dados do arquivo e incrementa +1
     * @param array $arq_array
     */
    private function increment_download($arq_array){
        
        $plusone = $arq_array['downloaded'] + 1;
        
        $this->ci->db->update('cms_arquivos', array('downloaded' => $plusone), array('id' => $arq_array['id']));
    }


    // -------------------------------------------------------------------------
    
    /**
     * Retorna dados do arquivo
     */
    public function get_arquivo($id) {
        
        // se já existe retorna
        if($this->this_arquivo){
            return $this->this_arquivo;
        }
        
        $this->ci->db->where('id', $id);
        $sql = $this->ci->db->et('cms_arquivos');
        
        if($sql->num_rows() == 0){
            $this->this_arquivo = FALSE;
            return FALSE;
        }
        
        // armazena na memória
        $this->this_arquivo = $sql->row_array();
        // retorna
        return $this->this_arquivo;
    }
    
}