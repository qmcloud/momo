<input type="checkbox" class="{{ $selectAllName }}" />&nbsp;

@if(!$isHoldSelectAllCheckbox)
<div class="btn-group">
    <a href="javascript:;" class="btn btn-sm btn-default">&nbsp;<span class="hidden-xs">{{ trans('admin.action') }}</span></a>
    <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu" role="menu">
        @foreach($actions as $action)
            <a href="javascript:;" class="{{ $action->getElementClass(false) }} dropdown-item">{{ $action->getTitle() }}</a>
        @endforeach
    </div>
</div>
@endif