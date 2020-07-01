<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */
// sqc添加
use App\Admin\Extensions\Column\ExpandRow;
use Encore\Admin\Grid\Column;
use Encore\Admin\Form;
use App\Admin\Extensions\Form\WangEditor;
use App\Admin\Extensions\Form\AddSpecification;
use Encore\Admin\Facades\Admin;

Column::extend('expand', ExpandRow::class);
Form::forget(['map', 'editor']);
Form::extend('editor', WangEditor::class);
Form::extend('addSpecification', AddSpecification::class);

Admin::js('/vendor/echarts/echarts.js');
Admin::js('/vendor/echarts/macarons.js');
//Admin::css('/vendor/admin/css/skin_0.css');