<?php

namespace Encore\Admin\RedisManager\DataType;

class SortedSets extends DataType
{
    /**
     * {@inheritdoc}
     */
    public function fetch(string $key)
    {
        return $this->getConnection()->zrange($key, 0, -1, ['WITHSCORES' => true]);
    }

    public function update(array $params)
    {
        $key = array_get($params, 'key');

        if (array_has($params, 'member')) {
            $member = array_get($params, 'member');
            $score = array_get($params, 'score');
            $this->getConnection()->zadd($key, [$member => $score]);
        }

        if (array_has($params, '_editable')) {
            $score = array_get($params, 'value');
            $member = array_get($params, 'pk');

            $this->getConnection()->zadd($key, [$member => $score]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $params)
    {
        $key = array_get($params, 'key');
        $ttl = array_get($params, 'ttl');
        $score = array_get($params, 'score');
        $member = array_get($params, 'member');

        $this->getConnection()->zadd($key, [$member => $score]);

        if ($ttl > 0) {
            $this->getConnection()->expire($key, $ttl);
        }

        return redirect(route('redis-edit-key', [
            'conn' => request('conn'),
            'key'  => $key,
        ]));
    }

    /**
     * Remove a member from a sorted set.
     *
     * @param array $params
     *
     * @return int
     */
    public function remove(array $params)
    {
        $key = array_get($params, 'key');
        $member = array_get($params, 'member');

        return $this->getConnection()->zrem($key, $member);
    }
}
