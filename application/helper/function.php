<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: xuebing <406964108@qq.com>
// +----------------------------------------------------------------------

/**
 * 系统函数库
 */

/**
 * 字符串截取，多余的以...代替
 * @param $text
 * @param $length
 * @return string
 */
function subtext($text, $length)
{
    if(mb_strlen($text, 'utf8') > $length)
        return mb_substr($text, 0, $length, 'utf8').'...';
    return $text;
}
/**
 * 判断是不是微信浏览器
 * @return bool
 */
function is_not_wx(){
    static $useragent;
    if(empty($useragent)){
        $useragent = strtolower(addslashes($_SERVER['HTTP_USER_AGENT']));
    }
    return strpos($useragent, 'micromessenger') === false && strpos($useragent, 'windows phone') === false;
}
/**
 * 长度校验方法
 * @param $str
 * @param string $max
 * @param string $min
 * @return bool
 */
function length_regex($str,$max='',$min=''){
    $length = mb_strlen($str,'UTF8');
    $return = true;

    if(!empty($min) && !empty($max)){//都不为空时两个都校验
        $return = $length >= $min && $length <= $max;
    }elseif(!empty($min)){//最小长度不为空
        $return = $length >= $min;
    }elseif(!empty($max)){//最大长度不为空
        $return = $length <= $max;
    }
    return !$return;
}
/**
 * 使用正则验证数据
 * @access public
 * @param string $value  要验证的数据
 * @param string $rule 验证规则
 * @return boolean
 */
function regex($value,$rule) {
    $validate = array(
        'chinese'   =>  '/^[\x{4e00}-\x{9fa5}]+$/u',//仅支持utf-8，gbk请用/^[".chr(0xa1)."-".chr(0xff)."]+$/
        'cen'       =>  '/^([{\x{4e00}-\x{9fa5}]|[0-9a-zA-Z])+$/u',//中文、英文、数字
        'require'   =>  '/\S+/',//必填
        'email'     =>  '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
        'url'       =>  '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
        'currency'  =>  '/^\d+(\.\d+)?$/',//货币金额
        'number'    =>  '/^\d+$/',
        'zip'       =>  '/^\d{6}$/',
        'integer'   =>  '/^[-\+]?\d+$/',//整数
        'double'    =>  '/^[-\+]?\d+(\.\d+)?$/',//
        'english'   =>  '/^[A-Za-z]+$/',
        'mobile'    =>  '/^(1[0-9])\d{9}$/',
        'en'        =>  '/^[A-Za-z0-9]+$/',//数字+英文
        'telephone' =>  '/^(0[0-9]{2,3}\-{0,1})?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$/',
    );
    $return = true;
    // 检查是否有内置的正则表达式
    if(isset($validate[strtolower($rule)])){

        $rule   = $validate[strtolower($rule)];
        $return = !(preg_match($rule,$value)===1);
    }
    return $return;
}

