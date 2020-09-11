<div class="form-group {!! !$errors->has($label) ?: 'has-error' !!}" >

    <label for="{{$id}}" class="col-sm-2 control-label" >{{$label}}</label>
    <div class="col-sm-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
	    <span style="color: red;">
            @include('admin::form.error')
        </span>

        <div id="wrapper">
            <div id="container">
                <!--头部，相册选择和格式选择-->

                <div class="uploader" id="uploader{{$name}}">
                    <div class="queueList" id="queueList{{$name}}">
                        <div id="dndArea{{$name}}" class="placeholder">
                            <div id="filePicker{{$name}}"></div>
                            <p>或将文件拖到这里，单次只可选一张哦</p>
                        </div>
                    </div>
                    <div class="statusBar" style="display:none;">
                        <div class="progress">
                            <span class="text">0%</span>
                            <span class="percentage"></span>
                        </div><div class="info"></div>
                        <div class="btns">
                            <div class="filePicker2" id="filePicker2{{$name}}"></div><div class="uploadBtn">开始上传</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
            <input readonly style="background-color: #fff" type="text" id="{{$name}}-savedpath" name="{{$name}}" value="{{ old($column, $value) }}" class="form-control title" placeholder="{{$label}}">
        </div>
        @include('admin::form.help-block')
    </div>
</div>






