<?php

namespace addons\alioss;

use think\Addons;

/**
 * 阿里云OSS上传插件
 */
class Alioss extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 加载配置
     */
    public function uploadConfigInit(&$upload)
    {
        $config = $this->getConfig();
        if ($config['uploadmode'] === 'client')
        {
            $upload = [
                'cdnurl'    => $config['cdnurl'],
                'uploadurl' => 'http://' . $config['bucket'] . '.' . $config['endpoint'],
                'bucket'    => $config['bucket'],
                'maxsize'   => $config['maxsize'],
                'mimetype'  => $config['mimetype'],
                'multipart' => [],
                'multiple'  => $config['multiple'] ? true : false,
                'storage'   => 'alioss'
            ];
        }
        else
        {
            $upload = array_merge($upload, [
                'maxsize'  => $config['maxsize'],
                'mimetype' => $config['mimetype'],
                'multiple' => $config['multiple'] ? true : false,
            ]);
        }
    }

    /**
     * 上传成功后
     */
    public function uploadAfter($attachment)
    {
        $config = $this->getConfig();
        if ($config['uploadmode'] === 'server')
        {
            $file = ROOT_PATH . 'public' . str_replace('/', DIRECTORY_SEPARATOR, $attachment->url);

            $name = basename($file);
            $md5 = md5_file($file);

            $auth = new \addons\alioss\library\Auth();
            $params = $auth->params($name, $md5, false);
            $multipart = [
                [
                    'name'     => 'key',
                    'contents' => $params['key'],
                ],
                [
                    'name'     => 'success_action_status',
                    'contents' => 200,
                ],
                [
                    'name'     => 'OSSAccessKeyId',
                    'contents' => $params['id'],
                ],
                [
                    'name'     => 'policy',
                    'contents' => $params['policy'],
                ],
                [
                    'name'     => 'Signature',
                    'contents' => $params['signature'],
                ],
                [
                    'name'     => 'file',
                    'contents' => fopen($file, 'r'),
                ],
            ];
            try
            {
                $uploadurl = 'http://' . $config['bucket'] . '.' . $config['endpoint'];

                $client = new \GuzzleHttp\Client();
//                $res = $client->request('POST', $uploadurl, [
//                    'multipart' => $multipart,
//                    'headers'   => ['Accept-Encoding' => 'gzip'],
//                ]);
                
                $multipartStream = new \GuzzleHttp\Psr7\MultipartStream($multipart);
                $boundary = $multipartStream->getBoundary();
                $body = (string) $multipartStream;
                //默认的request方法会添加Content-Length字段，但Alioss不识别，所以需要移除
                $body = preg_replace('/Content\-Length:\s(\d+)[\r\n]+Content\-Type/i', "Content-Type", $body);
                $params = [
                    'headers' => [
                        'Connection'   => 'close',
                        'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
                    ],
                    'body'    => $body,
                ];

                $res = $client->request('POST', $uploadurl, $params);
                $code = $res->getStatusCode();
                //成功不做任何操作
            }
            catch (\GuzzleHttp\Exception\ClientException $e)
            {
                $attachment->delete();
                //echo $e->getRequest()->getBody();
                unlink($file);
                echo json_encode(['code' => 0, 'msg' => '无法上传到远程服务器，错误:' . $e->getMessage()]);
                exit;
            }
        }
    }

}
