<?php
/**
 * Created by PhpStorm.
 * User: xiongxin
 * Date: 2016/3/19
 * Time: 15:58
 */


class Bootstrap extends Yaf\Bootstrap_Abstract {
    protected $config;

    public function _initConfig() {
        $this->config = Yaf\Application::app()->getConfig();
        Yaf\Registry::set("config", $this->config);
    }
    
    public function _initHelper() {
        Yaf\Loader::import(APP_PATH . '/application/helper/function.php');
    }

    public function _initSession() {
        session_set_save_handler(new RedisSession($this->config->redis->toArray()), true);
        Yaf\Session::getInstance()->start();
    }


    public function _initTwig(Yaf\Dispatcher $dispatcher) {
        $view = new TwigView(APP_PATH.'/application/views',$this->config->twig->toArray());
        $dispatcher->setView($view);
    }
}