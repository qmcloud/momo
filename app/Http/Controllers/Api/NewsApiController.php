<?php
/**
 * Created by PhpStorm.
 * User: jll
 * Date: 2018/3/7
 * Time: 18:28
 */
namespace App\Http\Controllers\Api;


use App\Models\News;

class NewsApiController extends ApiController
{
    //新闻列表
    public function getList()
    {
        $allnews = News::orderBy('created_at','desc')->paginate(6);
        return $this->success($allnews);
    }

    /**
     * 新闻详情信息
     */
    public function show($detail)
    {
        $news = News::find($detail);
        return $this->success($news);
    }
}