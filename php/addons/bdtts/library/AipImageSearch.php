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
class AipImageSearch extends AipBase {

    /**
     * 相同图检索—入库 same_hq_add api url
     * @var string
     */
    private $sameHqAddUrl = 'https://aip.baidubce.com/rest/2.0/realtime_search/same_hq/add';

    /**
     * 相同图检索—检索 same_hq_search api url
     * @var string
     */
    private $sameHqSearchUrl = 'https://aip.baidubce.com/rest/2.0/realtime_search/same_hq/search';

    /**
     * 相同图检索—更新 same_hq_update api url
     * @var string
     */
    private $sameHqUpdateUrl = 'https://aip.baidubce.com/rest/2.0/realtime_search/same_hq/update';

    /**
     * 相同图检索—删除 same_hq_delete api url
     * @var string
     */
    private $sameHqDeleteUrl = 'https://aip.baidubce.com/rest/2.0/realtime_search/same_hq/delete';

    /**
     * 相似图检索—入库 similar_add api url
     * @var string
     */
    private $similarAddUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/similar/add';

    /**
     * 相似图检索—检索 similar_search api url
     * @var string
     */
    private $similarSearchUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/similar/search';

    /**
     * 相似图检索—更新 similar_update api url
     * @var string
     */
    private $similarUpdateUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/similar/update';

    /**
     * 相似图检索—删除 similar_delete api url
     * @var string
     */
    private $similarDeleteUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/similar/delete';

    /**
     * 商品检索—入库 product_add api url
     * @var string
     */
    private $productAddUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/product/add';

    /**
     * 商品检索—检索 product_search api url
     * @var string
     */
    private $productSearchUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/product/search';

    /**
     * 商品检索—更新 product_update api url
     * @var string
     */
    private $productUpdateUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/product/update';

    /**
     * 商品检索—删除 product_delete api url
     * @var string
     */
    private $productDeleteUrl = 'https://aip.baidubce.com/rest/2.0/image-classify/v1/realtime_search/product/delete';


    /**
     * 绘本图片搜索—入库-image
     * @var string
     */
    private $picturebookAdd = "https://aip.baidubce.com/rest/2.0/imagesearch/v1/realtime_search/picturebook/add";

    /**
     * 绘本图片搜索—入库-检索
     * @var string
     */
    private $picturebookSearch = "https://aip.baidubce.com/rest/2.0/imagesearch/v1/realtime_search/picturebook/search";

    /**
     * 绘本图片搜索—入库-删除
     * @var string
     */
    private $picturebookDelete = "https://aip.baidubce.com/rest/2.0/imagesearch/v1/realtime_search/picturebook/delete";

    /**
     * 绘本图片搜索—入库-更新
     * @var string
     */
    private $picturebookUpdate = "https://aip.baidubce.com/rest/2.0/imagesearch/v1/realtime_search/picturebook/update";




    /**
     * 相同图检索—入库接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param string $brief - 检索时原样带回,最长256B。
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     * @return array
     */
    public function sameHqAdd($image, $brief, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);
        $data['brief'] = $brief;

        $data = array_merge($data, $options);

