<?php

namespace App\Admin\Extensions\Column;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class OpenMap extends AbstractDisplayer
{
    public function display(\Closure $callback = null, $btn = '')
    {
        $callback = $callback->bindTo($this->row);

        list($latitude, $longitude) = call_user_func($callback);

        $key = $this->getKey();

        $name = $this->column->getName();

        Admin::script($this->script());

        return <<<EOT
<button class="btn btn-xs btn-default grid-open-map" data-key="{$key}" data-lat="$latitude" data-lng="$longitude" data-toggle="modal" data-target="#grid-modal-{$name}-{$key}">
    <i class="fa fa-map-marker"></i> $btn
</button>

<div class="modal" id="grid-modal-{$name}-{$key}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span></button>
        <h4 class="modal-title">$btn</h4>
      </div>
      <div class="modal-body">
        <div id="grid-map-$key" style="height:450px;"></div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
EOT;
    }

    protected function script()
    {
        return <<<EOT

$('.grid-open-map').on('click', function() {

    var key = $(this).data('key');
    var lat = $(this).data('lat');
    var lng = $(this).data('lng');

    var center = new qq.maps.LatLng(lat, lng);

    var container = document.getElementById("grid-map-"+key);
    var map = new qq.maps.Map(container, {
        center: center,
        zoom: 13
    });

    var marker = new qq.maps.Marker({
        position: center,
        draggable: true,
        map: map
    });
});

EOT;
    }
}
