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

namespace Tx\Mailer;

use Psr\Log\LoggerInterface;
use Tx\Mailer\Exceptions\CodeException;
use Tx\Mailer\Exceptions\CryptoException;
use Tx\Mailer\Exceptions\SMTPException;

class SMTP
{
    /**
     * smtp socket
     */
    protected $smtp;

    /**
     * smtp server
     */
    protected $host;

    /**
     * smtp server port
     */
    protected $port;

    /**
     * smtp secure ssl tls tlsv1.0 tlsv1.1 tlsv1.2
     */
    protected $secure;

    /**
     * EHLO message
     */
    protected $ehlo;

    /**
     * smtp username
     */
    protected $username;

    /**
     * smtp password
     */
    protected $password;

    /**
     * oauth access token
     */
    protected $oauthToken;

    /**
     * $this->CRLF
     * @var string
     */
    protected $CRLF = "\r\n";

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var LoggerInterface - Used to make things prettier than self::$logger
     */
    protected $logger;

    /**
     * Stack of all commands issued to SMTP
     * @var array
     */
    protected $commandStack = array();

    /**
     * Stack of all results issued to SMTP
     * @var array
     */
    protected $resultStack = array();

    public function __construct(LoggerInterface $logger=null)
    {
        $this->logger = $logger;
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
        $this->host = $host;
        $this->port = $port;
        $this->secure = $secure;
        if(!$this->ehlo) $this->ehlo = $host;
        $this->logger && $this->logger->debug("Set: the server");
        return $this;
    }

    /**
     * auth login with server
     * @param string $username
     * @param string $password
     * @return $this
     */
    public function setAuth($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->logger && $this->logger->debug("Set: the auth login");
        return $this;
    }

    /**
     * auth oauthbearer with server
     * @param string $accessToken
     * @return $this
     */
    public function setOAuth($accessToken)
    {
        $this->oauthToken = $accessToken;
        $this->logger && $this->logger->debug("Set: the auth oauthbearer");
        return $this;
    }

    /**
     * set the EHLO message
     * @param $ehlo
     * @return $this
     */
    public function setEhlo($ehlo)
    {
        $this->ehlo = $ehlo;
        return $this;
    }

    /**
     * Send the message
     *
     * @param Message $message
     * @return bool
     * @throws CodeException
     * @throws CryptoException
     * @throws SMTPException
     */
    public function send(Message $message)
    {
        $this->logger && $this->logger->debug('Set: a message will be sent');
        $this->message = $message;
        $this->connect()
            ->ehlo();

        if ($this->secure === 'tls' || $this->secure === 'tlsv1.0' || $this->secure === 'tlsv1.1' | $this->secure === 'tlsv1.2') {
            $this->starttls()
                ->ehlo();
        }

        if ($this->username !== null || $this->password !== null) {
            $this->authLogin();
        } elseif ($this->oauthToken !== null) {
            $this->authOAuthBearer();
        }
        $this->mailFrom()
            ->rcptTo()
            ->data()
            ->quit();
        return fclose($this->smtp);
    }

    /**
     * connect the server
     * SUCCESS 220
     * @return $this
     * @throws CodeException
     * @throws SMTPException
     */
    protected function connect()
    {
        $this->logger && $this->logger->debug("Connecting to {$this->host} at {$this->port}");
        $host = ($this->secure == 'ssl') ? 'ssl://' . $this->host : $this->host;
        $this->smtp = @fsockopen($host, $this->port);
        //set block mode
        //    stream_set_blocking($this->smtp, 1);
        if (!$this->smtp){
            throw new SMTPException("Could not open SMTP Port.");
        }
        $code = $this->getCode();
        if ($code !== '220'){
            throw new CodeException('220', $code, array_pop($this->resultStack));
        }
        return $this;
    }

    /**
     * SMTP STARTTLS
     * SUCCESS 220
     * @return $this
     * @throws CodeException
     * @throws CryptoException
     * @throws SMTPException
     */
    protected function starttls()
    {
        $in = "STARTTLS" . $this->CRLF;
        $code = $this->pushStack($in);
        if ($code !== '220'){
            throw new CodeException('220', $code, array_pop($this->resultStack));
        }

        if ($this->secure !== 'tls' && version_compare(phpversion(), '5.6.0', '<')) {
            throw new CryptoException('Crypto type expected PHP 5.6 or greater');
        }

        switch ($this->secure) {
            case 'tlsv1.0':
                $crypto_type = STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT;
                break;
            case 'tlsv1.1':
                $crypto_type = STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
                break;
            case 'tlsv1.2':
                $crypto_type = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                break;
            default:
                $crypto_type = STREAM_CRYPTO_METHOD_TLS_CLIENT;
                break;
        }

        if(!\stream_socket_enable_crypto($this->smtp, true, $crypto_type)) {
            throw new CryptoException("Start TLS failed to enable crypto");
        }
        return $this;
    }

    /**
     * SMTP EHLO
     * SUCCESS 250
     * @return $this
     * @throws CodeException
     * @throws SMTPException
     */
    protected function ehlo()
    {
        $in = "EHLO " . $this->ehlo . $this->CRLF;
        $code = $this->pushStack($in);
        if ($code !== '250'){
            throw new CodeException('250', $code, array_pop($this->resultStack));
        }
        return $this;
    }

