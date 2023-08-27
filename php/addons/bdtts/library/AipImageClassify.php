<?php
/*
* Copyright (c) 2017 Baidu.com, Inc. All Rights Reserved
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/

require_once 'lib/AipBase.php';
class AipImageClassify extends AipBase {

    /**
     * 通用物体识别 advanced_general api url
     * @var string
     */
    private $advancedGeneralUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v2/advanced_general';

    /**
     * 菜品识别 dish_detect api url
     * @var string
     */
    private $dishDetectUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v2/dish';

    /**
     * 车辆识别 car_detect api url
     * @var string
     */
    private $carDetectUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/car';

    /**
     * 车辆检测 vehicle_detect api url
     * @var string
     */
    private $vehicleDetectUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/vehicle_detect';

    /**
     * 车辆外观损伤识别 vehicle_damage api url
     * @var string
     */
    private $vehicleDamageUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/vehicle_damage';

    /**
     * logo商标识别 logo_search api url
     * @var string
     */
    private $logoSearchUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v2/logo';

    /**
     * logo商标识别—添加 logo_add api url
     * @var string
     */
    private $logoAddUrl = 'https://aip.baidubce.com/rest/2.0/realtime_search/v1/logo/add';

    /**
     * logo商标识别—删除 logo_delete api url
     * @var string
     */
    private $logoDeleteUrl = 'https://aip.baidubce.com/rest/2.0/realtime_search/v1/logo/delete';

    /**
     * 动物识别 animal_detect api url
     * @var string
     */
    private $animalDetectUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/animal';

    /**
     * 植物识别 plant_detect api url
     * @var string
     */
    private $plantDetectUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/plant';

    /**
     * 图像主体检测 object_detect api url
     * @var string
     */
    private $objectDetectUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/object_detect';

    /**
     * 地标识别 landmark api url
     * @var string
     */
    private $landmarkUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/landmark';

    /**
     * 花卉识别 flower api url
     * @var string
     */
    private $flowerUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/flower';

    /**
     * 食材识别 ingredient api url
     * @var string
     */
    private $ingredientUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/classify/ingredient';

    /**
     * 红酒识别 redwine api url
     * @var string
     */
    private $redwineUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/redwine';

    /**
     * 货币识别 currency api url
     * @var string
     */
    private $currencyUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/currency';

    /**
     * 菜品识别-添加
     * @var string
     */
    private $customDishAddUrl = "https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/dish/add";
    /**
     * 菜品识别-搜索
     * @var string
     */
    private $customDishSearchUrl = "https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/dish/search";
    /**
     * 菜品识别-删除
     * @var string
     */
    private $customDishDeleteUrl = "https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/dish/delete";
    /**
     * 多目标识别
     * @var string
     */
    private $multiObjectDetectUrl = "https://aip.baidubce.com/rest/2.0/image-classify/v1/multi_object_detect";
    /**
     * 组合接口
     * @var string
     */
    private $combinationUrl = "https://aip.baidubce.com/api/v1/solution/direct/imagerecognition/combination";



    /**
     * 通用物体识别接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   baike_num 返回百科信息的结果数，默认不返回
     * @return array
     */
    public function advancedGeneral($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->advancedGeneralUrl, $data);
    }

    /**
     * 菜品识别接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   top_num 返回预测得分top结果数，默认为5
     *   filter_threshold 默认0.95，可以通过该参数调节识别效果，降低非菜识别率.
     *   baike_num 返回百科信息的结果数，默认不返回
     * @return array
     */
    public function dishDetect($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->dishDetectUrl, $data);
    }

    /**
     * 车辆识别接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   top_num 返回预测得分top结果数，默认为5
     *   baike_num 返回百科信息的结果数，默认不返回
     * @return array
     */
    public function carDetect($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->carDetectUrl, $data);
    }

    /**
     * 车辆检测接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   show 是否返回结果图（含统计值和跟踪框）。选true时返回渲染后的图片(base64)，其它无效值或为空则默认false。
     *   area 只统计该区域内的车辆数，缺省时为全图统计。<br>逗号分隔，如‘x1,y1,x2,y2,x3,y3...xn,yn'，按顺序依次给出每个顶点的x、y坐标（默认尾点和首点相连），形成闭合多边形区域。<br>服务会做范围（顶点左边需在图像范围内）及个数校验（数组长度必须为偶数，且大于3个顶点）。只支持单个多边形区域，建议设置矩形框，即4个顶点。**坐标取值不能超过图像宽度和高度，比如1280的宽度，坐标值最大到1279**。
     * @return array
     */
    public function vehicleDetect($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->vehicleDetectUrl, $data);
    }

    /**
     * 车辆外观损伤识别接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function vehicleDamage($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->vehicleDamageUrl, $data);
    }

    /**
     * logo商标识别接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   custom_lib 是否只使用自定义logo库的结果，默认false：返回自定义库+默认库的识别结果
     * @return array
     */
    public function logoSearch($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->logoSearchUrl, $data);
    }

    /**
     * logo商标识别—添加接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param string $brief - brief，检索时带回。此处要传对应的name与code字段，name长度小于100B，code长度小于150B
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function logoAdd($image, $brief, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);
        $data['brief'] = $brief;

        $data = array_merge($data, $options);

        return $this->request($this->logoAddUrl, $data);
    }

    /**
     * logo商标识别—删除接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function logoDeleteByImage($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->logoDeleteUrl, $data);
    }

    /**
     * logo商标识别—删除接口
     *
     * @param string $contSign - 图片签名（和image二选一，image优先级更高）
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function logoDeleteBySign($contSign, $options=array()){

        $data = array();
        
        $data['cont_sign'] = $contSign;

        $data = array_merge($data, $options);

        return $this->request($this->logoDeleteUrl, $data);
    }

    /**
     * 动物识别接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   top_num 返回预测得分top结果数，默认为6
     *   baike_num 返回百科信息的结果数，默认不返回
     * @return array
     */
    public function animalDetect($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->animalDetectUrl, $data);
    }

    /**
     * 植物识别接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   baike_num 返回百科信息的结果数，默认不返回
     * @return array
     */
    public function plantDetect($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->plantDetectUrl, $data);
    }

    /**
     * 图像主体检测接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   with_face 如果检测主体是人，主体区域是否带上人脸部分，0-不带人脸区域，其他-带人脸区域，裁剪类需求推荐带人脸，检索/识别类需求推荐不带人脸。默认取1，带人脸。
     * @return array
     */
    public function objectDetect($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->objectDetectUrl, $data);
    }

    /**
     * 地标识别接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function landmark($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->landmarkUrl, $data);
    }

    /**
     * 花卉识别接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   top_num 返回预测得分top结果数，默认为5
     *   baike_num 返回百科信息的结果数，默认不返回
     * @return array
     */
    public function flower($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->flowerUrl, $data);
    }

    /**
     * 食材识别接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   top_num 返回预测得分top结果数，如果为空或小于等于0默认为5；如果大于20默认20
     * @return array
     */
    public function ingredient($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->ingredientUrl, $data);
    }

    /**
     * 红酒识别接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function redwine($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->redwineUrl, $data);
    }

    /**
     * 货币识别接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function currency($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->currencyUrl, $data);
    }

    /**
     * 自定义菜品识别—入库
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function customDishesAddImage($image, $brief, $options=array()){

        $data = array();

        $data['image'] = base64_encode($image);

        $data['brief'] = $brief;

        $data = array_merge($data, $options);

        return $this->request($this->customDishAddUrl, $data);
    }


    /**
     * 自定义菜品识别—检索
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function customDishesSearch($image, $options=array()){

        $data = array();

        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->customDishSearchUrl, $data);
    }

    /**
     * 自定义菜品识别—删除
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function customDishesDeleteImage($image, $options=array()){

        $data = array();

        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->customDishDeleteUrl, $data);
    }



    /**
     * 自定义菜品识别—删除
     *
     * @param string $image - 图像数据签名
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function customDishesDeleteContSign($contSign, $options=array()){

        $data = array();

        $data['cont_sign'] = $contSign;

        $data = array_merge($data, $options);

        return $this->request($this->customDishDeleteUrl, $data);
    }



    /**
     * 图像多主体检测
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function multiObjectDetect($image, $options=array()){

        $data = array();

        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->multiObjectDetectUrl, $data);
    }



    /**
     * 组合接口-image
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function combinationByImage($image, $scenes, $options=array()){

        $data = array();

        $data['image'] = base64_encode($image);
        $data['scenes'] = $scenes;

        $data = array_merge($data, $options);

        return $this->request($this->combinationUrl, json_encode($data), array('Content-Type' => 'application/json;charset=utf-8'));
    }



    /**
     * 组合接口-imageUrl
     *
     * @param string $imageURl - 图像数据url
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function combinationByImageUrl($imageUrl, $scenes, $options=array()){

        $data = array();

        $data['imgUrl'] = $imageUrl;
        $data['scenes'] = $scenes;

        $data = array_merge($data, $options);

        return $this->request($this->combinationUrl, json_encode($data), array('Content-Type' => 'application/json;charset=utf-8'));
    }



}