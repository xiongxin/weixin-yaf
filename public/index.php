<?php
/**
 * Created by PhpStorm.
 * User: xiongxin
 * Date: 2016/3/19
 * Time: 15:50
 */

require_once '../vendor/autoload.php';

define("APP_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */
$app  = new Yaf\Application(APP_PATH . "/conf/application.ini");
$app->bootstrap()->run();
