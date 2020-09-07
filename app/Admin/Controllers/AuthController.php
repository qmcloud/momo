<?php

namespace App\Admin\Controllers;

use App\Models\Auth;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use App\Admin\Extensions\Tools\ReleasePost;
use App\Admin\Extensions\Tools\RestorePost;
use App\Admin\Extensions\Tools\ShowSelected;

class AuthController extends AdminController
{

    protected $title = '身份验证';

    protected function detail($id)
    {
        $show = new Show(Auth::findOrFail($id));

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
            ->title('header')
            ->description('description')
            ->row($this->form()->edit($id))
            ->row(Admin::grid(AuthComment::class, function (Grid $grid) use ($id) {

                $grid->setName('comments')
                    ->setTitle('Comments')
                    ->setRelation(Auth::find($id)->comments())
                    ->resource('/demo/post-comments');

                $grid->id();
                $grid->content();
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
        $grid = new Grid(new Auth());

        $grid->quickSearch();

        $grid->id('ID')->sortable();

        $grid->title()->ucfirst()->limit(30)->totalRow('统计');


        $states = [
            'on' => ['text' => 'YES'],
            'off' => ['text' => 'NO'],
        ];

        $grid->released()->switch($states);

        /*$grid->rate()->display(function ($rate) {
            $html = "<i class='fa fa-star' style='color:#ff8913'></i>";

            if ($rate < 1) {
                return '';
            }

            return join('&nbsp;', array_fill(0, min(5, $rate), $html));
        })->totalRow();*/

        $grid->column('float_bar')->floatBar();

//        $grid->column('Comments')->display(function () {
//            return $this->comments()->take(5)->get(['id', 'content', 'created_at'])->toArray();
//        })->table();

        $grid->created_at();

        $grid->rows(function (Grid\Row $row) {
            if ($row->id % 2) {
                $row->setAttributes(['style' => 'color:red;']);
            }
        });

        $grid->filter(function (Grid\Filter $filter) {

            $filter->expand();

            $filter->column(1/2, function ($filter) {
                $filter->like('title');

                $filter->group('rate', function ($group) {
                    $group->gt('大于');
                    $group->lt('小于');
                    $group->nlt('不小于');
                    $group->ngt('不大于');
                    $group->equal('等于');
                });

            });

            $filter->column(1/2, function ($filter) {
                $filter->equal('created_at')->datetime();
                $filter->between('updated_at')->datetime();
                $filter->where(function ($query) {

                    $input = $this->input;

                    $query->whereHas('tags', function ($query) use ($input) {
                        $query->where('name', $input);
                    });

                }, 'Has tag', 'tag');
            });

            $filter->scope('trashed')->onlyTrashed();
            $filter->scope('hot')->where('rate', '>', 3);
            $filter->scope('released')->where('released', 1);
            $filter->scope('new', 'Updated today')->whereDate('updated_at', date('Y-m-d'));
        });

        $grid->tools(function ($tools) {

            $tools->batch(function (Grid\Tools\BatchActions $batch) {

                $batch->add('Restore', new RestorePost());
                $batch->add('Release', new ReleasePost(1));
                $batch->add('Unrelease', new ReleasePost(0));
                $batch->add('Show selected', new ShowSelected());
            });

        });

        return $grid;
    }

    protected function _form()
    {
        $form = new Form(new Auth);

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
        $form = new Form(new Auth());

        $form->display('id', 'ID');

        $form->text('title')->default('hello');

        $form->select('author_id')->options(function ($id) {
            $user = User::find($id);

            if ($user) {
                return [$user->id => $user->name];
            }
        })->ajax('/demo/api/users');

        // @see https://github.com/laravel-admin-extensions/summernote
        $form->summernote('content');

        //$form->number('rate');
        $form->switch('released');

        $form->listbox('tags')->options(Tag::all()->pluck('name', 'id'))->settings(['selectorMinimalHeight' => 300]);

        $form->display('created_at', 'Created At');
        $form->display('updated_at', 'Updated At');

        $form->tools(function (Form\Tools $tools) {

//          $tools->disableList();
//          $tools->disableDelete();
//          $tools->disableView();

//          $tools->append('<a class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>&nbsp;&nbsp;delete</a>');
        });

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
        foreach (Auth::find($request->get('ids')) as $post) {
            $post->released = $request->get('action');
            $post->save();
        }
    }

    public function restore(Request $request)
    {
        return Auth::onlyTrashed()->find($request->get('ids'))->each->restore();
    }
}
