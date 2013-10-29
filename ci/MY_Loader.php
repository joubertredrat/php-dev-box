<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Customized Loader Class for Helper loads
 *
 * Loads only helper at this moment overriding only helper method.
 *
 * @package CodeIgniter
 * @subpackage  Libraries
 * @author Joubert Guimarães de Assis "RedRat" <joubert@redrat.com.br>
 * @copyright 2013 (c) RedRat Consultoria.
 * @category Loader
 */
class MY_Loader extends CI_Loader {

    /**
     * Load Helper with and without '_helper' sufix.
     *
     * This function loads the specified helper file.
     *
     * @param mixed
     * @return void
     */
    public function helper($helpers = array())
    {
        foreach ($this->_ci_prep_filename($helpers, '') as $helper)
        {
            if (isset($this->_ci_helpers[$helper]) || isset($this->_ci_helpers[$helper.'_helper']))
            {
                continue;
            }

            $ext_helper = APPPATH.'helpers/'.config_item('subclass_prefix').$helper.'.php';

            // Is this a helper extension request?
            if (file_exists($ext_helper))
            {
                $base_helper = BASEPATH.'helpers/'.$helper.'.php';

                if ( ! file_exists($base_helper))
                {
                        show_error('Unable to load the requested file: helpers/'.$helper.'.php');
                }

                include_once($ext_helper);
                include_once($base_helper);

                $this->_ci_helpers[$helper] = TRUE;
                log_message('debug', 'Helper loaded: '.$helper);
                continue;
            }
            else
            {
                $ext_helper = APPPATH.'helpers/'.config_item('subclass_prefix').$helper.'_helper.php';

                // Is this a helper extension request?
                if (file_exists($ext_helper))
                {
                    $base_helper = BASEPATH.'helpers/'.$helper.'_helper.php';

                    if ( ! file_exists($base_helper))
                    {
                            show_error('Unable to load the requested file: helpers/'.$helper.'_helper.php');
                    }

                    include_once($ext_helper);
                    include_once($base_helper);

                    $this->_ci_helpers[$helper.'_helper'] = TRUE;
                    log_message('debug', 'Helper loaded: '.$helper.'_helper');
                    continue;
                }
            }

            // Try to load the helper
            foreach ($this->_ci_helper_paths as $path)
            {
                if (file_exists($path.'helpers/'.$helper.'.php'))
                {
                    include_once($path.'helpers/'.$helper.'.php');

                    $this->_ci_helpers[$helper] = TRUE;
                    log_message('debug', 'Helper loaded: '.$helper);
                    break;
                }
                else
                {
                    // Try to load the helper with sufix
                    if (file_exists($path.'helpers/'.$helper.'_helper.php'))
                    {
                        include_once($path.'helpers/'.$helper.'_helper.php');

                        $this->_ci_helpers[$helper.'_helper'] = TRUE;
                        log_message('debug', 'Helper loaded: '.$helper.'_helper');
                        break;
                    }
                }
            }

            // unable to load the helper
            if ( ! isset($this->_ci_helpers[$helper]) && ! isset($this->_ci_helpers[$helper.'_helper']))
            {
                show_error('Unable to load the requested file: helpers/'.$helper.'.php or helpers/'.$helper.'_helper.php');
            }
        }
    }
}

/* End of file MY_Loader.php */
/* Location: ./application/core/MY_Loader.php */