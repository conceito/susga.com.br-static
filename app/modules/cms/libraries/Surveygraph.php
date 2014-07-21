<?php

/**
 * Class Surveygraph
 *
 * manage data-view of a survey report
 */
class Surveygraph
{

    /**
     * Título da janela
     * @var string
     */
    public $title = '';
    public $structure;
    /**
     * Filtros possíveis
     * @var array
     */
    public $filters = array(
        'dt1' => false, // data inicial
        'dt2' => false, // data final
    );
    /**
     * @var Controller
     */
    protected $controller;
    /**
     * Lista das views principais
     * @var array
     */
    protected $view = array();
    /**
     * Dados da survey
     * @var object
     */
    protected $survey;

    /**
     * the query
     * @var array
     */
    protected $query;
    /**
     * data to be shown and paginated
     * @var array
     */
    protected $data = array();
    private $ci;

    public function __construct()
    {
        $this->ci = & get_instance();
    }

    /**
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param array $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return \Controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param \Controller $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * the main view
     * panel with generic statistics and buttons to main actions
     *
     * view parts:
     * - filters    "Barra de filtros"
     * - breadcrumb "Nome do cliente e questionário"
     * - main       "Corpo da página"
     */
    public function renderIndexPanel()
    {
        $this->view['json_vars'] = json_encode($this->controller->json_vars);

        $this->view['title'] = $this->getTitle();

        $this->view['filters']    = $this->getViewFilters();
        $this->view['breadcrumb'] = $this->getViewBreadcrumb();
        $this->view['main']       = $this->getViewMain();
        $this->view['footer']     = $this->getViewFooter();

        $this->ci->load->view('cms/survey_view/main', $this->view);
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getViewFilters()
    {
        $view['brand']      = $this->ci->config->item('title');
        $view['survey'] = $this->getSurvey();
        $view['page'] = $this->ci->uri->segment(3);

        return $this->ci->load->view('cms/survey_view/filters', $view, true);
    }

    public function getViewBreadcrumb()
    {
        $view['stats'] = $this->getSurveyStats($this->survey['id']);
        $view['brand']      = $this->ci->config->item('title');
        $view['surveyName'] = $this->survey['titulo'];

        return $this->ci->load->view('cms/survey_view/breadcrumb', $view, true);
    }

    public function getSurveyStats($surveyId)
    {
        $sql = "select count(*) as total, insc.status from cms_inscritos as insc
where conteudo_id = ? and insc.status in (1,2)
group by status";

        $query = $this->ci->db->query($sql, array($surveyId));
        $result = $query->result_array();

        $total = 0;
        $completed = 0;
        $uncompleted = 0;

        foreach($result as $res)
        {
            $total += $res['total'];
            if($res['status'] == 1)
            {
                $completed += $res['total'];
            }
            else if($res['status'] == 2)
            {
                $uncompleted += $res['total'];
            }
        }

        return array(
            'total' => $total,
            'completed' => $completed,
            'uncompleted' => $uncompleted,
        );
    }

    /**
     * Define o conteúdo da view 'main'
     * @internal param string $view
     */
    public function getViewMain()
    {
        $view['data'] = $this->getData();
//dd($view['data']);
        return $this->ci->load->view('cms/survey_view/main-panel', $view, true);
    }

    public function getStructure()
    {
        return $this->structure;
    }

    public function setStructure($obj)
    {
        $this->structure = $obj;
    }

    public function getViewFooter()
    {
        $view['brand']      = $this->ci->config->item('title');
        $view['surveyName'] = $this->survey['titulo'];

        return $this->ci->load->view('cms/survey_view/footer', $view, true);
    }

    /**
     * render the general statistics
     */
    public function renderSheetPanel()
    {
        $this->view['json_vars'] = json_encode($this->controller->json_vars);

        $this->view['title'] = $this->getTitle();

        $this->view['filters']    = $this->getViewFilters();
        $this->view['breadcrumb'] = $this->getViewBreadcrumb();
        $this->view['main']       = $this->getViewSheet();
        $this->view['footer']     = $this->getViewFooter();

        $this->ci->load->view('cms/survey_view/main', $this->view);
    }

    /**
     * render the generic view with tabular data
     */
    public function getViewSheet()
    {
        $view['structure'] = $this->getStructure();

        return $this->ci->load->view('cms/survey_view/structure', $view, true);
    }

    /**
     * render the panel to show the answers of one specific question
     */
    public function renderQueryPanel()
    {
        $this->view['json_vars'] = json_encode($this->controller->json_vars);

        $this->view['title'] = $this->getTitle();

        $this->view['filters']    = $this->getViewFilters();
        $this->view['breadcrumb'] = $this->getViewBreadcrumb();
        $this->view['main']       = $this->getViewQuery();
        $this->view['footer']     = $this->getViewFooter();

        $this->ci->load->view('cms/survey_view/main', $this->view);
    }

    /**
     * render the generic view with tabular data
     */
    public function getViewQuery()
    {
        $view['survey'] = $this->getSurvey();
        $view['query'] = $this->getQuery();
        $view['answers'] = $this->getData();

        return $this->ci->load->view('cms/survey_view/query', $view, true);
    }

    public function getSurvey()
    {
        return $this->survey;
    }

    public function setSurvey($survey)
    {
        $this->survey = $survey;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Retorna array com todos os filtros combinados com a seleção da URI
     */
    public function getFilters()
    {

    }

    /**
     * Insere novo tipo de filtro
     * @param string $name
     */
    public function setFilter($name, $default = false)
    {
        $this->filters = array_merge($this->filters, array($name => $default));
    }

    /**
     * Define o conteúdo da view 'filters'
     * @param string $view
     */
    public function setViewFilters($view = '')
    {
        $this->view['filters'] = $view;
    }

    public function setPagination($baseUrl = 'cms/surveyView/query', $total = 0, $perPage = 10){

        $this->ci->load->library('pagination');
//        $this->set_pagination = true;

        // configura paginação ----------------------------------
        $config['base_url'] = site_url($baseUrl);
        $config['total_rows'] = $total;
        $config['per_page'] = $perPage;
        $config['uri_segment'] = 6;
        $config['num_links'] = 4; // quantas páginas são mostradas antes de depois na paginação
        $config['full_tag_open'] = '<div class="pagination pagination-centered clearfix"><ul class="">';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['first_link'] = 'primeira';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['next_link'] = '»';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '«';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['last_link'] = 'última';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        //        $config['display_pages'] = false;
        $this->ci->pagination->initialize($config);


    }
}