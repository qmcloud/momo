<?php
namespace JPush;
use InvalidArgumentException;

class PushPayload {

    private static $EFFECTIVE_DEVICE_TYPES = array('ios', 'android', 'winphone');
    const PUSH_URL = 'https://api.jpush.cn/v3/push';
    const PUSH_VALIDATE_URL = 'https://api.jpush.cn/v3/push/validate';

    private $client;
    private $platform;

    private $audience;
    private $tags;
    private $tagAnds;
    private $alias;
    private $registrationIds;

    private $notificationAlert;
    private $iosNotification;
    private $androidNotification;
    private $winPhoneNotification;
    private $smsMessage;
    private $message;
    private $options;

    /**
     * PushPayload constructor.
     * @param $client JPush
     */
    function __construct($client) {
        $this->client = $client;
    }

    public function setPlatform($platform) {
        # $required_keys = array('all', 'android', 'ios', 'winphone');
        if (is_string($platform)) {
            $ptf = strtolower($platform);
            if ('all' === $ptf) {
                $this->platform = 'all';
            } elseif (in_array($ptf, self::$EFFECTIVE_DEVICE_TYPES)) {
                $this->platform = array($ptf);
            }
        } elseif (is_array($platform)) {
            $ptf = array_map('strtolower', $platform);
            $this->platform = array_intersect($ptf, self::$EFFECTIVE_DEVICE_TYPES);
        }
        return $this;
    }

    public function setAudience($all) {
        if (strtolower($all) === 'all') {
            $this->addAllAudience();
            return $this;
        } else {
            throw new InvalidArgumentException('Invalid audience value');
        }
    }

    public function addAllAudience() {
        $this->audience = "all";
        return $this;
    }

    public function addTag($tag) {
        if (is_null($this->tags)) {
            $this->tags = array();
        }

        if (is_array($tag)) {
            foreach($tag as $_tag) {
                if (!is_string($_tag)) {
                    throw new InvalidArgumentException("Invalid tag value");
                }
                if (!in_array($_tag, $this->tags)) {
                    array_push($this->tags, $_tag);
                }
            }
        } else if (is_string($tag)) {
            if (!in_array($tag, $this->tags)) {
                array_push($this->tags, $tag);
            }
        } else {
            throw new InvalidArgumentException("Invalid tag value");
        }

        return $this;

    }

    public function addTagAnd($tag) {
        if (is_null($this->tagAnds)) {
            $this->tagAnds = array();
        }

        if (is_array($tag)) {
            foreach($tag as $_tag) {
                if (!is_string($_tag)) {
                    throw new InvalidArgumentException("Invalid tag_and value");
                }
                if (!in_array($_tag, $this->tagAnds)) {
                    array_push($this->tagAnds, $_tag);
                }
            }
        } else if (is_string($tag)) {
            if (!in_array($tag, $this->tagAnds)) {
                array_push($this->tagAnds, $tag);
            }
        } else {
            throw new InvalidArgumentException("Invalid tag_and value");
        }

        return $this;
    }

    public function addAlias($alias) {
        if (is_null($this->alias)) {
            $this->alias = array();
        }

        if (is_array($alias)) {
            foreach($alias as $_alias) {
                if (!is_string($_alias)) {
                    throw new InvalidArgumentException("Invalid alias value");
                }
                if (!in_array($_alias, $this->alias)) {
                    array_push($this->alias, $_alias);
                }
            }
        } else if (is_string($alias)) {
            if (!in_array($alias, $this->alias)) {
                array_push($this->alias, $alias);
            }
        } else {
            throw new InvalidArgumentException("Invalid alias value");
        }

        return $this;
    }

    public function addRegistrationId($registrationId) {
        if (is_null($this->registrationIds)) {
            $this->registrationIds = array();
        }

        if (is_array($registrationId)) {
            foreach($registrationId as $_registrationId) {
                if (!is_string($_registrationId)) {
                    throw new InvalidArgumentException("Invalid registration_id value");
                }
                if (!in_array($_registrationId, $this->registrationIds)) {
                    array_push($this->registrationIds, $_registrationId);
                }
            }
        } else if (is_string($registrationId)) {
            if (!in_array($registrationId, $this->registrationIds)) {
                array_push($this->registrationIds, $registrationId);
            }
        } else {
            throw new InvalidArgumentException("Invalid registration_id value");
        }

        return $this;
    }

