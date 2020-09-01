<?php

namespace App\Admin\Forms\Steps;

use Encore\Admin\Widgets\StepForm;
use Illuminate\Http\Request;

class Password extends StepForm
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = 'Input password';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        $data = json_encode($this->all(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

        admin_info('Form data', "<pre><code>$data</code></pre>");

        $this->clear();

        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->password('password');
        $this->password('password_confirmation');
    }
}
