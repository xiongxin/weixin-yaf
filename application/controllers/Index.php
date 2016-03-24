<?php
/**
 * Created by PhpStorm.
 * User: sks
 * Date: 2016/3/19
 * Time: 16:38
 */
class IndexController extends Base {
    public function indexAction() {
        $this->getSession()->set("new_user", "my name");
    }

    public function testAction() {
        var_dump(time_format('1457696730'));
        var_dump($this->getRequest()->getServer());
        var_dump($this->getRequest()->getRequestUri());

        $this->getResponse()->appendBody("test page");
    }
}