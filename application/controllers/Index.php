<?php
/**
 * Created by PhpStorm.
 * User: sks
 * Date: 2016/3/19
 * Time: 16:38
 */
class IndexController extends Yaf\Controller_Abstract {
    public function indexAction() {
        $this->getView()->assign("content", "hello world");
    }
}