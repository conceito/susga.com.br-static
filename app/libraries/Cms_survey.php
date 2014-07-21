<?php

class Cms_survey
{

    private $ci = NULL;
    public $moduloId = 62; // ID do módulo para ser reaproveitado
    public $surveyId = null; // não pode ser null

    /**
     * Armazena progresso
     * @var object
     */
    public $progress;

    /**
     * Armazena toda estrutura de questões dentro do passo atual
     * @var array
     */
    public $stepStructure = array();

    public function __construct()
    {
        $this->ci = &get_instance();
        // pode ser instanciada no autoload
        $this->ci->load->library('cms_usuario');
        $this->ci->load->model('cms/survey_model', 'survey');
        $this->ci->load->helper('survey');
    }

    /*
     * ----------------------------------------------------------
     * GETTERS SETTERS
     * ----------------------------------------------------------
     */

    /**
     * Existe um padrão, mas pode ser setado um novo módulo
     * @param int $modId
     */
    public function setModuleId($modId)
    {
        $this->moduloId = $modId;
    }

    /**
     * Define qual survey está sendo usada
     * @param int $id
     */
    public function setSurveyId($id)
    {
        $this->surveyId = $id;
    }

    public function getSurveyId()
    {
        return $this->surveyId;
    }

    /**
     * Após validar, o código fica na sessão até o final
     * @return string
     */
    public function getCode()
    {
        return $this->ci->phpsess->get('survey_code');
    }

    /**
     * Remove código da sessão.
     * Não retorna nada.
     */
    public function unsetCode()
    {
        $this->ci->phpsess->save('survey_valid_step', null);
        $this->ci->phpsess->save('survey_code', null);
        $this->ci->phpsess->save('survey_inscription_id', null);
    }

    /**
     * retorna o ID da resposta que está na sessão
     * @return int
     */
    public function getAnswerId()
    {
        return $this->ci->phpsess->get('survey_inscription_id');
    }
    
    /**
     * Retorna o passo que está gravado na sessão
     * @return int|string
     */
    public function getValidStep()
    {
        return $this->ci->phpsess->get('survey_valid_step');
    }

    /**
     * retorna dados da survey.
     * Aceita o 'slug' ou 'id'
     * 
     * @param mixed     $surveySlugId
     * @return object|boolean
     */
    public function get($surveySlugId)
    {
        if (is_numeric($surveySlugId))
        {
            $this->ci->db->where('id', $surveySlugId);
        }
        else
        {
            $this->ci->db->where('nick', $surveySlugId);
            $this->ci->db->where('tipo', 'survey');
        }

        $query = $this->ci->db->get('cms_conteudo');

        if ($query->num_rows() == 0)
        {
            return false;
        }

        $survey = $query->row();
        $this->setSurveyId($survey->id);

        return $survey;
    }

    public function setProgress($prog)
    {
        $this->progress = $prog;
    }

    /**
     * retorna um objeto com todos os dados de progresso do usuário
     */
    public function getProgress()
    {
        if (!empty($this->progress))
        {
            return $this->progress;
        }

        $segmentStep = $this->ci->uri->segment(4);
        $total = count($this->getAllSteps());
        $last = ($segmentStep == $total) ? true : false;

        $p = new stdClass();
        $p->actual = $segmentStep;
        $p->total = $total;
        $p->last = $last;

        $this->setProgress($p);

        return $p;
    }

    /**
     * Retorna um array de objetos de todos os passos
     * 
     * @param int   $surveyId
     * @return array
     */
    public function getAllSteps($surveyId = null)
    {
        if ($surveyId === null)
        {
            $surveyId = $this->getSurveyId();
        }

        $query = $this->ci->db->where('rel', $surveyId)
                ->where('tipo', 'survey_step')
                ->order_by('ordem, id')
                ->select('id, titulo, grupo, resumo, tipo')
                ->get('cms_conteudo');

        return $query->result();
    }

    public function getStep($step)
    {
        $surveyId = $this->getSurveyId();

        $query = $this->ci->db->where('rel', $surveyId)
                ->where('tipo', 'survey_step')
                ->order_by('ordem, id')
                ->limit(1, $step - 1)
                ->get('cms_conteudo');

        return $query->row();
    }

