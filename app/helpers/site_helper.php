<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');




if(! function_exists('clean_html_to_db'))
{
	/**
	 * clear atributes from these tags:
	 * - p
	 * - span
	 * remove comments
	 *
	 * @param string $str
	 * @return mixed
	 */
	function clean_html_to_db($str = '')
	{
		$str = preg_replace("/<span[^>]+\>/", "<span>", $str);
		$str = preg_replace("/<p[^>]+\>/", "<p>", $str);
		return preg_replace("/<!--(.*?)-->/", "", $str);
	}
}



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


if(!function_exists('opt'))
{
    /**
     * Retorna opções do CMS.
     * 
     * <code>
     * // usage:
     * echo opt('address');
     * // or
     * echo opt('address', 'no address');
     * // or
     * echo opt('address', true);
     * // or
     * echo opt('address', 'no address', true);
     * </code>
     * 
     * @param string $id
     * @param string $default
     * @param bool $parseHtml
     * @return string
     */
    function opt($id, $default = '', $parseHtml = false)
    {
        $ci = &get_instance();
        return $ci->cms_options->get($id, $default, $parseHtml);
    }
}



if(!function_exists('app_folder')){
    function app_folder() {
        $ci = &get_instance();
        if($ci->config->item('instalation_folder')){
            return trim($ci->config->item('instalation_folder'), '/') . '/';
        } else {
            return '';           
        }
    }
}

if( !function_exists('is_home'))
{
    function is_home()
    {
        $ci = &get_instance();
        $uri = $ci->uri->segment(1);
        return ($uri == false || $uri == 'inicio') ? true : false;
    }
}

if( !function_exists('is_page'))
{
    function is_page($pageuri = '')
    {
        if($pageuri === '')
        {
            return !is_home();
        }
        else
        {
            $ci = &get_instance();
            $uri = $ci->uri->segment(1);
            
            return ($uri === $pageuri) ? true : false;
        }
    }
}


/**
 * Indents a flat JSON string to make it more human-readable.
 *
 * @param string $json The original JSON string to process.
 *
 * @return string Indented version of the original JSON string.
 */
