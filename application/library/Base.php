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
            "access_token" => "Ov-I9QRBcvgJRzMc31RGxcE_4QBAvXUWeMK3NUK4Z4FaF_TDUgUi8T5PmMXL8TkI4naUz4EXhCp1cD44rX7SQwf_-pht18Eqtf_hFSSvGS8-sjnp_cmsn-cgd71GGjnsPHXdAAATLB",
            "expires_in" => 7200
        );
        $this->wechat = new Wechat($options);
        if(!is_not_wx() && empty($this->user)) {
            $forward = urlencode(DOMAIN.$_SERVER['REQUEST_URI']);
            $url = DOMAIN.'/callback/spread.html?forward='.$forward;
            $this->redirect($this->wechat->getOauthRedirect($url,'STATE','snsapi_userinfo'));
            //跳转回调接口
            //$url回调地址
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