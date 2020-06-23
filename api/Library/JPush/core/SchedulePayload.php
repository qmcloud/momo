<?php

class SchedulePayload {
    private static $LIMIT_KEYS = array('X-Rate-Limit-Limit'=>'rateLimitLimit', 'X-Rate-Limit-Remaining'=>'rateLimitRemaining', 'X-Rate-Limit-Reset'=>'rateLimitReset');

    const SCHEDULES_URL = 'https://api.jpush.cn/v3/schedules';
    private $client;

    /**
     * SchedulePayload constructor.
     * @param $client JPush
     */
    public function __construct($client) {
        $this->client = $client;
    }


    public function createSingleSchedule($name, $push_payload, $trigger) {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Invalid schedule name');
        }
        if (!is_array($push_payload)) {
            throw new InvalidArgumentException('Invalid schedule push payload');
        }
        if (!is_array($trigger)) {
            throw new InvalidArgumentException('Invalid schedule trigger');
        }
        $payload = array();
        $payload['name'] = $name;
        $payload['enabled'] = true;
        $payload['trigger'] = array("single"=>$trigger);
        $payload['push'] = $push_payload;
        $response = $this->client->_request(SchedulePayload::SCHEDULES_URL, JPush::HTTP_POST, json_encode($payload));
        return $this->__processResp($response);
    }

    public function createPeriodicalSchedule($name, $push_payload, $trigger) {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Invalid schedule name');
        }
        if (!is_array($push_payload)) {
            throw new InvalidArgumentException('Invalid schedule push payload');
        }
        if (!is_array($trigger)) {
            throw new InvalidArgumentException('Invalid schedule trigger');
        }
        $payload = array();
        $payload['name'] = $name;
        $payload['enabled'] = true;
        $payload['trigger'] = array("periodical"=>$trigger);
        $payload['push'] = $push_payload;
        $response = $this->client->_request(SchedulePayload::SCHEDULES_URL, JPush::HTTP_POST, json_encode($payload));
        return $this->__processResp($response);
    }

    public function updateSingleSchedule($schedule_id, $name=null, $enabled=null, $push_payload=null, $trigger=null) {
        if (!is_string($schedule_id)) {
            throw new InvalidArgumentException('Invalid schedule id');
        }
        $payload = array();
        if (!is_null($name)) {
            if (!is_string($name)) {
                throw new InvalidArgumentException('Invalid schedule name');
            } else {
                $payload['name'] = $name;
            }
        }

        if (!is_null($enabled)) {
            if (!is_bool($enabled)) {
                throw new InvalidArgumentException('Invalid schedule enable');
            } else {
                $payload['enabled'] = $enabled;
            }
        }

        if (!is_null($push_payload)) {
            if (!is_array($push_payload)) {
                throw new InvalidArgumentException('Invalid schedule push payload');
            } else {
                $payload['push'] = $push_payload;
            }
        }

        if (!is_null($trigger)) {
            if (!is_array($trigger)) {
                throw new InvalidArgumentException('Invalid schedule trigger');
            } else {
                $payload['trigger'] = array("single"=>$trigger);
            }
        }

        if (count($payload) <= 0) {
            throw new InvalidArgumentException('Invalid schedule, name, enabled, trigger, push can not all be null');
        }

        $url = SchedulePayload::SCHEDULES_URL . "/" . $schedule_id;
        $response = $this->client->_request($url, JPush::HTTP_PUT, json_encode($payload));
        return $this->__processResp($response);
    }

    public function updatePeriodicalSchedule($schedule_id, $name=null, $enabled=null, $push_payload=null, $trigger=null) {
        if (!is_string($schedule_id)) {
            throw new InvalidArgumentException('Invalid schedule id');
        }
        $payload = array();
        if (!is_null($name)) {
            if (!is_string($name)) {
                throw new InvalidArgumentException('Invalid schedule name');
            } else {
                $payload['name'] = $name;
            }
        }

        if (!is_null($enabled)) {
            if (!is_bool($enabled)) {
                throw new InvalidArgumentException('Invalid schedule enable');
            } else {
                $payload['enabled'] = $enabled;
            }
        }

        if (!is_null($push_payload)) {
            if (!is_array($push_payload)) {
                throw new InvalidArgumentException('Invalid schedule push payload');
            } else {
                $payload['push'] = $push_payload;
            }
        }

        if (!is_null($trigger)) {
            if (!is_array($trigger)) {
                throw new InvalidArgumentException('Invalid schedule trigger');
            } else {
                $payload['trigger'] = array("periodical"=>$trigger);
            }
        }

        if (count($payload) <= 0) {
            throw new InvalidArgumentException('Invalid schedule, name, enabled, trigger, push can not all be null');
        }

        $url = SchedulePayload::SCHEDULES_URL . "/" . $schedule_id;
        $response = $this->client->_request($url, JPush::HTTP_PUT, json_encode($payload));
        return $this->__processResp($response);
    }

    public function getSchedules($page=1) {
        if (!is_int($page)) {
            throw new InvalidArgumentException('Invalid pages');
        }
        $url = SchedulePayload::SCHEDULES_URL . "?page=" . $page;
        $response = $this->client->_request($url, JPush::HTTP_GET);
        return $this->__processResp($response);
    }

    public function getSchedule($schedule_id) {
        if (!is_string($schedule_id)) {
            throw new InvalidArgumentException('Invalid schedule id');
        }
        $url = SchedulePayload::SCHEDULES_URL . "/" . $schedule_id;
        $response = $this->client->_request($url, JPush::HTTP_GET);
        return $this->__processResp($response);
    }

    public function deleteSchedule($schedule_id) {
        if (!is_string($schedule_id)) {
            throw new InvalidArgumentException('Invalid schedule id');
        }
        $url = SchedulePayload::SCHEDULES_URL . "/" . $schedule_id;
        $response = $this->client->_request($url, JPush::HTTP_DELETE);
        return $this->__processResp($response);
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

