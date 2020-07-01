<?php

namespace App\Admin\Extensions\Form;

use Encore\Admin\Admin;
use Encore\Admin\Form;
use Encore\Admin\Form\Field;
use Illuminate\Support\Facades\Validator;

class AddSpecification extends Field
{
    protected $view = 'admin.form.product';

    /**
     * Form builder.
     *
     * @var \Closure
     */
    protected $builder = null;

    protected static $css = [
    ];

    protected static $js = [
        '/vendor/laravel-admin/add_specification.js',
    ];


    /**
     * Create a new HasMany field instance.
     *
     * @param $relationName
     * @param array $arguments
     */
    public function __construct($relationName, $arguments = [])
    {
        $this->relationName = $relationName;

        $this->column = $relationName;

        if (count($arguments) == 1) {
            $this->label = $this->formatLabel();
            $this->builder = $arguments[0];
        }

        if (count($arguments) == 2) {
            list($this->label, $this->builder) = $arguments;
        }
    }

    /**
     * Get validator for this field.
     *
     * @param array $input
     *
     * @return bool|Validator
     */
    public function getValidator(array $input)
    {
        return Validator::make([], [], [], []);
    }


    /**
     * Prepare input data for insert or update.
     *
     * @param array $input
     *
     * @return array
     */
    public function prepare($input)
    {
        return $input;
    }



    /**
     * Get the HasMany relation key name.
     *
     * @return string
     */
    protected function getKeyName()
    {
        if (is_null($this->form)) {
            return;
        }

        return $this->form->model()->{$this->relationName}()->getRelated()->getKeyName();
    }

    /**
     * Setup script for this field in different view mode.
     *
     * @param string $script
     *
     * @return void
     */
    protected function setupScript()
    {
        $this->script = <<<EOT
var goods_specification_arr = [];
$(function(){
    $('.spec_checkbox').change(function(){
        var goods_specification_vars = [];
        $('.spec_box').find('input[class="spec_checkbox"]:checked').each(function(index, domEle){
            var spec_box = $(this).parents('.spec_box');
            var specId = spec_box.attr('data-specId');
            var specName = spec_box.attr('data-specName');
            var itemId = $(domEle).attr('data-itemId');
            var itemName = $(domEle).attr('data-itemName');
            goods_specification_arr[itemId] = {itemId:itemId,itemName:itemName,specId:specId,specName:specName};
            if(!goods_specification_vars[specId]){
                goods_specification_vars[specId] = [];
            }
            goods_specification_vars[specId].push(itemId);
        });
        $("#spec_list_show").find('tr[data-has="1"]').hide().find('._remove_').val('1');
        $("#spec_list_show").find('tr[data-has="0"]').remove();
        spec_info_data(goods_specification_vars,goods_specification_arr);
    });
})

EOT;
    }

    /**
     * Build a spec form.
     *
     * @param string   $column
     * @param \Closure $builder
     * @param null     $key
     *
     * @return
     */
    protected function buildSpecForm($column, \Closure $builder)
    {
        $form = call_user_func($builder);
        return $form;
    }

    public function render()
    {
        //var_dump($this->form->model());
        //var_dump($this->variables());
        $template = $this->buildSpecForm($this->column, $this->builder);
        $this->setupScript();
        $checked_spec_item_ids = [];
        if($this->value){
            $tmp = array_pluck($this->value, 'goods_spec_item_ids');
            foreach($tmp as $tmpV){
                if($tmpV){
                    $checked_spec_item_ids = array_merge($checked_spec_item_ids,explode('_',$tmpV));
                }
            }
        }
        $checked_spec_item_ids = array_unique($checked_spec_item_ids);
        return parent::render()->with([
            'template'     => $template,
            'products' =>$this->value,
            'checked_spec_item_ids' =>$checked_spec_item_ids
        ]);
    }
}



// 删除
/*return [
    '1'=>[
        'id' =>  '2',
        'goods_specification_ids' =>  '0',
        'goods_sn' =>  '111',
        'goods_number' =>  '1111',
        'retail_price' =>  '1111',
        'goods_specification_names' =>  '222',
        'goods_spec_item_ids' =>  '0',
        'goods_spec_item_names' =>  '0',
        '_remove_' =>  '1'
    ]
];*/

//// prepare调试代码
// 修改
//return [
//    '1'=>[
//        'id' =>  '2',
//        'goods_specification_ids' =>  '0',
//        'goods_sn' =>  '111',
//        'goods_number' =>  '1111',
//        'retail_price' =>  '1111',
//        'goods_specification_names' =>  '222',
//        'goods_spec_item_ids' =>  '0',
//        'goods_spec_item_names' =>  '0',
//        '_remove_' =>  '0'
//    ]
//];
//// 新增
//return [
//    'new_1'=>[
//        'goods_specification_ids' =>  '0',
//        'goods_sn' =>  '1110',
//        'goods_number' =>  '1',
//        'retail_price' =>  '0',
//        'goods_specification_names' =>  '0',
//        'goods_spec_item_ids' =>  '0',
//        'goods_spec_item_names' =>  '0',
//        '_remove_' =>  '0'
//    ]
//];
//$form = $this->buildNestedForm($this->column, $this->builder);
//var_dump($this->original);
//var_dump($this->column);
//var_dump($input);die;
//return $form->setOriginal($this->original, $this->getKeyName())->prepare($input);
