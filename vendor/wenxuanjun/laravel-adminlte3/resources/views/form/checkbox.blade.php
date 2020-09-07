<div class="{{$viewClass['form-group']}} row {!! !$errors->has($column) ?: 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} col-form-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}" id="{{$id}}">

        @include('adminlte::form.error')

        @foreach($options as $option => $label)
            <div class="custom-control custom-checkbox {{ $inline ? 'custom-control-inline' : '' }}">
                <input type="checkbox" name="{{$name}}[]" value="{{$option}}" id="{{ $name.'-'.$option }}" class="{{$class}} custom-control-input" {{ in_array($option, (array)old($column, $value)) || ($value === null && in_array($label, $checked)) ?'checked':'' }} {!! $attributes !!} />
                <label class="custom-control-label" for="{{ $name.'-'.$option }}">{{ $label }}</label>
            </div>
        @endforeach

        @include('adminlte::form.help-block')

    </div>
</div>
