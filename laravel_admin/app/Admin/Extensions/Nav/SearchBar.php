<?php

namespace App\Admin\Extensions\Nav;

use Illuminate\Contracts\Support\Renderable;

class SearchBar implements Renderable
{
    public function render()
    {
        return view('admin.search-bar')->render();
    }
}