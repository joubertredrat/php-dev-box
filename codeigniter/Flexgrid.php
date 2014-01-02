<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Flexgrid library.
 *
 * @author Joubert "RedRat" <joubert@redrat.com.br>
 * @copyright Copyright (c) 2013, RedRat Consultoria
 * @license GPL version 2
 * @see Github, animes and mangás, cute girls and PHP, lot PHP
 * @link https://github.com/joubertredrat/php-dev-box
 * @link http://flexigrid.info/
 */

class Flexgrid
{
    /**
     * Codeigniter Instance.
     *
     * @var Object
     */
    private $CI;

    /**
     * Types of method to send data.
     *
     * @var array
     */
    private $method_types;

    /**
     * Library constructor.
     *
     * @return void
     */
    public function __construct() {
        $this->CI =& get_instance();
        if (!defined('FLEXGRID_LIMIT_ROWS'))
            $this->CI->load->config('flexgrid');
        $this->method_types[] = 'post';
        $this->method_types[] = 'get';
    }

    /**
     * Get all params sended by flexgrid.
     *
     * @param string $method method used by flexgrid do send params.
     * @return array
     */
    public function get_params($method = 'post') {
        if (!in_array($method, $this->method_types))
            return array();
        return array_merge($this->get_order($method), $this->get_search($method), $this->get_limit($method));
    }

    /**
     * Get order params sended by flexgrid.
     *
     * @param string $method method used by flexgrid do send params.
     * @return array
     */
    public function get_order($method = 'post') {
        if (!in_array($method, $this->method_types))
            return array();
        $data[FLEXGRID_ORDER_COLUMN] = $this->CI->input->{$method}(FLEXGRID_ORDER_COLUMN);
        $data[FLEXGRID_ORDER_SORT] = $this->CI->input->{$method}(FLEXGRID_ORDER_SORT);
        return $data;
    }

    /**
     * Get search params sended by flexgrid.
     *
     * @param string $method method used by flexgrid do send params.
     * @return array
     */
    public function get_search($method = 'post') {
        if (!in_array($method, $this->method_types))
            return array();
        $data[FLEXGRID_SEARCH_COLUMN] = $this->CI->input->{$method}(FLEXGRID_SEARCH_COLUMN);
        $data[FLEXGRID_SEARCH_QUERY] = $this->CI->input->{$method}(FLEXGRID_SEARCH_QUERY);
        return $data;
    }

    /**
     * Get limit params sended by flexgrid.
     *
     * @param string $method method used by flexgrid do send params.
     * @return array
     */
    public function get_limit($method = 'post') {
        if (!in_array($method, $this->method_types))
            return array();
        $data[FLEXGRID_LIMIT_PAGE] = $this->CI->input->{$method}(FLEXGRID_LIMIT_PAGE);
        $data[FLEXGRID_LIMIT_ROWS] = $this->CI->input->{$method}(FLEXGRID_LIMIT_ROWS);
        return $data;
    }

    /**
     * Get current flexgrid page.
     *
     * @param string $method method used by flexgrid do send params.
     * @return int
     */
    public function get_page($method = 'post') {
        return $this->CI->input->{$method}(FLEXGRID_LIMIT_PAGE) ? $this->CI->input->{$method}(FLEXGRID_LIMIT_PAGE) : 1;
    }

    /**
     * Include the flexgrid params to activerecord.
     *
     * @param object &$db Pointer to activerecord used my model.
     * @param array $params Flexgrid's params on this example format:
     *      $params['sortname'] = 'name';
     *      $params['sortorder'] = 'ASC';
     *      $params['qtype'] = 'name';
     *      $params['query'] = 'arquite';
     *      $params['page'] = 0;
     *      $params['rp'] = 30;
     * @return void
     * @see ./application/config/flexgrid.php
     */
    public function set_db_terms(&$db, $params) {
        if (isset($params[FLEXGRID_ORDER_COLUMN]) && isset($params[FLEXGRID_ORDER_SORT]))
            $db->order_by($params[FLEXGRID_ORDER_COLUMN], strtoupper($params[FLEXGRID_ORDER_SORT]));

        if (isset($params[FLEXGRID_SEARCH_COLUMN]) && (isset($params[FLEXGRID_SEARCH_QUERY]) && $params[FLEXGRID_SEARCH_QUERY] != ""))
            $db->like($params[FLEXGRID_SEARCH_COLUMN], $params[FLEXGRID_SEARCH_QUERY]);

        switch (true) {
            case isset($params[FLEXGRID_LIMIT_PAGE]) && isset($params[FLEXGRID_LIMIT_ROWS]):
                $db->limit($params[FLEXGRID_LIMIT_ROWS], (($params[FLEXGRID_LIMIT_PAGE] - 1) * $params[FLEXGRID_LIMIT_ROWS]));
                break;

            case isset($params[FLEXGRID_LIMIT_PAGE]):
                $db->limit($params[FLEXGRID_LIMIT_PAGE]);
                break;
        }
    }
}

/* End of file Flexgrid.php */
/* Location: ./application/libraries/Flexgrid.php */