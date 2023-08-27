<?php

namespace addons\bdtts\controller;

use think\addons\Controller;
use addons\bdtts\library\AipSpeech;

class Index extends Controller
{

    public function index()
    {

        $config = get_addon_config('bdtts');
        if (empty($config['appid']) || empty($config['apikey']) || empty($config['secret'])) {
            $this->error('请先配置必要的参数');
        }
        $post = $this->request->post();
        if (!(isset($post['tex']) && !empty($post['tex']))) {
            $this->error('必填参数为空');
        }

        $client = new AipSpeech($config['appid'], $config['apikey'], $config['secret']);
        $temp_path = ROOT_PATH . 'public' . DS . 'uploads' . DS . 'bdtts';
        if (!is_dir($temp_path)) {
            $dirres = mkdir($temp_path, 0700);
            if (!$dirres) {
                $this->error('无法自动创建缓存目录！请手动创建:' . $temp_path);
            }
        }
        $spd = 5;
        if (isset($post['spd']) && !empty($post['spd'])) {
            $spd = intval($post['spd']);
        }
        $pit = 5;
        if (isset($post['pit']) && !empty($post['pit'])) {
            $pit = intval($post['pit']);
        }
        $vol = 5;
        if (isset($post['vol']) && !empty($post['vol'])) {
            $vol = intval($post['vol']);
        }
        $per = 0;
        if (isset($post['per']) && !empty($post['per'])) {
            $per = intval($post['per']);
        }
        $rnd = substr(time(), -4);
        $file_name = date('Ymd') . $rnd . rand(0, 9) . rand(0, 9) . rand(0, 9) . '.wav';
        $filename = request()->domain() . DS . 'uploads/bdtts/' . $file_name;
        $file_name = $temp_path . DS . $file_name;

        //文本过长进行分段
        $len = mb_strlen($post['tex']);
        if ($len >= 5000) {
            $this->error('合成字符串单次内容过长');
        }
        $duan = 300; //每个分段的字符串

        if ($len <= $duan) {

            $result = $client->synthesis($post['tex'], 'zh', 1, array(
                'vol' => $vol,
                'pit' => $pit,
                'spd' => $spd,
                'per' => $per,
            ));

            if (!is_array($result)) {
                file_put_contents($file_name, $result);
                $data = ['realpath' => $file_name, 'filename' => $filename];
                return $this->success("成功", '', $data);
            } else {
                $this->error('错误信息：' . $result['err_msg']);
            }

        } else {
            $f = ceil($len / $duan);
            $s = 0;
            $err_msg = '';
            for ($i = 0; $i < $f; $i++) {

                $len_str = mb_substr($post['tex'], $duan * $i, $duan);
                $result = $client->synthesis($len_str, 'zh', 1, array(
                    'vol' => $vol,
                    'pit' => $pit,
                    'spd' => $spd,
                    'per' => $per,
                ));
                if (!is_array($result)) {
                    $s++;
                    file_put_contents($file_name, $result, FILE_APPEND);
                } else {
                    $err_msg = $result['err_msg'];
                }

            }
            if ($s > 0) {
                $data = ['realpath' => $file_name, 'filename' => $filename];
                $this->success("成功", '', $data);
            } else {
                $this->error('错误信息：' . $err_msg);
            }
        }

    }

}
