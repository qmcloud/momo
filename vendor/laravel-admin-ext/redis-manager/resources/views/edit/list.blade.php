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
                    <label for="inputItem" class="col-sm-2 control-label">Push</label>

                    <div class="col-sm-10">
                        <input type="text" class="form-control item" name="item" id="inputItem">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>

                    <div class="col-sm-10">
                        <button type="button" class="btn btn-success list-push" data-direction="right">Right push</button>
                        <button type="button" class="btn btn-success list-push" data-direction="left">Left push</button>
                    </div>
                </div>

                <hr>

                @if(isset($data['value']))
                <div class="form-group">

                    <label class="col-sm-2 control-label">Items</label>

                    <div class="col-sm-10">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>index</th>
                                <th>value</th>
                                <th width="80px;">action</th>
                            </tr>
                            </thead>

                            <tbody>

                            @foreach($data['value'] as $index => $value)
                                <tr>
                                    <td>{{ $index }}</td>
                                    <td>
                                        <a class="list-item" data-type="textarea" data-pk="{{ $index }}" data-url="{{ route('redis-update-key', ['type' => 'list', 'conn' => $conn, 'key' => $data['key']]) }}">{{ $value }}</a></td>
                                    <td>
                                        <a href="#" class="text-red remove-index" data-index="{{ $index }}"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>

        </form>

    </div>
    <!-- /.box-body -->

    <script>

        $(function () {

            $('.remove-index').on('click', function (e) {
                e.preventDefault();
                var key = $('input.key').val();
                var index = $(this).data('index');

                swal({
                        title: "Remove from list ?",
                        type: "error",
                        showCancelButton: true
                    })
                    .then(function(){

                        var params = {
                            key: key,
                            index: index,
                            type: 'list',
                            connection: "{{ $conn }}",
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

            $('.list-push').on('click', function (event) {
                event.preventDefault();

                var key = $('input.key').val();
                var item = $('input.item').val();
                var direction = $(this).data('direction');

                if (item == '') {
                    return;
                }

                var params = {
                    key: key,
                    push: direction,
                    item: item,
                    conn: "{{ $conn }}",
                    type:'list',
                    _token: LA.token
                };

                $.ajax({
                    url: '{{ route('redis-update-key') }}',
                    type: 'PUT',
                    data: params,
                    success: function(result) {
                        toastr.success('Push success.');
//                    $.pjax.reload('#pjax-container');
                        $.pjax({container:'#pjax-container', url: '{{ route('redis-edit-key') }}' + '?conn={{ $conn }}&key='+key });
                    }
                });

            });

            $('.list-item').editable();

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
