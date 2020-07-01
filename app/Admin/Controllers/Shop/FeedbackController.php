<?php

namespace App\Admin\Controllers\Shop;

use App\Models\ShopFeedback;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class FeedbackController extends Controller
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

            $content->header('用户反馈');
            $content->description('用户反馈管理');

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

            $content->header('用户反馈');
            $content->description('用户反馈状态管理');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(ShopFeedback::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->disableRowSelector();
            $grid->disableCreateButton();
            $grid->disableActions();

            $grid->id('序号')->sortable();

            $grid->user_name('用户昵称');
            $grid->user_contact('反馈用户联系方式');
            $grid->column('msg_type','反馈类型')->display(function($msg_type){
                return ShopFeedback::$Option[$msg_type];
            });
            $grid->msg_title('反馈标题');
            $grid->msg_content('反馈内容');
            $grid->msg_status('反馈状态')
                ->select(ShopFeedback::getStatusDispayMap());
            $grid->message_img('反馈附加图片')->image('', 80, 80);
            $grid->created_at('创建时间');
            $grid->updated_at('更新时间');
            $grid->filter(function ($filter) {
                $filter->equal('user_contact', '反馈人手机');
                $filter->equal('msg_type', '反馈类型')->select(ShopFeedback::$Option);
                $filter->equal('msg_status', '反馈状态')->select(ShopFeedback::getStatusDispayMap());
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
        return Admin::form(ShopFeedback::class, function (Form $form) {
            $form->display('id', '序号');
            $form->select('msg_status', '反馈状态')
                ->rules('required')
                ->options(ShopFeedback::getStatusDispayMap());
        });
    }
}
