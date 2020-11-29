@extends('laravel-admin-redis-manager::layout')

@section('page')

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">{{ $conn }}</h3> <small></small>

        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
        </div>
    </div>

    <form class="form-horizontal" action="{{ route('redis-index') }}" pjax-container>
        <div class="box-body">
            <div class="form-group">
                <label for="inputPattern" class="col-sm-2 control-label">Pattern</label>

                <div class="col-sm-9">
                    <input class="form-control input-sm" name="pattern" id="inputPattern" value="{{ request('pattern', '*')}}">
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-9">
                    <input type="hidden" name="conn" value="{{ $conn }}">

                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i>&nbsp;&nbsp;Search</button>

                    <a class="btn btn-danger btn-sm pull-right key-delete-multi"><i class="fa fa-trash"></i>&nbsp;&nbsp;Delete</a>

                    <a class="btn btn-warning btn-sm pull-right"  style="margin-right: 5px;" href="{{ route('redis-console', ['conn' => $conn]) }}"><i class="fa fa-terminal"></i>&nbsp;&nbsp;Console</a>

                    <div class="btn-group pull-right btn-group-sm" style="margin-right: 5px;">
                        <button type="button" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp;&nbsp;Create</button>
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ route('redis-create-key', ['conn' => $conn, 'type' => 'string']) }}">string</a></li>
                            <li><a href="{{ route('redis-create-key', ['conn' => $conn, 'type' => 'list']) }}">list</a></li>
                            <li><a href="{{ route('redis-create-key', ['conn' => $conn, 'type' => 'hash']) }}">hash</a></li>
                            <li><a href="{{ route('redis-create-key', ['conn' => $conn, 'type' => 'set']) }}">set</a></li>
                            <li><a href="{{ route('redis-create-key', ['conn' => $conn, 'type' => 'zset']) }}">zset</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- /.box-footer -->
    </form>

    <hr class="no-margin">

    <!-- /.box-header -->
    <div class="box-body table-responsive">

        <table class="table table-hover">
            <thead>
            <tr>
                <th><input type="checkbox" class="key-select-all"></th>
                <th>Key</th>
                <th>Type</th>
                <th>TTL(s)</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($keys as $index => $key)
                <tr>
                    <td><input type="checkbox" class="key-select" data-key="{{ $key[0] }}"></td>
                    <td><code>{{ $key[0] }}</code></td>
                    <td>
                        @php($type = $key[1]->getPayload())
                            <span class="label label-{{ \Encore\Admin\RedisManager\RedisManager::typeColor($type) }}">{{ $type }}</span>
                    </td>
                    <td>{{ $key[2] }}</td>
                    <td>
                        <a href="{{ route('redis-edit-key', ['key' => $key[0], 'conn' => $conn]) }}"><i class="fa fa-edit"></i></a>
                        &nbsp;
                        <a href="#" class="key-delete" data-key="{{ $key[0] }}"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>

        @if (empty($keys))
            <div class="text-center" style="padding: 20px;">
                Empty list or set.
            </div>
        @endif

    </div>
    <!-- /.box-body -->
</div>

<script>
    $(function () {

        $('a.key-delete').on('click', function (e) {

            e.preventDefault();
            var key = $(this).data('key');

            swal({
                    title: "Delete key ["+key+"] ?",
                    type: "error",
                    showCancelButton: true
                }).then(function (result) {
                    if (result.value) {

                        var params = {
                            conn: "{{ $conn }}",
                            key: key,
                            _token: LA.token
                        };

                        $.ajax({
                            url: '{{ route('redis-key-delete') }}',
                            type: 'DELETE',
                            data: params,
                            success: function (result) {
                                toastr.success('Key ' + key + ' deleted');
                                $.pjax.reload('#pjax-container');
                            }
                        });
                    }
                });
        });

        $('.key-delete-multi').click(function () {

            var keys = selectedRows();

            if (keys.length == 0) {
                return;
            }

            swal({
                    title: "Delete selected keys ?",
                    type: "error",
                    showCancelButton: true
                }).then(function (result) {
                    if (result.value) {


                        var params = {
                            conn: "{{ $conn }}",
                            key: keys,
                            _token: LA.token
                        };

                        $.ajax({
                            url: '{{ route('redis-key-delete') }}',
                            type: 'DELETE',
                            data: params,
                            success: function (result) {
                                toastr.success(params.key.length + ' keys deleted');
                                $.pjax.reload('#pjax-container');
                            }
                        });
                    }
                });
        });

        $('.key-select').iCheck({checkboxClass:'icheckbox_minimal-blue'}).on('ifChanged', function () {
            if (this.checked) {
                $(this).closest('tr').css('background-color', '#ffffd5');
            } else {
                $(this).closest('tr').css('background-color', '');
            }
        });

        $('.key-select-all').iCheck({checkboxClass:'icheckbox_minimal-blue'}).on('ifChanged', function(event) {
            if (this.checked) {
                $('.key-select').iCheck('check');
            } else {
                $('.key-select').iCheck('uncheck');
            }
        });

        var selectedRows = function () {
            var selected = [];
            $('.key-select:checked').each(function(){
                selected.push($(this).data('key'));
            });

            return selected;
        };
    });

</script>

@endsection