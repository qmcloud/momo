<?php

namespace App\Admin\Extensions\Nav;

use Encore\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;

class Modules implements Renderable
{
    protected function renderModal()
    {
        $modal = <<<MODAL
<div class="modal fade" id="admin-modules">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">模块</h4>
      </div>
      <div class="modal-body">
          <a class="btn btn-app" href="/posts">
            <i class="fa fa-edit"></i> Posts
          </a>
          <a class="btn btn-app" href="/users">
            <i class="fa fa-users"></i> Users
          </a>
          <a class="btn btn-app" href="/images">
            <i class="fa fa-picture-o"></i> Images
          </a>
          <a class="btn btn-app" href="/videos">
            <i class="fa fa-play"></i> Videos
          </a>
          <a class="btn btn-app" href="/articles">
            <i class="fa fa-file-text"></i> Articles
          </a>
          <a class="btn btn-app" href="/tags">
            <i class="fa fa-tags"></i> Tags
          </a>
      </div>
    </div>
  </div>
</div>
MODAL;

        Admin::html($modal);
    }

    public function render()
    {
        $script = <<<SCRIPT
$(function(){
    $('#admin-modules a').click(function() {
        $('.modal').modal('hide');
    });
});
SCRIPT;
        Admin::script($script);

        $this->renderModal();

        return <<<HTML
<li data-toggle="modal" data-target="#admin-modules">
    <a href="#">
        <i class="fa fa-th-large"></i>
    </a>
</li>
HTML;

    }
}