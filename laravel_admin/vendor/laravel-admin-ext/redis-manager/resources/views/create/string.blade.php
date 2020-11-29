@extends('laravel-admin-redis-manager::layout')

@section('page')

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit</h3> <small></small>
    </div>

    <form class="form-horizontal" method="post" action="{{ route('redis-store-key') }}" pjax-container>
        <div class="box-body">
            <div class="form-group">
                <label for="inputKey" class="col-sm-2 control-label">Key</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control key" name="key" id="inputKey" placeholder="key">
                </div>
            </div>
            <div class="form-group">
                <label for="inputValue" class="col-sm-2 control-label">Value</label>

                <div class="col-sm-10">
                    <textarea class="form-control value" id="inputValue" rows="6" name="value"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="inputExpire" class="col-sm-2 control-label">Expire</label>

                <div class="col-sm-10">
                    <input type="number" class="form-control" id="inputExpire" name="ttl" value="-1">
                </div>
            </div>

            {{ csrf_field() }}
            <input type="hidden" name="conn" value="{{ $conn }}">
            <input type="hidden" name="type" value="string">
        </div>
        <!-- /.box-body -->

        <div class="box-footer">
            <button type="reset" class="btn btn-default pull-right">Reset</button>
            <button class="btn btn-info col-sm-offset-2">Submit</button>
        </div>

    </form>

</div>
<!-- /.box-body -->

@endsection