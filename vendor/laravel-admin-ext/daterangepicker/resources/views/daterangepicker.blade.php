<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id['start']}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="input-group">

            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

            @if($multiple)

                <input value="{{ old($column['range'], $value['range']) }}" name="{{$name['range']}}" class="form-control {{$class['start']}}_{{$class['end']}}" style="width: 300px" {!! $attributes !!} />

                <input type="hidden" id="{{$id['start']}}" name="{{$name['start']}}" value="{{ old($column['start'], $value['start']) }}"/>
                <input type="hidden" id="{{$id['end']}}" name="{{$name['end']}}" value="{{ old($column['end'], $value['end']) }}"/>
            @else
                <input {!! $attributes !!} />
            @endif


        </div>

        @include('admin::form.help-block')

    </div>
</div>