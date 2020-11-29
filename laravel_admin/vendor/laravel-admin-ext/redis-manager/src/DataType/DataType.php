<?php

namespace Encore\Admin\RedisManager\DataType;

use Illuminate\Redis\Connections\Connection;

abstract class DataType
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * DataType constructor.
     *
     * @param $connection
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get redis connection.
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    abstract public function fetch(string $key);

    /**
     * @param array $params
     *
     * @return mixed
     */
    abstract public function update(array $params);

    /**
     * @param array $params
     *
     * @return mixed
     */
    abstract public function store(array $params);

    /**
     * Returns the remaining time to live of a key that has a timeout.
     *
     * @param string $key
     *
     * @return int
     */
    public function ttl($key)
    {
        return $this->getConnection()->ttl($key);
    }

    /**
     * Set a timeout on key.
     *
     * @param string $key
     * @param int    $expire
     *
     * @return void
     */
    public function setTtl($key, $expire)
    {
        if (is_null($expire)) {
            return;
        }

        $expire = (int) $expire;

        if ($expire > 0) {
            $this->getConnection()->expire($key, $expire);
        } else {
            $this->getConnection()->persist($key);
        }
    }
}
