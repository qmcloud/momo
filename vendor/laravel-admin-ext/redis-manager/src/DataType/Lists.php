<?php

namespace Encore\Admin\RedisManager\DataType;

class Lists extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch(string $key)
    {
        return $this->getConnection()->lrange($key, 0, -1);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $key = array_get($params, 'key');

        if (array_has($params, 'push')) {
            $item = array_get($params, 'item');
            $command = $params['push'] == 'left' ? 'lpush' : 'rpush';

            $this->getConnection()->{$command}($key, $item);
        }

        if (array_has($params, '_editable')) {
            $value = array_get($params, 'value');
            $index = array_get($params, 'pk');

            $this->getConnection()->lset($key, $index, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = array_get($params, 'key');
        $item = array_get($params, 'item');
        $ttl = array_get($params, 'ttl');

        $this->getConnection()->rpush($key, [$item]);

        if ($ttl > 0) {
            $this->getConnection()->expire($key, $ttl);
        }

        return redirect(route('redis-edit-key', [
            'conn' => request('conn'),
            'key'  => $key,
        ]));
    }

    /**
     * Remove a member from list by index.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function remove(array $params)
    {
        $key = array_get($params, 'key');
        $index = array_get($params, 'index');

        $lua = <<<'LUA'
redis.call('lset', KEYS[1], ARGV[1], '__DELETED__');
redis.call('lrem', KEYS[1], 1, '__DELETED__');
LUA;

        return $this->getConnection()->eval($lua, 1, $key, $index);
    }
}
