<?php

class Inscricao
{
    protected $ci;

    protected $table = 'cms_inscritos';

    public function __construct()
    {
        $this->ci =& get_instance();
    }

    /**
     * return the number of active subscriptions
     *
     * @param null|int $contentId
     * @param int $status
     * @return int
     */
    public function countByStatus($contentId = null, $status = 1)
    {
        if (!is_numeric($contentId))
        {
            return 0;
        }

        $this->ci->db->where('conteudo_id', $contentId);
        $this->ci->db->where('status', (int)$status);
        $sqlA = $this->ci->db->get($this->table);

        return (int)$sqlA->num_rows();

    }

}