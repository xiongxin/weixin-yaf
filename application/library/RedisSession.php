<?php
/**
 * Created by PhpStorm.
 * User: sks
 * Date: 2016/3/23
 * Time: 14:57
 */

class RedisSession implements SessionHandlerInterface {
    private $config;
    private $redis;
    private $key_prefix;
    private $maxLifetime;


    public function __construct($config) {
        $this->config = $config;
        $this->redis = new Redis();
        $this->key_prefix = !empty($this->config['key_prefix'])
            ? $this->config['key_prefix'] . ":"
            : 'session:';
        // We want to just use
        $this->maxLifetime = ini_get('session.gc_maxlifetime');
    }

    /**
     * We don't need to do anything extra to initialize the session since
     * we get the Redis connection in the constructor.
     *
     * @param  string $savePath The path where to storethe session.
     * @param  string $name     The session name.
     * @return bool
     */
    public function open($savePath, $name) {
        $connect = $this->redis->connect($this->config['host'],6379);
        if (!$connect) return false;
    }
    
    
    /**
     * Since we use Redis EXPIRES, we don't need to do any special garbage
     * collecting.
     *
     * @param  string $maxLifetime The max lifetime of a session.
     * @return bool
     */
    public function gc($maxLifetime) {
        return true;
    }

    /**
     * Close the current session by disconnecting from Redis.
     */
    public function close() {
        // This will force Predis to disconnect.
        unset($this->redis);
    }

    /**
     * Destroys the session by deleting the key from Redis.
     *
     * @param  string $sessionId The session id.
     * @return bool
     */
    public function destroy($sessionId) {
        $this->redis->del($this->key_prefix.$sessionId);
    }

    /**
     * Read the session data from Redis.
     *
     * @param  string $sessionId The session id.
     * @return string            The serialized session data.
     */
    public function read($sessionId) {
        $sessionId = $this->key_prefix.$sessionId;
        $sessionData = $this->redis->get($sessionId);

        // Refresh the Expire
        $this->redis->expire($sessionId, $this->maxLifetime);
        return $sessionData;
    }

    /**
     * Write the serialized session data to Redis. This also sets
     * the Redis key EXPIRES time so we don't have to rely on the
     * PHP gc.
     *
     * @param  string $sessionId   The session id.
     * @param  string $sessionData The serialized session data.
     * @return void
     */
    public function write($sessionId, $sessionData) {
        $sessionId = $this->key_prefix.$sessionId;

        // Write the session data to Redis.
        $this->redis->set($sessionId, $sessionData);

        // Set the expire so we don't have to rely on PHP's gc.
        $this->redis->expire($sessionId, $this->maxLifetime);
    }

}