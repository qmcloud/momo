<?php

class ReportPayload {
    private static $EFFECTIVE_TIME_UNIT = array('HOUR', 'DAY', 'MONTH');
    private static $LIMIT_KEYS = array('X-Rate-Limit-Limit'=>'rateLimitLimit', 'X-Rate-Limit-Remaining'=>'rateLimitRemaining', 'X-Rate-Limit-Reset'=>'rateLimitReset');
    const REPORT_URL = 'https://report.jpush.cn/v3/received';
    const MESSAGES_URL = 'https://report.jpush.cn/v3/messages';
    const USERS_URL = 'https://report.jpush.cn/v3/users';

    private $client;

    /**
     * ReportPayload constructor.
     * @param $client JPush
     */
    public function __construct($client)
    {
        $this->client = $client;
    }


    public function getReceived($msgIds) {
        $queryParams = '?msg_ids=';
        if (is_array($msgIds) && count($msgIds) > 0) {
            $isFirst = true;
            foreach ($msgIds as $msgId) {
                if ($isFirst) {
                    $queryParams .= $msgId;
                    $isFirst = false;
                } else {
                    $queryParams .= ',';
                    $queryParams .= $msgId;
                }
            }
        } else if (is_string($msgIds)) {
            $queryParams .= $msgIds;
        } else {
            throw new InvalidArgumentException("Invalid msg_ids");
        }

        $url = ReportPayload::REPORT_URL . $queryParams;
        return $this->__request($url);
    }

    public function getMessages($msgIds) {
        $queryParams = '?msg_ids=';
        if (is_array($msgIds) && count($msgIds) > 0) {
            $isFirst = true;
            foreach ($msgIds as $msgId) {
                if ($isFirst) {
                    $queryParams .= $msgId;
                    $isFirst = false;
                } else {
                    $queryParams .= ',';
                    $queryParams .= $msgId;
                }
            }
        } else if (is_string($msgIds)) {
            $queryParams .= $msgIds;
        } else {
            throw new InvalidArgumentException("Invalid msg_ids");
        }

        $url = ReportPayload::MESSAGES_URL . $queryParams;
        return $this->__request($url);
    }

    public function getUsers($time_unit, $start, $duration) {
        $time_unit = strtoupper($time_unit);
        if (!in_array($time_unit, self::$EFFECTIVE_TIME_UNIT)) {
            throw new InvalidArgumentException('Invalid time unit');
        }

        $url = ReportPayload::USERS_URL . '?time_unit=' . $time_unit . '&start=' . $start . '&duration=' . $duration;
        return $this->__request($url);
    }

    private function __request($url) {
        $response = $this->client->_request($url, JPush::HTTP_GET);
        if($response['http_code'] === 200) {
            $body = array();
            $body['data'] = (array)json_decode($response['body']);
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