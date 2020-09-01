<?php

namespace App\Admin\Forms\Steps;

use Encore\Admin\Widgets\StepForm;
use Illuminate\Http\Request;

class Info extends StepForm
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = 'Basic info';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        $this->clear();

        return $this->next($request->all());
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('username')->rules('min:8');
        $this->email('email');
    }
}
