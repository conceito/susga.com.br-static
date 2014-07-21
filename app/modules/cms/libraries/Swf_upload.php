<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Gera o input do arquivo e includes e script do head.
 * Instruções:
 *     - Inclua os scripts no head
 *     - Inclua o campo
 *
 * @package Library
 * @author Bruno Barros <bruno@brunobarros.com>
 * @copyright Copyright (c) 2009
 * @version 1.0
 * @access public
 **/
class Swf_upload {
    var $CI; // instancia do CI
    var $url_swf = "libs/swfupload";// onde estão os arquivos do SWF Upload
    var $mb = 2;                  // quantos Mb são aceitos no upload
    var $upload_proprio = true;   // vais usar a classe do CI, ou o PHP do Swf Upload
    var $file_types = "jpg|doc|pdf"; // Arquivos permitidos
    var $nome_campo = "Filedata"; // nome do input. Padrão 'Filedata'
    var $pasta_destino;           // onde será armazenado
    var $debug = 'false';         // janela de debug
    var $controller_upl = '';     // controller que processa o upload
    // para multi form
    var $file_upload_limit = 0;   // quantos uploads permite numa seção, Só para multi
	var $file_queue_limit = 1;    // quantos são enviados por vez
    var $tipo_swf = '';           // Se for 'multi', senão é normal


    function Swf_upload($rules = array())
    {
        // $this->tb_admins = 'aw_admin';
        $this->CI = &get_instance();
        // armazena as regras
        $this->initialize($rules);
    }
    // --------------------------------------------------------------------
    /**
     * Initialize preferences
     *
     * @access public
     * @param array $
     * @return void
     */
    function initialize($config = array())
    {
        $defaults = array(
		   	'url_swf'           => "libs/swfupload",
            'mb'                => 2,
            'upload_proprio'    => true,
            'file_types'        => "jpg|doc|pdf",
            'nome_campo'        => "Filedata",
            'debug'             => 'false',
            'pasta_destino'     => '',
            'file_upload_limit' => 0,
            'file_queue_limit'  => 1,
            'tipo_swf'          => '',
            'controller_upl'    => ''
            );

        foreach ($defaults as $key => $val) {
            if (isset($config[$key])) {
                $method = 'set_' . $key;

                if (method_exists($this, $method)) {
                    $this->$method($config[$key]);
                } else {
                    $this->$key = $config[$key];
                }
            } else {
                $this->$key = $val;
            }
        }
    }

    /**
	 * Gera o campo form 1 único upload é submetido
	 * @param $mb = quantos MB
	*/
    function campo_swfupload()
    {
        $saida = "<input type=\"text\" id=\"txtFileName\" disabled=\"true\" style=\"border: solid 1px; background-color: #FFFFFF;\" />
<span id=\"spanButtonPlaceholder\"></span> ($this->mb MB max)

<div class=\"flash\" id=\"fsUploadProgress\">
<!-- This is where the file progress gets shown.  SWFUpload doesn't update the UI directly.
        The Handlers (in handlers.js) process the upload events and make the UI updates -->
</div>
<input type=\"hidden\" name=\"hidFileID\" id=\"hidFileID\" value=\"\" /><!-- This is where the file ID is stored after SWFUpload uploads the file and gets the ID back from upload.php -->";

        return $saida;
    }


    /**
	 * Gera o campo form para múltiplos uploads
	 * @param $mb = quantos MB
	*/
    function campo_multi_swfupload()
    {
        $saida = "<div class=\"flash\" id=\"fsUploadProgress\">

	</div>
	<div style=\"padding-left: 5px;\">
	<span id=\"spanButtonPlaceholder\"></span>
	<input id=\"btnCancel\" type=\"button\" value=\"Cancel Uploads\" onclick=\"cancelQueue(swfu);\" disabled=\"disabled\" style=\"margin-left: 2px; height: 22px; font-size: 8pt;\" /> ($this->mb MB max)
	<br />
	</div>";

        return $saida;
    }

