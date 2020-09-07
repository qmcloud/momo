<div class="{{$viewClass['form-group']}} row {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}}  col-form-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('adminlte::form.error')

        <div class="input-group">

            @if ($prepend)
                <div class="input-group-prepend">
                    <span class="input-group-text">{!! $prepend !!}</span>
                </div>
            @endif

            <input {!! $attributes !!} />

            @if ($append)
            <div class="input-group-append">
                <span class="input-group-text">{!! $append !!}</span>
            </div>
            @endif

        </div>

        @include('adminlte::form.help-block')

    </div>
</div>