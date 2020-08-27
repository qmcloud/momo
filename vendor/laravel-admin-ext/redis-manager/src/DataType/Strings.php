<?php

namespace Encore\Admin\RedisManager\DataType;

class Strings extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch(string $key)
    {
        return $this->getConnection()->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $this->store($params);
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = array_get($params, 'key');
        $value = array_get($params, 'value');
        $ttl = array_get($params, 'ttl');

        $this->getConnection()->set($key, $value);

        if ($ttl > 0) {
            $this->getConnection()->expire($key, $ttl);
        }

        return redirect(route('redis-index', [
            'conn' => request('conn'),
        ]));
    }
}
