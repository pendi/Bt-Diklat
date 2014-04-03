<?php

/**
 *
 * arch.php
 *
 * @package     arch-php
 * @author      Jafar Shadiq <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2013 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2013-01-17 04:06:42
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2013-01-17 04:06:42   Jafar Shadiq <jafar@xinix.co.id>
 *
 *
 */

require_once __DIR__.'/common.php';

class ARCH {
    var $routes = array();
    var $environment;
    var $has_default = false;

    var $HOSTAPPPATH;

    var $SYSTEMPATH = 'system/';
    var $ARCHPATH = 'arch/';
    var $ROOTPATH;
    var $APPPATH;
    var $APPROOTPATH;

    function __construct($environment = '', $approotpath = '') {

        define('ARCHPHP_VERSION', 'v1-1.0.1');

        if (empty($_SERVER['HTTPS'])) $_SERVER['HTTPS'] = 'off';

        $this->HOSTAPPPATH = getcwd().'/application/';

        set_exception_handler(array($this, '_handle_exceptions'));
        register_shutdown_function(array($this, '_handle_shutdown'));

        $this->environment = $environment;

        $this->ROOTPATH = trim(__DIR__, 'arch');

        $this->APPROOTPATH = (empty($approotpath)) ? __DIR__ : $approotpath;

        $config_file = $this->APPROOTPATH.'/config/'.$environment.'/arch.php';
        if (file_exists($config_file)) {
            require_once $config_file;
        } else {
            $config_file = $this->APPROOTPATH.'/config/arch.php';
            require_once $config_file;
        }

        if (isset($config)) {
            foreach($config as $key => $value) {
                if (isset($this->$key)) {
                    $this->$key = $value;
                }
            }
        }

        $this->APPPATH = $this->_get_app_key().'/';

        define('BASEPATH', $this->ROOTPATH.$this->SYSTEMPATH);
        define('APPPATH', $this->_get_app_path());
        define('ARCHPATH', $this->ROOTPATH.$this->ARCHPATH);
        define('APPMODPATH', APPPATH . 'modules/');
        define('ARCHMODPATH', ARCHPATH . 'modules/');

        $manifest = $this->_get_manifest();

        define('ENVIRONMENT', $manifest['ENVIRONMENT']);
        define('THEMEPATH', (isset($manifest['THEMEPATH'])) ? $manifest['THEMEPATH'] : 'themes/');
        spl_autoload_register(array($this, '_autoload_class'));
    }

    function _get_app_key() {
        $str = '';

        if (is_cli_request()) {
            $str = $_SERVER['ARCHAPPID'];
        } else {
            $str .= '.'.$_SERVER['HTTP_HOST'];
            $str .= str_replace('/', '.', pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME));
            $str = trim($str, '.');
            if ($_SERVER['SERVER_PORT'] != 80 && ($_SERVER['SERVER_PORT'] != 443)) {
                $str = str_replace(':','.',$str);
            }
        }



        if (array_key_exists($str, $this->routes)) {
            $str = $this->routes[$str];
        }

        return $str;
    }

    function _get_app_path() {
        $path = $this->HOSTAPPPATH.$this->APPPATH;
        return (file_exists($path)) ? $path : $this->HOSTAPPPATH.'default/';
    }

    function _get_manifest() {
        $manifest = array(
            'ENVIRONMENT' => 'development',
        );
        if (file_exists($this->_get_app_path().'manifest.php')) {
            @require_once $this->_get_app_path().'manifest.php';
        } else {
            header('HTTP/1.1 500 ARCH-PHP Configuration Server Error', true, 500);
            throw new Exception("Manifest not found. Probably application data is missing or broken at ".$this->_get_app_path().'manifest.php');
        }
        return $manifest;
    }

    function _handle_exceptions($exception) {
        if (function_exists('log_message')) {
            $h ='Exception of type \''.get_class($exception).'\' occurred with Message: '.$exception->getMessage().' in File '.$exception->getFile().' at Line '.$exception->getLine();
            $bt =$exception->getTraceAsString();
            log_message('error', $h."\nBacktrace:\n".$bt, TRUE);
        }
        show_exception($exception);
        // mail('dev-mail@example.com', 'An Exception Occurred', $msg, 'From: test@example.com');
    }

    function _handle_shutdown() {
        $error = error_get_last();
        if (isset($error)) {
            _exception_handler($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    function _autoload_class($class) {
        $class = strtolower($class);
        if (substr($class, 0, 2) == 'ci') return;

        $exploded = explode('_', $class);
        $match_class = $exploded[count($exploded)-1];

        if ($match_class === 'model' || $match_class === 'controller') {
            foreach (array(APPPATH, ARCHPATH) as $path) {
                $file_path = $path . $match_class . 's/' . $class . '.php';
                if (file_exists($file_path)) {
                    require_once $file_path;
                    break;
                }
            }
        }
    }
}
