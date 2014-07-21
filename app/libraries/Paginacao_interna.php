<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Gera a paginação interna vinda da marcação <!-- pagebreak -->
* Instruções:
* 1) No seu Model inclua:
* 		// divide o texto nas marcas <!-- pagebreak -->
		$parte = explode('<!-- pagebreak -->', $row['txt']);
* 		$config['base_url'] = site_url('artigo/' . $row['nick']);
        $config['nPaginas'] = count($parte);
        $config['uri_segment'] = 3;
        $config['full_tag_open'] = '<div class="paginacao">';
        $config['full_tag_close'] = '</div>';
        $this->load->library('paginacao_interna', $config);
  2) No HTML inclua:
  * <?php echo $this->paginacao_interna->cria_links(); ?>
*
* @copyright Bruno Barros - bruno@brunobarros.com - 2009
*/
class Paginacao_interna {
    var $nPaginas = 0; // total de paginas *necessário
    var $cur_page = 0; // página atual. 0 = primeira
    var $base_url = ''; // *necessário
    var $uri_segment = 3;
    var $cur_link = ''; // link corrente
    var $first_link = '&lsaquo; First';
    var $next_link = '&gt;';
    var $prev_link = '&lt;';
    var $last_link = 'Last &rsaquo;';
    var $full_tag_open = '';
    var $full_tag_close = '';
    var $first_tag_open = '';
    var $first_tag_close = '&nbsp;';
    var $last_tag_open = '&nbsp;';
    var $last_tag_close = '';
    var $cur_tag_open = '&nbsp;<b>';
    var $cur_tag_close = '</b>';
    var $next_tag_open = '&nbsp;';
    var $next_tag_close = '&nbsp;';
    var $prev_tag_open = '&nbsp;';
    var $prev_tag_close = '';
    var $num_tag_open = '&nbsp;';
    var $num_tag_close = '';

    function Paginacao_interna($params = array())
    {
        // $this->tb_admins = 'aw_admin';
        $this->CI = &get_instance();
        // armazena as regras
        $this->initialize($params);
    }
    // --------------------------------------------------------------------
    /**
    * Initialize preferences
    *
    * @access public
    * @param array $
    * @return void
    */
    function initialize($params = array())
    {
        if (count($params) > 0) {
            foreach ($params as $key => $val) {
                if (isset($this->$key)) {
                    $this->$key = $val;
                }
            }
        }
    }

    /**
    * Generate the pagination links
    *
    * @access public
    * @return string
    */
    function cria_links()
    {
        // Se não há páginas... não precisa!.
        if ($this->nPaginas == 1) {
            return '';
        }
        // Determine the current page number.
        $CI = &get_instance();
        // determina a página atual
        if ($CI->uri->segment($this->uri_segment) != 0) {
            $this->cur_page = $CI->uri->segment($this->uri_segment);
            // Prep the current page - no funny business!
            $this->cur_page = (int) $this->cur_page;
        }
        // Calculate the start and end numbers. These determine
        // which number to start and end the digit links with
        $start = 0;
        $end = $this->nPaginas - 1;
        // prepara a url
        $this->base_url = rtrim($this->base_url, '/') . '/';
        // $uri_page_number = $this->cur_page;

        // And here we go...
        $output = '';

        // Render the "First" link
        if ($this->cur_page > 1) {
            $output .= $this->first_tag_open . '<a href="' . $this->base_url . '">' . $this->first_link . '</a>' . $this->first_tag_close;
        }

        // Render the "previous" link
        if ($this->cur_page >= 1) {
            $i = $this->cur_page - 1;
            // if ($i == 0) $i = '';
            $output .= $this->prev_tag_open . '<a href="' . $this->base_url . $i . '">' . $this->prev_link . '</a>' . $this->prev_tag_close;
        }

        // Write the digit links
        for ($loop = $start; $loop <= $end; $loop++) {
            $i = ($loop + 1); // número que será mostrado da página. Se é 1 mostra 2

            if ($loop >= 0) {

                if ($this->cur_page == $loop) {
                   	($this->cur_link == '') ? $i = $i : $i = $this->cur_link;
                    $output .= $this->cur_tag_open . $i . $this->cur_tag_close; // Current page
                } else {
                    $output .= $this->num_tag_open . '<a href="' . $this->base_url . $loop . '">' . $i . '</a>' . $this->num_tag_close;
                }

            }
        }

		// Render the "next" link
		if ($this->cur_page < ($this->nPaginas - 1)){

			$output .= $this->next_tag_open.'<a href="'.$this->base_url.($this->cur_page + 1).'">'.$this->next_link.'</a>'.$this->next_tag_close;

		}

		// Render the "Last" link
		if ($this->cur_page < ($this->nPaginas - 2))
		{
			$i = ($this->nPaginas - 1);
			$output .= $this->last_tag_open.'<a href="'.$this->base_url.$i.'">'.$this->last_link.'</a>'.$this->last_tag_close;
		}

		// Kill double slashes.  Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open.$output.$this->full_tag_close;

        return $output;
    }
}

?>