/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 */
function I($name,$default='',$filter=null,$datas=null) {
    $app = Yaf\Application::app();

    if(strpos($name,'.')) { // 指定参数来源
        list($method,$name) =   explode('.',$name,2);
    }else{ // 默认为自动判断
        $method =   'param';
    }
    switch(strtolower($method)) {
        case 'get'     :   $input =& $_GET;break;
        case 'post'    :   $input =& $_POST;break;
        case 'put'     :   parse_str(file_get_contents('php://input'), $input);break;
        case 'param'   :
            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input  = & $_POST;
                    break;
                case 'PUT':
                    parse_str(file_get_contents('php://input'), $input);
                    break;
                default:
                    $input  = & $_GET;
            }
            break;
        case 'path'    :
            $input  =   array();
            if(!empty($_SERVER['PATH_INFO'])){
                $input  =   explode('/',trim($_SERVER['PATH_INFO'],'/'));
            }
            break;
        case 'request' :   $input =& $_REQUEST;   break;
        case 'session' :   $input =& $_SESSION;   break;
        case 'cookie'  :   $input =& $_COOKIE;    break;
        case 'server'  :   $input =& $_SERVER;    break;
        case 'globals' :   $input =& $GLOBALS;    break;
        case 'data'    :   $input =& $datas;      break;
        default:
            return NULL;
    }

    if(''==$name) { // 获取全部变量
        $data       =   $input;
        $filters    =   isset($filter)?$filter:$app->getConfig()->get('default_filter');
        if($filters) {
            if(is_string($filters)){
                $filters    = explode(',',$filters);
            }
            foreach($filters as $filter){
                $data   = array_map_recursive($filter,$data); // 参数过滤
            }
        }
    }elseif(isset($input[$name])) { // 取值操作
        $data       =  $input[$name];
        $filters    =  isset($filter)?$filter:$app->getConfig()->get('application')->get('default_filter');
        if($filters) {
            if(is_string($filters)){
                $filters    = explode(',',$filters);
            }elseif(is_int($filters)){
                $filters    = array($filters);
            }

            foreach($filters as $filter){
                if(function_exists($filter)) {
                    $data  = is_array($data) ? array_map_recursive($filter,$data) : $filter($data); // 参数过滤
                }else{
                    $data = filter_var($data,is_int($filter)?$filter:filter_id($filter));
                    if(false === $data) {
                        return isset($default)?$default:NULL;
                    }
                }
            }
        }
    }
    $data = empty($data) ? $default : (is_array($default) && !is_array($data) ? [] : $data);
    !empty($data) && is_array($data) && array_walk_recursive($data,'think_filter');
    return $data;
}

function array_map_recursive($filter, $data) {
    $result = array();
    foreach ($data as $key => $val) {
        $result[$key] = is_array($val)
            ? array_map_recursive($filter, $val)
            : call_user_func($filter, $val);
    }
    return $result;
}

function think_filter(&$value){
    // TODO 其他安全过滤

    // 过滤查询特殊字符
    if(preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|LIKE|NOTLIKE|BETWEEN|IN)$/i',$value)){
        $value .= ' ';
    }
}
/**
 * URL组装 支持不同URL模式
 * @param string $url URL表达式，格式：'[模块/控制器/操作#锚点@域名]?参数1=值1&参数2=值2...'
 * @param string|array $vars 传入的参数，支持数组和字符串
 * @param string|boolean $suffix 伪静态后缀，默认为true表示获取配置值
 * @param boolean $domain 是否显示域名
 * @return string
 */
function U($url='',$vars='',$suffix=true,$domain=false) {

    if(!empty($url) && $suffix){
        $suffix = Yaf\Registry::get('config')->application->url_suffix;

        if($suffix && '/' != substr($url,-1)){
            $url  .=  '.'.ltrim($suffix,'.');
        }
    }

    // 解析参数
    if(is_string($vars)) { // aaa=1&bbb=2 转换成数组
        parse_str($vars,$vars);
    }elseif(!is_array($vars)){
        $vars = array();
    }

    if(!empty($vars)) {
        $vars   =   http_build_query($vars);
        $url   .=   '?'.$vars;
    }

    if($domain) {
        $url   =  $domain.$url;
    }
//    return strtolower($url);
    return $url;
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author xuebingwang<406964108@qq.com>
 */
function is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return isset($user['mobileNum']) ? $user['mobileNum'] : 0;
    }
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 强制大小生成缩略图
 * @param $url
 * @param int $w
 * @param int $h
 * @return string
 */
function imageMogr2($url,$w=null,$h=null){
    if(empty($w) || empty($h)){
        $url = $url.'?e='.(time()+3600);
    }else{
        $url = $url.'?imageMogr2/thumbnail/!'.$w.'x'.$h.'!&e='.(time()+3600);
    }

    $config = Yaf\Registry::get('config');
    $sign = hash_hmac('sha1', $url, $config->qiniu->secrectKey, true);
    return $url.'&token='.$config->qiniu->accessKey.':'.urlsafe_base64_encode($sign);
}
/**
 * 裁剪正中部分，等比缩小生成缩略图
 * @param $url
 * @param int $w
 * @param int $h
 * @return string
 */
