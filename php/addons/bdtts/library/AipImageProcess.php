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
class AipImageProcess extends AipBase {

    /**
     * 图像无损放大 image_quality_enhance api url
     * @var string
     */
    private $imageQualityEnhanceUrl = 'https://aip.baidubce.com/rest/2.0/image-process/v1/image_quality_enhance';

    /**
     * 图像去雾 dehaze api url
     * @var string
     */
    private $dehazeUrl = 'https://aip.baidubce.com/rest/2.0/image-process/v1/dehaze';

    /**
     * 图像对比度增强 contrast_enhance api url
     * @var string
     */
    private $contrastEnhanceUrl = 'https://aip.baidubce.com/rest/2.0/image-process/v1/contrast_enhance';

    /**
     * 黑白图像上色 colourize api url
     * @var string
     */
    private $colourizeUrl = 'https://aip.baidubce.com/rest/2.0/image-process/v1/colourize';

    /**
     * 拉伸图像恢复 stretch_restore api url
     * @var string
     */
    private $stretchRestoreUrl = 'https://aip.baidubce.com/rest/2.0/image-process/v1/stretch_restore';


    /**
     * 风格转换
     * @var string
     */
    private $styleTrans = "https://aip.baidubce.com/rest/2.0/image-process/v1/style_trans";

    /**
     * 图像修复
     * @var string
     */
    private $inpainting = "https://aip.baidubce.com/rest/2.0/image-process/v1/inpainting";

    /**
     * 图像清晰度增强
     * @var string
     */
    private $imageDefinitionEnhance = "https://aip.baidubce.com/rest/2.0/image-process/v1/image_definition_enhance";

    /**
     *人像动漫化
     * @var string
     */
    private $selfieAnime = "https://aip.baidubce.com/rest/2.0/image-process/v1/selfie_anime";

    /**
     * 天空分割
     * @var string
     */
    private $skySeg = "https://aip.baidubce.com/rest/2.0/image-process/v1/sky_seg";


    /**
     * 图像无损放大接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function imageQualityEnhance($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->imageQualityEnhanceUrl, $data);
    }

    /**
     * 图像去雾接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function dehaze($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->dehazeUrl, $data);
    }

    /**
     * 图像对比度增强接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function contrastEnhance($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->contrastEnhanceUrl, $data);
    }

    /**
     * 黑白图像上色接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function colourize($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->colourizeUrl, $data);
    }

    /**
     * 拉伸图像恢复接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function stretchRestore($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->stretchRestoreUrl, $data);
    }


    /**
     * 人像动漫化
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function selfieAnime($image, $options=array()){

        $data = array();

        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->selfieAnime, $data);
    }


    /**
     * 图像清晰度增强
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function imageDefinitionEnhance($image, $options=array()){

        $data = array();

        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->imageDefinitionEnhance, $data);
    }


    /**
     * 图像风格转换
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function __styleTrans($image, $options=array()){

        $data = array();

        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->styleTrans, $data);
    }


    /**
     * 天空分割
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function skySeg($image, $options=array()){

        $data = array();

        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->skySeg, $data);
    }


    /**
     * 图像修复
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function inpaintingByMask($image, $rectangle, $options=array()){

        $data = array();

        $data['image'] = base64_encode($image);
        $data['rectangle'] = $rectangle;

        $data = array_merge($data, $options);

        return $this->request($this->inpainting, $data);
    }

}