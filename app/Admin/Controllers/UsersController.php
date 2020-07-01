<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Table;
use App\User;    // 引入模型
use Encore\Admin\Widgets\Tab;
class UsersController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户管理');
            $content->description('用户列表');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('用户的编辑');
            $content->description('用户的编辑');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户添加');
            $content->description('用户添加');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(User::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->avatar('用户头像')->image('',50, 50);
            $grid->column('name','用户名')->editable();
            $grid->column('mobile','用户手机号')->label('primary');
            $grid->column('nickname','用户微信昵称');
            // 设置text、color、和存储值
            $states = [
                'on'  => ['value' => 2, 'text' => '配送员', 'color' => 'primary'],
                'off' => ['value' => 1, 'text' => '普通会员', 'color' => 'default'],
            ];
            
            $grid->role('用户状态')->switch($states);
            $grid->column('login_ip','最近登陆ip');
            $grid->column('login_time','最近登陆时间');
            
            
            // 搜索功能
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                // $filter->useModal();

                // 禁用id查询框
                $filter->disableIdFilter();

                // sql: ... WHERE `user.name` LIKE "%$name%";
                $filter->like('name', '用户名');

                // sql: ... WHERE `user.email` = $email;
                $filter->is('mobile', '用户手机号');


            });
            $grid->disableExport();// 禁用导出
            $grid->disableCreation();// 禁用新增
            $grid->disableBatchDeletion();// 禁用批量删除
        });
    }

     /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(User::class, function (Form $form) {
            $form->text('name', '用户名');
            $form->mobile('mobile','用户手机号');
            $states = [
                'on'  => ['value' => 2, 'text' => '配送员', 'color' => 'success'],
                'off' => ['value' => 1, 'text' => '普通会员', 'color' => 'default'],
            ];
            //$form->password('password', '用户密码')->help('修改用户密码');
            $form->switch('role', '用户状态')->states($states);
        });
    }

}
