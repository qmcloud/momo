<?php

namespace AdminNewDev\Adminlte\Http\Controllers;

use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;

class AdminlteController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('Title')
            ->description('Description')
            ->body(view('laravel-adminlte3::index'));
    }
}
