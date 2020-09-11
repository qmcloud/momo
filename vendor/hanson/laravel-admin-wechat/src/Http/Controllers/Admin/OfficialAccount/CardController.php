<?php


namespace Hanson\LaravelAdminWechat\Http\Controllers\Admin\OfficialAccount;

use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Hanson\LaravelAdminWechat\Actions\ImportCards;
use Hanson\LaravelAdminWechat\Http\Controllers\Admin\BaseController;
use Hanson\LaravelAdminWechat\Models\WechatCard;

class CardController extends BaseController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '卡券';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WechatCard());

        $grid->column('id', __('Id'));
        $grid->column('logo_url', '卡券的商户logo')->image('', 64, 64);
        $grid->column('card_type_readable', '类型');
        $grid->column('card_id', '微信卡券id')->copyable();
        $grid->column('code_type_readable', '码型');
        $grid->column('brand_name', '商户名字');
        $grid->column('title', '卡券名');
        $grid->column('sku', '商品信息')->display(function () {
            $sku = [];
            if(isset($this->sku['total_quantity'])) $sku[] = '卡券全部库存的数量: '.$this->sku['total_quantity'];
            if(isset($this->sku['quantity'])) $sku[] = '卡券现有库存的数量: '.$this->sku['quantity'];
            if ($sku) {
                return implode('<br>', $sku);
            }
        });
        $grid->column('date_info', '使用日期')->display(function () {
            $date = [];
            $date[] = '时间的类型: '.WechatCard::DATE_INFO_TYPE_MAP[$this->date_info['type']];
            if(isset($this->date_info['begin_timestamp'])) $date[] = '起用时间: '.Carbon::parse($this->date_info['begin_timestamp'])->toDateTimeString();
            if(isset($this->date_info['end_timestamp'])) $date[] = '结束时间: '.Carbon::parse($this->date_info['end_timestamp'])->toDateTimeString();
            if(isset($this->date_info['fixed_term'])) $date[] = '领取后多少天内有效: '.$this->date_info['fixed_term'];
            if(isset($this->date_info['fixed_begin_term'])) $date[] = '领取后多少天开始生效: '.$this->date_info['fixed_begin_term'];
            if ($date) {
                return implode('<br>', $date);
            }
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new ImportCards());
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WechatCard::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('card_type', __('Card type'));
        $show->field('card_id', __('Card id'));
        $show->field('logo_url', __('Logo url'));
        $show->field('code_type', __('Code type'));
        $show->field('brand_name', __('Brand name'));
        $show->field('title', __('Title'));
        $show->field('color', __('Color'));
        $show->field('notice', __('Notice'));
        $show->field('description', __('Description'));
        $show->field('sku', __('Sku'));
        $show->field('date_info', __('Date info'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WechatCard());

        $form->text('card_type', __('Card type'));
        $form->text('card_id', __('Card id'));
        $form->text('logo_url', __('Logo url'));
        $form->text('code_type', __('Code type'));
        $form->text('brand_name', __('Brand name'));
        $form->text('title', __('Title'));
        $form->color('color', __('Color'));
        $form->text('notice', __('Notice'));
        $form->text('description', __('Description'));
        $form->text('sku', __('Sku'));
        $form->text('date_info', __('Date info'));

        return $form;
    }
}

