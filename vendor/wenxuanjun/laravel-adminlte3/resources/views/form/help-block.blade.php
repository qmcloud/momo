@if($help)
<span class="form-text text-muted">
    <i class="fa {{ array_get($help, 'icon') }}"></i>&nbsp;{!! array_get($help, 'text') !!}
</span>
@endif