function imageView2($url,$w=null,$h=null){
    if(empty($w) || empty($h)){
        $url = $url.'?e='.(time()+3600);
    }else{
        $url = $url.'?imageView2/1/w/'.$w.'/h/'.$h.'/interlace/1&e='.(time()+3600);
    }

    $config = Yaf\Registry::get('config');
    $sign = hash_hmac('sha1', $url, $config->qiniu->secrectKey, true);
    return $url.'&token='.$config->qiniu->accessKey.':'.urlsafe_base64_encode($sign);
    return $url.'?imageMogr2/thumbnail/!'.$w.'x'.$h.'!';
}

/**
 * 获取7牛下载链接
 * @param $url
 * @return string
 */
function get_qiniu_file_durl($url){
    $url = $url.'?attname=&e='.(time()+3600);

    $config = Yaf\Registry::get('config');
    $sign = hash_hmac('sha1', $url, $config->qiniu->secrectKey, true);
    return $url.'&token='.$config->qiniu->accessKey.':'.urlsafe_base64_encode($sign);
}

function urlsafe_base64_encode($str) // URLSafeBase64Encode
{
    $find = array("+","/");
    $replace = array("-", "_");
    return str_replace($find, $replace, base64_encode($str));
}

/**
 * @param $name
 * @param string $value
 * @return bool|mixed|\Yaf\Session
 */
function session($name,$value=''){
    static $session;

    if(empty($session)){

        $session = Yaf\Session::getInstance();
    }

    switch (true){

        case '' === $value:
            //获取
//     	    SeasLog::debug('session get');
            $f = $session->get($name);
            break;
        case is_null($value):
            //删除
//             SeasLog::debug('session delete');
            $f = $session->del($name);
            break;
        default :
            //设置
//     	    SeasLog::debug('session set');
            $f = $session->set($name, $value);
            break;
    }

    return $f;
}
/**
 * CURL发送请求
 * @param $url
 * @param string $data
 * @param string $method
 * @param string $cookieFile
 * @param array $headers
 * @param int $connectTimeout
 * @param int $readTimeout
 * @return mixed|string
 */
function curlRequest($url, $data = '', $method = 'POST', $cookieFile = '', $headers = ["Content-Type:application/json;charset=UTF-8"], $connectTimeout = 30, $readTimeout = 30) {

    SeasLog::debug('发送URL:'.$url);
    SeasLog::debug('发送数据:'.$data);

    $method = strtoupper ( $method );

    $option = array (
        CURLOPT_URL => $url,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => $connectTimeout,
        CURLOPT_TIMEOUT => $readTimeout
    );

    if ($headers) {
        $option [CURLOPT_HTTPHEADER] = $headers;
    }

    if ($cookieFile) {
        $option [CURLOPT_COOKIEJAR] = $cookieFile;
        $option [CURLOPT_COOKIEFILE] = $cookieFile;
    }

    if ($data && $method == 'POST') {
        $option [CURLOPT_POST] = 1;
        $option [CURLOPT_POSTFIELDS] = $data;
    }
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt_array ( $ch, $option );
    $response = curl_exec ( $ch );

    if (curl_errno ( $ch ) > 0) {
        return curl_error ( $ch );
    }
    curl_close ( $ch );
    SeasLog::debug('收到数据:'.$response);
    return $response;
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author xuebing <406964108@qq.com>
 */
function time_format($time = NULL,$format='Y-m-d H:i:s'){
    $time = $time === NULL ? time() : intval($time);
    return date($format, $time);
}

/**
 * JSON转数组，始终返回一个数组
 * @param $json
 * @return array
 */
function json_to_array($json){
    if (!is_string($json)) {
        return array();
    }
    $value = json_decode($json,TRUE);
    return $value ? $value : array();
}

/**
 * 实例化一个没有模型文件的Model
 * @param string $name Model名称 支持指定基础模型 例如 User
 * @return Model
 */
function M($name = '')
{
    static $_model = array();
    $class = '\Model';
    $guid = $name . '_' . $class;
    if (!isset($_model[$guid])){
        $_model[$guid] = new $class($name);
    }
    return $_model[$guid];
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name=null, $type=0) {
    if(empty($name)){
        return '';
    }
    if ($type) {
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}