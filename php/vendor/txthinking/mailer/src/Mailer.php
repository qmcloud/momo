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
namespace Tx;

use Psr\Log\LoggerInterface;
use \Tx\Mailer\Message;
use \Tx\Mailer\SMTP;

/**
 * Class Mailer
 *
 * This class provides the Mailer public methods for backwards compatibility, but it is recommended
 * that you use the Tx\Mailer\SMTP and Tx\Mailer\Message classes going forward
 *
 * @package Tx
 */
class Mailer
{
    /**
     * SMTP Class
     * @var SMTP
     */
    protected $smtp;

    /**
     * Mail Message
     * @var Message
     */
    protected $message;

    /**
     * construct function
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger=null)
    {
        $this->smtp = new SMTP($logger);
        $this->message = new Message();
    }

    /**
     * set server and port
     * @param string $host server
     * @param int $port port
     * @param string $secure ssl tls tlsv1.0 tlsv1.1 tlsv1.2
     * @return $this
     */
    public function setServer($host, $port, $secure=null)
    {
        $this->smtp->setServer($host, $port, $secure);
        return $this;
    }

    /**
     * auth with server
     * @param string $username
     * @param string $password
     * @return $this
     */
    public function setAuth($username, $password)
    {
        $this->smtp->setAuth($username, $password);
        return $this;
    }

    /**
     * auth oauthbearer with server
     * @param string $accessToken
     * @return $this
     */
    public function setOAuth($accessToken)
    {
        $this->smtp->setOAuth($accessToken);
        return $this;
    }

    /**
     * set mail from
     * @param string $name
     * @param string $email
     * @return $this
     */
    public function setFrom($name, $email)
    {
        $this->message->setFrom($name, $email);
        return $this;
    }

    /**
     * set fake mail from
     * @param string $name
     * @param string $email
     * @return $this
     */
    public function setFakeFrom($name, $email)
    {
        $this->message->setFakeFrom($name, $email);
        return $this;
    }

    /**
     * add mail receiver
     * @param string $name
     * @param string $email
     * @return $this
     */
    public function addTo($name, $email)
    {
        $this->message->addTo($name, $email);
        return $this;
    }

    /**
     * add cc mail receiver
     * @param string $name
     * @param string $email
     * @return $this
     */
    public function addCc($name, $email)
    {
        $this->message->addCc($name, $email);
        return $this;
    }

    /**
     * add bcc mail receiver
     * @param string $name
     * @param string $email
     * @return $this
     */
    public function addBcc($name, $email)
    {
        $this->message->addBcc($name, $email);
        return $this;
    }

    /**
     * set mail subject
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->message->setSubject($subject);
        return $this;
    }

    /**
     * set mail body
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->message->setBody($body);
        return $this;
    }

    /**
     * add mail attachment
     * @param $name
     * @param $path
     * @return $this
     */
    public function addAttachment($name, $path)
    {
        $this->message->addAttachment($name, $path);
        return $this;
    }

    /**
     *  Send the message...
     * @return boolean
     */
    public function send()
    {
        return $this->smtp->send($this->message);
    }

}

