<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')
        <div class="btn btn-info pull-left cropper-btn">{{ trans('admin_cropper.choose') }}</div>
        @include('admin::form.help-block')
        <input class="cropper-file" type="file" accept="image/*" {!! $attributes !!}/>
        <!-- <img class="cropper-img" {!! empty($value) ? '' : 'src="'.old($column, $value).'"'  !!}> -->
        <img class="cropper-img" {!! empty($value) ? '' : 'src="'.$preview.'"'  !!}>
        <input class="cropper-input" name="{{$name}}" value="{{ old($column, $value) }}"/>
    </div>
</div>