<?php

namespace App\Admin\Controllers\Module;

use App\Http\Controllers\Controller;
use Illuminate\Support\MessageBag;
use App\Models\SpecialItem;
use Illuminate\Http\Request;
use App\Models\Carousel;
use Illuminate\Support\Facades\Validator;
use Response;
use Illuminate\Support\Facades\DB;

class ModuleIndexController extends Controller
{

    /**
     * 添加
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function special_item_add(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'special_id' => 'required',
                'item_type' => 'required',
            ],
            [
                'special_id.required' => '专题id参数缺失',
                'item_type.required' => '模板类型不存在',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $model = new SpecialItem();
        $model->special_id = $request->special_id;
        $model->item_type = $request->item_type;
        if($model->item_type == 'adv'){
            $model->sort = 255;
        }else{
            $model->sort = 1;
        }
        if($model->save()){
            return $this->success(['special_item_id'=>$model->id]);
        }
        return $this->error('添加失败');
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function special_item_del(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'special_id' => 'required',
                'item_id' => 'required',
            ],
            [
                'special_id.required' => '专题id参数缺失',
                'item_id.required' => '模板类型不存在',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $re = SpecialItem::destroy($request->item_id);
        if($re){
            return $this->success(['msg'=>'删除成功']);
        }
        return $this->error('删除失败');

    }

    /**
     * 操作 启用和禁用
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function special_item_handle(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'special_id' => 'required',
                'item_id' => 'required',
                'usable' => 'required',
            ],
            [
                'special_id.required' => '专题id参数缺失',
                'item_id.required' => '模板类型不存在',
                'usable.required' => '状态值不存在',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        $model = SpecialItem::find($request->item_id);
        if(empty($model)){
            return $this->error('没有找到相关专题');
        }
        $model->item_status = $request->usable;
        if($model->save()){
            return $this->success(['msg'=>'操作成功']);
        }
        return $this->error('修改失败');
    }

    /**
     * 更新排序
     */
    public function update_item_sort(Request $request)
    {
        // 参数校验
        $validator = Validator::make($request->all(),
            [
                'special_id' => 'required',
                'item_id_string' => 'required',
            ],
            [
                'special_id.required' => '专题id参数缺失',
                'item_id_string.required' => '参数缺失',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors(), 403);
        }
        DB::beginTransaction();
        try {
            $item_id_data = explode(',',$request->item_id_string);
            foreach ($item_id_data as $item_id_str){
                $tmp_data = explode(':',$item_id_str);
                $re = SpecialItem::where('id', $tmp_data[0])->update(['sort' => $tmp_data[1]]);
                if(!$re){
                    throw new \Exception('操作失败了');
                }
            }
            DB::commit();
            return $this->success(['msg'=>'操作成功']);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('操作失败');
        }
    }

    public function getView($id = 0)
    {
        return view('admin.module.moduleA'); //
    }

    /**
     * 正确返回
     */
    public function success($data){
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'code' => '200'
        ]);
    }

    /**
     * 错误返回
     */
    public function error($message,$code='401',$data = []){
        return response()->json([
            'status' => 'error',
            'data' => $data,
            'message'=>$message,
            'code' => $code
        ]);
    }
}