    public function setNotificationAlert($alert) {
        if (!is_string($alert)) {
            throw new InvalidArgumentException("Invalid alert value");
        }
        $this->notificationAlert = $alert;
        return $this;
    }

    public function addWinPhoneNotification($alert=null, $title=null, $_open_page=null, $extras=null) {
        $winPhone = array();

        if (!is_null($alert)) {
            if (!is_string($alert)) {
                throw new InvalidArgumentException("Invalid winphone notification");
            }
            $winPhone['alert'] = $alert;
        }

        if (!is_null($title)) {
            if (!is_string($title)) {
                throw new InvalidArgumentException("Invalid winphone title notification");
            }
            if(strlen($title) > 0) {
                $winPhone['title'] = $title;
            }
        }

        if (!is_null($_open_page)) {
            if (!is_string($_open_page)) {
                throw new InvalidArgumentException("Invalid winphone _open_page notification");
            }
            if (strlen($_open_page) > 0) {
                $winPhone['_open_page'] = $_open_page;
            }
        }

        if (!is_null($extras)) {
            if (!is_array($extras)) {
                throw new InvalidArgumentException("Invalid winphone extras notification");
            }
            if (count($extras) > 0) {
                $winPhone['extras'] = $extras;
            }
        }

        if (count($winPhone) <= 0) {
            throw new InvalidArgumentException("Invalid winphone notification");
        }

        $this->winPhoneNotification = $winPhone;
        return $this;
    }

    public function setSmsMessage($content, $delay_time = 0) {
        $sms = array();
        if (is_string($content) && mb_strlen($content) < 480) {
            $sms['content'] = $content;
        } else {
            throw new InvalidArgumentException('Invalid sms content, sms content\'s length must in [0, 480]');
        }

        $sms['delay_time'] = ($delay_time === 0 || (is_int($delay_time) && $delay_time > 0 && $delay_time <= 86400)) ? $delay_time : 0;

        $this->smsMessage = $sms;
        return $this;
    }

    public function build() {
        $payload = array();

        // validate platform
        if (is_null($this->platform)) {
            throw new InvalidArgumentException("platform must be set");
        }
        $payload["platform"] = $this->platform;

        // validate audience
        $audience = array();
        if (!is_null($this->tags)) {
            $audience["tag"] = $this->tags;
        }
        if (!is_null($this->tagAnds)) {
            $audience["tag_and"] = $this->tagAnds;
        }
        if (!is_null($this->alias)) {
            $audience["alias"] = $this->alias;
        }
        if (!is_null($this->registrationIds)) {
            $audience["registration_id"] = $this->registrationIds;
        }

        if (is_null($this->audience) && count($audience) <= 0) {
            throw new InvalidArgumentException("audience must be set");
        } else if (!is_null($this->audience) && count($audience) > 0) {
            throw new InvalidArgumentException("you can't add tags/alias/registration_id/tag_and when audience='all'");
        } else if (is_null($this->audience)) {
            $payload["audience"] = $audience;
        } else {
            $payload["audience"] = $this->audience;
        }


        // validate notification
        $notification = array();

        if (!is_null($this->notificationAlert)) {
            $notification['alert'] = $this->notificationAlert;
        }

        if (!is_null($this->androidNotification)) {
            $notification['android'] = $this->androidNotification;
            if (is_null($this->androidNotification['alert'])) {
                if (is_null($this->notificationAlert)) {
                    throw new InvalidArgumentException("Android alert can not be null");
                } else {
                    $notification['android']['alert'] = $this->notificationAlert;
                }
            }
        }

        if (!is_null($this->iosNotification)) {
            $notification['ios'] = $this->iosNotification;
            if (is_null($this->iosNotification['alert'])) {
                if (is_null($this->notificationAlert)) {
                    throw new InvalidArgumentException("iOS alert can not be null");
                } else {
                    $notification['ios']['alert'] = $this->notificationAlert;
                }
            }
        }

        if (!is_null($this->winPhoneNotification)) {
            $notification['winphone'] = $this->winPhoneNotification;
            if (is_null($this->winPhoneNotification['alert'])) {
                if (is_null($this->winPhoneNotification)) {
                    throw new InvalidArgumentException("WinPhone alert can not be null");
                } else {
                    $notification['winphone']['alert'] = $this->notificationAlert;
                }
            }
        }

        if (count($notification) > 0) {
            $payload['notification'] = $notification;
        }

        if (count($this->message) > 0) {
            $payload['message'] = $this->message;
        }
        if (!array_key_exists('notification', $payload) && !array_key_exists('message', $payload)) {
            throw new InvalidArgumentException('notification and message can not all be null');
        }

        if (count($this->smsMessage)) {
            $payload['sms_message'] = $this->smsMessage;
        }

        if (is_null($this->options)) {
            $this->options();
        }

        $payload['options'] = $this->options;

        return $payload;
    }