    /**
     * SMTP AUTH LOGIN
     * SUCCESS 334
     * SUCCESS 334
     * SUCCESS 235
     * @return $this
     * @throws CodeException
     * @throws SMTPException
     */
    protected function authLogin()
    {
        $in = "AUTH LOGIN" . $this->CRLF;
        $code = $this->pushStack($in);
        if ($code !== '334'){
            throw new CodeException('334', $code, array_pop($this->resultStack));
        }
        $in = base64_encode($this->username) . $this->CRLF;
        $code = $this->pushStack($in);
        if ($code !== '334'){
            throw new CodeException('334', $code, array_pop($this->resultStack));
        }
        $in = base64_encode($this->password) . $this->CRLF;
        $code = $this->pushStack($in);
        if ($code !== '235'){
            throw new CodeException('235', $code, array_pop($this->resultStack));
        }
        return $this;
    }

    /**
     * SMTP AUTH OAUTHBEARER
     * SUCCESS 235
     * @return $this
     * @throws CodeException
     * @throws SMTPException
     */
    protected function authOAuthBearer()
    {
        $authStr = sprintf("n,a=%s,%shost=%s%sport=%s%sauth=Bearer %s%s%s",
            $this->message->getFromEmail(),
            chr(1),
            $this->host,
            chr(1),
            $this->port,
            chr(1),
            $this->oauthToken,
            chr(1),
            chr(1)
        );
        $authStr = base64_encode($authStr);
        $in = "AUTH OAUTHBEARER $authStr" . $this->CRLF;
        $code = $this->pushStack($in);
        if ($code !== '235'){
            throw new CodeException('235', $code, array_pop($this->resultStack));
        }
        return $this;
    }

    /**
     * SMTP AUTH XOAUTH2
     * SUCCESS 235
     * @return $this
     * @throws CodeException
     * @throws SMTPException
     */
    protected function authXOAuth2()
    {
        $authStr = sprintf("user=%s%sauth=Bearer %s%s%s",
            $this->message->getFromEmail(),
            chr(1),
            $this->oauthToken,
            chr(1),
            chr(1)
        );
        $authStr = base64_encode($authStr);
        $in = "AUTH XOAUTH2 $authStr" . $this->CRLF;
        $code = $this->pushStack($in);
        if ($code !== '235'){
            throw new CodeException('235', $code, array_pop($this->resultStack));
        }
        return $this;
    }

    /**
     * SMTP MAIL FROM
     * SUCCESS 250
     * @return $this
     * @throws CodeException
     * @throws SMTPException
     */
    protected function mailFrom()
    {
        $in = "MAIL FROM:<{$this->message->getFromEmail()}>" . $this->CRLF;
        $code = $this->pushStack($in);
        if ($code !== '250') {
            throw new CodeException('250', $code, array_pop($this->resultStack));
        }
        return $this;
    }

    /**
     * SMTP RCPT TO
     * SUCCESS 250
     * @return $this
     * @throws CodeException
     * @throws SMTPException
     */
    protected function rcptTo()
    {
        $to = array_merge(
            $this->message->getTo(),
            $this->message->getCc(),
            $this->message->getBcc()
        );
        foreach ($to as $toEmail=>$_) {
            $in = "RCPT TO:<" . $toEmail . ">" . $this->CRLF;
            $code = $this->pushStack($in);
            if ($code !== '250') {
                throw new CodeException('250', $code, array_pop($this->resultStack));
            }
        }
        return $this;
    }

    /**
     * SMTP DATA
     * SUCCESS 354
     * SUCCESS 250
     * @return $this
     * @throws CodeException
     * @throws SMTPException
     */
    protected function data()
    {
        $in = "DATA" . $this->CRLF;
        $code = $this->pushStack($in);
        if ($code !== '354') {
            throw new CodeException('354', $code, array_pop($this->resultStack));
        }
        $in = $this->message->toString();
        $code = $this->pushStack($in);
        if ($code !== '250'){
            throw new CodeException('250', $code, array_pop($this->resultStack));
        }
        return $this;
    }

    /**
     * SMTP QUIT
     * SUCCESS 221
     * @return $this
     * @throws CodeException
     * @throws SMTPException
     */
    protected function quit()
    {
        $in = "QUIT" . $this->CRLF;
        $code = $this->pushStack($in);
        if ($code !== '221'){
            throw new CodeException('221', $code, array_pop($this->resultStack));
        }
        return $this;
    }

    protected function pushStack($string)
    {
        $this->commandStack[] = $string;
        fputs($this->smtp, $string, strlen($string));
        $this->logger && $this->logger->debug('Sent: '. $string);
        return $this->getCode();
    }

    /**
     * get smtp response code
     * once time has three digital and a space
     * @return string
     * @throws SMTPException
     */
    protected function getCode()
    {
        while ($str = fgets($this->smtp, 515)) {
            $this->logger && $this->logger->debug("Got: ". $str);
            $this->resultStack[] = $str;
            if(substr($str,3,1) == " ") {
                $code = substr($str,0,3);
                return $code;
            }
        }
        throw new SMTPException("SMTP Server did not respond with anything I recognized");
    }

}

