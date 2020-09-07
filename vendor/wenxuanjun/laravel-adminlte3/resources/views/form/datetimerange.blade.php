<div class="{{$viewClass['form-group']}} row {!! ($errors->has($errorKey['start'].'start') || $errors->has($errorKey['end'].'end')) ? 'has-error' : ''  !!}">

    <label for="{{$id['start']}}" class="{{$viewClass['label']}} col-form-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('adminlte::form.error')

        <div class="row" style="width: 390px">
            <div class="col-lg-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                    <input type="text" name="{{$name['start']}}" value="{{ old($column['start'], $value['start']) }}" class="form-control {{$class['start']}}" style="width: 160px" {!! $attributes !!} />
                </div>
            </div>

            <div class="col-lg-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                    <input type="text" name="{{$name['end']}}" value="{{ old($column['end'], $value['end']) }}" class="form-control {{$class['end']}}" style="width: 160px" {!! $attributes !!} />
                </div>
            </div>
        </div>

        @include('adminlte::form.help-block')

    </div>
</div>
