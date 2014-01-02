<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Router Class version by RedRat
 *
 * Parses URIs and determines routing
 *
 * @package CodeIgniter
 * @subpackage Libraries
 * @author Joubert RedRat <joubert@redrat.com.br>
 * @copyright 2012 (c) RedRat Consultoria.
 * @category Libraries
 * @link http://codeigniter.com/user_guide/general/routing.html
 */
class MY_Router extends CI_Router
{
    /**
     * Replace hyphen to underscore before request.
     *
     * @param array $segments
     * @return void
     */
    function _set_request($segments = array())
    {
        parent::_set_request(str_replace('-', '_', $segments));
    }
}

/* End of file MY_Router.php */
/* Location: ./application/core/MY_Router.php */