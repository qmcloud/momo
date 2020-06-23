<?php
namespace JPush;
use InvalidArgumentException;

class SchedulePayload {

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

        $url = SchedulePayload::SCHEDULES_URL;
        return Http::post($this->client, $url, $payload);
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

        $url = SchedulePayload::SCHEDULES_URL;
        return Http::post($this->client, $url, $payload);
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

        return Http::put($this->client, $url, $payload);

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
        return Http::put($this->client, $url, $payload);
    }

    public function getSchedules($page = 1) {
        if (!is_int($page)) {
            $page = 1;
        }
        $url = SchedulePayload::SCHEDULES_URL . "?page=" . $page;
        return Http::get($this->client, $url);
    }

    public function getSchedule($schedule_id) {
        if (!is_string($schedule_id)) {
            throw new InvalidArgumentException('Invalid schedule id');
        }
        $url = SchedulePayload::SCHEDULES_URL . "/" . $schedule_id;
        return Http::get($this->client, $url);
    }

    public function deleteSchedule($schedule_id) {
        if (!is_string($schedule_id)) {
            throw new InvalidArgumentException('Invalid schedule id');
        }
        $url = SchedulePayload::SCHEDULES_URL . "/" . $schedule_id;
        return Http::delete($this->client, $url);
    }
}