    public function toJSON() {
        $payload = $this->build();
        return json_encode($payload);
    }

    public function printJSON() {
        echo $this->toJSON();
        return $this;
    }

    public function send() {
        $url = PushPayload::PUSH_URL;
        return Http::post($this->client, $url, $this->build());
    }

    public function validate() {
        $url = PushPayload::PUSH_VALIDATE_URL;
        return Http::post($this->client, $url, $this->build());
    }

    private function generateSendno() {
        return rand(100000, getrandmax());
    }

    # new methods
    public function iosNotification($alert = '', array $notification = array()) {
        # $required_keys = array('sound', 'badge', 'content-available', 'mutable-content', category', 'extras');
        $ios = array();
        $ios['alert'] = (is_string($alert) || is_array($alert)) ? $alert : '';
        if (!empty($notification)) {
            if (isset($notification['sound']) && is_string($notification['sound'])) {
                $ios['sound'] = $notification['sound'];
            }
            if (isset($notification['badge'])) {
                $ios['badge'] = (int)$notification['badge'] ? $notification['badge'] : 0;
            }
            if (isset($notification['content-available']) && is_bool($notification['content-available']) && $notification['content-available']) {
                $ios['content-available'] = $notification['content-available'];
            }
            if (isset($notification['mutable-content']) && is_bool($notification['mutable-content']) && $notification['mutable-content']) {
                $ios['mutable-content'] = $notification['mutable-content'];
            }
            if (isset($notification['category']) && is_string($notification['category'])) {
                $ios['category'] = $notification['category'];
            }
            if (isset($notification['extras']) && is_array($notification['extras']) && !empty($notification['extras'])) {
                $ios['extras'] = $notification['extras'];
            }
        }
        if (!isset($ios['sound'])) {
            $ios['sound'] = '';
        }
        if (!isset($ios['badge'])) {
            $ios['badge'] = '+1';
        }
        $this->iosNotification = $ios;
        return $this;
    }

    public function androidNotification($alert = '', array $notification = array()) {
        # $required_keys = array('title', 'builder_id', 'extras');
        $android = array();
        $android['alert'] = is_string($alert) ? $alert : '';
        if (!empty($notification)) {
            if (isset($notification['title']) && is_string($notification['title'])) {
                $android['title'] = $notification['title'];
            }
            if (isset($notification['builder_id']) && is_int($notification['builder_id'])) {
                $android['builder_id'] = $notification['builder_id'];
            }
            if (isset($notification['extras']) && is_array($notification['extras']) && !empty($notification['extras'])) {
                $android['extras'] = $notification['extras'];
            }
            if (isset($notification['priority']) && is_int($notification['priority'])) {
                $android['priority'] = $notification['priority'];
            }
            if (isset($notification['category']) && is_string($notification['category'])) {
                $android['category'] = $notification['category`'];
            }
            if (isset($notification['style']) && is_int($notification['style'])) {
                $android['style'] = $notification['style'];
            }
            if (isset($notification['big_text']) && is_string($notification['big_text'])) {
                $android['big_text'] = $notification['big_text'];
            }
            if (isset($notification['inbox']) && is_array($notification['inbox'])) {
                $android['inbox'] = $notification['inbox'];
            }
            if (isset($notification['big_pic_path']) && is_string($notification['big_pic_path'])) {
                $android['big_pic_path'] = $notification['big_pic_path'];
            }
        }
        $this->androidNotification = $android;
        return $this;
    }

