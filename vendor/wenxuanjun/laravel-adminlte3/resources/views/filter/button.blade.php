<div class="btn-group" style="margin-right: 10px" data-toggle="buttons">
    <label class="btn btn-sm btn-dropbox {{ $btn_class }} {{ $expand ? 'active' : '' }}" title="{{ trans('admin.filter') }}">
        <input type="checkbox"><i class="fa fa-filter"></i><span class="hidden-xs">&nbsp;&nbsp;{{ trans('admin.filter') }}</span>
    </label>

    @if($scopes->isNotEmpty())
        <button type="button" class="btn btn-sm btn-dropbox dropdown-toggle" data-toggle="dropdown">
            <span>{{ $current_label }}</span>
        </button>
        <div class="dropdown-menu" role="menu">
            @foreach($scopes as $scope)
                {!! $scope->render() !!}
            @endforeach
            <div role="separator" class="dropdown-divider"></div>
            <a href="{{ $url_no_scopes }}" class="dropdown-item">{{ trans('admin.cancel') }}</a>
        </div>
    @endif
</div>