if (!function_exists('json_indent')) {


    function json_indent($json) {

        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $indentStr = '  ';
        $newLine = "\n";
        $prevChar = '';
        $outOfQuotes = true;

        for ($i = 0; $i <= $strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

                // If this character is the end of an element, 
                // output a new line and indent the next line.
            } else if (($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element, 
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }

}

// -----------------------------------------------------------------------------
/**
 * Recebe o array completo do post
 */
if(!function_exists('post_tags_html')){
    function post_tags_html($post, $base_uri = '', $tag = 'a'){
        
        if(! isset($post['post_tags']) || $post['post_tags'] === FALSE){
            return FALSE;
        }
        
        $ci = &get_instance();
        $uri = $ci->uri->to_array(array('tag'));
        
        // se não for declarado, pega a URI
        if($base_uri == ''){
            $base_uri = $ci->uri->segment(1);
        }
        
        $html = '<div class="post-tags">';
        
        foreach($post['post_tags'] as $row){
            
            // verifica se esta TAGS está selecionada na URI
            /*'id' =>  '54' 
                'nick' =>  'post-tag-a' 
                'nome' =>  'post tag a' 
                'cor1' =>  '#d3d3d3' 
                'cor2' =>  '#282626'
             */
            $sel = '';
            if($uri['tag'] == $row['id'] || $uri['tag'] == $row['nick']){
                $sel = 'active';
            }
            
            $html .= '<'.$tag.' href="'.  site_url($base_uri.'/tag:'.$row['nick']).'" class="label '.$sel.'">#'.$row['nome'].'</'.$tag.'>';
        }
        
        $html .= '</div>';
        
        return $html;
        
    }
}


// -----------------------------------------------------------------------------
// Conversão de caracteres especiais quando trabalha com várias línguas
if (!function_exists('lang_to_utf8')) {

    function lang_to_utf8($str) {
        return iconv('ISO-8859-1', 'UTF-8', $str);
    }

}


if (!function_exists('imgLimitWHtags')) {

    /**
     * Gera as tags width="" height="" para limitar o tamanho das imagens de acordo
     * com o limite passado no terceiro parâmetro.
     * @param type $original_width
     * @param type $original_height
     * @param type $limite
     * @param type $inset
     * @return string 
     */
    function imgLimitWHtags($original_width, $original_height, $limite, $inset = 'inner') {


        // verifica o lado maior
        if ($original_height > $original_width) {
            // em pé
            $razao = $original_height / $original_width;

            if ($inset == 'inner') {
                $w = $limite / $razao;
                $h = $limite;
            } else if ($inset = 'outer') {
                $w = $limite;
                $h = $limite * $razao;
            }
        } else {
            // deitada
            $razao = $original_width / $original_height;

            if ($inset == 'inner') {
                $w = $limite;
                $h = $limite / $razao;
            } else if ($inset = 'outer') {
                $w = $limite * $razao;
                $h = $limite;
            }
        }

        $ret = 'width="' . round($w) . '" height="' . round($h) . '"';

        return $ret;
    }

}



if (!function_exists('mybug')) {

    function mybug($var, $print = false) {
        echo '<pre>';

        if( $print ){
            print_r($var);
        } else {
            var_dump($var);
        }
        exit;
    }
    
    function dd($var, $print = false)
    {
        mybug($var, $print);
    }

}

if(!function_exists('d'))
{
    function d($var = '')
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }
}

// pasta de imagens
if (!function_exists('img')) {

    function img($local = 'assets/img') {

        $url = base_url() . app_folder() . $local;
        // ensure there's a trailing slash
        $url = rtrim($url, '/') . '/';

        return $url;
    }

}

// Pega o nome da imagem e acrescenta _thumb
if (!function_exists('thumb')) {

    function thumb($str = '') {
        //if(strlen($str) < 4) return 'padrao.jpg';
        if (strlen($str) < 4)
            return '';
        $thumb = substr($str, 0, -4) . '_thumb' . substr($str, -4);
        return $thumb; //<<--
    }

}
// Pega o nome da imagem e acrescenta _med
if (!function_exists('med')) {

    function med($str = '') {
        //if(strlen($str) < 4) return 'padrao.jpg';
        if (strlen($str) < 4)
            return '';
        $thumb = substr($str, 0, -4) . '_med' . substr($str, -4);
        return $thumb; //<<--
    }

}
// Pega o nome da imagem e acrescenta _max
if (!function_exists('grande')) {

    function grande($str = '') {
        //if(strlen($str) < 4) return 'padrao.jpg';
        if (strlen($str) < 4)
            return '';
        $thumb = substr($str, 0, -4) . '_max' . substr($str, -4);
        return $thumb; //<<--
    }

}

// Coloca os pontos e - na string 99999999999999
if (!function_exists('cpfcarac')) {

    function cpfcarac($str = '00000000000') {

        $cpf = substr($str, 0, 3) . '.';
        $cpf .= substr($str, 3, 3) . '.';
        $cpf .= substr($str, 6, 3) . '-';
        $cpf .= substr($str, -2);
        return $cpf; //<<--
    }

}
// calcula o peso total de um determinado produto
if (!function_exists('pesocalc')) {

    function pesocalc($peso_uni, $quant = 1, $ret = 'kg') {

        $peso = str_replace(array('.', ','), '', $peso_uni);
        $peso = $peso * $quant;
        if ($ret == 'kg') {// kilos
            $peso = $peso / 1000;
        } else {// gramas
            $peso = $peso;
        }
        return $peso; //<<--
    }

}

// reduz o nome noprimeiro e último nomes. Pode abreviar os intermediários
if (!function_exists('sobrenome')) {

    function sobrenome($nomecompleto, $intermediarios = false) {
        $partes = explode(' ', trim($nomecompleto));

        $ttl = count($partes);

        $rejeitar = array('de', 'do', 'da');

        $saida = $partes[0] . ' ';

        if ($intermediarios && $ttl > 2) {// mais de dois nomes
            for ($x = 1; $x < ($ttl - 1); $x++) {

                $sob = $partes[$x];
                // valida str
                if (strlen($sob) < 2 || array_search($sob, $rejeitar) !== false) {
                    continue;
                }

                $sob = strtoupper(substr($sob, 0, 1));
                $saida .= $sob . '. ';
            }
        }
        $saida .= $partes[$ttl - 1];

        return $saida;
    }

}

// retorna o caminho físico
if (!function_exists('fisic_path')) {

    function fisic_path() {
        $cam = trim(trim(dirname(FCPATH), '/'), '\\');
        $cam2 = trim(trim(FCPATH, '/'), '\\');
//                deprecated
	if  ($_SERVER['HTTP_HOST'] == "localhost"){
            return $cam2 . '/';
        }            
        else {
            return '/'.$cam2.'/';
        }

        return $cam2 . '/';
    }

}

//----------------------------------------------------------------------------

if(!function_exists('get_meta')){
    
    /**
     * Recebe as metas do conteúdo e retorna baseado nos argumentos.
     * 
     * @param array $dataProvider
     * @param string $key
     * @param string $type
     * @param boolean $retValue Retorna o array da meta, ou a meta_value
     * @return boolean
     */
    function get_meta($dataProvider, $key = null, $type = null, $retValue = false)
    {
        if(!is_array($dataProvider)){
            return false;
        }
        
        if($key === null && $type === null){
            return $dataProvider;
        }
        
        $selected = array();
        
        // percorre metas
        foreach ($dataProvider as $meta)
        {
            if(strlen($key) > 0 && $type === null){
                if($meta['meta_key'] === $key){
                    $selected[] = $meta;
                }
            }
            else {
                if($meta['meta_key'] === $key && $meta['meta_type'] === $type){
                    $selected[] = $meta;
                }
            }
        }
        
        if(count($selected) == 1){
            return ($retValue) ? $selected[0]['meta_value'] : $selected[0];
        } else if(count($selected) > 1){
            return $selected;
        } else {
            return false;
        }
           
    }
    
}


/**
 * Função que usa o Google API para minificar e combinar arquivos CSS e JS.
 * Arquivos API na pasta ci_itens/min
 * O cache está na pasta upl/arqs, ver em ci_itens/min/config.php
 *
 * @param string $tipo [css ou js]
 * @param array $lista
 * @param string $local
 * @param string $media
 * @return string
 * */
if (!function_exists('minify')) {

    function minify($tipo = '', $lista = array(), $local = '', $media = 'all') {
        if (!is_array($lista)) {
            $lista = array($lista); // se não for, transforma em array
        }
        if (count($lista) == 0 || $tipo == '') {
            return '';
        }
        $ci = & get_instance();
        $pathMin = base_url() . 'libs/minify/?f='; // API minify
        $comple = trim($ci->config->item('base_url_complemento'), '/'); // pasta dentro do root (app/config/config.php)
        $pasta = trim($comple . '/' . trim($local, '/'), '/');


        $saida = '';
        foreach ($lista as $nome) {
            $saida .= trim($nome) . '.' . $tipo . ',';
        }
        $saida = trim($saida, ',');

        if ($tipo == 'css') {
            $minifyed = "<link href=\"" . $pathMin . $saida . '&b=' . $pasta . "\" rel=\"stylesheet\" type=\"text/css\" media=\"$media\" />\n";
        } else if ($tipo == 'js') {
            $minifyed = "<script type=\"text/javascript\" src=\"" . $pathMin . $saida . '&b=' . $pasta . "\"></script>\n";
        }

        return $minifyed;
    }

}


if (!function_exists('generate_menu')) {
    
     function gm_check_children($parent_id, $items)
     {
         if(!is_array($items))
         {
             return false;
         }
         
         foreach($items as $item)
         {
             if($item['rel'] == $parent_id)
             {
                 return true;
             }
         }
         return false;
     }

    function generate_menu($menu_id, $classId = '.menu', $lang = 'pt') {

        // get CI
        $ci =& get_instance();
        // saida
        $saida = '';

        // pega todos os itens de menu
//        * "titulo" => rotulo do menu
//        * "nick"   => url do conteudo
//        * "rel"    => ID do item de menu pai
//        * "visitas"=> ID do conteudo
//        * "txt"    => title
//        * "resumo" => CSS
//        * "tags"   => target
        $ci->db->where('modulo_id', 37);
        $ci->db->where('tipo', 'conteudo');
        $ci->db->where('grupo', $menu_id);
        $ci->db->where('status', 1);
        $ci->db->where('lang', $lang);
        $ci->db->order_by('ordem');
        $ci->db->select('id, nick, titulo, rel, resumo, txt, tags');
        $sql = $ci->db->get('cms_conteudo');

        if ($sql->num_rows() > 0) {

            // todos os itens do menu
            $itens = $sql->result_array();

            // separa os itens de nível == 0
            $nivel_0 = array();

            // parseia itens de menu nível == 0
            foreach ($itens as $item) {

                // separa os de nível == 0
                if ($item['rel'] == 0) {

                    $nivel_0[] = $item;
                }
            }

            // define se é classe ou ID do menu
            if (substr($classId, 0, 1) == '.') {
                $classId = 'class="' . substr($classId, 1) . '"';
            } else {
                $classId = 'id="' . substr($classId, 1) . '"';
            }

            // inicia a saida de dados
            $saida .= '<ul ' . $classId . '>' . PHP_EOL;

            // 2º parseamento - insere de forma recursiva os subitens
            for ($x = 0; $x < count($nivel_0); $x++) {
                
                $hasChildren = gm_check_children($nivel_0[$x]['id'], $itens);

                $item_id = $nivel_0[$x]['id'];
                $rotulo = $nivel_0[$x]['titulo'];
                $url = site_url($nivel_0[$x]['nick']);
                $title = $nivel_0[$x]['txt'];
                $css = $nivel_0[$x]['resumo'] . ($hasChildren) ? ' dropdown' : '';
                $target = (strlen($nivel_0[$x]['tags']) > 3) ? 'target="' . $nivel_0[$x]['tags'] . '"' : '';
                $pai_id = $nivel_0[$x]['rel'];
                $a_attr = ($hasChildren) ? 'data-toggle="dropdown"' : '';
                if($hasChildren) $rotulo = $rotulo . '<b class="caret"></b>';
                
                // ------- monta link ------------
                $saida .= '<li class="' . $css . '"><a href="' . $url . '" ' . $target . ' title="' . $title . '" '.$a_attr.'>' . $rotulo . '</a>';
                // -------------------------------
                // verifica se tem subníveis == 1
                $li_nivel_1 = false;
                for ($j = 0; $j < count($itens); $j++) {
                    
                    $hasChildren = gm_check_children($itens[$j]['id'], $itens);

                    $item_id_1 = $itens[$j]['id'];
                    $rotulo_1 = $itens[$j]['titulo'];
                    $url_1 = site_url($itens[$j]['nick']);
                    $title_1 = $itens[$j]['txt'];
                    $css_1 = $itens[$j]['resumo'] . ($hasChildren) ? ' dropdown' : '';
                    $target_1 = (strlen($itens[$j]['tags']) > 3) ? 'target="' . $itens[$j]['tags'] . '"' : '';
                    $pai_id_1 = $itens[$j]['rel'];
                    $a_attr = ($hasChildren) ? 'data-toggle="dropdown"' : '';
                    if($hasChildren) $rotulo_1 = $rotulo_1 . '<b class="caret"></b>';

                    if ($pai_id_1 == $item_id) {
                        // ------- monta link ------------
                        $li_nivel_1 .= '<li class="' . $css_1 . '"><a href="' . $url_1 . '" ' . $target_1 . ' title="' . $title_1 . '" '.$a_attr.'>' . $rotulo_1 . '</a>';
                        // -------------------------------
                        // verifica se tem subníveis == 2
                        $li_nivel_2 = false;
                        for ($u = 0; $u < count($itens); $u++) {

                            $hasChildren = gm_check_children($itens[$u]['id'], $itens);
                            
                            $item_id_2 = $itens[$u]['id'];
                            $rotulo_2 = $itens[$u]['titulo'];
                            $url_2 = site_url($itens[$u]['nick']);
                            $title_2 = $itens[$u]['txt'];
                            $css_2 = $itens[$u]['resumo'] . ($hasChildren) ? ' dropdown' : '';
                            $target_2 = (strlen($itens[$u]['tags']) > 3) ? 'target="' . $itens[$u]['tags'] . '"' : '';
                            $pai_id_2 = $itens[$u]['rel'];
                            $a_attr = ($hasChildren) ? 'data-toggle="dropdown"' : '';
                            if($hasChildren) $rotulo_2 = $rotulo_2 . '<b class="caret"></b>';

                            if ($pai_id_2 == $item_id_1) {

                                // ------- monta link ------------
                                $li_nivel_2 .= '<li class="' . $css_2 . '"><a href="' . $url_2 . '" ' . $target_2 . ' title="' . $title_2 . '" '.$a_attr.'>' . $rotulo_2 . '</a>';
                                // -------------------------------
                                // verifica se tem subníveis == 3
                                $li_nivel_3 = false;
                                for ($p = 0; $p < count($itens); $p++) {

                                    $item_id_3 = $itens[$p]['id'];
                                    $rotulo_3 = $itens[$p]['titulo'];
                                    $url_3 = site_url($itens[$p]['nick']);
                                    $title_3 = $itens[$p]['txt'];
                                    $css_3 = $itens[$p]['resumo'];
                                    $target_3 = (strlen($itens[$p]['tags']) > 3) ? 'target="' . $itens[$p]['tags'] . '"' : '';
                                    $pai_id_3 = $itens[$p]['rel'];

                                    if ($pai_id_3 == $item_id_2) {

                                        // ------- monta link ------------
                                        $li_nivel_3 .= '<li class="' . $css_3 . '"><a href="' . $url_3 . '" ' . $target_3 . ' title="' . $title_3 . '">' . $rotulo_3 . '</a>';
                                        // -------------------------------
                                        // saida li nivel = 2
                                        $li_nivel_3 .= '</li>' . PHP_EOL;
                                    }
                                }
                                // se existir conteudo no nível = 3 acrescenta na saida do nível 2
                                if ($li_nivel_3) {

                                    $li_nivel_2 .= PHP_EOL . '<ul class="sub-3 dropdown-menu">' . PHP_EOL;
                                    $li_nivel_2 .= $li_nivel_3;
                                    $li_nivel_2 .= '</ul>' . PHP_EOL;
                                }


                                // saida li nivel = 2
                                $li_nivel_2 .= '</li>' . PHP_EOL;
                            }
                        }
                        // se existir conteudo no nível = 2 acrescenta na saida do nível 1
                        if ($li_nivel_2) {

                            $li_nivel_1 .= PHP_EOL . '<ul class="sub-2 dropdown-menu">' . PHP_EOL;
                            $li_nivel_1 .= $li_nivel_2;
                            $li_nivel_1 .= '</ul>' . PHP_EOL;
                        }

                        // saida li nivel = 1
                        $li_nivel_1 .= '</li>' . PHP_EOL;
                    }
                }
                // se existir conteudo no nível = 1 acrescenta na saida
                if ($li_nivel_1) {

                    $saida .= PHP_EOL . '<ul class="sub-1 dropdown-menu">' . PHP_EOL;
                    $saida .= $li_nivel_1;
                    $saida .= '</ul>' . PHP_EOL;
                }

                // saida li nivel = 0
                $saida .= '</li>' . PHP_EOL;
            }

            // finaliza saida de dados
            $saida .= '</ul>' . PHP_EOL;
        }

        return $saida;
    }

}



if(!function_exists('adminbar_url'))
{
    /**
     * Recebe a string do adminbar e retorna o endereço de edição, ou false.
     *  
     * @param string $adminbarStr
     * @return boolean|string
     */
    function adminbar_url($adminbarStr = '')
    {
        // se encontrou é o segundo elemento
        if(preg_match('/(http.+)(?:\b")/', $adminbarStr, $matches))
        {
            return $matches[1];
        }
        return false;
    }
}


if (!function_exists('first_img'))
{
    /**
     * return the first img path
     *
     * @param $gallery
     * @return bool
     */
    function first_img($gallery)
    {
        if (!is_array($gallery))
        {
            return false;
        }
        if (isset($gallery['full_path']))
        {
            return $gallery['full_path'];
        }

        if (isset($gallery[0]['full_path']))
        {
            return $gallery[0]['full_path'];
        }

        return false;
    }
}

if (!function_exists('first_attachment'))
{
    /**
     * return the first attachment path
     *
     * @param $attach
     * @return bool
     */
    function first_attachment($attach)
    {
        if (!is_array($attach))
        {
            return false;
        }
        if (isset($attach['nome']))
        {
            return $attach['nome'];
        }

        if (isset($attach[0]['nome']))
        {
            return $attach[0]['nome'];
        }

        return false;
    }
}

if (!function_exists('category_hierarchy'))
{
    /**
     * receive the categories ordered and set the active one
     * @param array $categories
     * @return array
     */
    function category_hierarchy(array $categories)
    {
        $active        = null;
        $hierarchyLine = array();
        $rCat = array_reverse($categories);
        $onTrail = false;
        $lastRel = 0;

        foreach ($rCat as $k => $c)
        {
            if($onTrail)
            {
                if($lastRel == $c['id'])
                {
                    $hierarchyLine[] = $c;
                    $lastRel = $c['rel'];
                }

            }

            if ($c['active'] === 'active')
            {
                $active          = $c;
                $lastRel = $c['rel'];
                $hierarchyLine[] = $c;
                $onTrail = true;
            }
        }

        return array_reverse($hierarchyLine);
    }
}


if(!function_exists('category_multilevel_hierarchy'))
{
    /**
     * receive the categories array and loop through
     * organize on a multilevel array
     *
     * @param $categories
     */
    function category_multilevel_hierarchy($categories)
    {
        if(! is_array($categories))
        {
            return false;
        }

        $cat = array();

        foreach($categories as $k => $c)
        {
            // grab the first level
            if($c['rel'] == 0)
            {
                $that = $c;
                // remove from the array
                array_slice($categories, $k, 1);

                // look for the children of this category
                $that['children'] = category_multilevel_hierarchy_recursive($categories, $cat, $that['id']);
                $cat[] = $that;
            }
        }

        return $cat;
    }

    function category_multilevel_hierarchy_recursive(&$categories, &$cat, $parentId)
    {
        $thisChildrens = array();

        foreach($categories as $k => $c)
        {
            if($c['rel'] == $parentId)
            {
                $that = $c;
                // remove
                array_slice($categories, $k, 1);

                $that['children'] = category_multilevel_hierarchy_recursive($categories, $cat, $that['id']);
                $thisChildrens[] = $that;
            }
        }

        return $thisChildrens;

    }
}