<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


if(! function_exists('view_exist'))
{
	/**
	 * check if view exists
	 * if it has 'cms' at the beginning it is cms module
	 *
	 * @param string $viewPath
	 * @return bool
	 */
	function view_exist($viewPath = '')
	{
		$module = 'views/';// no module
		$path = trim($viewPath, '/');
		if(substr($viewPath, 0, 3) === 'cms')
		{
			$module = 'modules/cms/views/';
			$path = trim(substr($viewPath, 3), '/');
		}

		if (file_exists(APPPATH .$module. $path . EXT))
		{
			return true;
		}

		return false;
	}
}

/**
 * Entra a string 'tipo' e retorna o número da revisão
 */
if (!function_exists('revision_num'))
{

    function revision_num($rev_string)
    {
        $ex = explode('-', $rev_string);
        return $ex[2];
    }

}

// pasta de imagens
if (!function_exists('cms_img'))
{

    function cms_img($local = 'ci_itens/img')
    {

        $url = base_url() . app_folder() . $local;
        // ensure there's a trailing slash
        $url = rtrim($url, '/') . '/';

        return $url;
    }

}


/**
 * Entra a string 'tipo' e retorna o número do post original
 */
if (!function_exists('revision_id'))
{

    function revision_id($rev_string)
    {
        $ex = explode('-', $rev_string);
        return $ex[0];
    }

}

/**
 * Monta inputs de filtros
 */
if (!function_exists('local_search'))
{

    function local_search($config = array())
    {

        $ci = & get_instance();

        // configurações
        $campo = (isset($config['campo'])) ? $config['campo'] : '';
        $type = (isset($config['type'])) ? $config['type'] : 'text';
        $class = (isset($config['class'])) ? $config['class'] : '';
        $style = (isset($config['style'])) ? $config['style'] : '';
        $id = (isset($config['id'])) ? $config['id'] : '';
        $options = (isset($config['options'])) ? $config['options'] : false;

        // valor
        $value = ''; // init
        $uri = $ci->uri->to_array('filter_' . $campo);
        $value = $uri['filter_' . $campo];

        // tratamento para input
        if ($type == 'date' && strlen($value) == '10')
        {
            $value = formaPadrao($value);
        }

        // valor via POST tem prioridade
        if (isset($_POST['filter_' . $campo]))
        {
            $value = $_POST['filter_' . $campo];
        }

        // se houverem options, o input será um combobox
        if ($options)
        {
            $extra = 'id="' . $id . '" class="' . $class . '" style="' . $style . '"';
//            $options = array_merge(array(''=>''), $options);
            $options = array('' => '') + $options;

            $input = form_dropdown('filter_' . $campo, $options, $value, $extra);
        }
        // senão um input text
        else
        {
            $input = '<input type="text" name="filter_' . $campo . '" class="' . $class . '" id="' . $id . '" value="' . $value . '" size="100%" style="' . $style . '">';
        }



        return $input;
    }

}

/**
 * Indents a flat JSON string to make it more human-readable.
 *
 * @param string $json The original JSON string to process.
 *
 * @return string Indented version of the original JSON string.
 */
