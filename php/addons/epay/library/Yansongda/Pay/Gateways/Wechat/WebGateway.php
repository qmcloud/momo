<?php

namespace Yansongda\Pay\Gateways\Wechat;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Yansongda\Pay\Events;
use Yansongda\Pay\Exceptions\GatewayException;
use Yansongda\Pay\Exceptions\InvalidArgumentException;
use Yansongda\Pay\Exceptions\InvalidSignException;
use Yansongda\Supports\Collection;

class WebGateway extends Gateway
{
    /**
     * Pay an order.
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @author yansongda <me@yansongda.cn>
     *
     */
    public function pay($endpoint, array $payload): Response
    {
        $payload['spbill_create_ip'] = Request::createFromGlobals()->server->get('SERVER_ADDR');
        $payload['trade_type'] = $this->getTradeType();

        $code_url = $this->preOrder($payload)['code_url'];
        $params = [
            'body'         => $payload['body'],
            'code_url'     => $code_url,
            'out_trade_no' => $payload['out_trade_no'],
            'return_url'   => Support::getInstance()->return_url,
            'total_fee'    => $payload['total_fee'],
        ];

        $params['sign'] = md5(implode('', $params) . Support::getInstance()->app_id);
        $endpoint = addon_url("epay/api/wechat");

        Events::dispatch(new Events\PayStarted('Wechat', 'Web/Wap', $endpoint, $payload));

        return $this->buildPayHtml($endpoint, $params);
    }

    /**
     * Build Html response.
     *
     * @param string $endpoint
     * @param array  $payload
     * @param string $method
     *
     * @return Response
     * @author yansongda <me@yansongda.cn>
     *
     */
    protected function buildPayHtml($endpoint, $payload, $method = 'POST'): Response
    {
        if (strtoupper($method) === 'GET') {
            return RedirectResponse::create($endpoint . '?' . http_build_query($payload));
        }

        $sHtml = "<form id='wechat_submit' name='wechat_submit' action='" . $endpoint . "' method='" . $method . "'>";
        foreach ($payload as $key => $val) {
            $val = str_replace("'", '&apos;', $val);
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        $sHtml .= "<input type='submit' value='ok' style='display:none;'></form>";
        $sHtml .= "<script>document.forms['wechat_submit'].submit();</script>";

        return Response::create($sHtml);
    }

    /**
     * Get trade type config.
     *
     * @return string
     * @author yansongda <me@yansongda.cn>
     *
     */
    protected function getTradeType(): string
    {
        return 'NATIVE';
    }
}
