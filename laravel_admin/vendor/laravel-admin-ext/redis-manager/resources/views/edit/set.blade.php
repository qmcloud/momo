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

                    <label class="col-sm-2 control-label">Members</label>

                    <div class="col-sm-10">
                        <select class="form-control members" multiple="multiple">
                            @foreach($data['value'] as $member)
                                <option selected>{{ $member }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

        </form>

    </div>
    <!-- /.box-body -->

    <script>

        $(function () {

            $('.members').select2({
                tags: true,
                tokenSeparators: [','],
                createTag: function (tag) {
                    return {
                        id: tag.term,
                        text: tag.term,
                        isNew : true
                    };
                }
            }).on("select2:select", function(e) {
                if(e.params.data.isNew){
                    // append the new option element prenamently:
                    $(this).find('[value="'+e.params.data.id+'"]').replaceWith('<option selected value="'+e.params.data.id+'">'+e.params.data.text+'</option>');
                    // store the new tag:

                    var key = $('input.key').val();

                    var params = {
                        key: key,
                        member: e.params.data.id,
                        conn: "{{ $conn }}",
                        type:'set',
                        _token: LA.token
                    };

                    $.ajax({
                        url: '{{ route('redis-update-key') }}',
                        type: 'PUT',
                        data: params,
                        success: function(result) {
                            toastr.success('Push success.');
//                            $.pjax.reload('#pjax-container');
                        }
                    });
                }
            }).on("select2:unselect", function(e) {

                var key = $('input.key').val();

                var params = {
                    key: key,
                    member: e.params.data.id,
                    type: 'set',
                    connection: "{{ $conn }}",
                    _token: LA.token
                };

                $.ajax({
                    url: '{{ route('redis-remove-item') }}',
                    type: 'DELETE',
                    data: params,
                    success: function(result) {
                        toastr.success('List item removed');
//                        $.pjax.reload('#pjax-container');
                    }
                });

            });

            $('.update-expire').on('click', function (event) {
                event.preventDefault();

                var key = $('input.key').val();
                var ttl = $('input.ttl').val();

                var params = {
                    key: key,
                    ttl: ttl,
                    conn: "{{ $conn }}",
                    type:'set',
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
