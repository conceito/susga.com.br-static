<?php

/**
 * Class Surveyquery
 *
 * render the view answered for each type os question
 * used to render the graphic sheet
 */
class Surveyquery
{

    /**
     * @var CI_Controller
     */
    protected $ci;

    /**
     * @var array
     */
    protected $options = array();

    function __construct()
    {
        $this->ci = & get_instance();
        //        $this->ci->load->model('survey_model', 'survey');

        $this->options = $this->ci->config->item('survey');
    }

    /**
     * receive the query and delegate the proper function to render
     *
     * object(stdClass)[33]
     * public 'id' => string '166' (length=3)
     * public 'ordem' => string '1' (length=1)
     * public 'titulo' => string 'Em que hospital você teve a sua cirurgia cardíaca realizada?' (length=62)
     * public 'resumo' => string '' (length=0)
     * public 'txtmulti' => string '["Casa de Sa\u00fade S\u00e3o Jos\u00e9","*Pr\u00f3 Card\u00edaco","Hospital S\u00e3o Lucas","Hospital da Unimed ","INC","Outros"]' (length=130)
     * public 'tags' => string 'singleOptions' (length=13)
     * public 'grupo' => string '161' (length=3)
     * public 'rel' => string '160' (length=3)
     * public 'modulo_id' => string '62' (length=2)
     * public 'tipo' => string 'survey_query' (length=12)
     *
     * @param array $query
     */
    public function render($query)
    {

        $method = 'render_' . $query['tags'];
        if (method_exists($this, $method))
        {
            return $this->$method($query);
        }
//        else if (in_array($query['id'], array(167)))
//        {
//            return $this->render_input($query);
//        }

        return '';
    }

    public function render_input($q)
    {
        $baseOptions = $this->summariseInputValues($q);

        $v['result'] = $baseOptions;

        return $this->ci->load->view('cms/survey_view/graphics/simpleTabularData_table', $v, true);
    }

    public function render_text($q)
    {
        $url = site_url('cms/surveyView/query/' . $q['rel'] . '/' . $q['id']);
        return "<a href=\"{$url}\">Ver respostas</a>";
    }


    public function render_5levels($q)
    {
        $baseOptions = key_from_array('answer.5levels', $this->options);

        $v['result'] = $this->getNumericTabularOptions($baseOptions, $q);

        return $this->ci->load->view('cms/survey_view/graphics/tabularData_table', $v, true);
    }

    public function render_5and1($q)
    {
        $baseOptions = key_from_array('answer.5and1', $this->options);

        $v['result'] = $this->getNumericTabularOptions($baseOptions, $q);

        //        dd($v['result']);

        return $this->ci->load->view('cms/survey_view/graphics/tabularData_table', $v, true);
    }

    public function render_binary($q)
    {
        $baseOptions = key_from_array('answer.binary', $this->options);

        $v['result'] = $this->getNumericTabularOptions($baseOptions, $q);

        return $this->ci->load->view('cms/survey_view/graphics/tabularData_table', $v, true);
    }

    public function render_range10($q)
    {
        $baseOptions = key_from_array('answer.range10', $this->options);

        $v['result'] = $this->getNumericTabularOptions($baseOptions, $q);

        return $this->ci->load->view('cms/survey_view/graphics/tabularData_table', $v, true);
    }

    public function render_singleOptions($q)
    {
        $baseOptions = $this->unserializeOptions($q['txtmulti']);

        $v['result'] = $this->getNumericTabularOptions($baseOptions, $q);

        return $this->ci->load->view('cms/survey_view/graphics/simpleTabularData_table', $v, true);
    }

    /**
     * <code>
     * return array(
     *  array(
     *      'label' => 'Excelente',
     *      'point' => 5,
     *      'total_points' => 55,
     *      'total_answer' => 7,
     *      'total_perc' => 25,
     * ),
     * );
     * </code>
     * @param array $options
     * @param array $query
     * @return array
     */
    public function getNumericTabularOptions(array $options, array $query)
    {
        $results      = $this->getNumericResultFromQuery($query['id']);
        $totalResults = $this->getTotalAnswersFromResult($results);

        $tabularResult = array();

        foreach ($options as $k => $o)
        {
            // loop thought results
            $thisTotal = 0;
            foreach ($results as $r)
            {
                if ($r['answer_id'] == $k)
                {
                    $thisTotal = (int)$r['total'];
                }
            }

            $tabularResult[] = array(
                'label'        => str_replace('*', '', $o),
                'point'        => $k,
                'total_points' => $thisTotal * $k,
                'total_answer' => $thisTotal,
                'total_perc'   => percentual($totalResults, $thisTotal)
            );
        }

        return $tabularResult;
    }

    /**
     * execute query to retrieve tabular data from countable type of queries
     * @param $queryId
     */
    public function getNumericResultFromQuery($queryId)
    {
        $sql = "select insc.status, insc.conteudo_id as survey, res.valor as answer_id, count(*) as total
        from cms_conteudo_rel as res
        inner join cms_inscritos as insc on insc.id = res.parent_id
        where insc.status = 1 AND res.conteudo_id = ?
        group by answer_id order by answer_id";

        $query = $this->ci->db->query($sql, array($queryId));

        return $query->result_array();
    }


    public function summariseInputValues($query)
    {
        $results      = $this->getNumericResultFromQuery($query['id']);
        $totalResults = $this->getTotalAnswersFromResult($results);

        $tabularResult = array();

        foreach ($results as $o)
        {
            $tabularResult[] = array(
                'label'        => $o['answer_id'],
                'point'        => 0,
                'total_points' => 0,
                'total_answer' => $o['total'],
                'total_perc'   => percentual($totalResults, $o['total'])
            );
        }

        return $tabularResult;
    }

    /**
     * loop thought results and sum the totals up
     *
     * @param $results
     * @return int
     */
    public function getTotalAnswersFromResult($results)
    {
        $total = 0;

        if (is_array($results) && isset($results[0]['total']))
        {
            foreach ($results as $r)
            {
                $total += (int)$r['total'];
            }
        }

        return $total;
    }


    /**
     * prepare a list of options to be matched against the database answers
     *
     * @param $optionsString
     * @return mixed
     */
    public function unserializeOptions($optionsString)
    {

        $options = json_decode($optionsString);

        return $options;
    }

}