        return $this->request($this->sameHqAddUrl, $data);
    }

    /**
     * 相同图检索—入库接口
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param string $brief - 检索时原样带回,最长256B。
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     * @return array
     */
    public function sameHqAddUrl($url, $brief, $options=array()){

        $data = array();
        
        $data['url'] = $url;
        $data['brief'] = $brief;

        $data = array_merge($data, $options);

        return $this->request($this->sameHqAddUrl, $data);
    }

    /**
     * 相同图检索—检索接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     *   tag_logic 检索时tag之间的逻辑， 0：逻辑and，1：逻辑or
     *   pn 分页功能，起始位置，例：0。未指定分页时，默认返回前300个结果；接口返回数量最大限制1000条，例如：起始位置为900，截取条数500条，接口也只返回第900 - 1000条的结果，共计100条
     *   rn 分页功能，截取条数，例：250
     * @return array
     */
    public function sameHqSearch($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->sameHqSearchUrl, $data);
    }

    /**
     * 相同图检索—检索接口
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     *   tag_logic 检索时tag之间的逻辑， 0：逻辑and，1：逻辑or
     *   pn 分页功能，起始位置，例：0。未指定分页时，默认返回前300个结果；接口返回数量最大限制1000条，例如：起始位置为900，截取条数500条，接口也只返回第900 - 1000条的结果，共计100条
     *   rn 分页功能，截取条数，例：250
     * @return array
     */
    public function sameHqSearchUrl($url, $options=array()){

        $data = array();
        
        $data['url'] = $url;

        $data = array_merge($data, $options);

        return $this->request($this->sameHqSearchUrl, $data);
    }

    /**
     * 相同图检索—更新接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   brief 更新的摘要信息，最长256B。样例：{"name":"周杰伦", "id":"666"}
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     * @return array
     */
    public function sameHqUpdate($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->sameHqUpdateUrl, $data);
    }

    /**
     * 相同图检索—更新接口
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   brief 更新的摘要信息，最长256B。样例：{"name":"周杰伦", "id":"666"}
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     * @return array
     */
    public function sameHqUpdateUrl($url, $options=array()){

        $data = array();
        
        $data['url'] = $url;

        $data = array_merge($data, $options);

        return $this->request($this->sameHqUpdateUrl, $data);
    }

    /**
     * 相同图检索—更新接口
     *
     * @param string $contSign - 图片签名
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   brief 更新的摘要信息，最长256B。样例：{"name":"周杰伦", "id":"666"}
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     * @return array
     */
    public function sameHqUpdateContSign($contSign, $options=array()){

        $data = array();
        
        $data['cont_sign'] = $contSign;

        $data = array_merge($data, $options);

        return $this->request($this->sameHqUpdateUrl, $data);
    }

    /**
     * 相同图检索—删除接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function sameHqDeleteByImage($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->sameHqDeleteUrl, $data);
    }

    /**
     * 相同图检索—删除接口
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function sameHqDeleteByUrl($url, $options=array()){

        $data = array();
        
        $data['url'] = $url;

        $data = array_merge($data, $options);

        return $this->request($this->sameHqDeleteUrl, $data);
    }

    /**
     * 相同图检索—删除接口
     *
     * @param string $contSign - 图片签名
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function sameHqDeleteBySign($contSign, $options=array()){

        $data = array();
        
        $data['cont_sign'] = $contSign;

        $data = array_merge($data, $options);

        return $this->request($this->sameHqDeleteUrl, $data);
    }

    /**
     * 相似图检索—入库接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param string $brief - 检索时原样带回,最长256B。
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     * @return array
     */
    public function similarAdd($image, $brief, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);
        $data['brief'] = $brief;

        $data = array_merge($data, $options);

        return $this->request($this->similarAddUrl, $data);
    }

    /**
     * 相似图检索—入库接口
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param string $brief - 检索时原样带回,最长256B。
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     * @return array
     */
    public function similarAddUrl($url, $brief, $options=array()){

        $data = array();
        
        $data['url'] = $url;
        $data['brief'] = $brief;

        $data = array_merge($data, $options);

        return $this->request($this->similarAddUrl, $data);
    }

    /**
     * 相似图检索—检索接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     *   tag_logic 检索时tag之间的逻辑， 0：逻辑and，1：逻辑or
     *   pn 分页功能，起始位置，例：0。未指定分页时，默认返回前300个结果；接口返回数量最大限制1000条，例如：起始位置为900，截取条数500条，接口也只返回第900 - 1000条的结果，共计100条
     *   rn 分页功能，截取条数，例：250
     * @return array
     */
    public function similarSearch($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->similarSearchUrl, $data);
    }

    /**
     * 相似图检索—检索接口
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     *   tag_logic 检索时tag之间的逻辑， 0：逻辑and，1：逻辑or
     *   pn 分页功能，起始位置，例：0。未指定分页时，默认返回前300个结果；接口返回数量最大限制1000条，例如：起始位置为900，截取条数500条，接口也只返回第900 - 1000条的结果，共计100条
     *   rn 分页功能，截取条数，例：250
     * @return array
     */
    public function similarSearchUrl($url, $options=array()){

        $data = array();
        
        $data['url'] = $url;

        $data = array_merge($data, $options);

        return $this->request($this->similarSearchUrl, $data);
    }

    /**
     * 相似图检索—更新接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   brief 更新的摘要信息，最长256B。样例：{"name":"周杰伦", "id":"666"}
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     * @return array
     */
    public function similarUpdate($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->similarUpdateUrl, $data);
    }

    /**
     * 相似图检索—更新接口
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   brief 更新的摘要信息，最长256B。样例：{"name":"周杰伦", "id":"666"}
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     * @return array
     */
    public function similarUpdateUrl($url, $options=array()){

        $data = array();
        
        $data['url'] = $url;

        $data = array_merge($data, $options);

        return $this->request($this->similarUpdateUrl, $data);
    }

    /**
     * 相似图检索—更新接口
     *
     * @param string $contSign - 图片签名
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   brief 更新的摘要信息，最长256B。样例：{"name":"周杰伦", "id":"666"}
     *   tags 1 - 65535范围内的整数，tag间以逗号分隔，最多2个tag。样例："100,11" ；检索时可圈定分类维度进行检索
     * @return array
     */
    public function similarUpdateContSign($contSign, $options=array()){

        $data = array();
        
        $data['cont_sign'] = $contSign;

        $data = array_merge($data, $options);

        return $this->request($this->similarUpdateUrl, $data);
    }

    /**
     * 相似图检索—删除接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function similarDeleteByImage($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->similarDeleteUrl, $data);
    }

    /**
     * 相似图检索—删除接口
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function similarDeleteByUrl($url, $options=array()){

        $data = array();
        
        $data['url'] = $url;

        $data = array_merge($data, $options);

        return $this->request($this->similarDeleteUrl, $data);
    }

    /**
     * 相似图检索—删除接口
     *
     * @param string $contSign - 图片签名
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function similarDeleteBySign($contSign, $options=array()){

        $data = array();
        
        $data['cont_sign'] = $contSign;

        $data = array_merge($data, $options);

        return $this->request($this->similarDeleteUrl, $data);
    }

    /**
     * 商品检索—入库接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param string $brief - 检索时原样带回,最长256B。**请注意，检索接口不返回原图，仅反馈当前填写的brief信息，所以调用该入库接口时，brief信息请尽量填写可关联至本地图库的图片id或者图片url、图片名称等信息**
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   class_id1 商品分类维度1，支持1-65535范围内的整数。检索时可圈定该分类维度进行检索
     *   class_id2 商品分类维度1，支持1-65535范围内的整数。检索时可圈定该分类维度进行检索
     * @return array
     */
    public function productAdd($image, $brief, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);
        $data['brief'] = $brief;

        $data = array_merge($data, $options);

        return $this->request($this->productAddUrl, $data);
    }

    /**
     * 商品检索—入库接口
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param string $brief - 检索时原样带回,最长256B。**请注意，检索接口不返回原图，仅反馈当前填写的brief信息，所以调用该入库接口时，brief信息请尽量填写可关联至本地图库的图片id或者图片url、图片名称等信息**
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   class_id1 商品分类维度1，支持1-65535范围内的整数。检索时可圈定该分类维度进行检索
     *   class_id2 商品分类维度1，支持1-65535范围内的整数。检索时可圈定该分类维度进行检索
     * @return array
     */
    public function productAddUrl($url, $brief, $options=array()){

        $data = array();
        
        $data['url'] = $url;
        $data['brief'] = $brief;

        $data = array_merge($data, $options);

        return $this->request($this->productAddUrl, $data);
    }

    /**
     * 商品检索—检索接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   class_id1 商品分类维度1，支持1-65535范围内的整数。检索时可圈定该分类维度进行检索
     *   class_id2 商品分类维度1，支持1-65535范围内的整数。检索时可圈定该分类维度进行检索
     *   pn 分页功能，起始位置，例：0。未指定分页时，默认返回前300个结果；接口返回数量最大限制1000条，例如：起始位置为900，截取条数500条，接口也只返回第900 - 1000条的结果，共计100条
     *   rn 分页功能，截取条数，例：250
     * @return array
     */
    public function productSearch($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->productSearchUrl, $data);
    }

    /**
     * 商品检索—检索接口
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   class_id1 商品分类维度1，支持1-65535范围内的整数。检索时可圈定该分类维度进行检索
     *   class_id2 商品分类维度1，支持1-65535范围内的整数。检索时可圈定该分类维度进行检索
     *   pn 分页功能，起始位置，例：0。未指定分页时，默认返回前300个结果；接口返回数量最大限制1000条，例如：起始位置为900，截取条数500条，接口也只返回第900 - 1000条的结果，共计100条
     *   rn 分页功能，截取条数，例：250
     * @return array
     */
    public function productSearchUrl($url, $options=array()){

        $data = array();
        
        $data['url'] = $url;

        $data = array_merge($data, $options);

        return $this->request($this->productSearchUrl, $data);
    }

    /**
     * 商品检索—更新接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   brief 更新的摘要信息，最长256B。样例：{"name":"周杰伦", "id":"666"}
     *   class_id1 更新的商品分类1，支持1-65535范围内的整数。
     *   class_id2 更新的商品分类2，支持1-65535范围内的整数。
     * @return array
     */
    public function productUpdate($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->productUpdateUrl, $data);
    }

    /**
     * 商品检索—更新接口
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   brief 更新的摘要信息，最长256B。样例：{"name":"周杰伦", "id":"666"}
     *   class_id1 更新的商品分类1，支持1-65535范围内的整数。
     *   class_id2 更新的商品分类2，支持1-65535范围内的整数。
     * @return array
     */
    public function productUpdateUrl($url, $options=array()){

        $data = array();
        
        $data['url'] = $url;

        $data = array_merge($data, $options);

        return $this->request($this->productUpdateUrl, $data);
    }

    /**
     * 商品检索—更新接口
     *
     * @param string $contSign - 图片签名
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     *   brief 更新的摘要信息，最长256B。样例：{"name":"周杰伦", "id":"666"}
     *   class_id1 更新的商品分类1，支持1-65535范围内的整数。
     *   class_id2 更新的商品分类2，支持1-65535范围内的整数。
     * @return array
     */
    public function productUpdateContSign($contSign, $options=array()){

        $data = array();
        
        $data['cont_sign'] = $contSign;

        $data = array_merge($data, $options);

        return $this->request($this->productUpdateUrl, $data);
    }

    /**
     * 商品检索—删除接口
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function productDeleteByImage($image, $options=array()){

        $data = array();
        
        $data['image'] = base64_encode($image);

        $data = array_merge($data, $options);

        return $this->request($this->productDeleteUrl, $data);
    }

    /**
     * 商品检索—删除接口
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function productDeleteByUrl($url, $options=array()){

        $data = array();
        
        $data['url'] = $url;

        $data = array_merge($data, $options);

        return $this->request($this->productDeleteUrl, $data);
    }

    /**
     * 商品检索—删除接口
     *
     * @param string $contSign - 图片签名
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function productDeleteBySign($contSign, $options=array()){

        $data = array();
        
        $data['cont_sign'] = $contSign;

        $data = array_merge($data, $options);

        return $this->request($this->productDeleteUrl, $data);
    }



    /**
     * 绘本图片搜索—入库-image
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param string $brief - 简介
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function  pictureBookAddImage($image, $brief, $options=array())
    {
        $data = array();
        $data['image'] = base64_encode($image);
        $data['brief'] = $brief;
        $data = array_merge($data, $options);
        return $this->request($this->picturebookAdd, $data);
    }

    /**
     * 绘本图片搜索—入库-url
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param string $brief - 简介
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function  pictureBookAddUrl($url, $brief, $options=array())
    {
        $data = array();
        $data['url'] = $url;
        $data['brief'] = $brief;
        $data = array_merge($data, $options);
        return $this->request($this->picturebookAdd, $data);
    }

    /**
     * 绘本图片搜索—检索-image
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function  pictureBookSearchImage($image, $options=array()) {
        $data = array();
        $data['image'] = base64_encode($image);
        $data = array_merge($data, $options);
        return $this->request($this->picturebookSearch, $data);
    }


    /**
     * 绘本图片搜索—检索-url
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function  pictureBookSearchUrl($url, $options=array()) {
        $data = array();
        $data['url'] = $url;
        $data = array_merge($data, $options);
        return $this->request($this->picturebookSearch, $data);
    }

    /**
     * 绘本图片搜索—更新-image
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function  pictureBookUpdate($image, $options=array()) {
        $data = array();
        $data['image'] = base64_encode($image);
        $data = array_merge($data, $options);
        return $this->request($this->picturebookUpdate, $data);
    }

    /**
     * 绘本图片搜索—更新-url
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function  pictureBookUpdateUrl($url, $options=array())
    {
        $data = array();
        $data['url'] = $url;
        $data = array_merge($data, $options);
        return $this->request($this->picturebookUpdate, $data);
    }

    /**
     * 绘本图片搜索—更新-cont_sign
     *
     * @param string $contSign - 图片签名
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function  pictureBookUpdateContSign($contSign, $options=array())
    {
        $data = array();
        $data['cont_sign'] = $contSign;
        $data = array_merge($data, $options);
        return $this->request($this->picturebookUpdate, $data);
    }

    /**
     * 绘本图片搜索—删除-image
     *
     * @param string $image - 图像数据，base64编码，要求base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function  pictureBookDeleteByImage($image, $options=array())
    {
        $data = array();
        $data['image'] = base64_encode($image);
        $data = array_merge($data, $options);
        return $this->request($this->picturebookDelete, $data);
    }


    /**
     * 绘本图片搜索—删除-url
     *
     * @param string $url - 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/png/bmp格式，当image字段存在时url字段失效
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function  pictureBookDeleteByUrl($url, $options=array())
    {
        $data = array();
        $data['url'] = $url;
        $data = array_merge($data, $options);
        return $this->request($this->picturebookDelete, $data);
    }

    /**
     * 绘本图片搜索—删除-cont_sign
     * @param string $contSign - 图片签名
     * @param array $options - 可选参数对象，key: value都为string类型
     * @description options列表:
     * @return array
     */
    public function  pictureBookDeleteBySign($contSign, $options=array())
    {
        $data = array();
        $data['cont_sign'] = $contSign;
        $data = array_merge($data, $options);
        return $this->request($this->picturebookDelete, $data);
    }











}

