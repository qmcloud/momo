
<div class="form-group  ">
    @include('admin::form.error')
    <label for="promotion_desc" class="col-sm-2 control-label {{$viewClass['label']}}"><h4 class="pull-right">{{ $label }}</h4></label>

    <div class="col-sm-8">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $label }}</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                        <i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <!-- 规格checkbox -->

                @foreach($template as $item)
                    @if (!$item->spec_items->isEmpty())
                    <div class="row spec_box" data-itemIndex="{{$loop->index}}" data-specName="{{$item->name}}" data-specId="{{$item->id}}">
                        <div class="col-xs-2">
                            {{$item->name}}
                        </div>
                        <div class="col-xs-10">
                            <div class="form-group">
                                @foreach($item->spec_items as $spec_item)
                                    <label>
                                        <input type="checkbox" class="spec_checkbox"
                                               @if (in_array($spec_item->id, $checked_spec_item_ids))
                                                       checked="checked"
                                               @endif
                                               data-itemName="{{$spec_item->item}}" data-itemId="{{$spec_item->id}}" name="goods_specification_ids[]" value="{{$spec_item->id}}"> {{$spec_item->item}}
                                    </label>
                                    &nbsp;&nbsp;
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach

            </div>
            <!-- /.box-body -->
            <div class="box-footer">

                <div class="box">
                    <div class="box-body no-padding">
                        <table class="table table-condensed">
                            <tr>
                                <th>规格组合</th>
                                <th>单位价格，单价</th>
                                <th>库存</th>
                                <th>商家货号</th>
                            </tr>
                            <tbody id="spec_list_show">
                                @if ($products)
                                    @foreach($products as $index => $product)
                                        <tr id="{{$product['goods_spec_item_ids']}}" data-has="1">
                                            <td>{{$product['goods_spec_item_names']}}
                                                <input type="hidden" class="form-control" name="products[{{$index}}][goods_spec_item_ids]" value="{{$product['goods_spec_item_ids']}}" >
                                                <input type="hidden" class="form-control id" name="products[{{$index}}][id]" value="{{$product['id']}}"  >
                                                <input type="hidden" class="form-control _remove_" name="products[{{$index}}][_remove_]" value="0"  >
                                                <input type="hidden" class="form-control" name="products[{{$index}}][goods_spec_item_names]" value="{{$product['goods_spec_item_names']}}" >
                                                <input type="hidden" class="form-control" name="products[{{$index}}][goods_specification_names]"value="{{$product['goods_specification_names']}}" >
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="hidden" class="form-control" name="products[{{$index}}][goods_specification_ids]" value="{{$product['goods_specification_ids']}}" >
                                                    <span class="input-group-addon">$</span>
                                                    <input type="text" class="form-control" name="products[{{$index}}][retail_price]" value="{{$product['retail_price']}}">
                                                    <span class="input-group-addon">元</span></div></td><td><div class="input-group">
                                                    <input type="text" class="form-control"  name="products[{{$index}}][goods_number]" value="{{$product['goods_number']}}" ></div></td><td>
                                                <div class="input-group"><input type="text" class="form-control"  name="products[{{$index}}][goods_sn]" value="{{$product['goods_sn']}}" ></div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <!-- /.box-footer-->
        </div>
    </div>
</div>


<script>

</script>




{{--<tr>--}}
    {{--<td>--}}
        {{--红色 * 16G--}}
        {{--<input type="hidden" class="form-control" name="goods_specification_ids" value="">--}}
        {{--<input type="hidden" class="form-control" name="goods_spec_item_ids" value="">--}}
        {{--<input type="hidden" class="form-control" name="goods_spec_item_names" value="">--}}
        {{--<input type="hidden" class="form-control" name="goods_specification_names" value="">--}}
    {{--</td>--}}
    {{--<td>--}}
        {{--<div class="input-group">--}}
            {{--<input type="hidden" class="form-control" name="goods_specification_ids">--}}
            {{--<span class="input-group-addon">$</span>--}}
            {{--<input type="text" class="form-control" name="retail_price">--}}
            {{--<span class="input-group-addon">元</span>--}}
        {{--</div>--}}
    {{--</td>--}}
    {{--<td>--}}
        {{--<div class="input-group">--}}
            {{--<input type="text" class="form-control"  name="goods_number">--}}
        {{--</div>--}}
    {{--</td>--}}
    {{--<td>--}}
        {{--<div class="input-group">--}}
            {{--<input type="text" class="form-control"  name="goods_sn">--}}
        {{--</div>--}}
    {{--</td>--}}
{{--</tr>--}}