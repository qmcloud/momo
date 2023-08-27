<?php

namespace addons\epay\library;

class Response extends \Symfony\Component\HttpFoundation\Response implements \JsonSerializable, \Serializable
{
    public function __toString()
    {
        return $this->getContent();
    }

    public function jsonSerialize()
    {
        return $this->getContent();
    }

    public function serialize()
    {
        return serialize($this->content);
    }

    public function unserialize($serialized)
    {
        return $this->content = unserialize($serialized);
    }
}
