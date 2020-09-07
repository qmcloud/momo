<div class="card-footer row">

    {{ csrf_field() }}

    <div class="col-md-{{$width['label']}}">
    </div>

    <div class="col-md-{{$width['field']}}">

        <div class="d-flex d-sm-inline-flex align-items-center">
            @if(in_array('submit', $buttons))
                <button type="submit" class="btn btn-primary mr-2 px-4">{{ trans('admin.submit') }}</button>
            @endif
            @if(in_array('reset', $buttons))
                <button type="reset" class="btn btn-warning mr-3">{{ trans('admin.reset') }}</button>
            @endif
        </div>
        <div class="d-flex d-sm-inline-flex align-items-center">
            @if(in_array('view', $checkboxes))
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" name="after-save" value="3" id="after-save3" class="custom-control-input" />
                    <label class="custom-control-label" for="after-save3">{{ trans('admin.view') }}</label>
                </div>
            @endif
            @if(in_array('continue_editing', $checkboxes))
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" name="after-save" value="1" id="after-save1" class="custom-control-input" />
                    <label class="custom-control-label" for="after-save1">{{ trans('admin.continue_editing') }}</label>
                </div>
            @endif
            @if(in_array('continue_creating', $checkboxes))
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" name="after-save" value="2" id="after-save2" class="custom-control-input" />
                    <label class="custom-control-label" for="after-save2">{{ trans('admin.continue_creating') }}</label>
                </div>
            @endif
        </div>
        
    </div>
</div>