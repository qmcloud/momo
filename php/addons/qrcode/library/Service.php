<?php

namespace addons\qrcode\library;

use Endroid\QrCode\ErrorCorrectionLevel;

class Service
{
    /**
     * 生成二维码
     * @param $params
     * @return \Endroid\QrCode\QrCode
     * @throws \Endroid\QrCode\Exception\InvalidPathException
     */
    public static function qrcode($params)
    {
        $config = get_addon_config('qrcode');
        $params = is_array($params) ? $params : [$params];
        $params = array_merge($config, $params);

        $params['labelfontpath'] = ROOT_PATH . 'public' . $config['labelfontpath'];
        $params['logopath'] = ROOT_PATH . 'public' . $config['logopath'];

        // 前景色
        list($r, $g, $b) = sscanf($params['foreground'], "#%02x%02x%02x");
        $foregroundcolor = ['r' => $r, 'g' => $g, 'b' => $b];

        // 背景色
        list($r, $g, $b) = sscanf($params['background'], "#%02x%02x%02x");
        $backgroundcolor = ['r' => $r, 'g' => $g, 'b' => $b];

        // 创建实例
        $qrCode = new \Endroid\QrCode\QrCode($params['text']);
        $qrCode->setSize($params['size']);

        // 高级选项
        $qrCode->setWriterByName($params['format']);
        $qrCode->setMargin($params['padding']);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel($params['errorlevel']));
        $qrCode->setForegroundColor($foregroundcolor);
        $qrCode->setBackgroundColor($backgroundcolor);

        // 设置标签
        if (isset($params['label']) && $params['label']) {
            $qrCode->setLabel($params['label'], $params['labelfontsize'], $params['labelfontpath'], $params['labelalignment']);
        }

        // 设置Logo
        if (isset($params['logo']) && $params['logo']) {
            $qrCode->setLogoPath($params['logopath']);
            $qrCode->setLogoSize($params['logosize'], $params['logosize']);
        }

        $qrCode->setRoundBlockSize(true);
        $qrCode->setValidateResult(false);
        $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

        return $qrCode;
    }
}
