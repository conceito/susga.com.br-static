<?php

// Breadcrumb generator for CodeIgniter
class Breadcrumb {

    // variables
    public $link_type = ' &gt; '; // must have spaces around it
    public $breadcrumb = array();
    public $output = '';
    public $divider = '&raquo;';

    public function __construct() {
        
    }

        // -----------------------------------------------------------------------
    /**
     * clear
     * @return boolean
     */
    public function clear() {
        // clear the breadcrumb library to start again
        $props = array('breadcrumb', 'output');

        // loop through
        foreach ($props as $val) {
            // clear
            $this->$val = null;
        }

        // completed
        return true;
    }

    // -----------------------------------------------------------------------
    /**
     * add a "crumb"
     * @param type $title
     * @param type $url
     * @return boolean
     */
    public function add($title, $url = false) {
        
        if(is_string($title)){
            // pass into breadcrumb array
            $this->breadcrumb[] = array('title' => $title, 'url' => $url);
        } 
        // se for array, provavelmente está recebendo o método get_hierarchy()
        else if(is_array($title)){
            
            $count = 0;
            $ttl   = count($title);
            
            foreach ($title as $key => $row){
                $this->breadcrumb[] = array(
                    'title' => $row['titulo'],
                    'url'   => ($count == ($ttl-1)) ? false : $row['uri']
                );
                
                $count++;
            }
            
        }
        

        // completed
        return true;
    }

    // ------------------------------------------------------------------------
    /**
     * change link type
     * @param type $new_link
     * @return boolean
     */
    public function change_link($new_link) {
        // change
        $this->link_type = ' ' . $new_link . ' '; // the spaces are added for visual reasons
        // completed
        return true;
    }

    // ------------------------------------------------------------------------
    /**
     * produce output
     * @return type
     */
    public function output() {
        // define local counter
        $counter = 0;
        
        if(empty($this->breadcrumb)){
            return '';
        }

        $this->output = '<ul class="breadcrumb">';

        // loop through breadcrumbs
        foreach ($this->breadcrumb as $key => $val) {
            
            // are we using a hyperlink?
            if ($val['url']) {
                // add href tag
                $this->output .= '<li><a href="' . site_url($val['url']) . '">' . $val['title'] . '</a> <span class="divider">' . $this->divider . '</span></li>';
            } else {
                // don't use hyperlinks
                $this->output .= '<li class="active">' . $val['title'] . '</li>';
            }

            // increment counter
            $counter++;
        }

        $this->output .= '</ul>';

        // return
        return $this->output;
    }

}