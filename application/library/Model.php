<?php
/**
 * Created by PhpStorm.
 * User: xuebin<406964108@qq.com>
 * Date: 2014/12/24
 * Time: 16:19
 * @copyright Copyright (c) 2014
 */
use Yaf\Registry;

class Model
{

    protected $db;

    public $table;

    private static $instance;

    /**
     * 是
     */
    const BOOL_YES = 'YES';

    /**
     * 不是
     */
    const BOOL_NO = 'NO#';

    /**
     * 构造函数
     * @param string $table
     * @author xuebing<406964108@qq.com>
     */
    public function __construct($table=null){

        $this->db = self::getInstance();

        if(empty($table)){

            $this->getTable();
        }else{
            $this->table    = $table;
        }

        $this->init();
    }

    protected function init(){

    }
    /**
     * 获取mysql数据库操作实例类
     * @return medoo
     */
    public static function getInstance() {
        if(empty(self::$instance)){
            $config     = Registry::get('config');
            self::$instance = new Medoo($config->database->config->m->toArray());

            if($config->database->config->debug){
                self::$instance->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            }
        }

        return self::$instance;
    }

    /**
     * 得到当前的数据对象名称
     * @access public
     * @return string
     */
    public function getTable() {
        if(empty($this->table)){

            $tableName = parse_name(substr(get_class($this),0,-strlen('Model')));
            $this->table    =   strtolower($tableName);

        }
        return $this->table;
    }

    /**
     *
     * @param string $query
     * @author xuebing<406964108@qq.com>
     */
    public function query($query){

        return $this->db->query($query);
    }

    /**
     *
     * @param string $query
     * @author xuebing<406964108@qq.com>
     */
    public function exec($query){

        return $this->db->exec($query);
    }

    /**
     *
     * @param string $string
     * @author xuebing<406964108@qq.com>
     */
    public function quote($string){
        return $this->db->quote($string);
    }

    /**
     * @param $join
     * @param null $columns
     * @param null $where
     * @return bool
     * @author xuebing<406964108@qq.com>
     */
    public function select($join, $columns = null, $where = null){

        return $this->db->select($this->table,$join,$columns,$where);
    }

    /**
     * @param null $join
     * @param null $column
     * @param null $where
     * @return bool
     * @author xuebing<406964108@qq.com>
     */
    public function get($join = null, $column = null, $where = null){

        return $this->db->get($this->table,$join,$column,$where);
    }

    /**
     * @param $join
     * @param null $where
     * @return bool
     */
    public function has($join, $where = null){

        return $this->db->has($this->table,$join,$where);
    }

    /**
     * @param $datas
     * @param bool $hasPk
     * @return array
     * @author xuebing<406964108@qq.com>
     */
    public function insert($datas,$hasPk=false){

        return $this->db->insert($this->table,$datas,$hasPk);
    }

    /**
     *
     * @param array $data
     * @param string $where
     * @author xuebing<406964108@qq.com>
     */
    public function update($data, $where = null){

        return $this->db->update($this->table,$data,$where);
    }

    /**
     *
     * @param array $where
     * @author xuebing<406964108@qq.com>
     */
    public function delete($where){

        return $this->db->delete($this->table,$where);
    }

    /**
     *
     * @param array $columns
     * @param string $search
     * @param string $replace
     * @param string $where
     * @author xuebing<406964108@qq.com>
     */
    public function replace($columns, $search = null, $replace = null, $where = null){

        return $this->db->replace($this->table,$columns,$search,$replace,$where);
    }

    /**
     * @param null $join
     * @param null $column
     * @param null $where
     * @return int
     * @author xuebing<406964108@qq.com>
     */
    public function count($join = null, $column = null, $where = null){

        return $this->db->count($this->table,$join,$column,$where);
    }

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return int|string
     */
    public function max($join, $column = null, $where = null){

        return $this->db->max($this->table,$join,$column,$where);
    }

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return int|string
     * @author xuebing<406964108@qq.com>
     */
    public function min($join, $column = null, $where = null){

        return $this->db->min($this->table,$join,$column,$where);
    }

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return int
     * @author xuebing<406964108@qq.com>
     */
    public function avg($join, $column = null, $where = null){

        return $this->db->avg($this->table,$join,$column,$where);
    }

    /**
     * @param $join
     * @param null $column
     * @param null $where
     * @return int
     * @author xuebing<406964108@qq.com>
     */
    public function sum($join, $column = null, $where = null){
        return $this->db->sum($this->table,$join,$column,$where);
    }

    /**
     * 获取错误信息
     * @author xuebing<406964108@qq.com>
     */
    public function error(){

        return $this->db->error();
    }

    /**
     * 获取最后一条执行的sql
     * @author xuebing<406964108@qq.com>
     */
    public function last_query(){

        return $this->db->last_query();
    }

    /**
     * 数据库执行日志
     * @author xuebing<406964108@qq.com>
     */
    public function log(){

        return $this->db->log();
    }

    /**
     * 数据库信息
     * @author xuebing<406964108@qq.com>
     */
    public function info(){

        return $this->db->info();
    }

    public function getPDO(){

        return self::$instance->pdo;
    }
}