if (!function_exists('json_indent'))
{


    function json_indent($json)
    {

        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $indentStr = '  ';
        $newLine = "\n";
        $prevChar = '';
        $outOfQuotes = true;

        for ($i = 0; $i <= $strLen; $i++)
        {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\')
            {
                $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element, 
                // output a new line and indent the next line.
            }
            else if (($char == '}' || $char == ']') && $outOfQuotes)
            {
                $result .= $newLine;
                $pos--;
                for ($j = 0; $j < $pos; $j++)
                {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element, 
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes)
            {
                $result .= $newLine;
                if ($char == '{' || $char == '[')
                {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++)
                {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }

}

// ------------------------------------------------------------------------

/**
 * Cms URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if (!function_exists('cms_url'))
{

    function cms_url($uri = '')
    {
        $CI = & get_instance();
        return $CI->config->site_url($uri);
    }

}

// ------------------------------------------------------------------------

/**
 * cms_uri_url
 *
 * Cria link com Base URL e acrescenta variáveis da uri.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if (!function_exists('cms_uri_url'))
{

    function cms_uri_url($uri = '')
    {
        $CI = & get_instance();
        $uris = $CI->uri->segment_array();
        $uris = array_slice($uris, 3);

        return $CI->config->site_url($uri) . '/' . implode('/', $uris);
    }

}

// ----------------------------------------------------------------------------
// 
//////    formato brasilleiro
if (!function_exists('moneyBR'))
{

    function moneyBR($str)
    {
        // primeiro retira
        $str = number_format($str, 2, ',', '.');
        return $str;
    }

}

// ----------------------------------------------------------------------------

/**
 * Retorna uma sequencia de números qualquer no padrão: 0.00 
 */
if (!function_exists('moneyFormat'))
{

    function moneyFormat($param)
    {

        // verifica se tem centavos com vírgula, 999,99
        if (substr($param, -3, 1) == ',')
        {
            $param = str_replace(array(','), '.', $param);
        }

        // primeiro retira
        $param = str_replace(array(','), '', $param);
        return number_format((double) $param, 2, '.', '');
    }

}


// ----------------------------------------------------------------------------

/**
 * Converte cor no formato hexadecimal para RGB. 
 */
if (!function_exists('hex2rgb'))
{

    function hex2rgb($hex, $format = 'string')
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3)
        {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        }
        else
        {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array($r, $g, $b);

        if ($format == 'string')
        {
            return implode(",", $rgb);
        }
        else if ($format == 'array')
        {
            return $rgb;
        }
    }

}



// ---------------------------------------------------------------------------


if (!function_exists('str_hierarchy'))
{

    function str_hierarchy($level = NULL)
    {

        if ($level == NULL)
        {
            $level = 0;
        }

        $str_hierarchy = str_repeat('.&nbsp;', $level) . '&mdash; '; // &mdash;   &nbsp;&nbsp; ∟
//        $str_hierarchy = substr($str_hierarchy, 0, -1) . '∟';
        if ($level == 0)
        {
            $str_hierarchy = '';
        }

        return $str_hierarchy;
    }

}

// --------------------------------------------------------------------------
// transforma telefone SQL (nn|nnnn-nnnn) para input ((nn)nnnn-nnnn)
if (!function_exists('tel_input'))
{

    function tel_input($tel)
    {
        if (strlen($tel) < 12
        )
            return '';
        $p = explode('|', $tel);
        $saida = '(' . $p[0] . ')' . $p[1];
        return $saida;
    }

}

// --------------------------------------------------------------------------
// transforma telefone input ((nn)nnnn-nnnn) para SQL (nn|nnnn-nnnn)
if (!function_exists('tel_sql'))
{

    function tel_sql($tel)
    {
        if (strlen(trim($tel)) < 13
        )
            return '';
        $saida = substr(trim($tel), 1, 2) . '|' . substr(trim($tel), -9);
        return $saida;
    }

}

// --------------------------------------------------------------------------

/**
 * Retorna os dados do grupo principal (último) 
 */
if (!function_exists('lastGrupo'))
{

    function lastGrupo($grupoParents)
    {
        if ($grupoParents === false)
        {
            return array('grupoCor2' => '');
        }
        else
        {

            $ttl = count($grupoParents);
            $last = $grupoParents[$ttl - 1];

            return $last;
        }
    }

}

// --------------------------------------------------------------------------
// retorna a língua ativa, se não houver coloca o PT como default
if (!function_exists('get_lang'))
{

    function get_lang()
    {
        $ci = &get_instance();
        $lsess = $ci->phpsess->get('lang', 'cms');
        if (strlen(trim($lsess)) < 2)
        {
            $ci->phpsess->save('lang', 'pt', 'cms');
            return 'pt';
        }
        return $lsess;
    }

}

// --------------------------------------------------------------------------

/**
 * Salva o texto no BD como UTF8, menos as aspas..
 *
 * @param mixed $str
 * @return
 * */
if (!function_exists('campo_texto_utf8'))
{

    function campo_texto_utf8($str)
    {
        if (ENVIRONMENT == 'development')
        {
            $saida = html_entity_decode($str, ENT_NOQUOTES);
        }
        else
        {
            $saida = utf8_encode(html_entity_decode($str, ENT_NOQUOTES));
        }
        return $saida;
    }

}

if(! function_exists('clean_html_field'))
{
    /**
     * clean html tags os html fields value
     *
     * @param string $htmlValue
     * @return mixed
     */
    function clean_html_field($htmlValue = '')
    {
        $v = strip_tags(campo_texto_utf8($htmlValue));

        return str_replace(array('&nbsp;'), array(' '), $v);
    }
}

if(! function_exists('cms_html_entity_decode'))
{
    function cms_html_entity_decode($str = '')
    {
        if (ENVIRONMENT == 'development')
        {
            $str = html_entity_decode($str, ENT_NOQUOTES);
        }
        else
        {
            $str = utf8_encode(html_entity_decode($str, ENT_NOQUOTES));
        }

        return $str;
    }
}

// --------------------------------------------------------------------------

/**
 * calcula o percentual
 *
 * @param mixed $str
 * @return
 * */
if (!function_exists('percentual'))
{

    function percentual($total, $parte)
    {
        if ($total < 1
        )
            return 0;
        $saida = ($parte * 100) / $total;
        return floor($saida);
    }

}

// --------------------------------------------------------------------------

/**
 * Prepara dados do form para salvar no bd
 */
if (!function_exists('prep_rel_to_sql'))
{

    function prep_rel_to_sql($rel)
    {

        if (!is_array($rel))
        {
            return 0;
        }

        $result = implode(',', $rel);

        return $result;
    }

}

// --------------------------------------------------------------------------

/**
 * Faz o cálculo de retorno entre visualização e cliques
 */
if (!function_exists('ctr'))
{

    function ctr($clicks = NULL, $views = NULL)
    {

        if ($clicks == NULL || $views == NULL || ($clicks == 0 && $views == 0))
        {
            return 0;
        }

        $result = round(($clicks / $views) * 100, 1);

        return $result;
    }

}

// --------------------------------------------------------------------------
/**
 * Converte bytes em Mb...
 */
if (!function_exists('format_bytes'))
{

    function format_bytes($size, $precision = 1)
    {
        $base = log($size) / log(1024);
        $suffixes = array('B', 'Kb', 'Mb', 'Gb', 'Tb');

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }

}



if (!function_exists('file_append_str'))
{

    /**
     * Append a string rigth before the extension
     * ex: file.jpg => file_append.jpg
     * 
     * @param string $file
     * @param string $append
     */
    function file_append_str($file, $append = '')
    {
        $pieces = explode('.', $file);

        $ext = $pieces[count($pieces) - 1];
        unset($pieces[count($pieces) - 1]);

        $name = implode('', $pieces);

        $appended = $name . $append . '.' . $ext;
        return $appended;
    }

}


if (!function_exists('file_prepend_str'))
{

    /**
     * Prepend a string rigth before the file name
     * ex: file.jpg => append_file.jpg
     * 
     * @param string $file
     * @param string $prepend
     */
    function file_prepend_str($file, $prepend = '')
    {
        $pieces = explode('.', $file);

        $ext = $pieces[count($pieces) - 1];
        unset($pieces[count($pieces) - 1]);

        $nameAndPath = implode('', $pieces);

        // separate name from path
        $pieces2 = explode('/', $nameAndPath);

        // has folder structure
        if (count($pieces2) > 1)
        {
            $name = $pieces2[count($pieces2) - 1];
            unset($pieces2[count($pieces2) - 1]);

            $path = implode('/', $pieces2) . '/';
        }
        else
        {
            $path = '';
            $name = implode('', $pieces2);
        }

        $prepended = $path . $prepend . $name . '.' . $ext;
        return $prepended;
    }

}


if (!function_exists('append_upload_folder'))
{

    /**
     * Recebe o path e incrementa com  as pastas internas
     *
     * @param string $fisicPath
     * @return string
     */
    function append_upload_folder($fisicPath)
    {
        $fullFolderName = rtrim($fisicPath, '/') . '/' . date("Y") . '/' . date("m");

        if (!wp_mkdir_p($fullFolderName))
        {
            return $fisicPath;
        }

        return $fullFolderName;
    }

}

if (!function_exists('prepend_upload_file'))
{

    /**
     * Adiciona o caminho de pastas internas antes do nome do arquivo
     *
     * @param string $fileName
     * @return string
     */
    function prepend_upload_file($fileName)
    {
        $prepended = date("Y") . '/' . date("m") . '/' . rtrim($fileName, '/');

        return $prepended;
    }

}


if(! function_exists('wp_mkdir_p'))
{
    /**
     * Recursive directory creation based on full path.
     *
     * Will attempt to set permissions on folders.
     *
     * @since 2.0.1
     *
     * @param string $target Full path to attempt to create.
     * @return bool Whether the path was created. True if path already exists.
     */
    function wp_mkdir_p($target)
    {
        $wrapper = null;

        // strip the protocol
        $wrappers    = stream_get_wrappers();
        $wrappers_re = '(' . join('|', $wrappers) . ')';

        if (preg_match("!^$wrappers_re://!", $target) === 1)
        {
            list($wrapper, $target) = explode('://', $target, 2);
        }

        // from php.net/mkdir user contributed notes
        $target = str_replace('//', '/', $target);

        // put the wrapper back on the target
        if ($wrapper !== null)
        {
            $target = $wrapper . '://' . $target;
        }

        // safe mode fails with a trailing slash under certain PHP versions.
        $target = rtrim($target, '/'); // Use rtrim() instead of untrailingslashit to avoid formatting.php dependency.
        if (empty($target))
        {
            $target = '/';
        }

        if (file_exists($target))
        {
            return @is_dir($target);
        }

        // We need to find the permissions of the parent folder that exists and inherit that.
        $target_parent = dirname($target);
        while ('.' != $target_parent && !is_dir($target_parent))
        {
            $target_parent = dirname($target_parent);
        }

        // Get the permission bits.
        $dir_perms = false;
        if ($stat = @stat($target_parent))
        {
            $dir_perms = $stat['mode'] & 0007777;
        }
        else
        {
            $dir_perms = 0777;
        }

        if (@mkdir($target, $dir_perms, true))
        {

            // If a umask is set that modifies $dir_perms, we'll have to re-set the $dir_perms correctly with chmod()
            if ($dir_perms != ($dir_perms & ~umask()))
            {
                $folder_parts = explode('/', substr($target, strlen($target_parent) + 1));
                for ($i = 1; $i <= count($folder_parts); $i++)
                {
                    @chmod($target_parent . '/' . implode('/', array_slice($folder_parts, 0, $i)), $dir_perms);
                }
            }

            return true;
        }

        return false;
    }
}


if (!function_exists('key_from_array'))
{
    /**
     * return items from a associative array using dot pattern
     *
     * key_from_array('my.dot.item', array());
     *
     * @param null $key
     * @param array $array
     * @return mixed
     */
    function key_from_array($key = null, $array = array())
    {
        $c = $array;

        if ($key !== null)
        {
            $a = explode('.', $key);
            for ($x = 0; $x < count($a); $x++)
            {
                // se não existir o índice, termina
                if (!isset($c[$a[$x]]))
                {
                    break;

                    return null;
                }
                $c = $c[$a[$x]];
                // no final
                if ($x === count($a) - 1)
                {
                    return $c;
                }
            }
        }
        else
        {
            return $c;
        }
    }
}
