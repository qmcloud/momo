<?php

namespace Encore\Admin\RedisManager\DataType;

class Sets extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch(string $key)
    {
        return $this->getConnection()->smembers($key);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $params)
    {
        $key = array_get($params, 'key');

        if (array_has($params, 'member')) {
            $member = array_get($params, 'member');
            $this->getConnection()->sadd($key, $member);
        }

        if (array_has($params, '_editable')) {
            $new = array_get($params, 'value');
            $old = array_get($params, 'pk');

            $this->getConnection()->transaction(function ($tx) use ($key, $old, $new) {
                $tx->srem($key, $old);
                $tx->sadd($key, $new);
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = array_get($params, 'key');
        $ttl = array_get($params, 'ttl');
        $members = array_get($params, 'members');

        $this->getConnection()->sadd($key, $members);

        if ($ttl > 0) {
            $this->getConnection()->expire($key, $ttl);
        }

        return redirect(route('redis-edit-key', [
            'conn' => request('conn'),
            'key'  => $key,
        ]));
    }

    /**
     * Remove a member from a set.
     *
     * @param array $params
     *
     * @return int
     */
    public function remove(array $params)
    {
        $key = array_get($params, 'key');
        $member = array_get($params, 'member');

        return $this->getConnection()->srem($key, $member);
    }
}
