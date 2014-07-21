<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['default_controller'] = "inicio";
$route['404_override'] = 'pages/display';

/******************
 * dynamic routes *
 *****************/
include_once APPPATH . "cache/config/routes.php";

/******************
 *  manual routes *
 *****************/
// para sistema de banners
$route['banner/(:any)'] = "inicio/$1";
// controller de notícias
$route['noticias/[tag:|pag:](:any)'] = "noticias/index/$1";// paginação/tags
$route['noticias/c/(:any)'] = "noticias/categoria/$1";// categoria
$route['noticias/(:any)'] = "noticias/display/$1";

$route['cursos/pag:(:any)'] = "cursos/index/$1";// paginação
$route['cursos/c/(:any)'] = "cursos/categoria/$1";// categoria
$route['cursos/(:any)'] = "cursos/display/$1";

// para pesquisa
//$route['pesquisa/r/(:any)'] = "pesquisa/results/$1";
$route['pesquisa/(:any)'] = "pesquisa/results/$1";


$route['loja/p/(:any)'] = "loja/produto/$1";
$route['loja/c/(:any)'] = "loja/categoria/$1";// categoria
//$route['loja/^[tag:|pag:](:any)'] = "loja/index/$1";// paginação/tags


// rereescrevendo
//$route['pagina-a/pagina-interna'] = "file";

/*
| -------------------------------------------------------------------------
| ROTAS GENÉRICAS PARA SITES MULTILINGUES
| Para rotas específicas definir abaixo destas regras.
| -------------------------------------------------------------------------
 */
//$route['^../(.+)$'] = "$1"; // For 2 chars
//$route['^...../(.+)$'] = "$1"; // For 5 chars ex.: /pt_br/about
//$route['^..$'] = $route['default_controller'];

// URI like '/en/about' -> use controller 'about'
$route['^fr/(.+)$'] = "$1";
$route['^en/(.+)$'] = "$1";
$route['^pt/(.+)$'] = "$1";
$route['^es/(.+)$'] = "$1";

// '/en' and '/fr' URIs -> use default controller
$route['^fr$'] = $route['default_controller'];
$route['^en$'] = $route['default_controller'];
$route['^pt$'] = $route['default_controller'];
$route['^es$'] = $route['default_controller'];
/*
| -------------------------------------------------------------------------
| /FIM/ DAS ROTAS GENÉRICAS PARA SITES MULTILINGUES
| -------------------------------------------------------------------------
 */

/* End of file routes.php */
/* Location: ./application/config/routes.php */