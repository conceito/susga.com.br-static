<?php

class Ga_test extends Frontend_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('ga_api');
        $this->load->helper('url');
    }

    public function organic() {
        
        // http://code.google.com/apis/analytics/docs/gdata/dimsmets/dimsmets.html

        // Also please note, if you using default values you still need to init()
//        $data = $this->ga_api->login()
//                ->dimension('visitorType')
//                ->metric('visitors, newVisits')
//                ->limit(60)
//                ->get_array();
        
        $data = $this->ga_api->login()
//                ->dimension('referralPath')
                ->metric('organicSearches')
                ->limit(30)
                ->get_array();
        
        mybug($data, true);
    }
    
    // --------------------------------------------------------------------
    //Requête simple des sites référents
    // --------------------------------------------------------------------
    function index() {
        $data['referrers'] = $this->ga_api->dimension('source')->metric('visits, pageviews')->get_object();

        $this->load->view('site_add/analytics', $data);
    }

    // --------------------------------------------------------------------
    // Exemple d'une requete avec un segment par défaut ( -12 = Non-bounce Visits)
    // --------------------------------------------------------------------
    function segment() {
        $data['referrers'] = $this->ga_api->segment('-12')->dimension('source')->metric('visits, pageviews')->get_object();

        $this->load->view('site_add/analytics', $data);
    }

    // --------------------------------------------------------------------
    // Faire fonctionner la librairie avec la pagination de codeigniter
    // --------------------------------------------------------------------
    function paginate($offset = 1) {
        
        $uri = $this->uri->to_array(array('pag'));
        $pag = ($uri['pag']=='') ? 1 : $uri['pag'];
        $offset = ($pag-1) * 20 + 1;        
     
        $this->ga_api->dimension('city')
                ->metric('visits, pageviews')
                ->when('2 month ago')
                ->limit(20)
                ->offset($offset);

        $data['cities'] = $this->ga_api->get_object();

        $config['base_url'] = site_url('labs/ga_test/paginate/');
        $config['uri_segment'] = 3;
        $config['total_rows'] = $data['cities']['summary']->totalResults;
        $config['per_page'] = $data['cities']['summary']->itemsPerPage;

        $this->load->library('pagination', $config);
        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('site_add/analytics', $data);
    }

    // --------------------------------------------------------------------
    // Plusieurs requêtes en même temps
    // --------------------------------------------------------------------
    function multiple() {
        // première requête
        $this->ga_api->dimension('city')->metric('visits, pageviews');
        $this->ga_api->when('2 month ago')->limit(5);

        $data['cities'] = $this->ga_api->get_object();

        // Nous n'avons pas beoin de tout redéfinir ici.
        // Cependant, on va remettre la valeur limit par défaut (10) et when (1 mois). On a pas besoin
        // non de reinitialiser la dimension, la nouvelle valeur sera prise en compte
        // metric aura la même valeur

        $data['referrers'] = $this->ga_api->dimension('source')->clear('limit, when')->get_object();

        $this->load->view('site_add/analytics', $data);
    }

    // --------------------------------------------------------------------
    // Définissez un dossier pour le cache ici ou dans la fichier de config
    // avant d'utiliser cette fonction.
    // --------------------------------------------------------------------
    function cache($offset = 1) {
        //$config['cache_folder'] = ''; // dossier cache
        $config['cache_data'] = TRUE; // activation du cache
        $config['clear_cache'] = array('date', '2 minutes ago'); // durée de vie du cache

        $this->ga_api->initialize($config);
        $data['cities'] = $this->ga_api->offset($offset)->dimension('city')->metric('visits, pageviews')->get_object();

        $config['base_url'] = site_url('analytics/cache/');
        $config['uri_segment'] = 3;
        $config['total_rows'] = $data['cities']['summary']->totalResults;
        $config['per_page'] = $data['cities']['summary']->itemsPerPage;

        $this->load->library('pagination', $config);
        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('site_add/analytics', $data);
    }

    // --------------------------------------------------------------------
    // Liste les profils sur votre compte analytics.
    // --------------------------------------------------------------------
    function accounts($profile = false) {
        if ($profile) {
            $config['profile_id'] = 'ga:' . $profile;
            $this->ga_api->clear('login')->initialize($config);

            $data['cities'] = $this->ga_api->dimension('city')->metric('visits, pageviews')->get_object();
        } else {
            $data['accounts'] = $this->ga_api->login()->get_accounts();
            //print_r($data);			
        }
        
//        mybug($data['accounts']);

        $this->load->view('site_add/analytics', $data);
    }

}