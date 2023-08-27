<?php

namespace addons\qrcode\controller;

use think\addons\Controller;
use think\exception\HttpResponseException;
use think\Response;

/**
 * 二维码生成
 *
 */
class Index extends Controller
{
    public function index()
    {
        return $this->view->fetch();
    }

    // 生成二维码
    public function build()
    {
        $config = get_addon_config('qrcode');
        if (isset($config['limitreferer']) && $config['limitreferer']) {
            $referer = $this->request->server('HTTP_REFERER', '');
            $refererInfo = parse_url($referer);
            $refererHost = $referer && $refererInfo ? $refererInfo['host'] : '';
            $refererHostArr = explode('.', $refererHost);
            $wildcardDomain = '';
            if (count($refererHostArr) > 2) {
                $refererHostArr[0] = '*';
                $wildcardDomain = implode('.', $refererHostArr);
            }
            $allowRefererList = $config['allowrefererlist'] ?? '';
            $domainArr = explode("\n", str_replace("\r", "", $allowRefererList));
            $domainArr = array_filter(array_unique($domainArr));
            $domainArr[] = request()->host(true);

            $inAllowList = false;

            if (in_array('*', $domainArr) || ($refererHost && in_array($refererHost, $domainArr)) || ($wildcardDomain && in_array($wildcardDomain, $domainArr))) {
                $inAllowList = true;
            }

            if (!$inAllowList && (!$referer && $config['allowemptyreferer'])) {
                $inAllowList = true;
            }

            if (!$inAllowList) {
                $response = Response::create('暂无权限', 'html', 403);
                throw new HttpResponseException($response);
            }
        }
        $params = $this->request->get();
        $params = array_intersect_key($params, array_flip(['text', 'size', 'padding', 'errorlevel', 'foreground', 'background', 'logo', 'logosize', 'logopath', 'label', 'labelfontsize', 'labelalignment']));

        $params['text'] = $this->request->get('text', $config['text'], 'trim');
        $params['label'] = $this->request->get('label', $config['label'], 'trim');

        $qrCode = \addons\qrcode\library\Service::qrcode($params);

        $mimetype = $config['format'] == 'png' ? 'image/png' : 'image/svg+xml';

        $response = Response::create()->header("Content-Type", $mimetype);

        // 直接显示二维码
        header('Content-Type: ' . $qrCode->getContentType());
        $response->content($qrCode->writeString());

        // 写入到文件
        if ($config['writefile']) {
            $qrcodePath = ROOT_PATH . 'public/uploads/qrcode/';
            if (!is_dir($qrcodePath)) {
                @mkdir($qrcodePath);
            }
            if (is_really_writable($qrcodePath)) {
                $filePath = $qrcodePath . md5(implode('', $params)) . '.' . $config['format'];
                $qrCode->writeFile($filePath);
            }
        }

        return $response;
    }
}
