<?php

namespace App\Admin\Controllers;

use App\Models\Attestation;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use App\Admin\Extensions\Tools\ReleasePost;
use App\Admin\Extensions\Tools\RestorePost;
use App\Admin\Extensions\Tools\ShowSelected;

class AttestationController extends AdminController
{

    protected $title = '身份验证';

    protected function detail($id)
    {
        $show = new Show(Attestation::findOrFail($id));

        $show->panel()
            ->style('danger')
            ->title('post基本信息')
            ->tools(function ($tools) {
                $tools->disableEdit();
            });;


        $show->created_at();
        $show->updated_at();


        return $show;
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->title('编辑')
            ->row($this->form()->edit($id))
            ->row(Admin::grid(Attestation::class, function (Grid $grid) use ($id) {

                $grid->id();
                $grid->uid();
                $grid->real_name();
                $grid->mobile();
                $grid->cer_no();
                $grid->status()->display(function ($status) {
                    switch ($status){
                        case 0:$status= '审核中';
                            break;
                        case 1:$status= '审核完成';
                            break;
                        case 2:$status= '驳回';
                            break;
                        default :$status;
                    }
                    return $status;
                });
                $grid->created_at();
                $grid->updated_at();

            }));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Attestation);

        //$grid->quickSearch();

        $grid->column('uid', 'UID')->sortable();

        $grid->column('real_name',"真实姓名");

        $grid->column('mobile',"手机号码");

        $grid->column('cer_no',"身份证号");

        // 第六列显示released字段，通过display($callback)方法来格式化显示输出
        $grid->column('status',"审核状态")->display(function ($status) {
            switch ($status){
                case 0:$status= '审核中';
                break;
                case 1:$status= '审核完成';
                    break;
                case 2:$status= '驳回';
                    break;
                default :$status;
            }
            return $status;
        });

        // 下面为三个时间字段的列显示
        $grid->column('created_at', '提交时间');

        $grid->column('updated_at', '修改时间');


        // filter($callback)方法用来设置表格的简单搜索框
        $grid->filter(function ($filter) {
            $filter->like('uid','UID');
            $filter->like('real_name',"真实姓名");
            $filter->like('mobile',"手机");
            $filter->like('cer_no',"身份证");
            $filter->equal('status',"审核状态")->select([0=> '审核中',1=>'审核完成',2=>'驳回']);
            // 设置created_at字段的范围查询
            $filter->between('created_at', '创建时间')->datetime();
        });

        return $grid;
    }

    protected function _form()
    {
        $form = new Form(new Attestation);

        $form->row(function ($row) {
            $row->width(2)->display('id', 'ID');
        });

        $form->row(function ($row) {
            $row->width(4)->text('title', '文章标题')->rules('min:3|max:5')->help('标题标题标题标题标题标题标题');
            $row->width(4)->select('author_id', '选择作者')->options(function ($id) {
                $user = User::find($id);

                if ($user) {
                    return [$user->id => $user->name];
                }
            })->ajax('/demo/api/users');
            $row->width(2)->number('rate', '评分');
            $row->width(2)->switch('released', '发布?');
        });

        $form->row(function ($row) {
            $row->width(5)->listbox('tags', '选择标签')->options(Tag::all()->pluck('name', 'id'))->settings(['selectorMinimalHeight' => 300]);
            $row->width(7)->textarea('content', '文章内容')->rows(19)->help('标题标题标题标题标题标题标题');
        });

        $form->row(function ($row) {
            $row->width(6)->datetimeRange('created_at', 'updated_at', '创建时间');
        });

        return $form;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Attestation());

        $form->text('uid', 'UID')->help('用户的uid');

        $form->text('real_name',"真实姓名");

        $form->text('mobile',"手机号码");

        $form->text('cer_no',"身份证号");

        $form->select('status',"审核状态")->options([0=>'审核中',1=>'审核完成',2=>'驳回']);

        $form->datetime('created_at', '提交时间');

        $form->datetime('updated_at', '修改时间');

        /*$form->tools(function (Form\Tools $tools) {

            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();

            $tools->append('<a class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>&nbsp;&nbsp;delete</a>');
        });*/

        return $form;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function users(Request $request)
    {
        $q = $request->get('q');

        return User::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }

    public function release(Request $request)
    {
        foreach (Attestation::find($request->get('ids')) as $post) {
            $post->released = $request->get('action');
            $post->save();
        }
    }

    public function restore(Request $request)
    {
        return Attestation::onlyTrashed()->find($request->get('ids'))->each->restore();
    }
}
