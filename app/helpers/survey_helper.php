<?php

/**
 * Helpers para gerar formulários de surveys
 */
if (!function_exists('survey_form_input'))
{

    /**
     * Retorna um <input>
     * @param object $q
     * @return string
     */
    function survey_form_input($q, $for = '')
    {
        $name = _surveyformname($q->id);
        $class = 'input-' . $q->query_type . ' input-id-' . $q->id;

        $h = "<input type=\"text\" name=\"{$name}\" id=\"{$for}\" class=\"{$class}\">";
        
        return $h;
    }

}


if (!function_exists('survey_form_text'))
{

    /**
     * Retorna um <textarea>
     * @param object $q
     * @return string
     */
    function survey_form_text($q, $for = '')
    {
        $name = _surveyformname($q->id);
        $class = 'input-' . $q->query_type . ' input-id-' . $q->id;

        $h = "<textarea name=\"{$name}\" id=\"{$for}\" class=\"{$class}\"></textarea>";
        return $h;
    }

}


if (!function_exists('survey_form_singleOptions'))
{

    /**
     * Retorna um <select>
     * @param object $q
     * @return string
     */
    function survey_form_singleOptions($q, $for = '')
    {
        $name = _surveyformname($q->id);
        $class = 'input-' . $q->query_type . ' input-id-' . $q->id;
        $options = array();
        $selected = null;
        $extra = "id=\"{$for}\" class=\"{$class}\"";
        
        foreach ( (array)$q->query_options as $c => $v)
        {
            // seleciona iniciado com *
            if(substr($v, 0, 1) == '*')
            {
                $v = substr($v, 1);
                $selected = $c;
            }
            $options[$c] = $v;
        }

        $h = form_dropdown($name, $options, $selected, $extra);
        return $h;
    }

}

if (!function_exists('survey_form_multiOptions'))
{

    /**
     * Retorna um <select multiple>
     * @param object $q
     * @return string
     */
    function survey_form_multiOptions($q, $for = '')
    {
        $name = _surveyformname($q->id);
        $class = 'input-' . $q->query_type . ' input-id-' . $q->id;
        $options = array();
        $selected = null;
        $extra = "id=\"{$for}\" class=\"{$class}\" multiple=\"multiple\"";

        foreach ( (array)$q->query_options as $c => $v)
        {
            // seleciona iniciado com *
            if(substr($v, 0, 1) == '*')
            {
                $v = substr($v, 1);
                $selected = $c;
            }
            $options[$c] = $v;
        }
        
        $h = form_dropdown($name, $options, $selected, $extra);
        return $h;
    }

}

if (!function_exists('survey_form_5and1'))
{

    /**
     * Retorna uma lista de <input radio>
     * @param object $q
     * @return string
     */
    function survey_form_5and1($q, $for = '')
    {
        $name = _surveyformname($q->id);
        $class = 'input-' . $q->query_type . ' input-id-' . $q->id;
        
        $ci = & get_instance();
        $opts = $ci->config->item('survey');
        $options = $opts['answer']['5and1'];
        
        $h = "<input type=\"hidden\" name=\"{$name}\" value=\"0\">";
        foreach($options as $c => $v)
        {
            $s = '';
            if(substr($v, 0, 1) == '*')// seleciona iniciado com *
            {
                $v = substr($v, 1);
                $s = 'checked';
            }
            $h .= "<label class=\"radio inline\">
        <input type=\"radio\" name=\"{$name}\" id=\"radio_{$q->id}_{$c}\" class=\"{$class}\" value=\"{$c}\" {$s}> <span></span> {$v} </label>";
        }
        
   
        return $h;
    }

}

if (!function_exists('survey_form_5levels'))
{
    /**
     * Retorna uma lista de <input radio>
     * @param object $q
     * @return string
     */
    function survey_form_5levels($q, $for = '')
    {
        $name = _surveyformname($q->id);
        $class = 'input-' . $q->query_type . ' input-id-' . $q->id;
        
        $ci = & get_instance();
        $opts = $ci->config->item('survey');
        $options = $opts['answer']['5levels'];
        
        $h = "<input type=\"hidden\" name=\"{$name}\" value=\"0\">";
        foreach($options as $c => $v)
        {
            $s = '';
            if(substr($v, 0, 1) == '*')// seleciona iniciado com *
            {
                $v = substr($v, 1);
                $s = 'checked';
            }
            $h .= "<label class=\"radio inline\">
        <input type=\"radio\" name=\"{$name}\" id=\"radio_{$q->id}_{$c}\" class=\"{$class}\" value=\"{$c}\" {$s}> <span></span> {$v} </label>";
        }
   
        return $h;
    }
}

if (!function_exists('survey_form_binary'))
{
    /**
     * Retorna uma lista de <input radio>
     * @param object $q
     * @return string
     */
    function survey_form_binary($q, $for = '')
    {
        $name = _surveyformname($q->id);
        $class = 'input-' . $q->query_type . ' input-id-' . $q->id;
        
        $ci = & get_instance();
        $opts = $ci->config->item('survey');
        $options = $opts['answer']['binary'];
        
        $h = "<input type=\"hidden\" name=\"{$name}\" value=\"0\">";
        foreach($options as $c => $v)
        {
            $s = '';
            if(substr($v, 0, 1) == '*')// seleciona iniciado com *
            {
                $v = substr($v, 1);
                $s = 'checked';
            }
            $h .= "<label class=\"radio inline\">
        <input type=\"radio\" name=\"{$name}\" id=\"radio_{$q->id}_{$c}\" class=\"{$class}\" value=\"{$c}\" {$s}> <span></span> {$v} </label>";
        }
   
        return $h;
    }
}


if (!function_exists('survey_form_range10'))
{
    /**
     * Retorna uma lista de <select>
     * @param object $q
     * @return string
     */
    function survey_form_range10($q, $for = '')
    {
        $name = _surveyformname($q->id);
        $class = 'input-' . $q->query_type . ' input-id-' . $q->id;
        $extra = "id=\"{$for}\" class=\"{$class}\"";
        $selected = null;
        
        $ci = & get_instance();
        $opts = $ci->config->item('survey');
        $range = $opts['answer']['range10'];
        
        
        foreach($range as $c => $v)
        {
            $s = '';
            if(substr($v, 0, 1) == '*')// seleciona iniciado com *
            {
                $v = substr($v, 1);
                $selected = $c;
            }
            $options[$c] = $v;
        }
        

        $h = form_dropdown($name, $options, $selected, $extra);
   
        return $h;
    }
}

/**
 * Padrão de nome para os forms
 * @param int $id
 * @return string
 */
function _surveyformname($id)
{
    return 'surveyquery[' . $id .']';
}