    /**
     * Salva a estrutura de objetos na memória e retorna a classe para uso dos
     * métodos específicos.
     * 
     * <code>
     * $struct = $this->cms_survey->stepStructure($step->id);
     * $struct->loop(); // retorna toda estrutura
     * $struct->groups(); // apegas grupos
     * $struct->queries(); // apenas questões
     * </code>
     * 
     * @param type $step
     * @return \Cms_survey
     */
    public function stepStructure($stepId)
    {
        $surveyId = $this->getSurveyId();

        $this->stepStructure = $this->generateStepStructure($stepId);

        return $this;
    }

    /**
     * Retorna a estrutura para o looping principal
     * @return array
     */
    public function loop()
    {
        return $this->stepStructure;
    }

    /**
     * Retorna o html do form usando o helper apropriado
     * 
     * @see helpers/survey_helper.php
     * @param type $queryObj
     * @return string html do form
     */
    public function form($queryObj, $for = '')
    {
        $func = "survey_form_" . $queryObj->query_type;

        $queryObj->query_options = $this->ci->survey->unserializeQueryOptions($queryObj->query_options);

        if (function_exists($func))
        {
            return $func($queryObj, $for);
        }
        return '';
    }

    /**
     * Faz consulta e retorna array com todos os objetos de um passo.
     * 
     * @param int $stepId ID do passo
     * @return array|boolean
     */
    public function generateStepStructure($stepId)
    {
        $tmp = array();

        // primeira consulta, grupos e questões de primeiro nível
        $this->ci->db->where('grupo', $stepId);
        $this->ci->db->where("( tipo = 'survey_group' OR tipo = 'survey_query' )");
        $this->ci->db->order_by('ordem, id');
        $this->ci->db->select('id, titulo, resumo, txt, ordem, grupo, tipo, txtmulti as query_options, tags as query_type');
        $query = $this->ci->db->get('cms_conteudo');

        if ($query->num_rows() == 0)
        {
            return false;
        }

        // segunda consulta, questões dentro de grupos
        foreach ($query->result() as $q)
        {
            $tmp[] = $q;

            $this->ci->db->where('grupo', $q->id);
            $this->ci->db->where('tipo', 'survey_query');
            $this->ci->db->order_by('ordem, id');
            $this->ci->db->select('id, titulo, resumo, txt, ordem, grupo, tipo, txtmulti as query_options, tags as query_type');
            $query2 = $this->ci->db->get('cms_conteudo');

            if ($query2->num_rows() > 0)
            {
                foreach ($query2->result() as $q2)
                {
                    $tmp[] = $q2;
                }
            }
        }

        return $tmp;
    }

    /**
     * Dispara erro caso não exista um ID válido para manipulação da lib
     * @throws Exception
     */
    public function checkValidId()
    {
        if (!is_numeric($this->surveyId))
        {
            throw new Exception('Não foi definido um ID válido para a Survey.');
        }
    }

    /*
     * ----------------------------------------------------------
     * FORM HELPERS
     * ----------------------------------------------------------
     */

    /**
     * Retorna um combobox com os passos de uma pesquisa.
     * @param int        $selected       ID do passo selecionado
     * @param string     $attrbs         Atributos do <select>
     * @param bool|array $firstOption    Opção inicial alternativa
     * @return string
     */
    public function formStepsCombo($selected = null, $attrbs = '', $firstOption = false)
    {
        $this->checkValidId();

        $steps = $this->ci->survey->getStepsFrom($this->surveyId);

        $options = array();

        if (!$steps)
        {
            $options[''] = "Nenhum encontrado";
        }
        else
        {
            if (is_array($firstOption))
            {
                $options = $firstOption;
            }
            foreach ($steps as $s)
            {
                $options[$s->id] = $s->titulo;
            }
        }

        return form_dropdown('survey_steps', $options, $selected, $attrbs);
    }

