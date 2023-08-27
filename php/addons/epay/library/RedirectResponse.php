<?php

namespace addons\epay\library;

class RedirectResponse extends \Symfony\Component\HttpFoundation\RedirectResponse implements \JsonSerializable, \Serializable
{
    public function __toString()
    {
        return $this->getContent();
    }

    public function setTargetUrl($url)
    {
        if ('' === ($url ?? '')) {
            throw new \InvalidArgumentException('无法跳转到空页面');
        }

        $this->targetUrl = $url;

        $this->setContent(
            sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="0;url=\'%1$s\'" />

        <title>正在跳转支付 %1$s</title>
    </head>
    <body>
        <div id="redirect" style="display:none;">正在跳转支付 <a href="%1$s">%1$s</a></div>
        <script type="text/javascript">
            setTimeout(function(){
                document.getElementById("redirect").style.display = "block";
            }, 1000);
        </script>
    </body>
</html>', htmlspecialchars($url, \ENT_QUOTES, 'UTF-8')));

        $this->headers->set('Location', $url);

        return $this;
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
