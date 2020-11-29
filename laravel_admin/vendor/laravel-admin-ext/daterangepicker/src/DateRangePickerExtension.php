<?php

namespace Encore\DateRangePicker;

use Encore\Admin\Extension;

class DateRangePickerExtension extends Extension
{
    public $name = 'daterangepicker';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';
}