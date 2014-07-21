<?php
class Swfupl extends MX_Controller{
    function  __construct() {
        parent::__construct();

        /*
         * CARREGA CONFIGURAÇÕES DO CMS
         */
        $this->load->config('cmsConfig');
        /*
         * CARREGA CLASSES GENÉRICAS PARA CMS
         */
       
        $this->load->helper(array('cms/cmshelper'));
        $this->load->library(array('cms/layout_cms', 'cms/cms_libs'));

        $this->pastaImg = fisic_path() . $this->config->item('upl_imgs');
        $this->pastaArq = fisic_path() . $this->config->item('upl_arqs');
        $this->pesoMaxImg = $this->config->item('peso_max_upl');
        $this->pesoMaxArq = $this->config->item('peso_max_arqs');
        $this->extImg = $this->config->item('upl_ext');
        $this->extArq = $this->config->item('upl_ext_arqs');
        $this->formaUpl = $this->cms_libs->conf(7); // pegar no BD 0 ou 1

        
    }
    
    function debug($var = ''){
        $ext = ($var == 'co:0') ? $this->extImg : $this->extArq;
        $pasta = ($var == 'co:0') ? $this->pastaImg : $this->pastaArq;
        $peso = ($var == 'co:0') ? $this->pesoMaxImg : $this->pesoMaxArq;
        echo '<pre>';
        var_dump($pasta);
        
    }

    function fazSwfUpload($var = '')
    {
        // -- recebe variaveis -- //
        // $var = $this->cms_uri->to_array(3, array('co', 'id', 'pasta', 'onde'));
        $ext = ($var == 'co:0') ? $this->extImg : $this->extArq;
        $pasta = ($var == 'co:0') ? $this->pastaImg : $this->pastaArq;
        $peso = ($var == 'co:0') ? $this->pesoMaxImg : $this->pesoMaxArq;
        // recomponho entrada da pasta
  
        $pasta = append_upload_folder($pasta);
        
        $config['file_name'] = $this->name_cleaner($_FILES['Filedata']['name']);
        $config['upload_path'] = $pasta; //'C:\apache2triad\htdocs\cms4.com.br/upl/imgs';
        $config['allowed_types'] = '*'; //$ext; // jpg|gif|...
        $config['max_size'] = $peso;
        $config['remove_spaces'] = true;
        $this->load->library('upload', $config);

        $this->upload->do_upload('Filedata'); // sobre o arquivo
        $res = $this->upload->data();

//        log_message('error', 'SWFUPL-pasta: '.$pasta);

//        echo 'teste';
//        exit;

        echo $res['file_name']; // retorno para o SWF upload
    }

    function name_cleaner($texto = '')
    {
        // tirando os acentos
        // -----------------------------------------------------
        $array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
        $array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
        $texto = str_replace($array1, $array2, $texto);
        $texto = strtolower($texto);

        $texto = url_title($texto);

        return $texto;
    }
}
