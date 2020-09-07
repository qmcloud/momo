<div class="{{$viewClass['form-group']}} row {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} col-form-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('adminlte::form.error')

        <div class="input-group" style="width: 250px;">

            <input {!! $attributes !!} />
            <div class="input-group-append">
                <span class="input-group-text clearfix" style="padding: 1px;"><img id="{{$column}}-captcha" src="{{ captcha_src() }}" style="height:30px;cursor: pointer;"  title="Click to refresh"/></span>
            </div>

        </div>

        @include('adminlte::form.help-block')

    </div>
</div>