    /*
	 * Gera o HEAD com includes e script de config
	 *
	*/
    function head_swfupload()
    {
        // se existir, controla a pasta dos hadllers
        if($this->tipo_swf == '') $dir = ''; // padrão. um no form
		if($this->tipo_swf == 'multi') $dir = '/'.$this->tipo_swf; // pasta com handles próprios

        $includes = $this->scripts(array('swfupload'), $this->url_swf);// padrão
        $includes .= $this->scripts(array('fileprogress',
                'handlers',
                'swfupload.queue'), $this->url_swf . $dir);
        $includes .= $this->estilos(array('default'), $this->url_swf);

        // inicia o script
		$script = "<script type=\"text/javascript\">
		var swfu;
		  // teste = $this->tipo_swf
		window.onload = function () {
			swfu = new SWFUpload({";

        // Utiliza o controller do CI
		if ($this->upload_proprio) {
            $script .= "// Backend settings
				upload_url: \"" . $this->controller_upl .'/'. $this->pasta_destino . "\",	// Relative to the SWF file, you can use an absolute URL as well.";
        }
		// Utiliza o upload.php do swf_upload
		else {
            $script .= "// Upload alternativo
				upload_url: \"" . base_url() . $this->url_swf . "/upload.php?pasta_destino=" . $this->pasta_destino . "\", // alternativo";
        }

        $script .= "//
				file_post_name: \"" . $this->nome_campo . "\",

				// Flash file settings
				file_size_limit : \"" . $this->mb . " MB\",
				file_types : \"" . $this->file_types . "\",	// or you could use something like: \"*.doc;*.wpd;*.pdf\",
				file_types_description : \"Arquivos \",
				file_upload_limit : \"" . $this->file_upload_limit . "\",
				file_queue_limit : \"" . $this->file_queue_limit . "\",
				";
		// handler exclusivo do upload simples
		if($this->tipo_swf == '')$script .= "// Event handler settings
				swfupload_loaded_handler : swfUploadLoaded,";

		$script .="
		   	  	file_dialog_start_handler : fileDialogStart,
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,";

			// handler exclusivo do upload simples
		if($this->tipo_swf == 'multi')$script .= "// Event handler settings
				upload_start_handler : uploadStart,";

		$script .="
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,

				// Button Settings
				button_image_url : \"" . base_url() . $this->url_swf . "/XPButtonUploadText_61x22.png\",	// Relative to the SWF file
				button_placeholder_id : \"spanButtonPlaceholder\",
				button_width: 61,
				button_height: 22,

				// Flash Settings
				flash_url : \"" . base_url() . $this->url_swf . "/swfupload.swf\",

				custom_settings : {
					progress_target : \"fsUploadProgress\",";

		// alternador dependendo do tipo de upload (multi)
		if($this->tipo_swf == '')$script .= "upload_successful : false ";
		if($this->tipo_swf == 'multi')$script .= "
		   	   	cancelButtonId : \"btnCancel\" ";

		$script .= "},

				// Debug settings
				debug: " . $this->debug . "
			});

		};
	</script>";

        return $includes . $script;
    }


    /*
	 * Monta a string de file types
	*/
    function set_file_types($string = '')
    {
        if (strlen(trim($string)) < 3) $string = "jpg|doc|pdf"; // default

        $array = explode('|', $string);

        $saida = '';

        foreach($array as $ext) {
            $saida .= '*.' . $ext . ';';
        }

        $this->file_types = substr($saida, 0, -1); // retira a última virgula e sai

    }

    /*
	 * Monta a string de file types
	*/
    function set_debug($var = false)
    {
        $this->debug = ($var == true) ? 'true' : 'false';
    }

    /**
     * Prepara a pasta destino
     */
    function set_pasta_destino($onde)
    {
        $this->pasta_destino = str_replace('/', '_', $onde);
    }

    /**
     * dependendo do file_upload_limit seta para MULTI
     *
     **/
    function set_tipo_swf(){

     ($this->file_upload_limit > 1) ? $this->tipo_swf = 'multi' : $this->tipo_swf = '';

	}

        /**
     * Se acionada carrega scripts JS dentro da pasta "js" na raiz do site,
     * ou outro local passado na variavel '$local'.
     *
     * @param array $lista : nome dos scripts sem extenção (js)
     * @param string $local : pasta padrão [js]
     * @return string
     */
    function scripts($lista, $local = 'js') {
        if (!is_array($lista)) {
            $lista = array($lista); // se não for, transforma em array
        }
        if (count($lista) == 0) {
            return '';
        }


        $saida = '';
        foreach ($lista as $lib => $nomejs) {

            if (!is_numeric($lib))
                $pasta = base_url() . 'libs/' . $lib;
            else
                $pasta = base_url() . $local;

            $saida .= "<script type=\"text/javascript\" src=\"" . $pasta . "/" . $nomejs . ".js\"></script>\n";
        }

        return $saida;
    }
     /**
     * Se acionada carrega estilos CSS dentro da pasta "css" na raiz do site,
     * ou outro local passado na variavel '$local'.
     *
     * @param array $lista : nome dos estilos sem extenção (css)
     * @param string $local : pasta padrão [css]
     * @param string $media : tipo de css, 'screen' é o padrão
     * @return string
     */
    function estilos($lista, $local = 'css', $media = 'screen') {
        if (!is_array($lista)) {
            $lista = array($lista); // se não for, transforma em array
        }
        if (count($lista) == 0) {
            return '';
        }

        $pasta = base_url() . $local;
        $saida = '';
        foreach ($lista as $nomes) {
            $saida .= "<link href=\"" . $pasta . "/" . $nomes . ".css\" rel=\"stylesheet\" type=\"text/css\" media=\"$media\" />\n";
        }

        return $saida;
    }
}

?>