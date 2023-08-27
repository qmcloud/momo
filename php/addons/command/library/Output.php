<?php

namespace addons\command\library;

/**
 * Class Output
 */
class Output extends \think\console\Output
{

    protected $message = [];

    public function __construct($driver = 'console')
    {
        parent::__construct($driver);
    }

    protected function block($style, $message)
    {
        $this->message[] = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }

}
