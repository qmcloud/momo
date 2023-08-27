<?php

declare(strict_types=1);

namespace Yansongda\Supports\Traits;

use RuntimeException;

trait Serializable
{
    /**
     * toJson.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return string
     */
    public function toJson()
    {
        return $this->serialize();
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @see   https://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource
     *
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        if (method_exists($this, 'toArray')) {
            return $this->toArray();
        }

        return [];
    }

    /**
     * String representation of object.
     *
     * @see   https://php.net/manual/en/serializable.serialize.php
     *
     * @return string the string representation of the object or null
     *
     * @since 5.1.0
     */
    public function serialize()
    {
        if (method_exists($this, 'toArray')) {
            return json_encode($this->toArray());
        }

        return json_encode([]);
    }

    /**
     * Constructs the object.
     *
     * @see   https://php.net/manual/en/serializable.unserialize.php
     *
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     *
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        $data = json_decode($serialized, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException('Invalid Json Format');
        }

        foreach ($data as $key => $item) {
            if (method_exists($this, 'set')) {
                $this->set($key, $item);
            }
        }
    }
}