    /**
     * Retorna um combobox com os grupos de uma pesquisa.
     * @param int        $selected       ID do passo selecionado
     * @param string     $attrbs         Atributos do <select>
     * @param bool|array $firstOption    Opção inicial alternativa
     * @return string
     */
    public function formGroupsCombo($stepId = null, $selected = null, $attrbs = '', $firstOption = false)
    {
        $this->checkValidId();
        $opts = '';

        // grupos de um passo específico
        if (is_numeric($stepId))
        {
            $steps = $this->ci->survey->getGroupsFromStep($stepId);
        }
        // grupos de um survey
        else
        {
            $steps = $this->ci->survey->getGroupsFromSurvey($this->surveyId);
        }

        $options = array();

        if (!$steps)
        {
            $options[''] = "Nenhum encontrado";
        }
        else
        {
            if (is_array($firstOption))
            {
                $foa = array_values($firstOption);
                $chv = key($firstOption);
                $opts .= "<option value=\"{$chv}\">{$foa[0]}</option> \n";
            }
            foreach ($steps as $s)
            {
                if ($s->id == $selected)
                {
                    $sl = 'selected="selected"';
                }
                else
                {
                    $sl = '';
                }

                $opts .= "<option value=\"{$s->id}\" data-step-id=\"{$s->grupo}\" {$sl}>{$s->titulo}</option> \n";
            }
        }


        $html = "<select name=\"survey_groups\" {$attrbs}>\n";
        $html .= $opts;
        $html .= "</select>\n";

        return $html;
    }

    /**
     * Faz pesquisa pelo código, valida e salva na sessão
     * @param type $code
     */
    public function validateCode($code = '', $surveyId = '')
    {
        $this->ci->load->library(array('cms_usuario', 'cms_metadados'));

        // registra meta para código
        $this->ci->cms_metadados->addMetaFields(array('meta_key' => 'survey_code', 'meta_type' => 'unused', 'meta_value' => ''));


        $qm = $this->ci->db->where('meta_key', 'survey_code')
                ->where('meta_value', $code)
                ->order_by('id desc')
                ->limit(1)
                ->get('cms_usuariometas');

        if ($qm->num_rows() == 0)
        {
            return array('error' => true, 'msg' => 'Código inexistente.');
        }

        $qcode = $qm->row();

        if ($qcode->meta_type == 'used')
        {
            return array('error' => true, 'msg' => 'Este código já foi utilizado.');
        }

        // código válido!
        // pega usuário
        // $qcode->usuario_id
        // atualiza status do código... não pode usar novamente
        $this->ci->db->where('id', $qcode->id)
                ->update('cms_usuariometas', array('meta_type' => 'used'));

        // cria inscrição de respostas para o usuário
        $id = $this->ci->cms_usuario->inscribe(array(
            'conteudo_id' => $surveyId,
            'comentario' => 'questionário',
            'user_id' => $qcode->usuario_id,
            'status' => 2
                ), false, false);
//        dd($id);
        // se for válido, salva na sessão
        $this->ci->phpsess->save('survey_inscription_id', $id);
        $this->ci->phpsess->save('survey_code', $code);
        // salva o passo que usuário pode submeter
        $this->ci->phpsess->save('survey_valid_step', 1);

        return array('error' => false, 'msg' => 'Código encontrado e válido.');
    }
    
    
    /**
     * Ao chegar no final, valida as respostas alterando o status
     */
    public function validateAnswers()
    {
        $answerId = $this->getAnswerId();
        
        // valida usuário
        $this->ci->db->where('id', $this->getUserId())
                ->update('cms_usuarios', array('status' => 1));
        
        // valida respostas
        return $this->ci->db->where('id', $answerId)
                ->update('cms_inscritos', array('status' => 1));
    }

    /**
     * Verifica se o código do usuário está na sessão
     */
    public function isValid()
    {
        $code = $this->ci->phpsess->get('survey_code');

        return ($code) ? true : false;
    }

    /**
     * Processa dados da resposta do passo e salva no banco.
     * 
     * @param int   $surveyId ID da survey
     * @param int   $step Número do passo
     * @param array $data Dados via POST
     * @return array
     */
    public function saveStepAnswers($surveyId, $step, $data)
    {
        $responses = $data['surveyquery'];
        $answerId = $this->getAnswerId();

        // percorre as respostas e salva no banco

        foreach ($responses as $id => $val)
        {
            $this->ci->db->insert('cms_conteudo_rel', array(
                'parent_id' => $answerId,
                'usuario_id' => '',
                'conteudo_id' => $id,
                'valor' => $val,
                'data' => date("Y-m-d H:i:s"),
                'status' => '1',
            ));
        }
        
        $total = count($this->getAllSteps($surveyId));
        
        if($step < $total)
        {
            $next = $step + 1;
        }
        else
        {
            $next = 'fim';
        }

        
        return array(
            'error' => false, 
            'msg' => 'Código encontrado e válido.',
            'nextStep' => $next
            );
        
    }

}