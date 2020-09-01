<?php

namespace App\Admin\Forms\Steps;

use Encore\Admin\Widgets\StepForm;
use Illuminate\Http\Request;

class Profile extends StepForm
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = 'Input profile';

    /**
     * Handle the form request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        return $this->next($request->all());
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->mobile('mobile');
        $this->image('avatar');
        $this->text('address')->required();
    }
}
