<?php
/**
 * Created by PhpStorm.
 * User: sks
 * Date: 2016/3/19
 * Time: 17:19
 */
use Yaf\Controller_Abstract;
use Yaf\Registry;
use Yaf\Session;
//TODO:1.建数据库 2.完成用户验证
class Base extends Controller_Abstract {
    private $config;
    private $user;
    private $wechat;
    private $session;

    public function init() {
        $this->config = Registry::get('config');
        $this->session = Session::getInstance();
        $this->user = $this->session->get('user');
        $options = array(
            'token'=>'weixin', //填写你设定的key
            'appid'=>'wx1a08bb8a9459ad7e', //填写高级调用功能的app id, 请在微信开发模式后台查询
            'appsecret'=>'7f0689ead41f57df91109a7054127794', //填写高级调用功能的密钥
            'token_ctime' => 12222,
            'access_token'=> 'aaa',
            'expires_in'=> 122
        );
        $this->wechat = new Wechat($options);
        var_dump($_SERVER);
        if(!is_not_wx() && empty($this->user)) {
            $forward = urlencode('http://' . $_SERVER['HTTP_HOST'] . $this->getRequest()->getRequestUri());
            $url = DOMAIN.'/callback/spread?forward='.$forward;

            //跳转回调接口
            //$url回调地址
            $this->redirect($this->wechat->getOauthRedirect($url,'','snsapi_base'));
        }

        $this->getView()->getTwig()->addGlobal('user', $this->user); //添加一个全局变量
    }

    public function assign($name, $value=null) {
        $this->getView()->assign($name, $value);
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getUser() {
        return $this->user;
    }

}