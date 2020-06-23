<?php

class DevicePayload {
    private static $LIMIT_KEYS = array('X-Rate-Limit-Limit'=>'rateLimitLimit', 'X-Rate-Limit-Remaining'=>'rateLimitRemaining', 'X-Rate-Limit-Reset'=>'rateLimitReset');

    const DEVICE_URL = 'https://device.jpush.cn/v3/devices/';
    const DEVICE_STATUS_URL = 'https://device.jpush.cn/v3/devices/status/';
    const TAG_URL = 'https://device.jpush.cn/v3/tags/';
    const IS_IN_TAG_URL = 'https://device.jpush.cn/v3/tags/{tag}/registration_ids/{registration_id}';
    const ALIAS_URL = 'https://device.jpush.cn/v3/aliases/';


    private $client;

    /**
     * DevicePayload constructor.
     * @param $client JPush
     */
    public function __construct($client)
    {
        $this->client = $client;
    }


    public function getDevices($registrationId) {
        $url = DevicePayload::DEVICE_URL . $registrationId;
        $response = $this->client->_request($url, JPush::HTTP_GET);
        return $this->__processResp($response);
    }

    public function updateDevice($registrationId, $alias = null, $mobile=null, $addTags = null, $removeTags = null) {
        $payload = array();
        if (!is_string($registrationId)) {
            throw new InvalidArgumentException('Invalid registration_id');
        }

        $aliasIsNull = is_null($alias);
        $mobileIsNull = is_null($mobile);
        $addTagsIsNull = is_null($addTags);
        $removeTagsIsNull = is_null($removeTags);


        if ($aliasIsNull && $addTagsIsNull && $removeTagsIsNull && $mobileIsNull) {
            throw new InvalidArgumentException("alias, addTags, removeTags not all null");
        }

        if (!$aliasIsNull) {
            if (is_string($alias)) {
                $payload['alias'] = $alias;
            } else {
                throw new InvalidArgumentException("Invalid alias string");
            }
        }

        if (!$mobileIsNull) {
            if (is_string($mobile)) {
                $payload['mobile'] = $mobile;
            } else {
                throw new InvalidArgumentException("Invalid mobile string");
            }
        }

        $tags = array();

        if (!$addTagsIsNull) {
            if (is_array($addTags)) {
                $tags['add'] = $addTags;
            } else {
                throw new InvalidArgumentException("Invalid addTags array");
            }
        }

        if (!$removeTagsIsNull) {
            if (is_array($removeTags)) {
                $tags['remove'] = $removeTags;
            } else {
                throw new InvalidArgumentException("Invalid removeTags array");
            }
        }

        if (count($tags) > 0) {
            $payload['tags'] = $tags;
        }

        $url = DevicePayload::DEVICE_URL . $registrationId;
        $response = $this->client->_request($url, JPush::HTTP_POST, json_encode($payload));
        return $this->__processResp($response);
    }

    public function getTags() {
        $response = $this->client->_request(DevicePayload::TAG_URL, JPush::HTTP_GET);
        return $this->__processResp($response);
    }

    public function isDeviceInTag($registrationId, $tag) {
        if (!is_string($registrationId)) {
            throw new InvalidArgumentException("Invalid registration_id");
        }

        if (!is_string($tag)) {
            throw new InvalidArgumentException("Invalid tag");
        }

        $url = str_replace('{tag}', $tag, self::IS_IN_TAG_URL);
        $url = str_replace('{registration_id}', $registrationId, $url);

        $response = $this->client->_request($url, JPush::HTTP_GET);
        return $this->__processResp($response);
    }

