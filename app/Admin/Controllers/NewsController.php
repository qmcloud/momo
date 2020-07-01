<?php
namespace App\Admin\Controllers;

use App\Models\News;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class NewsController extends Controller
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

            $content->header('新闻通知列表');
            $content->description('新闻通知列表');
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

            $content->header('新闻信息修改');
            $content->description('新闻信息修改');

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

            $content->header('新增新闻');
            $content->description('新增新闻通知');

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
        return Admin::grid(News::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->news_title('新闻标题');
            $grid->news_lable('新闻标签');
            $grid->news_author('新闻发布者');
            $grid->news_content('新闻内容');
            $grid->created_at();
            $grid->updated_at();

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(News::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->text('news_title', '新闻标题')
                ->rules('required');
            $form->text('news_lable', '新闻标签')
                ->rules('required');
            $form->text('news_author', '编辑作者')
                ->rules('required');
            $form->text('news_content', '新闻内容')
                ->rules('required');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
