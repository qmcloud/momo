<?php

namespace App\Admin\Forms\Settings;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Database extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '数据存储';

    public $option_name = 'settings_db';
    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        //dump($request->all());
        $this->BaseValidator($request->all())->validate();

        $res = \App\Models\Option::updateOrCreate(
            ['option_name' => $this->option_name], ['option_value' => json_encode($request->all(), JSON_UNESCAPED_UNICODE)]
        );

        if ($res) {
            admin_success('操作成功.');
        } else {
            admin_error('操作失败.');
        }

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {

        $this->select('dbtype', '存储类型')->options(['Qiniu'=>'七牛云存储','Local'=>'本地存储'])
            ->when('Qiniu', function (Form $form) {
                $form->text("qn_accessKey","accessKey")->help('AccessKey')->rules("required");
                $form->text("qn_secretKey","accessKey")->help('SecretKey')->rules("required");
                $form->select("qn_protocol","http协议")->options(["http"=>"http","https"=>"https"])->help('AccessKey')->rules("required");
                $form->text("bucket","bucket")->help('Bucket名字')->rules("required");
                $form->text("qn_name","文件名")->help('文件名')->rules("required");
                $form->text("notify_url","notify_url")->help('回调地址');

            })->rules('required');
        $this->text('path', '备份根路径')->rules('required')->help('路径必须以 / 结尾');
        $this->text('backup_size', '备份卷大小')->rules('required')->help('该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M');
        $this->radio('zip', '备份是否压缩')->options([1 => '是', 0 => '否'])->help('压缩备份文件需要PHP环境支持 gzopen, gzwrite函数');
        $this->radio('zip_level', '备份压缩级别')->options([1 => '最低', 2 => '一般', 3 => '最高'])->help('数据库备份文件的压缩级别，该配置在开启压缩时生效');
    }

    /**
     * The data of the form.
     *
     * @return array $data
     */
    public function data()
    {
        $res = \App\Models\Option::where(['option_name'=>$this->option_name])->get()->toArray();

        return empty($res)?[
            'path'        => '../data/',
            'backup_size' => '20971520',
            'zip'         => 1,
            'zip_level'   => 2,
        ]:json_decode($res[0]['option_value'],true);
    }

    /**
     * Get a validator for an incoming login request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function BaseValidator(array $data)
    {
        return Validator::make($data, [

        ]);
    }
}
