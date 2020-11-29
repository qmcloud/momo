<div class="btn-group" data-toggle="buttons">
    @foreach($options as $option => $icon)
    <label class="btn btn-default btn-sm {{ \Request::get('view', 'card') == $option ? 'active' : '' }}">
        <input type="radio" class="grid-view" value="{{ $option }}"><i class="fa fa-{{ $icon }}"></i>
    </label>
    @endforeach
</div>