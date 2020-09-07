<div class="{{$viewClass['form-group']}} row">
    <label class="{{$viewClass['label']}} col-form-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        <div class="form-control-plaintext">
            {!! $value !!}&nbsp;
        </div>

        @include('adminlte::form.help-block')

    </div>
</div>