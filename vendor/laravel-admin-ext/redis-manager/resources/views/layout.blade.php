<div class="row">

    <div class="col-md-3">
        <div class="box with-border">
            <div class="box-header with-border">
                <h3 class="box-title">Connections</h3>

                <div class="box-tools">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body no-padding">
                <ul class="nav nav-pills nav-stacked">
                    @foreach($connections as $name => $connection)
						@if(!empty($connection['host']))
                        <li @if($name == $conn)class="active"@endif>
                            <a href=" {{ route('redis-index', ['conn' => $name]) }}">
                                <i class="fa fa-database"></i> {{ $name }}  &nbsp;&nbsp;<small>[{{ $connection['host'].':'.$connection['port'] }}]</small>
                            </a>
                        </li>
						@endif
                    @endforeach
                </ul>
            </div>
            <!-- /.box-body -->
        </div>

        <div class="box box-default collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title">Connection <small><code>{{ $conn }}</code></small></h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>

            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        @foreach($connections[$conn] as $name => $value)
                            <tr>
                                <td width="160px">{{ $name }}</td>
                                <td><span class="label label-primary">{{ is_array($value) ? json_encode($value) : $value }}</span></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
        </div>

        <div class="box with-border">
            <div class="box-header with-border">
                <h3 class="box-title">Information</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
                <div class="box-group" id="accordion">
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->

                    @foreach($info as $part => $detail)
                        <div class="panel box box-default no-border">
                            <div class="box-header">
                            <span class="box-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $part }}" aria-expanded="false" class="collapsed" style="font-size: 14px;">
                                    {{ $part }}
                                </a>
                            </span>
                            </div>
                            <div id="collapse{{ $part }}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                <div class="box-body no-padding no-border">
                                    <div class="table-responsive">
                                        <table class="table table-striped no-margin">
                                            @foreach($detail as $key => $value)
                                                <tr>
                                                    <td>{{ $key }}</td>
                                                    <td>
                                                        @if(is_array($value))
                                                            <pre><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
                                                        @else
                                                            <span class="label label-primary">{{ $value }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>
            </div>
            <!-- /.box-body -->
        </div>

    </div>

    <div class="col-md-9">

        @yield('page')

    </div>

</div>

