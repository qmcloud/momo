<div class="{{$viewClass['form-group']}} row {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} col-form-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('adminlte::form.error')

        <input type="file" class="{{$class}}" name="{{$name}}[]" {!! $attributes !!} />

        @include('adminlte::form.help-block')

    </div>
</div>
