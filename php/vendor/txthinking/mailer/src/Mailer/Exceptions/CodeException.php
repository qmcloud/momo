<?php
/***************************************************\
 *
 *  Mailer (https://github.com/txthinking/Mailer)
 *
 *  A lightweight PHP SMTP mail sender.
 *  Implement RFC0821, RFC0822, RFC1869, RFC2045, RFC2821
 *
 *  Support html body, don't worry that the receiver's
 *  mail client can't support html, because Mailer will
 *  send both text/plain and text/html body, so if the
 *  mail client can't support html, it will display the
 *  text/plain body.
 *
 *  Create Date 2012-07-25.
 *  Under the MIT license.
 *
 \***************************************************/
/**
 * Created by PhpStorm.
 * User: msowers
 * Date: 3/30/15
 * Time: 2:42 PM
 */

namespace Tx\Mailer\Exceptions;


class CodeException extends SMTPException
{
    public function __construct($expected, $received, $serverMessage = null)
    {
        $message = "Unexpected return code - Expected: {$expected}, Got: {$received}";
        if (isset($serverMessage)) {
            $message .= " | " . $serverMessage;
        }
        parent::__construct($message);
    }

}
