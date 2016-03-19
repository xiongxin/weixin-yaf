<?php
/**
 * Created by PhpStorm.
 * User: xiongxin
 * Date: 2016/3/19
 * Time: 15:58
 */


class Bootstrap extends Yaf\Bootstrap_Abstract {

    public function _initConfig() {
        $config = Yaf\Application::app()->getConfig();
        Yaf\Registry::set("config", $config);
    }
    /*
     * @param Yaf\Dispatcher $dispatcher
     */
    public function _initTwig(Yaf\Dispatcher $dispatcher) {
        $view = new TwigView(APP_PATH.'/application/views',Yaf\Registry::get("config")->get("twig")->toArray());
        $dispatcher->setView($view);
    }
}