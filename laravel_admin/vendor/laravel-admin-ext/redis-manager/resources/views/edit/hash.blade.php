@extends('laravel-admin-redis-manager::layout')

@section('page')

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Edit</h3> <small></small>
        </div>


        <form class="form-horizontal">

            <div class="box-body">

                <div class="form-group">
                    <label for="inputKey" class="col-sm-2 control-label">Key</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control key" id="inputKey" placeholder="key" readonly value="{{ $data['key'] ?? '' }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputExpire" class="col-sm-2 control-label">Expires</label>

                    <div class="col-sm-10">
                        <input type="number" class="form-control ttl" id="inputExpire"  value="{{ $data['ttl'] ?? -1 }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>

                    <div class="col-sm-10">
                        <button type="button" class="btn btn-primary update-expire">Update expire</button>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Field</label>

                    <div class="col-sm-10">
                        <input class="form-control field">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Value</label>

                    <div class="col-sm-10">
                        <input class="form-control value">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>

                    <div class="col-sm-10">
                        <button type="button" class="btn btn-success hash-add" >Add field</button>
                    </div>
                </div>

                <hr>

                <div class="form-group">

                    <label class="col-sm-2 control-label">Members</label>

                    <div class="col-sm-10">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>key</th>
                                <th>value</th>
                                <th width="80px;">action</th>
                            </tr>
                            </thead>

                            <tbody>

                            @foreach($data['value'] as $field => $value)
                                <tr>
                                    <td>{{ $field }}</td>
                                    <td>
                                        <a class="hash-field" data-type="textarea" data-pk="{{ $field }}" data-url="{{ route('redis-update-key', ['type' => 'hash', 'conn' => $conn, 'key' => $data['key']]) }}">{{ $value }}</a></td>
                                    <td>
                                        <a href="#" class="text-red remove-key" data-field="{{ $field }}"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </form>

    </div>
    <!-- /.box-body -->

    <script>

        $(function () {

            $('.remove-key').on('click', function (e) {
                e.preventDefault();
                var key = $('input.key').val();
                var field = $(this).data('field');

                swal({
                        title: "Remove from list ?",
                        type: "error",
                        showCancelButton: true
                    })
                    .then(function(){

                        var params = {
                            key: key,
                            field: field,
                            connection: "{{ $conn }}",
                            type: 'hash',
                            _token: LA.token
                        };

                        $.ajax({
                            url: '{{ route('redis-remove-item') }}',
                            type: 'DELETE',
                            data: params,
                            success: function(result) {
                                toastr.success('List item removed');
                                $.pjax.reload('#pjax-container');
                            }
                        });
                    });
            });

            $('.hash-add').on('click', function (event) {
                event.preventDefault();

                var key = $('input.key').val();
                var field = $('input.field').val();
                var value = $('input.value').val();

                if (field == '' || value == '') {
                    return;
                }

                var params = {
                    key: key,
                    field: field,
                    value: value,
                    conn: "{{ $conn }}",
                    type:'hash',
                    _token: LA.token
                };

                $.ajax({
                    url: '{{ route('redis-update-key') }}',
                    type: 'PUT',
                    data: params,
                    success: function(result) {
                        toastr.success('Add success.');
                        $.pjax.reload('#pjax-container');
                    }
                });

            });

            $('.hash-field').editable();

            $('.update-expire').on('click', function (event) {
                event.preventDefault();

                var key = $('input.key').val();
                var ttl = $('input.ttl').val();

                var params = {
                    key: key,
                    ttl: ttl,
                    conn: "{{ $conn }}",
                    type:'list',
                    _token: LA.token
                };

                $.ajax({
                    url: '{{ route('redis-update-key') }}',
                    type: 'PUT',
                    data: params,
                    success: function(result) {
                        toastr.success('Update success.');
                        $.pjax.reload('#pjax-container');
                    }
                });
            })
        });

    </script>

@endsection
