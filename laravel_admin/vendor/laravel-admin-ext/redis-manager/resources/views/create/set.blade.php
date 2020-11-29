@extends('laravel-admin-redis-manager::layout')

@section('page')

    <script>

        $(function () {

            $('select.members').select2({
                tags: true,
                tokenSeparators: [',']
            });
        });

    </script>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Edit</h3> <small></small>
        </div>


        <form class="form-horizontal" method="post" action="{{ route('redis-store-key') }}" pjax-container>

            <div class="box-body">

                <div class="form-group">
                    <label for="inputKey" class="col-sm-2 control-label">Key</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="inputKey" placeholder="key" name="key">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputExpire" class="col-sm-2 control-label">Expires</label>

                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="inputExpire"  value="-1" name="ttl">
                    </div>
                </div>

                <div class="form-group">

                    <label class="col-sm-2 control-label">Members</label>

                    <div class="col-sm-10">
                        <select class="form-control members" name="members[]" multiple="multiple">
                        </select>
                    </div>
                </div>

                {{ csrf_field() }}
                <input type="hidden" name="conn" value="{{ $conn }}">
                <input type="hidden" name="type" value="set">

            </div>

            <div class="box-footer">
                <button type="reset" class="btn btn-default pull-right">Reset</button>
                <button class="btn btn-info col-sm-offset-2">Submit</button>
            </div>

        </form>

    </div>
    <!-- /.box-body -->

@endsection