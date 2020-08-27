<?php

namespace Encore\DateRangePicker;

use Encore\Admin\Form\Field;

/**
 * Class DateRangePicker
 * @package Encore\DateRangePicker
 *
 * @see http://www.daterangepicker.com/
 */
class DateRangePicker extends Field
{
    /**
     * @var string
     */
    protected $view = 'laravel-admin-daterangepicker::daterangepicker';

    /**
     * @var array
     */
    protected static $css = [
        'vendor/laravel-admin-ext/daterangepicker/daterangepicker.css',
    ];

    /**
     * @var array
     */
    protected static $js = [
        'vendor/laravel-admin-ext/daterangepicker/daterangepicker.js',
    ];

    protected $format = 'YYYY-MM-DD';

    /**
     * @var bool
     */
    protected $multiple = false;

    /**
     * DateRangePicker constructor.
     * @param $column
     * @param array $arguments
     */
    public function __construct($column, $arguments = [])
    {
        if (is_string($column)) {
            return parent::__construct($column, $arguments);
        }

        if (is_array($column)) {
            $this->column = [];
            $this->column['start'] = $column[0];
            $this->column['end'] = $column[1];

            $this->label = $this->formatLabel($arguments);

            $this->id = $this->formatId($this->column);

            $this->multiple = true;
        }
    }

    /**
     * Predefine Date Ranges.
     *
     * @param array $ranges
     * @return $this
     */
    public function ranges($ranges = [])
    {
        return $this->options(compact('ranges'));
    }

    /**
     * Set date format.
     *
     * @param $format
     * @return $this
     */
    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        array_set($this->options, 'locale.format', $this->format);

        $global = DateRangePickerExtension::config('config', []);

        $options = json_encode(array_merge($global, $this->options));

        $locale = config('app.locale');

        $classSelector = join('_', $this->getElementClass());

        $this->script = <<<SCRIPT

moment.locale('$locale');

$('.{$classSelector}').daterangepicker($options);

SCRIPT;

        if ($this->multiple) {
            $this->script .= <<<SCRIPT
$('.{$classSelector}').on('apply.daterangepicker', function(ev, picker) {
  var range = $('.{$classSelector}').val().split(' - ');
  $('#{$this->id['start']}').val(range[0]);
  $('#{$this->id['end']}').val(range[1]);
});
SCRIPT;
        }

        $this->value['range'] = implode(' - ', $this->value());
        $this->column['range'] = join('_', $this->column);

        return parent::render()->with(['multiple' => $this->multiple]);
    }
}