    public function message($msg_content, array $msg = array()) {
        # $required_keys = array('title', 'content_type', 'extras');
        if (is_string($msg_content)) {
            $message = array();
            $message['msg_content'] = $msg_content;
            if (!empty($msg)) {
                if (isset($msg['title']) && is_string($msg['title'])) {
                    $message['title'] = $msg['title'];
                }
                if (isset($msg['content_type']) && is_string($msg['content_type'])) {
                    $message['content_type'] = $msg['content_type'];
                }
                if (isset($msg['extras']) && is_array($msg['extras']) && !empty($msg['extras'])) {
                    $message['extras'] = $msg['extras'];
                }
            }
            $this->message = $message;
        }
        return $this;
    }

    public function options(array $opts = array()) {
        # $required_keys = array('sendno', 'time_to_live', 'override_msg_id', 'apns_production', 'big_push_duration');
        $options = array();
        if (isset($opts['sendno']) && is_int($opts['sendno'])) {
            $options['sendno'] = $opts['sendno'];
        } else {
            $options['sendno'] = $this->generateSendno();
        }
        if (isset($opts['time_to_live']) && is_int($opts['time_to_live']) && $opts['time_to_live'] <= 864000 && $opts['time_to_live'] >= 0) {
            $options['time_to_live'] = $opts['time_to_live'];
        }
        if (isset($opts['override_msg_id']) && is_long($opts['override_msg_id'])) {
            $options['override_msg_id'] = $opts['override_msg_id'];
        }
        if (isset($opts['apns_production']) && is_bool($opts['apns_production'])) {
            $options['apns_production'] = $opts['apns_production'];
        } else {
            $options['apns_production'] = false;
        }
        if (isset($opts['big_push_duration']) && is_int($opts['big_push_duration']) && $opts['big_push_duration'] <= 1400 && $opts['big_push_duration'] >= 0) {
            $options['big_push_duration'] = $opts['big_push_duration'];
        }
        $this->options = $options;

        return $this;
    }

    ###############################################################################
    ############# 以下函数已过期，不推荐使用，仅作为兼容接口存在 #########################
    ###############################################################################
    public function addIosNotification($alert=null, $sound=null, $badge=null, $content_available=null, $category=null, $extras=null) {
        $ios = array();

        if (!is_null($alert)) {
            if (!is_string($alert) && !is_array($alert)) {
                throw new InvalidArgumentException("Invalid ios alert value");
            }
            $ios['alert'] = $alert;
        }

        if (!is_null($sound)) {
            if (!is_string($sound)) {
                throw new InvalidArgumentException("Invalid ios sound value");
            }
            if ($sound !== Config::DISABLE_SOUND) {
                $ios['sound'] = $sound;
            }
        } else {
            // 默认sound为''
            $ios['sound'] = '';
        }

        if (!is_null($badge)) {
            if (is_string($badge) && !preg_match("/^[+-]{1}[0-9]{1,3}$/", $badge)) {
                if (!is_int($badge)) {
                    throw new InvalidArgumentException("Invalid ios badge value");
                }
            }
            if ($badge != Config::DISABLE_BADGE) {
                $ios['badge'] = $badge;
            }
        } else {
            // 默认badge为'+1'
            $ios['badge'] = '+1';
        }

        if (!is_null($content_available)) {
            if (!is_bool($content_available)) {
                throw new InvalidArgumentException("Invalid ios content-available value");
            }
            $ios['content-available'] = $content_available;
        }

        if (!is_null($category)) {
            if (!is_string($category)) {
                throw new InvalidArgumentException("Invalid ios category value");
            }
            if (strlen($category)) {
                $ios['category'] = $category;
            }
        }

        if (!is_null($extras)) {
            if (!is_array($extras)) {
                throw new InvalidArgumentException("Invalid ios extras value");
            }
            if (count($extras) > 0) {
                $ios['extras'] = $extras;
            }
        }

        if (count($ios) <= 0) {
            throw new InvalidArgumentException("Invalid iOS notification");
        }

        $this->iosNotification = $ios;
        return $this;
    }

