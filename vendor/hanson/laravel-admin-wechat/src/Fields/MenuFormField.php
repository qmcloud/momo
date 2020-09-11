<?php


namespace Hanson\LaravelAdminWechat\Fields;

use Encore\Admin\Form\Field;

class MenuFormField extends Field
{
    protected $view = 'wechat::menu';

    protected static $css = [
        'vendor/laravel-admin-ext/wechat/css/menu.min.css'
    ];

    protected static $js = [
        'https://cdn.jsdelivr.net/npm/vue',
        'vendor/laravel-admin-ext/wechat/js/menu.min.js'
    ];

    public function render()
    {
        if(empty($this->default)) {
            $this->value = [
                "button" => [],
            ];
        } else {
            $this->value = $this->default;
        }

        if (is_array($this->value)) {
            $this->value = json_encode($this->value, JSON_UNESCAPED_UNICODE);
        }

        return parent::render();
    }
}