    public function updateTag($tag, $addDevices = null, $removeDevices = null) {
        if (!is_string($tag)) {
            throw new InvalidArgumentException("Invalid tag");
        }

        $addDevicesIsNull = is_null($addDevices);
        $removeDevicesIsNull = is_null($removeDevices);

        if ($addDevicesIsNull && $removeDevicesIsNull) {
            throw new InvalidArgumentException("Either or both addDevices and removeDevices must be set.");
        }

        $registrationId = array();

        if (!$addDevicesIsNull) {
            if (is_array($addDevices)) {
                $registrationId['add'] = $addDevices;
            } else {
                throw new InvalidArgumentException("Invalid addDevices");
            }
        }

        if (!$removeDevicesIsNull) {
            if (is_array($removeDevices)) {
                $registrationId['remove'] = $removeDevices;
            } else {
                throw new InvalidArgumentException("Invalid removeDevices");
            }
        }

        $url = DevicePayload::TAG_URL . $tag;
        $payload = array('registration_ids'=>$registrationId);

        $response = $this->client->_request($url, JPush::HTTP_POST, json_encode($payload));
        return $this->__processResp($response);
    }

    public function deleteTag($tag) {
        if (!is_string($tag)) {
            throw new InvalidArgumentException("Invalid tag");
        }
        $url = DevicePayload::TAG_URL . $tag;
        $response = $this->client->_request($url, JPush::HTTP_DELETE);
        return $this->__processResp($response);
    }

    public function getAliasDevices($alias, $platform = null) {
        if (!is_string($alias)) {
            throw new InvalidArgumentException("Invalid alias");
        }

        $url = self::ALIAS_URL . $alias;

        if (!is_null($platform)) {
            if (is_array($platform)) {
                $isFirst = true;
                foreach($platform as $item) {
                    if ($isFirst) {
                        $url = $url . '?platform=' . $item;
                        $isFirst = false;
                    } else {
                        $url = $url . ',' . $item;
                    }
                }
            } else if (is_string($platform)) {
                $url = $url . '?platform=' . $platform;
            } else {
                throw new InvalidArgumentException("Invalid platform");
            }
        }

        $response = $this->client->_request($url, JPush::HTTP_GET);
        return $this->__processResp($response);
    }

    public function deleteAlias($alias) {
        if (!is_string($alias)) {
            throw new InvalidArgumentException("Invalid alias");
        }
        $url = self::ALIAS_URL . $alias;
        $response = $this->client->_request($url, JPush::HTTP_DELETE);
        return $this->__processResp($response);
    }

    public function getDevicesStatus($registrationId) {
        if (!is_array($registrationId) && !is_string($registrationId)) {
            throw new InvalidArgumentException('Invalid registration_id');
        }

        if (is_string($registrationId)) {
            $registrationId = explode(',', $registrationId);
        }

        $payload = array();
        if (count($registrationId) <= 0) {
            throw new InvalidArgumentException('Invalid registration_id');
        }
        $payload['registration_ids'] = $registrationId;


        $response = $this->client->_request(DevicePayload::DEVICE_STATUS_URL, JPush::HTTP_POST, json_encode($payload));
        if($response['http_code'] === 200) {
            $body = array();
            echo $response['body'];
            $body['data'] = (array)json_decode($response['body']);
            $headers = $response['headers'];
            if (is_array($headers)) {
                $limit = array();
                $limit['rateLimitLimit'] = $headers['X-Rate-Limit-Limit'];
                $limit['rateLimitRemaining'] = $headers['X-Rate-Limit-Remaining'];
                $limit['rateLimitReset'] = $headers['X-Rate-Limit-Reset'];
                $body['limit'] = (object)$limit;
                return (object)$body;
            }
            return $body;
        } else {
            throw new APIRequestException($response);
        }
    }

    private function __processResp($response) {
        if($response['http_code'] === 200) {
            $body = array();
            $data = json_decode($response['body']);
            if (!is_null($data)) {
                $body['data'] = json_decode($response['body']);
            }
            $headers = $response['headers'];
            if (is_array($headers)) {
                $limit = array();
                foreach (self::$LIMIT_KEYS as $key => $value) {
                    if (array_key_exists($key, $headers)) {
                        $limit[$value] = $headers[$key];
                    }
                }
                if (count($limit) > 0) {
                    $body['limit'] = (object)$limit;
                }
                return (object)$body;
            }
            return $body;
        } else {
            throw new APIRequestException($response);
        }
    }
}