    public function addAndroidNotification($alert=null, $title=null, $builderId=null, $extras=null) {
        $android = array();

        if (!is_null($alert)) {
            if (!is_string($alert)) {
                throw new InvalidArgumentException("Invalid android alert value");
            }
            $android['alert'] = $alert;
        }

        if (!is_null($title)) {
            if(!is_string($title)) {
                throw new InvalidArgumentException("Invalid android title value");
            }
            if(strlen($title) > 0) {
                $android['title'] = $title;
            }
        }

        if (!is_null($builderId)) {
            if (!is_int($builderId)) {
                throw new InvalidArgumentException("Invalid android builder_id value");
            }
            $android['builder_id'] = $builderId;
        }

        if (!is_null($extras)) {
            if (!is_array($extras)) {
                throw new InvalidArgumentException("Invalid android extras value");
            }
            if (count($extras) > 0) {
                $android['extras'] = $extras;
            }
        }

        if (count($android) <= 0) {
            throw new InvalidArgumentException("Invalid android notification");
        }

        $this->androidNotification = $android;
        return $this;
    }

    public function setMessage($msg_content, $title=null, $content_type=null, $extras=null) {
        $message = array();

        if (is_null($msg_content) || !is_string($msg_content)) {
            throw new InvalidArgumentException("Invalid message content");
        } else {
            $message['msg_content'] = $msg_content;
        }

        if (!is_null($title)) {
            if (!is_string($title)) {
                throw new InvalidArgumentException("Invalid message title");
            }
            $message['title'] = $title;
        }

        if (!is_null($content_type)) {
            if (!is_string($content_type)) {
                throw new InvalidArgumentException("Invalid message content type");
            }
            $message["content_type"] = $content_type;
        }

        if (!is_null($extras)) {
            if (!is_array($extras)) {
                throw new InvalidArgumentException("Invalid message extras");
            }
            if (count($extras) > 0) {
                $message['extras'] = $extras;
            }
        }

        $this->message = $message;
        return $this;
    }

    public function setOptions($sendno=null, $time_to_live=null, $override_msg_id=null, $apns_production=null, $big_push_duration=null) {
        $options = array();

        if (!is_null($sendno)) {
            if (!is_int($sendno)) {
                throw new InvalidArgumentException('Invalid option sendno');
            }
            $options['sendno'] = $sendno;
        } else {
            $options['sendno'] = $this->generateSendno();
        }

        if (!is_null($time_to_live)) {
            if (!is_int($time_to_live) || $time_to_live < 0 || $time_to_live > 864000) {
                throw new InvalidArgumentException('Invalid option time to live, it must be a int and in [0, 864000]');
            }
            $options['time_to_live'] = $time_to_live;
        }

        if (!is_null($override_msg_id)) {
            if (!is_long($override_msg_id)) {
                throw new InvalidArgumentException('Invalid option override msg id');
            }
            $options['override_msg_id'] = $override_msg_id;
        }

        if (!is_null($apns_production)) {
            if (!is_bool($apns_production)) {
                throw new InvalidArgumentException('Invalid option apns production');
            }
            $options['apns_production'] = $apns_production;
        } else {
            $options['apns_production'] = false;
        }

        if (!is_null($big_push_duration)) {
            if (!is_int($big_push_duration) || $big_push_duration < 0 || $big_push_duration > 1440) {
                throw new InvalidArgumentException('Invalid option big push duration, it must be a int and in [0, 1440]');
            }
            $options['big_push_duration'] = $big_push_duration;
        }

        $this->options = $options;
        return $this;
    }
}
