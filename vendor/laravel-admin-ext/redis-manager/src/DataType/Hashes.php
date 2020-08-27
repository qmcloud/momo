<?php

namespace Encore\Admin\RedisManager\DataType;

class Hashes extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch(string $key)
    {
        return $this->getConnection()->hgetall($key);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $key = array_get($params, 'key');

        if (array_has($params, 'field')) {
            $field = array_get($params, 'field');
            $value = array_get($params, 'value');

            $this->getConnection()->hset($key, $field, $value);
        }

        if (array_has($params, '_editable')) {
            $value = array_get($params, 'value');
            $field = array_get($params, 'pk');

            $this->getConnection()->hset($key, $field, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = array_get($params, 'key');
        $ttl = array_get($params, 'ttl');
        $field = array_get($params, 'field');
        $value = array_get($params, 'value');

        $this->getConnection()->hset($key, $field, $value);

        if ($ttl > 0) {
            $this->getConnection()->expire($key, $ttl);
        }

        return redirect(route('redis-edit-key', [
            'conn' => request('conn'),
            'key'  => $key,
        ]));
    }

    /**
     * Remove a field from a hash.
     *
     * @param array $params
     *
     * @return int
     */
    public function remove(array $params)
    {
        $key = array_get($params, 'key');
        $field = array_get($params, 'field');

        return $this->getConnection()->hdel($key, [$field]);
    }
}
