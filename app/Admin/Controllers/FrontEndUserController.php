<?php

namespace App\Admin\Controllers;

use App\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class FrontEndUserController extends Controller
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

            $content->header('前端用户列表');
            $content->description('前端用户管理');

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

            $content->header('header');
            $content->description('description');

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

            $content->header('header');
            $content->description('description');

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

            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableRowSelector();

            $grid->id('序号')->sortable();
            $grid->name('姓名');
            $grid->mobile('手机号');
            $grid->nickname('昵称');
            $grid->login_time('登录时间');
            $grid->login_ip('登录IP');
            $grid->role('用户类型')->select(User::getRoleDisplayMap());
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');

            $grid->filter(function ($filter) {
                $filter->like('name', '姓名');
                $filter->equal('mobile', '手机号');
                $filter->equal('role', '用户类型')->radio(User::getRoleDisplayMap());
            });
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

            $form->display('id', '序号');
            $form->select('role', '用户类型')->options(User::getRoleDisplayMap());
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
        });
    }
}
