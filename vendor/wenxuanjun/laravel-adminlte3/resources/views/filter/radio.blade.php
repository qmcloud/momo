<div class="input-group input-group-sm">
    @foreach($options as $option => $label)
        <div class="custom-control custom-radio {{ $inline ? 'custom-control-inline' : '' }}">
            <input type="radio" name="{{$name}}" value="{{$option}}" id="{{ $name.'-'.$option }}" class="{{ $id }} custom-control-input"  {{ ((string)$option === request($name, is_null($value) ? '' : $value)) ? 'checked' : '' }} />
            <label class="custom-control-label" for="{{ $name.'-'.$option }}">{{ $label }}</label>
        </div>
    @endforeach
</div>