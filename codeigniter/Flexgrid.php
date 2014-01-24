<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Flexgrid library.
 *
 * @author Joubert "RedRat" <joubert@redrat.com.br>
 * @copyright Copyright (c) 2013, RedRat Consultoria
 * @license GPL version 2
 * @see Github, animes and mangás, cute girls and PHP, lot PHP
 * @see ./application/config/flexgrid.php
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
     * Method to flexgrid send data.
     *
     * @var string
     */
    private $method;

    /**
     * Data to return to flexgrid.
     *
     * @var array
     */
    private $data;

    /**
     * Row of data
     *
     * @var array
     */
    private $row;

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
        $this->set_method();
        $this->data[FLEXGRID_RETURN_ROWS] = array();
    }

    /**
     * Set the method used by flexgrid.
     *
     * @param string $method
     * @return void
     */
    public function set_method($method = 'post') {
        if (!in_array($method, $this->method_types))
            exit('Flexgrid: invalid method '.$method);
        $this->method = $method;
    }

    /**
     * Set the total rows by query.
     *
     * @param int $total
     * @return void
     */
    public function set_total($total) {
        $this->data[FLEXGRID_RETURN_TOTAL] = (int) $total;
    }

    /**
     * Include the flexgrid params to activerecord.
     *
     * @param object &$db Pointer to activerecord used by model.
     * @param array $params Flexgrid's params on this example format:
     *      $params['sortname'] = 'name';
     *      $params['sortorder'] = 'ASC';
     *      $params['qtype'] = 'name';
     *      $params['query'] = 'arquite';
     *      $params['page'] = 0;
     *      $params['rp'] = 30;
     * @return void
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

    /**
     * Get all params sended by flexgrid.
     *
     * @return array
     */
    public function get_params() {
        return array_merge($this->get_order(), $this->get_search(), $this->get_limit());
    }

    /**
     * Get order params sended by flexgrid.
     *
     * @return array
     */
    public function get_order() {
        $data[FLEXGRID_ORDER_COLUMN] = $this->CI->input->{$this->method}(FLEXGRID_ORDER_COLUMN);
        $data[FLEXGRID_ORDER_SORT] = $this->CI->input->{$this->method}(FLEXGRID_ORDER_SORT);
        return $data;
    }

    /**
     * Get search params sended by flexgrid.
     *
     * @return array
     */
    public function get_search() {
        $data[FLEXGRID_SEARCH_COLUMN] = $this->CI->input->{$this->method}(FLEXGRID_SEARCH_COLUMN);
        $data[FLEXGRID_SEARCH_QUERY] = $this->CI->input->{$this->method}(FLEXGRID_SEARCH_QUERY);
        return $data;
    }

    /**
     * Get limit params sended by flexgrid.
     *
     * @return array
     */
    public function get_limit() {
        $data[FLEXGRID_LIMIT_PAGE] = $this->CI->input->{$this->method}(FLEXGRID_LIMIT_PAGE);
        $data[FLEXGRID_LIMIT_ROWS] = $this->CI->input->{$this->method}(FLEXGRID_LIMIT_ROWS);
        return $data;
    }

    /**
     * Get custom param sended by flexgrid.
     *
     * @param string $name
     * @return string
     */
    public function get_param($name) {
        return $this->CI->input->{$this->method}($name);
    }

    /**
     * Get current flexgrid page.
     *
     * @return int
     */
    public function get_page() {
        return $this->CI->input->{$this->method}(FLEXGRID_LIMIT_PAGE) ? $this->CI->input->{$this->method}(FLEXGRID_LIMIT_PAGE) : 1;
    }

    /**
     * Get array data to send to flexgrid.
     *
     * @return array
     */
    public function get_data() {
        $this->data[FLEXGRID_RETURN_PAGE] = $this->get_page();
        foreach ($this->get_return_types() as $type) {
            if (!isset($this->data[$type]))
                exit('Flexgrid: '.$type.' required');
        }
        return $this->data;
    }

    /**
     * Return return attributes to flexgrid.
     *
     * @return array
     */
    public function get_return_types() {
        return array(FLEXGRID_RETURN_PAGE, FLEXGRID_RETURN_ROWS, FLEXGRID_RETURN_TOTAL);
    }

    /**
     * Add row to cell
     *
     * @param string $name
     * @param string $data
     * @return void
     */
    public function add_row($name, $data) {
        $this->row[$name] = $data;
    }

    /**
     * Add cell to data
     *
     * @param int $id Row id
     * @return void
     */
    public function add_cell($id = null) {
        $cell[FLEXGRID_RETURN_CELL] = $this->row;
        if(!is_null($id))
            $cell[FLEXGRID_RETURN_ID] = $id;
        $this->data[FLEXGRID_RETURN_ROWS][] = $cell;
        $this->row = array();
    }

    /**
     * Return data to flexgrid in json format
     *
     * @return void
     */
    public function return_json() {
        header('Content-type: application/json');
        exit(json_encode($this->data));
    }

    /**
     * Return data to flexgrid in xml format
     *
     * @param string $encoding Encoding type.
     * @return void
     */
    public function return_xml($encoding = 'utf-8') {
        $data = $this->get_data();

        $xml[] = '<?xml version="1.0" encoding="'.$encoding.'"?>';
        $xml[] = '<rows>';
        $xml[] = '<'.FLEXGRID_RETURN_PAGE.'>'.$data[FLEXGRID_RETURN_PAGE].'</'.FLEXGRID_RETURN_PAGE.'>';
        $xml[] = '<'.FLEXGRID_RETURN_TOTAL.'>'.$data[FLEXGRID_RETURN_TOTAL].'</'.FLEXGRID_RETURN_TOTAL.'>';

        foreach ($data[FLEXGRID_RETURN_ROWS] as $cell) {
            $xml[] = '<row'.(isset($cell[FLEXGRID_RETURN_ID]) ? ' id="'.$cell[FLEXGRID_RETURN_ID].'"' : '').'>';
            foreach ($cell[FLEXGRID_RETURN_CELL] as $name => $data) {
                $xml[] = '<'.FLEXGRID_RETURN_CELL.'><![CDATA['.$data.']]></'.FLEXGRID_RETURN_CELL.'>';
            }
            $xml[] = '</row>';
        }
        $xml[] = '</rows>';
        header('Content-type: text/xml');
        exit(implode('', $xml));
    }
}

/* End of file Flexgrid.php */
/* Location: ./application/libraries/Flexgrid.php */