<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{config('admin.title')}} | {{ trans('admin.login') }}</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="{{ admin_asset("vendor/wenxuanjun/laravel-adminlte3/plugins/bootstrap/css/bootstrap.min.css") }}">
  <link rel="stylesheet" href="{{ admin_asset("vendor/wenxuanjun/laravel-adminlte3/plugins/font-awesome/css/font-awesome.min.css") }}">
  <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/square/blue.css") }}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <style>.raised{border-radius: 6px 6px 6px 6px;-webkit-box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.14), 0 6px 30px 5px rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.14), 0 6px 30px 5px rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);padding:25px 20px 25px 20px;}
  </style>
</head>
<body class="hold-transition login-page" @if(config('admin.login_background_image'))style="background: url({{config('admin.login_background_image')}}) no-repeat;background-size: cover;"@endif>
<div class="row" style="margin-top:60px;margin-bottom:30px">
  <div class="col-sm-6 offset-sm-3">
    <div class="card raised">
      <div><h1 class="text-center"><a class="text-dark" href="{{ admin_base_path('/') }}">{{config('admin.name')}}</a></h1></div>
      <p class="text-center">{{ trans('admin.login') }}</p>
      <form action="{{ admin_base_path('auth/login') }}" method="post">
        <div class="form-group has-feedback {!! !$errors->has('username') ?: 'has-error' !!}">
        @if($errors->has('username'))
          @foreach($errors->get('username') as $message)
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-times-circle-o"></i> {{$message}}
          </div>
          @endforeach
        @endif
          <input type="text" class="form-control" placeholder="{{ trans('admin.username') }}" name="username" value="{{ old('username') }}">
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback {!! !$errors->has('password') ?: 'has-error' !!}">
          @if($errors->has('password'))
            @foreach($errors->get('password') as $message)
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <i class="fa fa-times-circle-o"></i> {{$message}}
            </div>
            @endforeach
          @endif
          <input type="password" class="form-control" placeholder="{{ trans('admin.password') }}" name="password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="form-group">
          <div class="row">
            <div class="col-sm-3">
            @if(config('admin.auth.remember'))
              <div class="checkbox icheck" style="padding-left:10px;padding-top:5px">
                <label>
                  <input type="checkbox" name="remember" value="1" {{ (!old('username') || old('remember')) ? 'checked' : '' }}>
                  {{ trans('admin.remember_me') }}
                </label>
              </div>
            @endif
            </div>
            <div class="col-sm-4 offset-sm-5">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <button type="submit" class="btn btn-secondary btn-block">{{ trans('admin.login') }}</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js")}} "></script>
<script src="{{ admin_asset("vendor/wenxuanjun/laravel-adminlte3/plugins/bootstrap/js/bootstrap.min.js")}}"></script>
<script src="{{ admin_asset("vendor/laravel-admin/AdminLTE/plugins/iCheck/icheck.min.js")}}"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%'
    });
  });
</script>
</body>
</html>
