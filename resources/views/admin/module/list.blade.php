@extends('admin.module.base.base')
@section('content')
    <style type="text/css">
        .mb-item-edit-content {
            background: #EFFAFE url(http://test.121mai.com/admin/templates/default/images/cms_edit_bg_line.png) repeat-y scroll 0 0;
        }
        .item_x{
            font-size: 12px;
            vertical-align: top;
            letter-spacing: normal;
            display: inline-block;
            width: 300px;
            margin: 0 0 8px 8px;
            border: solid 1px #DDD;
            line-height: 0;
            background-color: #FFF;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
        }
        .goods-pic_x{
            line-height: 0;
            background-color: #FFF;
            text-align: center;
            vertical-align: middle;
            width: 290px;
            height: 114px;
            overflow: hidden;
        }
        .goods-pic_x img {
            max-width: 145px;
            max-height: 145px;
            margin-top: expression(145-this.height/2);
        }
    </style>
    <div id="append_parent"></div>
    <div id="ajaxwaitid"></div>
    <div class="page">
        <!-- 页面导航 -->
        <div class="fixed-empty"></div>
        <!-- 帮助 -->
        <table class="table tb-type2" id="prompt">
            <tbody>
            <tr>
                <td>
                    <ul>
                        <li>点击右侧组件的<strong>“添加”</strong>按钮，增加对应类型版块到页面，其中<strong>“广告条版块”</strong>只能添加一个。</li>
                        <li>鼠标触及左侧页面对应版块，出现操作类链接，可以对该区域块进行<strong>“移动”、“启用/禁用”、“编辑”、“删除”</strong>操作。</li>
                        <li>新增加的版块内容默认为<strong>“禁用”</strong>状态，编辑内容并<strong>“启用”</strong>该块后将在手机端即时显示。</li>
                    </ul>
                </td>
            </tr>
            </tbody>
        </table>
        <!-- 列表 -->
        <div class="mb-special-layout">
            <div class="mb-item-box">
                <div id="item_list" class="item-list">

                    {{--adv--}}
                    @foreach($list as $item)
                        @switch($item->item_type)
                            @case('adv')
                                <div nctype="special_item" class="special-item adv_list
                                @if ($item->item_status ==1)
                                        usable
                                @else
                                        unusable
                                @endif
                              " data-item-id="{{$item->id}}" data-item-sort="{{$item->sort}}" >
                                    <div class="item_type">
                                        轮播图
                                    </div>
                                    <div id="item_edit_content">
                                        <div class="index_block adv_list">
                                            <div nctype="item_content" class="content" id="sortable_adv_list">
                                                <div nctype="item_image" class="item">
                                                    @if ($item->carousels &&!$item->carousels->isEmpty())
                                                        <img nctype="image" src="{{config('filesystems.disks.oss.url').'/'.$item->carousels[0]['carousel_img']}}" alt="">
                                                    @else
                                                        <img nctype="image" src="{{asset('images/defaultimg.jpg')}}" alt="">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="handle">
                                        <a nctype="btn_move_up" href="javascript:;"><i class="icon-arrow-up"></i>上移</a>
                                        <a nctype="btn_move_down" href="javascript:;"><i class="icon-arrow-down"></i>下移</a>
                                        @if ($item->item_status ==1)
                                            <a nctype="btn_usable" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-off"></i>禁用</a>
                                        @else
                                            <a nctype="btn_usable" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-off"></i>启用</a>
                                        @endif
                                        <a nctype="btn_edit_item" data-item-id="{{$item->id}}" href="javascript:;" data-item-url="{{admin_base_path('module-adv').'/'.$item->id.'/edit?module=1&specialId='.$specialId}}" >
                                            <i class="icon-edit"></i>编辑
                                        </a>
                                        <a nctype="btn_del_item" data-item-id="{{$item->id}}" href="javascript:;">
                                            <i class="icon-trash"></i>删除
                                        </a>
                                    </div>
                                </div>
                            @break

                            @case('moduleB')
                                <div nctype="special_item" class="special-item goods
                                @if ($item->item_status ==1)
                                        usable
                                @else
                                        unusable
                                @endif
                                        " data-item-id="{{$item->id}}"  data-item-sort="{{$item->sort}}" >
                                    <div class="item_type">
                                        商品版块
                                    </div>
                                    <div id="item_edit_content">
                                        <!--v3-v12-->
                                        <div class="index_block goods-list">
                                            <div class="title">
                                                <span></span>
                                            </div>
                                            <div nctype="item_content" class="content" id="sortable">
                                                @foreach($item['goodsList'] as $goods)
                                                <div nctype="item_image" class="item">
                                                    <div class="goods-pic">
                                                        @if ($goods && $goods->primary_pic_url)
                                                            <img nctype="image" src="{{config('filesystems.disks.oss.url').'/'.$goods->primary_pic_url}}" alt="">
                                                        @else
                                                            <img nctype="goods_image" src="{{asset('images/defaultimg.jpg')}}" alt="">
                                                        @endif

                                                    </div>
                                                    <div class="goods-name" nctype="goods_name">
                                                        {{$goods->goods_name}}
                                                    </div>
                                                    <div class="goods-price" nctype="goods_price">
                                                        ￥{{$goods->retail_price}}
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="handle">
                                        <a nctype="btn_move_up" href="javascript:;"><i class="icon-arrow-up"></i>上移</a>
                                        <a nctype="btn_move_down" href="javascript:;"><i class="icon-arrow-down"></i>下移</a>
                                        @if ($item->item_status ==1)
                                            <a nctype="btn_usable" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-off"></i>禁用</a>
                                        @else
                                            <a nctype="btn_usable" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-off"></i>启用</a>
                                        @endif
                                        <a nctype="btn_edit_item"  data-item-url="{{admin_base_path('module-b').'/'.$item->id.'/edit?module=1&specialId='.$specialId}}" data-item-id="{{$item->id}}"  href="javascript:;"><i class="icon-edit"></i>编辑</a>
                                        <a nctype="btn_del_item" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-trash"></i>删除</a>
                                    </div>
                                </div>
                                @break

                            @case('moduleC')
                            <div nctype="special_item" class="special-item home1
                            @if ($item->item_status ==1)
                                    usable
                            @else
                                    unusable
                            @endif
                                    " data-item-id="{{$item->id}}"  data-item-sort="{{$item->sort}}" >
                                <div class="item_type">
                                    滑播
                                </div>
                                <div id="item_edit_content">
                                    <div class="index_block moduleC">
                                        <div nctype="item_content" class="content" id="sortable_moduleC">
                                            <div nctype="item_image" class="item">
                                                @if ($item->carousels &&!$item->carousels->isEmpty())
                                                    <img nctype="image" src="{{config('filesystems.disks.oss.url').'/'.$item->carousels[0]['carousel_img']}}" alt="">
                                                @else
                                                    <img nctype="image" src="{{asset('images/defaultimg.jpg')}}" alt="">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="handle">
                                    <a nctype="btn_move_up" href="javascript:;"><i class="icon-arrow-up"></i>上移</a>
                                    <a nctype="btn_move_down" href="javascript:;"><i class="icon-arrow-down"></i>下移</a>
                                    @if ($item->item_status ==1)
                                        <a nctype="btn_usable" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-off"></i>禁用</a>
                                    @else
                                        <a nctype="btn_usable" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-off"></i>启用</a>
                                    @endif
                                    <a nctype="btn_edit_item" data-item-id="{{$item->id}}" data-item-url="{{admin_base_path('module-c').'/'.$item->id.'/edit?module=1&specialId='.$specialId}}" href="javascript:;">
                                        <i class="icon-edit"></i>编辑
                                    </a>
                                    <a nctype="btn_del_item" data-item-id="{{$item->id}}" href="javascript:;">
                                        <i class="icon-trash"></i>删除
                                    </a>
                                </div>
                            </div>
                            @break

                            @case('moduleE')
                                <div nctype="special_item" class="special-item goods
                                @if ($item->item_status ==1)
                                        usable
                                @else
                                        unusable
                                @endif
                                        " data-item-id="{{$item->id}}"  data-item-sort="{{$item->sort}}" >
                                    <div class="item_type">
                                        商品横向布局版块
                                    </div>
                                    <div id="item_edit_content">
                                        <!--v3-v12-->
                                        <div class="index_block goods-list">
                                            <div class="title">
                                                <span></span>
                                            </div>
                                            <div nctype="item_content" class="content" id="sortable">
                                                @foreach($item['goodsList'] as $goods)
                                                    <div nctype="item_image_x" class="item_x">
                                                        <div class="goods-pic_x">
                                                            @if ($goods && $goods->primary_pic_url)
                                                                <img nctype="image" src="{{config('filesystems.disks.oss.url').'/'.$goods->primary_pic_url}}" alt="">
                                                            @else
                                                                <img nctype="goods_image" src="{{asset('images/defaultimg.jpg')}}" alt="">
                                                            @endif

                                                        </div>
                                                        <div class="goods-name" nctype="goods_name">
                                                            {{$goods->goods_name}}
                                                        </div>
                                                        <div class="goods-price" nctype="goods_price">
                                                            ￥{{$goods->retail_price}}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="handle">
                                        <a nctype="btn_move_up" href="javascript:;"><i class="icon-arrow-up"></i>上移</a>
                                        <a nctype="btn_move_down" href="javascript:;"><i class="icon-arrow-down"></i>下移</a>
                                        @if ($item->item_status ==1)
                                            <a nctype="btn_usable" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-off"></i>禁用</a>
                                        @else
                                            <a nctype="btn_usable" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-off"></i>启用</a>
                                        @endif
                                        <a nctype="btn_edit_item"  data-item-url="{{admin_base_path('module-e').'/'.$item->id.'/edit?module=1&specialId='.$specialId}}" data-item-id="{{$item->id}}"  href="javascript:;"><i class="icon-edit"></i>编辑</a>
                                        <a nctype="btn_del_item" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-trash"></i>删除</a>
                                    </div>
                                </div>
                                @break

                            @case('moduleF')
                            <div nctype="special_item" class="special-item goods
                                @if ($item->item_status ==1)
                                    usable
                                @else
                                    unusable
                                @endif
                                    " data-item-id="{{$item->id}}"  data-item-sort="{{$item->sort}}" >
                                <div class="item_type">
                                    促销商品版块
                                </div>
                                <div id="item_edit_content">
                                    <!--v3-v12-->
                                    <div class="index_block goods-list">
                                        <div class="title">
                                            <span></span>
                                        </div>
                                        <div nctype="item_content" class="content" id="sortable">
                                            @foreach($item['goodsList'] as $goods)
                                                <div nctype="item_image" class="item">
                                                    <div class="goods-pic">
                                                        @if ($goods && $goods->primary_pic_url)
                                                            <img nctype="image" src="{{config('filesystems.disks.oss.url').'/'.$goods->primary_pic_url}}" alt="">
                                                        @else
                                                            <img nctype="goods_image" src="{{asset('images/defaultimg.jpg')}}" alt="">
                                                        @endif

                                                    </div>
                                                    <div class="goods-name" nctype="goods_name">
                                                        {{$goods->goods_name}}
                                                    </div>
                                                    <div class="goods-price" nctype="goods_price">
                                                        ￥{{$goods->retail_price}}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="handle">
                                    <a nctype="btn_move_up" href="javascript:;"><i class="icon-arrow-up"></i>上移</a>
                                    <a nctype="btn_move_down" href="javascript:;"><i class="icon-arrow-down"></i>下移</a>
                                    @if ($item->item_status ==1)
                                        <a nctype="btn_usable" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-off"></i>禁用</a>
                                    @else
                                        <a nctype="btn_usable" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-off"></i>启用</a>
                                    @endif
                                    <a nctype="btn_edit_item"  data-item-url="{{admin_base_path('module-f').'/'.$item->id.'/edit?module=1&specialId='.$specialId}}" data-item-id="{{$item->id}}"  href="javascript:;"><i class="icon-edit"></i>编辑</a>
                                    <a nctype="btn_del_item" data-item-id="{{$item->id}}" href="javascript:;"><i class="icon-trash"></i>删除</a>
                                </div>
                            </div>
                            @break

                            @default

                        @endswitch
                    @endforeach
                </div>
            </div>
            <div class="module-list">
                <div class="module_adv_list">
                    <span>轮播图</span><a nctype="btn_add_item" class="add" href="javascript:;" data-module-type="adv" data-module-specialId="{{$specialId}}" >添加</a>
                </div>
                <div class="module_home1">
                    <span>滑块版块布局C</span><a nctype="btn_add_item" class="add" href="javascript:;" data-module-type="moduleC" data-module-specialId="{{$specialId}}">添加</a>
                </div>

                {{--<div class="module_home2">--}}
                    {{--<span>滑块版块布局C</span><a nctype="btn_add_item" class="add" href="javascript:;" data-module-type="moduleX" data-module-specialId="{{$specialId}}" >添加</a>--}}
                {{--</div>--}}
                {{--<div class="module_home3">--}}
                    {{--<span>模型版块布局D</span><a nctype="btn_add_item" class="add" href="javascript:;" data-module-type="home3" data-module-specialId="{{$specialId}}" >添加</a>--}}
                {{--</div>--}}
                <div class="module_goods">
                    <span>商品版块[布局B]</span><a nctype="btn_add_item" class="add" href="javascript:;" data-module-type="moduleB" data-module-specialId="{{$specialId}}" >添加</a>
                </div>
                <div class="module_home1">
                    <span>商品横向布局C</span><a nctype="btn_add_item" class="add" href="javascript:;" data-module-type="moduleE" data-module-specialId="{{$specialId}}" >添加</a>
                </div>
                <div class="module_goods">
                    <span>促销商品版块[布局F]</span><a nctype="btn_add_item" class="add" href="javascript:;" data-module-type="moduleF" data-module-specialId="{{$specialId}}" >添加</a>
                </div>
            </div>
        </div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
    var special_id = 0;
    var url_item_add = "{{route('special_item_add')}}";
    var url_item_del = "{{route('special_item_del')}}";
    var url_item_handle = "{{route('special_item_handle')}}";
    var url_item_sort = "{{route('update_item_sort')}}";

    $(document).ready(function () {
        //添加模块
        $('[nctype="btn_add_item"]').on('click', function () {
            var module_type = $(this).attr('data-module-type');
            special_id = $(this).attr('data-module-specialId');
            // 轮播只允许添加一个
            if(module_type =='adv' && $('.adv_list').length>0){
                showError('轮播只能添加一个');
                return ;
            }
            var data = {
                special_id: special_id,
                item_type: module_type
            };
            $.post(url_item_add, data, function (data) {
                if (data.code == '200') {
                    location.reload();
                } else {
                    showError(data.message);
                }
            }, "json");
        });

        //删除模块
        $('#item_list').on('click', '[nctype="btn_del_item"]', function () {
            if (!confirm('确认删除？')) {
                return false;
            }
            var $this = $(this);
            var item_id = $this.attr('data-item-id');
            $.post(url_item_del, {item_id: item_id, special_id: special_id}, function (data) {
                if (data.code == '200') {
                    $this.parents('.special-item').remove();
                } else {
                    showError(data.message);
                }
            }, "json");
        });

        //编辑模块
        $('#item_list').on('click', '[nctype="btn_edit_item"]', function () {
            var item_url = $(this).attr('data-item-url');
            top.location.href=item_url;
        });

        var item_id_string = '';
        //上移
        $('#item_list').on('click', '[nctype="btn_move_up"]', function () {
            var $current = $(this).parents('[nctype="special_item"]');
            $prev = $current.prev('[nctype="special_item"]');
            var pre_id = $prev.attr('data-item-id');
            var current_id = $current.attr('data-item-id');
            $pre_sort = $prev.attr('data-item-sort');
            $current_sort = $current.attr('data-item-sort');
            $current.attr('data-item-sort',$pre_sort);
            $prev.attr('data-item-sort',$current_sort);
            item_id_string = pre_id+':'+ $current_sort +','+ current_id+':'+ $pre_sort
            if ($prev.length > 0) {
                $prev.before($current);
                update_item_sort(item_id_string);
            } else {
                showError('已经是第一个了');
            }
        });

        //下移
        $('#item_list').on('click', '[nctype="btn_move_down"]', function () {
            var $current = $(this).parents('[nctype="special_item"]');
            $next = $current.next('[nctype="special_item"]');
            var next_id = $next.attr('data-item-id');
            var current_id = $current.attr('data-item-id');
            $next_sort = $next.attr('data-item-sort');
            $current_sort = $current.attr('data-item-sort');
            $current.attr('data-item-sort',$next_sort);
            $next.attr('data-item-sort',$current_sort);
            item_id_string = next_id+':'+ $current_sort +','+ current_id+':'+ $next_sort
            if ($next.length > 0) {
                $next.after($current);
                update_item_sort(item_id_string);
            } else {
                showError('已经是最后一个了');
            }
        });

        var update_item_sort = function (item_id_string) {
            $.post(url_item_sort, {
                special_id: special_id,
                item_id_string: item_id_string
            }, function (data) {
                if (data.code != '200') {
                    showError(data.message);
                }
            }, 'json');
        };

        //启用/禁用控制
        $('#item_list').on('click', '[nctype="btn_usable"]', function () {
            var $current = $(this).parents('[nctype="special_item"]');
            var item_id = $current.attr('data-item-id');
            var usable = '';
            if ($current.hasClass('usable')) {
                $current.removeClass('usable');
                $current.addClass('unusable');
                usable = '0';
                $(this).html('<i class="icon-off"></i>启用');
            } else {
                $current.removeClass('unusable');
                $current.addClass('usable');
                usable = '1';
                $(this).html('<i class="icon-off"></i>禁用');
            }
            $.post(url_item_handle, {
                item_id: item_id,
                usable: usable,
                special_id: special_id
            }, function (data) {
                if (data.code != '200') {
                    showError(data.message);
                }
            }, 'json');
        });

    });
</script>
@endsection
